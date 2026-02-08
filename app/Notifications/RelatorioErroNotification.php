<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notificação quando ocorre erro ao gerar relatório/PDF.
 */
class RelatorioErroNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $tipoRelatorio;
    protected string $mensagemErro;
    protected ?int $companyId;

    /**
     * Create a new notification instance.
     *
     * @param string $tipoRelatorio Nome do relatório
     * @param string $mensagemErro Mensagem de erro
     * @param int|null $companyId ID da empresa
     */
    public function __construct(string $tipoRelatorio, string $mensagemErro, ?int $companyId = null)
    {
        $this->tipoRelatorio = $tipoRelatorio;
        $this->mensagemErro = $mensagemErro;
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
            'icon' => 'ki-cross-circle',
            'color' => 'danger',
            'title' => 'Erro ao Gerar Relatório',
            'message' => "Falha ao gerar {$this->tipoRelatorio}: {$this->mensagemErro}",
            'action_url' => null,
            'target' => '_self',
            'company_id' => $this->companyId,
            'tipo' => 'relatorio_erro',
        ];
    }
}
