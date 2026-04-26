<?php

namespace App\Console\Commands\Notifications;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Mantém a tabela `notifications_log` (criada na Onda 2) enxuta. Cada envio
 * por canal externo (WhatsApp, e-mail, etc.) é registrado para auditoria,
 * mas após N dias os logs já cumpriram seu papel — manter linhas antigas
 * só engorda o tenant.
 *
 * Default: remove logs com mais de 90 dias.
 */
class PurgeOldNotificationLogs extends Command
{
    protected $signature = 'notifications:purge-old-logs
                            {--days=90 : Idade mínima (em dias) para purgar}
                            {--tenant= : Limita a um tenant específico}
                            {--dry-run : Não deleta, só relata o que faria}';

    protected $description = 'Remove registros antigos de notifications_log para reduzir tamanho do banco';

    public function handle(): int
    {
        $days   = max(1, (int) $this->option('days'));
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = Carbon::now()->subDays($days);

        $tenants = $this->option('tenant')
            ? Tenant::where('id', $this->option('tenant'))->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant encontrado.');
            return self::SUCCESS;
        }

        $totalDeleted = 0;

        foreach ($tenants as $tenant) {
            $tenant->run(function () use ($tenant, $cutoff, $dryRun, &$totalDeleted) {
                if (! Schema::hasTable('notifications_log')) {
                    $this->line("· Tenant {$tenant->id}: tabela notifications_log inexistente, pulando.");
                    return;
                }

                $batchSize  = 2000;
                $localCount = 0;

                do {
                    $query = DB::table('notifications_log')
                        ->where('created_at', '<', $cutoff)
                        ->limit($batchSize);

                    if ($dryRun) {
                        $count   = (clone $query)->count();
                        $deleted = $count;
                        // Sai do loop em dry-run para não duplicar contagem.
                        $localCount += $deleted;
                        break;
                    }

                    $deleted = $query->delete();
                    $localCount += $deleted;
                } while (! $dryRun && $deleted >= $batchSize);

                $totalDeleted += $localCount;

                if ($localCount > 0) {
                    $this->info("· Tenant {$tenant->id}: " . ($dryRun ? "[dry-run] {$localCount} candidatos" : "{$localCount} removidos"));
                }
            });
        }

        $this->newLine();
        $this->info($dryRun ? "Total candidatos: {$totalDeleted}" : "Total removidos: {$totalDeleted}");

        Log::info('[PurgeOldNotificationLogs] Execução', [
            'days'    => $days,
            'tenant'  => $this->option('tenant'),
            'dry_run' => $dryRun,
            'total'   => $totalDeleted,
        ]);

        return self::SUCCESS;
    }
}
