<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Stancl\Tenancy\Facades\Tenancy;
use App\Models\Tenant;

class FixBankStatementCompanyId extends Command
{
    protected $signature = 'fix:bank-statement-company-id';
    protected $description = 'Corrige o company_id nos bank_statements que estão vazios ou nulos';

    public function handle()
    {
        $this->info('Iniciando correção de company_id nos bank_statements...');

        $tenants = Tenant::all();
        
        foreach ($tenants as $tenant) {
            $this->info("Processando tenant: {$tenant->id}");
            
            Tenancy::initialize($tenant);
            
            try {
                // Corrigir registros com company_id vazio ou nulo
                $affected = \DB::table('bank_statements')
                    ->whereNull('company_id')
                    ->orWhere('company_id', '')
                    ->update([
                        'company_id' => \DB::raw('(SELECT company_id FROM entidades_financeiras WHERE id = bank_statements.entidade_financeira_id)')
                    ]);
                
                $this->info("  - Corrigidos: {$affected} registros");
                
                // Verificar se ainda há registros sem company_id
                $remaining = \DB::table('bank_statements')
                    ->whereNull('company_id')
                    ->orWhere('company_id', '')
                    ->count();
                
                if ($remaining > 0) {
                    $this->warn("  - Ainda há {$remaining} registros sem company_id (entidade_financeira pode não existir)");
                }
                
            } catch (\Exception $e) {
                $this->error("  - Erro: " . $e->getMessage());
            }
            
            Tenancy::end();
        }

        $this->info('Correção concluída!');
        return Command::SUCCESS;
    }
}
