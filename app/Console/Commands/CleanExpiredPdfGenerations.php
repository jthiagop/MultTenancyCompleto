<?php

namespace App\Console\Commands;

use App\Models\PdfGeneration;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanExpiredPdfGenerations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:clean-expired {--days=5 : Dias para expiração}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove PDFs gerados que expiraram (padrão: 5 dias)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $this->info("🗑️  Limpando PDFs expirados (mais de {$days} dias)...");

        $totalDeleted = 0;
        $totalFilesDeleted = 0;

        // Processar cada tenant
        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($days, &$totalDeleted, &$totalFilesDeleted, $tenant) {
                // Buscar PDFs expirados (por expires_at ou created_at)
                $expiredPdfs = PdfGeneration::where(function ($query) use ($days) {
                    $query->where('expires_at', '<', now())
                          ->orWhere(function ($q) use ($days) {
                              $q->whereNull('expires_at')
                                ->where('created_at', '<', now()->subDays($days));
                          });
                })->get();

                foreach ($expiredPdfs as $pdf) {
                    // Deletar arquivo físico se existir
                    if ($pdf->filename && Storage::disk('public')->exists($pdf->filename)) {
                        Storage::disk('public')->delete($pdf->filename);
                        $totalFilesDeleted++;
                        $this->line("   📄 Arquivo deletado: {$pdf->filename}");
                    }

                    // Deletar registro do banco
                    $pdf->delete();
                    $totalDeleted++;
                }

                if ($expiredPdfs->count() > 0) {
                    $this->info("   ✅ Tenant {$tenant->id}: {$expiredPdfs->count()} registros removidos");
                }
            });
        }

        $this->newLine();
        $this->info("✨ Limpeza concluída!");
        $this->table(
            ['Métrica', 'Quantidade'],
            [
                ['Registros deletados', $totalDeleted],
                ['Arquivos removidos', $totalFilesDeleted],
                ['Tenants processados', $tenants->count()],
            ]
        );

        Log::info('[CleanExpiredPdfGenerations] Limpeza executada', [
            'registros_deletados' => $totalDeleted,
            'arquivos_removidos' => $totalFilesDeleted,
            'dias_expiracao' => $days,
        ]);

        return Command::SUCCESS;
    }
}
