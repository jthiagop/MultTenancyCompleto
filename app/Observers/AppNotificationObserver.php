<?php

namespace App\Observers;

use App\Events\NotificationCountChanged;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

/**
 * Observer da AppNotification — dispara o evento de contagem em tempo real
 * sempre que uma notificação muda de estado relevante para o badge.
 *
 * Disparos:
 *  - created  → +1 unread
 *  - updated  → quando read_at sai de NULL
 *  - deleted  → recalcula
 *
 * O cálculo é uma única query rápida (um WHERE indexado em
 * notifications.notifiable_id + read_at). Em workloads pesados pode-se
 * mover para job; por ora é O(1) consulta por evento.
 */
class AppNotificationObserver
{
    public function created(AppNotification $notification): void
    {
        $this->dispatch($notification, 'created');
    }

    public function updated(AppNotification $notification): void
    {
        // Apenas reagimos quando o read_at foi alterado
        if (! $notification->wasChanged('read_at')) {
            return;
        }

        $this->dispatch($notification, 'read');
    }

    public function deleted(AppNotification $notification): void
    {
        $this->dispatch($notification, 'deleted');
    }

    private function dispatch(AppNotification $notification, string $reason): void
    {
        try {
            $tenantId = tenancy()->tenant?->id;
            if (! $tenantId) {
                return; // Sem contexto de tenant, broadcast não faz sentido
            }

            // Notifiable pode ser qualquer Model — só fazemos broadcast se for usuário.
            $userId = (int) $notification->notifiable_id;
            if ($userId <= 0) {
                return;
            }

            $unread = AppNotification::query()
                ->where('notifiable_id', $userId)
                ->where('notifiable_type', User::class)
                ->whereNull('read_at')
                ->count();

            event(new NotificationCountChanged(
                tenantId:    (string) $tenantId,
                userId:      $userId,
                unreadCount: $unread,
                reason:      $reason,
            ));
        } catch (\Throwable $e) {
            // Broadcast nunca pode quebrar o fluxo principal de notificação.
            Log::warning('[AppNotificationObserver] Falha ao disparar broadcast', [
                'reason' => $reason,
                'error'  => $e->getMessage(),
            ]);
        }
    }
}
