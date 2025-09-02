<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Flasher\Laravel\Facade\Flasher;


class EntidadeFinanceiraController extends Controller
{
    // Lista todas as entidades financeiras
    public function index()
    {
        // Busca as entidades da empresa ativa E carrega as movimentações.
        $entidades = EntidadeFinanceira::with('movimentacoes')
            ->forActiveCompany() // <-- Mágica do Scope!
            ->get();

        $banks = Bank::all();

        return view('app.cadastros.entidades.index', compact('entidades', 'banks'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        return view('app.cadastros.entidades.index');
    }

    // Salva uma nova entidade financeira
    public function store(Request $request)
    {
        // 1. Pega a empresa ativa da sessão (seu código aqui está perfeito)
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            Flasher::addError('Nenhuma empresa selecionada.');
            return redirect()->back();
        }

        // 2. Formata o saldo e adiciona o company_id (seu código aqui está perfeito)
        $request->merge([
            'saldo_inicial' => str_replace(['.', ','], ['', '.'], $request->saldo_inicial),
            'company_id'    => $activeCompanyId
        ]);

        // 3. Validação CORRIGIDA
        $validatedData = $request->validate([
            'tipo'          => 'required|in:caixa,banco',
            'company_id'    => 'required|integer|exists:companies,id',
            'nome'          => 'required_unless:tipo,banco|nullable|string|max:100',
            'bank_id'       => 'required_if:tipo,banco|nullable|integer|exists:banks,id', // CORREÇÃO: Valida 'bank_id' em vez de 'banco'
            'agencia'       => 'nullable|string|max:20',
            'conta'         => 'nullable|string|max:20',
            'saldo_inicial' => 'required|numeric',
            'descricao'     => 'nullable|string|max:255',
        ]);

        // 4. Lógica para gerar o nome da entidade (A CORREÇÃO PRINCIPAL)
        if ($request->tipo === 'banco') {
            // Busca o nome do banco no banco de dados usando o ID
            $bank = Bank::find($validatedData['bank_id']);

            // Cria um nome descritivo para a entidade
            $validatedData['nome'] = "{$bank->name} - Ag. {$validatedData['agencia']} C/C {$validatedData['conta']}";
        }

        $validatedData['banco_id'] = $request->tipo === 'banco' ? $validatedData['bank_id'] : null; // Adiciona o banco_id se for do tipo 'banco'
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        try {
            $entidade = EntidadeFinanceira::create($validatedData);

            // Lógica para criar a primeira movimentação... (seu código aqui está ótimo)
            Movimentacao::create([
                'entidade_id'   => $entidade->id,
                'tipo'          => 'entrada',
                'valor'         => $validatedData['saldo_inicial'],
                'descricao'     => 'Saldo inicial da entidade financeira',
                'company_id'    => $validatedData['company_id'],
            ]);

            flash()->success('A entidade financeira foi criada com sucesso!');
            return redirect()->route('entidades.index');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar entidade: ' . $e->getMessage());
            Flasher::addError('Ocorreu um erro ao criar a entidade.');
            return redirect()->back()->withInput();
        }
    }

    // Adiciona uma movimentação
    public function addMovimentacao(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,saida',
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string|max:255',
        ]);

        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

        // Cria a movimentação
        Movimentacao::create([
            'entidade_id' => $entidade->id,
            'tipo' => $request->tipo,
            'valor' => $request->valor,
            'descricao' => $request->descricao,
        ]);

        // Atualiza o saldo atual da entidade
        $entidade->atualizarSaldo();

        return redirect()->route('entidades.index')->with('success', 'Movimentação adicionada com sucesso!');
    }

    public function destroy(string $id)
    {
        try {
            // 1) Localiza a entidade financeira pelo ID
            // CORREÇÃO DE SEGURANÇA: Busca a entidade dentro do escopo da empresa ativa
            $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);
            // 2) Exclui as movimentações associadas (se necessário)
            $movimentacao = Movimentacao::where('entidade_id', $entidade->id)->delete();

            // 3) Exclui a entidade financeira
            $entidade->delete();

            // 4) Mensagem de sucesso e redirecionamento
            flash()->success('A entidade financeira foi excluída com sucesso!');
            return redirect()->back(); // Redireciona para a lista de entidades
        } catch (\Exception $e) {
            // 5) Em caso de erro, registra log e retorna com mensagem de erro
            \Log::error('Erro ao excluir entidade financeira: ' . $e->getMessage());

            Flasher::addError('Ocorreu um erro ao excluir a entidade financeira: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id)
    {
        // 1. A fonte da verdade é a SESSÃO.
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma empresa selecionada.');
        }

        // 2. Carrega a entidade financeira usando o scope para garantir segurança.
        //    O 'with' já carrega as transações de forma otimizada.
        $entidade = EntidadeFinanceira::forActiveCompany()
            ->with(['transacoesFinanceiras' => function ($query) {
                $query->orderBy('data_competencia', 'desc');
            }])
            ->findOrFail($id);

        // 3. Busca os lançamentos do extrato pendentes para esta entidade.
        //    Esta consulta já está correta, pois filtra pelo 'entidade_financeira_id'.
        $bankStatements = BankStatement::where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->orderBy('dtposted', 'desc')
            ->paginate(20);

        // 4. Para cada lançamento do extrato, busca possíveis correspondências.
        foreach ($bankStatements as $lancamento) {
            $valorAbs = abs($lancamento->amount);
            $tipo = $lancamento->amount < 0 ? 'saida' : 'entrada';
            $dataInicio = Carbon::parse($lancamento->dtposted)->startOfDay()->subMonths(2);
            $dataFim = Carbon::parse($lancamento->dtposted)->endOfDay()->addMonths(2);
            $numeroDocumento = $lancamento->checknum;

            // CORREÇÃO: A busca por transações agora também usa o scope.
            $possiveis = TransacaoFinanceira::forActiveCompany()
                ->where('entidade_id', $id)
                ->where('tipo', $tipo)
                ->where('valor', $valorAbs)
                ->whereBetween('data_competencia', [$dataInicio, $dataFim])
                ->when($numeroDocumento, function ($query) use ($numeroDocumento) {
                    $query->where('numero_documento', $numeroDocumento);
                })
                ->get();

            $lancamento->possiveisTransacoes = $possiveis;
        }

        // 5. CORREÇÃO: Carrega dados auxiliares usando os scopes.
        //$centrosAtivos = CostCenter::forActiveCompany()->get();

        $lps = LancamentoPadrao::all();

        // 6. A sua lógica de cálculo de percentual e agrupamento por dia está ótima.
        $totalTransacoes = $entidade->transacoesFinanceiras->count();
        $totalConciliadas = $entidade->transacoesFinanceiras->where('status_conciliacao', 'ok')->count();
        $percentualConciliado = $totalTransacoes > 0 ? ($totalConciliadas / $totalTransacoes) * 100 : 0;
        $transacoesPorDia = $entidade->transacoesFinanceiras->groupBy(fn($item) => Carbon::parse($item->data_competencia)->format('Y-m-d'));

        // 7. Retorna a view com todos os dados corretamente filtrados.
        return view('app.financeiro.entidade.show', [
            'entidade' => $entidade,
            'transacoes' => $entidade->transacoesFinanceiras,
            'conciliacoesPendentes' => $bankStatements,
            //'centrosAtivos' => $centrosAtivos,
            'lps' => $lps,
            'percentualConciliado' => round($percentualConciliado),
            'transacoesPorDia' => $transacoesPorDia,
        ]);
    }
}
