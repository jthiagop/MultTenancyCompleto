<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para inicializar tenancy quando requisições vêm de localtunnel/ngrok
 * Deve ser executado ANTES do middleware padrão de tenancy
 */
class InitializeTenancyForWebhook
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se já está inicializado, não fazer nada
        if (Tenancy::initialized) {
            return $next($request);
        }

        $host = $request->getHost();
        
        // Verificar se é localtunnel (ex: recife.loca.lt)
        if (str_contains($host, '.loca.lt')) {
            // Extrair subdomínio (ex: "recife" de "recife.loca.lt")
            $subdomain = explode('.loca.lt', $host)[0];
            
            Log::info("[InitializeTenancyForWebhook] Detectado localtunnel com subdomínio: {$subdomain}");

            // Buscar tenant pelo domínio que corresponde ao subdomínio
            // Ex: se subdomain é "recife", buscar domínio "recife.localhost" ou similar
            $tenant = \App\Models\Tenant::whereHas('domains', function ($query) use ($subdomain) {
                $query->where('domain', 'LIKE', "{$subdomain}.%")
                      ->orWhere('domain', 'LIKE', "%{$subdomain}%");
            })->first();

            if ($tenant) {
                Tenancy::initialize($tenant);
                Log::info("[InitializeTenancyForWebhook] Tenant inicializado via localtunnel: {$tenant->id} ({$tenant->name})");
            } else {
                Log::warning("[InitializeTenancyForWebhook] Tenant não encontrado para subdomínio localtunnel: {$subdomain}");
            }
        }
        // Verificar se é ngrok e tem tenant_id como parâmetro
        elseif (str_contains($host, 'ngrok') || str_contains($host, 'ngrok.io')) {
            $tenantId = $request->input('tenant_id') ?? $request->header('X-Tenant-ID');
            
            if ($tenantId) {
                $tenant = \App\Models\Tenant::find($tenantId);
                if ($tenant) {
                    Tenancy::initialize($tenant);
                    Log::info("[InitializeTenancyForWebhook] Tenant inicializado via ngrok: {$tenant->id} ({$tenant->name})");
                }
            }
        }

        return $next($request);
    }
}
