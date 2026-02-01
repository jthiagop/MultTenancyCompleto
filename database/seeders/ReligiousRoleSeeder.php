<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligiousRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Presbítero', 'slug' => 'presbitero', 'sort_order' => 1],
            ['name' => 'Diácono',    'slug' => 'diacono',    'sort_order' => 2],
            ['name' => 'Irmão',      'slug' => 'irmao',      'sort_order' => 3],
        ];

        foreach ($roles as $role) {
            DB::table('religious_roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role + ['is_active' => true]
            );
        }
    }
}