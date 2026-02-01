<?php

namespace Database\Seeders;

use App\Models\Province;
use App\Models\ReligiousRole;
use App\Models\FormationStage;
use App\Models\ReligiousMember;
use Illuminate\Database\Seeder;

class SecretaryTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Criar uma província de teste
        $provincia = Province::firstOrCreate([
            'slug' => 'sao-paulo'
        ], [
            'name' => 'São Paulo',
            'is_active' => true
        ]);

        // Obter as funções religiosas
        $presbitero = ReligiousRole::where('slug', 'presbitero')->first();
        $diacono = ReligiousRole::where('slug', 'diacono')->first();
        $irmao = ReligiousRole::where('slug', 'irmao')->first();

        // Obter as etapas de formação
        $vocacionado = FormationStage::where('slug', 'vocacionado')->first();
        $noviciado = FormationStage::where('slug', 'noviciado')->first();
        $votos_perpetuos = FormationStage::where('slug', 'votos_perpetuos')->first();

        // Membro 1 - Presbítero
        ReligiousMember::firstOrCreate([
            'name' => 'Pe. João Silva',
        ], [
            'province_id' => $provincia->id,
            'religious_role_id' => $presbitero->id,
            'current_stage_id' => $votos_perpetuos->id,
            'birth_date' => '1980-05-15',
            'temporary_profession_date' => '2005-12-08',
            'perpetual_profession_date' => '2010-12-08',
            'priestly_ordination_date' => '2012-06-15',
            'is_active' => true
        ]);

        // Membro 2 - Diácono
        ReligiousMember::firstOrCreate([
            'name' => 'Diácono Paulo Santos'
        ], [
            'province_id' => $provincia->id,
            'religious_role_id' => $diacono->id,
            'current_stage_id' => $votos_perpetuos->id,
            'birth_date' => '1985-03-20',
            'temporary_profession_date' => '2008-12-08',
            'perpetual_profession_date' => '2013-12-08',
            'diaconal_ordination_date' => '2015-05-30',
            'is_active' => true
        ]);

        // Membro 3 - Irmão com votos perpétuos
        ReligiousMember::firstOrCreate([
            'name' => 'Ir. Carlos Oliveira'
        ], [
            'province_id' => $provincia->id,
            'religious_role_id' => $irmao->id,
            'current_stage_id' => $votos_perpetuos->id,
            'birth_date' => '1990-08-10',
            'temporary_profession_date' => '2012-12-08',
            'perpetual_profession_date' => '2018-12-08',
            'is_active' => true
        ]);

        // Membro 4 - Irmão com votos temporários apenas (para tab Votos Simples)
        ReligiousMember::firstOrCreate([
            'name' => 'Ir. José Santos'
        ], [
            'province_id' => $provincia->id,
            'religious_role_id' => $irmao->id,
            'current_stage_id' => $noviciado->id,
            'birth_date' => '1995-03-25',
            'temporary_profession_date' => '2020-12-08',
            'is_active' => true
        ]);

        echo "Dados de teste da secretaria criados com sucesso!\n";
    }
}