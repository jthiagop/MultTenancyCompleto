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
            // Recupera a companhia associada ao usuário autenticado
            $subsidiary = User::getCompany();

            if (!$subsidiary) {
                return redirect()->back()->with('error', 'Companhia não encontrada.');
            }


            // Processa os dados validados
            $validatedData = $request->validated();

            // Verifica se "descricao2" foi enviado e atribui a "descricao"
            $validatedData['descricao'] = $validatedData['descricao2'] ;


            // **Garante que o valor sempre seja positivo**
            $validatedData['valor'] = abs($validatedData['valor']);

            // Adiciona informações padrão
            $validatedData['company_id'] = $subsidiary->company_id;
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
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Nome original do arquivo (sem caminho completo)
                $nomeOriginal = $file->getClientOriginalName();
                $anexoName = time() . '_' . $file->getClientOriginalName();
                $anexoPath = $file->storeAs('anexos', $anexoName, 'public');

                ModulosAnexo::create([
                    'anexavel_id'   => $caixa->id,                   // ID da transacao_financeira
                    'anexavel_type' => TransacaoFinanceira::class,   // caminho da classe do Model
                    'nome_arquivo'  => $nomeOriginal,
                    'caminho_arquivo' => $anexoPath,
                    'tamanho_arquivo' => $file->getSize(),
                    'tipo_arquivo'  => $file->getMimeType() ?? '',  // se quiser
                    'created_by'    => Auth::id(),
                    'created_by_name' => Auth::user()->name,
                    // etc., se tiver mais campos
                ]);
            }
        }
    }
}
