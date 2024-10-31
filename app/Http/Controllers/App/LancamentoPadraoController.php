<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\LancamentoPadrao;
use Illuminate\Http\Request;

class LancamentoPadraoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $lps = LancamentoPadrao::all();
        $lancamentoPadrao = LancamentoPadrao::all();


        // Mapeia categorias para classes de cor
        $categoryColors = [
            'Serviços essenciais' => 'badge-light-success',
            'Suprimentos' => 'badge-light-primary',
            'Pessoal' => 'badge-light-warning',
            'Alimentação' => 'badge-light-info',
            'Saúde' => 'badge-light-danger',
            'Manutenção' => 'badge-light-dark',
            'Liturgia' => 'badge-light-muted',
            'Equipamentos' => 'badge-light-secondary',
            'Material de escritório' => 'badge-light-light',
            'Educação' => 'badge-light-orange',
            'Transporte' => 'badge-light-teal',
            'Contribuições' => 'badge-light-purple',
            // Adicione outras categorias e cores conforme necessário
        ];

        return view('app.cadastros.lancamentoPadrao.index', compact('lps', 'categoryColors', 'lancamentoPadrao'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Obtém o tipo selecionado do request
        $tipo = $request->input('tipo');

        // Se um tipo foi selecionado, busque os lançamentos correspondentes
        $lancamentos = $tipo ? LancamentoPadrao::where('tipo', $tipo)->get() : collect();

        return view('sua_view', compact('lancamentos', 'tipo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'category' => 'required|string',
        ]);

        $user = auth()->user(); // Usuário autenticado

        // Criação do lançamento
        LancamentoPadrao::create([
            'type' => $request->input('type'),
            'description' => $request->input('description'),
            'date' => $request->input('date'),
            'category' => $request->input('category'),
            'user_id' => $user->id, // Pegando o ID do usuário autenticado
        ]);

        return redirect()->route('lancamentoPadrao.index') ;
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lps = LancamentoPadrao::find($id);
        return response()->json($lps);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string',
            // Validações adicionais...
        ]);

        $lancamento = LancamentoPadrao::findOrFail($id);
        $lancamento->update($data);

        return redirect()->route('lancamentoPadrao.index')->with('success', 'Lançamento Padrão atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



}
