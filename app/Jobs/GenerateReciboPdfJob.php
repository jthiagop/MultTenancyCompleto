<?php

namespace App\Jobs;

use App\Models\Financeiro\Recibo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use App\Models\PdfGeneration;

class GenerateReciboPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutos
    public $tries = 3;

    protected $reciboId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct($reciboId, $tenantId, $pdfGenerationId)
    {
        $this->reciboId = $reciboId;
        $this->tenantId = $tenantId;
        $this->pdfGenerationId = $pdfGenerationId;
    }

    public function handle()
    {
        try {
            // Inicializar tenant
            if ($this->tenantId) {
                $tenant = \App\Models\Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            // Atualizar status para processing
            $pdfGen = PdfGeneration::find($this->pdfGenerationId);
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'processing',
                    'started_at' => now(),
                ]);
            }

            $recibo = Recibo::with(['address', 'transacao'])->findOrFail($this->reciboId);
            $company = \App\Models\Company::find($recibo->transacao->company_id);

            // Gerar HTML
            $html = view('app.relatorios.financeiro.recibo', [
                'recibo' => $recibo,
                'company' => $company,
                'companyLogo' => $this->logoToBase64($company),
            ])->render();

            // Gerar PDF
            $pdf = BrowsershotHelper::configureChromePath(
                Browsershot::html($html)
                    ->format('A4')
                    ->margins(5, 5, 5, 5)
                    ->showBackground()
            )->pdf();

            // Salvar PDF no storage
            $filename = "pdfs/recibos/recibo_{$recibo->id}_" . time() . ".pdf";
            Storage::disk('public')->put($filename, $pdf);

            // Atualizar recibo com caminho do PDF
            $recibo->update(['pdf_path' => $filename]);

            // Atualizar status para completed
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'completed',
                    'filename' => $filename,
                    'completed_at' => now(),
                ]);
            }

            Log::info("PDF Recibo gerado com sucesso", [
                'pdf_id' => $this->pdfGenerationId,
                'recibo_id' => $this->reciboId,
                'filename' => $filename,
            ]);

            return $filename;

        } catch (\Exception $e) {
            // Atualizar status para failed
            if (isset($pdfGen)) {
                $pdfGen->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'completed_at' => now(),
                ]);
            }

            Log::error("Erro ao gerar PDF Recibo", [
                'pdf_id' => $this->pdfGenerationId,
                'recibo_id' => $this->reciboId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw para retry
        }
    }

    protected function logoToBase64($company): ?string
    {
        if (!$company || !$company->avatar) {
            $path = public_path('assets/media/png/perfil.svg');
        } else {
            $path = storage_path('app/public/' . $company->avatar);
        }

        if (!file_exists($path)) {
            return null;
        }

        return 'data:image/' . pathinfo($path, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($path));
    }
}
