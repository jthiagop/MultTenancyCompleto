<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notificação genérica para relatórios/PDFs gerados com sucesso.
 * Pode ser usada para: Boletim Financeiro, Conciliação Bancária, Prestação de Contas, etc.
 */
class RelatorioGeradoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $downloadUrl;
    protected string $tipoRelatorio;
    protected ?int $companyId;
    protected ?int $triggeredBy;

    /**
     * Create a new notification instance.
     *
     * @param string $downloadUrl URL para download do PDF
     * @param string $tipoRelatorio Nome do relatório (ex: "Boletim Financeiro", "Conciliação Bancária")
     * @param int|null $companyId ID da empresa (para filtro multitenant)
     * @param int|null $triggeredBy ID do usuário que solicitou o relatório
     */
    public function __construct(string $downloadUrl, string $tipoRelatorio, ?int $companyId = null, ?int $triggeredBy = null)
    {
        $this->downloadUrl = $downloadUrl;
        $this->tipoRelatorio = $tipoRelatorio;
        $this->companyId = $companyId;
        $this->triggeredBy = $triggeredBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'ki-file-added',
            'color' => 'success',
            'title' => $this->tipoRelatorio,
            'message' => "Seu relatório foi gerado com sucesso. Clique para baixar.",
            'action_url' => $this->downloadUrl,
            'target' => '_blank',
            'company_id' => $this->companyId,
            'triggered_by' => $this->triggeredBy,
            'tipo' => 'relatorio_gerado',
        ];
    }
}
