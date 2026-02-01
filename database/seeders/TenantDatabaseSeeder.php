<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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

        // Chama o seeder de módulos
        $this->call(ModuleSeeder::class);

        // Chama o seeder de integrações
        $this->call(IntegracaoSeeder::class);

        // Chama os seeders de secretaria
        $this->call(ReligiousRoleSeeder::class);
        $this->call(FormationStageSeeder::class);
    }
}
