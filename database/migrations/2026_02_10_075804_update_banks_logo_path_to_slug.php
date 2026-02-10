<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prefixo a ser removido dos caminhos de logo existentes.
     */
    private const LOGO_PREFIX = '/tenancy/assets/media/svg/bancos/';

    /**
     * Run the migrations.
     * 
     * Converte caminhos completos para slugs:
     * /tenancy/assets/media/svg/bancos/brasil.svg -> brasil.svg
     */
    public function up(): void
    {
        // Atualiza todos os registros que têm o prefixo completo
        DB::table('banks')
            ->where('logo_path', 'LIKE', self::LOGO_PREFIX . '%')
            ->get()
            ->each(function ($bank) {
                $slug = str_replace(self::LOGO_PREFIX, '', $bank->logo_path);
                
                DB::table('banks')
                    ->where('id', $bank->id)
                    ->update(['logo_path' => $slug]);
            });
    }

    /**
     * Reverse the migrations.
     * 
     * Restaura os caminhos completos a partir dos slugs.
     */
    public function down(): void
    {
        // Restaura o prefixo completo para registros que são apenas slugs
        DB::table('banks')
            ->whereNotNull('logo_path')
            ->where('logo_path', 'NOT LIKE', '/%')
            ->where('logo_path', 'NOT LIKE', 'http%')
            ->get()
            ->each(function ($bank) {
                DB::table('banks')
                    ->where('id', $bank->id)
                    ->update(['logo_path' => self::LOGO_PREFIX . $bank->logo_path]);
            });
    }
};
