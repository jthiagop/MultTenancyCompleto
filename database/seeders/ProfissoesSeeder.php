<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profissao;

class ProfissoesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $profissoes = [
            ['nome' => 'Médico', 'popularidade' => 1],
            ['nome' => 'Enfermeiro', 'popularidade' => 2],
            ['nome' => 'Professor', 'popularidade' => 3],
            ['nome' => 'Advogado', 'popularidade' => 4],
            ['nome' => 'Engenheiro', 'popularidade' => 5],
            ['nome' => 'Veterinário', 'popularidade' => 6],
            ['nome' => 'Dentista', 'popularidade' => 7],
            ['nome' => 'Psicólogo', 'popularidade' => 8],
            ['nome' => 'Farmacêutico', 'popularidade' => 9],
            ['nome' => 'Contador', 'popularidade' => 10],
            ['nome' => 'Administrador', 'popularidade' => 11],
            ['nome' => 'Arquiteto', 'popularidade' => 12],
            ['nome' => 'Policial', 'popularidade' => 13],
            ['nome' => 'Bombeiro', 'popularidade' => 14],
            ['nome' => 'Piloto', 'popularidade' => 15],
            ['nome' => 'Chef de Cozinha', 'popularidade' => 16],
            ['nome' => 'Jornalista', 'popularidade' => 17],
            ['nome' => 'Publicitário', 'popularidade' => 18],
            ['nome' => 'Designer', 'popularidade' => 19],
            ['nome' => 'Programador', 'popularidade' => 20],
            ['nome' => 'Analista de Sistemas', 'popularidade' => 21],
            ['nome' => 'Vendedor', 'popularidade' => 22],
            ['nome' => 'Comerciante', 'popularidade' => 23],
            ['nome' => 'Motorista', 'popularidade' => 24],
            ['nome' => 'Eletricista', 'popularidade' => 25],
            ['nome' => 'Encanador', 'popularidade' => 26],
            ['nome' => 'Pedreiro', 'popularidade' => 27],
            ['nome' => 'Carpinteiro', 'popularidade' => 28],
            ['nome' => 'Pintor', 'popularidade' => 29],
            ['nome' => 'Mecânico', 'popularidade' => 30],
            ['nome' => 'Barbeiro', 'popularidade' => 31],
            ['nome' => 'Cabeleireiro', 'popularidade' => 32],
            ['nome' => 'Esteticista', 'popularidade' => 33],
            ['nome' => 'Massagista', 'popularidade' => 34],
            ['nome' => 'Personal Trainer', 'popularidade' => 35],
            ['nome' => 'Nutricionista', 'popularidade' => 36],
            ['nome' => 'Fisioterapeuta', 'popularidade' => 37],
            ['nome' => 'Fonoaudiólogo', 'popularidade' => 38],
            ['nome' => 'Terapeuta Ocupacional', 'popularidade' => 39],
            ['nome' => 'Assistente Social', 'popularidade' => 40],
            ['nome' => 'Bibliotecário', 'popularidade' => 41],
            ['nome' => 'Tradutor', 'popularidade' => 42],
            ['nome' => 'Fotógrafo', 'popularidade' => 43],
            ['nome' => 'Videomaker', 'popularidade' => 44],
            ['nome' => 'Músico', 'popularidade' => 45],
            ['nome' => 'Ator', 'popularidade' => 46],
            ['nome' => 'Bancário', 'popularidade' => 47],
            ['nome' => 'Corretor de Imóveis', 'popularidade' => 48],
            ['nome' => 'Corretor de Seguros', 'popularidade' => 49],
            ['nome' => 'Agente de Viagens', 'popularidade' => 50],
        ];

        foreach ($profissoes as $profissao) {
            Profissao::updateOrCreate(
                ['nome' => $profissao['nome']],
                [
                    'popularidade' => $profissao['popularidade'],
                    'ativo' => true
                ]
            );
        }
    }
}
