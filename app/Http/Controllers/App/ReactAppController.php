<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\EntidadeFinanceira;
use App\Models\Financeiro\CostCenter;
use App\Models\FormasPagamento;
use App\Models\LancamentoPadrao;
use App\Models\HorarioMissa;
use App\Models\Module;
use App\Models\Parceiro;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactAppController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Mesmo fluxo do Dashboard: sessão sem empresa ativa → primeira empresa do usuário
        $activeCompanyId = session('active_company_id');
        $company = $user->companies()->find($activeCompanyId);

        if (! $company && $user->companies()->exists()) {
            $company = $user->companies()->first();
            session(['active_company_id' => $company->id]);
        }

        if (! $company) {
            abort(403, 'Nenhuma empresa associada a este usuário.');
        }

        $companyId = $company->id;

        $modules = app(ModuleService::class)
            ->getDashboardModules($user, $companyId)
            ->map(fn ($m) => [
                'name'        => $m->name,
                'description' => $m->description,
                'key'         => $m->key,
                'url'         => $m->route_name ? static::resolveRouteUrl($m->route_name) : null,
                'icon'        => $m->icon_path ? static::resolveIconUrl($m->icon_path) : null,
                'icon_class'  => $m->icon_class,
            ])
            ->values();

        // Todas as empresas às quais o usuário tem acesso (espelha $allCompanies do userMenu.blade.php)
        $allCompanies = $user->companies()
            ->with(['addresses:company_id,cep,rua,bairro,numero,cidade,uf'])
            ->get([
                'companies.id',
                'companies.name',
                'companies.razao_social',
                'companies.cnpj',
                'companies.email',
                'companies.avatar',
                'companies.type',
                'companies.parent_id',
            ])
            ->map(fn ($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'razao_social'=> $c->razao_social ?: null,
            'cnpj'        => $c->cnpj,
            'email'       => $c->email,
            'type'        => $c->type,
            'parent_id'   => $c->parent_id,
            'avatar_url'  => $c->avatar ? '/file/' . ltrim(preg_replace('#^public/#', '', $c->avatar), '/') : null,
            'address'      => $c->addresses ? [
                'cep'    => $c->addresses->cep,
                'rua'    => $c->addresses->rua,
                'bairro' => $c->addresses->bairro,
                'numero' => $c->addresses->numero,
                'cidade' => $c->addresses->cidade,
                'uf'     => $c->addresses->uf,
            ] : null,
        ])->values();

        $appData = [
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'avatar_url' => $user->avatar_url,
            ],
            'companyId'         => $companyId,
            'companies'         => $allCompanies,
            'csrfToken'         => csrf_token(),
            'logoutUrl'         => route('react.logout'),
            'baseUrl'           => '/app/',
            'modules'           => $modules,
            'formSelectData'    => $this->loadFormSelectData($companyId),
            'hasHorariosMissa'  => HorarioMissa::where('company_id', $companyId)->exists(),
            'hasAdminRole'      => $user->hasRole('admin'),
            'hasGlobalRole'     => $user->hasRole('global'),
            'canUsersIndex'         => $user->can('users.index'),
            'canCompanyIndex'       => $user->can('company.index'),
            'canContabilidadeIndex' => $user->can('contabilidade.index'),
            'canFinanceiroIndex'    => $user->can('financeiro.index'),
            'canNotafiscalIndex'    => $user->can('notafiscal.index'),
            'canSecretaryIndex'     => $user->can('secretary.index'),
            'canFieisIndex'         => $user->can('fieis.index'),
            'canDizimosIndex'       => self::userCanAccessModuleKey($user, 'dizimos'),
            'canDizimosCreate'      => self::userCanModuleAction($user, 'dizimos', 'create'),
            'canDizimosEdit'        => self::userCanModuleAction($user, 'dizimos', 'edit'),
            'canDizimosDelete'      => self::userCanModuleAction($user, 'dizimos', 'delete'),
        ];

        return view('react-app', compact('appData'));
    }

    /**
     * Alinha com Module::userHasPermission (ex.: role global vê o módulo no dashboard
     * mesmo sem permissão Spatie explícita).
     */
    private static function userCanAccessModuleKey(\App\Models\User $user, string $moduleKey): bool
    {
        if ($user->hasRole('global') || $user->hasRole('admin') || $user->hasRole('admin_user')) {
            return true;
        }

        $module = Module::query()->where('key', $moduleKey)->first();

        if ($module) {
            return $module->userHasPermission($user);
        }

        return $user->can("{$moduleKey}.index");
    }

    /**
     * Permissão granular por ação dentro de um módulo (create/edit/delete/...).
     * Mantém o mesmo bypass de roles administrativas usado em userCanAccessModuleKey.
     */
    private static function userCanModuleAction(\App\Models\User $user, string $moduleKey, string $action): bool
    {
        if ($user->hasRole('global') || $user->hasRole('admin') || $user->hasRole('admin_user')) {
            return true;
        }
        return $user->can("{$moduleKey}.{$action}");
    }

    private static function resolveRouteUrl(string $routeName): ?string
    {
        try {
            return route($routeName);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function resolveIconUrl(string $iconPath): string
    {
        // Já é uma URL absoluta (ex: /tenancy/assets/media/png/financeiro.svg)
        if (str_starts_with($iconPath, '/')) {
            return $iconPath;
        }

        // Arquivo em storage do tenant (ex: modules/icons/foto.png ou public/modules/icons/foto.png)
        $relativePath = ltrim(preg_replace('#^public/#', '', $iconPath), '/');

        return '/file/' . $relativePath;
    }

    private function loadFormSelectData(?int $companyId): array
    {
        if (! $companyId) {
            return ['parceiros' => [], 'entidades' => [], 'categorias' => [], 'centrosCusto' => [], 'formasPagamento' => [], 'filiais' => []];
        }

        $parceiros = collect();
        try {
            $parceiros = Parceiro::where('company_id', $companyId)
                ->orderBy('nome')
                ->get(['id', 'nome', 'natureza'])
                ->map(fn ($p) => ['id' => (string) $p->id, 'nome' => $p->nome, 'natureza' => $p->natureza]);
        } catch (\Throwable) {
        }

        $entidades = collect();
        try {
            $entidadesCol = EntidadeFinanceira::where('company_id', $companyId)
                ->with('bank:id,name,logo_path')
                ->orderBy('nome')
                ->get();
            $saldos = EntidadeFinanceira::saldosCalculadosPorEntidadeIds($entidadesCol->pluck('id')->all());
            $entidades = $entidadesCol->map(fn ($e) => [
                'id'           => (string) $e->id,
                'nome'         => $e->nome,
                'label'        => $e->tipo === 'banco'
                    ? (($e->agencia ? $e->agencia . ' - ' : '') . ($e->conta ?? $e->nome))
                    : $e->nome,
                'tipo'         => $e->tipo,
                'account_type' => $e->tipo === 'banco' ? ($e->account_type ?? null) : null,
                'logo'         => $e->bank?->logo_path
                               ? '/tenancy/assets/media/svg/bancos/' . $e->bank->logo_path
                               : null,
                'saldo_atual'  => round((float) ($saldos[$e->id] ?? 0), 2),
            ]);
        } catch (\Throwable) {
        }

        $categorias = collect();
        try {
            $categorias = LancamentoPadrao::forActiveCompany($companyId ? (int) $companyId : null)
                ->with('companies:id')
                ->orderBy('description')
                ->get(['id', 'codigo', 'description', 'type'])
                ->map(fn ($c) => [
                    'id'          => (string) $c->id,
                    'codigo'      => $c->codigo ?? null,
                    'description' => $c->description,
                    'type'        => $c->type,
                    'scope'       => $c->classificacaoParaCompany($companyId ? (int) $companyId : null),
                    'company_ids' => $c->companies->pluck('id')->map(fn ($v) => (string) $v)->values()->all(),
                ]);
        } catch (\Throwable) {
        }

        $centrosCusto = collect();
        try {
            $centrosCusto = CostCenter::where('company_id', $companyId)
                ->where('status', 1)
                ->orderBy('code')
                ->get(['id', 'code', 'name'])
                ->map(fn ($c) => ['id' => (string) $c->id, 'code' => $c->code, 'name' => $c->name]);
        } catch (\Throwable) {
        }

        $formasPagamento = collect();
        try {
            $formasPagamento = FormasPagamento::where('ativo', true)
                ->orderBy('nome')
                ->get(['id', 'codigo', 'nome'])
                ->map(fn ($f) => ['id' => (string) $f->id, 'codigo' => $f->codigo, 'nome' => $f->nome]);
        } catch (\Throwable) {
        }

        $filiais = collect();
        try {
            $filiais = Company::where('id', $companyId)
                ->orWhere('parent_id', $companyId)
                ->select('id', 'name', 'type', 'avatar')
                ->orderBy('name')
                ->get()
                ->map(fn ($c) => [
                    'id'         => (string) $c->id,
                    'name'       => $c->name,
                    'type'       => $c->type,
                    'avatar_url' => $c->avatar ? '/file/' . ltrim(preg_replace('#^public/#', '', $c->avatar), '/') : null,
                ]);
        } catch (\Throwable) {
        }

        return [
            'parceiros'       => $parceiros->values(),
            'entidades'       => $entidades->values(),
            'categorias'      => $categorias->values(),
            'centrosCusto'    => $centrosCusto->values(),
            'formasPagamento' => $formasPagamento->values(),
            'filiais'         => $filiais->values(),
        ];
    }
}
