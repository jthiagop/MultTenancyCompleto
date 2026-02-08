<?php

namespace App\Http\Controllers\App;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tenant_user;
use App\Models\TenantFilial;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\PermissionService;
use App\Models\Module;

class UserController extends Controller
{
    /**
     * Busca os ícones dos módulos do banco de dados
     *
     * @return array
     */
    private function getModuleIcons(): array
    {
        $modules = Module::where('is_active', true)->get();
        $moduleIcons = [];
        $defaultIcon = asset('assets/media/avatars/blank.png');

        // Ícones para módulos que existem apenas como permissões (sem registro na tabela modules)
        $fallbackIcons = [
            'users' => '/assets/media/png/perfil.svg',
            'notafiscal' => '/assets/media/png/nfe.svg',
            'company' => '/assets/media/png/building.svg',
        ];

        // Adicionar fallbacks primeiro
        foreach ($fallbackIcons as $key => $iconPath) {
            $moduleIcons[$key] = $iconPath;
        }

        foreach ($modules as $module) {
            if ($module->icon_path) {
                // Se o caminho começa com /assets, usar diretamente (arquivo público estático)
                if (str_starts_with($module->icon_path, '/assets')) {
                    $moduleIcons[$module->key] = $module->icon_path;
                } 
                // Se começa com modules/icons, usar Storage::url() para gerar URL completa
                elseif (str_starts_with($module->icon_path, 'modules/icons')) {
                    $moduleIcons[$module->key] = Storage::url($module->icon_path);
                } 
                // Se não começa com /, pode ser um caminho de storage
                elseif (!str_starts_with($module->icon_path, '/')) {
                    $moduleIcons[$module->key] = Storage::url($module->icon_path);
                } else {
                    // Fallback: usar o caminho diretamente
                    $moduleIcons[$module->key] = $module->icon_path;
                }
            } else {
                // Ícone padrão se não houver icon_path
                $moduleIcons[$module->key] = $defaultIcon;
            }
        }

        return $moduleIcons;
    }
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

        // Buscar permissões agrupadas por módulo
        $permissionService = new PermissionService();
        $permissionsByModule = $permissionService->getPermissionsByModule();
        $moduleNames = $permissionService->getModuleNames();

        // Buscar ícones dos módulos do banco de dados
        $moduleIcons = $this->getModuleIcons();

        return view(
            'app.users.index',
            [
                'users' => $users,
                'roleColors' => $roleColors,
                'companies' => $companies,
                'permissionsByModule' => $permissionsByModule,
                'moduleNames' => $moduleNames,
                'moduleIcons' => $moduleIcons,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(TenantFilial $tenantFilial, PermissionService $permissionService)
    {
        $tenantFiliais = $tenantFilial->all();
        $permissionsByModule = $permissionService->getPermissionsByModule();
        $moduleNames = $permissionService->getModuleNames();

        // Buscar ícones dos módulos do banco de dados
        $moduleIcons = $this->getModuleIcons();

        return view('app.users.create', [
            'tenantFiliais' => $tenantFiliais,
            'permissionsByModule' => $permissionsByModule,
            'moduleNames' => $moduleNames,
            'moduleIcons' => $moduleIcons,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar se é atualização (tem user_id) ou criação
        $userId = $request->input('user_id');
        $emailRule = $userId
            ? 'required|email|max:255|unique:users,email,' . $userId
            : 'required|email|max:255|unique:users,email';

        // Obtendo o nome do banco de dados
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => $userId
                ? ['nullable', 'confirmed', Rules\Password::defaults()]
                : ['required', 'confirmed', Rules\Password::defaults()],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Exemplo de regras para o campo avatar
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'roles' => 'nullable|array',
            'roles.*' => 'integer|exists:roles,id',
            'filiais' => 'array', // Certifique-se que 'filiais' é um array
            'status' => 'nullable|boolean', // Status como um campo booleano
            'notifications' => 'nullable|array', // Deve ser um array, opcional (email/telefone)
            'must_change_password' => 'nullable|boolean', // Campo para obrigar troca de senha
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
        $userData = [
            'name' => $validatedData['name'],
            'avatar' => $validatedData['avatar'],
            'active' => json_encode($validatedData['status'] ?? [0]),
            'notifications' => json_encode($validatedData['notifications'] ?? []),
            'must_change_password' => $request->has('must_change_password') && $request->input('must_change_password') == '1' ? true : false,
        ];

        // Atualizar senha apenas se fornecida
        if (!empty($validatedData['password'])) {
            $userData['password'] = bcrypt($validatedData['password']);
        }

        // Verificar se é o primeiro usuário do tenant (antes de criar)
        $isFirstUser = !$userId && User::count() === 0;

        if ($userId) {
            // Atualização
            $user = User::findOrFail($userId);
            $user->update($userData);
        } else {
            // Criação
            $userData['email'] = $validatedData['email'];
            $userData['password'] = bcrypt($validatedData['password']);
            $user = User::create($userData);
        }

        // Sincronizar roles
        if (isset($validatedData['roles']) && !empty($validatedData['roles'])) {
            // Se roles foram enviados no request, usar eles
            $validRoles = Role::whereIn('id', $request->input('roles'))->pluck('id')->toArray();
            $user->syncRoles($validRoles);
        } elseif (!$userId) {
            // Se for criação e não houver roles no request, atribuir role padrão "user"
            $defaultRole = Role::where('name', 'user')->first();
            if ($defaultRole) {
                $user->assignRole($defaultRole);
            } elseif ($isFirstUser) {
                // Se for o primeiro usuário, atribuir role "admin" ou "global"
                $adminRole = Role::whereIn('name', ['admin', 'global'])->first();
                if ($adminRole) {
                    $user->assignRole($adminRole);
                }
            }
        }

        // Sincronizar permissões
        if (isset($validatedData['permissions']) && !empty($validatedData['permissions'])) {
            // Validar que as permissões existem
            $validPermissions = Permission::whereIn('id', $request->input('permissions'))->pluck('id')->toArray();
            $user->syncPermissions($validPermissions);
        } elseif ($isFirstUser) {
            // Se for o primeiro usuário, dar todas as permissões
            $allPermissions = Permission::all()->pluck('id')->toArray();
            if (!empty($allPermissions)) {
                $user->syncPermissions($allPermissions);
            }
        } else {
            // Se não houver permissões e não for o primeiro usuário, remover todas
            $user->syncPermissions([]);
        }
        // --- A LÓGICA PRINCIPAL ESTÁ AQUI ---
        // Junta a empresa principal com as filiais
        $companiesToSync = $request->input('filiais', []);

        // Remove duplicatas e sincroniza com a tabela pivot
        $user->companies()->sync(array_unique($companiesToSync));


        // Se for requisição AJAX, retornar JSON
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Usuário criado ou atualizado com sucesso!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'must_change_password' => $user->must_change_password,
                ]
            ]);
        }

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

        // Buscar permissões agrupadas por módulo
        $permissionService = new PermissionService();
        $permissionsByModule = $permissionService->getPermissionsByModule();
        $moduleNames = $permissionService->getModuleNames();

        // Permissões efetivas = diretas + via role (o que o usuário REALMENTE pode)
        $userPermissions = $user->getAllPermissions()->pluck('id')->toArray();

        // Permissões que vêm APENAS via role (para indicação visual na UI)
        $rolePermissions = $user->getPermissionsViaRoles()->pluck('id')->toArray();

        // Permissões diretas do usuário
        $directPermissions = $user->getDirectPermissions()->pluck('id')->toArray();

        // Buscar ícones dos módulos do banco de dados
        $moduleIcons = $this->getModuleIcons();

        // Verificar se é o primeiro usuário (Usuário Supremo)
        $isFirstUser = User::orderBy('id', 'asc')->first()->id === $user->id;

        return view(
            'app.users.edit',
            [
                'user' => $user,
                'roles' => $roles,
                'tenantFiliais' => $tenantFiliais,
                'companies' => $companies,
                'permissionsByModule' => $permissionsByModule,
                'moduleNames' => $moduleNames,
                'moduleIcons' => $moduleIcons,
                'userPermissions' => $userPermissions,
                'rolePermissions' => $rolePermissions,
                'directPermissions' => $directPermissions,
                'isFirstUser' => $isFirstUser,
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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
            'filiais' => 'array', // Adicione esta linha para validar o campo 'filiais'
            'must_change_password' => 'nullable|boolean', // Campo para obrigar troca de senha
        ]);


        // Processar upload do avatar se fornecido
        if ($request->hasFile('avatar')) {
            $validateData['avatar'] = $this->handleAvatarUpload($request);
        }

        // Atualizar must_change_password se fornecido
        if ($request->has('must_change_password')) {
            $validateData['must_change_password'] = $request->input('must_change_password') == '1';
        }

        $user->update($validateData);

        // Sincronizar permissões se estiverem presentes
        if (isset($validateData['permissions']) && !empty($validateData['permissions'])) {
            // Validar que as permissões existem antes de sincronizar
            $validPermissions = Permission::whereIn('id', $request->input('permissions', []))->pluck('id')->toArray();
            $user->syncPermissions($validPermissions);
        } else {
            // Se não houver permissões, remover todas
            $user->syncPermissions([]);
        }
        // Adicione o trecho abaixo para sincronizar as filiais
        if (isset($validateData['filiais'])) {
            $user->filiais()->sync($validateData['filiais']);
        }

        // Se for requisição AJAX, retornar JSON
        if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso!',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'must_change_password' => $user->must_change_password,
                ]
            ]);
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
     * Atualiza apenas as permissões de um usuário específico.
     */
    public function updatePermissions(Request $request, User $user)
    {
        // Validação para garantir que os dados recebidos são seguros
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id' // Garante que cada item no array é um ID de permissão válido
        ]);

        // Pega o array de IDs de permissões do formulário (ex: ['1', '4', '5'])
        $permissionIds = $request->input('permissions', []);

        // Verificar se as permissões existem antes de sincronizar
        $validPermissions = Permission::whereIn('id', $permissionIds)->pluck('id')->toArray();

        // Se houver IDs inválidos, retornar erro
        if (count($permissionIds) !== count($validPermissions)) {
            $invalidIds = array_diff($permissionIds, $validPermissions);
            return redirect()->back()
                ->withErrors(['permissions' => 'Algumas permissões não foram encontradas: ' . implode(', ', $invalidIds)])
                ->withInput();
        }

        // Ao gerenciar permissões explicitamente, remover roles do usuário.
        // A role serviu apenas como template inicial de permissões.
        // A partir de agora, apenas permissões diretas serão consideradas.
        if ($user->roles->isNotEmpty()) {
            $removedRoles = $user->getRoleNames()->implode(', ');
            $user->syncRoles([]);
            \Log::info("Roles [{$removedRoles}] removidas do usuário #{$user->id} ({$user->email}) — permissões agora são gerenciadas diretamente.");
        }

        // Sincroniza as permissões diretas usando o método do Spatie
        $user->syncPermissions($validPermissions);

        return redirect()->back()->with('success', 'Permissões atualizadas com sucesso!');
    }

    /**
     * Atribui todas as permissões disponíveis ao usuário (Usuário Supremo).
     */
    public function assignAllPermissions(User $user)
    {
        try {
            // Buscar todas as permissões
            $allPermissions = Permission::all();

            if ($allPermissions->isEmpty()) {
                return redirect()->back()->with('error', 'Nenhuma permissão encontrada no sistema. Execute o seeder de permissões primeiro.');
            }

            // Atribuir todas as permissões
            $user->syncPermissions($allPermissions->pluck('id')->toArray());

            return redirect()->back()->with('success', "Todas as {$allPermissions->count()} permissões foram atribuídas ao usuário com sucesso!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atribuir permissões: ' . $e->getMessage());
        }
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
                'updated_by' => Auth::user()->id
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
            $automaticPassword = $request->boolean('automatic_password', false);
            $requireChange = $request->boolean('require_change', true);

            $password = null;
            $generatedPassword = null;

            if ($automaticPassword) {
                // Gerar senha automática
                $generatedPassword = $this->generateSecurePassword();
                $password = $generatedPassword;
            } else {
                // Validação dos dados para senha manual
                $request->validate([
                    'password' => 'required|string|min:8|max:256|confirmed',
                    'require_change' => 'boolean'
                ], [
                    'password.required' => 'A senha é obrigatória.',
                    'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
                    'password.max' => 'A senha deve ter no máximo 256 caracteres.',
                    'password.confirmed' => 'A confirmação da senha não confere.',
                ]);

                $password = $request->password;
            }

            // Atualizar a senha
            $user->password = Hash::make($password);
            $user->must_change_password = $requireChange;
            $user->password_changed_at = now();
            $user->save();

            Log::info('Senha do usuário redefinida administrativamente', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'must_change_password' => $user->must_change_password,
                'automatic_password' => $automaticPassword,
                'reset_by' => Auth::user()->id
            ]);

            $response = [
                'success' => true,
                'message' => $automaticPassword
                    ? 'Senha gerada com sucesso! ' . ($requireChange ? 'O usuário será obrigado a alterar a senha no próximo login.' : '')
                    : 'Senha redefinida com sucesso! ' . ($requireChange ? 'O usuário será obrigado a alterar a senha no próximo login.' : ''),
                'must_change_password' => $user->must_change_password
            ];

            // Se senha foi gerada automaticamente, incluir na resposta
            if ($automaticPassword && $generatedPassword) {
                $response['generated_password'] = $generatedPassword;
            }

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Erro ao redefinir senha do usuário', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ocorreu um erro ao redefinir a senha. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Gera uma senha segura automaticamente
     */
    private function generateSecurePassword($length = 12)
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $symbols = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $all = $uppercase . $lowercase . $numbers . $symbols;

        // Garantir que a senha tenha pelo menos um de cada tipo
        $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Preencher o resto da senha com caracteres aleatórios
        for ($i = strlen($password); $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        // Embaralhar a senha
        return str_shuffle($password);
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
            $user = Auth::user();

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
                'user_id' => Auth::user()->id,
                'request_data' => $request->all()
            ]);

            return back()->withErrors([
                'password' => 'Ocorreu um erro inesperado. Tente novamente.'
            ])->withInput();
        }
    }
}
