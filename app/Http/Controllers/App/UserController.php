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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function showProfile() {}

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
            $user->getLastLoginFormattedAttribute = $user->last_login ? Carbon::parse($user->last_login)->diffForHumans() : 'Nunca';
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
            'filiais' => 'array', // Certifique-se de que 'filiais' é um array
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
                'active' => json_encode($validatedData['status'] ?? [0]),
                'notifications' => json_encode($validatedData['notifications'] ?? []),
            ]
        );
        // Sincronizar permissões (roles) se estiverem presentes
        if (isset($validatedData['roles'])) {
            $user->roles()->sync($request->input('roles'));
        }
        // --- A LÓGICA PRINCIPAL ESTÁ AQUI ---
        // Junta a empresa principal com as filiais
        $companiesToSync = $request->input('filiais', []);

        // Remove duplicatas e sincroniza com a tabela pivot
        $user->companies()->sync(array_unique($companiesToSync));


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

        $companies = Company::all();


        return view(
            'app.users.edit',
            [
                'user' => $user,
                'roles' => $roles,
                'tenantFiliais' => $tenantFiliais,
                'companies' => $companies
            ]
        );
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

    /**
     * Atualiza apenas as permissões (roles) de um usuário específico.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRoles(Request $request, User $user)
    {
        // Validação para garantir que os dados recebidos são seguros
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id' // Garante que cada item no array é um ID de role válido
        ]);

        // Pega o array de IDs de roles do formulário (ex: ['1', '4'])
        $roles = $request->input('roles', []);

        // O syncRoles entende que você está passando IDs e vai sincronizar corretamente.
        if (isset($roles)) {
            $user->roles()->sync($roles);
        }
        return redirect()->back()->with('success', 'Permissões atualizadas com sucesso!');
    }

    /**
     * Atualiza apenas o acesso às filiais (companhias) de um usuário específico.
     */
    public function updateFiliais(Request $request, User $user)
    {
        // Validação
        $request->validate([
            'filiais' => 'nullable|array',
            'filiais.*' => 'integer|exists:companies,id' // Garante que cada item é um ID de companhia válido
        ]);

        // Pega o array de IDs das filiais do formulário
        $filiais = $request->input('filiais', []);

        // Sincroniza o acesso. O método sync() cuida de adicionar e remover os acessos.
        // Assumindo que a relação no seu modelo User se chama 'companies'
        $user->companies()->sync($filiais);

        return redirect()->back()->with('success', 'Acesso às filiais atualizado com sucesso!');
    }

    /**
     * Ativa ou desativa a conta de um usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, User $user)
    {
        try {
            // Inverte o status atual do usuário.
            // Se 'active' for true, se tornará false, e vice-versa (graças ao cast no modelo).
            $newStatus = !$user->active;

            $user->update(['active' => $newStatus]);

            $message = $newStatus ? 'Usuário ativado com sucesso!' : 'Usuário desativado com sucesso!';

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            // Registra o erro detalhado no log para o desenvolvedor.
            Log::error('Erro ao atualizar o status do usuário: ' . $e->getMessage());

            // Retorna uma mensagem de erro amigável para o usuário.
            return redirect()->back()->with('error', 'Ocorreu um erro inesperado. Por favor, tente novamente.');
        }
    }

    /**
     * Atualiza o email do usuário com validação de senha
     */
    public function updateEmail(Request $request, User $user)
    {
        try {
            // Validação dos dados
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'required|string|min:6',
            ], [
                'email.required' => 'O email é obrigatório.',
                'email.email' => 'O email deve ter um formato válido.',
                'email.unique' => 'Este email já está sendo usado por outro usuário.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            ]);

            // Verificar se a senha está correta
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Senha incorreta. Verifique e tente novamente.'
                ], 422);
            }

            // Verificar se o email é diferente do atual
            if ($request->email === $user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'O novo email deve ser diferente do email atual.'
                ], 422);
            }

            // Atualizar o email
            $user->email = $request->email;
            $user->email_verified_at = null; // Marcar como não verificado
            $user->save();

            Log::info('Email do usuário atualizado', [
                'user_id' => $user->id,
                'old_email' => $user->getOriginal('email'),
                'new_email' => $request->email,
                'updated_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email atualizado com sucesso! Você precisará verificar o novo email na próxima vez que fizer login.',
                'new_email' => $user->email
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar email do usuário: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro inesperado. Por favor, tente novamente.'
            ], 500);
        }
    }

    /**
     * Verifica se a senha está correta (para validação em tempo real)
     */
    public function verifyPassword(Request $request, User $user)
    {
        try {
            $request->validate([
                'password' => 'required|string',
            ]);

            $isCorrect = Hash::check($request->password, $user->password);

            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'message' => $isCorrect ? 'Senha correta' : 'Senha incorreta'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao verificar senha: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar senha'
            ], 500);
        }
    }

    /**
     * Redefine a senha do usuário (administrativo - sem senha atual)
     */
    public function resetPassword(Request $request, User $user)
    {
        try {
            // Validação dos dados
            $request->validate([
                'password' => 'required|string|min:8|max:256|confirmed',
                'require_change' => 'boolean'
            ], [
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
                'password.max' => 'A senha deve ter no máximo 256 caracteres.',
                'password.confirmed' => 'A confirmação da senha não confere.',
            ]);

            // Validar complexidade da senha
            $password = $request->password;
            $hasUppercase = preg_match('/[A-Z]/', $password);
            $hasLowercase = preg_match('/[a-z]/', $password);
            $hasNumbers = preg_match('/[0-9]/', $password);
            $hasSymbols = preg_match('/[^A-Za-z0-9]/', $password);

            $complexityCount = $hasUppercase + $hasLowercase + $hasNumbers + $hasSymbols;

            if ($complexityCount < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'A senha deve conter pelo menos 3 dos seguintes: letras maiúsculas, minúsculas, números e símbolos.'
                ], 422);
            }

            // Atualizar a senha
            $user->password = Hash::make($password);
            $user->must_change_password = $request->boolean('require_change', true);
            $user->password_changed_at = now();
            $user->save();

            Log::info('Senha do usuário redefinida administrativamente', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'must_change_password' => $user->must_change_password,
                'reset_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Senha redefinida com sucesso! ' . 
                    ($user->must_change_password ? 'O usuário será obrigado a alterar a senha no próximo login.' : ''),
                'must_change_password' => $user->must_change_password
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erro ao redefinir senha do usuário: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro inesperado. Por favor, tente novamente.'
            ], 500);
        }
    }

    /**
     * Exibe a tela de alteração obrigatória de senha
     */
    public function showPasswordChange()
    {
        return view('app.auth.change-password');
    }

    /**
     * Processa a alteração obrigatória de senha
     */
    public function updatePasswordChange(Request $request)
    {
        try {
            $user = auth()->user();

            // Validação dos dados
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8|max:256|confirmed',
            ], [
                'current_password.required' => 'A senha atual é obrigatória.',
                'password.required' => 'A nova senha é obrigatória.',
                'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
                'password.max' => 'A nova senha deve ter no máximo 256 caracteres.',
                'password.confirmed' => 'A confirmação da senha não confere.',
            ]);

            // Verificar se a senha atual está correta
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'A senha atual está incorreta.'
                ])->withInput();
            }

            // Validar complexidade da senha
            $password = $request->password;
            $hasUppercase = preg_match('/[A-Z]/', $password);
            $hasLowercase = preg_match('/[a-z]/', $password);
            $hasNumbers = preg_match('/[0-9]/', $password);
            $hasSymbols = preg_match('/[^A-Za-z0-9]/', $password);

            $complexityCount = $hasUppercase + $hasLowercase + $hasNumbers + $hasSymbols;

            if ($complexityCount < 3) {
                return back()->withErrors([
                    'password' => 'A senha deve conter pelo menos 3 dos seguintes: letras maiúsculas, minúsculas, números e símbolos.'
                ])->withInput();
            }

            // Atualizar a senha
            $user->password = Hash::make($password);
            $user->must_change_password = false;
            $user->password_changed_at = now();
            $user->save();

            Log::info('Usuário alterou senha obrigatória', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);

            return redirect()->route('dashboard')->with('success', 'Senha alterada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao alterar senha obrigatória: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return back()->withErrors([
                'password' => 'Ocorreu um erro inesperado. Tente novamente.'
            ])->withInput();
        }
    }
}
