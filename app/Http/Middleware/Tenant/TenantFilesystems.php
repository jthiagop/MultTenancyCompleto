<?php

namespace App\Http\Middleware\Tenant;

use App\Tenant\ManagerTenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantFilesystems
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Só configurar o filesystem do tenant se o usuário estiver autenticado
        if(auth()->check())
            $this->setFilesystemsRoot();

        return $next($request);
    }

    public function setFilesystemsRoot()
    {
        $tenant = app(ManagerTenant::class)->getTenant();

        if (!$tenant) {
            return;
        }

        // Use the database name as the folder name
        $tenantDatabaseName = $tenant->database;

        config()->set(
            'filesystems.disks.tenant.root',
            config('filesystems.disks.tenant.root') . "/{$tenantDatabaseName}"
        );
    }
}
