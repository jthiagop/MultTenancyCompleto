<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Models\Contabilide\AccountMapping;
use App\Models\Contabilide\ChartOfAccount;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContabilidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Determina qual aba deve estar ativa, com 'lancamento-padrao' como padrão.
        $activeTab = $request->query('tab', 'lancamento-padrao');

        // 1. Busca todas as contas da empresa ativa, ordenadas pelo código.
        $allAccounts = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        // 2. Agrupa todas as contas pelo ID do pai.
        // O resultado será um array onde a chave é o parent_id e o valor é uma coleção de filhos.
        $allGroupedAccounts = $allAccounts->groupBy('parent_id');

        // 3. Pega as contas raiz (aquelas que não têm pai).
        // Usamos o operador '??' para garantir que, se não houver contas raiz, teremos uma coleção vazia.
        $rootAccounts = $allGroupedAccounts[null] ?? collect();

        // Busca todas as contas da empresa ativa para a listagem e para o dropdown do modal.
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        $mapeamentos = AccountMapping::with(['lancamentoPadrao', 'contaDebito', 'contaCredito'])
            ->forActiveCompany()
            ->get();

        // Dados para popular os dropdowns do modal de mapeamento.
        $lancamentosPadrao = LancamentoPadrao::forActiveCompany()->get();

        // Busca lançamentos padrão para a aba de lançamentos padrão
        $lancamentosPadraoList = LancamentoPadrao::with(['contaDebito', 'contaCredito', 'user'])
            ->forActiveCompany()
            ->orderBy('description')
            ->get();

        return view('app.contabilidade.index', compact(
            'rootAccounts',
            'allGroupedAccounts',
            'mapeamentos',
            'activeTab',
            'lancamentosPadrao',
            'lancamentosPadraoList',
            'contas'
        ));
    }

    public function categoriasData(Request $request)
    {
        $activeCompanyId = (int) session('active_company_id');

        $rows = LancamentoPadrao::forActiveCompany($activeCompanyId ?: null)
            ->with([
                'contaDebito:id,code,name',
                'contaCredito:id,code,name',
                'companies:id,name,avatar,type',
            ])
            ->orderBy('description')
            ->get()
            ->map(fn ($lp) => [
                'id' => (int) $lp->id,
                'codigo' => $lp->codigo,
                'descricao' => (string) ($lp->description ?? ''),
                'categoria' => (string) ($lp->category ?? ''),
                'tipo' => (string) ($lp->type ?? ''),
                'is_active' => (bool) ($lp->is_active ?? true),
                'contaDebito' => $lp->contaDebito
                    ? trim(($lp->contaDebito->code ?? '') . ' - ' . ($lp->contaDebito->name ?? ''))
                    : 'Não definida',
                'contaCredito' => $lp->conta_credito_id == 0
                    ? '-- Usar conta do Banco/Caixa --'
                    : ($lp->contaCredito
                        ? trim(($lp->contaCredito->code ?? '') . ' - ' . ($lp->contaCredito->name ?? ''))
                        : 'Não definida'),
                'conta_debito_id' => $lp->conta_debito_id,
                'conta_credito_id' => $lp->conta_credito_id,
                'scope' => $lp->classificacaoParaCompany($activeCompanyId ?: null),
                'company_ids' => $lp->companies->pluck('id')->map(fn ($v) => (int) $v)->values()->all(),
                'companies' => $lp->companies->map(fn ($c) => [
                    'id' => (int) $c->id,
                    'name' => (string) $c->name,
                    'type' => (string) ($c->type ?? ''),
                    'avatar' => $c->avatar,
                ])->values()->all(),
            ])
            ->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
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
        $activeCompanyId = session('active_company_id');
        if (! $activeCompanyId) {
            return response()->json(['success' => false, 'message' => 'Nenhuma empresa ativa.'], 403);
        }

        $validated = $request->validate([
            'description'      => 'required|string|max:255',
            'codigo'           => [
                'nullable',
                'string',
                'max:50',
                // Unicidade por company é validada manualmente abaixo, via pivot.
            ],
            'category'         => 'nullable|string|max:255',
            'type'             => ['required', Rule::in(['entrada', 'saida', 'ambos', 'transferencia', 'somente_contabil'])],
            'is_active'        => 'boolean',
            'conta_debito_id'  => 'nullable|integer|exists:chart_of_accounts,id',
            'conta_credito_id' => 'nullable|integer|exists:chart_of_accounts,id',
            'company_ids'      => 'nullable|array',
            'company_ids.*'    => 'integer|exists:companies,id',
        ], [
            'codigo.max' => 'O código não pode ter mais de 50 caracteres.',
        ]);

        $codigo = isset($validated['codigo']) ? trim($validated['codigo']) : null;
        if ($codigo === '') {
            $codigo = null;
        }

        // Resolve as companies destino (vazio = global no tenant).
        $companyIds = array_values(array_unique(array_map('intval', $validated['company_ids'] ?? [])));
        if (empty($companyIds) && array_key_exists('company_ids', $request->all()) === false) {
            // Request legacy sem o campo — default: escopa na company ativa.
            $companyIds = [(int) $activeCompanyId];
        }

        // Unicidade do código: visível no mesmo contexto (considerando herança)
        if ($codigo !== null) {
            $duplicada = LancamentoPadrao::forActiveCompany((int) $activeCompanyId)
                ->where('codigo', $codigo)
                ->exists();
            if ($duplicada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma categoria com este código visível nesta empresa.',
                    'errors'  => ['codigo' => ['Já existe uma categoria com este código visível nesta empresa.']],
                ], 422);
            }
        }

        $categoria = LancamentoPadrao::create([
            'description'      => $validated['description'],
            'codigo'           => $codigo,
            'category'         => $validated['category'] ?? null,
            'type'             => $validated['type'],
            'is_active'        => $validated['is_active'] ?? true,
            'conta_debito_id'  => $validated['conta_debito_id'] ?? null,
            'conta_credito_id' => $validated['conta_credito_id'] ?? null,
            'user_id'          => Auth::id(),
        ]);

        $categoria->syncCompanyHierarchy($companyIds);

        return response()->json([
            'success'   => true,
            'message'   => 'Categoria criada com sucesso.',
            'categoria' => $categoria,
        ], 201);
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
        $categoria = LancamentoPadrao::forActiveCompany()
            ->with('companies:id')
            ->findOrFail($id);

        return response()->json([
            'success'   => true,
            'categoria' => [
                'id'               => $categoria->id,
                'codigo'           => $categoria->codigo,
                'description'      => $categoria->description,
                'category'         => $categoria->category,
                'type'             => $categoria->type,
                'is_active'        => (bool) $categoria->is_active,
                'conta_debito_id'  => $categoria->conta_debito_id,
                'conta_credito_id' => $categoria->conta_credito_id,
                'company_ids'      => $categoria->companies->pluck('id')->map(fn ($v) => (int) $v)->values()->all(),
                'scope'            => $categoria->classificacaoParaCompany(),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $categoria = LancamentoPadrao::forActiveCompany()->findOrFail($id);

        $activeCompanyId = session('active_company_id');

        $validated = $request->validate([
            'description'      => 'required|string|max:255',
            'codigo'           => [
                'nullable',
                'string',
                'max:50',
            ],
            'category'         => 'nullable|string|max:255',
            'type'             => ['required', Rule::in(['entrada', 'saida', 'ambos', 'transferencia', 'somente_contabil'])],
            'is_active'        => 'boolean',
            'conta_debito_id'  => 'nullable|integer|exists:chart_of_accounts,id',
            'conta_credito_id' => 'nullable|integer|exists:chart_of_accounts,id',
            'company_ids'      => 'nullable|array',
            'company_ids.*'    => 'integer|exists:companies,id',
        ], [
            'codigo.max' => 'O código não pode ter mais de 50 caracteres.',
        ]);

        $codigo = isset($validated['codigo']) ? trim($validated['codigo']) : null;
        if ($codigo === '') {
            $codigo = null;
        }

        // Unicidade do código: visível no mesmo contexto e não seja o próprio registro.
        if ($codigo !== null) {
            $duplicada = LancamentoPadrao::forActiveCompany((int) $activeCompanyId)
                ->where('codigo', $codigo)
                ->where('id', '<>', $categoria->id)
                ->exists();
            if ($duplicada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Já existe uma categoria com este código visível nesta empresa.',
                    'errors'  => ['codigo' => ['Já existe uma categoria com este código visível nesta empresa.']],
                ], 422);
            }
        }

        $categoria->update([
            'description'      => $validated['description'],
            'codigo'           => $codigo,
            'category'         => $validated['category'] ?? null,
            'type'             => $validated['type'],
            'is_active'        => $validated['is_active'] ?? true,
            'conta_debito_id'  => $validated['conta_debito_id'] ?? null,
            'conta_credito_id' => $validated['conta_credito_id'] ?? null,
        ]);

        // Apenas ajusta o pivot quando o cliente enviou explicitamente o campo
        if ($request->has('company_ids')) {
            $companyIds = array_values(array_unique(array_map('intval', $request->input('company_ids', []) ?: [])));
            $categoria->syncCompanyHierarchy($companyIds);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categoria atualizada com sucesso.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categoria = LancamentoPadrao::forActiveCompany()->findOrFail($id);
        $categoria->delete();

        return response()->json([
            'success' => true,
            'message' => 'Categoria removida com sucesso.',
        ]);
    }
}
