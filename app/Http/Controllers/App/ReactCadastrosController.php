<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Company;
use App\Models\Module;
use App\Models\User;
use App\Services\PermissionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReactCadastrosController extends Controller
{
    /**
     * GET /api/cadastros/company/active
     * Retorna dados da empresa ativa na sessão para edição no React.
     */
    public function activeCompany(): JsonResponse
    {
        $user = Auth::user();
        $activeCompanyId = session('active_company_id');

        if (! $activeCompanyId) {
            return response()->json(['message' => 'Nenhuma empresa ativa na sessão.'], 422);
        }

        $company = $user->companies()
            ->with(['addresses', 'horariosMissas'])
            ->findOrFail($activeCompanyId);

        $path = $company->avatar ? ltrim($company->avatar, '/') : null;
        if ($path && str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        // Horários de missas agrupados por dia da semana: ['domingo' => ['08:00','10:00'], ...]
        $horariosMissas = $company->horariosMissas;
        $intervaloMinutos = $horariosMissas->first()?->intervalo ?? 90;
        $intervaloFormatado = sprintf('%02d:%02d', intdiv((int) $intervaloMinutos, 60), (int) $intervaloMinutos % 60);

        $horariosPorDia = $horariosMissas
            ->groupBy('dia_semana')
            ->map(fn ($itens) => $itens->map(fn ($h) => substr((string) $h->horario, 0, 5))->values()->all())
            ->all();

        return response()->json([
            'data' => [
                'id' => $company->id,
                'name' => $company->name,
                'razao_social' => $company->razao_social,
                'cnpj' => $company->cnpj,
                'email' => $company->email,
                'details' => $company->details,
                'status' => $company->status,
                'avatar_url' => $path ? '/file/' . $path : null,
                'address' => [
                    'cep' => $company->addresses?->cep,
                    'rua' => $company->addresses?->rua,
                    'numero' => $company->addresses?->numero,
                    'bairro' => $company->addresses?->bairro,
                    'cidade' => $company->addresses?->cidade,
                    'uf' => $company->addresses?->uf,
                ],
                'intervalo_padrao' => $intervaloFormatado,
                'horarios_missas' => $horariosPorDia,
            ],
        ]);
    }

    /**
     * PUT /api/cadastros/company/active
     * Atualiza dados básicos da empresa ativa. Alteração de status apenas admin.
     */
    public function updateActiveCompany(Request $request): JsonResponse
    {
        $user = Auth::user();
        $activeCompanyId = session('active_company_id');

        if (! $activeCompanyId) {
            return response()->json(['message' => 'Nenhuma empresa ativa na sessão.'], 422);
        }

        $company = $user->companies()->findOrFail($activeCompanyId);

        if ($request->has('status') && ! $user->hasRole('admin')) {
            return response()->json(['message' => 'Apenas administradores podem alterar o status da empresa.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:18', Rule::unique('companies', 'cnpj')->ignore($company->id)],
            'email' => ['nullable', 'email', Rule::unique('companies', 'email')->ignore($company->id)],
            'details' => ['nullable', 'string'],
            'cep' => ['nullable', 'string', 'max:10'],
            'rua' => ['nullable', 'string', 'max:255'],
            'numero' => ['nullable', 'string', 'max:20'],
            'bairro' => ['nullable', 'string', 'max:255'],
            'cidade' => ['nullable', 'string', 'max:255'],
            'uf' => ['nullable', 'string', 'max:2'],
            'status' => ['nullable', 'in:active,inactive'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'avatar_remove' => ['nullable', 'in:0,1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $company->fill([
            'name' => $data['name'],
            'razao_social' => $data['razao_social'] ?? null,
            'cnpj' => $data['cnpj'],
            'email' => $data['email'] ?? null,
            'details' => $data['details'] ?? null,
        ]);

        if (array_key_exists('status', $data) && $user->hasRole('admin')) {
            $company->status = $data['status'];
        }

        if ($request->hasFile('avatar')) {
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }
            $company->avatar = $request->file('avatar')->store('brasao', 'public');
        } elseif (($data['avatar_remove'] ?? '0') === '1') {
            if ($company->avatar) {
                Storage::disk('public')->delete($company->avatar);
            }
            $company->avatar = null;
        }

        $company->save();

        Address::updateOrCreate(
            ['company_id' => $company->id],
            [
                'cep' => $data['cep'] ?? null,
                'rua' => $data['rua'] ?? null,
                'numero' => $data['numero'] ?? null,
                'bairro' => $data['bairro'] ?? null,
                'cidade' => $data['cidade'] ?? null,
                'uf' => $data['uf'] ?? null,
            ]
        );

        $company->load('addresses');

        $path = $company->avatar ? ltrim($company->avatar, '/') : null;
        if ($path && str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Organismo atualizado com sucesso.',
            'data' => [
                'id' => $company->id,
                'name' => $company->name,
                'razao_social' => $company->razao_social,
                'cnpj' => $company->cnpj,
                'email' => $company->email,
                'details' => $company->details,
                'status' => $company->status,
                'avatar_url' => $path ? '/file/' . $path : null,
                'address' => [
                    'cep' => $company->addresses?->cep,
                    'rua' => $company->addresses?->rua,
                    'numero' => $company->addresses?->numero,
                    'bairro' => $company->addresses?->bairro,
                    'cidade' => $company->addresses?->cidade,
                    'uf' => $company->addresses?->uf,
                ],
            ],
        ]);
    }

    /**
     * GET /app/cadastros/usuarios
     * Lista paginada de usuários da empresa ativa, com busca e ordenação.
     */
    public function usuarios(Request $request): JsonResponse
    {
        $companyId = session('active_company_id');

        $activeCompany = $companyId ? Company::find($companyId) : null;
        $isMatriz = $activeCompany && strtolower((string) $activeCompany->type) === 'matriz';

        $query = User::with('roles', 'companies');

        if (! $isMatriz) {
            $query->whereHas('companies', fn ($q) => $q->where('companies.id', $companyId));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $allowed = ['name', 'email', 'created_at', 'last_login', 'active'];
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'name';
        }

        $query->orderBy($sortBy, $sortDir);

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->paginate($perPage);

        $data = $paginated->getCollection()->map(function (User $user) {
            return [
                'id'                   => $user->id,
                'name'                 => $user->name,
                'email'                => $user->email,
                'avatar_url'           => $user->avatar_url,
                'active'               => (bool) $user->active,
                'last_login_formatted' => $user->last_login
                    ? Carbon::parse($user->last_login)->diffForHumans()
                    : 'Nunca',
                'created_at_formatted' => $user->created_at
                    ? $user->created_at->translatedFormat('d/m/Y')
                    : '—',
                'roles'     => $user->roles->map(fn ($r) => ['name' => $r->name])->values(),
                'companies' => $user->companies->map(function ($c) {
                    $avatarUrl = null;
                    if ($c->avatar) {
                        $path = ltrim($c->avatar, '/');
                        if (str_starts_with($path, 'public/')) {
                            $path = substr($path, strlen('public/'));
                        }
                        $avatarUrl = '/file/' . $path;
                    }
                    return ['id' => $c->id, 'name' => $c->name, 'avatar_url' => $avatarUrl];
                })->values(),
            ];
        });

        return response()->json([
            'data'         => $data,
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
        ]);
    }

    /**
     * GET /api/cadastros/companies
     * Lista todos os organismos disponíveis para seleção.
     */
    public function allCompanies(): JsonResponse
    {
        $companies = Company::with('addresses')->orderBy('name')->get();

        $data = $companies->map(function (Company $c) {
            $address = $c->addresses?->first();

            $avatarUrl = null;
            if ($c->avatar) {
                $path = ltrim($c->avatar, '/');
                if (str_starts_with($path, 'public/')) {
                    $path = substr($path, strlen('public/'));
                }
                $avatarUrl = '/file/' . $path;
            }

            return [
                'id'         => $c->id,
                'name'       => $c->name,
                'avatar_url' => $avatarUrl,
                'city'       => $address?->city,
                'state'      => $address?->state,
            ];
        });

        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/cadastros/usuarios/{user}
     * Retorna dados de um usuário específico para o formulário React.
     */
    public function showUsuario(User $user): JsonResponse
    {
        $user->load('companies', 'permissions', 'roles.permissions');

        return response()->json([
            'id'                    => $user->id,
            'name'                  => $user->name,
            'email'                 => $user->email,
            'avatar_url'            => $user->avatar_url,
            'active'                => (bool) $user->active,
            'force_password_change' => (bool) $user->must_change_password,
            'company_ids'           => $user->companies->pluck('id')->values(),
            'permission_ids'        => $user->getAllPermissions()->pluck('id')->values(),
        ]);
    }

    /**
     * GET /api/cadastros/permissions
     * Retorna todas as permissões agrupadas por módulo com metadados.
     */
    public function permissions(): JsonResponse
    {
        $service     = new PermissionService();
        $grouped     = $service->getPermissionsByModule();
        $moduleNames = $service->getModuleNames();
        $actionNames = $service->getActionNames();

        // Cores por ação (mapeadas para variantes Tailwind usadas no front)
        $actionColors = [
            'index'  => 'blue',
            'show'   => 'blue',
            'create' => 'green',
            'store'  => 'green',
            'edit'   => 'amber',
            'update' => 'amber',
            'delete' => 'red',
            'import' => 'purple',
            'export' => 'purple',
        ];

        // Ícones dos módulos
        $moduleIconMap = $this->buildModuleIcons();

        $modules = [];
        foreach ($grouped as $key => $permissions) {
            $perms = array_map(function ($p) use ($actionNames, $actionColors) {
                $parts  = explode('.', $p->name);
                $action = end($parts);
                return [
                    'id'           => $p->id,
                    'name'         => $p->name,
                    'action'       => $action,
                    'action_label' => $actionNames[$action] ?? ucfirst($action),
                    'color'        => $actionColors[$action] ?? 'gray',
                ];
            }, $permissions);

            $modules[] = [
                'key'      => $key,
                'name'     => $moduleNames[$key] ?? ucfirst($key),
                'icon_url' => $moduleIconMap[$key] ?? null,
                'permissions' => $perms,
            ];
        }

        return response()->json(['modules' => $modules]);
    }

    /**
     * Constrói mapa de ícones de módulos (mesmo padrão do UserController).
     */
    private function buildModuleIcons(): array
    {
        $fallback = [
            'users'      => '/tenancy/assets/media/png/perfil.svg',
            'notafiscal' => '/tenancy/assets/media/png/nfe.svg',
            'company'    => '/tenancy/assets/media/png/building.svg',
        ];

        $icons = $fallback;
        $modules = Module::where('is_active', true)->get();

        foreach ($modules as $module) {
            if (! $module->icon_path) continue;
            if (str_starts_with($module->icon_path, '/assets') || str_starts_with($module->icon_path, '/')) {
                $icons[$module->key] = $module->icon_path;
            } else {
                $icons[$module->key] = Storage::url($module->icon_path);
            }
        }

        return $icons;
    }
}
