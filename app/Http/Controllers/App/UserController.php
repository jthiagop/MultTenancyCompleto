<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tenant_user;
use App\Models\TenantFilial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function showProfile()
    {
    }

    public function index()
    {
        $users = User::with('roles')->get();

        $roleColors = [
            'global' => 'badge-danger',
            'admin' => 'badge-primary',
            'admin_user' => 'badge-warning',
            'user' => 'badge-info',
            // Adicione mais papéis e cores conforme necessário
        ];

        $companies = Company::all();

        $users = User::all()->map(function ($user) {
            $user->last_login_formatted = $user->last_login ? Carbon::parse($user->last_login)->diffForHumans() : 'Nunca';
            return $user;
        });


        return view(
            'app.users.index',
            [
                'users' => $users,
                'roleColors' => $roleColors,
                'companies' => $companies,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TenantFilial $tenantFilial)
    {

        $tenantFiliais = $tenantFilial->all();

        return view('app.users.create', ['tenantFiliais' => $tenantFiliais]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Obtendo o nome do banco de dados
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Exemplo de regras para o campo avatar
            'roles' => 'required|array',
            'company_id' => 'required',
            'filiais' => 'array', // Certifique-se de que 'filiais' é um array
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
        } else {
            // Define uma imagem padrão caso nenhum arquivo tenha sido enviado
            $validatedData['avatar'] = 'tenant/blank.png'; // Ajuste o caminho conforme necessário
        }

        $user = User::create($validatedData);

        // Sincronizar permissões (roles) se estiverem presentes
        if (isset($validatedData['roles'])) {
            $user->roles()->sync($request->input('roles'));
        }
        // Verifique se a chave 'filiais' existe no array de dados
        if (isset($validatedData['filiais'])) {
            $user->filiais()->sync($validatedData['filiais']);
        }

        // Relacionar o usuário à empresa na tabela pivot company_user
        $user->companies()->attach($validatedData['company_id'], [
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('users.index');
    }
    /**
     * Display the specified resource.
     */
    public function show($path, User $user)
    {
        // Formatar a data do último login
        $lastLogin = $user->last_login ? Carbon::parse($user->last_login)->diffForHumans() : 'Nunca';

        return view('app.profile.edit', ['lastLogin' => $lastLogin]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, TenantFilial $tenantFilial)
    {

        $tenantFiliais = $tenantFilial->all();

        $roles = Role::get();



        return view('app.users.edit', ['user' => $user, 'roles' => $roles, 'tenantFiliais' => $tenantFiliais]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        $validateData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'filiais' => 'array', // Adicione esta linha para validar o campo 'filiais'
        ]);

        $user->update($validateData);

        if (isset($validateData['roles'])) {
            $user->roles()->sync($request->input('roles'));
        }
        // Adicione o trecho abaixo para sincronizar as filiais
        if (isset($validateData['filiais'])) {
            $user->filiais()->sync($validateData['filiais']);
        }
        return redirect()->route('users.index')->with('success', ' Usuário cadastrodo com Sucesso!');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
