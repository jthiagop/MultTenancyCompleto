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
use App\Services\LoteContabilExportService;
use Carbon\Carbon;

class GenerateLoteContabilJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected $dataInicial;
    protected $dataFinal;
    protected $entidadeId;
    protected $formato;
    protected $campoData;
    protected $companyId;
    protected $userId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct(
        string $dataInicial,
        string $dataFinal,
        int $entidadeId,
        string $formato,
        string $campoData,
        int $companyId,
        int $userId,
        string $tenantId,
        int $pdfGenerationId
    ) {
        $this->dataInicial     = $dataInicial;
        $this->dataFinal       = $dataFinal;
        $this->entidadeId      = $entidadeId;
        $this->formato         = $formato;
        $this->campoData       = $campoData;
        $this->companyId       = $companyId;
        $this->userId          = $userId;
        $this->tenantId        = $tenantId;
        $this->pdfGenerationId = $pdfGenerationId;
    }

    public function handle(LoteContabilExportService $loteService): void
    {
        try {
            Log::info('[GenerateLoteContabilJob] Job iniciado', [
                'tenant_id'         => $this->tenantId,
                'company_id'        => $this->companyId,
                'entidade_id'       => $this->entidadeId,
                'formato'           => $this->formato,
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

            // Gerar lote contábil via serviço existente (espera d/m/Y)
            $resultado = $loteService->gerar(
                $this->entidadeId,
                $this->dataInicial,
                $this->dataFinal,
                $this->campoData,
                $this->formato
            );

            // Salvar arquivo no storage
            $entidade   = EntidadeFinanceira::find($this->entidadeId);
            $dataInicio = Carbon::createFromFormat('d/m/Y', $this->dataInicial);
            $dataFim    = Carbon::createFromFormat('d/m/Y', $this->dataFinal);
            $filePrefix = $dataInicio->format('Ymd') . '_' . $dataFim->format('Ymd');
            $ext        = strtolower($this->formato);
            $filename   = "contabil/{$filePrefix}_{$this->companyId}_{$this->entidadeId}_" . time() . '.' . $ext;

            $path = base_path('storage/app/public/' . $filename);
            if (!is_dir(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            file_put_contents($path, $resultado['conteudo']);

            $friendlyName = "Contabilidade " . strtoupper($this->formato) . " - {$entidade?->nome} - {$dataInicio->format('d/m/Y')} a {$dataFim->format('d/m/Y')}";

            if ($pdfGen) {
                $pdfGen->update([
                    'status'       => 'completed',
                    'filename'     => $filename,
                    'file_name'    => $friendlyName,
                    'completed_at' => now(),
                    'expires_at'   => now()->addDays(PdfGeneration::EXPIRATION_DAYS),
                ]);
            }

            Log::info('[GenerateLoteContabilJob] Lote contábil gerado com sucesso', [
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

            Log::error('[GenerateLoteContabilJob] Erro ao gerar lote contábil', [
                'pdf_id' => $this->pdfGenerationId,
                'error'  => $e->getMessage(),
            ]);

            $user = User::find($this->userId);
            if ($user) {
                $user->notify(new RelatorioErroNotification(
                    "Contabilidade " . strtoupper($this->formato) . " - {$this->dataInicial} a {$this->dataFinal}",
                    $e->getMessage(),
                    $this->companyId
                ));
            }

            throw $e;
        }
    }
}
