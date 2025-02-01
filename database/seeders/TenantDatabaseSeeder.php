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
        Role::create(['name' => 'global'], ['descript'=>'Acesso global à todos os recursos']);
        Role::create(['name' => 'admin'], ['descript'=>'Acesso global à maioria dos recursos']);
        Role::create(['name' => 'admin_user'], ['descript'=>'Acesso acessa a filial como um administrador local']);
        Role::create(['name' => 'user'], ['descript'=> 'Eles podem visualizar suas próprias transações, gerar relatórios e acompanhar seu histórico financeiro.']);
        Role::create(['name' => 'sub_user'], ['descript'=>'Ideal para pessoas que precisam visualizar dados de conteúdo, mas não precisa fazer quaisquer atualizações']);
    }
}
