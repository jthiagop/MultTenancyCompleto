<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
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
        $user = Auth::user();
        // Obter o ID da empresa associada ao usuário autenticado
        $companyId = $user->company_id;

        // Verifica se a empresa foi encontrada
        if (!$companyId) {
            return redirect()->back()->with('error', 'Empresa não encontrada para o usuário autenticado.');
        }

        $entidades = EntidadeFinanceira::with('movimentacoes')
            ->where('company_id', $companyId)
            ->get();
        return view('app.cadastros.entidades.index', compact('entidades'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        return view('app.cadastros.entidades.index');
    }

    // Salva uma nova entidade financeira
    public function store(Request $request)
    {
        // Recupera o ID da empresa do usuário logado
        $companyId = Auth::user()->company_id;

        // 1) Remover formatação de milhar e substituir vírgulas por pontos
        $request->merge([
            'saldo_inicial' => str_replace(['.', ','], ['', '.'], $request->saldo_inicial),
            'saldo_atual'   => str_replace(['.', ','], ['', '.'], $request->saldo_atual),
            'company_id'    => $companyId // Adiciona o company_id ao request
        ]);

        // 2) Validação condicional
        $validatedData = $request->validate([
            'tipo'          => 'required|in:caixa,banco,dizimo,coleta,doacao',
            'company_id' => 'required|integer|exists:companies,id',
            // Se tipo == 'banco', campo 'banco' é obrigatório; caso contrário, 'nome' é obrigatório.
            'nome' => 'required_unless:tipo,banco|nullable|string|max:100',
            'banco' => 'required_if:tipo,banco|nullable|string|max:100',


            'agencia'       => 'nullable|string|max:20',
            'conta'         => 'nullable|string|max:20',

            'saldo_inicial' => 'required|numeric',
            'saldo_atual'   => 'nullable|numeric',
            'descricao'     => 'nullable|string|max:255',
        ], [
            'nome.required_if'  => 'O campo "Nome da Entidade" é obrigatório quando o tipo não for "banco".',
            'banco.required_if' => 'Selecione um banco quando o tipo for "banco".',
        ]);

        // 3) Ajustar “nome” conforme o tipo
        //    Se for "banco", usamos o campo 'banco' como o nome da entidade; caso contrário, usamos 'nome'.
        if ($request->tipo === 'banco') {
            $validatedData['nome'] = $validatedData['banco']; // Atribui o campo 'banco' como nome
        }

        // 4) Se saldo_atual não for informado, usar saldo_inicial como valor padrão
        $validatedData['saldo_atual'] = $validatedData['saldo_atual'] ?? $validatedData['saldo_inicial'];

        // 5) Adicionar campos de auditoria / company
        $validatedData['created_by']       = Auth::id();
        $validatedData['created_by_name']  = Auth::user()->name;
        $validatedData['updated_by']       = Auth::id();
        $validatedData['updated_by_name']  = Auth::user()->name;


        try {
            // 6) Criar a entidade no banco
            $entidade = EntidadeFinanceira::create($validatedData);

            // 7) Criar a movimentação inicial (opcional, conforme sua lógica)
            Movimentacao::create([
                'entidade_id'   => $entidade->id,
                'tipo'          => 'entrada',
                'valor'         => $validatedData['saldo_inicial'],
                'descricao'     => 'Saldo inicial da entidade financeira',
                'company_id'    => $validatedData['company_id'],
                'created_by'    => Auth::id(),
                'created_by_name' => Auth::user()->name,
                'updated_by'    => Auth::id(),
                'updated_by_name' => Auth::user()->name,
            ]);

            // 8) Mensagem de sucesso e redirecionamento
            flash()->success('O lançamento foi salvo com sucesso!');
            return redirect()->back();
        } catch (\Exception $e) {
            // 9) Em caso de erro, registra log (opcional) e retorna com mensagem de erro
            \Log::error('Ocorreu um erro ao processar o lançamento: ' . $e->getMessage());

            Flasher::addError('Ocorreu um erro: ' . $e->getMessage());
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

        $entidade = EntidadeFinanceira::findOrFail($id);

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
            $entidade = EntidadeFinanceira::findOrFail($id);
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

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        ini_set('memory_limit', '512M');

        // 1. Recupera o ID da empresa do usuário logado
        $companyId = Auth::user()->company_id;

        // 2. Carrega a entidade financeira (banco) do usuário logado,
        //    junto com duas relações:
        //    - transacoesFinanceiras (todas, ordenadas por data_competencia desc)
        //    - bankStatements (apenas as não conciliadas ou pendentes/divergentes, ordenadas por dtposted desc)
        $entidade = EntidadeFinanceira::where('company_id', $companyId)
            ->with([
                'transacoesFinanceiras' => function ($query) {
                    $query->orderBy('data_competencia', 'desc');
                }
            ])
            ->findOrFail($id);

        // Consulta paginada para bankStatements
        $bankStatements = BankStatement::where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->orderBy('dtposted', 'desc')
            ->paginate(20); // <-- quantidade por página

        // 3. Para cada lançamento bancário pendente (bankStatement),
        //    buscar possíveis transações financeiras compatíveis
        foreach ($bankStatements as $lancamento) {
            // Se o valor for negativo no extrato, definimos 'saida'; caso contrário, 'entrada'.
            $valorAbs = abs($lancamento->amount);
            $tipo = $lancamento->amount < 0 ? 'saida' : 'entrada';

            // Criamos um intervalo de tolerância de 2 meses antes e 2 meses depois da data do extrato
            $dataInicio = Carbon::parse($lancamento->dtposted)
                ->startOfDay()
                ->subMonths(2);

            $dataFim = Carbon::parse($lancamento->dtposted)
                ->endOfDay()
                ->addMonths(2);

            // Exemplo de comparação com numero_documento do extrato
            $numeroDocumento = $lancamento->checknum;

            // 4. Busca em transacoes_financeiras as candidatas
            $possiveis = TransacaoFinanceira::where('company_id', $companyId)
                ->where('entidade_id', $id)
                ->where('tipo', $tipo)
                ->where('valor', $valorAbs)
                // Tolerância de até 2 meses antes e 2 meses depois
                ->whereBetween('data_competencia', [$dataInicio, $dataFim])
                // ✅ Adiciona o filtro pelo número do documento
                ->when($numeroDocumento, function ($query) use ($numeroDocumento) {
                    $query->where('numero_documento', $numeroDocumento);
                })
                ->get();

            // Atribuímos essa coleção de possíveis transações na propriedade possiveisTransacoes do $lancamento
            $lancamento->possiveisTransacoes = $possiveis;
        }


        // 5. Carrega dados auxiliares (centros de custo, lançamentos padrão, etc.)
        $centrosAtivos = CostCenter::getCadastroCentroCusto();
        $lps = LancamentoPadrao::all();

        // 6. Calcula o percentual de conciliação (exemplo)
        $totalTransacoes = $entidade->transacoesFinanceiras->count();
        $totalConciliadas = $entidade->transacoesFinanceiras
            ->where('status_conciliacao', 'ok')
            ->count();
        $percentualConciliado = $totalTransacoes > 0
            ? ($totalConciliadas / $totalTransacoes) * 100
            : 0;

        // 7. Agrupa as transacoesFinanceiras por dia, se quiser exibir em layout “por data”
        //    (opcional, dependendo de como você exibirá no Blade)
        $transacoesPorDia = $entidade->transacoesFinanceiras->groupBy(function ($item) {
            return Carbon::parse($item->data_competencia)->format('Y-m-d');
        });

        // 8. Retorna a view com os dados necessários
        return view('app.financeiro.entidade.show', [
            'entidade' => $entidade,
            // Movimentações gerais
            'transacoes' => $entidade->transacoesFinanceiras,
            // Lançamentos pendentes de conciliação
            'conciliacoesPendentes' => $bankStatements,        // <<-- Usar a coleção paginada
            // Auxiliares
            'centrosAtivos' => $centrosAtivos,
            'lps' => $lps,
            // Percentual conciliado
            'percentualConciliado' => round($percentualConciliado),
            // Transações agrupadas por dia (se quiser exibir estilo timeline/por data)
            'transacoesPorDia' => $transacoesPorDia,
        ]);
    }
}
