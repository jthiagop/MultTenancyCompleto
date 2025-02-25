<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Financer\StoreTransacaoFinanceiraRequest;
use App\Models\Banco;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\ModulosAnexo;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

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
    public function update(Request $request, string $id)
    {
        //
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
            $bankStatement = BankStatement::find($request->bank_statement_id);
            $transacao = TransacaoFinanceira::find($request->transacao_id);

            if (!$bankStatement || !$transacao) {
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
