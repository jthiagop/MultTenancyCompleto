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
            ['name' => 'Banco do Brasil S.A', 'compe_code' => '001', 'logo_path' => '/assets/media/svg/bancos/brasil.svg'],
            ['name' => 'Banco Santander S.A', 'compe_code' => '033', 'logo_path' => '/assets/media/svg/bancos/santander.svg'],
            ['name' => 'Bradesco S.A', 'compe_code' => '237', 'logo_path' => '/assets/media/svg/bancos/bradesco.svg'],
            ['name' => 'Caixa Econômica Federal', 'compe_code' => '104', 'logo_path' => '/assets/media/svg/bancos/caixa.svg'],
            ['name' => 'Itaú Unibanco S.A', 'compe_code' => '341', 'logo_path' => '/assets/media/svg/bancos/itau.svg'],
            ['name' => 'Nu Pagamentos S.A (Nubank)', 'compe_code' => '260', 'logo_path' => '/assets/media/svg/bancos/nubank.svg'],
            ['name' => 'Banco Inter S.A', 'compe_code' => '077', 'logo_path' => '/assets/media/svg/bancos/inter.svg'],
            ['name' => 'Sicoob', 'compe_code' => '756', 'logo_path' => '/assets/media/svg/bancos/sicoob.svg'],
            ['name' => 'Sicredi', 'compe_code' => '748', 'logo_path' => '/assets/media/svg/bancos/sicredi.svg'],
            ['name' => 'Stone Pagamentos S.A', 'compe_code' => '197', 'logo_path' => '/assets/media/svg/bancos/stone.svg'],
            ['name' => 'PagSeguro Internet S.A', 'compe_code' => '290', 'logo_path' => '/assets/media/svg/bancos/pagseguro.svg'],
            ['name' => 'Mercado Pago', 'compe_code' => '323', 'logo_path' => '/assets/media/svg/bancos/mercadopago.svg'],
            ['name' => 'Unicred', 'compe_code' => '136', 'logo_path' => '/assets/media/svg/bancos/unicred.svg'],
        ];

        foreach ($banks as $bank) {
            // Usa firstOrCreate para evitar duplicatas se o seeder for rodado mais de uma vez
            Bank::firstOrCreate(['name' => $bank['name']], $bank);
        }
    }
}
