<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('modulos_anexos', function (Blueprint $table) {
            // Adiciona campo para distinguir entre arquivo e link
            $table->string('forma_anexo')->default('arquivo')->after('anexavel_type');

            // Adiciona campo para armazenar URLs quando forma_anexo = 'link'
            $table->text('link')->nullable()->after('caminho_arquivo');

            // Adiciona campo para tipo de documento (Boleto, Nota Fiscal, etc.)
            // Diferente de tipo_arquivo que é o mime type
            $table->string('tipo_anexo')->nullable()->after('tipo_arquivo');

            // Torna campos de arquivo nullable, pois quando for link não terá arquivo
            $table->string('nome_arquivo')->nullable()->change();
            $table->string('caminho_arquivo')->nullable()->change();
            $table->string('tipo_arquivo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modulos_anexos', function (Blueprint $table) {
            // Reverte os campos para não nullable (se necessário)
            $table->string('nome_arquivo')->nullable(false)->change();
            $table->string('caminho_arquivo')->nullable(false)->change();
            $table->string('tipo_arquivo')->nullable(false)->change();

            // Remove os campos adicionados
            $table->dropColumn(['forma_anexo', 'link', 'tipo_anexo']);
        });
    }
};
