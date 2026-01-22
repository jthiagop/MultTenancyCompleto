<?php

use App\Http\Middleware\Tenant\TenantFilesystems;
use App\Models\Tenant;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        using: function () {
            $centralDomains = config('tenancy.central_domains');
            
            // Rota de webhook WhatsApp - deve funcionar em qualquer domínio (ngrok, localhost, etc)
            // Registrada ANTES do loop de domínios para funcionar globalmente
            Route::withoutMiddleware([\Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class])
                ->match(['get', 'post'], '/webhooks/meta/whatsapp', [App\Http\Controllers\WhatsAppIntegrationController::class, 'webhook'])
                ->name('whatsapp.webhook');

            foreach ($centralDomains as $domain) {
                Route::middleware('web')
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));

            // Endpoint central para buscar tenant por código de acesso mobile
            Route::middleware(['api'])
                ->domain($domain)
                ->post('/api/tenant/by-code', function () {
                    $accessCode = request()->input('code');
                    $serverIP = request()->input('server_ip'); // IP do servidor (opcional, para desenvolvimento)

                    if (!$accessCode) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Parâmetro "code" é obrigatório'
                        ], 400);
                    }

                    \Log::info('Buscando tenant por código de acesso: ' . $accessCode . ($serverIP ? ' (IP: ' . $serverIP . ')' : ''));

                    try {
                        // Buscar tenant pelo código de acesso no banco central
                        $tenant = Tenant::where('app_access_code', $accessCode)->first();

                        if ($tenant) {
                            $domainObj = $tenant->domains->first();
                            $domainName = $domainObj->domain ?? null;

                            // Se foi fornecido IP do servidor, usar ele
                            if ($serverIP) {
                                $baseURL = 'http://' . $serverIP . ':8001';
                            } else {
                                // Detectar se a requisição veio de um IP (mobile app)
                                $requestHost = request()->getHost();
                                $requestIP = request()->ip();
                                $isIPRequest = filter_var($requestHost, FILTER_VALIDATE_IP) !== false;

                                // Se a requisição veio de um IP, usar o IP na URL base
                                if ($isIPRequest || filter_var($requestIP, FILTER_VALIDATE_IP)) {
                                    $detectedIP = $isIPRequest ? $requestHost : $requestIP;
                                    $baseURL = 'http://' . $detectedIP . ':8001';
                                } else {
                                    // Requisição web normal, usar o domínio completo
                                    $baseURL = 'http://' . $domainName;
                                    if (str_contains($domainName, 'localhost') || str_contains($domainName, '127.0.0.1')) {
                                        $baseURL .= ':8001';
                                    }
                                }
                            }

                            return response()->json([
                                'status' => 'ok',
                                'tenant' => $tenant->id,
                                'domain' => $domainName,
                                'base_url' => $baseURL,
                                'tenant_name' => $tenant->name,
                                'message' => 'Tenant encontrado'
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Erro ao buscar tenant por código: ' . $e->getMessage());
                    }

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Código de acesso inválido ou tenant não encontrado'
                    ], 404);
                })->name('api.tenant.by-code');

            // Endpoint central para verificar tenant (funciona em qualquer domínio central)
            Route::middleware(['api'])
                ->domain($domain)
                ->post('/api/tenant/verify', function () {
                        $requestedDomain = request()->input('domain');

                        if (!$requestedDomain) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Parâmetro "domain" é obrigatório'
                            ], 400);
                        }

                        \Log::info('Verificando tenant via endpoint central - Domain: ' . $requestedDomain);

                        try {
                            // Buscar por qualquer domínio que comece com o subdomínio
                            $tenant = Tenant::whereHas('domains', function ($query) use ($requestedDomain) {
                                $query->where('domain', 'LIKE', $requestedDomain . '.%')
                                      ->orWhere('domain', $requestedDomain);
                            })->first();

                            // Se ainda não encontrou, buscar todos os domínios
                            if (!$tenant) {
                                $allDomains = \App\Models\Domain::where('domain', 'LIKE', $requestedDomain . '.%')
                                    ->orWhere('domain', $requestedDomain)
                                    ->get();

                                foreach ($allDomains as $domainObj) {
                                    $domainParts = explode('.', $domainObj->domain);
                                    if (isset($domainParts[0]) && $domainParts[0] === $requestedDomain) {
                                        $tenant = Tenant::find($domainObj->tenant_id);
                                        if ($tenant) {
                                            break;
                                        }
                                    }
                                }
                            }

                            if ($tenant) {
                                $domainObj = $tenant->domains->first();
                                $domain = $domainObj->domain ?? ($requestedDomain . '.localhost');

                                // Detectar se a requisição veio de um IP (mobile app)
                                $requestHost = request()->getHost();
                                $requestIP = request()->ip();
                                $isIPRequest = filter_var($requestHost, FILTER_VALIDATE_IP) !== false;

                                // Se a requisição veio de um IP, usar o IP na URL base
                                if ($isIPRequest || filter_var($requestIP, FILTER_VALIDATE_IP)) {
                                    $serverIP = $isIPRequest ? $requestHost : $requestIP;
                                    // Usar apenas o IP e porta, sem subdomínio (o app vai passar o domínio como header)
                                    $baseURL = 'http://' . $serverIP . ':8001';
                                } else {
                                    // Requisição web normal, usar o domínio completo
                                    $baseURL = 'http://' . $domain;
                                    if (str_contains($domain, 'localhost') || str_contains($domain, '127.0.0.1')) {
                                        $baseURL .= ':8001';
                                    }
                                }

                                return response()->json([
                                    'status' => 'ok',
                                    'tenant' => $tenant->id,
                                    'domain' => $domain,
                                    'base_url' => $baseURL,
                                    'message' => 'Tenant encontrado e disponível'
                                ]);
                            }
                        } catch (\Exception $e) {
                            \Log::error('Erro ao verificar tenant via endpoint central: ' . $e->getMessage());
                        }

                        return response()->json([
                            'status' => 'error',
                            'message' => 'Tenant não encontrado'
                        ], 404);
                    })->name('api.tenant.verify.central');
            }

            Route::middleware('web')->middleware('tenant.filesystems')->group(base_path('routes/tenant.php'));

            // Rota pública de status (verifica se tenant existe sem inicializá-lo)
            Route::middleware(['api'])->get('/api/status', function () {
                $host = request()->getHost();
                $requestedDomain = request()->input('domain'); // Permite passar domínio como parâmetro

                \Log::info('Verificando tenant - Host: ' . $host . ', Requested: ' . ($requestedDomain ?? 'null'));

                // Verificar se o tenant existe no banco central
                try {
                    $tenant = null;

                    // PRIORIDADE 1: Se foi passado parâmetro domain, buscar por ele primeiro
                    // Isso é útil quando o app mobile passa apenas o subdomínio (ex: "proneb")
                    if ($requestedDomain) {
                        \Log::info('Buscando por parâmetro domain: ' . $requestedDomain);

                        // Buscar por qualquer domínio que comece com o subdomínio seguido de ponto
                        // Ex: "proneb" encontra "proneb.localhost", "proneb.dominusbr.com", etc.
                        $tenant = Tenant::whereHas('domains', function ($query) use ($requestedDomain) {
                            $query->where('domain', 'LIKE', $requestedDomain . '.%')
                                  ->orWhere('domain', $requestedDomain);
                        })->first();

                        // Se ainda não encontrou, buscar todos os domínios e verificar se algum começa com o subdomínio
                        if (!$tenant) {
                            $allDomains = \App\Models\Domain::where('domain', 'LIKE', $requestedDomain . '.%')
                                ->orWhere('domain', $requestedDomain)
                                ->get();

                            foreach ($allDomains as $domain) {
                                $domainParts = explode('.', $domain->domain);
                                if (isset($domainParts[0]) && $domainParts[0] === $requestedDomain) {
                                    $tenant = Tenant::find($domain->tenant_id);
                                    if ($tenant) {
                                        \Log::info('Tenant encontrado via busca ampla: ' . $tenant->id);
                                        break;
                                    }
                                }
                            }
                        }
                    }

                    // PRIORIDADE 2: Se não encontrou e não foi passado parâmetro, buscar pelo host
                    if (!$tenant) {
                        // Buscar pelo host completo primeiro (ex: proneb.dominusbr.com ou recife.localhost)
                        $tenant = Tenant::whereHas('domains', function ($query) use ($host) {
                            $query->where('domain', $host);
                        })->first();

                        // Se não encontrou, tentar extrair o subdomínio e buscar
                        if (!$tenant) {
                            $parts = explode('.', $host);
                            if (count($parts) > 0) {
                                $subdomain = $parts[0];

                                \Log::info('Buscando por subdomínio do host: ' . $subdomain);

                                $tenant = Tenant::whereHas('domains', function ($query) use ($subdomain) {
                                    $query->where('domain', 'LIKE', $subdomain . '.%')
                                          ->orWhere('domain', $subdomain);
                                })->first();
                            }
                        }
                    }

                    if ($tenant) {
                        $domain = $tenant->domains->first();
                        $domainName = $domain->domain ?? $host;

                        // Adicionar porta 8001 se for localhost
                        $baseURL = 'http://' . $domainName;
                        if (str_contains($domainName, 'localhost') || str_contains($domainName, '127.0.0.1')) {
                            $baseURL .= ':8001';
                        }

                        \Log::info('Tenant encontrado: ' . $tenant->id . ' - Domain: ' . $domainName);
                        return response()->json([
                            'status' => 'ok',
                            'tenant' => $tenant->id,
                            'domain' => $domainName,
                            'base_url' => $baseURL,
                            'message' => 'Tenant encontrado e disponível'
                        ]);
                    }

                    \Log::warning('Tenant não encontrado para host: ' . $host);
                } catch (\Exception $e) {
                    \Log::error('Erro ao verificar tenant: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
                }

                // Listar todos os domínios disponíveis para debug (apenas em desenvolvimento)
                $availableDomains = [];
                if (config('app.debug')) {
                    try {
                        $allDomains = \App\Models\Domain::select('domain', 'tenant_id')->get();
                        $availableDomains = $allDomains->map(function ($domain) {
                            $tenant = Tenant::find($domain->tenant_id);
                            return [
                                'domain' => $domain->domain,
                                'tenant_id' => $domain->tenant_id,
                                'tenant_name' => $tenant->name ?? null,
                            ];
                        })->toArray();
                    } catch (\Exception $e) {
                        \Log::error('Erro ao listar domínios para debug: ' . $e->getMessage());
                    }
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Tenant não encontrado. Verifique se o domínio está correto.',
                    'debug' => [
                        'host' => $host,
                        'requested_domain' => $requestedDomain,
                        'available_domains' => $availableDomains,
                    ]
                ], 404);
            })->name('api.tenant.status');

            // Endpoint alternativo: busca por código/domínio sem depender do host da requisição
            // Útil quando o app mobile não consegue acessar o subdomínio diretamente
            Route::middleware(['api'])->post('/api/tenant/verify', function () {
                $requestedDomain = request()->input('domain');

                if (!$requestedDomain) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Parâmetro "domain" é obrigatório'
                    ], 400);
                }

                \Log::info('Verificando tenant via POST - Domain: ' . $requestedDomain);

                try {
                    // Buscar por qualquer domínio que comece com o subdomínio
                    $tenant = Tenant::whereHas('domains', function ($query) use ($requestedDomain) {
                        $query->where('domain', 'LIKE', $requestedDomain . '.%')
                              ->orWhere('domain', $requestedDomain);
                    })->first();

                    // Se ainda não encontrou, buscar todos os domínios
                    if (!$tenant) {
                        $allDomains = \App\Models\Domain::where('domain', 'LIKE', $requestedDomain . '.%')
                            ->orWhere('domain', $requestedDomain)
                            ->get();

                        foreach ($allDomains as $domain) {
                            $domainParts = explode('.', $domain->domain);
                            if (isset($domainParts[0]) && $domainParts[0] === $requestedDomain) {
                                $tenant = Tenant::find($domain->tenant_id);
                                if ($tenant) {
                                    break;
                                }
                            }
                        }
                    }

                    if ($tenant) {
                        $domain = $tenant->domains->first();
                        return response()->json([
                            'status' => 'ok',
                            'tenant' => $tenant->id,
                            'domain' => $domain->domain ?? null,
                            'message' => 'Tenant encontrado e disponível'
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Erro ao verificar tenant via POST: ' . $e->getMessage());
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Tenant não encontrado'
                ], 404);
            })->name('api.tenant.verify');

            // Rotas de API para tenants (mobile app) - requerem tenant inicializado
            Route::middleware(['api', 'tenant.filesystems'])->group(base_path('routes/tenant-api.php'));
        }

    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exceção CSRF para webhooks do WhatsApp (Evolution API) e rotas de autenticação
        $middleware->validateCsrfTokens(except: [
            'api/whatsapp/*',
            '*/whatsapp/webhook',
            '*/webhooks/meta/whatsapp',
            'login',           // Rota de login do tenant (POST)
            'register',        // Rota de registro do tenant (POST)
            'forgot-password', // Rota de recuperação de senha (POST)
            'reset-password',  // Rota de reset de senha (POST)
        ]);
        
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'password.change.required' => \App\Http\Middleware\CheckPasswordChangeRequired::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant.filesystems' => TenantFilesystems::class,
            'ensureUserHasAccess' => \App\Http\Middleware\EnsureUserHasAccess::class,
            'CheckSessionExpiration' => \App\Http\Middleware\CheckSessionExpiration::class,
            'HandleSessionExpiration' => \App\Http\Middleware\HandleSessionExpiration::class,
            'set.active.company' => \App\Http\Middleware\SetActiveCompany::class, // Adicione o alias aqui
            'ensure.tenant.setup' => \App\Http\Middleware\EnsureTenantSetup::class, // Middleware para garantir setup do tenant
            'require.password.change' => \App\Http\Middleware\RequirePasswordChange::class,
            'check.user.active' => \App\Http\Middleware\CheckUserActive::class,
            'initialize.tenancy.for.webhook' => \App\Http\Middleware\InitializeTenancyForWebhook::class,
        ]);

        // Adicione o middleware ao grupo 'web' aqui
        $middleware->appendToGroup('web', [
            //\App\Http\Middleware\SetActiveCompany::class,
            \App\Http\Middleware\EnsureTenantSetup::class, // Verificar setup do tenant automaticamente
            \App\Http\Middleware\CheckSessionExpiration::class, // Verificar expiração de sessão
            \App\Http\Middleware\HandleSessionExpiration::class, // Tratar erros 419 de forma elegante
            \App\Http\Middleware\RequirePasswordChange::class, // Verificar se usuário precisa trocar senha
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        // Interceptar erro 419 (Page Expired) e redirecionar para login
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sua sessão expirou. Faça login novamente.',
                    'error' => 'SESSION_EXPIRED'
                ], 419);
            }

            // Verificar se estamos em um contexto de tenant
            $isTenant = $request->is('app/*') || $request->routeIs('tenant.*');

            if ($isTenant) {
                return redirect()->route('login')->with('error', 'Sua sessão expirou por inatividade. Faça login novamente para continuar.');
            } else {
                return redirect()->route('login')->with('error', 'Sua sessão expirou por inatividade. Faça login novamente para continuar.');
            }
        });
    })->create();
