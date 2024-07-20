<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companyes = Company::all();
        return view('app.company.index', ['companyes'=> $companyes]);
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

                // Obtendo o nome do banco de dados
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'cnpj' => 'required|max:20|unique:companies,cnpj',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Exemplo de regras para o campo avatar
                ]);

                // Processar e salvar o avatar
                if ($request->hasFile('avatar')) {
                    // Obtém o arquivo de avatar do request
                    $avatar = $request->file('avatar');

                    // Gera um nome único para o arquivo de avatar
                    $avatarName = time() . '_' . $avatar->getClientOriginalName();

                    // Salva o arquivo na pasta 'perfis' dentro da pasta de armazenamento (storage/app/public)
                    $avatarPath = Storage::put('perfis', $request->file('avatar'));

                    // Salva o nome do arquivo na coluna 'avatar' do usuário no banco de dados
                    $validatedData['avatar'] = $avatarPath;
                }

        $user = Company::create($validatedData);

        return redirect()->route('company.index');

    }

    /**
     * Display the specified resource.
     */
    public function show($companyId)
    {
        $company = Company::with('users')->findOrFail($companyId);
        return view('company.show', compact('company'));
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
