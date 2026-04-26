<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Disparado sempre que a contagem de notificações não-lidas de um usuário
 * muda (criada, marcada como lida, marcada todas, removida).
 *
 * É broadcast em canal privado por usuário no tenant atual:
 *   private-tenant.{tenantId}.user.{userId}.notifications
 *
 * O front-end React (useNotifications) escuta este evento via Laravel Echo
 * para atualizar o badge instantaneamente, sem esperar o próximo polling
 * (30s por padrão).
 *
 * Sem driver de broadcast configurado o evento simplesmente não chega ao
 * cliente — o polling faz fallback automático.
 */
class NotificationCountChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $tenantId,
        public readonly int    $userId,
        public readonly int    $unreadCount,
        /** Tipo do trigger: 'created' | 'read' | 'read_all' | 'deleted' */
        public readonly string $reason = 'created',
    ) {}

    /** @return array<int, PrivateChannel> */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("tenant.{$this->tenantId}.user.{$this->userId}.notifications"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification.count.changed';
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'unread_count' => $this->unreadCount,
            'reason'       => $this->reason,
            'at'           => now()->toIso8601String(),
        ];
    }
}
