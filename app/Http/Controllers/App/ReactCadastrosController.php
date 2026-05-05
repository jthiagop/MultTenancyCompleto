<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Controllers\App\SecretaryController;
use App\Models\Address;
use App\Models\Company;
use App\Models\FormationStage;
use App\Models\Module;
use App\Models\Province;
use App\Models\ReligiousMember;
use App\Models\ReligiousRole;
use App\Models\User;
use App\Services\PermissionService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * GET /api/cadastros/organismos
     * Lista paginada de organismos (companies) para a página de administração — React.
     *
     * Suporta: search, sort_by/sort_dir, page/per_page.
     * Retorna metadata (status, tipo, endereço resumido, avatares dos top 5 usuários, total de usuários).
     */
    public function organismos(Request $request): JsonResponse
    {
        $query = Company::query()
            ->with([
                'addresses',
                'users' => fn ($q) => $q->select('users.id', 'users.name', 'users.avatar'),
            ])
            ->withCount('users');

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('razao_social', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowed = ['name', 'email', 'type', 'status', 'created_at', 'users_count'];
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'name';
        }
        $query->orderBy($sortBy, $sortDir);

        $perPage = min((int) $request->input('per_page', 20), 100);
        $paginated = $query->paginate($perPage);

        $avatarUrl = static function (?string $avatar): ?string {
            if (! $avatar) {
                return null;
            }
            $path = ltrim($avatar, '/');
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, strlen('public/'));
            }
            return '/file/' . $path;
        };

        $data = $paginated->getCollection()->map(function (Company $c) use ($avatarUrl) {
            $addr = $c->addresses;
            $addrLine = null;
            if ($addr) {
                $parts = array_filter([
                    trim((string) ($addr->rua ?? '')) !== '' ? $addr->rua : null,
                    trim((string) ($addr->cidade ?? '')) !== '' ? $addr->cidade : null,
                    trim((string) ($addr->uf ?? '')) !== '' ? $addr->uf : null,
                ]);
                $addrLine = count($parts) ? implode(', ', $parts) : null;
            }

            return [
                'id'                   => (int) $c->id,
                'name'                 => (string) $c->name,
                'razao_social'         => $c->razao_social,
                'cnpj'                 => $c->cnpj,
                'email'                => $c->email,
                'type'                 => $c->type,
                'status'               => $c->status,
                'avatar_url'           => $avatarUrl($c->avatar),
                'address_line'         => $addrLine,
                'cidade'               => $addr?->cidade,
                'uf'                   => $addr?->uf,
                'users_count'          => (int) ($c->users_count ?? 0),
                'users_preview'        => $c->users->take(5)->map(fn (User $u) => [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'avatar_url' => $avatarUrl($u->avatar),
                ])->values(),
                'created_at_formatted' => $c->created_at
                    ? $c->created_at->translatedFormat('d/m/Y')
                    : '—',
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
     * GET /api/cadastros/organismos/{company}
     * Retorna dados detalhados de um organismo para edição no Sheet React.
     */
    public function showOrganismo(Company $company): JsonResponse
    {
        $company->load('addresses');
        $addr = $company->addresses;

        $avatarUrl = null;
        if ($company->avatar) {
            $path = ltrim($company->avatar, '/');
            if (str_starts_with($path, 'public/')) {
                $path = substr($path, strlen('public/'));
            }
            $avatarUrl = '/file/' . $path;
        }

        return response()->json([
            'data' => [
                'id'           => (int) $company->id,
                'name'         => $company->name,
                'razao_social' => $company->razao_social,
                'cnpj'         => $company->cnpj,
                'email'        => $company->email,
                'status'       => $company->status,
                'type'         => $company->type,
                'avatar_url'   => $avatarUrl,
                'address' => [
                    'cep'         => $addr?->cep,
                    'rua'         => $addr?->rua,
                    'numero'      => $addr?->numero,
                    'complemento' => $addr?->complemento,
                    'bairro'      => $addr?->bairro,
                    'cidade'      => $addr?->cidade,
                    'uf'          => $addr?->uf,
                ],
            ],
        ]);
    }

    /**
     * POST /api/cadastros/organismos
     * Cria um novo organismo (company) + endereço opcional.
     */
    public function storeOrganismo(Request $request): JsonResponse
    {
        $data = $this->validateOrganismo($request);

        $company = Company::create([
            'name'         => $data['name'],
            'razao_social' => $data['razao_social'] ?? null,
            'cnpj'         => $data['cnpj'],
            'email'        => $data['email'] ?? null,
            'type'         => $data['type']   ?? 'filial',
            'status'       => $data['status'] ?? 'active',
            'created_by'   => Auth::id(),
        ]);

        if ($request->hasFile('avatar')) {
            $company->update(['avatar' => Storage::put('perfis', $request->file('avatar'))]);
        }

        $this->upsertAddress($company, $data);

        return response()->json([
            'success' => true,
            'message' => 'Organismo cadastrado com sucesso.',
            'data'    => ['id' => $company->id],
        ], 201);
    }

    /**
     * PUT /api/cadastros/organismos/{company}
     * Atualiza um organismo existente e seu endereço.
     */
    public function updateOrganismo(Request $request, Company $company): JsonResponse
    {
        $data = $this->validateOrganismo($request, $company->id);

        $fillData = [
            'name'         => $data['name'],
            'razao_social' => $data['razao_social'] ?? null,
            'cnpj'         => $data['cnpj'],
            'email'        => $data['email'] ?? null,
            'updated_by'   => Auth::id(),
        ];

        // Status / Tipo — sempre atualiza se chegou no request (mesmo que já venha igual ao BD).
        // Pega direto do request para não depender de "nullable" do validator engolir o valor.
        $rawStatus = $request->input('status');
        $rawType   = $request->input('type');
        if (is_string($rawStatus) && in_array($rawStatus, ['active', 'inactive'], true)) {
            $fillData['status'] = $rawStatus;
        }
        if (is_string($rawType) && in_array($rawType, ['matriz', 'filial'], true)) {
            $fillData['type'] = $rawType;
        }

        $company->fill($fillData);

        if ($request->hasFile('avatar')) {
            $company->avatar = Storage::put('perfis', $request->file('avatar'));
        }

        $saved  = $company->save();
        $dirty  = $company->getChanges();

        if (app()->isLocal() || config('app.debug')) {
            Log::info('updateOrganismo', [
                'company_id' => $company->id,
                'received'   => array_intersect_key(
                    $request->only(['name', 'cnpj', 'status', 'type']),
                    array_flip(['name', 'cnpj', 'status', 'type']),
                ),
                'fill_data'  => $fillData,
                'changes'    => $dirty,
                'saved'      => $saved,
            ]);
        }

        $this->upsertAddress($company, $data);

        return response()->json([
            'success' => true,
            'message' => 'Organismo atualizado com sucesso.',
            'data'    => ['id' => $company->id],
        ]);
    }

    /**
     * Regras de validação compartilhadas entre store/update de organismo.
     *
     * @return array<string, mixed>
     */
    protected function validateOrganismo(Request $request, ?int $ignoreId = null): array
    {
        $cnpjRule = Rule::unique('companies', 'cnpj');
        if ($ignoreId) {
            $cnpjRule = $cnpjRule->ignore($ignoreId);
        }

        $validator = Validator::make($request->all(), [
            'name'         => ['required', 'string', 'max:255'],
            'razao_social' => ['nullable', 'string', 'max:255'],
            'cnpj'         => ['required', 'string', 'max:20', $cnpjRule],
            'email'        => ['nullable', 'email', 'max:255'],
            'status'       => ['nullable', 'in:active,inactive'],
            'type'         => ['nullable', 'in:matriz,filial'],
            'avatar'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // endereço (todos opcionais; o campo "rua" é o mais visível no Sheet)
            'cep'          => ['nullable', 'string', 'max:20'],
            'rua'          => ['nullable', 'string', 'max:255'],
            'numero'       => ['nullable', 'string', 'max:20'],
            'complemento'  => ['nullable', 'string', 'max:255'],
            'bairro'       => ['nullable', 'string', 'max:120'],
            'cidade'       => ['nullable', 'string', 'max:120'],
            'uf'           => ['nullable', 'string', 'size:2'],
        ], [
            'cnpj.unique' => 'Este CNPJ já está cadastrado em outro organismo.',
            'name.required' => 'O nome do organismo é obrigatório.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'avatar.image'  => 'O logo deve ser uma imagem.',
            'avatar.mimes'  => 'O logo deve estar em jpg, jpeg, png ou webp.',
            'avatar.max'    => 'O logo deve ter no máximo 2 MB.',
        ]);

        return $validator->validate();
    }

    /**
     * Cria ou atualiza o endereço vinculado ao organismo.
     *
     * @param array<string, mixed> $data
     */
    protected function upsertAddress(Company $company, array $data): void
    {
        $uf = $data['uf'] ?? null;
        $addressFields = array_filter([
            'cep'         => $data['cep']         ?? null,
            'rua'         => $data['rua']         ?? null,
            'numero'      => $data['numero']      ?? null,
            'complemento' => $data['complemento'] ?? null,
            'bairro'      => $data['bairro']      ?? null,
            'cidade'      => $data['cidade']      ?? null,
            'uf'          => $uf ? strtoupper((string) $uf) : null,
        ], fn ($v) => $v !== null && $v !== '');

        if (empty($addressFields)) {
            return;
        }

        Address::updateOrCreate(
            ['company_id' => $company->id],
            $addressFields,
        );
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

        // permissões diretas = o que syncPermissions() altera no banco
        // via role = herdadas do cargo (só leitura neste formulário; desmarcar no UI
        // não remove se ainda existirem no role — evitava parecer "não salva")
        $directIds = $user->getDirectPermissions()->pluck('id')->values();
        $roleIds   = $user->getPermissionsViaRoles()->pluck('id')->unique()->values();

        return response()->json([
            'id'                    => $user->id,
            'name'                  => $user->name,
            'email'                 => $user->email,
            'avatar_url'            => $user->avatar_url,
            'active'                => (bool) $user->active,
            'force_password_change' => (bool) $user->must_change_password,
            'company_ids'           => $user->companies->pluck('id')->values(),
            'permission_ids'        => $directIds,
            'role_permission_ids'   => $roleIds,
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
     * GET /api/secretary/membros
     * Lista paginada de membros religiosos para o React, com busca, ordenação e filtro de função.
     */
    public function membros(Request $request): JsonResponse
    {
        $query = ReligiousMember::with([
            'province',
            'role',
            'currentStage',
            'currentFormationPeriod.company',
        ])->where('is_active', true);

        // Filtro por role_slug (aba ativa)
        if ($roleSlug = $request->input('role_slug')) {
            $query->whereHas('role', fn ($q) => $q->where('slug', $roleSlug));
        }

        // Filtro votos simples (profissão temporária, sem perpétua)
        if ($request->input('profession') === 'temporaria') {
            $query->whereNotNull('temporary_profession_date')
                  ->whereNull('perpetual_profession_date');
        }

        // Busca textual
        if ($search = trim((string) $request->input('search', ''))) {
            $query->where('name', 'like', "%{$search}%");
        }

        // Ordenação
        $sortBy  = $request->input('sort_by', 'name');
        $sortDir = $request->input('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $allowed = ['name', 'created_at', 'birth_date', 'priestly_ordination_date',
                    'diaconal_ordination_date', 'temporary_profession_date'];
        if (! in_array($sortBy, $allowed, true)) {
            $sortBy = 'name';
        }
        $query->orderBy($sortBy, $sortDir);

        $perPage   = min((int) $request->input('per_page', 25), 100);
        $paginated = $query->paginate($perPage);

        $data = $paginated->getCollection()->map(function (ReligiousMember $member) {
            // Avatar
            $avatarUrl = null;
            if ($member->avatar) {
                $path = ltrim($member->avatar, '/');
                if (str_starts_with($path, 'public/')) {
                    $path = substr($path, strlen('public/'));
                }
                $avatarUrl = '/file/' . $path;
            }

            // Data-chave baseada na função
            $dataChave = null;
            if ($member->role) {
                $dataChave = match ($member->role->slug) {
                    'presbitero' => $member->priestly_ordination_date?->format('d/m/Y'),
                    'diacono'    => $member->diaconal_ordination_date?->format('d/m/Y'),
                    'irmao'      => ($member->perpetual_profession_date ?? $member->temporary_profession_date)
                                       ?->format('d/m/Y'),
                    default      => null,
                };
            }

            return [
                'id'               => $member->id,
                'name'             => $member->name,
                'avatar_url'       => $avatarUrl,
                'province'         => $member->province?->name,
                'role'             => $member->role ? [
                    'name' => $member->role->name,
                    'slug' => $member->role->slug,
                ] : null,
                'current_stage'    => $member->currentStage ? [
                    'id'   => $member->currentStage->id,
                    'name' => $member->currentStage->name,
                ] : null,
                'current_location' => $member->currentFormationPeriod?->company?->name,
                'data_chave'       => $dataChave,
                'is_active'        => (bool) $member->is_active,
            ];
        });

        // Stats para as abas (sempre sobre toda a base ativa)
        $baseQuery = ReligiousMember::where('is_active', true);
        $stats = [
            'todos'         => (clone $baseQuery)->count(),
            'presbiteros'   => (clone $baseQuery)->whereHas('role', fn ($q) => $q->where('slug', 'presbitero'))->count(),
            'diaconos'      => (clone $baseQuery)->whereHas('role', fn ($q) => $q->where('slug', 'diacono'))->count(),
            'irmaos'        => (clone $baseQuery)->whereHas('role', fn ($q) => $q->where('slug', 'irmao'))->count(),
            'votos_simples' => (clone $baseQuery)
                                ->whereNotNull('temporary_profession_date')
                                ->whereNull('perpetual_profession_date')
                                ->count(),
        ];

        return response()->json([
            'data'         => $data,
            'total'        => $paginated->total(),
            'per_page'     => $paginated->perPage(),
            'current_page' => $paginated->currentPage(),
            'last_page'    => $paginated->lastPage(),
            'stats'        => $stats,
        ]);
    }

    /**
     * Dados de lookup para o formulário de cadastro de membro da Secretaria.
     * Retorna etapas de formação, organismos e funções religiosas.
     */
    public function secretaryFormData(): JsonResponse
    {
        $formationStages = FormationStage::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'sort_order']);

        $companies = Company::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $religiousRoles = ReligiousRole::where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'formation_stages' => $formationStages,
            'companies'        => $companies,
            'religious_roles'  => $religiousRoles,
        ]);
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
