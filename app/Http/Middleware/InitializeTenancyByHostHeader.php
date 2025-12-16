<?php

namespace App\Http\Middleware;

use Closure;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

/**
 * Middleware que inicializa o tenant verificando primeiro o header Host
 * Útil quando requisições vêm de IPs (mobile app) mas precisam identificar o tenant pelo domínio
 */
class InitializeTenancyByHostHeader extends InitializeTenancyByDomain
{
    /**
     * Handle an incoming request.
     */
    public function handle($request, Closure $next)
    {
        // Se a requisição vem de um IP, verificar o header Host primeiro
        $host = $request->getHost();
        $isIP = filter_var($host, FILTER_VALIDATE_IP) !== false;

        \Log::info('[InitializeTenancyByHostHeader] Host da requisição: ' . $host . ' (é IP: ' . ($isIP ? 'sim' : 'não') . ')');

        $domainToUse = $host; // Por padrão, usar o host da requisição

        if ($isIP) {
            // Tentar usar o header Host se disponível
            $hostHeader = $request->header('Host');

            // Tentar também o header X-Host que pode ser enviado pelo mobile app
            $xHostHeader = $request->header('X-Host') ?? $request->header('x-host');

            // Log de todos os headers recebidos para debug
            \Log::info('[InitializeTenancyByHostHeader] Header Host recebido: ' . ($hostHeader ?? 'não fornecido'));
            \Log::info('[InitializeTenancyByHostHeader] Header X-Host recebido: ' . ($xHostHeader ?? 'não fornecido'));
            \Log::info('[InitializeTenancyByHostHeader] Todos os headers: ' . json_encode($request->headers->all()));

            // Remover porta do header Host se presente (ex: 192.168.1.2:8001 -> 192.168.1.2)
            $hostHeaderWithoutPort = $hostHeader ? preg_replace('/:\d+$/', '', $hostHeader) : null;

            // Verificar se o header Host é um IP (com ou sem porta)
            $isHostHeaderIP = $hostHeaderWithoutPort ? filter_var($hostHeaderWithoutPort, FILTER_VALIDATE_IP) !== false : true;

            // Priorizar X-Host se disponível, senão usar Host se não for IP
            if ($xHostHeader && !filter_var($xHostHeader, FILTER_VALIDATE_IP)) {
                $domainToUse = $xHostHeader;
                \Log::info('[InitializeTenancyByHostHeader] Usando header X-Host para identificar tenant: ' . $domainToUse);
            } elseif ($hostHeader && !$isHostHeaderIP) {
                // O header Host contém um domínio válido, usar ele
                $domainToUse = $hostHeaderWithoutPort; // Remover porta se houver
                \Log::info('[InitializeTenancyByHostHeader] Usando header Host para identificar tenant: ' . $domainToUse . ' (requisição de IP: ' . $host . ')');
            } else {
                \Log::warning('[InitializeTenancyByHostHeader] Header Host não disponível ou é um IP (' . ($hostHeader ?? 'null') . '). Tentando identificar tenant pelo IP...');
            }
        }

        // Usar o método initializeTenancy diretamente com o domínio correto
        // Em vez de chamar parent::handle(), chamamos initializeTenancy diretamente
        // para garantir que usamos o domínio correto (do header Host se disponível)
        try {
            return $this->initializeTenancy($request, $next, $domainToUse ?? $host);
        } catch (\Exception $e) {
            \Log::error('[InitializeTenancyByHostHeader] Erro ao inicializar tenant: ' . $e->getMessage());
            \Log::error('[InitializeTenancyByHostHeader] Host atual: ' . $request->getHost());
            \Log::error('[InitializeTenancyByHostHeader] Header Host: ' . $request->header('Host'));
            \Log::error('[InitializeTenancyByHostHeader] DomainToUse: ' . ($domainToUse ?? $host));
            throw $e;
        }
    }
}

