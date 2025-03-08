<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\FormasPagamento;
use Auth;
use Illuminate\Http\Request;

class FormasPagamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Busca todos os dados da tabela formas_pagamento
        $formasPagamento = FormasPagamento::all();

        // Passa os dados para a view
        return view('app.cadastros.formasPagamento.index', compact('formasPagamento'));
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
    // Criar uma nova forma de pagamento
    public function store(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'codigo' => 'required|string|max:10|unique:formas_pagamento',
            'ativo' => 'required|boolean|in:1,0',
            'tipo_taxa' => 'required|in:valor_fixo,porcentagem',
            'taxa' => 'required',
            'observacao' => 'nullable'
        ]);

        // Campos de auditoria
        $validatedData['created_by'] = Auth::id();
        $validatedData['created_by_name'] = Auth::user()->name;


        // Cria a forma de pagamento
        $formaPagamento = FormasPagamento::create($validated);

        // Retorna uma resposta JSON
        return redirect()
        ->back()
        ->with('success', 'Forma de Pagamento cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    // Obter uma forma de pagamento específica
    // Atualizar uma forma de pagamento
    public function update(Request $request, $id)
    {
        $formaPagamento = FormasPagamento::findOrFail($id);

        $request->validate([
            'nome' => 'string|max:100',
            'codigo' => 'string|max:10|unique:formas_pagamento,codigo,' . $formaPagamento->id,
            'ativo' => 'boolean',
            'taxa' => 'numeric',
            'prazo_liberacao' => 'integer',
            'metodo_integracao' => 'nullable|string|max:100',
        ]);

        $formaPagamento->update($request->all());

        return $formaPagamento;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FormasPagamento $formasPagamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $formaPagamento = FormasPagamento::findOrFail($id);
        $formaPagamento->delete();

        return response()->noContent();
    }
}
