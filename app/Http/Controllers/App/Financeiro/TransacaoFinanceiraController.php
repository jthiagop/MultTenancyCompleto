<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Support\Money;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Financeiro\ModulosAnexo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\RecurrenceService;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;
use App\Models\Movimentacao;
use App\Models\Banco;
use App\Models\User;
use Carbon\Carbon;
use Flasher;
use Log;


class TransacaoFinanceiraController extends Controller
{
    protected RecurrenceService $recurrenceService;

    public function __construct(RecurrenceService $recurrenceService)
    {
        $this->recurrenceService = $recurrenceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransacaoFinanceiraRequest $request)
    {
        // Recupera a companhia associada ao usuário autenticado
        $subsidiary = User::getCompany();

        if (!$subsidiary) {
            return redirect()->back()->with('error', 'Companhia não encontrada.');
        }

        // Validação dos dados é automática com StoreTransacaoFinanceiraRequest, não é necessário duplicar validações aqui

        // Processa os dados validados
        $validatedData = $request->validated();

        // Converte a data para o formato adequado
        // O valor já foi convertido para centavos pelo StoreTransacaoFinanceiraRequest usando Money
        $validatedData['data_competencia'] = Carbon::createFromFormat('d-m-Y', $validatedData['data_competencia'])->format('Y-m-d');

        // Adiciona informações padrão
        $validatedData['company_id'] = $subsidiary->company_id;
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        // ✅ REGRA DE NEGÓCIO: Só cria movimentação se for efetivada (pago/recebido)
        // Transações em_aberto são previsões e NÃO impactam saldo
        $situacao = $validatedData['situacao'] ?? 'em_aberto';
        $situacoesEfetivadas = ['pago', 'recebido'];
        $movimentacao = null;

        // ========================================================
        // PARCELAMENTO: Se há parcelas, criar N transações separadas
        // ========================================================
        $parcelas = $validatedData['parcelas'] ?? null;
        unset($validatedData['parcelas'], $validatedData['parcelamento']);

        if (is_array($parcelas) && count($parcelas) >= 2) {
            $transacoesCriadas = [];
            $primeiraCaixa = null;

            foreach ($parcelas as $index => $parcela) {
                $dadosParcela = $validatedData;

                // Sobrescrever campos com dados da parcela
                $dadosParcela['valor'] = $parcela['valor'];
                $dadosParcela['descricao'] = $parcela['descricao'] ?? $validatedData['descricao'] . ' ' . $index . '/' . count($parcelas);

                // Converter vencimento da parcela (dd/mm/yyyy → Y-m-d)
                if (!empty($parcela['vencimento'])) {
                    try {
                        $dadosParcela['data_vencimento'] = Carbon::createFromFormat('d/m/Y', $parcela['vencimento'])->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dadosParcela['data_vencimento'] = $validatedData['data_competencia'];
                    }
                }

                // Entidade financeira: usar a entidade do formulário principal
                // (não vem mais por parcela, usa a entidade_id já presente em $validatedData)

                // Situação: parcelas são em_aberto (não pagas)
                $dadosParcela['situacao'] = 'em_aberto';
                $dadosParcela['agendado'] = $parcela['agendado'] ?? false;
                $dadosParcela['movimentacao_id'] = null;

                // Vincular parcelas à primeira transação como parent
                if ($primeiraCaixa) {
                    $dadosParcela['parent_id'] = $primeiraCaixa->id;
                }

                $caixa = TransacaoFinanceira::create($dadosParcela);
                $transacoesCriadas[] = $caixa;

                if (!$primeiraCaixa) {
                    $primeiraCaixa = $caixa;
                }

                // Processar lançamento padrão para cada parcela
                $this->processarLancamentoPadrao($dadosParcela);
            }

            // Usar a primeira parcela como referência para anexos e Domus
            $caixa = $primeiraCaixa;

            // Processar anexos na primeira parcela
            $this->processarAnexos($request, $caixa);

            $mensagem = count($transacoesCriadas) . ' parcelas criadas com sucesso!';
            Flasher::addSuccess($mensagem);

            // Se veio do Domus IA (AJAX)
            if ($request->ajax() || $request->wantsJson()) {
                $domusDocumentoId = null;
                if ($request->filled('domus_documento_id')) {
                    try {
                        $domusDoc = \App\Models\DomusDocumento::find($request->input('domus_documento_id'));
                        if ($domusDoc) {
                            $domusDocumentoId = $domusDoc->id;
                            $domusDoc->update(['status' => \App\Enums\StatusDomusDocumento::LANCADO]);
                            $this->anexarDocumentoDomus($domusDoc, $caixa);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Erro ao processar DomusDocumento: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => $mensagem,
                    'transacao_id' => $caixa->id,
                    'parcelas_count' => count($transacoesCriadas),
                    'domus_documento_id' => $domusDocumentoId,
                ]);
            }

            return redirect()->back()->with('message', $mensagem);
        }

        // ========================================================
        // TRANSAÇÃO ÚNICA (À Vista / 1x)
        // ========================================================

        if (in_array($situacao, $situacoesEfetivadas)) {
            // ✅ Usar valor_pago na movimentação (valor real que saiu/entrou na conta)
            // valor_pago = valor + juros + multa - desconto (calculado no prepareForValidation)
            // Se não houver valor_pago, fallback para valor original
            $valorMovimentacao = $validatedData['valor_pago'] ?? $validatedData['valor'];

            $movimentacao = Movimentacao::create([
                'entidade_id' => $validatedData['entidade_id'],
                'tipo' => $validatedData['tipo'],
                'valor' => $valorMovimentacao,
                'data' => $validatedData['data_competencia'],
                'descricao' => $validatedData['descricao'],
                'company_id' => $validatedData['company_id'],
                'created_by' => $validatedData['created_by'],
                'created_by_name' => $validatedData['created_by_name'],
                'updated_by' => $validatedData['updated_by'],
                'updated_by_name' => $validatedData['updated_by_name'],
            ]);
            $validatedData['movimentacao_id'] = $movimentacao->id;
        }

        // Cria o registro no caixa
        $caixa = TransacaoFinanceira::create($validatedData);

        // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))

        // Verifica e processa lançamentos padrão
        $this->processarLancamentoPadrao($validatedData);

        // Processa anexos, se existirem
        $this->processarAnexos($request, $caixa);

        // Processa Recorrência se solicitado
        \Log::info('Verificando recorrência', [
            'has_repetir' => $request->has('repetir_lancamento'),
            'repetir_value' => $request->repetir_lancamento,
            'has_frequencia' => $request->has('frequencia'),
            'frequencia' => $request->frequencia,
            'intervalo' => $request->intervalo_repeticao,
            'apos_ocorrencias' => $request->apos_ocorrencias,
        ]);

        if ($request->has('repetir_lancamento') && $request->repetir_lancamento == '1') {
            \Log::info('Chamando processarRecorrencia');
            $this->processarRecorrencia($request, $caixa, $validatedData);
        } else {
            \Log::info('Recorrência NÃO ativada');
        }

        // Adiciona mensagem de sucesso
        Flasher::addSuccess('Lançamento criado com sucesso!');

        // Se veio do Domus IA (AJAX), atualizar status do documento e retornar JSON
        if ($request->ajax() || $request->wantsJson()) {
            // Atualizar status do DomusDocumento e criar anexo automático
            $domusDocumentoId = null;
            if ($request->filled('domus_documento_id')) {
                try {
                    $domusDoc = \App\Models\DomusDocumento::find($request->input('domus_documento_id'));
                    if ($domusDoc) {
                        $domusDocumentoId = $domusDoc->id;
                        
                        // Atualizar status para lançado
                        $domusDoc->update(['status' => \App\Enums\StatusDomusDocumento::LANCADO]);
                        
                        // Criar anexo automático a partir do documento do Domus IA
                        $this->anexarDocumentoDomus($domusDoc, $caixa);
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro ao processar DomusDocumento: ' . $e->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Lançamento criado com sucesso!',
                'transacao_id' => $caixa->id,
                'domus_documento_id' => $domusDocumentoId,
            ]);
        }

        return redirect()->back()->with('message', 'Lançamento criado com sucesso!');
    }

    /**
     * Processa a lógica de recorrência
     */
    private function processarRecorrencia(Request $request, TransacaoFinanceira $transacaoOriginal, array $validatedData)
    {
        \Log::info('ProcessarRecorrencia INICIADO', [
            'transacao_id' => $transacaoOriginal->id,
            'frequencia' => $request->frequencia,
            'intervalo' => $request->intervalo_repeticao,
            'total' => $request->apos_ocorrencias,
        ]);

        try {
            $recorrencia = null;

            // 1. Identificar ou Criar a Recorrência
            if ($request->filled('configuracao_recorrencia') && is_numeric($request->configuracao_recorrencia)) {
                $recorrencia = \App\Models\Financeiro\Recorrencia::find($request->configuracao_recorrencia);
            } else {
                // Criar nova recorrência
                $recorrencia = \App\Models\Financeiro\Recorrencia::create([
                    'company_id' => $validatedData['company_id'],
                    'intervalo_repeticao' => $request->intervalo_repeticao ?? 1,
                    'frequencia' => $request->frequencia,
                    'total_ocorrencias' => $request->apos_ocorrencias,
                    'ocorrencias_geradas' => 0,
                    'data_inicio' => $validatedData['data_competencia'],
                    'data_proxima_geracao' => $validatedData['data_competencia'],
                    'ativo' => true,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name,
                ]);
            }

            if (!$recorrencia) {
                return;
            }

            // 2. Delegate to RecurrenceService for all transaction generation
            $this->recurrenceService->generateRecurringTransactions(
                $recorrencia,
                $transacaoOriginal,
                $validatedData
            );

        } catch (\Exception $e) {
            \Log::error('Erro ao processar recorrência: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Processa lançamentos padrão.
     */
    private function processarLancamentoPadrao(array $validatedData)
    {
        $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
        if ($lancamentoPadrao && $lancamentoPadrao->description === 'Deposito Bancário') {
            $validatedData['origem'] = 'Banco';
            $validatedData['tipo'] = 'entrada';

            // Cria outra movimentação para "Deposito Bancário"
            $movimentacaoBanco = Movimentacao::create([
                'entidade_id' => $validatedData['entidade_banco_id'],
                'tipo' => $validatedData['tipo'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'company_id' => $validatedData['company_id'],
                'created_by' => $validatedData['created_by'],
                'created_by_name' => $validatedData['created_by_name'],
                'updated_by' => $validatedData['updated_by'],
                'updated_by_name' => $validatedData['updated_by_name'],
            ]);

            // Cria o lançamento no banco
            $validatedData['movimentacao_id'] = $movimentacaoBanco->id;
            Banco::create($validatedData);
        }
    }

    /**
     * Processa os anexos enviados.
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $caixa)
    {
        // Verifica se há anexos no formato anexos[index][arquivo] ou anexos[index][link]
        if (!$request->has('anexos') || !is_array($request->input('anexos'))) {
            return;
        }

        $anexos = $request->input('anexos');
        
        foreach ($anexos as $index => $anexoData) {
            $formaAnexo = $anexoData['forma_anexo'] ?? 'arquivo';
            $tipoAnexo = $anexoData['tipo_anexo'] ?? null;
            $descricao = $anexoData['descricao'] ?? null;
            
            if ($formaAnexo === 'arquivo') {
                // Processa arquivo
                $fileKey = "anexos.{$index}.arquivo";
                
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                    $nomeOriginal = $file->getClientOriginalName();
                    $anexoName = time() . '_' . $nomeOriginal;
                    $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                    ModulosAnexo::create([
                        'anexavel_id'     => $caixa->id,
                        'anexavel_type'   => TransacaoFinanceira::class,
                        'forma_anexo'     => 'arquivo',
                        'nome_arquivo'    => $nomeOriginal,
                        'caminho_arquivo' => $anexoPath,
                        'tipo_arquivo'    => $file->getMimeType() ?? '',
                        'extensao_arquivo' => $file->getClientOriginalExtension(),
                        'mime_type'       => $file->getMimeType() ?? '',
                        'tamanho_arquivo' => $file->getSize(),
                        'tipo_anexo'      => $tipoAnexo,
                        'descricao'       => $descricao,
                        'status'          => 'ativo',
                        'data_upload'     => now(),
                        'created_by'     => Auth::id(),
                        'created_by_name' => Auth::user()->name,
                    ]);
                }
            } elseif ($formaAnexo === 'link') {
                // Processa link
                $link = $anexoData['link'] ?? null;
                
                if ($link) {
                    ModulosAnexo::create([
                        'anexavel_id'     => $caixa->id,
                        'anexavel_type'   => TransacaoFinanceira::class,
                        'forma_anexo'     => 'link',
                        'link'            => $link,
                        'tipo_anexo'      => $tipoAnexo,
                        'descricao'       => $descricao,
                        'status'          => 'ativo',
                        'data_upload'     => now(),
                        'created_by'     => Auth::id(),
                        'created_by_name' => Auth::user()->name,
                    ]);
                }
            }
        }
    }

    /**
     * Anexa automaticamente o documento do Domus IA à transação financeira.
     * 
     * @param \App\Models\DomusDocumento $domusDoc
     * @param TransacaoFinanceira $transacao
     * @return void
     */
    private function anexarDocumentoDomus(\App\Models\DomusDocumento $domusDoc, TransacaoFinanceira $transacao): void
    {
        try {
            // Verificar se o documento tem um arquivo válido
            if (empty($domusDoc->caminho_arquivo)) {
                Log::warning('DomusDocumento sem caminho de arquivo', ['id' => $domusDoc->id]);
                return;
            }

            // Determinar tipo de anexo baseado no tipo do documento
            $tipoAnexo = match($domusDoc->tipo_documento) {
                'NF-e', 'NFC-e', 'NOTA_FISCAL' => 'nota_fiscal',
                'CUPOM', 'CUPOM_FISCAL' => 'cupom_fiscal',
                'BOLETO' => 'boleto',
                'RECIBO' => 'recibo',
                'FATURA_CARTAO' => 'fatura',
                'COMPROVANTE' => 'comprovante',
                default => 'documento'
            };

            // Descrição automática
            $descricao = 'Documento importado via Domus IA';
            if ($domusDoc->estabelecimento_nome) {
                $descricao .= ' - ' . $domusDoc->estabelecimento_nome;
            }
            if ($domusDoc->tipo_documento) {
                $descricao = $domusDoc->tipo_documento . ' - ' . $descricao;
            }

            // Criar o anexo vinculado à transação
            ModulosAnexo::create([
                'anexavel_id'      => $transacao->id,
                'anexavel_type'    => TransacaoFinanceira::class,
                'forma_anexo'      => 'arquivo',
                'nome_arquivo'     => $domusDoc->nome_arquivo,
                'caminho_arquivo'  => $domusDoc->caminho_arquivo,
                'tipo_arquivo'     => $domusDoc->tipo_arquivo ?? '',
                'extensao_arquivo' => pathinfo($domusDoc->nome_arquivo, PATHINFO_EXTENSION),
                'mime_type'        => $domusDoc->mime_type ?? '',
                'tamanho_arquivo'  => $domusDoc->tamanho_arquivo ?? 0,
                'tipo_anexo'       => $tipoAnexo,
                'descricao'        => $descricao,
                'status'           => 'ativo',
                'data_upload'      => now(),
                'created_by'       => Auth::id(),
                'created_by_name'  => Auth::user()->name ?? 'Sistema',
            ]);

            Log::info('Anexo criado automaticamente a partir do DomusDocumento', [
                'domus_documento_id' => $domusDoc->id,
                'transacao_id' => $transacao->id,
                'arquivo' => $domusDoc->nome_arquivo,
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao anexar documento Domus à transação: ' . $e->getMessage(), [
                'domus_documento_id' => $domusDoc->id,
                'transacao_id' => $transacao->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransacaoFinanceira $transacaoFinanceira)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransacaoFinanceira $transacaoFinanceira)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        // Verificar se a transação pertence à empresa ativa
        if ($transacaoFinanceira->company_id != $activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Transação não encontrada.'
            ], 404);
        }

        try {
            return \DB::transaction(function () use ($transacaoFinanceira) {
                // Guardar informações para log
                $transacaoId = $transacaoFinanceira->id;
                $entidadeId = $transacaoFinanceira->entidade_id;
                $valor = $transacaoFinanceira->valor;
                $tipo = $transacaoFinanceira->tipo;
                $movimentacaoId = $transacaoFinanceira->movimentacao_id;
                $origem = $transacaoFinanceira->origem;

                // ✅ Saldo será recalculado dinamicamente via calculateBalance()
                \Log::info('Deletando transação - saldo será recalculado dinamicamente', [
                    'transacao_id' => $transacaoFinanceira->id,
                    'entidade_id' => $transacaoFinanceira->entidade_id,
                    'valor' => $valor,
                    'tipo' => $tipo
                ]);

                // 2. Se conciliada, desfazer vínculo com bank_statement
                if ($transacaoFinanceira->bankStatements()->exists()) {
                    $bankStatements = $transacaoFinanceira->bankStatements;
                    
                    foreach ($bankStatements as $bankStatement) {
                        $bankStatement->update([
                            'reconciled' => false,
                            'status_conciliacao' => 'pendente'
                        ]);
                        
                        \Log::info('Bank statement resetado', [
                            'bank_statement_id' => $bankStatement->id,
                            'status' => 'pendente'
                        ]);
                    }
                    
                    // Remover vínculo na tabela pivot
                    $transacaoFinanceira->bankStatements()->detach();
                }

                // 3. Atualizar movimentação relacionada (se houver campo valor_conciliado)
                if ($movimentacaoId) {
                    $movimentacao = Movimentacao::find($movimentacaoId);
                    
                    if ($movimentacao) {
                        // Se existir campo valor_conciliado, resetar para null
                        if (\Schema::hasColumn('movimentacoes', 'valor_conciliado')) {
                            $movimentacao->valor_conciliado = null;
                            $movimentacao->save();
                        }
                        
                        // Deletar a movimentação
                        $movimentacao->delete();
                        
                        \Log::info('Movimentação deletada', [
                            'movimentacao_id' => $movimentacaoId
                        ]);
                    }
                }

                // 4. Deletar a transação financeira
                $transacaoFinanceira->delete();
                // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))

                \Log::info('Transação excluída com sucesso', [
                    'transacao_id' => $transacaoId,
                    'movimentacao_id' => $movimentacaoId,
                    'entidade_id' => $entidadeId,
                    'valor' => (float) $valor,
                    'tipo' => $tipo,
                    'origem' => $origem,
                    'saldo_recalculado' => $entidade ? $entidade->saldo_atual : null,
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->name
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transação excluída com sucesso!'
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir transação', [
                'transacao_id' => $transacaoFinanceira->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir transação: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Retorna os dados em formato JSON para DataTables.
     */
    public function getData(Request $request)
    {
        // Monta a query base. Se precisar de Eager Loading, faça ->with('entidadeFinanceira','lancamentoPadrao',...)
        $query = TransacaoFinanceira::with([
            'entidadeFinanceira',
            'lancamentoPadrao'
        ]);

        // Transforma em DataTable
        return DataTables::of($query)
            ->addColumn('action', function ($row) {
                // Exemplo: se quiser alguma coluna de ação
                return '<a href="#" class="btn btn-sm btn-primary">Editar</a>';
            })
            ->addColumn('entidade_nome', function ($row) {
                // Exemplo para acessar $row->entidadeFinanceira->nome de forma segura
                return optional($row->entidadeFinanceira)->nome ?? '-';
            })
            ->editColumn('comprovacao_fiscal', function ($row) {
                // Renderiza o ícone igual ao Blade
                if ($row->comprovacao_fiscal === 1) {
                    return '<i class="fas fa-check-circle text-success" title="Tem comprovação Fiscal"></i>';
                } else {
                    return '<i class="bi bi-x-circle-fill text-danger" title="Não tem comprovação fiscal"></i>';
                }
            })
            ->editColumn('lancamentoPadrao.description', function ($row) {
                return optional($row->lancamentoPadrao)->description ?? '-';
            })
            ->editColumn('lancamentoPadrao.category', function ($row) {
                return optional($row->lancamentoPadrao)->category ?? '-';
            })
            ->editColumn('data_competencia', function ($row) {
                return optional($row->data_competencia)->format('d M, Y') ?? '-';
            })
            ->editColumn('tipo', function ($row) {
                // Similar à logic de badge
                if ($row->tipo === 'entrada') {
                    return '<span class="badge badge-light-success">Entrada</span>';
                } else {
                    return '<span class="badge badge-light-danger">Saída</span>';
                }
            })
            ->editColumn('valor', function ($row) {
                // Formata valor (converte centavos para reais)
                return 'R$ ' . number_format((float) $row->valor, 2, ',', '.'); // Valor já está em DECIMAL
            })
            ->rawColumns(['comprovacao_fiscal', 'tipo', 'action']) // Indica quais colunas podem ter HTML
            ->make(true);
    }

    public function grafico(Request $request)
    {
        // Obtém o mês e ano selecionado ou usa o mês atual como padrão
        $mesSelecionado = $request->input('mes', Carbon::now()->month);
        $anoSelecionado = $request->input('ano', Carbon::now()->year);

        // Obtém a quantidade de dias no mês selecionado
        $diasNoMes = Carbon::create($anoSelecionado, $mesSelecionado, 1)->daysInMonth;

        // Inicializa arrays para armazenar os dados do gráfico
        $dias = [];
        $recebimentos = [];
        $pagamentos = [];
        $transfEntrada = [];
        $transfSaida = [];
        $saldo = [];

        // Busca todas as transações do mês selecionado
        $transacoes = TransacaoFinanceira::whereYear('data_competencia', $anoSelecionado)
            ->whereMonth('data_competencia', $mesSelecionado)
            ->orderBy('data_competencia')
            ->get();

        // Variável para armazenar o saldo acumulado
        $saldoAcumulado = 0;

        // Loop para preencher os dados do gráfico para cada dia do mês
        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $dataLoop = Carbon::create($anoSelecionado, $mesSelecionado, $dia)->format('Y-m-d');

            // Filtra as transações do dia
            $transacoesDia = $transacoes->filter(fn($t) => $t->data_competencia->format('Y-m-d') === $dataLoop);

            // Calcula os totais de cada tipo de transação no dia
            $valorRecebimentos = $transacoesDia->where('tipo', 'entrada')->sum('valor');
            $valorPagamentos = $transacoesDia->where('tipo', 'saida')->sum('valor');
            $valorTransfEnt = $transacoesDia->where('tipo', 'transfer_in')->sum('valor');
            $valorTransfSai = $transacoesDia->where('tipo', 'transfer_out')->sum('valor');

            // Atualiza o saldo acumulado (valores já estão em DECIMAL)
            $saldoAcumulado += (($valorRecebimentos + $valorTransfEnt) - ($valorPagamentos + $valorTransfSai));

            // Adiciona os valores ao array (valores já estão em DECIMAL)
            $dias[] = $dia;
            $recebimentos[] = (float) $valorRecebimentos;
            $pagamentos[] = (float) $valorPagamentos;
            $transfEntrada[] = (float) $valorTransfEnt;
            $transfSaida[] = (float) $valorTransfSai;
            $saldo[] = (float) $saldoAcumulado;
        }

        // Retorna para a view com os dados
        return view('financeiro.graficos.mensal', compact(
            'dias',
            'recebimentos',
            'pagamentos',
            'transfEntrada',
            'transfSaida',
            'saldo',
            'mesSelecionado',
            'anoSelecionado'
        ));
    }
}
