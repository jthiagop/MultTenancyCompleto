<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormationStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['name' => 'Vocacionado', 'slug' => 'vocacionado', 'sort_order' => 1],
            ['name' => 'Postulantado I', 'slug' => 'postulantado_1', 'sort_order' => 2],
            ['name' => 'Postulantado II', 'slug' => 'postulantado_2', 'sort_order' => 3],
            ['name' => 'Noviciado', 'slug' => 'noviciado', 'sort_order' => 4],
            ['name' => 'Pós-noviciado', 'slug' => 'pos_noviciado', 'sort_order' => 5],
            ['name' => 'Votos Temporários', 'slug' => 'votos_temporarios', 'sort_order' => 6],
            ['name' => 'Votos Perpétuos', 'slug' => 'votos_perpetuos', 'sort_order' => 7],
        ];

        foreach ($stages as $stage) {
            DB::table('formation_stages')->updateOrInsert(
                ['slug' => $stage['slug']],
                $stage + ['is_active' => true]
            );
        }
    }
}