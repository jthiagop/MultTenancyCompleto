<?php

namespace Database\Seeders;

use App\Models\LancamentoPadrao;
use Illuminate\Database\Seeder;

/**
 * Cria os Lançamentos Padrão necessários para transferências entre contas.
 *
 * Executar: php artisan tenants:seed --class=TransferenciaLancamentoPadraoSeeder
 */
class TransferenciaLancamentoPadraoSeeder extends Seeder
{
    public function run(): void
    {
        $companyId = session('active_company_id');

        if (!$companyId) {
            $this->command?->warn('Nenhuma empresa ativa na sessão. O seeder criará os LPs sem company_id.');
        }

        $lps = [
            [
                'description' => 'Transferência de Saída',
                'type' => 'saida',
                'category' => 'Transferência',
            ],
            [
                'description' => 'Transferência de Entrada',
                'type' => 'entrada',
                'category' => 'Transferência',
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

        $this->command?->info('Lançamentos Padrão de Transferência criados com sucesso!');
    }
}
