<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prefixos possíveis a serem removidos dos caminhos de logo existentes.
     */
    private const LOGO_PREFIXES = [
        '/tenancy/assets/media/svg/bancos/',
        '/assets/media/svg/bancos/',
    ];

    /**
     * Run the migrations.
     * 
     * Converte caminhos completos para slugs:
     * /tenancy/assets/media/svg/bancos/brasil.svg -> brasil.svg
     * /assets/media/svg/bancos/brasil.svg -> brasil.svg
     */
    public function up(): void
    {
        // Atualiza todos os registros que têm qualquer um dos prefixos
        DB::table('banks')
            ->where(function ($query) {
                foreach (self::LOGO_PREFIXES as $prefix) {
                    $query->orWhere('logo_path', 'LIKE', $prefix . '%');
                }
            })
            ->get()
            ->each(function ($bank) {
                // Extrai apenas o nome do arquivo (slug)
                $slug = basename($bank->logo_path);
                
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
                    ->update(['logo_path' => self::LOGO_PREFIXES[0] . $bank->logo_path]);
            });
    }
};
