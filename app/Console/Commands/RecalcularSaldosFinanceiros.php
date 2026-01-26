<?php

namespace App\Console\Commands;

use App\Models\EntidadeFinanceira;
use Illuminate\Console\Command;

class RecalcularSaldosFinanceiros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financeiro:recalcular-saldos {--company_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula todos os saldos das entidades financeiras sincronizando cache com movimentações';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando recálculo de saldos financeiros...');
        $this->newLine();

        $companyId = $this->option('company_id');

        try {
            $corrigidas = EntidadeFinanceira::recalcularTodosSaldos($companyId);

            $this->info("✅ Recálculo concluído com sucesso!");
            $this->info("📊 {$corrigidas} entidade(s) teve saldo recalculado");
            $this->newLine();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao recalcular saldos: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
