<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Adress;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $companyes = Company::all();
        return view('app.company.index', ['companyes' => $companyes, 'users' => $users]);
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
    public function show($id)
    {
        $company = Company::findOrFail($id); // Garante que a empresa existe ou lança erro 404
        return view('app.company.show', compact('company'));
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $company = Company::with('addresses')->findOrFail($id); // Busca a empresa e o endereço relacionado
        return view('app.company.edit', ['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validação dos dados
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|max:19|regex:/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
            'email' => 'required|email|max:255',
            'cep' => 'required|string|size:9|regex:/^\d{5}-\d{3}$/',
            'logradouro' => 'required|string|max:255',
            'numero' => 'nullable|string|max:10',
            'bairro' => 'required|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'localidade' => 'required|string|max:255',
            'uf' => ['required', Rule::in(['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'])],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Retorna com os erros de validação, caso existam
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Atualiza os dados principais da empresa
        $company = Company::findOrFail($id);
        $company->update($request->only('name', 'cnpj', 'email'));

        // Atualiza o endereço da empresa
        $address = Adress::firstOrNew(['company_id' => $company->id]);
        $address->fill([
            'cep' => $request->cep,
            'rua' => $request->logradouro,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'complemento' => $request->complemento,
            'cidade' => $request->localidade,
            'uf' => $request->uf,
        ])->save();

        // Atualiza o avatar da empresa
        if ($request->hasFile('avatar')) {
            // Remove o avatar anterior, se houver
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }

            // Armazena o novo avatar
            $avatarPath = $request->file('avatar')->store('brasao', 'public');
            $company->avatar = $avatarPath;
        } elseif ($request->input('avatar_remove') === '1') {
            // Remove o avatar se solicitado
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }
            $company->avatar = null;
        }


        // Salva as mudanças da empresa
        $company->save();

        // Redireciona com mensagem de sucesso
        return redirect()->route('company.edit', $company->id)
            ->with('success', 'Informações atualizadas com sucesso.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
