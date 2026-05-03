<?php

namespace App\Http\Controllers;

use App\Jobs\SeedTenantJob;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Symfony\Component\HttpFoundation\Response;

class TenantController extends Controller
{
    /**
     * Subdomínios reservados que não podem ser usados como `domain_name`.
     * Bloqueia colisão com central_domains, ferramentas internas e padrões
     * comuns que confundem usuários (`mail`, `webmail`, etc.).
     */
    private const RESERVED_DOMAINS = [
        'www', 'admin', 'api', 'app', 'mail', 'webmail', 'ftp', 'sftp',
        'cdn', 'assets', 'static', 'staging', 'test', 'tests', 'dev',
        'qa', 'demo', 'support', 'help', 'docs', 'blog', 'shop', 'store',
        'panel', 'painel', 'dashboard', 'companies', 'tenant', 'tenants',
        'central', 'root', 'system', 'sys',
    ];

    public function index()
    {
        $this->authorizeAdmin();

        $tenants = Tenant::with('domains')->get();

        return view('tenant.index', ['tenants' => $tenants]);
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('tenant.create');
    }

    /**
     * Cria um tenant + domínio + dispara seed.
     *
     * Fluxo defensivo:
     *   1. Validação rigorosa (regex, blacklist, lower-case automático)
     *   2. Normalização do domain_name
     *   3. Cria o tenant central — isso dispara o JobPipeline:
     *        Jobs\CreateDatabase → Jobs\MigrateDatabase → RobustTenantSetupJob
     *      Se qualquer parte falhar, capturamos a exceção e DELETAMOS o
     *      tenant órfão — `$tenant->delete()` aciona Jobs\DeleteDatabase
     *      e remove o tenant do banco central, deixando o sistema limpo.
     *   4. Cria o domínio. Se o passo 3 falhou, nunca chegamos aqui.
     *   5. Dispara seed (job assíncrono).
     *
     * Sem `DB::transaction` no banco central porque o registro do tenant
     * precisa estar disponível para o pipeline (CreateDatabase/MigrateDatabase
     * abrem conexões nomeadas que dependem do tenant). O rollback em caso
     * de falha é manual via `$tenant->delete()`.
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $domainNameRaw = (string) $request->input('domain_name', '');
        $domainNameNormalized = strtolower(trim($domainNameRaw));
        $request->merge(['domain_name' => $domainNameNormalized]);

        $validated = $request->validate([
            // `name` representa o nome da EMPRESA/organização (vira Company.name
            // dentro do tenant). É também o display name do tenant central.
            'name'        => ['required', 'string', 'max:255'],
            // Nome do ADMINISTRADOR inicial (vira User.name dentro do tenant).
            // Separado de `name` porque pessoa ≠ empresa. Opcional: se não
            // informado, caímos no nome da empresa para preservar o
            // comportamento legado.
            'user_name'   => ['nullable', 'string', 'max:255'],
            'email'       => ['required', 'email:rfc', 'max:255', 'unique:tenants,email'],
            'domain_name' => [
                'required',
                'string',
                'min:3',
                'max:63', // limite RFC para um label DNS
                'regex:/^[a-z0-9]([a-z0-9-]*[a-z0-9])?$/',
                Rule::notIn(self::RESERVED_DOMAINS),
            ],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'domain_name.regex'  => 'O domínio só pode conter letras minúsculas, números e hifens, e não pode começar nem terminar com hífen.',
            'domain_name.not_in' => 'Este nome de domínio é reservado pelo sistema. Escolha outro.',
        ]);

        $userName = trim((string) ($validated['user_name'] ?? '')) ?: $validated['name'];

        $fullDomain = $validated['domain_name'] . '.' . config('app.domain');

        // Pré-checagem: o domínio completo precisa ser único na tabela `domains`.
        // Sem isso o erro de duplicidade só apareceria depois do tenant ser criado.
        $domainExists = DB::table('domains')->where('domain', $fullDomain)->exists();
        if ($domainExists) {
            return $this->validationFailure(
                $request,
                ['domain_name' => 'Já existe um tenant usando este domínio.'],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $tenant = null;

        // Preserva o comportamento histórico de usar `domain_name` como
        // suffix do DB do tenant (legado). Salvamos o valor original e
        // RESTAURAMOS no finally, para que a alteração não vaze para
        // requests subsequentes processados pelo mesmo worker (PHP-FPM
        // /Octane). Sem isso, o suffix configurado dinamicamente
        // continuaria valendo no resto do ciclo de vida do worker.
        $originalSuffix = config('tenancy.database.suffix', '');

        try {
            config(['tenancy.database.suffix' => $validated['domain_name']]);

            // Cria o tenant — isso DISPARA o pipeline (criar DB + migrar).
            // Qualquer exceção aqui é da pipeline e queremos limpar.
            // `user_name` é guardado no JSON `data` do tenant (Stancl
            // VirtualColumn) — fica acessível como `$tenant->user_name`
            // para os jobs de seed/setup que criam o usuário inicial.
            $tenant = Tenant::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => $validated['password'],
                'user_name' => $userName,
            ]);

            // Cria o domínio só DEPOIS que o pipeline rodou OK.
            $tenant->domains()->create([
                'domain' => $fullDomain,
            ]);

            // Seed em fila assíncrona (não bloqueia a resposta).
            SeedTenantJob::dispatch($tenant);

            Log::info('Tenant criado com sucesso', [
                'tenant_id' => $tenant->id,
                'domain'    => $fullDomain,
                'admin_id'  => $request->user()?->id,
            ]);

            return $this->successResponse($request, $tenant, $fullDomain);
        } catch (\Throwable $e) {
            // Rollback: se o tenant foi criado mas algo após falhou,
            // delete-o — Jobs\DeleteDatabase removerá o DB físico.
            if ($tenant !== null) {
                try {
                    $tenant->delete();
                    Log::warning('Tenant órfão removido após falha na criação', [
                        'tenant_id' => $tenant->id,
                    ]);
                } catch (\Throwable $cleanupError) {
                    Log::error('Falha ao remover tenant órfão', [
                        'tenant_id' => $tenant->id,
                        'error'     => $cleanupError->getMessage(),
                    ]);
                }
            }

            Log::error('Erro ao criar tenant', [
                'domain' => $fullDomain,
                'error'  => $e->getMessage(),
                'trace'  => Str::limit($e->getTraceAsString(), 2000),
            ]);

            return $this->errorResponse(
                $request,
                'Não foi possível criar o tenant: ' . $e->getMessage(),
            );
        } finally {
            // Restaura o suffix global — não vaza para próximos requests.
            config(['tenancy.database.suffix' => $originalSuffix]);
        }
    }

    public function show(Tenant $tenant)
    {
        $this->authorizeAdmin();

        return response()->json($tenant->load('domains'));
    }

    public function destroy(Tenant $tenant): JsonResponse|RedirectResponse
    {
        $this->authorizeAdmin();

        try {
            $tenant->delete();

            Log::info('Tenant removido', ['tenant_id' => $tenant->id]);

            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()
                ->route('tenants.index')
                ->with('success', 'Tenant removido com sucesso.');
        } catch (\Throwable $e) {
            Log::error('Erro ao remover tenant', [
                'tenant_id' => $tenant->id,
                'error'     => $e->getMessage(),
            ]);

            return $this->errorResponse(
                request(),
                'Erro ao remover tenant: ' . $e->getMessage(),
            );
        }
    }

    public function dashboard()
    {
        // placeholder mantido para compatibilidade
    }

    /**
     * Gera um código de acesso mobile para o tenant.
     */
    public function generateCode(?Tenant $tenant = null): JsonResponse
    {
        try {
            if (! $tenant) {
                $tenantId = tenant('id');
                if (! $tenantId) {
                    return response()->json(['error' => 'Tenant não encontrado'], 404);
                }

                $tenant = Tenant::find($tenantId);
                if (! $tenant) {
                    return response()->json(['error' => 'Tenant não encontrado'], 404);
                }
            }

            return response()->json(['code' => $tenant->generateAppCode()], 200);
        } catch (\Throwable $e) {
            Log::error('Erro ao gerar código de acesso mobile: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao gerar código: ' . $e->getMessage()], 500);
        }
    }

    // ── helpers ────────────────────────────────────────────────────────────

    /**
     * Apenas administradores podem gerenciar tenants no banco central.
     *
     * Estratégia em camadas (qualquer uma libera o acesso):
     *   1. Role 'admin' ou 'global' no Spatie Permission.
     *   2. E-mail listado em `CENTRAL_ADMIN_EMAILS` (CSV no .env) —
     *      útil para SREs sem precisar mexer em DB.
     *   3. Fallback de "instalação inicial": se o banco central tem
     *      exatamente UM usuário, ele é o super-admin de fato. Esse
     *      cenário cobre o primeiro acesso após instalar o sistema,
     *      antes de qualquer role ter sido seedada. Assim que um
     *      segundo usuário central for criado, esse fallback some
     *      automaticamente e os roles passam a ser obrigatórios.
     *
     * Sem essa proteção, qualquer usuário autenticado podia criar
     * tenant — risco crítico.
     */
    private function authorizeAdmin(): void
    {
        $user = request()->user();

        if (! $user) {
            abort(403, 'Não autenticado.');
        }

        // Camada 1: roles formais
        $hasRole = method_exists($user, 'hasRole')
            && ($user->hasRole('admin') || $user->hasRole('global'));
        if ($hasRole) {
            return;
        }

        // Camada 2: lista explícita de e-mails (env)
        $allowedEmails = collect(explode(',', (string) env('CENTRAL_ADMIN_EMAILS', '')))
            ->map(fn ($e) => trim(strtolower($e)))
            ->filter()
            ->all();
        if (! empty($allowedEmails) && in_array(strtolower((string) $user->email), $allowedEmails, true)) {
            return;
        }

        // Camada 3: fallback de instalação inicial (único usuário central)
        try {
            if (Tenant::query()->getConnection()->table('users')->count() === 1) {
                Log::info('TenantController: liberando acesso via fallback de instalação inicial', [
                    'user_id' => $user->id,
                ]);
                return;
            }
        } catch (\Throwable) {
            // Se a tabela `users` não existir no central por algum motivo,
            // ignora — caímos no abort abaixo.
        }

        abort(403, 'Apenas administradores podem gerenciar tenants.');
    }

    private function successResponse(Request $request, Tenant $tenant, string $fullDomain): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'tenant'  => [
                    'id'     => $tenant->id,
                    'name'   => $tenant->name,
                    'email'  => $tenant->email,
                    'domain' => $fullDomain,
                ],
            ], 201);
        }

        return redirect()
            ->route('tenants.index')
            ->with('success', "Tenant criado em {$fullDomain}");
    }

    private function errorResponse(Request $request, string $message): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return redirect()
            ->back()
            ->withInput($request->except(['password', 'password_confirmation']))
            ->with('error', $message);
    }

    private function validationFailure(Request $request, array $errors, int $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'errors'  => $errors,
            ], $status);
        }

        return redirect()
            ->back()
            ->withInput($request->except(['password', 'password_confirmation']))
            ->withErrors($errors);
    }
}
