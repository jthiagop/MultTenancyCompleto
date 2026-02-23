<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Contabilide\ChartOfAccount;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\BankStatement;
use App\Models\Financeiro\CostCenter;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\FormasPagamento;
use App\Models\HorarioMissa;
use App\Models\LancamentoPadrao;
use App\Models\Movimentacao;
use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Flasher\Laravel\Facade\Flasher;


class EntidadeFinanceiraController extends Controller
{
    // Lista todas as entidades financeiras
    public function index()
    {
        // Busca as entidades da empresa ativa E carrega as movimentações e conta contábil.
        $entidades = EntidadeFinanceira::with(['movimentacoes', 'contaContabil'])
            ->forActiveCompany() // <-- Mágica do Scope!
            ->get();

        $banks = Bank::all();

        // Busca contas contábeis para o select do modal
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.cadastros.entidades.index', compact('entidades', 'banks', 'contas'));
    }

    // Mostra o formulário de criação
    public function create()
    {
        // Busca as entidades da empresa ativa E carrega as movimentações e conta contábil.
        $entidades = EntidadeFinanceira::with(['movimentacoes', 'contaContabil'])
            ->forActiveCompany()
            ->get();

        $banks = Bank::all();

        // Busca contas contábeis para o select do modal
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.cadastros.entidades.index', compact('entidades', 'banks', 'contas'));
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

        // 2. Formatar o saldo se foi enviado e adicionar o company_id
        $mergeData = ['company_id' => $activeCompanyId];
        
        // Verificar se o campo saldo_inicial foi enviado antes de formatá-lo
        if ($request->has('saldo_inicial') && !is_null($request->saldo_inicial)) {
            // Usa Money para converter formato brasileiro → decimal
            $money = Money::fromHumanInput((string) $request->saldo_inicial);
            $mergeData['saldo_inicial'] = $money->toDatabase();
        }
        
        $request->merge($mergeData);

        // 3. Validação CORRIGIDA
        $validatedData = $request->validate([
            'tipo'          => 'required|in:caixa,banco',
            'company_id'    => 'required|integer|exists:companies,id',
            'nome'          => 'required_unless:tipo,banco|nullable|string|max:100',
            'bank_id'       => 'required_if:tipo,banco|nullable|integer|exists:banks,id', // CORREÇÃO: Valida 'bank_id' em vez de 'banco'
            'agencia'       => 'nullable|string|max:20',
            'conta'         => 'nullable|string|max:20',
            'account_type'  => 'required_if:tipo,banco|nullable|in:corrente,poupanca,aplicacao,renda_fixa,tesouro_direto',
            'saldo_inicial' => 'required|numeric',
            'descricao'     => 'nullable|string|max:255',
            'conta_contabil_id' => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        // 4. Lógica para gerar o nome da entidade
        if ($request->tipo === 'banco') {
            // Busca o nome do banco no banco de dados usando o ID
            $bank = Bank::find($validatedData['bank_id']);

            // Se o usuário forneceu um apelido (nome_banco), usa ele como nome
            $nomeBanco = $request->input('nome_banco');
            if (!empty($nomeBanco)) {
                $validatedData['nome'] = $nomeBanco;
            } else {
                // Caso contrário, gera automaticamente
                $accountTypeNames = [
                    'corrente' => 'Conta Corrente',
                    'poupanca' => 'Poupança',
                    'aplicacao' => 'Aplicação',
                    'renda_fixa' => 'Renda Fixa',
                    'tesouro_direto' => 'Tesouro Direto',
                ];

                $accountTypeName = $accountTypeNames[$validatedData['account_type']] ?? 'Conta';
                $validatedData['nome'] = "{$bank->name} - {$accountTypeName} - Ag. {$validatedData['agencia']} C/C {$validatedData['conta']}";
            }
        }

        $validatedData['banco_id'] = $request->tipo === 'banco' ? $validatedData['bank_id'] : null; // Adiciona o banco_id se for do tipo 'banco'
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;
        $validatedData['updated_by'] = Auth::id();
        $validatedData['updated_by_name'] = Auth::user()->name;

        try {
            $entidade = EntidadeFinanceira::create($validatedData);

            // Lógica para criar a primeira movimentação (com categoria para o accessor saldo_inicial_real)
            Movimentacao::create([
                'entidade_id'   => $entidade->id,
                'tipo'          => 'entrada',
                'valor'         => $validatedData['saldo_inicial'],
                'descricao'     => 'Saldo inicial da entidade financeira',
                'categoria'     => 'saldo_inicial',
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

        // Saldo atualizado automaticamente pelo MovimentacaoObserver (increment/decrement O(1))

        return redirect()->route('entidades.index')->with('success', 'Movimentação adicionada com sucesso!');
    }

    // Mostra o formulário de edição
    public function edit(string $id)
    {
        // Verifica se o usuário é admin ou global
        if (!Auth::user()->hasRole(['admin', 'global'])) {
            Flasher::addError('Você não tem permissão para editar entidades financeiras.');
            return redirect()->route('entidades.index');
        }

        // Busca as entidades da empresa ativa E carrega as movimentações e conta contábil.
        $entidades = EntidadeFinanceira::with(['movimentacoes', 'contaContabil'])
            ->forActiveCompany()
            ->get();

        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);
        $banks = Bank::all();

        // Busca contas contábeis para o select do modal
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        return view('app.cadastros.entidades.index', compact('entidades', 'entidade', 'banks', 'contas'));
    }

    // Atualiza uma entidade financeira
    public function update(Request $request, string $id)
    {
        $isAjax = $request->expectsJson();

        // Verifica se o usuário é admin ou global
        if (!Auth::user()->hasRole(['admin', 'global'])) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Você não tem permissão para editar entidades financeiras.'], 403);
            }
            Flasher::addError('Você não tem permissão para editar entidades financeiras.');
            return redirect()->route('entidades.index');
        }

        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

        // Validação baseada no tipo da entidade existente
        $rules = [];
        
        if ($entidade->tipo === 'caixa') {
            $rules = [
                'nome'      => 'required|string|max:100',
                'descricao' => 'nullable|string|max:255',
                'conta_contabil_id' => 'nullable|integer|exists:chart_of_accounts,id',
            ];
        } else {
            $rules = [
                'bank_id'      => 'required|integer|exists:banks,id',
                'agencia'      => 'required|string|max:20',
                'conta'        => 'required|string|max:20',
                'account_type' => 'required|in:corrente,poupanca,aplicacao,renda_fixa,tesouro_direto',
                'descricao'    => 'nullable|string|max:255',
                'conta_contabil_id' => 'nullable|integer|exists:chart_of_accounts,id',
            ];
        }

        $validatedData = $request->validate($rules);

        try {
            // Atualiza os campos permitidos
            if ($entidade->tipo === 'caixa') {
                $entidade->nome = $validatedData['nome'];
                $entidade->descricao = $validatedData['descricao'] ?? null;
                $entidade->conta_contabil_id = $validatedData['conta_contabil_id'] ?? null;
            } else {
                $entidade->banco_id = $validatedData['bank_id'];
                $entidade->agencia = $validatedData['agencia'];
                $entidade->conta = $validatedData['conta'];
                $entidade->account_type = $validatedData['account_type'];
                $entidade->descricao = $validatedData['descricao'] ?? null;
                $entidade->conta_contabil_id = $validatedData['conta_contabil_id'] ?? null;

                // Regenera o nome com os novos dados
                $bank = Bank::find($entidade->banco_id);
                if ($bank) {
                    $accountTypeNames = [
                        'corrente' => 'Conta Corrente',
                        'poupanca' => 'Poupança',
                        'aplicacao' => 'Aplicação',
                        'renda_fixa' => 'Renda Fixa',
                        'tesouro_direto' => 'Tesouro Direto',
                    ];
                    $accountTypeName = $accountTypeNames[$entidade->account_type] ?? 'Conta';
                    $entidade->nome = "{$bank->name} - {$accountTypeName} - Ag. {$entidade->agencia} C/C {$entidade->conta}";
                }
            }

            $entidade->updated_by = Auth::id();
            $entidade->updated_by_name = Auth::user()->name;
            $entidade->save();

            // Recarrega com conta contábil
            $entidade->load('contaContabil');

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Entidade financeira atualizada com sucesso!',
                    'data' => [
                        'id' => $entidade->getRouteKey(),
                        'nome' => $entidade->nome,
                        'tipo' => $entidade->tipo,
                        'descricao' => $entidade->descricao,
                        'conta_contabil' => $entidade->contaContabil
                            ? $entidade->contaContabil->code . ' - ' . $entidade->contaContabil->name
                            : null,
                        'saldo_atual' => $entidade->saldo_atual,
                        'saldo_inicial_real' => $entidade->saldo_inicial_real,
                        'updated_at' => $entidade->updated_at->format('d/m/Y H:i'),
                    ],
                ]);
            }

            Flasher::addSuccess('Entidade financeira atualizada com sucesso!');
            return redirect()->route('entidades.index');
        } catch (\Exception $e) {
            \Log::error('Erro ao atualizar entidade: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Ocorreu um erro ao atualizar a entidade.'], 500);
            }

            Flasher::addError('Ocorreu um erro ao atualizar a entidade.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Renomeia uma entidade financeira via AJAX.
     */
    public function renomear(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'nome' => 'required|string|max:150',
            ]);

            $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

            $entidade->nome = $validated['nome'];
            $entidade->updated_by = Auth::id();
            $entidade->updated_by_name = Auth::user()->name;
            $entidade->save();

            return response()->json([
                'success' => true,
                'message' => 'Nome atualizado com sucesso!',
                'nome' => $entidade->nome,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Erro ao renomear entidade: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao renomear entidade.',
            ], 500);
        }
    }

    public function destroy(Request $request, string $id)
    {
        $isAjax = $request->expectsJson();

        if (!Auth::user()->hasRole(['admin', 'global'])) {
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Você não tem permissão para excluir entidades financeiras.'], 403);
            }
            Flasher::addError('Você não tem permissão para excluir entidades financeiras.');
            return redirect()->back();
        }

        try {
            $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

            // Verifica se há transações vinculadas
            $totalTransacoes = $entidade->transacoesFinanceiras()->count();
            if ($totalTransacoes > 0) {
                $msg = "Esta entidade possui {$totalTransacoes} transação(ões) vinculada(s). Exclua as transações primeiro.";
                if ($isAjax) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                Flasher::addError($msg);
                return redirect()->back();
            }

            // Exclui movimentações e entidade
            Movimentacao::where('entidade_id', $entidade->id)->delete();
            $entidade->delete();

            if ($isAjax) {
                return response()->json(['success' => true, 'message' => 'Entidade financeira excluída com sucesso!']);
            }

            flash()->success('A entidade financeira foi excluída com sucesso!');
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir entidade financeira: ' . $e->getMessage());

            if ($isAjax) {
                return response()->json(['success' => false, 'message' => 'Ocorreu um erro ao excluir a entidade financeira.'], 500);
            }

            Flasher::addError('Ocorreu um erro ao excluir a entidade financeira: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function show($id, Request $request)
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

        // ✅ 2.5. Filtragem Server-Side por Tab (amount_cents)
        // Recebe: ?tab=all (padrão), ?tab=received (amount_cents > 0), ?tab=paid (amount_cents < 0)
        $tab = $request->input('tab', 'all');

        // Base query para conciliações pendentes (com filtro de company_id para segurança multi-tenant)
        $query = BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->where(function ($q) {
                $q->where('conciliado_com_missa', false)
                  ->orWhereNull('conciliado_com_missa');
            });

        // Aplica filtro baseado na tab
        if ($tab === 'received') {
            // Recebimentos: amount_cents > 0 (valores positivos)
            $query->where('amount_cents', '>', 0);
        } elseif ($tab === 'paid') {
            // Pagamentos: amount_cents < 0 (valores negativos)
            $query->where('amount_cents', '<', 0);
        }
        // Se $tab === 'all', não aplica filtro (retorna todas)

        // Calcula contadores ANTES de paginar (reutiliza base query com company_id)
        $countsBaseQuery = BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->where(function ($q) {
                $q->where('conciliado_com_missa', false)
                  ->orWhereNull('conciliado_com_missa');
            });

        $counts = [
            'all'      => (clone $countsBaseQuery)->count(),
            'received' => (clone $countsBaseQuery)->where('amount_cents', '>', 0)->count(),
            'paid'     => (clone $countsBaseQuery)->where('amount_cents', '<', 0)->count(),
        ];

        // 3. Busca os lançamentos DO EXTRATO pendentes para esta entidade, FILTRADOS POR TAB
        //    + mantém query string para paginação
        $bankStatements = $query->orderBy('dtposted', 'desc')->paginate(20)->withQueryString();

        // 4. Para cada lançamento do extrato, busca possíveis correspondências com score inteligente.
        //    Usa o ConciliacaoMatchingService para consistência com conciliacoesTab()
        $matchingService = new \App\Services\ConciliacaoMatchingService();
        foreach ($bankStatements as $lancamento) {
            $lancamento->possiveisTransacoes = $matchingService->buscarPossiveisTransacoes($lancamento, $id, 5);
        }

        // 5. CORREÇÃO: Carrega dados auxiliares usando os scopes.
        $centrosAtivos = CostCenter::forActiveCompany()->get();

        $lps = LancamentoPadrao::all();
        
        $formasPagamento = FormasPagamento::where('ativo', true)->orderBy('nome')->get();

        // 6. A sua lógica de cálculo de percentual e agrupamento por dia está ótima.
        $totalTransacoes = $entidade->transacoesFinanceiras->count();
        $totalConciliadas = $entidade->transacoesFinanceiras->where('status_conciliacao', 'ok')->count();
        $percentualConciliado = $totalTransacoes > 0 ? ($totalConciliadas / $totalTransacoes) * 100 : 0;
        $transacoesPorDia = $entidade->transacoesFinanceiras->groupBy(fn($item) => Carbon::parse($item->data_competencia)->format('Y-m-d'));

        // 6.1. Carrega todas as entidades financeiras do tipo 'banco' para o select
        $entidadesBancos = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'banco')
            ->orderBy('nome')
            ->get();

        // 6.1.1. Carrega todas as entidades financeiras do tipo 'caixa' para o select
        $entidadesCaixa = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'caixa')
            ->orderBy('nome')
            ->get();

        // 6.2. Verifica se existem horários de missa cadastrados para a empresa ativa
        $companyId = session('active_company_id');
        $hasHorariosMissas = HorarioMissa::where('company_id', $companyId)->exists();

        // 7. Retorna a view com todos os dados corretamente filtrados.
        return view('app.financeiro.entidade.show', [
            'entidade' => $entidade,
            'transacoes' => $entidade->transacoesFinanceiras,
            'conciliacoesPendentes' => $bankStatements,
            'counts' => $counts, // ✅ Novo: contadores por tipo
            'tab' => $tab, // ✅ Novo: tab atual
            'centrosAtivos' => $centrosAtivos,
            'lps' => $lps,
            'formasPagamento' => $formasPagamento,
            'percentualConciliado' => round($percentualConciliado),
            'transacoesPorDia' => $transacoesPorDia,
            'entidadesBancos' => $entidadesBancos,
            'entidadesCaixa' => $entidadesCaixa,
            'hasHorariosMissas' => $hasHorariosMissas,
            'activeTab' => 'conciliacoes', // Aba padrão
        ]);
    }

    /**
     * Retorna os dados da entidade financeira em formato JSON
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showJson($id, Request $request)
    {
        // 1. A fonte da verdade é a SESSÃO.
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        // Filtros de data (opcionais)
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        // 2. Carrega a entidade financeira usando o scope para garantir segurança.
        //    O 'with' já carrega as transações de forma otimizada.
        $entidade = EntidadeFinanceira::forActiveCompany()
            ->with(['transacoesFinanceiras' => function ($query) use ($dataInicio, $dataFim) {
                if ($dataInicio) {
                    $query->whereDate('data_competencia', '>=', Carbon::parse($dataInicio)->startOfDay());
                }
                if ($dataFim) {
                    $query->whereDate('data_competencia', '<=', Carbon::parse($dataFim)->endOfDay());
                }
                $query->orderBy('data_competencia', 'desc');
            }])
            ->findOrFail($id);

        // 3. Busca os lançamentos do extrato pendentes para esta entidade.
        //    Filtro de company_id para segurança multi-tenant.
        $bankStatements = BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->where(function ($q) {
                $q->where('conciliado_com_missa', false)
                  ->orWhereNull('conciliado_com_missa');
            })
            ->when($dataInicio, function ($query) use ($dataInicio) {
                $query->whereDate('dtposted', '>=', Carbon::parse($dataInicio)->startOfDay());
            })
            ->when($dataFim, function ($query) use ($dataFim) {
                $query->whereDate('dtposted', '<=', Carbon::parse($dataFim)->endOfDay());
            })
            ->orderBy('dtposted', 'desc')
            ->paginate(20);

        // 4. Para cada lançamento do extrato, busca possíveis correspondências com score.
        $matchingService = new \App\Services\ConciliacaoMatchingService();
        foreach ($bankStatements as $lancamento) {
            $lancamento->possiveisTransacoes = $matchingService->buscarPossiveisTransacoes($lancamento, $id, 5);
        }

        // 5. CORREÇÃO: Carrega dados auxiliares usando os scopes.
        $centrosAtivos = CostCenter::forActiveCompany()->get();

        $lps = LancamentoPadrao::all();

        // 6. A sua lógica de cálculo de percentual e agrupamento por dia está ótima.
        $totalTransacoes = $entidade->transacoesFinanceiras->count();
        $totalConciliadas = $entidade->transacoesFinanceiras->where('status_conciliacao', 'ok')->count();
        $percentualConciliado = $totalTransacoes > 0 ? ($totalConciliadas / $totalTransacoes) * 100 : 0;
        $transacoesPorDia = $entidade->transacoesFinanceiras->groupBy(fn($item) => Carbon::parse($item->data_competencia)->format('Y-m-d'));

        // 7. Calcula informações adicionais
        // Data da última atualização (updated_at da entidade)
        $dataUltimaAtualizacao = $entidade->updated_at;

        // Data do último lançamento importado (dtposted mais recente ou imported_at mais recente)
        $ultimoLancamentoImportado = BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->orderBy('dtposted', 'desc')
            ->first();
        $dataUltimoLancamento = $ultimoLancamentoImportado ? $ultimoLancamentoImportado->dtposted : null;

        // Valor pendente de conciliação (soma dos amounts dos bank statements pendentes)
        $valorPendenteConciliacao = BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->sum('amount');

        // 8. Retorna os dados em formato JSON.
        return response()->json([
            'success' => true,
            'data' => [
                'entidade' => $entidade,
                'transacoes' => $entidade->transacoesFinanceiras,
                'conciliacoesPendentes' => $bankStatements,
                'centrosAtivos' => $centrosAtivos,
                'lancamentosPadrao' => $lps,
                'percentualConciliado' => round($percentualConciliado),
                'transacoesPorDia' => $transacoesPorDia,
                'estatisticas' => [
                    'total_transacoes' => $totalTransacoes,
                    'total_conciliadas' => $totalConciliadas,
                    'total_pendentes' => $totalTransacoes - $totalConciliadas,
                ],
                'informacoesAdicionais' => [
                    'data_ultima_atualizacao' => $dataUltimaAtualizacao,
                    'data_ultimo_lancamento_importado' => $dataUltimoLancamento,
                    'valor_pendente_conciliacao' => abs($valorPendenteConciliacao),
                    'saldo_atual' => $entidade->saldo_atual ?? '0.00', // DECIMAL: retorna como string para preservar precisão
                ]
            ]
        ]);
    }

    /**
     * Retorna conciliações pendentes filtradas por tab (AJAX)
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function conciliacoesTab($id, Request $request)
    {
        try {
            $activeCompanyId = session('active_company_id');
            if (!$activeCompanyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma empresa selecionada.'
                ], 403);
            }

            $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);
            $tab = $request->input('tab', 'all');
            $page = $request->input('page', 1);

            // ✅ Query base com isolamento de empresa (segurança multi-tenant)
            $query = BankStatement::where('company_id', $activeCompanyId)
                ->where('entidade_financeira_id', $id)
                ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                ->whereDoesntHave('transacoes') // ✅ Garante que registros já conciliados não apareçam
                ->where(function ($q) {
                    $q->where('conciliado_com_missa', false)
                      ->orWhereNull('conciliado_com_missa');
                });

            // Filtro por tab (usando amount_cents para evitar problemas com decimais)
            if ($tab === 'received') {
                $query->where('amount_cents', '>', 0);
            } elseif ($tab === 'paid') {
                $query->where('amount_cents', '<', 0);
            }

            // Paginação com 5 itens por página para melhor performance
            $bankStatements = $query->orderBy('dtposted', 'desc')->paginate(5, ['*'], 'page', $page);

            // 🤖 MOTOR DE SUGESTÃO INTELIGENTE - Injeta sugestões automáticas
            try {
                $suggestionService = new \App\Services\ConciliacaoSuggestionService();
                foreach ($bankStatements as $lancamento) {
                    try {
                        $sugestaoGerada = $suggestionService->gerarSugestao($lancamento);
                        $lancamento->sugestao = $sugestaoGerada;
                    } catch (\Exception $e) {
                        \Log::warning('Erro ao gerar sugestão para lançamento', [
                            'lancamento_id' => $lancamento->id,
                            'error' => $e->getMessage()
                        ]);
                        $lancamento->sugestao = null;
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao inicializar serviço de sugestões', ['error' => $e->getMessage()]);
            }

            // ✅ Buscar possíveis transações para cada lançamento com score inteligente
            $matchingService = new \App\Services\ConciliacaoMatchingService();
            foreach ($bankStatements as $lancamento) {
                $lancamento->possiveisTransacoes = $matchingService->buscarPossiveisTransacoes($lancamento, $id, 5);

                // 🏦 Detectar movimentações internas (Rende Fácil, poupança automática, etc.)
                $lancamento->movimentacao_interna = \App\Services\MovimentacaoInternaDetector::detectar(
                    $lancamento->memo ?? '',
                    $lancamento->amount
                );
            }

            // ✅ Dados auxiliares com cache (melhora performance)
            // Nota: Removido cache por incompatibilidade com tenancy em drivers file/database
            $centrosAtivos = CostCenter::forActiveCompany()->get();
            $lps = LancamentoPadrao::all();
            $formasPagamento = FormasPagamento::where('ativo', true)->orderBy('nome')->get();

            // ✅ Calcula counts atualizados para todas as tabs (UX melhor)
            $baseQuery = BankStatement::where('company_id', $activeCompanyId)
                ->where('entidade_financeira_id', $id)
                ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
                ->whereDoesntHave('transacoes') // ✅ Consistente com a query de dados
                ->where(function ($q) {
                    $q->where('conciliado_com_missa', false)
                      ->orWhereNull('conciliado_com_missa');
                });

            $counts = [
                'all' => (clone $baseQuery)->count(),
                'received' => (clone $baseQuery)->where('amount_cents', '>', 0)->count(),
                'paid' => (clone $baseQuery)->where('amount_cents', '<', 0)->count(),
            ];

            // Renderizar o componente como HTML (SSR approach)
            $html = view('app.financeiro.entidade.partials.conciliacao-pane', [
                'entidade' => $entidade,
                'conciliacoesPendentes' => $bankStatements,
                'tipo' => $tab !== 'all' ? ($tab === 'received' ? 'entrada' : 'saida') : null,
                'centrosAtivos' => $centrosAtivos,
                'lps' => $lps,
                'formasPagamento' => $formasPagamento,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => [
                    'current_page' => $bankStatements->currentPage(),
                    'last_page' => $bankStatements->lastPage(),
                    'total' => $bankStatements->total(),
                ],
                'counts' => $counts, // ✅ Permite atualizar badges sem reload
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro em conciliacoesTab:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar conciliações: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna o histórico de conciliações realizadas para uma entidade financeira
     * Agora com paginação, busca e filtro por status (ok, pendente, ignorado, divergente)
     * Suporta requisição AJAX com retorno JSON ou direto do blade com HTML
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function historicoConciliacoes($id, Request $request)
    {
        // Verifica empresa ativa na sessão
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        // Verifica se a entidade pertence à empresa ativa
        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

        // Paginação: min 10, max 100
        $perPage = min(max((int) $request->input('per_page', 10), 10), 100);
        $q = trim((string) $request->input('q', ''));
        $status = trim((string) $request->input('status', 'all')); // Filtro de status (default: 'all' para mostrar todos)

        // Status permitidos: ok, pendente, ignorado, divergente, all, todos
        $statusPermitidos = ['ok', 'pendente', 'ignorado', 'divergente', 'all', 'todos'];
        if (!in_array($status, $statusPermitidos)) {
            $status = 'ok';
        }

        // ✅ Calcula contadores ANTES de paginar
        $countBaseQuery = BankStatement::query()
            ->where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id);

        $counts = [
            'all' => (clone $countBaseQuery)->where('status_conciliacao', '!=', 'pendente')->count(),
            'ok' => (clone $countBaseQuery)->where('status_conciliacao', 'ok')->count(),
            'pendente' => (clone $countBaseQuery)->where('status_conciliacao', 'pendente')->count(),
            'ignorado' => (clone $countBaseQuery)->where('status_conciliacao', 'ignorado')->count(),
            'divergente' => (clone $countBaseQuery)->where('status_conciliacao', 'divergente')->count(),
        ];

        // Query base: conciliações da entidade
        $query = BankStatement::query()
            ->where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id);

        // Filtro por status: se não for 'all' ou 'todos', filtra por status específico
        if (!in_array($status, ['all', 'todos'])) {
            $query->where('status_conciliacao', $status);
        } else {
            // No status 'all', listamos tudo EXCETO os pendentes (que são o extrato aberto)
            $query->where('status_conciliacao', '!=', 'pendente');
        }

        // Eager loading e ordenação
        $query->with(['transacoes.lancamentoPadrao', 'transacoes.parceiro', 'transacoes.createdBy'])
            ->latest('updated_at');

        // Busca (search) por múltiplos campos
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('memo', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhereHas('transacoes.parceiro', fn ($p) => $p->where('nome', 'like', "%{$q}%"))
                  ->orWhereHas('transacoes.lancamentoPadrao', fn ($lp) => $lp->where('description', 'like', "%{$q}%"))
                  ->orWhereHas('transacoes.createdBy', fn ($u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        // ✅ Calcula totais (entradas e saídas) da query filtrada completa (antes de paginar)
        $totalEntradas = (clone $query)->where('amount', '>=', 0)->sum('amount');
        $totalSaidas = abs((clone $query)->where('amount', '<', 0)->sum('amount'));

        // Paginação
        $paginator = $query->paginate($perPage)->appends(request()->query());

        // Formata usando Resource
        $dados = collect($paginator->items())->map(function ($item) {
            $transacao = $item->transacoes->first();

            // Converte datas para Carbon se necessário
            $dataExtrato = $item->dtposted instanceof \Carbon\Carbon 
                ? $item->dtposted 
                : ($item->dtposted ? \Carbon\Carbon::parse($item->dtposted) : null);
            
            $dataConciliacao = $item->updated_at instanceof \Carbon\Carbon
                ? $item->updated_at
                : ($item->updated_at ? \Carbon\Carbon::parse($item->updated_at) : null);

            return [
                'id' => $item->id,
                'transacao_id' => $transacao?->id,
                'descricao' => $item->memo ?? $item->name ?? '-', // Histórico do Banco
                'memo' => $item->memo,
                'transacao_descricao' => $transacao?->descricao ?? '-',
                'parceiro_nome' => $transacao?->parceiro?->nome ?? '-',
                'tipo' => $item->amount >= 0 ? 'entrada' : 'saida',
                'valor' => abs($item->amount),
                'status' => $item->status_conciliacao ?? 'pendente',
                'lancamento_padrao' => $transacao?->lancamentoPadrao?->description ?? '-',
                'usuario' => $transacao?->createdBy?->name ?? '-',
                'data_conciliacao' => $dataConciliacao?->toDateString(),
                'data_conciliacao_formatada' => $dataConciliacao?->format('d/m/Y'),
                'data_extrato' => $dataExtrato?->toDateString(),
                'data_extrato_formatada' => $dataExtrato?->format('d/m/Y'),
            ];
        });

        // Se é requisição AJAX, retorna JSON com HTML renderizado
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            // Renderiza a tabela dentro do HTML
            $html = view('app.financeiro.entidade.partials.historico-table', [
                'dados' => $dados,
                'entidade' => $entidade,
                'status' => $status,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'counts' => $counts,
                'total_entradas' => $totalEntradas,
                'total_saidas' => $totalSaidas,
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'total' => $paginator->total(),
                    'per_page' => $paginator->perPage(),
                ],
            ]);
        }

        // Requisição normal retorna o JSON
        return response()->json([
            'success' => true,
            'data' => $dados,
            'counts' => $counts,
            'total_entradas' => $totalEntradas,
            'total_saidas' => $totalSaidas,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }

    /**
     * Retorna os detalhes completos de uma conciliação (BankStatement + Transação)
     *
     * @param int $bankStatementId
     * @return \Illuminate\Http\JsonResponse
     */
    public function detalhesConciliacao($bankStatementId)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        // Busca o bank statement com a transação relacionada
        $bankStatement = BankStatement::where('company_id', $activeCompanyId)
            ->with(['transacoes.lancamentoPadrao', 'transacoes.costCenter', 'transacoes.createdBy', 'transacoes.updatedBy'])
            ->findOrFail($bankStatementId);

        $transacao = $bankStatement->transacoes->first();

        // Formata os dados para o drawer
        $dados = [
            'id' => $bankStatement->id,
            'transacao_id' => $transacao?->id,
            
            // Dados da transação
            'descricao' => $bankStatement->memo ?? $bankStatement->name ?? $transacao?->descricao ?? '-',
            'tipo' => $bankStatement->amount >= 0 ? 'entrada' : 'saida',
            'valor' => abs($bankStatement->amount),
            
            // Datas
            'data_competencia' => $transacao?->data_competencia,
            'data_competencia_formatada' => $transacao?->data_competencia ? \Carbon\Carbon::parse($transacao->data_competencia)->format('d/m/Y') : '-',
            'data_conciliacao' => $bankStatement->updated_at,
            'data_conciliacao_formatada' => $bankStatement->updated_at ? $bankStatement->updated_at->format('d/m/Y H:i') : '-',
            'data_extrato' => $bankStatement->dtposted,
            'data_extrato_formatada' => $bankStatement->dtposted ? \Carbon\Carbon::parse($bankStatement->dtposted)->format('d/m/Y') : '-',
            
            // Status e arquivo OFX
            'status_conciliacao' => $bankStatement->status_conciliacao ?? 'pendente',
            'arquivo_ofx' => $bankStatement->file_name ?? '-',
            'data_importacao_ofx' => $bankStatement->created_at,
            'data_importacao_ofx_formatada' => $bankStatement->created_at ? $bankStatement->created_at->format('d/m/Y H:i') : '-',
            
            // Dados da transação
            'lancamento_padrao' => $transacao?->lancamentoPadrao?->description ?? '-',
            'centro_custo' => $transacao?->costCenter?->nome ?? '-',
            'tipo_documento' => $transacao?->tipo_documento ?? '-',
            'numero_documento' => $transacao?->numero_documento ?? $bankStatement->checknum ?? '-',
            'comprovacao_fiscal' => $transacao?->comprovacao_fiscal ?? '-',
            'origem' => $transacao?->origem ?? '-',
            'entidade_financeira' => $transacao?->entidadeFinanceira?->nome ?? '-',
            'historico_complementar' => $transacao?->historico_complementar ?? null,
            
            // Auditoria
            'created_by_name' => $transacao?->createdBy?->name ?? '-',
            'created_at_formatado' => $transacao?->created_at ? $transacao->created_at->format('d/m/Y H:i') : '-',
            'updated_by_name' => $transacao?->updatedBy?->name ?? '-',
            'updated_at_formatado' => $transacao?->updated_at ? $transacao->updated_at->format('d/m/Y H:i') : '-',
            
            // Anexos (se houver)
            'anexos' => [],
            'recibo' => $transacao?->recibo ?? null,
        ];

        return response()->json($dados);
    }

    /**
     * Retorna apenas o total de conciliações pendentes (sem paginação)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalPendentes($id)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        // Verifica se a entidade pertence à empresa ativa
        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);

        // Conta o total de lançamentos pendentes (sem paginação, sem filtro de data)
        $query = BankStatement::where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->whereNotIn('status_conciliacao', ['ok', 'ignorado'])
            ->whereDoesntHave('transacoes')
            ->where(function ($q) {
                $q->where('conciliado_com_missa', false)
                  ->orWhereNull('conciliado_com_missa');
            });

        // Aplicar filtro de amount_cents baseado na tab
        $tab = request()->query('tab', 'all');
        if ($tab === 'received') {
            $query->where('amount_cents', '>', 0);
        } elseif ($tab === 'paid') {
            $query->where('amount_cents', '<', 0);
        }

        $totalPendentes = $query->count();

        return response()->json([
            'success' => true,
            'total' => $totalPendentes
        ]);
    }

    /**
     * Desfaz uma conciliação, deletando a transação e movimentação relacionadas
     * e atualizando o saldo da entidade financeira
     *
     * @param int $bankStatementId
     * @return \Illuminate\Http\JsonResponse
     */
    public function desfazerConciliacao($bankStatementId)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma empresa selecionada.'
            ], 403);
        }

        try {
            return \DB::transaction(function () use ($bankStatementId, $activeCompanyId) {
                // Busca o bank statement
                $bankStatement = BankStatement::where('company_id', $activeCompanyId)
                    ->with(['transacoes.movimentacao', 'transacoes.entidadeFinanceira'])
                    ->findOrFail($bankStatementId);

                $statusAtual = $bankStatement->status_conciliacao;

                // Caso 1: Status "ignorado" ou "divergente" - apenas muda o status para pendente
                // Não há transações/movimentações vinculadas, então não precisa mexer em nada
                if (in_array($statusAtual, ['ignorado', 'divergente'])) {
                    $bankStatement->update([
                        'reconciled' => false,
                        'status_conciliacao' => 'pendente'
                    ]);

                    \Log::info('Conciliação desfeita (status ignorado/divergente)', [
                        'bank_statement_id' => $bankStatementId,
                        'status_anterior' => $statusAtual,
                        'status_novo' => 'pendente',
                        'user_id' => Auth::id()
                    ]);

                    // Busca contadores atualizados
                    $countBaseQuery = BankStatement::query()
                        ->where('company_id', $activeCompanyId)
                        ->where('entidade_financeira_id', $bankStatement->entidade_financeira_id);

                    $counts = [
                        'all' => (clone $countBaseQuery)->where('status_conciliacao', '!=', 'pendente')->count(),
                        'ok' => (clone $countBaseQuery)->where('status_conciliacao', 'ok')->count(),
                        'ignorado' => (clone $countBaseQuery)->where('status_conciliacao', 'ignorado')->count(),
                        'divergente' => (clone $countBaseQuery)->where('status_conciliacao', 'divergente')->count(),
                    ];

                    return response()->json([
                        'success' => true,
                        'message' => 'Status alterado para pendente com sucesso!',
                        'counts' => $counts
                    ]);
                }

                // Caso 2: Status "ok" (conciliado) - precisa desfazer transação e movimentação
                // Verifica se há transação vinculada
                $transacao = $bankStatement->transacoes->first();
                if (!$transacao) {
                    // Se não há transação mas o status é "ok", apenas muda para pendente
                    $bankStatement->update([
                        'reconciled' => false,
                        'status_conciliacao' => 'pendente'
                    ]);

                    \Log::warning('Conciliação desfeita sem transação vinculada', [
                        'bank_statement_id' => $bankStatementId,
                        'status_anterior' => $statusAtual,
                        'user_id' => Auth::id()
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Status alterado para pendente com sucesso!'
                    ]);
                }

                // Guarda informações para log
                $entidadeId = $transacao->entidade_id;
                $tipo = $transacao->tipo;
                $valor = abs($transacao->valor);
                $movimentacaoId = $transacao->movimentacao_id;
                
                $entidade = EntidadeFinanceira::findOrFail($entidadeId);
                $saldoAnterior = $entidade->saldo_atual;

                // 1. REVERTER O CACHE (saldo_atual) - ATOMICAMENTE
                if ($tipo === 'entrada') {
                    $entidade->saldo_atual -= $valor;
                } else {
                    $entidade->saldo_atual += $valor;
                }
                $entidade->save();

                \Log::info('Saldo revertido', [
                    'entidade_id' => $entidadeId,
                    'tipo' => $tipo,
                    'valor' => $valor,
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_novo' => $entidade->saldo_atual
                ]);

                // 2. Deletar a movimentação relacionada
                if ($movimentacaoId) {
                    Movimentacao::where('id', $movimentacaoId)->delete();
                }

                // 3. Remover o vínculo na tabela pivot
                $bankStatement->transacoes()->detach($transacao->id);

                // 4. Deletar a transação financeira
                $transacao->delete();

                // 5. Atualizar o status do bank statement
                $bankStatement->update([
                    'reconciled' => false,
                    'status_conciliacao' => 'pendente'
                ]);

                \Log::info('Conciliação desfeita com sucesso', [
                    'bank_statement_id' => $bankStatementId,
                    'transacao_id' => $transacao->id,
                    'movimentacao_id' => $movimentacaoId,
                    'entidade_id' => $entidadeId,
                    'tipo' => $tipo,
                    'saldo_final' => $entidade->saldo_atual,
                    'user_id' => Auth::id()
                ]);

                // Busca contadores atualizados
                $countBaseQuery = BankStatement::query()
                    ->where('company_id', $activeCompanyId)
                    ->where('entidade_financeira_id', $bankStatement->entidade_financeira_id);

                $counts = [
                    'all' => (clone $countBaseQuery)->where('status_conciliacao', '!=', 'pendente')->count(),
                    'ok' => (clone $countBaseQuery)->where('status_conciliacao', 'ok')->count(),
                    'ignorado' => (clone $countBaseQuery)->where('status_conciliacao', 'ignorado')->count(),
                    'divergente' => (clone $countBaseQuery)->where('status_conciliacao', 'divergente')->count(),
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Conciliação desfeita com sucesso!',
                    'counts' => $counts
                ]);
            });
        } catch (\Exception $e) {
            \Log::error('Erro ao desfazer conciliação', [
                'bank_statement_id' => $bankStatementId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao desfazer conciliação: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exibe a aba de movimentações da entidade financeira
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function movimentacoes($id)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma empresa selecionada.');
        }

        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);
        $entidadesBancos = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'banco')
            ->orderBy('nome')
            ->get();
        $entidadesCaixa = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'caixa')
            ->orderBy('nome')
            ->get();
        $hasHorariosMissas = HorarioMissa::where('company_id', $activeCompanyId)->exists();

        return view('app.financeiro.entidade.show', [
            'entidade' => $entidade,
            'entidadesBancos' => $entidadesBancos,
            'entidadesCaixa' => $entidadesCaixa,
            'hasHorariosMissas' => $hasHorariosMissas,
            'activeTab' => 'movimentacoes',
        ]);
    }

    /**
     * Exibe a aba de informações da entidade financeira
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function informacoes($id)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma empresa selecionada.');
        }

        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);
        $entidadesBancos = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'banco')
            ->orderBy('nome')
            ->get();
        $entidadesCaixa = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'caixa')
            ->orderBy('nome')
            ->get();
        $hasHorariosMissas = HorarioMissa::where('company_id', $activeCompanyId)->exists();

        return view('app.financeiro.entidade.show', [
            'entidade' => $entidade,
            'entidadesBancos' => $entidadesBancos,
            'entidadesCaixa' => $entidadesCaixa,
            'hasHorariosMissas' => $hasHorariosMissas,
            'activeTab' => 'informacoes',
        ]);
    }

    /**
     * Exibe a aba de histórico de conciliações da entidade financeira
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function historico($id)
    {
        $activeCompanyId = session('active_company_id');
        if (!$activeCompanyId) {
            return redirect()->route('dashboard')->with('error', 'Nenhuma empresa selecionada.');
        }

        $entidade = EntidadeFinanceira::forActiveCompany()->findOrFail($id);
        $entidadesBancos = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'banco')
            ->orderBy('nome')
            ->get();
        $entidadesCaixa = EntidadeFinanceira::forActiveCompany()
            ->where('tipo', 'caixa')
            ->orderBy('nome')
            ->get();
        $hasHorariosMissas = HorarioMissa::where('company_id', $activeCompanyId)->exists();

        // Contadores por status para as abas do histórico
        $baseQuery = BankStatement::query()
            ->where('company_id', $activeCompanyId)
            ->where('entidade_financeira_id', $id)
            ->where('status_conciliacao', '!=', 'pendente');

        $counts = [
            'ok' => (clone $baseQuery)->where('status_conciliacao', 'ok')->count(),
            'pendente' => 0,
            'ignorado' => (clone $baseQuery)->where('status_conciliacao', 'ignorado')->count(),
            'divergente' => (clone $baseQuery)->where('status_conciliacao', 'divergente')->count(),
        ];

        return view('app.financeiro.entidade.show', [
            'entidade' => $entidade,
            'counts' => $counts,
            'entidadesBancos' => $entidadesBancos,
            'entidadesCaixa' => $entidadesCaixa,
            'hasHorariosMissas' => $hasHorariosMissas,
            'activeTab' => 'historico',
        ]);
    }
}
