<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notificação genérica para avisos do sistema.
 * Pode ser usada para qualquer mensagem customizada.
 */
class AvisoSistemaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $titulo;
    protected string $mensagem;
    protected string $cor;
    protected string $icone;
    protected ?string $actionUrl;
    protected ?int $companyId;

    /**
     * Create a new notification instance.
     *
     * @param string $titulo Título da notificação
     * @param string $mensagem Mensagem/descrição
     * @param string $cor Cor do badge (success, danger, warning, info, primary)
     * @param string $icone Ícone do Metronic (ki-*)
     * @param string|null $actionUrl URL de ação (opcional)
     * @param int|null $companyId ID da empresa
     */
    public function __construct(
        string $titulo,
        string $mensagem,
        string $cor = 'info',
        string $icone = 'ki-notification-on',
        ?string $actionUrl = null,
        ?int $companyId = null
    ) {
        $this->titulo = $titulo;
        $this->mensagem = $mensagem;
        $this->cor = $cor;
        $this->icone = $icone;
        $this->actionUrl = $actionUrl;
        $this->companyId = $companyId;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => $this->icone,
            'color' => $this->cor,
            'title' => $this->titulo,
            'message' => $this->mensagem,
            'action_url' => $this->actionUrl,
            'target' => '_self',
            'company_id' => $this->companyId,
            'tipo' => 'aviso_sistema',
        ];
    }
}
