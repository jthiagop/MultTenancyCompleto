<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SeedIntegracoes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integracoes:seed {--tenant= : ID do tenant específico}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executa o seeder de integrações para um ou todos os tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->option('tenant');

        if ($tenantId) {
            $this->seedSpecificTenant($tenantId);
        } else {
            $this->seedAllTenants();
        }

        $this->newLine();
    }

    private function seedAllTenants(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant encontrado.');
            return;
        }

        $this->info("Processando {$tenants->count()} tenant(s)...");
        $this->newLine();

        foreach ($tenants as $tenant) {
            $this->seedTenant($tenant);
        }

        $this->newLine();
    }

    private function seedSpecificTenant(string $tenantId): void
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant com ID {$tenantId} não encontrado.");
            return;
        }

        $this->info("Processando tenant: {$tenant->name} (ID: {$tenant->id})");
        $this->seedTenant($tenant);
    }

    private function seedTenant(Tenant $tenant): void
    {
        $tenant->run(function () use ($tenant) {
            $this->info("  🌱 Executando seeder de integrações para: {$tenant->name}");

            try {
                // Executar apenas o seeder de integrações
                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\IntegracaoSeeder',
                    '--force' => true,
                    '--quiet' => true
                ]);

                $this->info("  ✅ Seeder executado com sucesso para {$tenant->name}!");
                
            } catch (\Exception $e) {
                $this->error("  ❌ Erro ao executar seeder: " . $e->getMessage());
                Log::error("Erro ao executar seeder de integrações para tenant {$tenant->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
