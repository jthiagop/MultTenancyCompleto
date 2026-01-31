<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use App\Helpers\BrowsershotHelper;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\PdfGeneration;
use Carbon\Carbon;

class GenerateConciliacaoPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    protected $filters;
    protected $companyId;
    protected $userId;
    protected $tenantId;
    protected $pdfGenerationId;

    public function __construct($filters, $companyId, $userId, $tenantId, $pdfGenerationId)
    {
        $this->filters = $filters;
        $this->companyId = $companyId;
        $this->userId = $userId;
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

            $company = \App\Models\Company::find($this->companyId);
            
            // Buscar transações
            $query = TransacaoFinanceira::where('company_id', $this->companyId);
            
            if (!empty($this->filters['data_inicio'])) {
                $query->where('data_competencia', '>=', $this->filters['data_inicio']);
            }
            if (!empty($this->filters['data_fim'])) {
                $query->where('data_competencia', '<=', $this->filters['data_fim']);
            }
            if (!empty($this->filters['entidade_id'])) {
                $query->where('entidade_financeira_id', $this->filters['entidade_id']);
            }
            
            $transacoes = $query->with(['lancamentoPadrao', 'entidadeFinanceira'])->get();
            
            // Gerar HTML
            $html = view('app.relatorios.financeiro.conciliacao_pdf', [
                'transacoes' => $transacoes,
                'company' => $company,
                'filters' => $this->filters,
            ])->render();

            // Gerar PDF
            $pdf = BrowsershotHelper::configureChromePath(
                Browsershot::html($html)
                    ->format('A4')
                    ->landscape()
                    ->showBackground()
                    ->margins(8, 8, 8, 8)
            )->pdf();

            // Salvar PDF no storage CENTRAL (não no tenant)
            // Usar caminho absoluto para evitar que o FilesystemTenancyBootstrapper redirecione
            $filename = "pdfs/conciliacoes/conciliacao_" . $this->companyId . "_" . time() . ".pdf";
            $centralStoragePath = base_path('storage/app/public/' . $filename);
            
            // Garantir que o diretório existe
            $directory = dirname($centralStoragePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Salvar arquivo diretamente no storage central
            file_put_contents($centralStoragePath, $pdf);

            // Atualizar status para completed
            if ($pdfGen) {
                $pdfGen->update([
                    'status' => 'completed',
                    'filename' => $filename,
                    'completed_at' => now(),
                ]);
            }

            Log::info("PDF Conciliação gerado com sucesso", [
                'pdf_id' => $this->pdfGenerationId,
                'filename' => $filename,
                'company_id' => $this->companyId,
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

            Log::error("Erro ao gerar PDF Conciliação", [
                'pdf_id' => $this->pdfGenerationId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Re-throw para retry
        }
    }
}
