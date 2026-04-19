<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Banco;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\HorarioMissa;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use App\Services\ConciliacaoMissasService;
use App\Services\ConciliacaoSuggestionService;
use App\Models\ConciliacaoRegra;
use App\Support\Money;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DB;
use Flasher;
use Illuminate\Http\Request;
use Log;
use Validator;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use App\Jobs\GenerateConciliacaoPdfJob;
use App\Models\PdfGeneration;

class ConciliacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Gera PDF do relatório de conciliações bancárias
     */
    public function gerarPdf(Request $request)
    {
        // 1) Filtros
        $dataInicial = $request->input('data_inicial');
        $dataFinal   = $request->input('data_final');
        $status      = $request->input('status', 'pendente');
        $entidadeId  = $request->input('entidade_id');

        // 2) Query otimizada - IMPORTANTE: Filtrar por company_id para segurança
        $companyId = session('active_company_id');
        $query = BankStatement::where('company_id', $companyId);

        // Filtrar por data se parâmetros forem fornecidos
        if ($dataInicial && $dataFinal) {
            $dataInicialFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dataInicial)->format('Y-m-d');
            $dataFinalFormatted = \Carbon\Carbon::createFromFormat('d/m/Y', $dataFinal)->format('Y-m-d');
            $query->whereBetween('dtposted', [$dataInicialFormatted, $dataFinalFormatted]);
        }

        // Filtrar por status se fornecido e não for 'todos'
        if ($status && $status !== 'todos') {
            $query->where('status_conciliacao', $status);
        }

        // Filtrar por entidade se fornecido
        if ($entidadeId) {
            $query->where('entidade_id', $entidadeId);
        }

        // Buscar conciliações com relacionamentos
        $conciliacoes = $query->with('transacoes')->orderBy('dtposted', 'desc')->get();

        // Buscar entidade se ID foi fornecido
        $entidade = $entidadeId ? EntidadeFinanceira::find($entidadeId) : null;

        // 3) HTML da view
        $html = view('app.relatorios.financeiro.conciliacao_pdf', [
            'conciliacoes'  => $conciliacoes,
            'dataInicial'   => $dataInicial,
            'dataFinal'     => $dataFinal,
            'status'        => $status,
            'entidade'      => $entidade,
            'company'       => Auth::user()->companies()->first(),
        ])->render();

        // 4) PDF
        $pdf = BrowsershotHelper::configureChromePath(
            Browsershot::html($html)
                ->format('A4')
                ->landscape()
                ->showBackground()
                ->margins(8, 8, 8, 8)
        )->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename=conciliacao-bancaria.pdf',
        ]);
    }

    /**
     * Gera PDF de forma assíncrona (não trava o servidor)
     */
    public function gerarPdfAsync(Request $request)
    {
        try {
            // Filtros
            $filters = [
                'data_inicial' => $request->input('data_inicial'),
                'data_final' => $request->input('data_final'),
                'status' => $request->input('status', 'pendente'),
                'entidade_id' => $request->input('entidade_id'),
            ];

            $companyId = session('active_company_id');
            $tenantId = tenant('id');

            // Criar registro de rastreamento
            $pdfGen = PdfGeneration::create([
                'type' => 'conciliacao',
                'user_id' => Auth::id(),
                'company_id' => $companyId,
                'status' => 'pending',
                'parameters' => $filters,
            ]);

            // Despachar job
            GenerateConciliacaoPdfJob::dispatch(
                $filters,
                $companyId,
                Auth::id(),
                $tenantId,
                $pdfGen->id
            );

            return response()->json([
                'success' => true,
                'pdf_id' => $pdfGen->id,
                'message' => 'PDF sendo gerado em background. Aguarde...'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao despachar job de PDF Conciliação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao iniciar geração de PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica status de geração de PDF
     */
    public function checkPdfStatus($id)
    {
        try {
            $pdfGen = PdfGeneration::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'status' => $pdfGen->status,
                'download_url' => $pdfGen->download_url,
                'error_message' => $pdfGen->error_message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF não encontrado'
            ], 404);
        }
    }

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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // O valor já será processado pelo StoreTransacaoFinanceiraRequest usando Money
            // Não é necessário fazer conversão manual aqui

            // Converter data_competencia para formato correto
            if ($request->has('data_competencia')) {
                $dataFormatada = Carbon::createFromFormat('Y-m-d', $request->input('data_competencia'))->format('Y-m-d');
                $request->merge(['data_competencia' => $dataFormatada]);
            }


            // Validação dos dados recebidos
            $validator = Validator::make($request->all(), [
                'data_competencia' => 'required|date',
                'valor' => 'required|numeric|min:0',
                'descricao' => 'required|string|max:255',
                'numero_documento' => 'nullable|string|max:50',
                'entidade_id' => 'required|exists:entidades_financeiras,id',
            ], [
                'valor.numeric' => 'O valor deve ser um número válido.',
                'data_competencia.required' => 'A data de competência é obrigatória.',
            ]);

            // Se a validação falhar, retorna com erros
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Dados validados
            $validatedData = $validator->validated();

            // Busca a transação financeira
            $transacao = TransacaoFinanceira::findOrFail($id);
            $movimentacao = Movimentacao::findOrFail($transacao->movimentacao_id);

            // ✅ Saldos serão recalculados dinamicamente
            // Nenhuma modificação direta necessária
            
            \Log::debug('Atualizando movimentação - saldos serão recalculados', [
                'movimentacao_id' => $movimentacao->id,
                'entidade_anterior' => $movimentacao->entidade_id,
                'entidade_nova' => $validatedData['entidade_id']
            ]);

            // Atualiza os dados da movimentação
            $movimentacao->update([
                'entidade_id' => $validatedData['entidade_id'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'updated_by' => Auth::user()->id,
            ]);

            // Atualiza a transação financeira
            $transacao->update([
                'data_competencia' => $validatedData['data_competencia'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'numero_documento' => $validatedData['numero_documento'],
                'movimentacao_id' => $movimentacao->id,
            ]);

            // Mensagem de sucesso
            Flasher::addSuccess('Transação financeira atualizada com sucesso!');
            return redirect()->back()->with('message', 'Atualização realizada com sucesso!');
        } catch (\Exception $e) {
            // Registro do erro para depuração
            Log::error('Erro ao atualizar a transação financeira: ' . $e->getMessage());

            // Redireciona com mensagem de erro
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar: ' . $e->getMessage());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function conciliarTransacao($transactionId)
    {
        $companyId = session('active_company_id');
        $transacao = BankStatement::where('company_id', $companyId)->find($transactionId);

        if ($transacao) {
            $transacao->update(['reconciled' => true]);
            return "Transação ID {$transactionId} conciliada com sucesso!";
        }

        return "Transação não encontrada.";
    }

    // Exibir transações pendentes de conciliação
    // Você pode criar uma tela para visualizar apenas as transações não conciliadas:
    //$naoConciliadas = BankStatement::where('reconciled', false)->get();

    public function conciliar(StoreTransacaoFinanceiraRequest $request)
    {
        // Inicia uma transação para evitar inconsistências no banco
        return DB::transaction(function () use ($request) {
            // Recupera a empresa do usuário logado
            $companyId = session('active_company_id');

            if (!$companyId) {
                return redirect()->back()->with('error', 'Companhia não encontrada.');
            }


            // Processa os dados validados
            $validatedData = $request->validated();

            Log::debug('Conciliação iniciada', [
                'tipo' => $validatedData['tipo'],
                'user_id' => Auth::id(),
            ]);

            // Verifica se "descricao2" foi enviado e atribui a "descricao"
            $validatedData['descricao'] = $validatedData['descricao2'] ;

            // **Define a situação baseada no tipo (entrada/saida)**
            // Quando vem de conciliação, marca automaticamente como recebido ou pago
            $validatedData['situacao'] = $validatedData['tipo'] === 'entrada' ? 'recebido' : 'pago';

            // **Garante que o valor sempre seja positivo**
            $validatedData['valor'] = abs($validatedData['valor']);

            Log::debug('Dados processados', [
                'valor_final' => $validatedData['valor'],
                'situacao' => $validatedData['situacao'],
            ]);

            // Busca o lançamento padrão para obter conta_debito_id e conta_credito_id se não foram enviados
            if (isset($validatedData['lancamento_padrao_id']) && !isset($validatedData['conta_debito_id'])) {
                $lancamentoPadrao = LancamentoPadrao::find($validatedData['lancamento_padrao_id']);
                if ($lancamentoPadrao) {
                    // Se não foram enviados no request, busca do lançamento padrão
                    if (!isset($validatedData['conta_debito_id']) && $lancamentoPadrao->conta_debito_id) {
                        $validatedData['conta_debito_id'] = $lancamentoPadrao->conta_debito_id;
                    }
                    if (!isset($validatedData['conta_credito_id']) && $lancamentoPadrao->conta_credito_id) {
                        $validatedData['conta_credito_id'] = $lancamentoPadrao->conta_credito_id;
                    }
                }
            }

            // Adiciona informações padrão
            $validatedData['company_id'] = $companyId;
            $validatedData['origem'] = 'conciliacao_bancaria'; // Marca origem da transação
            $validatedData['created_by'] = Auth::id();
            $validatedData['created_by_name'] = Auth::user()->name;
            $validatedData['updated_by'] = Auth::id();
            $validatedData['updated_by_name'] = Auth::user()->name;

            // Gera movimentação financeira
            $movimentacao = $this->movimentacao($validatedData);
            $validatedData['movimentacao_id'] = $movimentacao->id;

            Log::debug('Movimentação criada', [
                'movimentacao_id' => $movimentacao->id,
            ]);

            // Cria a transação financeira
            $caixa = TransacaoFinanceira::create($validatedData);

            // Atualiza a movimentação com o relacionamento polimórfico
            // para permitir busca bidirecional (transação → movimentação e movimentação → transação)
            $movimentacao->origem_id = $caixa->id;
            $movimentacao->origem_type = TransacaoFinanceira::class;
            $movimentacao->save();

            Log::debug('Transação financeira criada', [
                'transacao_id' => $caixa->id,
                'movimentacao_origem_id' => $movimentacao->origem_id,
            ]);

            // Processa lançamentos padrão
            $this->processarLancamentoPadrao($validatedData);

            // Processa anexos, se existirem
            $this->processarAnexos($request, $caixa);

            // Recupera os registros necessários (com filtro de company_id para segurança multi-tenant)
            $bankStatement = BankStatement::where('company_id', $companyId)->find($request->input('bank_statement_id'));
            // Usar a transação que acabamos de criar, não buscar por transacao_id
            $transacao = $caixa; // $caixa é a TransacaoFinanceira que acabamos de criar

            Log::debug('Busca de registros para conciliação', [
                'bank_statement_id' => $request->input('bank_statement_id'),
                'transacao_criada_id' => $caixa->id,
                'bank_statement_found' => $bankStatement ? 'sim' : 'não',
            ]);

            if (!$bankStatement) {
                Log::error('Erro: BankStatement não encontrado no método conciliar', [
                    'bank_statement_id' => $request->input('bank_statement_id'),
                    'bank_statement_found' => $bankStatement ? 'sim' : 'não'
                ]);
                return redirect()->back()->with('error', 'Erro ao buscar dados para conciliação.');
            }

            // Define o valor conciliado
            // CRÍTICO: O StoreTransacaoFinanceiraRequest já converte valor para DECIMAL em prepareForValidation()
            // O $validatedData['valor'] JÁ ESTÁ EM DECIMAL (float)
            // O $transacao->valor TAMBÉM está em DECIMAL (sem cast integer no modelo)
            // NÃO devemos converter novamente!
            
            if (isset($validatedData['valor'])) {
                // ✅ VALOR JÁ ESTÁ EM DECIMAL (vindo do request validado)
                $valorConciliado = (float) $validatedData['valor'];
                
                Log::debug('Valor conciliado definido', [
                    'valor_conciliado' => $valorConciliado,
                ]);
            } else {
                // Fallback: usa valor da transação (que já está em DECIMAL)
                $valorConciliado = (float) $transacao->valor;
                
                Log::debug('Valor conciliado via fallback (transação)', [
                    'valor_conciliado' => $valorConciliado,
                ]);
            }

            Log::debug('Cálculo de valor conciliado', [
                'valor_conciliado' => $valorConciliado,
                'bank_statement_amount' => $bankStatement->amount,
            ]);

            // ✅ A lógica de status é centralizada em BankStatement::conciliarCom()
            // **Chama o método conciliarCom() que atualiza saldo e cria pivot**
            $bankStatement->conciliarCom($transacao, $valorConciliado);

            Log::info('Conciliação finalizada', [
                'bank_statement_id' => $bankStatement->id,
                'transacao_id' => $transacao->id,
                'valor_conciliado' => $valorConciliado,
                'status' => $bankStatement->status_conciliacao,
                'entidade_id' => $validatedData['entidade_id'],
                'user_id' => Auth::id(),
            ]);

            // 🤖 APRENDIZADO AUTOMÁTICO - Sistema aprende com a ação do usuário
            $this->aprenderComUsuario($request, $bankStatement);

            // 📊 FEEDBACK DA SUGESTÃO — registra aceite/rejeição para melhoria contínua
            $this->registrarFeedbackSugestao($request, $bankStatement);

            // Retornar JSON se for requisição AJAX, senão redirecionar
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                // Busca dados atualizados para retornar
                $entidadeId = $request->input('entidade_id');
                
                // Total de pendentes (geral, sem filtro de data)
                $totalPendentes = BankStatement::where('company_id', $companyId)
                    ->where('entidade_financeira_id', $entidadeId)
                    ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                    ->whereDoesntHave('transacoes')
                    ->where(function ($q) {
                        $q->where('conciliado_com_missa', false)
                          ->orWhereNull('conciliado_com_missa');
                    })
                    ->count();
                
                // Contadores por tipo
                $baseQuery = BankStatement::where('company_id', $companyId)
                    ->where('entidade_financeira_id', $entidadeId)
                    ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                    ->whereDoesntHave('transacoes')
                    ->where(function ($q) {
                        $q->where('conciliado_com_missa', false)
                          ->orWhereNull('conciliado_com_missa');
                    });
                
                $counts = [
                    'all' => (clone $baseQuery)->count(),
                    'received' => (clone $baseQuery)->where('amount_cents', '>', 0)->count(),
                    'paid' => (clone $baseQuery)->where('amount_cents', '<', 0)->count(),
                ];
                
                // 🔄 REFRESH da entidade para pegar o saldo_atual ATUALIZADO
                $entidade = \App\Models\EntidadeFinanceira::find($entidadeId);
                $entidade->refresh(); // Recarrega do banco de dados
                $saldoAtual = $entidade ? $entidade->saldo_atual : 0;
                $valorPendente = (clone $baseQuery)->sum('amount');
                
                Log::debug('Contadores atualizados após conciliação', [
                    'counts' => $counts,
                    'entidade_id' => $entidadeId,
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Lançamento conciliado com sucesso!',
                    'data' => [
                        'transacao_id' => $transacao->id,
                        'bank_statement_id' => $bankStatement->id,
                        'status' => $bankStatement->status_conciliacao,
                        'total_pendentes' => $totalPendentes,
                        'counts' => $counts,
                        'informacoesAdicionais' => [
                            'saldo_atual' => $saldoAtual,
                            'valor_pendente_conciliacao' => abs($valorPendente),
                            'data_ultima_atualizacao' => now(),
                        ]
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Lançamento conciliado com sucesso!');
        });
    }

    public function pivot(Request $request)
    {
        // Log da requisição recebida
        Log::debug('Iniciando conciliação pivot', [
            'user_id' => Auth::id(),
            'bank_statement_id' => $request->input('bank_statement_id'),
        ]);

        return DB::transaction(function () use ($request) {
            try {
                // Validação dos dados de entrada
                $request->validate([
                    'bank_statement_id' => 'required|exists:bank_statements,id',
                    'transacao_financeira_id' => 'required|exists:transacoes_financeiras,id',
                    'valor_conciliado' => 'nullable|numeric|min:0'
                ]);

                Log::debug('Validação pivot ok', [
                    'bank_statement_id' => $request->bank_statement_id,
                    'transacao_financeira_id' => $request->transacao_financeira_id,
                ]);

                // ✅ Busca os registros corretamente (com filtro de company_id para segurança multi-tenant)
                $companyId = session('active_company_id');
                $bankStatement = BankStatement::where('company_id', $companyId)->findOrFail($request->bank_statement_id);
                $transacao = TransacaoFinanceira::findOrFail($request->transacao_financeira_id);

                Log::debug('Registros encontrados para pivot', [
                    'bank_statement_id' => $bankStatement->id,
                    'transacao_id' => $transacao->id,
                ]);

                // ✅ Define o valor conciliado
                // CRÍTICO: O valor SEMPRE deve vir em REAIS (decimal) do frontend
                // Normaliza o valor removendo formatação e converte para DECIMAL
                $valorRequest = $request->valor_conciliado ?? $transacao->valor;
                
                // Usa Money para normalizar e converter para DECIMAL
                // Se o valor já está em DECIMAL (float), usa fromDatabase
                if (is_numeric($valorRequest) && !is_string($valorRequest)) {
                    // Valor já está em DECIMAL (vindo de $transacao->valor que agora é DECIMAL)
                    $money = Money::fromDatabase((float) $valorRequest);
                } else {
                    // Valor vem do frontend em formato brasileiro (string) ou precisa conversão
                    $money = Money::fromHumanInput((string) $valorRequest);
                }
                
                // ✅ CONVERSÃO: Formato brasileiro → DECIMAL usando Money
                // valor_conciliado no pivot é DECIMAL, não INTEGER
                $valorConciliado = $money->toDatabase();

                Log::debug('Cálculo de valor conciliado (pivot)', [
                    'valor_conciliado' => $valorConciliado,
                    'bank_statement_amount' => $bankStatement->amount,
                ]);

                // Verificar se já existe conciliação
                $conciliacaoExistente = $bankStatement->transacoes()->where('transacao_financeira_id', $transacao->id)->exists();

                if ($conciliacaoExistente) {
                    Log::warning('Tentativa de conciliar transação já conciliada', [
                        'bank_statement_id' => $bankStatement->id,
                        'transacao_id' => $transacao->id
                    ]);

                    return redirect()->back()->with('warning', 'Esta transação já foi conciliada anteriormente.');
                }

                // ✅ Chama o método diretamente no modelo
                $bankStatement->conciliarCom($transacao, $valorConciliado);

                Log::info('Conciliação pivot realizada', [
                    'bank_statement_id' => $bankStatement->id,
                    'transacao_id' => $transacao->id,
                    'valor_conciliado' => $valorConciliado,
                    'status' => $bankStatement->status_conciliacao,
                    'user_id' => Auth::id(),
                ]);

                // Retornar JSON se for requisição AJAX
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    // ✅ Busca dados atualizados para retornar (consistente com conciliar())
                    $entidadeId = $bankStatement->entidade_financeira_id;
                    
                    // Contadores por tipo (com filtros consistentes)
                    $baseQuery = BankStatement::where('company_id', $companyId)
                        ->where('entidade_financeira_id', $entidadeId)
                        ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                        ->whereDoesntHave('transacoes')
                        ->where(function ($q) {
                            $q->where('conciliado_com_missa', false)
                              ->orWhereNull('conciliado_com_missa');
                        });
                    
                    $counts = [
                        'all' => (clone $baseQuery)->count(),
                        'received' => (clone $baseQuery)->where('amount_cents', '>', 0)->count(),
                        'paid' => (clone $baseQuery)->where('amount_cents', '<', 0)->count(),
                    ];

                    $totalPendentes = $counts['all'];
                    
                    // 🔄 REFRESH da entidade para pegar o saldo_atual ATUALIZADO
                    $entidade = \App\Models\EntidadeFinanceira::find($entidadeId);
                    $entidade?->refresh();
                    $saldoAtual = $entidade ? $entidade->saldo_atual : 0;
                    $valorPendente = (clone $baseQuery)->sum('amount');

                    return response()->json([
                        'success' => true,
                        'message' => 'Conciliação realizada com sucesso!',
                        'data' => [
                            'transacao_id' => $transacao->id,
                            'bank_statement_id' => $bankStatement->id,
                            'status' => $bankStatement->status_conciliacao,
                            'total_pendentes' => $totalPendentes,
                            'counts' => $counts,
                            'informacoesAdicionais' => [
                                'saldo_atual' => $saldoAtual,
                                'valor_pendente_conciliacao' => abs($valorPendente),
                                'data_ultima_atualizacao' => now(),
                            ]
                        ]
                    ]);
                }

                return redirect()->back()->with('success', 'Conciliação realizada com sucesso!');

            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Erro de validação na conciliação', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);

                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dados inválidos para conciliação.',
                        'errors' => $e->errors()
                    ], 422);
                }

                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput()
                    ->with('error', 'Dados inválidos para conciliação.');

            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                Log::error('Registro não encontrado na conciliação', [
                    'message' => $e->getMessage(),
                    'request_data' => $request->all()
                ]);

                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao buscar dados para conciliação.'
                    ], 404);
                }

                return redirect()->back()->with('error', 'Erro ao buscar dados para conciliação.');

            } catch (\Exception $e) {
                Log::error('Erro inesperado na conciliação', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all()
                ]);

                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro interno do servidor. Verifique os logs para mais detalhes.'
                    ], 500);
                }

                return redirect()->back()->with('error', 'Erro interno do servidor. Verifique os logs para mais detalhes.');
            }
        });
    }




    public function ignorarLançamento(Request $request)
    {
        $request->validate([
            'bank_statement_id' => 'required|exists:bank_statements,id',
        ]);

        $companyId = session('active_company_id');
        $bankStatement = BankStatement::where('company_id', $companyId)->findOrFail($request->bank_statement_id);

        // Marcar como ignorado
        $bankStatement->update([
            'status_conciliacao' => 'ignorado',
            'reconciled' => false, // Para garantir que não apareça como conciliado
        ]);

        return redirect()->back()->with('success', 'Lançamento ignorado com sucesso!');
    }

    public function ignorar(Request $request, $id)
    {
        // Encontra o lançamento bancário pelo ID (com filtro de company_id para segurança multi-tenant)
        $companyId = session('active_company_id');
        $bankStatement = BankStatement::where('company_id', $companyId)->findOrFail($id);

        // Atualiza o status para "ignorado"
        $bankStatement->update(['status_conciliacao' => 'ignorado']);

        if ($request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'success' => true,
                'message' => 'Lançamento ignorado com sucesso!',
            ]);
        }

        return redirect()->back()->with('success', 'Lançamento ignorado com sucesso!');
    }

    /**
     * Processa movimentacao.
     */
    private function movimentacao(array $validatedData)
    {
        Log::debug('Criando movimentação', [
            'tipo' => $validatedData['tipo'],
        ]);

        // Cria o lançamento na tabela 'movimentacoes'
        $movimentacao = Movimentacao::create([
            'entidade_id' => $validatedData['entidade_id'],
            'tipo'        => $validatedData['tipo'],
            'valor'       => $validatedData['valor'],
            'data'        => $validatedData['data_competencia'],
            'descricao'   => $validatedData['descricao'],
            'company_id'  => $validatedData['company_id'],
            'created_by'  => $validatedData['created_by'],
            'created_by_name' => $validatedData['created_by_name'],
            'updated_by'      => $validatedData['updated_by'],
            'updated_by_name' => $validatedData['updated_by_name'],
            'lancamento_padrao_id' => $validatedData['lancamento_padrao_id'] ?? null,
            'conta_debito_id' => $validatedData['conta_debito_id'] ?? null,
            'conta_credito_id' => $validatedData['conta_credito_id'] ?? null,
            'data_competencia' => $validatedData['data_competencia'] ?? null,
        ]);

        Log::debug('Movimentação salva', [
            'movimentacao_id' => $movimentacao->id,
        ]);

        // Retorna o objeto Movimentacao recém-criado, de onde poderemos pegar o ID
        return $movimentacao;
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
     *
     * Suporta dois formatos:
     *  1) Formulário complexo: anexos[index][arquivo], anexos[index][link], etc.
     *  2) Formulário simples (conciliação): campo "anexo" (singular) com um único arquivo.
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $caixa)
    {
        // ─── Formato 1: anexos[index][arquivo] (formulário completo) ───
        if ($request->has('anexos') && is_array($request->input('anexos'))) {
            $this->processarAnexosComplexos($request, $caixa);
        }

        // ─── Formato 2: campo "anexo" simples (formulário de conciliação) ───
        if ($request->hasFile('anexo')) {
            $file = $request->file('anexo');

            if ($file->isValid()) {
                $nomeOriginal = $file->getClientOriginalName();
                $anexoName = time() . '_' . $nomeOriginal;
                $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                ModulosAnexo::create([
                    'anexavel_id'      => $caixa->id,
                    'anexavel_type'    => TransacaoFinanceira::class,
                    'forma_anexo'      => 'arquivo',
                    'nome_arquivo'     => $nomeOriginal,
                    'caminho_arquivo'  => $anexoPath,
                    'tipo_arquivo'     => $file->getMimeType() ?? '',
                    'extensao_arquivo' => $file->getClientOriginalExtension(),
                    'mime_type'        => $file->getMimeType() ?? '',
                    'tamanho_arquivo'  => $file->getSize(),
                    'tipo_anexo'       => 'comprovante',
                    'descricao'        => 'Anexo da conciliação bancária',
                    'status'           => 'ativo',
                    'data_upload'      => now(),
                    'created_by'       => Auth::id(),
                    'created_by_name'  => Auth::user()->name,
                ]);
            }
        }

        // Atualiza automaticamente o campo comprovacao_fiscal
        $caixa->updateComprovacaoFiscal();
    }

    /**
     * Processa anexos no formato complexo: anexos[index][arquivo/link].
     */
    private function processarAnexosComplexos(Request $request, TransacaoFinanceira $caixa)
    {
        $anexos = $request->input('anexos');
        $allFiles = $request->allFiles();

        foreach ($anexos as $index => $anexoData) {
            $formaAnexo = $anexoData['forma_anexo'] ?? 'arquivo';
            $tipoAnexo = $anexoData['tipo_anexo'] ?? null;
            $descricao = $anexoData['descricao'] ?? null;

            if ($formaAnexo === 'arquivo') {
                // Tenta encontrar o arquivo usando diferentes chaves
                $file = null;

                // Tenta com notação de ponto
                $fileKey = "anexos.{$index}.arquivo";
                if ($request->hasFile($fileKey)) {
                    $file = $request->file($fileKey);
                }

                // Se não encontrou, tenta buscar em allFiles
                if (!$file && isset($allFiles['anexos'][$index]['arquivo'])) {
                    $file = $allFiles['anexos'][$index]['arquivo'];
                }

                if ($file && $file->isValid()) {
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
     * Busca contas financeiras disponíveis para transferência (exceto a conta de origem)
     */
    public function contasDisponiveis(Request $request)
    {
        try {
            $entidadeOrigemId = $request->input('entidade_origem_id');
            $companyId = session('active_company_id'); // Recupera a empresa do usuário logado

            // Log para debug
            Log::debug('Buscando contas disponíveis', [
                'entidade_origem_id' => $entidadeOrigemId,
            ]);

            // Busca todas as entidades financeiras da mesma empresa, exceto a de origem
            $query = EntidadeFinanceira::where('company_id', $companyId)
                ->where('tipo', 'banco'); // Apenas contas bancárias

            // Se houver entidade de origem, exclui ela
            if ($entidadeOrigemId) {
                $query->where('id', '!=', $entidadeOrigemId);
            }

            $contas = $query->orderBy('nome', 'asc')
                ->with('bank:id,compe_code,name') // Eager load banco para performance
                ->get()
                ->map(function ($conta) {
                    $accountTypeLabels = [
                        'corrente' => 'Conta Corrente',
                        'poupanca' => 'Poupança',
                        'aplicacao' => 'Aplicação',
                        'renda_fixa' => 'Renda Fixa',
                        'tesouro_direto' => 'Tesouro Direto',
                    ];

                    return [
                        'id' => $conta->id,
                        'nome' => $conta->nome,
                        'tipo' => $conta->tipo,
                        'account_type' => $conta->account_type,
                        'account_type_label' => $conta->account_type ? ($accountTypeLabels[$conta->account_type] ?? ucfirst($conta->account_type)) : null,
                        'saldo_atual' => $conta->saldo_atual,
                        'banco_code' => $conta->bank?->compe_code, // Código do banco para matching de mov. interna
                        'banco_nome' => $conta->bank?->name,
                    ];
                });

            // Log para debug
            Log::debug('Contas encontradas', [
                'total' => $contas->count(),
            ]);

            return response()->json([
                'success' => true,
                'contas' => $contas,
                'total' => $contas->count() // Adiciona total para debug
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar contas disponíveis para transferência', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'entidade_origem_id' => $request->input('entidade_origem_id'),
                'company_id' => session('active_company_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas disponíveis: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Processa a transferência entre contas bancárias
     * 
     * Simplificado: não exige LP nem centro de custo para movimentações internas
     */
    public function transferir(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                // Validação dos dados (LP e centro de custo são opcionais)
                $validated = $request->validate([
                    'bank_statement_id' => 'required|exists:bank_statements,id',
                    'entidade_origem_id' => 'required|exists:entidades_financeiras,id',
                    'entidade_destino_id' => 'required|exists:entidades_financeiras,id|different:entidade_origem_id',
                    'valor' => 'required|numeric|min:0.01',
                    'data_transferencia' => 'required|date',
                    'lancamento_padrao_id' => 'nullable|exists:lancamento_padraos,id',
                    'cost_center_id' => 'nullable|exists:cost_centers,id',
                    'descricao' => 'nullable|string|max:500',
                    'checknum' => 'nullable|string|max:100',
                ]);

                // Filtro de company_id para segurança multi-tenant
                $companyId = session('active_company_id');

                if (!$companyId) {
                    return redirect()->back()->with('error', 'Companhia não encontrada.');
                }

                $bankStatement = BankStatement::where('company_id', $companyId)->findOrFail($validated['bank_statement_id']);
                $entidadeOrigem = EntidadeFinanceira::findOrFail($validated['entidade_origem_id']);
                $entidadeDestino = EntidadeFinanceira::findOrFail($validated['entidade_destino_id']);

                // Usa Money para converter formato brasileiro → decimal
                $money = Money::fromHumanInput((string) $validated['valor']);
                $valor = $money->toDatabase();

                // ✅ Determina o tipo baseado no sinal do amount do bank statement
                // amount negativo = pagamento (saída), positivo = recebimento (entrada)
                $tipo = $bankStatement->amount < 0 ? 'saida' : 'entrada';

                // Busca o lançamento padrão se fornecido
                $lancamentoPadrao = isset($validated['lancamento_padrao_id']) 
                    ? LancamentoPadrao::find($validated['lancamento_padrao_id'])
                    : null;

                // Prepara os dados para criar a transação (apenas da conta de origem - conciliação)
                // Converte valor para DECIMAL usando Money
                $moneyValor = Money::fromDatabase($valor);

                // Transferência já foi realizada, então a situação é PAGO (saída) ou RECEBIDO (entrada)
                $situacao = $tipo === 'saida' 
                    ? \App\Enums\SituacaoTransacao::PAGO 
                    : \App\Enums\SituacaoTransacao::RECEBIDO;
                
                $validatedData = [
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data_transferencia'],
                    'data_vencimento' => $validated['data_transferencia'], // Mesma data
                    'data_pagamento' => $validated['data_transferencia'],  // Já foi pago/recebido
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => $tipo,
                    'valor' => $moneyValor->toDatabase(), // TransacaoFinanceira usa DECIMAL
                    'situacao' => $situacao, // Pago ou Recebido (transferência já realizada)
                    'descricao' => $validated['descricao'] ?? 'Transferência para ' . $entidadeDestino->nome,
                    'lancamento_padrao_id' => $validated['lancamento_padrao_id'] ?? null,
                    'cost_center_id' => $validated['cost_center_id'] ?? null,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência automática entre contas bancárias - Conta destino: ' . $entidadeDestino->nome,
                    'numero_documento' => $validated['checknum'] ?? null,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name,
                ];

                // Adiciona campos contábeis do lançamento padrão se existirem
                if ($lancamentoPadrao) {
                    $validatedData['conta_debito_id'] = $lancamentoPadrao->conta_debito_id ?? null;
                    $validatedData['conta_credito_id'] = $lancamentoPadrao->conta_credito_id ?? null;
                }

                // Cria movimentação financeira na ORIGEM
                $movimentacao = $this->movimentacao($validatedData);
                $validatedData['movimentacao_id'] = $movimentacao->id;

                // Cria a transação financeira (da conta de origem)
                $transacao = TransacaoFinanceira::create($validatedData);

                // ✅ Cria movimentação financeira no DESTINO (tipo invertido)
                // Se saiu da origem (saída), entra no destino (entrada) e vice-versa
                $tipoDestino = $tipo === 'saida' ? 'entrada' : 'saida';
                $situacaoDestino = $tipoDestino === 'saida'
                    ? \App\Enums\SituacaoTransacao::PAGO
                    : \App\Enums\SituacaoTransacao::RECEBIDO;

                $dadosDestino = $validatedData;
                $dadosDestino['entidade_id'] = $entidadeDestino->id;
                $dadosDestino['tipo'] = $tipoDestino;
                $dadosDestino['situacao'] = $situacaoDestino;
                $dadosDestino['descricao'] = $validated['descricao'] ?? 'Transferência de ' . $entidadeOrigem->nome;
                $dadosDestino['historico_complementar'] = 'Transferência automática entre contas bancárias - Conta origem: ' . $entidadeOrigem->nome;

                $movimentacaoDestino = $this->movimentacao($dadosDestino);
                $dadosDestino['movimentacao_id'] = $movimentacaoDestino->id;

                // Cria a transação financeira no destino
                $transacaoDestino = TransacaoFinanceira::create($dadosDestino);

                // Processa lançamentos padrão
                $this->processarLancamentoPadrao($validatedData);

                // Processa anexos, se existirem
                $this->processarAnexos($request, $transacao);

                // Define o valor conciliado
                $valorConciliado = abs($valor);

                // Lógica para definir o status
                if (bccomp($valorConciliado, abs($bankStatement->amount), 2) === 0) {
                    $status = 'ok'; // Conciliação perfeita
                } elseif ($valorConciliado < abs($bankStatement->amount)) {
                    $status = 'parcial'; // Conciliação parcial (falta valor)
                } elseif ($valorConciliado > abs($bankStatement->amount)) {
                    $status = 'divergente'; // Conciliação com excesso
                } else {
                    $status = 'pendente'; // Algo inesperado, pendente de verificação
                }

                // Marca o bank statement como conciliado
                $bankStatement->update([
                    'reconciled' => true,
                    'status_conciliacao' => $status,
                ]);

                // Vincula a transação ao bank statement (apenas uma transação)
                $bankStatement->transacoes()->attach($transacao->id, [
                    'valor_conciliado' => $valorConciliado,
                    'status_conciliacao' => $status,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))

                Log::info('Transferência conciliada', [
                    'bank_statement_id' => $bankStatement->id,
                    'entidade_origem_id' => $entidadeOrigem->id,
                    'entidade_destino_id' => $entidadeDestino->id,
                    'transacao_id' => $transacao->id,
                    'user_id' => Auth::id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transferência conciliada com sucesso!',
                    'transacao_id' => $transacao->id,
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $e->errors(),
                ], 422);
            } catch (\Exception $e) {
                Log::error('Erro ao processar conciliação de transferência', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao processar conciliação: ' . $e->getMessage(),
                ], 500);
            }
        });
    }

    /**
     * Processa conciliação manual de missas
     */
    public function processarConciliacaoMissas(Request $request)
    {
        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 400);
            }

            $bankStatementIds = $request->input('bank_statement_ids', []);

            $bankStatements = null;
            // Se array vazio ou não fornecido, processa todas as não conciliadas
            if (!empty($bankStatementIds)) {
                $bankStatements = BankStatement::where('company_id', $companyId)
                    ->whereIn('id', $bankStatementIds)
                    ->get();
            }
            // Se $bankStatements for null, o serviço processará todas as não conciliadas

            $conciliacaoService = new ConciliacaoMissasService();
            $estatisticas = $conciliacaoService->processarTransacoes($companyId, $bankStatements);

            $mensagem = 'Conciliação processada com sucesso!';
            if ($estatisticas['total_processadas'] > 0) {
                $mensagem .= sprintf(
                    ' Processadas: %d, Conciliadas: %d, Valor: R$ %s',
                    $estatisticas['total_processadas'],
                    $estatisticas['conciliadas'],
                    number_format($estatisticas['valor_total'], 2, ',', '.')
                );
            } else {
                $mensagem = 'Nenhuma transação relevante encontrada para processar.';
            }

            return response()->json([
                'success' => true,
                'message' => $mensagem,
                'estatisticas' => $estatisticas
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao processar conciliação de missas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar conciliação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados para o modal de conciliação de missas
     */
    public function getConciliacoesMissas(Request $request)
    {
        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 400);
            }

            // Busca transações conciliadas com missas
            $transacoesConciliadas = BankStatement::where('company_id', $companyId)
                ->where('conciliado_com_missa', true)
                ->with(['horarioMissa', 'transacoes'])
                ->get();

            // Estatísticas
            $totalConciliadas = $transacoesConciliadas->count();
            $valorTotal = $transacoesConciliadas->sum('amount');
            $missasEnvolvidas = $transacoesConciliadas->pluck('horario_missa_id')->unique()->count();

            // Última atualização
            $ultimaAtualizacao = $transacoesConciliadas->max('updated_at');

            // Lista de transações para a tabela
            $listaTransacoes = $transacoesConciliadas->map(function ($statement) {
                $transacaoFinanceira = $statement->transacoes->first();
                return [
                    'id' => $statement->id,
                    'nome' => $transacaoFinanceira
                        ? ($transacaoFinanceira->descricao ?? 'Coleta de missa')
                        : ($statement->memo ?? 'Coleta de missa'),
                    'valor' => number_format($statement->amount, 2, ',', '.'),
                    'valor_raw' => $statement->amount,
                    'data' => $statement->transaction_datetime
                        ? Carbon::parse($statement->transaction_datetime)->format('d/m/Y H:i')
                        : Carbon::parse($statement->dtposted)->format('d/m/Y H:i'),
                    'missa' => $statement->horarioMissa
                        ? $statement->horarioMissa->dia_semana . ' às ' . (is_string($statement->horarioMissa->horario)
                            ? substr($statement->horarioMissa->horario, 0, 5)
                            : Carbon::parse($statement->horarioMissa->horario)->format('H:i'))
                        : 'N/A'
                ];
            });

            return response()->json([
                'success' => true,
                'estatisticas' => [
                    'total_conciliadas' => $totalConciliadas,
                    'valor_total' => number_format($valorTotal, 2, ',', '.'),
                    'valor_total_raw' => $valorTotal,
                    'missas_envolvidas' => $missasEnvolvidas,
                    'ultima_atualizacao' => $ultimaAtualizacao
                        ? Carbon::parse($ultimaAtualizacao)->format('d/m/Y H:i')
                        : 'N/A'
                ],
                'transacoes' => $listaTransacoes
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar conciliações de missas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna transações candidatas para conciliação com missas
     */
    public function getTransacoesCandidatas(Request $request)
    {
        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 400);
            }

            $conciliacaoService = new ConciliacaoMissasService();

            // Busca todas as transações PIX não conciliadas
            // Por enquanto, não filtramos por rejeitado (campo pode não existir ainda)
            $transacoesNaoConciliadas = BankStatement::where('company_id', $companyId)
                ->where('conciliado_com_missa', false)
                ->where('amount', '>', 0)
                ->get();

            // Filtra apenas transações PIX relevantes
            $transacoesRelevantes = $conciliacaoService->filtrarTransacoesRelevantes($transacoesNaoConciliadas);

            $transacoesCandidatas = [];
            $missasEnvolvidas = [];

            foreach ($transacoesRelevantes as $bankStatement) {
                // Extrai data/hora da transação se ainda não foi extraída
                if (!$bankStatement->transaction_datetime) {
                    $transactionDatetime = $conciliacaoService->extrairDataHoraDoMemo(
                        $bankStatement->memo,
                        $bankStatement->dtposted
                    );
                    $bankStatement->transaction_datetime = $transactionDatetime['datetime'];
                    $bankStatement->source_time = $transactionDatetime['source'];
                    $bankStatement->save();
                } else {
                    $transactionDatetime = [
                        'datetime' => Carbon::parse($bankStatement->transaction_datetime)
                    ];
                }

                // Busca missas correspondentes usando o novo algoritmo
                $missaCorrespondente = $conciliacaoService->encontrarMissasCorrespondentes(
                    $transactionDatetime['datetime'],
                    $companyId
                );

                if ($missaCorrespondente) {
                    // Calcula diferença em minutos
                    $horarioTime = is_string($missaCorrespondente->horario)
                        ? $missaCorrespondente->horario
                        : $missaCorrespondente->horario->format('H:i:s');

                    if (strlen($horarioTime) === 5) {
                        $horarioTime .= ':00';
                    }

                    $horarioMissa = Carbon::parse($transactionDatetime['datetime']->format('Y-m-d') . ' ' . $horarioTime);
                    $diferencaMinutos = abs($transactionDatetime['datetime']->diffInMinutes($horarioMissa));

                    // Formata dia da semana
                    $diasSemana = [
                        'domingo' => 'Domingo',
                        'segunda' => 'Segunda',
                        'terca' => 'Terça',
                        'quarta' => 'Quarta',
                        'quinta' => 'Quinta',
                        'sexta' => 'Sexta',
                        'sabado' => 'Sábado'
                    ];

                    $transacoesCandidatas[] = [
                        'id' => $bankStatement->id,
                        'data_hora' => $transactionDatetime['datetime']->format('d/m/Y H:i'),
                        'valor' => number_format($bankStatement->amount, 2, ',', '.'),
                        'valor_raw' => $bankStatement->amount,
                        'origem' => 'Pix',
                        'missa_sugerida' => $diasSemana[$missaCorrespondente->dia_semana] . ' às ' . substr($horarioTime, 0, 5),
                        'horario_missa_id' => $missaCorrespondente->id,
                        'diferenca_minutos' => $diferencaMinutos,
                        'status' => 'Sugerido como coleta de missa',
                        'dentro_intervalo' => true,
                        'memo' => $bankStatement->memo
                    ];

                    if (!in_array($missaCorrespondente->id, $missasEnvolvidas)) {
                        $missasEnvolvidas[] = $missaCorrespondente->id;
                    }
                }
            }

            // Ordena por diferença de minutos (mais próximas primeiro)
            usort($transacoesCandidatas, function($a, $b) {
                return $a['diferenca_minutos'] <=> $b['diferenca_minutos'];
            });

            $valorTotalCandidatas = array_sum(array_column($transacoesCandidatas, 'valor_raw'));

            return response()->json([
                'success' => true,
                'transacoes' => $transacoesCandidatas,
                'estatisticas' => [
                    'total_candidatas' => count($transacoesCandidatas),
                    'total_conciliadas' => 0, // Será atualizado quando houver conciliações
                    'valor_total_candidatas' => number_format($valorTotalCandidatas, 2, ',', '.'),
                    'valor_total_candidatas_raw' => $valorTotalCandidatas,
                    'missas_envolvidas' => count($missasEnvolvidas)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao buscar transações candidatas', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar dados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirma uma transação como coleta de missa
     */
    public function confirmarMissa(Request $request)
    {
        try {
            $request->validate([
                'bank_statement_id' => 'required|exists:bank_statements,id',
                'horario_missa_id' => 'required|exists:horarios_missas,id'
            ]);

            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 400);
            }

            $bankStatement = BankStatement::where('company_id', $companyId)
                ->findOrFail($request->bank_statement_id);

            $horarioMissa = HorarioMissa::where('company_id', $companyId)
                ->findOrFail($request->horario_missa_id);

            $conciliacaoService = new ConciliacaoMissasService();

            // Cria lançamento financeiro
            $transacaoFinanceira = $conciliacaoService->criarLancamentoFinanceiro($bankStatement, $horarioMissa);

            if ($transacaoFinanceira) {
                // Atualiza flags de conciliação
                $bankStatement->conciliado_com_missa = true;
                $bankStatement->horario_missa_id = $horarioMissa->id;
                $bankStatement->save();

                // Vincula BankStatement com TransacaoFinanceira
                $bankStatement->transacoes()->attach($transacaoFinanceira->id, [
                    'valor_conciliado' => $bankStatement->amount,
                    'status_conciliacao' => 'conciliado'
                ]);

                Log::info('Missa conciliada', [
                    'bank_statement_id' => $bankStatement->id,
                    'transacao_id' => $transacaoFinanceira->id,
                    'horario_missa_id' => $horarioMissa->id,
                    'user_id' => Auth::id(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transação confirmada como coleta de missa com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar lançamento financeiro.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erro ao confirmar missa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao confirmar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rejeita uma transação como coleta de missa
     */
    public function rejeitarMissa(Request $request)
    {
        try {
            $request->validate([
                'bank_statement_id' => 'required|exists:bank_statements,id'
            ]);

            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 400);
            }

            $bankStatement = BankStatement::where('company_id', $companyId)
                ->findOrFail($request->bank_statement_id);

            // Marca como rejeitada (não cria lançamento financeiro)
            // Mantém conciliado_com_missa = false para não aparecer mais como candidata
            // Poderia adicionar um campo 'rejeitado' se necessário no futuro

            return response()->json([
                'success' => true,
                'message' => 'Transação rejeitada. Não será mais sugerida como coleta de missa.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao rejeitar missa', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao rejeitar: ' . $e->getMessage()
            ], 500);
        }
    }

    // ── Conciliação em lote ─────────────────────────────────────────────

    public function conciliarLote(Request $request)
    {
        $companyId = session('active_company_id');
        if (!$companyId) {
            return response()->json(['success' => false, 'message' => 'Companhia não encontrada.'], 403);
        }

        $itens = $request->input('itens', []);
        if (empty($itens) || !is_array($itens)) {
            return response()->json(['success' => false, 'message' => 'Nenhum item informado.'], 422);
        }

        $sucesso = 0;
        $erros = [];

        DB::beginTransaction();
        try {
            $suggestionService = app(ConciliacaoSuggestionService::class);

            foreach ($itens as $item) {
                $bsId = $item['bank_statement_id'] ?? null;
                $mode = $item['mode'] ?? 'sugestao';

                $bs = BankStatement::where('company_id', $companyId)->find($bsId);
                if (!$bs) {
                    $erros[] = "Extrato #{$bsId} não encontrado.";
                    continue;
                }

                if ($mode === 'match' && !empty($item['transacao_id'])) {
                    $transacao = TransacaoFinanceira::where('company_id', $companyId)->find($item['transacao_id']);
                    if ($transacao) {
                        $bs->conciliarCom($transacao, (float) $transacao->valor);
                        $sucesso++;
                    } else {
                        $erros[] = "Transação #{$item['transacao_id']} não encontrada.";
                    }
                } else {
                    $sug = $suggestionService->sugerirPorDados($companyId, $bs->memo, null, (float) $bs->amount);
                    if (($sug['confianca'] ?? 0) < 50 || !$sug['lancamento_padrao_id']) {
                        $erros[] = "Extrato #{$bsId}: confiança insuficiente ({$sug['confianca']}%).";
                        continue;
                    }

                    $tipo = $bs->amount < 0 ? 'saida' : 'entrada';
                    $valor = abs((float) $bs->amount);
                    $data = [
                        'company_id'          => $companyId,
                        'tipo'                => $tipo,
                        'valor'               => $valor,
                        'data_competencia'    => $bs->dtposted,
                        'descricao'           => $sug['descricao'] ?? $bs->memo ?? 'Conciliação em lote',
                        'descricao2'          => $sug['descricao'] ?? $bs->memo ?? 'Conciliação em lote',
                        'entidade_id'         => $bs->entidade_financeira_id,
                        'lancamento_padrao_id' => $sug['lancamento_padrao_id'],
                        'cost_center_id'      => $sug['cost_center_id'],
                        'tipo_documento'      => $sug['tipo_documento'] ?? '',
                        'origem'              => 'conciliacao_bancaria',
                        'situacao'            => $tipo === 'entrada' ? 'recebido' : 'pago',
                        'created_by'          => Auth::id(),
                        'created_by_name'     => Auth::user()->name,
                        'updated_by'          => Auth::id(),
                        'updated_by_name'     => Auth::user()->name,
                    ];

                    if ($sug['parceiro_id']) {
                        $data['parceiro_id'] = $sug['parceiro_id'];
                    }

                    $lp = LancamentoPadrao::find($sug['lancamento_padrao_id']);
                    if ($lp) {
                        if ($lp->conta_debito_id) $data['conta_debito_id'] = $lp->conta_debito_id;
                        if ($lp->conta_credito_id) $data['conta_credito_id'] = $lp->conta_credito_id;
                    }

                    $mov = $this->movimentacao($data);
                    $data['movimentacao_id'] = $mov->id;
                    $transacao = TransacaoFinanceira::create($data);
                    $mov->origem_id = $transacao->id;
                    $mov->origem_type = TransacaoFinanceira::class;
                    $mov->save();

                    $bs->conciliarCom($transacao, $valor);
                    $sucesso++;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro conciliação em lote', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "{$sucesso} conciliação(ões) realizada(s).",
            'data'    => ['sucesso' => $sucesso, 'erros' => $erros],
        ]);
    }

    public function ignorarLote(Request $request)
    {
        $companyId = session('active_company_id');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Nenhum item informado.'], 422);
        }

        $updated = BankStatement::where('company_id', $companyId)
            ->whereIn('id', $ids)
            ->update(['status_conciliacao' => 'ignorado']);

        return response()->json([
            'success' => true,
            'message' => "{$updated} extrato(s) ignorado(s).",
            'data'    => ['total' => $updated],
        ]);
    }

    // ── Dashboard de acurácia da IA ─────────────────────────────────────

    public function dashboardIa()
    {
        try {
            $companyId = session('active_company_id');
            if (!$companyId) {
                return response()->json(['success' => false, 'data' => null], 200);
            }

            $service = app(ConciliacaoSuggestionService::class);
            $data = $service->getDashboardData((int) $companyId);

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (\Throwable $e) {
            Log::warning('dashboardIa: indisponível', ['message' => $e->getMessage()]);
            return response()->json(['success' => false, 'data' => null], 200);
        }
    }

    public function buscarLancamento(Request $request)
    {
        try {
            $companyId = session('active_company_id');

            if (!$companyId) {
                return response()->json(['success' => false, 'message' => 'Empresa não identificada.'], 403);
            }

            $entidadeId = $request->input('entidade_id');
            $searchTerm = trim($request->input('q', ''));
            $startDate  = $request->input('start_date');
            $endDate    = $request->input('end_date');

            $builder = TransacaoFinanceira::where('company_id', $companyId)
                ->with(['parceiro:id,nome', 'entidadeFinanceira:id,nome', 'lancamentoPadrao:id,description']);

            if ($entidadeId) {
                $builder->where('entidade_id', $entidadeId);
            }

            if ($startDate && $endDate) {
                $builder->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('data_vencimento', [$startDate, $endDate])
                      ->orWhere(function ($q2) use ($startDate, $endDate) {
                          $q2->whereNull('data_vencimento')
                             ->whereBetween('data_competencia', [$startDate, $endDate]);
                      });
                });
            }

            if ($searchTerm !== '') {
                $builder->where(function ($q) use ($searchTerm) {
                    $q->where('descricao', 'like', "%{$searchTerm}%")
                      ->orWhereHas('parceiro', function ($pq) use ($searchTerm) {
                          $pq->where('nome', 'like', "%{$searchTerm}%");
                      });
                });
            }

            $results = $builder->orderByDesc('data_vencimento')->orderByDesc('data_competencia')
                ->limit(50)
                ->get()
                ->map(function ($t) {
                    return [
                        'id'                => $t->id,
                        'data_competencia'  => $t->getRawOriginal('data_competencia'),
                        'data_vencimento'   => $t->getRawOriginal('data_vencimento'),
                        'data_pagamento'    => $t->getRawOriginal('data_pagamento'),
                        'tipo'              => $t->tipo,
                        'valor'             => (float) $t->valor,
                        'valor_pago'        => (float) $t->valor_pago,
                        'descricao'         => $t->descricao,
                        'parceiro_nome'     => $t->parceiro?->nome,
                        'conta_nome'        => $t->entidadeFinanceira?->nome,
                        'categoria'         => $t->lancamentoPadrao?->description,
                        'situacao'          => $t->getRawOriginal('situacao'),
                        'tipo_documento'    => $t->tipo_documento,
                        'numero_documento'  => $t->numero_documento,
                        'conciliado'        => $t->movimentacao_id !== null,
                    ];
                });

            return response()->json(['success' => true, 'data' => $results]);
        } catch (\Exception $e) {
            Log::error('buscarLancamento: erro', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile() . ':' . $e->getLine(),
            ]);
            return response()->json(['success' => false, 'message' => 'Erro interno ao buscar lançamentos.'], 500);
        }
    }

    // ── Motor de aprendizado automático ─────────────────────────────────

    private function aprenderComUsuario($request, $bankStatement)
    {
        if (!$request->lancamento_padrao_id) {
            return;
        }

        $companyId = session('active_company_id');
        $memo = $bankStatement->memo ?? '';
        $termoBusca = Str::upper(substr($memo, 0, 20));

        $regraExiste = ConciliacaoRegra::where('company_id', $companyId)
            ->where('termo_busca', $termoBusca)
            ->exists();

        if (!$regraExiste && !empty($termoBusca)) {
            ConciliacaoRegra::create([
                'company_id'           => $companyId,
                'termo_busca'          => $termoBusca,
                'lancamento_padrao_id' => $request->lancamento_padrao_id,
                'cost_center_id'       => $request->cost_center_id,
                'tipo_documento'       => $request->tipo_documento,
                'descricao_sugerida'   => $request->descricao ?? $request->descricao2,
                'prioridade'           => 0,
                'created_by'           => Auth::id(),
                'created_by_name'      => Auth::user()->name,
            ]);

            Log::debug('Nova regra de conciliação aprendida', [
                'termo_busca' => $termoBusca,
                'lancamento_padrao_id' => $request->lancamento_padrao_id,
            ]);
        }

        ConciliacaoSuggestionService::invalidarCacheRegras((int) $companyId);
    }

    private function registrarFeedbackSugestao($request, $bankStatement)
    {
        $sugOrigem = $request->input('sug_origem');
        if (!$sugOrigem) return;

        $companyId = session('active_company_id');
        $service = app(ConciliacaoSuggestionService::class);

        $sugOriginal = [
            'sug_lancamento_padrao_id' => $request->input('sug_lancamento_padrao_id'),
            'sug_cost_center_id'       => $request->input('sug_cost_center_id'),
            'sug_tipo_documento'       => $request->input('sug_tipo_documento'),
            'sug_descricao'            => $request->input('sug_descricao'),
            'sug_parceiro_id'          => $request->input('sug_parceiro_id'),
            'sug_confianca'            => $request->input('sug_confianca', 0),
            'sug_origem'               => $sugOrigem,
        ];

        $dadosEscolhidos = [
            'lancamento_padrao_id' => $request->input('lancamento_padrao_id'),
            'cost_center_id'       => $request->input('cost_center_id'),
            'tipo_documento'       => $request->input('tipo_documento'),
            'descricao'            => $request->input('descricao2', $request->input('descricao')),
            'fornecedor_id'        => $request->input('fornecedor_id'),
        ];

        $service->registrarFeedback((int) $companyId, $bankStatement->id, $sugOriginal, $dadosEscolhidos);
    }
}
