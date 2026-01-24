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
use Illuminate\Support\Facades\Auth;
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

            // Tratamento do valor para garantir formato correto
            if ($request->has('valor')) {
                $valorFormatado = str_replace(',', '.', str_replace('.', '', $request->input('valor')));
                $request->merge(['valor' => $valorFormatado]);
            }

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

            // Ajusta o saldo da entidade antes de atualizar os valores
            $oldEntidade = EntidadeFinanceira::findOrFail($movimentacao->entidade_id);

            // Reverter saldo anterior
            if ($movimentacao->tipo === 'entrada') {
                $oldEntidade->saldo_atual -= $movimentacao->valor;
            } else {
                $oldEntidade->saldo_atual += $movimentacao->valor;
            }
            $oldEntidade->save();

            // Atualiza os dados da movimentação
            $movimentacao->update([
                'entidade_id' => $validatedData['entidade_id'],
                'valor' => $validatedData['valor'],
                'descricao' => $validatedData['descricao'],
                'updated_by' => Auth::user()->id,
            ]);

            // Ajusta o saldo na nova entidade financeira
            $newEntidade = EntidadeFinanceira::findOrFail($validatedData['entidade_id']);

            if ($movimentacao->tipo === 'entrada') {
                $newEntidade->saldo_atual += $validatedData['valor'];
            } else {
                $newEntidade->saldo_atual -= $validatedData['valor'];
            }
            $newEntidade->save();

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
        $transacao = BankStatement::find($transactionId);

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

            // Verifica se "descricao2" foi enviado e atribui a "descricao"
            $validatedData['descricao'] = $validatedData['descricao2'] ;

            // **Define a situação baseada no tipo (entrada/saida)**
            // Quando vem de conciliação, marca automaticamente como recebido ou pago
            $validatedData['situacao'] = $validatedData['tipo'] === 'entrada' ? 'recebido' : 'pago';

            // **Garante que o valor sempre seja positivo**
            $validatedData['valor'] = abs($validatedData['valor']);

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
            $validatedData['created_by'] = Auth::id();
            $validatedData['created_by_name'] = Auth::user()->name;
            $validatedData['updated_by'] = Auth::id();
            $validatedData['updated_by_name'] = Auth::user()->name;

            // Gera movimentação financeira
            $movimentacao = $this->movimentacao($validatedData);
            $validatedData['movimentacao_id'] = $movimentacao->id;

            // Cria a transação financeira
            $caixa = TransacaoFinanceira::create($validatedData);

            // Processa lançamentos padrão
            $this->processarLancamentoPadrao($validatedData);

            // Processa anexos, se existirem
            $this->processarAnexos($request, $caixa);

            // Recupera os registros necessários
            $bankStatement = BankStatement::find($request->input('bank_statement_id'));
            // Usar a transação que acabamos de criar, não buscar por transacao_id
            $transacao = $caixa; // $caixa é a TransacaoFinanceira que acabamos de criar

            Log::info('Tentativa de buscar registros no método conciliar', [
                'bank_statement_id' => $request->input('bank_statement_id'),
                'transacao_criada_id' => $caixa->id,
                'bank_statement_found' => $bankStatement ? 'sim' : 'não',
                'transacao_found' => $transacao ? 'sim' : 'não',
                'request_all' => $request->all()
            ]);

            if (!$bankStatement) {
                Log::error('Erro: BankStatement não encontrado no método conciliar', [
                    'bank_statement_id' => $request->input('bank_statement_id'),
                    'bank_statement_found' => $bankStatement ? 'sim' : 'não'
                ]);
                return redirect()->back()->with('error', 'Erro ao buscar dados para conciliação.');
            }

            // Define o valor conciliado
            $valorConciliado = $request->valor ?? $transacao->valor;

            // **Lógica para definir o status**
            if (bccomp($valorConciliado, $bankStatement->amount, 2) === 0) {
                $status = 'ok'; // Conciliação perfeita
            } elseif ($valorConciliado < $transacao->valor) {
                $status = 'parcial'; // Conciliação parcial (falta valor)
            } elseif ($valorConciliado > $transacao->valor) {
                $status = 'divergente'; // Conciliação com excesso
            } else {
                $status = 'pendente'; // Algo inesperado, pendente de verificação
            }

            // **Atualiza bank_statements**
            $bankStatement->update([
                'reconciled' => true,
                'status_conciliacao' => $status,
            ]);

            // **Armazena na tabela pivot**
            $bankStatement->transacoes()->attach($transacao->id, [
                'valor_conciliado' => $valorConciliado,
                'status_conciliacao' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Retornar JSON se for requisição AJAX, senão redirecionar
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => 'Lançamento conciliado com sucesso!',
                    'data' => [
                        'transacao_id' => $transacao->id,
                        'bank_statement_id' => $bankStatement->id,
                        'status' => $status,
                    ]
                ]);
            }

            return redirect()->back()->with('success', 'Lançamento conciliado com sucesso!');
        });
    }

    public function pivot(Request $request)
    {
        // Log da requisição recebida
        Log::info('Iniciando processo de conciliação', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return DB::transaction(function () use ($request) {
            try {
                // Validação dos dados de entrada
                $request->validate([
                    'bank_statement_id' => 'required|exists:bank_statements,id',
                    'transacao_financeira_id' => 'required|exists:transacoes_financeiras,id',
                    'valor_conciliado' => 'nullable|numeric|min:0'
                ]);

                Log::info('Validação dos dados de entrada passou', [
                    'bank_statement_id' => $request->bank_statement_id,
                    'transacao_financeira_id' => $request->transacao_financeira_id,
                    'valor_conciliado' => $request->valor_conciliado
                ]);

                // ✅ Busca os registros corretamente
                $bankStatement = BankStatement::findOrFail($request->bank_statement_id);
                $transacao = TransacaoFinanceira::findOrFail($request->transacao_financeira_id);

                Log::info('Registros encontrados', [
                    'bank_statement' => [
                        'id' => $bankStatement->id,
                        'amount' => $bankStatement->amount,
                        'dtposted' => $bankStatement->dtposted,
                        'memo' => $bankStatement->memo,
                        'reconciled' => $bankStatement->reconciled
                    ],
                    'transacao' => [
                        'id' => $transacao->id,
                        'valor' => $transacao->valor,
                        'data_competencia' => $transacao->data_competencia,
                        'descricao' => $transacao->descricao
                    ]
                ]);

                // ✅ Define o valor conciliado
                $valorConciliado = $request->valor_conciliado ?? $transacao->valor;

                Log::info('Valor conciliado definido', [
                    'valor_conciliado' => $valorConciliado,
                    'valor_original_transacao' => $transacao->valor,
                    'valor_bank_statement' => $bankStatement->amount
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

                Log::info('Conciliação realizada com sucesso', [
                    'bank_statement_id' => $bankStatement->id,
                    'transacao_id' => $transacao->id,
                    'valor_conciliado' => $valorConciliado,
                    'status_conciliacao' => $bankStatement->status_conciliacao
                ]);

                // Retornar JSON se for requisição AJAX
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Conciliação realizada com sucesso!',
                        'data' => [
                            'transacao_id' => $transacao->id,
                            'bank_statement_id' => $bankStatement->id,
                            'status' => $bankStatement->status_conciliacao,
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

        $bankStatement = BankStatement::findOrFail($request->bank_statement_id);

        // Marcar como ignorado
        $bankStatement->update([
            'status_conciliacao' => 'ignorado',
            'reconciled' => false, // Para garantir que não apareça como conciliado
        ]);

        return redirect()->back()->with('success', 'Lançamento ignorado com sucesso!');
    }

    public function ignorar($id)
    {
        // Encontra o lançamento bancário pelo ID
        $bankStatement = BankStatement::findOrFail($id);

        // Atualiza o status para "ignorado"
        $bankStatement->update(['status_conciliacao' => 'ignorado']);

        // Redireciona com mensagem de sucesso
        return redirect()->back()->with('success', 'Lançamento ignorado com sucesso!');
    }

    /**
     * Processa movimentacao.
     */
    private function movimentacao(array $validatedData)
    {
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
     */
    private function processarAnexos(Request $request, TransacaoFinanceira $caixa)
    {
        // Verifica se há anexos no formato anexos[index][arquivo] ou anexos[index][link]
        if (!$request->has('anexos') || !is_array($request->input('anexos'))) {
            return;
        }

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

        // Atualiza automaticamente o campo comprovacao_fiscal
        $caixa->updateComprovacaoFiscal();
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
            Log::info('Buscando contas disponíveis', [
                'entidade_origem_id' => $entidadeOrigemId,
                'company_id' => $companyId
            ]);

            // Busca todas as entidades financeiras da mesma empresa, exceto a de origem
            $query = EntidadeFinanceira::where('company_id', $companyId)
                ->where('tipo', 'banco'); // Apenas contas bancárias

            // Se houver entidade de origem, exclui ela
            if ($entidadeOrigemId) {
                $query->where('id', '!=', $entidadeOrigemId);
            }

            $contas = $query->orderBy('nome', 'asc')
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
                    ];
                });

            // Log para debug
            Log::info('Contas encontradas', [
                'total' => $contas->count(),
                'contas' => $contas->pluck('nome')->toArray()
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
     */
    public function transferir(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                // Validação dos dados
                $validated = $request->validate([
                    'bank_statement_id' => 'required|exists:bank_statements,id',
                    'entidade_origem_id' => 'required|exists:entidades_financeiras,id',
                    'entidade_destino_id' => 'required|exists:entidades_financeiras,id|different:entidade_origem_id',
                    'valor' => 'required|numeric|min:0.01',
                    'data_transferencia' => 'required|date',
                    'lancamento_padrao_id' => 'required|exists:lancamento_padraos,id',
                    'cost_center_id' => 'nullable|exists:cost_centers,id',
                    'descricao' => 'nullable|string|max:500',
                    'checknum' => 'nullable|string|max:100',
                ]);

                $bankStatement = BankStatement::findOrFail($validated['bank_statement_id']);
                $entidadeOrigem = EntidadeFinanceira::findOrFail($validated['entidade_origem_id']);
                $entidadeDestino = EntidadeFinanceira::findOrFail($validated['entidade_destino_id']);

                // Converte o valor do formato brasileiro para decimal
                $valor = str_replace(['.', ','], ['', '.'], $validated['valor']);

                // Recupera a empresa do usuário logado
                $companyId = session('active_company_id');

                if (!$companyId) {
                    return redirect()->back()->with('error', 'Companhia não encontrada.');
                }

                // Determina o tipo baseado no valor do bank statement (negativo = saída, positivo = entrada)
                $tipo = $bankStatement->amount < 0 ? 'saida' : 'entrada';

                // Busca o lançamento padrão para obter conta_debito_id e conta_credito_id
                $lancamentoPadrao = LancamentoPadrao::find($validated['lancamento_padrao_id']);

                // Prepara os dados para criar a transação (apenas da conta de origem - conciliação)
                $validatedData = [
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data_transferencia'],
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => $tipo,
                    'valor' => abs($valor),
                    'descricao' => $validated['descricao'] ?? 'Transferência para ' . $entidadeDestino->nome,
                    'lancamento_padrao_id' => $validated['lancamento_padrao_id'],
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

                // Cria movimentação financeira
                $movimentacao = $this->movimentacao($validatedData);
                $validatedData['movimentacao_id'] = $movimentacao->id;

                // Cria a transação financeira (apenas uma - da conta de origem)
                $transacao = TransacaoFinanceira::create($validatedData);

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

                Log::info('Conciliação de transferência realizada com sucesso', [
                    'bank_statement_id' => $bankStatement->id,
                    'entidade_origem_id' => $entidadeOrigem->id,
                    'entidade_destino_id' => $entidadeDestino->id,
                    'valor' => $valor,
                    'transacao_id' => $transacao->id,
                ]);

                return redirect()->back()->with('success', 'Transferência conciliada com sucesso!');
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            } catch (\Exception $e) {
                Log::error('Erro ao processar conciliação de transferência', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);

                return redirect()->back()
                    ->with('error', 'Erro ao processar conciliação. Tente novamente.')
                    ->withInput();
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
}
