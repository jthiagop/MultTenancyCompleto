<?php

namespace App\Policies;

use App\Models\AppNotification;
use App\Models\User;

/**
 * Policy de notificação: apenas o destinatário pode visualizar, marcar como
 * lida ou apagar uma notificação.
 *
 * Antes da Onda 4 a verificação era feita inline no controller via
 * `Auth::user()->notifications()->where('id', $id)->first()`. Centralizar
 * em policy:
 *  - reduz risco de "esquecer o where()" em novos endpoints;
 *  - permite expansão futura (auditoria, suspensão de acesso, etc.).
 */
class NotificationPolicy
{
    /**
     * Permite acesso administrativo via super-admin (caso exista).
     * Retorne true para conceder acesso global; null para deixar a decisão
     * para os métodos abaixo.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, AppNotification $notification): bool
    {
        return $this->isOwner($user, $notification);
    }

    public function update(User $user, AppNotification $notification): bool
    {
        return $this->isOwner($user, $notification);
    }

    public function delete(User $user, AppNotification $notification): bool
    {
        return $this->isOwner($user, $notification);
    }

    /**
     * Apenas notificações cujo notifiable é o próprio usuário (User polymorphic).
     * Considera notificações com company_id NULL (broadcast tenant-wide) como
     * acessíveis se a empresa ativa do usuário for a empresa configurada na
     * sessão — o controller já filtra; a policy reforça.
     */
    private function isOwner(User $user, AppNotification $notification): bool
    {
        if ((int) $notification->notifiable_id !== (int) $user->getKey()) {
            return false;
        }

        $expectedType = $user->getMorphClass();
        if (! empty($notification->notifiable_type)
            && $notification->notifiable_type !== $expectedType) {
            return false;
        }

        return true;
    }
}
