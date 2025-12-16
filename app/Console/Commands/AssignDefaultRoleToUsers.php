<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignDefaultRoleToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-default-role
                            {--role=user : O nome do role a ser atribuído}
                            {--tenant= : ID específico do tenant (obrigatório)}
                            {--all : Executar em todos os tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atribui um role padrão a todos os usuários que não possuem nenhum role no contexto do tenant';

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
            $this->info('Exemplo: php artisan users:assign-default-role --tenant=1');
            $this->info('Ou para todos: php artisan users:assign-default-role --all');
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
            $roleName = $this->option('role');

            $this->info("  🔍 Buscando role '{$roleName}' no tenant: {$tenant->name}");

            // Buscar o role no contexto do tenant
            $role = Role::where('name', $roleName)->first();

            if (!$role) {
                $availableRoles = Role::pluck('name')->toArray();
                $this->error("  ❌ Role '{$roleName}' não encontrado neste tenant!");
                if (!empty($availableRoles)) {
                    $this->info("  Roles disponíveis: " . implode(', ', $availableRoles));
                } else {
                    $this->warn("  ⚠️  Nenhum role encontrado neste tenant. Execute o seeder de roles primeiro.");
                }
                return Command::FAILURE;
            }

            // Buscar todos os usuários sem roles
            $usersWithoutRoles = User::doesntHave('roles')->get();

            if ($usersWithoutRoles->isEmpty()) {
                $this->info("  ✅ Todos os usuários já possuem pelo menos um role neste tenant.");
                return Command::SUCCESS;
            }

            $this->info("  👥 Encontrados {$usersWithoutRoles->count()} usuário(s) sem roles.");

            // Atribuir o role
            $bar = $this->output->createProgressBar($usersWithoutRoles->count());
            $bar->start();

            foreach ($usersWithoutRoles as $user) {
                try {
                    $user->assignRole($role);
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->error("\n  ❌ Erro ao atribuir role ao usuário {$user->id}: " . $e->getMessage());
                }
            }

            $bar->finish();
            $this->newLine();
            $this->info("  ✅ Role '{$roleName}' atribuído com sucesso a {$usersWithoutRoles->count()} usuário(s)!");

            return Command::SUCCESS;
        });
    }
}
