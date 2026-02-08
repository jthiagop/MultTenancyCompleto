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

        // 3. Garantir que todos os módulos existam (registro global)
        $this->syncModules();

        // 4. Atribuir novas permissões a usuários admin existentes
        $this->syncAdminUserPermissions();

        // 5. Limpar cache do Spatie
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

        // Role authenticated → NENHUMA permissão (apenas para middleware de rotas)
        // Usada quando admin customiza permissões manualmente — todas viram diretas
        $this->ensureRoleExists('authenticated');
        $this->assignToRole('authenticated', collect([]));
    }

    /**
     * Garante que uma role exista no sistema.
     */
    private function ensureRoleExists(string $roleName): void
    {
        $role = Role::firstOrCreate(
            ['name' => $roleName, 'guard_name' => 'web']
        );
        if ($role->wasRecentlyCreated) {
            $this->line("  <fg=green>✓</> Role criada: {$roleName}");
        }
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
     * Garante que todos os módulos existam como registros globais.
     * Módulos agora são definidos uma única vez (sem company_id).
     */
    private function syncModules(): void
    {
        $this->info('📦 Etapa 3: Sincronizando módulos (registro global)...');

        if (!Schema::hasTable('modules')) {
            $this->warn('  ⚠ Tabela modules não existe');
            return;
        }

        $moduleDefinitions = [
            ['key' => 'financeiro', 'name' => 'Financeiro', 'route_name' => 'financeiro.index', 'icon_path' => '/assets/media/png/financeiro.svg', 'icon_class' => 'fa-money-bill', 'permission' => 'financeiro.index', 'description' => 'Cadastros financeiros, movimentações', 'order_index' => 1],
            ['key' => 'patrimonio', 'name' => 'Patrimônio', 'route_name' => 'patrimonio.index', 'icon_path' => '/assets/media/png/house3d.png', 'icon_class' => 'fa-building', 'permission' => 'patrimonio.index', 'description' => 'Gestão patrimonial, foro e laudêmio', 'order_index' => 2],
            ['key' => 'contabilidade', 'name' => 'Contabilidade', 'route_name' => 'contabilidade.index', 'icon_path' => '/assets/media/png/contabilidade.png', 'icon_class' => 'fa-calculator', 'permission' => 'contabilidade.index', 'description' => 'Gerenciar plano de contas e DE/PARA', 'order_index' => 3],
            ['key' => 'dizimos', 'name' => 'Dízimo e Doações', 'route_name' => 'dizimos.index', 'icon_path' => '/assets/media/png/dizimo.png', 'icon_class' => 'fa-hand-holding-dollar', 'permission' => 'dizimos.index', 'description' => 'Gerenciamento de dízimo e doações', 'order_index' => 4],
            ['key' => 'fieis', 'name' => 'Cadastro de Fiéis', 'route_name' => 'fieis.index', 'icon_path' => '/assets/media/png/fieis.png', 'icon_class' => 'fa-users', 'permission' => 'fieis.index', 'description' => 'Gerenciamento de membros e contribuições', 'order_index' => 5],
            ['key' => 'cemiterio', 'name' => 'Cadastro de Sepulturas', 'route_name' => 'cemiterio.index', 'icon_path' => '/assets/media/png/lapide2.png', 'icon_class' => 'fa-cross', 'permission' => 'cemiterio.index', 'description' => 'Gerenciamento de sepultamentos, manutenção e pagamentos', 'order_index' => 6],
            ['key' => 'secretary', 'name' => 'Secretaria', 'route_name' => 'secretary.index', 'icon_path' => '/assets/media/png/secretaria.png', 'icon_class' => 'fa-file-lines', 'permission' => 'secretary.index', 'description' => 'Gerenciamento de membros religiosos e secretaria', 'order_index' => 7],
        ];

        $created = 0;
        $updated = 0;

        foreach ($moduleDefinitions as $moduleDef) {
            $existing = Module::withTrashed()->where('key', $moduleDef['key'])->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $this->line("  <fg=yellow>↻</> '{$moduleDef['name']}' restaurado");
                }

                // Atualizar permission se estava null
                if (!$existing->permission && $moduleDef['permission']) {
                    $existing->update(['permission' => $moduleDef['permission']]);
                    $updated++;
                    $this->line("  <fg=green>✓</> '{$moduleDef['name']}' permission corrigida");
                }
            } else {
                Module::create(array_merge($moduleDef, [
                    'is_active' => true,
                    'show_on_dashboard' => true,
                ]));
                $created++;
                $this->line("  <fg=green>✓</> '{$moduleDef['name']}' criado");
            }
        }

        $this->info("  → {$created} criados, {$updated} atualizados, " . Module::active()->count() . " ativos no total");
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