<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     */
    public function up(): void
    {
        // 1. Renomeia a tabela 'cadastro_bancos' para 'banks'.
        // Se a tabela já foi renomeada por uma tentativa anterior, este comando pode falhar.
        // Se isso acontecer, você pode comentar esta linha e rodar de novo.
        if (Schema::hasTable('cadastro_bancos')) {
            Schema::rename('cadastro_bancos', 'banks');
        }

        // 2. Modifica a tabela 'banks' para ser uma lista mestra de instituições.
        Schema::table('banks', function (Blueprint $table) {
            // Primeiro, remove as chaves estrangeiras para evitar erros.
            // Usamos um bloco try-catch para o caso de a chave já ter sido removida.
            try {
                $table->dropForeign('cadastro_bancos_company_id_foreign');
                $table->dropForeign('cadastro_bancos_created_by_foreign');
            } catch (\Exception $e) {
                // Ignora o erro se a chave não existir
            }

            // Remove as colunas que não pertencem a uma instituição.
            $table->dropColumn([
                'company_id',
                'conta',
                'agencia',
                'digito',
                'account_type',
                'description',
                'created_by',
                'banco' // Coluna redundante
            ]);

            // Adiciona a coluna para o logo da instituição.
            $table->string('logo_path')->nullable()->after('name');
            $table->string('compe_code', 3)->nullable()->after('logo_path');
        });

        // 3. Adiciona a chave estrangeira na coluna 'banco_id' que já existe.
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->foreign('banco_id')->references('id')->on('banks')->onDelete('set null');
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down(): void
    {
        // Processo reverso para poder desfazer a migration
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->dropForeign(['banco_id']);
        });

        Schema::rename('banks', 'cadastro_bancos');

        Schema::table('cadastro_bancos', function (Blueprint $table) {
            // Recria a estrutura antiga
            $table->unsignedBigInteger('company_id');
            $table->string('conta');
            // ... (recriar todas as colunas antigas)
        });
    }
};