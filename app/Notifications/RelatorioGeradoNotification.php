<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\PdfGeneration;

/**
 * Notificação genérica para relatórios/PDFs gerados com sucesso.
 * Inclui metadados ricos: tamanho do arquivo, tipo, expiração.
 */
class RelatorioGeradoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected string $downloadUrl;
    protected string $tipoRelatorio;
    protected ?int $companyId;
    protected ?int $triggeredBy;
    protected ?string $fileSize;
    protected ?string $expiresAt;

    /**
     * @param string $downloadUrl URL para download do PDF
     * @param string $tipoRelatorio Nome amigável (ex: "Boletim Financeiro - dezembro/2025")
     * @param int|null $companyId ID da empresa
     * @param int|null $triggeredBy ID do usuário que solicitou
     * @param PdfGeneration|null $pdfGeneration Model com metadados do arquivo
     */
    public function __construct(
        string $downloadUrl,
        string $tipoRelatorio,
        ?int $companyId = null,
        ?int $triggeredBy = null,
        ?PdfGeneration $pdfGeneration = null
    ) {
        $this->downloadUrl = $downloadUrl;
        $this->tipoRelatorio = $tipoRelatorio;
        $this->companyId = $companyId;
        $this->triggeredBy = $triggeredBy;

        // Extrair metadados do PdfGeneration
        if ($pdfGeneration) {
            $this->expiresAt = $pdfGeneration->expires_at?->toISOString();
            $filePath = storage_path('app/public/' . $pdfGeneration->filename);
            $this->fileSize = file_exists($filePath) ? $this->formatFileSize(filesize($filePath)) : null;
        } else {
            $this->fileSize = null;
            $this->expiresAt = null;
        }
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'icon' => 'fa-solid fa-file-pdf',
            'color' => 'danger',
            'title' => $this->tipoRelatorio,
            'message' => 'Seu relatório foi gerado com sucesso. Clique para baixar.',
            'action_url' => $this->downloadUrl,
            'target' => '_blank',
            'company_id' => $this->companyId,
            'triggered_by' => $this->triggeredBy,
            'tipo' => 'relatorio_gerado',
            'file_type' => 'PDF',
            'file_size' => $this->fileSize,
            'expires_at' => $this->expiresAt,
        ];
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . ' KB';
        }
        return $bytes . ' B';
    }
}
