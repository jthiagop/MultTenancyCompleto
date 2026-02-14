<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean-expired {--days=0 : Dias adicionais de tolerância após expiração}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove notificações com arquivos expirados (baseado no campo expires_at)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $toleranceDays = (int) $this->option('days');
        $this->info("🗑️  Limpando notificações expiradas...");
        
        if ($toleranceDays > 0) {
            $this->info("   📅 Tolerância adicional: {$toleranceDays} dias após expiração");
        }

        $totalDeleted = 0;
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $tenant->run(function () use (&$totalDeleted, $tenant, $toleranceDays) {
                $deleted = $this->cleanExpiredNotifications($toleranceDays);
                
                if ($deleted > 0) {
                    $this->info("   ✅ Tenant {$tenant->id}: {$deleted} notificações removidas");
                    $totalDeleted += $deleted;
                }
            });
        }

        $this->newLine();
        $this->info("✨ Limpeza concluída!");
        $this->table(
            ['Métrica', 'Quantidade'],
            [
                ['Notificações deletadas', $totalDeleted],
                ['Tenants processados', $tenants->count()],
            ]
        );

        Log::info('[CleanExpiredNotifications] Limpeza executada', [
            'notifications_deleted' => $totalDeleted,
            'tenants_processed' => $tenants->count(),
            'tolerance_days' => $toleranceDays,
        ]);

        return Command::SUCCESS;
    }

    /**
     * Remove notificações cujo expires_at já passou.
     */
    private function cleanExpiredNotifications(int $toleranceDays): int
    {
        $cutoffDate = Carbon::now()->subDays($toleranceDays);
        
        // Notificações expiradas têm expires_at definido no campo JSON 'data'
        // Formato: data->expires_at é ISO 8601 string
        $deleted = DB::table('notifications')
            ->whereNotNull('data')
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.expires_at')) IS NOT NULL")
            ->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.expires_at')) < ?", [$cutoffDate->toISOString()])
            ->delete();

        return $deleted;
    }
}
