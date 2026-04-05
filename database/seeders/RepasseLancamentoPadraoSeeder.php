<?php

namespace Database\Seeders;

use App\Models\LancamentoPadrao;
use Illuminate\Database\Seeder;

/**
 * Cria os Lançamentos Padrão necessários para repasses entre matriz e filiais.
 *
 * Executar: php artisan tenants:seed --class=RepasseLancamentoPadraoSeeder
 */
class RepasseLancamentoPadraoSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            $this->command?->warn('Nenhuma empresa ativa na sessão. O seeder criará os LPs sem company_id.');
        }

        $lps = [
            [
                'description' => 'Repasse Enviado',
                'type' => 'saida',
                'category' => 'Repasse',
            ],
            [
                'description' => 'Repasse Recebido',
                'type' => 'entrada',
                'category' => 'Repasse',
            ],
        ];

        foreach ($lps as $lp) {
            LancamentoPadrao::firstOrCreate(
                [
                    'description' => $lp['description'],
                    'company_id' => $companyId,
                ],
                [
                    'type' => $lp['type'],
                    'category' => $lp['category'],
                    'company_id' => $companyId,
                    'user_id' => 1,
                ]
            );
        }

        $this->command?->info('Lançamentos Padrão de Repasse criados com sucesso!');
    }
}
