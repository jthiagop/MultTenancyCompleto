<?php

namespace App\Http\Controllers\App\Financeiro;

use App\Http\Controllers\Controller;
use App\Models\Parceiro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParceiroController extends Controller
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
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $activeCompanyId = session('active_company_id');

        $parceiro = Parceiro::create([
            'nome' => $validated['nome'],
            'cnpj' => $validated['cnpj'] ?? null,
            'telefone' => $validated['telefone'] ?? null,
            'email' => $validated['email'] ?? null,
            'company_id' => $activeCompanyId,
            'created_by' => Auth::id(),
            'created_by_name' => Auth::user()->name ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Parceiro cadastrado com sucesso!',
            'parceiro' => [
                'id' => $parceiro->id,
                'nome' => $parceiro->nome,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Parceiro $parceiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parceiro $parceiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parceiro $parceiro)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parceiro $parceiro)
    {
        //
    }
}
