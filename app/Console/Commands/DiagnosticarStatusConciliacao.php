<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Facades\Tenancy;
use App\Models\Tenant;
use App\Models\Financeiro\BankStatement;

class DiagnosticarStatusConciliacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagnosticar:status-conciliacao {--tenant=}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Diagnostica a distribuição de status de conciliação no banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');
        
        if (!$tenantId) {
            $this->info('❌ Por favor, especifique um tenant: php artisan diagnosticar:status-conciliacao --tenant=TENANT_ID');
            return 1;
        }

        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("❌ Tenant '{$tenantId}' não encontrado!");
            return 1;
        }

        // Inicializa tenancy para o tenant especificado
        Tenancy::initialize($tenant);

        $this->info("✅ Conectado ao tenant: {$tenant->id}");
        $this->newLine();

        // Busca distribuição de status
        $statusCount = BankStatement::selectRaw('status_conciliacao, COUNT(*) as total')
            ->groupBy('status_conciliacao')
            ->get();

        if ($statusCount->isEmpty()) {
            $this->warn('⚠️ Nenhum registro de BankStatement encontrado!');
            return 0;
        }

        $this->info('📊 Distribuição de Status de Conciliação:');
        $this->line(str_repeat('-', 50));

        $total = 0;
        foreach ($statusCount as $row) {
            $status = $row->status_conciliacao ?? '(null)';
            $count = $row->total;
            $total += $count;
            $this->line("  {$status}: {$count} registros");
        }

        $this->line(str_repeat('-', 50));
        $this->info("  TOTAL: {$total} registros");
        $this->newLine();

        // Amostra de registros
        $this->info('📋 Amostra dos primeiros 10 registros:');
        $this->line(str_repeat('-', 80));

        $records = BankStatement::select('id', 'memo', 'status_conciliacao', 'amount', 'created_at')
            ->limit(10)
            ->get();

        foreach ($records as $record) {
            $this->line(sprintf(
                "  ID: %5d | Status: %-10s | Memo: %-30s | Valor: %12s",
                $record->id,
                $record->status_conciliacao ?? '(null)',
                substr($record->memo ?? '-', 0, 30),
                number_format($record->amount, 2, ',', '.')
            ));
        }

        $this->newLine();
        $this->info('✅ Diagnóstico concluído!');

        return 0;
    }
}
