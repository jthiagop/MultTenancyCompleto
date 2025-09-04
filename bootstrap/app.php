<?php

use App\Http\Middleware\Tenant\TenantFilesystems;
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

            foreach ($centralDomains as $domain) {
                Route::middleware('web')
                    ->domain($domain)
                    ->group(base_path('routes/web.php'));
            }

            Route::middleware('web', 'tenant.filesystems')->group(base_path('routes/tenant.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'tenant.filesystems' => TenantFilesystems::class,
            'ensureUserHasAccess' => \App\Http\Middleware\EnsureUserHasAccess::class,
            'CheckSessionExpiration' => \App\Http\Middleware\CheckSessionExpiration::class,
            'set.active.company' => \App\Http\Middleware\SetActiveCompany::class, // Adicione o alias aqui
            'ensure.tenant.setup' => \App\Http\Middleware\EnsureTenantSetup::class, // Middleware para garantir setup do tenant
        ]);

        // Adicione o middleware ao grupo 'web' aqui
        $middleware->appendToGroup('web', [
            //\App\Http\Middleware\SetActiveCompany::class,
            \App\Http\Middleware\EnsureTenantSetup::class, // Verificar setup do tenant automaticamente
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
