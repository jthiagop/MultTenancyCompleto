<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            ['name' => 'Banco do Brasil S.A', 'compe_code' => '001', 'logo_path' => 'brasil.svg'],
            ['name' => 'Banco Santander S.A', 'compe_code' => '033', 'logo_path' => 'santander.svg'],
            ['name' => 'Bradesco S.A', 'compe_code' => '237', 'logo_path' => 'bradesco.svg'],
            ['name' => 'Caixa Econômica Federal', 'compe_code' => '104', 'logo_path' => 'caixa.svg'],
            ['name' => 'Itaú Unibanco S.A', 'compe_code' => '341', 'logo_path' => 'itau.svg'],
            ['name' => 'Nu Pagamentos S.A (Nubank)', 'compe_code' => '260', 'logo_path' => 'nubank.svg'],
            ['name' => 'Banco Inter S.A', 'compe_code' => '077', 'logo_path' => 'inter.svg'],
            ['name' => 'Sicoob', 'compe_code' => '756', 'logo_path' => 'sicoob.svg'],
            ['name' => 'Sicredi', 'compe_code' => '748', 'logo_path' => 'sicredi.svg'],
            ['name' => 'Stone Pagamentos S.A', 'compe_code' => '197', 'logo_path' => 'stone.svg'],
            ['name' => 'PagSeguro Internet S.A', 'compe_code' => '290', 'logo_path' => 'pagseguro.svg'],
            ['name' => 'Mercado Pago', 'compe_code' => '323', 'logo_path' => 'mercadopago.svg'],
            ['name' => 'Unicred', 'compe_code' => '136', 'logo_path' => 'unicred.svg'],
        ];

        foreach ($banks as $bank) {
            // Usa firstOrCreate para evitar duplicatas se o seeder for rodado mais de uma vez
            Bank::firstOrCreate(['name' => $bank['name']], $bank);
        }
    }
}
