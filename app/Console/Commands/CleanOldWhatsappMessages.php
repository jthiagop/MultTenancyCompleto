<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanOldWhatsappMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:clean-old-messages {--days=30 : Número de dias para manter os registros (padrão: 30)} {--dry-run : Executar sem deletar, apenas mostrar quantos registros seriam removidos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove registros antigos da tabela whatsapp_messages_processed (mantém apenas os últimos N dias)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');

        if ($days < 1) {
            $this->error('O número de dias deve ser maior ou igual a 1.');
            return Command::FAILURE;
        }

        $cutoffDate = Carbon::now()->subDays($days);

        if ($dryRun) {
            $this->info("🔍 Modo dry-run: nenhum registro será deletado");
            $this->info("📅 Removeria registros mais antigos que: {$cutoffDate->format('d/m/Y H:i:s')}");
        }

        // Contar registros que seriam removidos
        $count = DB::table('whatsapp_messages_processed')
            ->where('processed_at', '<', $cutoffDate)
            ->count();

        if ($count === 0) {
            $this->info("✅ Nenhum registro antigo encontrado (mantendo registros dos últimos {$days} dias).");
            return Command::SUCCESS;
        }

        $this->info("📋 Encontrados {$count} registro(s) mais antigo(s) que {$days} dias.");

        if ($dryRun) {
            $this->info("💡 Execute sem --dry-run para remover estes registros.");
            $this->info("💡 Exemplo: php artisan whatsapp:clean-old-messages --days={$days}");
        } else {
            // Deletar registros antigos em lotes (chunks) para melhor performance em grandes volumes
            // Evita locks longos na tabela ao deletar milhões de registros
            $chunkSize = 1000;
            $totalDeleted = 0;

            $this->info("🗑️  Deletando registros em lotes de {$chunkSize}...");

            do {
                $deleted = DB::table('whatsapp_messages_processed')
                    ->where('processed_at', '<', $cutoffDate)
                    ->limit($chunkSize)
                    ->delete();

                $totalDeleted += $deleted;

                if ($deleted > 0) {
                    $this->info("  📊 Deletados {$totalDeleted} de {$count} registro(s)...");
                }
            } while ($deleted > 0);

            $this->info("✅ {$totalDeleted} registro(s) antigo(s) removido(s) com sucesso.");
            $this->info("📊 Mantidos apenas registros dos últimos {$days} dias.");

            // Logar a ação
            Log::info("Comando whatsapp:clean-old-messages executado. {$totalDeleted} registro(s) removido(s) (mantendo últimos {$days} dias).");
        }

        return Command::SUCCESS;
    }
}
