<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Support\Money;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Financeiro\ModulosAnexo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\DomusDocumentoLancamentoService;
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

    public function __construct(
        RecurrenceService $recurrenceService,
        protected DomusDocumentoLancamentoService $domusLancamentoService
    ) {
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
                        $domusDoc = $this->domusLancamentoService->findForActiveCompany((int) $request->input('domus_documento_id'));
                        if ($domusDoc) {
                            $this->domusLancamentoService->markLancadoAndAttachAnexo($domusDoc, $caixa);
                            $domusDocumentoId = $domusDoc->id;
                        }
                    } catch (\Exception $e) {
                        Log::warning('Erro ao processar DomusDocumento: '.$e->getMessage());
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
            $domusDocumentoId = null;
            if ($request->filled('domus_documento_id')) {
                try {
                    $domusDoc = $this->domusLancamentoService->findForActiveCompany((int) $request->input('domus_documento_id'));
                    if ($domusDoc) {
                        $this->domusLancamentoService->markLancadoAndAttachAnexo($domusDoc, $caixa);
                        $domusDocumentoId = $domusDoc->id;
                    }
                } catch (\Exception $e) {
                    Log::warning('Erro ao processar DomusDocumento: '.$e->getMessage());
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
    public function destroyById(Request $request, int $id)
    {
        $transacaoFinanceira = TransacaoFinanceira::findOrFail($id);
        return $this->destroy($transacaoFinanceira, $request);
    }

    public function destroy(TransacaoFinanceira $transacaoFinanceira, Request $request)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        if ($transacaoFinanceira->company_id != $activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Transação não encontrada.'
            ], 404);
        }

        $scope       = $request->input('scope', 'single');          // 'single' | 'all'
        $rateioScope = $request->input('rateio_scope', 'parent_only'); // 'parent_only' | 'all' | 'children_only'

        try {
            return \DB::transaction(function () use ($transacaoFinanceira, $scope, $rateioScope) {

                // ── A) Trata lançamentos intercompany (rateio filhos) ──────────────────
                if ($transacaoFinanceira->rateioFilhos()->exists()) {
                    // 'all' ou 'children_only' → exclui filhos ainda em aberto
                    if ($rateioScope === 'all' || $rateioScope === 'children_only') {
                        $transacaoFinanceira->rateioFilhos()
                            ->whereNotIn('situacao', ['pago', 'recebido'])
                            ->get()
                            ->each(fn ($filho) => $this->deleteSingle($filho));
                    }
                    // Desvincula todos os filhos restantes (pagos/recebidos viram independentes)
                    $transacaoFinanceira->rateioFilhos()->update(['rateio_origem_id' => null]);

                    // 'children_only' → só exclui os filhos; mantém o registro pai
                    if ($rateioScope === 'children_only') {
                        return response()->json([
                            'success' => true,
                            'message' => 'Registros filhos (filiais) excluídos com sucesso! O lançamento da matriz foi mantido.',
                        ]);
                    }
                }

                // ── A2) Transação atual É um filho de rateio ──────────────────────────
                // Regra: um rateio filho NUNCA pode excluir o registro pai nem os
                // irmãos. Operações sobre o pai/irmãos só podem ser disparadas a
                // partir do próprio pai (bloco A acima). Aqui o filho exclui
                // apenas a si mesmo, ignorando qualquer rateio_scope vindo do
                // cliente (defesa em profundidade contra payload manipulado).
                if ($transacaoFinanceira->rateio_origem_id !== null) {
                    $this->deleteSingle($transacaoFinanceira);

                    return response()->json([
                        'success' => true,
                        'message' => 'Lançamento excluído com sucesso!',
                    ]);
                }

                // ── B) scope=all + parcelado ──────────────────────────────────────────
                $ehParcelado = $transacaoFinanceira->parent_id !== null
                    || $transacaoFinanceira->parcelas()->exists();

                if ($scope === 'all' && $ehParcelado) {
                    $rootId = $transacaoFinanceira->parent_id ?? $transacaoFinanceira->id;
                    TransacaoFinanceira::where(function ($q) use ($rootId) {
                        $q->where('id', $rootId)->orWhere('parent_id', $rootId);
                    })
                        ->whereNotIn('situacao', ['pago', 'recebido'])
                        ->get()
                        ->each(fn ($item) => $this->deleteSingle($item));

                    return response()->json([
                        'success' => true,
                        'message' => 'Parcelas excluídas com sucesso! As já pagas/recebidas foram mantidas.',
                    ]);
                }

                // ── C) scope=all + recorrente ─────────────────────────────────────────
                $ehRecorrente = $transacaoFinanceira->recorrencia_id !== null
                    || $transacaoFinanceira->recorrencia()->exists();

                if ($scope === 'all' && $ehRecorrente) {
                    // Busca via coluna recorrencia_id (vínculo direto)
                    $recorrenciaId = $transacaoFinanceira->recorrencia_id;
                    if ($recorrenciaId) {
                        TransacaoFinanceira::where('recorrencia_id', $recorrenciaId)
                            ->whereNotIn('situacao', ['pago', 'recebido'])
                            ->get()
                            ->each(fn ($item) => $this->deleteSingle($item));
                    } else {
                        // Fallback: busca via pivot recorrencia_transacoes
                        $recorrencias = $transacaoFinanceira->recorrencia()->pluck('recorrencias.id');
                        if ($recorrencias->isNotEmpty()) {
                            TransacaoFinanceira::whereHas('recorrencia', fn ($q) => $q->whereIn('recorrencias.id', $recorrencias))
                                ->whereNotIn('situacao', ['pago', 'recebido'])
                                ->get()
                                ->each(fn ($item) => $this->deleteSingle($item));
                        } else {
                            $this->deleteSingle($transacaoFinanceira);
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Ocorrências excluídas com sucesso! As já pagas/recebidas foram mantidas.',
                    ]);
                }

                // ── D) scope=single (padrão) ──────────────────────────────────────────
                $this->deleteSingle($transacaoFinanceira);

                return response()->json([
                    'success' => true,
                    'message' => 'Lançamento excluído com sucesso!',
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir transação', [
                'transacao_id' => $transacaoFinanceira->id,
                'error'        => $e->getMessage(),
                'trace'        => $e->getTraceAsString(),
                'user_id'      => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir transação: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Executa a exclusão física de um único lançamento financeiro,
     * desfazendo vínculos de conciliação e movimentação antes de deletar.
     */
    private function deleteSingle(TransacaoFinanceira $t): void
    {
        // Safety: desvincula qualquer filho de rateio que possa bloquear o observer
        if ($t->rateioFilhos()->exists()) {
            $t->rateioFilhos()->update(['rateio_origem_id' => null]);
        }

        // Desfaz conciliações bancárias vinculadas
        if ($t->bankStatements()->exists()) {
            foreach ($t->bankStatements as $bs) {
                $bs->update([
                    'reconciled'          => false,
                    'status_conciliacao'  => 'pendente',
                ]);
            }
            $t->bankStatements()->detach();
        }

        // Deleta a movimentação (o MovimentacaoObserver atualiza saldo_atual automaticamente).
        // Busca por DUAS vias: movimentacao_id direto OU relação polimórfica (origem_type/origem_id).
        // Transações baixadas via TransacaoFinanceiraService::registrarBaixa() usam apenas o
        // vínculo polimórfico e não preenchem movimentacao_id na transação.
        $movimentacao = null;
        if ($t->movimentacao_id) {
            $movimentacao = Movimentacao::find($t->movimentacao_id);
        }
        if (!$movimentacao) {
            $movimentacao = $t->movimentacao()->first();
        }
        if ($movimentacao) {
            if (\Schema::hasColumn('movimentacoes', 'valor_conciliado')) {
                $movimentacao->valor_conciliado = null;
                $movimentacao->save();
            }
            $movimentacao->delete();
        }

        \Log::info('Transação excluída via deleteSingle', [
            'transacao_id' => $t->id,
            'entidade_id'  => $t->entidade_id,
            'valor'        => (float) $t->valor,
            'tipo'         => $t->tipo,
            'origem'       => $t->origem,
            'user_id'      => Auth::id(),
        ]);

        $t->delete();
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
