<?php

namespace App\Http\Controllers\App\Contabilidade;

use App\Http\Controllers\Controller;
use App\Models\Contabilide\AccountMapping;
use App\Models\Contabilide\ChartOfAccount;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;

class ContabilidadeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Determina qual aba deve estar ativa, com 'plano-contas' como padrão.
        $activeTab = $request->query('tab', 'plano-contas');

        // 1. Busca todas as contas da empresa ativa, ordenadas pelo código.
        $allAccounts = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        // 2. Agrupa todas as contas pelo ID do pai.
        // O resultado será um array onde a chave é o parent_id e o valor é uma coleção de filhos.
        $groupedAccounts = $allAccounts->groupBy('parent_id');

        // 3. Pega as contas raiz (aquelas que não têm pai).
        // Usamos o operador '??' para garantir que, se não houver contas raiz, teremos uma coleção vazia.
        $rootAccounts = $groupedAccounts[null] ?? collect();

        // Busca todas as contas da empresa ativa para a listagem e para o dropdown do modal.
        $contas = ChartOfAccount::forActiveCompany()->orderBy('code')->get();

        $mapeamentos = AccountMapping::with(['lancamentoPadrao', 'contaDebito', 'contaCredito'])
            ->forActiveCompany()
            ->get();

        // Dados para popular os dropdowns do modal de mapeamento.
        $lancamentosPadrao = LancamentoPadrao::all();

        return view('app.contabilidade.index', [
            'rootAccounts' => $rootAccounts,
            'allGroupedAccounts' => $groupedAccounts,
            'mapeamentos' => $mapeamentos,
            'activeTab' => $activeTab,
            'lancamentosPadrao' => $lancamentosPadrao,
        ], compact('contas'));
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
}
