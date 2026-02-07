<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Facades\Schema;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:sync-all {--fresh : Remove permissões órfãs que não estão mais no seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza todas as permissões, associações de roles e módulos para o tenant atual';

    /**
     * Lista completa de permissões do sistema.
     */
    private function getPermissions(): array
    {
        return [
            // Financeiro
            'financeiro.index', 'financeiro.create', 'financeiro.edit', 'financeiro.delete', 'financeiro.show',
            // Patrimônio
            'patrimonio.index', 'patrimonio.create', 'patrimonio.edit', 'patrimonio.delete', 'patrimonio.show',
            // Contabilidade
            'contabilidade.index',
            'contabilidade.plano-contas.index', 'contabilidade.plano-contas.create',
            'contabilidade.plano-contas.edit', 'contabilidade.plano-contas.delete',
            'contabilidade.plano-contas.import', 'contabilidade.plano-contas.export',
            'contabilidade.mapeamento.index', 'contabilidade.mapeamento.store', 'contabilidade.mapeamento.delete',
            // Fiéis
            'fieis.index', 'fieis.create', 'fieis.edit', 'fieis.delete', 'fieis.show',
            // Cemitério
            'cemiterio.index', 'cemiterio.create', 'cemiterio.edit', 'cemiterio.delete', 'cemiterio.show',
            // Nota Fiscal
            'notafiscal.index', 'notafiscal.create', 'notafiscal.edit', 'notafiscal.delete', 'notafiscal.show',
            // Dízimo e Doações
            'dizimos.index', 'dizimos.create', 'dizimos.edit', 'dizimos.delete', 'dizimos.show',
            // Secretaria
            'secretary.index', 'secretary.create', 'secretary.edit', 'secretary.delete', 'secretary.show',
            // Organismos
            'company.index', 'company.create', 'company.edit', 'company.delete', 'company.show',
            // Usuários
            'users.index', 'users.create', 'users.edit', 'users.delete', 'users.show',
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Sincronizando permissões do sistema...');
        $this->newLine();

        // 1. Criar/verificar permissões
        $this->syncPermissions();

        // 2. Associar permissões aos roles
        $this->syncRolePermissions();

        // 3. Atualizar módulo dizimos (corrigir permission null)
        $this->fixModulePermissions();

        // 4. Criar módulo secretary se não existir
        $this->ensureSecretaryModule();

        // 5. Atribuir novas permissões a usuários admin existentes
        $this->syncAdminUserPermissions();

        // 6. Limpar cache do Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->newLine();
        $this->info('✅ Sincronização completa!');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Permissões no sistema', Permission::where('guard_name', 'web')->count()],
                ['Roles no sistema', Role::count()],
                ['Módulos ativos', Schema::hasTable('modules') ? Module::where('is_active', true)->count() : 'N/A'],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Cria permissões que ainda não existem no banco.
     */
    private function syncPermissions(): void
    {
        $this->info('📋 Etapa 1: Sincronizando permissões...');

        $permissions = $this->getPermissions();
        $created = 0;
        $existing = 0;

        foreach ($permissions as $name) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );

            if ($permission->wasRecentlyCreated) {
                $created++;
                $this->line("  <fg=green>✓</> Criada: {$name}");
            } else {
                $existing++;
            }
        }

        $this->info("  → {$created} novas, {$existing} já existiam");

        // Remover permissões órfãs se --fresh
        if ($this->option('fresh')) {
            $orphaned = Permission::where('guard_name', 'web')
                ->whereNotIn('name', $permissions)
                ->get();

            if ($orphaned->isNotEmpty()) {
                foreach ($orphaned as $orphan) {
                    $this->warn("  <fg=red>✗</> Removida órfã: {$orphan->name}");
                    $orphan->delete();
                }
                $this->info("  → {$orphaned->count()} permissões órfãs removidas");
            }
        }
    }

    /**
     * Associa permissões padrão a cada role.
     */
    private function syncRolePermissions(): void
    {
        $this->info('🔐 Etapa 2: Associando permissões aos roles...');

        $allPermissions = Permission::where('guard_name', 'web')->get();

        // Role global → TODAS
        $this->assignToRole('global', $allPermissions);

        // Role admin → TODAS
        $this->assignToRole('admin', $allPermissions);

        // Role admin_user → Tudo exceto company
        $adminUserPerms = $allPermissions->filter(function ($p) {
            $module = explode('.', $p->name)[0];
            return !in_array($module, ['company']);
        });
        $this->assignToRole('admin_user', $adminUserPerms);

        // Role user → Sem delete, sem company/users
        $userPerms = $allPermissions->filter(function ($p) {
            $parts = explode('.', $p->name);
            $action = end($parts);
            $module = $parts[0];
            return !in_array($action, ['delete']) && !in_array($module, ['company', 'users']);
        });
        $this->assignToRole('user', $userPerms);

        // Role sub_user → Somente index/show, sem company/users
        $subUserPerms = $allPermissions->filter(function ($p) {
            $parts = explode('.', $p->name);
            $action = end($parts);
            $module = $parts[0];
            return in_array($action, ['index', 'show']) && !in_array($module, ['company', 'users']);
        });
        $this->assignToRole('sub_user', $subUserPerms);
    }

    /**
     * Atribui permissões a um role específico.
     */
    private function assignToRole(string $roleName, $permissions): void
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $role->syncPermissions($permissions);
            $this->line("  <fg=cyan>→</> {$roleName}: {$permissions->count()} permissões");
        } else {
            $this->warn("  ⚠ Role '{$roleName}' não encontrado");
        }
    }

    /**
     * Corrige o módulo dizimos que tinha permission: null.
     */
    private function fixModulePermissions(): void
    {
        $this->info('📦 Etapa 3: Verificando módulos...');

        if (!Schema::hasTable('modules')) {
            $this->warn('  ⚠ Tabela modules não existe');
            return;
        }

        // Corrigir dizimos
        $dizimosModule = Module::where('key', 'dizimos')->whereNull('permission')->first();
        if ($dizimosModule) {
            $dizimosModule->update(['permission' => 'dizimos.index']);
            $this->line("  <fg=green>✓</> Módulo 'dizimos' corrigido: permission = 'dizimos.index'");
        } else {
            $this->line("  → Módulo 'dizimos' já está correto");
        }
    }

    /**
     * Cria o módulo secretary se não existir.
     */
    private function ensureSecretaryModule(): void
    {
        if (!Schema::hasTable('modules')) {
            return;
        }

        $existing = Module::withTrashed()->where('key', 'secretary')->first();

        if (!$existing) {
            // Buscar companies existentes para criar para cada uma
            $companies = Schema::hasTable('companies') ? \App\Models\Company::all() : collect([null]);

            if ($companies->isEmpty()) {
                $companies = collect([null]);
            }

            foreach ($companies as $company) {
                Module::create([
                    'company_id' => $company ? $company->id : null,
                    'key' => 'secretary',
                    'name' => 'Secretaria',
                    'route_name' => 'secretary.index',
                    'icon_path' => '/assets/media/png/secretaria.png',
                    'icon_class' => 'fa-file-lines',
                    'permission' => 'secretary.index',
                    'description' => 'Gerenciamento de membros religiosos e secretaria',
                    'order_index' => 7,
                    'is_active' => true,
                    'show_on_dashboard' => true,
                ]);
            }
            $this->line("  <fg=green>✓</> Módulo 'secretary' criado");
        } elseif ($existing->trashed()) {
            $existing->restore();
            $this->line("  <fg=green>✓</> Módulo 'secretary' restaurado");
        } else {
            $this->line("  → Módulo 'secretary' já existe");
        }
    }

    /**
     * Garante que usuários admin existentes recebam as novas permissões.
     */
    private function syncAdminUserPermissions(): void
    {
        $this->info('👤 Etapa 4: Sincronizando permissões de usuários admin...');

        $newPermissionNames = [
            'dizimos.index', 'dizimos.create', 'dizimos.edit', 'dizimos.delete', 'dizimos.show',
            'secretary.index', 'secretary.create', 'secretary.edit', 'secretary.delete', 'secretary.show',
        ];

        $adminRoleNames = ['global', 'admin', 'admin_user'];
        $updatedCount = 0;

        try {
            $validRoles = Role::whereIn('name', $adminRoleNames)->pluck('name')->toArray();

            if (!empty($validRoles)) {
                $users = User::role($validRoles)->get();

                foreach ($users as $user) {
                    foreach ($newPermissionNames as $permName) {
                        $permission = Permission::where('name', $permName)->first();
                        if ($permission && !$user->hasPermissionTo($permission)) {
                            $user->givePermissionTo($permission);
                            $updatedCount++;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->warn("  ⚠ Erro: " . $e->getMessage());
        }

        $this->line("  → {$updatedCount} permissões atribuídas a usuários admin");
    }
}
