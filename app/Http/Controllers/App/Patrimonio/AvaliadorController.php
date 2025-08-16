<?php

namespace App\Http\Controllers\App\Patrimonio;

use App\Http\Controllers\Controller;
use App\Models\Patrimonio\Avaliador;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AvaliadorController extends Controller
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
        try {
            $request->validate([
                'nome' => 'required|string|max:255',
                'registro_profissional' => 'nullable|string|max:50',
                'tipo_profissional' => 'required|string',
                'telefone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
            ]);

            $avaliador = Avaliador::create([
                'nome' => $request->nome,
                'registro_profissional' => $request->registro_profissional,
                'tipo_profissional' => $request->tipo_profissional,
                'telefone' => $request->telefone,
                'email' => $request->email,
                'created_by' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Avaliador cadastrado com sucesso!');
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao salvar avaliador.', 'error' => $e->getMessage()], 500);
        }
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
