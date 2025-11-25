<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Banco;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;
use Flasher;
use Illuminate\Http\Request;
use Log;
use Validator;

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


            // **Garante que o valor sempre seja positivo**
            $validatedData['valor'] = abs($validatedData['valor']);

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

                return redirect()->back()->with('success', 'Conciliação realizada com sucesso!');

            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Erro de validação na conciliação', [
                    'errors' => $e->errors(),
                    'request_data' => $request->all()
                ]);

                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput()
                    ->with('error', 'Dados inválidos para conciliação.');

            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                Log::error('Registro não encontrado na conciliação', [
                    'message' => $e->getMessage(),
                    'request_data' => $request->all()
                ]);

                return redirect()->back()->with('error', 'Erro ao buscar dados para conciliação.');

            } catch (\Exception $e) {
                Log::error('Erro inesperado na conciliação', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all()
                ]);

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
    }

    /**
     * Busca contas financeiras disponíveis para transferência (exceto a conta de origem)
     */
    public function contasDisponiveis(Request $request)
    {
        try {
            $entidadeOrigemId = $request->input('entidade_origem_id');
            $companyId = session('active_company_id'); // Recupera a empresa do usuário logado

            // Busca todas as entidades financeiras da mesma empresa, exceto a de origem
            $contas = EntidadeFinanceira::where('company_id', $companyId)
                ->where('id', '!=', $entidadeOrigemId)
                ->where('tipo', 'banco') // Apenas contas bancárias
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

            return response()->json([
                'success' => true,
                'contas' => $contas,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar contas disponíveis para transferência', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar contas disponíveis.',
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
                    'descricao' => 'nullable|string|max:500',
                ]);

                $bankStatement = BankStatement::findOrFail($validated['bank_statement_id']);
                $entidadeOrigem = EntidadeFinanceira::findOrFail($validated['entidade_origem_id']);
                $entidadeDestino = EntidadeFinanceira::findOrFail($validated['entidade_destino_id']);

                // Converte o valor do formato brasileiro para decimal
                $valor = str_replace(['.', ','], ['', '.'], $validated['valor']);

                // Verifica se a entidade de origem tem saldo suficiente
                if ($entidadeOrigem->saldo_atual < $valor) {
                    return redirect()->back()->with('error', 'Saldo insuficiente na conta de origem para realizar a transferência.');
                }

                // Recupera a empresa do usuário logado
                $companyId = session('active_company_id');

                // Cria movimentação de saída (origem)
                $movimentacaoSaida = Movimentacao::create([
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => 'saida',
                    'valor' => $valor,
                    'data' => $validated['data_transferencia'],
                    'descricao' => $validated['descricao'] ?? 'Transferência para ' . $entidadeDestino->nome,
                    'company_id' => $companyId,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name,
                ]);

                // Cria movimentação de entrada (destino)
                $movimentacaoEntrada = Movimentacao::create([
                    'entidade_id' => $entidadeDestino->id,
                    'tipo' => 'entrada',
                    'valor' => $valor,
                    'data' => $validated['data_transferencia'],
                    'descricao' => $validated['descricao'] ?? 'Transferência de ' . $entidadeOrigem->nome,
                    'company_id' => $companyId,
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    'updated_by' => Auth::id(),
                    'updated_by_name' => Auth::user()->name,
                ]);

                // Cria a transação de saída (origem)
                $transacaoSaida = TransacaoFinanceira::create([
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data_transferencia'],
                    'entidade_id' => $entidadeOrigem->id,
                    'tipo' => 'saida',
                    'valor' => $valor,
                    'descricao' => $validated['descricao'] ?? 'Transferência para ' . $entidadeDestino->nome,
                    'lancamento_padrao_id' => $validated['lancamento_padrao_id'],
                    'movimentacao_id' => $movimentacaoSaida->id,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência automática entre contas bancárias',
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                ]);

                // Cria a transação de entrada (destino)
                $transacaoEntrada = TransacaoFinanceira::create([
                    'company_id' => $companyId,
                    'data_competencia' => $validated['data_transferencia'],
                    'entidade_id' => $entidadeDestino->id,
                    'tipo' => 'entrada',
                    'valor' => $valor,
                    'descricao' => $validated['descricao'] ?? 'Transferência de ' . $entidadeOrigem->nome,
                    'lancamento_padrao_id' => $validated['lancamento_padrao_id'],
                    'movimentacao_id' => $movimentacaoEntrada->id,
                    'origem' => 'transferencia',
                    'historico_complementar' => 'Transferência automática entre contas bancárias',
                    'created_by' => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                ]);

                // Atualiza os saldos das entidades
                $entidadeOrigem->saldo_atual -= $valor;
                $entidadeOrigem->save();

                $entidadeDestino->saldo_atual += $valor;
                $entidadeDestino->save();

                // Marca o bank statement como conciliado
                $bankStatement->update([
                    'reconciled' => true,
                    'status_conciliacao' => 'ok',
                ]);

                // Vincula as transações ao bank statement
                $bankStatement->transacoes()->attach($transacaoSaida->id, [
                    'valor_conciliado' => $valor,
                    'status_conciliacao' => 'ok',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $bankStatement->transacoes()->attach($transacaoEntrada->id, [
                    'valor_conciliado' => $valor,
                    'status_conciliacao' => 'ok',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::info('Transferência entre contas realizada com sucesso', [
                    'bank_statement_id' => $bankStatement->id,
                    'entidade_origem_id' => $entidadeOrigem->id,
                    'entidade_destino_id' => $entidadeDestino->id,
                    'valor' => $valor,
                    'transacao_saida_id' => $transacaoSaida->id,
                    'transacao_entrada_id' => $transacaoEntrada->id,
                ]);

                return redirect()->back()->with('success', 'Transferência realizada com sucesso!');
            } catch (\Illuminate\Validation\ValidationException $e) {
                return redirect()->back()
                    ->withErrors($e->errors())
                    ->withInput();
            } catch (\Exception $e) {
                Log::error('Erro ao processar transferência entre contas', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);

                return redirect()->back()
                    ->with('error', 'Erro ao processar transferência. Tente novamente.')
                    ->withInput();
            }
        });
    }
}
