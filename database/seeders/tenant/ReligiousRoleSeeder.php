<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligiousRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Irmão',          'slug' => 'irmao',          'sort_order' => 1],
            ['name' => 'Caminho ao presbiterato', 'slug' => 'caminho_ao_presbiterato', 'sort_order' => 2],
            ['name' => 'Diácono',        'slug' => 'diacono',        'sort_order' => 3],
            ['name' => 'Presbítero',     'slug' => 'presbitero',     'sort_order' => 4],
            ['name' => 'Bispo',          'slug' => 'bispo',          'sort_order' => 5],
            ['name' => 'Bispo Emérito',  'slug' => 'bispo_emerito',  'sort_order' => 6],
        ];

        foreach ($roles as $role) {
            DB::table('religious_roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role + ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
