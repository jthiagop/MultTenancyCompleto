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
            'details' => 'nullable|string|max:5000', // Detalhes do usuário, texto opcional com limite
            'status' => 'nullable|boolean', // Status como um campo booleano
            'notifications' => 'nullable|array', // Deve ser um array, opcional (email/telefone)
        ]);

        $validatedData['status'] = json_encode($request->input('status', [0]));

        // Verificar se já existe um usuário associado ao e-mail para manter o avatar atual
        $existingUser = User::where('email', $validatedData['email'])->first();

        if ($existingUser) {
            $validatedData['avatar'] = $existingUser->avatar;
        } else {
            // Processar o upload do avatar se não existir
            $validatedData['avatar'] = $this->handleAvatarUpload($request);
        }

        // Criação ou atualização do usuário
        $user = User::updateOrCreate(
            ['email' => $validatedData['email']], // Critério para identificar o usuário
            [
                'name' => $validatedData['name'],
                'password' => bcrypt($validatedData['password']),
                'avatar' => $validatedData['avatar'],
                'company_id' => $validatedData['company_id'],
                'details' => $validatedData['details'],
                'active' => json_encode($validatedData['status'] ?? [0]),
                'notifications' => json_encode($validatedData['notifications'] ?? []),
                ]
        );
        // Sincronizar permissões (roles) se estiverem presentes
        if (isset($validatedData['roles'])) {
            $user->roles()->sync($request->input('roles'));
        }

        // Relacionar o usuário à empresa na tabela pivot company_user
        $user->companies()->attach($validatedData['company_id'], [
            'created_at' => now(),
            'updated_at' => now()
        ]);
    // Adicionar mensagem de sucesso ao Flasher
    session()->flash('success', 'Usuário criado ou atualizado com sucesso.');

    // Retornar para a página anterior
    return redirect()->back();
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

    /**
 * Processa o upload do avatar.
 *
 * @param Request $request
 * @return string Caminho do avatar salvo
 */
private function handleAvatarUpload(Request $request)
{
    if ($request->hasFile('avatar')) {
        $avatar = $request->file('avatar');
        $avatarName = time() . '_' . $avatar->getClientOriginalName();

        if (!Storage::exists('perfis')) {
            Storage::makeDirectory('perfis');
        }

        return Storage::putFileAs('perfis', $avatar, $avatarName);
    }

    return 'tenant/blank.png'; // Imagem padrão se não houver upload
}
}
