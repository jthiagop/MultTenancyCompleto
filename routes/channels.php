<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Canais privados de broadcast da aplicação. Cada usuário só recebe
| eventos do tenant em que está autenticado.
|
| Para ativar este arquivo é necessário registrar o BroadcastServiceProvider
| (Laravel >= 11 já carrega channels.php automaticamente quando há driver
| configurado). Sem driver, o arquivo é inerte — o evento é despachado mas
| não chega a nenhum cliente.
|
*/

Broadcast::channel('tenant.{tenantId}.domus-ia', function ($user, $tenantId) {
    if (! $user) {
        return false;
    }

    // Em multitenancy via stancl/tenancy, o tenant ativo é exposto em runtime.
    try {
        if (function_exists('tenant') && tenant()) {
            return (string) tenant()->id === (string) $tenantId;
        }
    } catch (\Throwable $e) {
        // Sem contexto de tenant — nega o acesso por segurança.
        return false;
    }

    return false;
});

/*
| Canal privado de notificações por usuário.
|
| Padrão: tenant.{tenantId}.user.{userId}.notifications
|
| Apenas o próprio usuário (autenticado no tenant correto) recebe os
| eventos NotificationCountChanged. Garante isolamento mesmo entre
| usuários do mesmo tenant.
*/
Broadcast::channel('tenant.{tenantId}.user.{userId}.notifications', function ($user, $tenantId, $userId) {
    if (! $user) {
        return false;
    }

    // 1) Tenant precisa bater com o ativo.
    try {
        if (! function_exists('tenant') || ! tenant()
            || (string) tenant()->id !== (string) $tenantId) {
            return false;
        }
    } catch (\Throwable $e) {
        return false;
    }

    // 2) Usuário só pode escutar o próprio canal.
    return (int) $user->getKey() === (int) $userId;
});
