<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SeedPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed-permissions {--tenant= : ID específico do tenant} {--all : Executar em todos os tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executar seeder de permissões em tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Iniciando seed de permissões...');

        if ($this->option('all')) {
            $this->seedAllTenants();
        } elseif ($this->option('tenant')) {
            $this->seedSpecificTenant($this->option('tenant'));
        } else {
            $this->error('Especifique --tenant=ID ou --all');
            return 1;
        }

        $this->info('✅ Seed de permissões concluído!');
        return 0;
    }

    private function seedAllTenants(): void
    {
        $tenants = Tenant::all();
        $this->info("Encontrados {$tenants->count()} tenants para processar.");

        $progressBar = $this->output->createProgressBar($tenants->count());
        $progressBar->start();

        foreach ($tenants as $tenant) {
            try {
                $this->seedTenant($tenant);
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("\nErro ao processar tenant {$tenant->id}: " . $e->getMessage());
                Log::error("Erro ao processar tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
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
            $this->info("  🌱 Executando seeder de permissões para: {$tenant->name}");

            try {
                // Executar apenas o seeder de permissões
                Artisan::call('db:seed', [
                    '--class' => 'PermissionSeeder',
                    '--force' => true,
                    '--quiet' => true
                ]);

                $this->info("  ✅ Seeder executado com sucesso para {$tenant->name}!");
                
            } catch (\Exception $e) {
                $this->error("  ❌ Erro ao executar seeder: " . $e->getMessage());
                Log::error("Erro ao executar seeder para tenant {$tenant->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }
}

