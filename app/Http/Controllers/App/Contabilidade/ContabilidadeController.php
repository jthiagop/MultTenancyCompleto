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
        $rows = LancamentoPadrao::forActiveCompany()
            ->with(['contaDebito:id,code,name', 'contaCredito:id,code,name'])
            ->orderBy('description')
            ->get()
            ->map(fn ($lp) => [
                'id' => (int) $lp->id,
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
        $validated = $request->validate([
            'description'      => 'required|string|max:255',
            'category'         => 'nullable|string|max:255',
            'type'             => ['required', Rule::in(['entrada', 'saida', 'ambos', 'transferencia', 'somente_contabil'])],
            'is_active'        => 'boolean',
            'conta_debito_id'  => 'nullable|integer|exists:chart_of_accounts,id',
            'conta_credito_id' => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        $activeCompanyId = session('active_company_id');
        if (! $activeCompanyId) {
            return response()->json(['success' => false, 'message' => 'Nenhuma empresa ativa.'], 403);
        }

        $categoria = LancamentoPadrao::create([
            'description'      => $validated['description'],
            'category'         => $validated['category'] ?? null,
            'type'             => $validated['type'],
            'is_active'        => $validated['is_active'] ?? true,
            'conta_debito_id'  => $validated['conta_debito_id'] ?? null,
            'conta_credito_id' => $validated['conta_credito_id'] ?? null,
            'company_id'       => $activeCompanyId,
            'user_id'          => Auth::id(),
        ]);

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
        $categoria = LancamentoPadrao::forActiveCompany()->findOrFail($id);

        return response()->json([
            'success'   => true,
            'categoria' => [
                'id'               => $categoria->id,
                'description'      => $categoria->description,
                'category'         => $categoria->category,
                'type'             => $categoria->type,
                'is_active'        => (bool) $categoria->is_active,
                'conta_debito_id'  => $categoria->conta_debito_id,
                'conta_credito_id' => $categoria->conta_credito_id,
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $categoria = LancamentoPadrao::forActiveCompany()->findOrFail($id);

        $validated = $request->validate([
            'description'      => 'required|string|max:255',
            'category'         => 'nullable|string|max:255',
            'type'             => ['required', Rule::in(['entrada', 'saida', 'ambos', 'transferencia', 'somente_contabil'])],
            'is_active'        => 'boolean',
            'conta_debito_id'  => 'nullable|integer|exists:chart_of_accounts,id',
            'conta_credito_id' => 'nullable|integer|exists:chart_of_accounts,id',
        ]);

        $categoria->update([
            'description'      => $validated['description'],
            'category'         => $validated['category'] ?? null,
            'type'             => $validated['type'],
            'is_active'        => $validated['is_active'] ?? true,
            'conta_debito_id'  => $validated['conta_debito_id'] ?? null,
            'conta_credito_id' => $validated['conta_credito_id'] ?? null,
        ]);

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
