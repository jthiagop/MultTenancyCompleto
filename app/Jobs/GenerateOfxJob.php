<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\EntidadeFinanceira;
use App\Models\PdfGeneration;
use App\Models\User;
use App\Notifications\RelatorioGeradoNotification;
use App\Notifications\RelatorioErroNotification;
use App\Services\OfxExportService;
use Carbon\Carbon;

class GenerateOfxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected $dataInicial;
    protected $dataFinal;
    protected $entidadeId;
    protected $companyId;
    protected $userId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct(
        string $dataInicial,
        string $dataFinal,
        int $entidadeId,
        int $companyId,
        int $userId,
        string $tenantId,
        int $pdfGenerationId
    ) {
        $this->dataInicial     = $dataInicial;
        $this->dataFinal       = $dataFinal;
        $this->entidadeId      = $entidadeId;
        $this->companyId       = $companyId;
        $this->userId          = $userId;
        $this->tenantId        = $tenantId;
        $this->pdfGenerationId = $pdfGenerationId;
    }

    public function handle(OfxExportService $ofxExportService): void
    {
        try {
            Log::info('[GenerateOfxJob] Job iniciado', [
                'tenant_id'         => $this->tenantId,
                'company_id'        => $this->companyId,
                'entidade_id'       => $this->entidadeId,
                'pdf_generation_id' => $this->pdfGenerationId,
            ]);

            // Inicializar tenant
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            // Definir company na sessão para que scopes forActiveCompany() funcionem no contexto da queue
            session(['active_company_id' => $this->companyId]);

            $pdfGen = PdfGeneration::find($this->pdfGenerationId);
            if ($pdfGen) {
                $pdfGen->update(['status' => 'processing', 'started_at' => now()]);
            }

            // Gerar OFX via serviço existente (espera d/m/Y)
            $resultado = $ofxExportService->gerarOfx(
                $this->entidadeId,
                $this->dataInicial,
                $this->dataFinal
            );

            // Salvar arquivo no storage
            $entidade    = EntidadeFinanceira::find($this->entidadeId);
            $dataInicio  = Carbon::createFromFormat('d/m/Y', $this->dataInicial);
            $dataFim     = Carbon::createFromFormat('d/m/Y', $this->dataFinal);
            $filePrefix  = $dataInicio->format('Ymd') . '_' . $dataFim->format('Ymd');
            $filename    = "ofx/{$filePrefix}_{$this->companyId}_{$this->entidadeId}_" . time() . '.ofx';

            $path = base_path('storage/app/public/' . $filename);
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            file_put_contents($path, $resultado['conteudo']);

            $friendlyName = "OFX - {$entidade?->nome} - {$dataInicio->format('d/m/Y')} a {$dataFim->format('d/m/Y')}";

            if ($pdfGen) {
                $pdfGen->update([
                    'status'       => 'completed',
                    'filename'     => $filename,
                    'file_name'    => $friendlyName,
                    'completed_at' => now(),
                    'expires_at'   => now()->addDays(PdfGeneration::EXPIRATION_DAYS),
                ]);
            }

            Log::info('[GenerateOfxJob] OFX gerado com sucesso', [
                'pdf_id'   => $this->pdfGenerationId,
                'filename' => $filename,
            ]);

            $user = User::find($this->userId);
            if ($user && $pdfGen) {
                $pdfGen->refresh();
                $user->notify(new RelatorioGeradoNotification(
                    $pdfGen->download_url ?? '#',
                    $friendlyName,
                    $this->companyId,
                    $this->userId,
                    $pdfGen
                ));
            }

        } catch (\Exception $e) {
            if (isset($pdfGen)) {
                $pdfGen->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at'  => now(),
                ]);
            }

            Log::error('[GenerateOfxJob] Erro ao gerar OFX', [
                'pdf_id' => $this->pdfGenerationId,
                'error'  => $e->getMessage(),
            ]);

            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new RelatorioErroNotification(
                    "OFX - {$this->dataInicial} a {$this->dataFinal}",
                    $e->getMessage(),
                    $this->companyId
                ));
            }

            throw $e;
        }
    }
}
