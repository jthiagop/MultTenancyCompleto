<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\Tenant\ReligiousRoleSeeder;
use Database\Seeders\Tenant\FormationStageSeeder;
use Database\Seeders\Tenant\MinistryTypeSeeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usar firstOrCreate para evitar duplicatas
        Role::firstOrCreate(
            ['name' => 'global', 'guard_name' => 'web'],
            ['description' => 'Acesso global à todos os recursos']
        );

        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Acesso global à maioria dos recursos']
        );

        Role::firstOrCreate(
            ['name' => 'admin_user', 'guard_name' => 'web'],
            ['description' => 'Acesso acessa a filial como um administrador local']
        );

        Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'web'],
            ['description' => 'Eles podem visualizar suas próprias transações, gerar relatórios e acompanhar seu histórico financeiro.']
        );

        Role::firstOrCreate(
            ['name' => 'sub_user', 'guard_name' => 'web'],
            ['description' => 'Ideal para pessoas que precisam visualizar dados de conteúdo, mas não precisa fazer quaisquer atualizações']
        );

        // Chama o nosso novo seeder de bancos
        $this->call(BankSeeder::class);

        // Chama o seeder de formas de pagamento
        $this->call(FormasPagamentoSeeder::class);

        // Chama o seeder de profissões
        $this->call(ProfissoesSeeder::class);

        // Chama o seeder de permissões
        $this->call(PermissionSeeder::class);

        // Associar permissões padrão aos roles
        $this->assignDefaultRolePermissions();

        // Chama o seeder de módulos
        $this->call(ModuleSeeder::class);

        // Chama o seeder de integrações
        $this->call(IntegracaoSeeder::class);

        // Chama os seeders de secretaria
        $this->call(ReligiousRoleSeeder::class);
        $this->call(FormationStageSeeder::class);
        $this->call(MinistryTypeSeeder::class);
    }

    /**
     * Associa permissões padrão a cada role.
     * Usa syncPermissions para ser idempotente.
     */
    private function assignDefaultRolePermissions(): void
    {
        $allPermissions = \Spatie\Permission\Models\Permission::where('guard_name', 'web')->get();

        if ($allPermissions->isEmpty()) {
            return;
        }

        // Role global → TODAS as permissões
        $globalRole = Role::where('name', 'global')->first();
        if ($globalRole) {
            $globalRole->syncPermissions($allPermissions);
        }

        // Role admin → TODAS as permissões
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->syncPermissions($allPermissions);
        }

        // Role admin_user → Módulos operacionais (sem company management)
        $adminUserRole = Role::where('name', 'admin_user')->first();
        if ($adminUserRole) {
            $adminUserPermissions = $allPermissions->filter(function ($permission) {
                $module = explode('.', $permission->name)[0];
                // admin_user acessa tudo exceto gerenciamento de companies
                return !in_array($module, ['company']);
            });
            $adminUserRole->syncPermissions($adminUserPermissions);
        }

        // Role user → Visualizar, criar e editar (sem delete e sem company/users)
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userPermissions = $allPermissions->filter(function ($permission) {
                $parts = explode('.', $permission->name);
                $action = end($parts);
                $module = $parts[0];
                // user pode tudo exceto delete e gerenciamento de companies/users
                return !in_array($action, ['delete'])
                    && !in_array($module, ['company', 'users']);
            });
            $userRole->syncPermissions($userPermissions);
        }

        // Role sub_user → Somente visualização (index + show)
        $subUserRole = Role::where('name', 'sub_user')->first();
        if ($subUserRole) {
            $subUserPermissions = $allPermissions->filter(function ($permission) {
                $parts = explode('.', $permission->name);
                $action = end($parts);
                $module = $parts[0];
                // sub_user pode apenas visualizar, sem company/users
                return in_array($action, ['index', 'show'])
                    && !in_array($module, ['company', 'users']);
            });
            $subUserRole->syncPermissions($subUserPermissions);
        }
    }
}
