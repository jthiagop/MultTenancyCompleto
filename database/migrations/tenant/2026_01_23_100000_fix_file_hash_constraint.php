<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa as migrações.
     * Remove a constraint UNIQUE incorreta de file_hash
     * Adiciona a constraint composta correta para evitar duplicação de transações
     */
    public function up()
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            // Remove a constraint única incorreta de file_hash se existir
            try {
                $table->dropUnique('bank_statements_file_hash_unique');
            } catch (\Exception $e) {
                // Ignora se a constraint não existir
                \Log::info('Constraint bank_statements_file_hash_unique não encontrada, continuando...');
            }
        });

        Schema::table('bank_statements', function (Blueprint $table) {
            // Adiciona a chave composta CORRETA
            // Isso garante que não há duplicação de (fitid + dtposted + entidade_financeira_id)
            // Mesmo que o file_hash seja igual (múltiplas transações do mesmo arquivo)
            $table->unique(['fitid', 'dtposted', 'entidade_financeira_id'], 'unique_ofx_transaction_v2');
        });
    }

    /**
     * Reverte as migrações.
     */
    public function down()
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            // Remove a constraint composta
            try {
                $table->dropUnique('unique_ofx_transaction_v2');
            } catch (\Exception $e) {
                // Ignora se não existir
            }

            // Re-adiciona a constraint única de file_hash (estado anterior)
            $table->unique('file_hash');
        });
    }
};
