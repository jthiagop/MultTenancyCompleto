<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\NamePatrimonio;
use App\Models\User;
use Illuminate\Http\Request;

class NamePatrimonioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nameForos = NamePatrimonio::all();

        return view('app.patrimonios.create', [
            'nameForos' => $nameForos,
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
    public function store(Request $request, $id = null)
    {
        $subsidiaryId = User::getCompany();
        $user = auth()->user(); // Usuário autenticado


        $validatedData = $request->validate([
            'name' => 'required',
            'cep' => 'required',
            'logradouro' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
            'uf' => 'required|string|size:2', // Verifica se o UF tem exatamente 2 caracteres
            'ibge' => 'required|numeric|digits:7', // Exemplo para o IBGE com 7 dígitos
            'complemento' => 'nullable|string|max:255', // Torne o campo opcional e defina um limite de caracteres
            'numForo' => 'nullable|string|max:10', // Torne o campo opcional e defina um limite de caracteres
        ]);
        $validatedData['company_id'] = $subsidiaryId->company_id;
        $validatedData['created_by'] = $user->id;
        $validatedData['updated_by'] = $user->id;

        $patrimonio = NamePatrimonio::create($validatedData);


        return response()->json(['success' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(NamePatrimonio $namePatrimonio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NamePatrimonio $namePatrimonio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NamePatrimonio $namePatrimonio)
    {
        $user = auth()->user(); // Usuário autenticado


        $validatedData = $request->validate([
            'name' => 'required',
            'cep' => 'required',
            'logradouro' => 'required|string|max:255',
            'bairro' => 'required|string|max:255',
            'localidade' => 'required|string|max:255',
            'uf' => 'required|string|size:2', // Verifica se o UF tem exatamente 2 caracteres
            'ibge' => 'required|numeric|digits:7', // Exemplo para o IBGE com 7 dígitos
            'complemento' => 'nullable|string|max:255', // Torne o campo opcional e defina um limite de caracteres
            'numForo' => 'nullable|string|max:10', // Torne o campo opcional e defina um limite de caracteres
        ]);
        $validatedData['updated_by'] = $user->id;

        $namePatrimonio->update($validatedData);

        return response()->json(['success' => true]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NamePatrimonio $namePatrimonio)
    {
        //
    }
}
