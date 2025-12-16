<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class AssignAllPermissionsToFirstUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-all-permissions-to-first 
                            {--user-id= : ID específico do usuário para atribuir todas as permissões}
                            {--tenant= : ID específico do tenant (obrigatório)}
                            {--all : Executar em todos os tenants}
                            {--force : Forçar atribuição mesmo se o usuário já tiver permissões}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atribui todas as permissões ao primeiro usuário cadastrado (Usuário Supremo) no contexto do tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            return $this->assignToAllTenants();
        } elseif ($this->option('tenant')) {
            return $this->assignToSpecificTenant($this->option('tenant'));
        } else {
            $this->error('Especifique --tenant=ID ou --all');
            return Command::FAILURE;
        }
    }

    private function assignToAllTenants(): int
    {
        $tenants = Tenant::all();
        $this->info("Encontrados {$tenants->count()} tenants para processar.");

        $progressBar = $this->output->createProgressBar($tenants->count());
        $progressBar->start();

        foreach ($tenants as $tenant) {
            try {
                $this->assignToTenant($tenant);
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("\nErro ao processar tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine();
        return Command::SUCCESS;
    }

    private function assignToSpecificTenant(string $tenantId): int
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant com ID {$tenantId} não encontrado.");
            return Command::FAILURE;
        }

        $this->info("Processando tenant: {$tenant->name} (ID: {$tenant->id})");
        return $this->assignToTenant($tenant);
    }

    private function assignToTenant(Tenant $tenant): int
    {
        return $tenant->run(function () use ($tenant) {
            $userId = $this->option('user-id');
            $force = $this->option('force');

            $this->info("  👤 Atribuindo permissões no tenant: {$tenant->name}");

            // Se um ID específico foi fornecido, usar esse usuário
            if ($userId) {
                $user = User::find($userId);
                if (!$user) {
                    $this->error("  ❌ Usuário com ID {$userId} não encontrado neste tenant!");
                    return Command::FAILURE;
                }
            } else {
                // Caso contrário, buscar o primeiro usuário (mais antigo)
                $user = User::orderBy('id', 'asc')->first();
                
                if (!$user) {
                    $this->error("  ❌ Nenhum usuário encontrado neste tenant!");
                    return Command::FAILURE;
                }
            }

            // Verificar se o usuário já tem permissões (a menos que --force seja usado)
            if (!$force && $user->permissions()->count() > 0) {
                if (!$this->confirm("  O usuário '{$user->name}' já possui {$user->permissions()->count()} permissões. Deseja substituir por todas as permissões disponíveis?")) {
                    $this->info("  Operação cancelada.");
                    return Command::SUCCESS;
                }
            }

            // Buscar todas as permissões
            $allPermissions = Permission::all();
            
            if ($allPermissions->isEmpty()) {
                $this->warn("  ⚠️  Nenhuma permissão encontrada neste tenant. Execute o seeder de permissões primeiro.");
                return Command::FAILURE;
            }

            // Atribuir todas as permissões
            try {
                $user->syncPermissions($allPermissions->pluck('id')->toArray());
                
                $this->info("  ✅ Sucesso! Todas as {$allPermissions->count()} permissões foram atribuídas ao usuário:");
                $this->line("     - ID: {$user->id}");
                $this->line("     - Nome: {$user->name}");
                $this->line("     - Email: {$user->email}");
                $this->line("     - Total de permissões: {$user->permissions()->count()}");
                
                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error("  ❌ Erro ao atribuir permissões: " . $e->getMessage());
                return Command::FAILURE;
            }
        });
    }
}
