<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MinistryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Leitorado',    'slug' => 'leitorado',    'sort_order' => 1],
            ['name' => 'Acolitato',    'slug' => 'acolitato',    'sort_order' => 2],
            ['name' => 'Diaconato',    'slug' => 'diaconato',    'sort_order' => 3],
            ['name' => 'Presbiterato', 'slug' => 'presbiterato', 'sort_order' => 4],
            ['name' => 'Episcopado',   'slug' => 'episcopado',   'sort_order' => 5],
        ];

        foreach ($types as $type) {
            DB::table('ministry_types')->updateOrInsert(
                ['slug' => $type['slug']],
                $type + [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
