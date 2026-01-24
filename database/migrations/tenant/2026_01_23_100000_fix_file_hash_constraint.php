<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Executa as migrações.
     * Remove a constraint UNIQUE incorreta de file_hash
     * Adiciona a constraint composta correta para evitar duplicação de transações
     */
    public function up()
    {
        // ✅ Verifica se o índice existe ANTES de tentar remover
        $connection = DB::connection()->getName();
        $database = DB::connection()->getDatabaseName();
        
        // Verifica se a constraint existe
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'bank_statements' 
            AND CONSTRAINT_NAME = 'bank_statements_file_hash_unique'
        ", [$database]);

        // Se existe, remove
        if (!empty($constraints)) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropUnique('bank_statements_file_hash_unique');
            });
        }

        // Verifica se a constraint composta já existe
        $compositeConstraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'bank_statements' 
            AND CONSTRAINT_NAME = 'unique_ofx_transaction_v2'
        ", [$database]);

        // Se não existe, cria
        if (empty($compositeConstraints)) {
            Schema::table('bank_statements', function (Blueprint $table) {
                // Adiciona a chave composta CORRETA
                // Isso garante que não há duplicação de (fitid + dtposted + entidade_financeira_id)
                // Mesmo que o file_hash seja igual (múltiplas transações do mesmo arquivo)
                $table->unique(['fitid', 'dtposted', 'entidade_financeira_id'], 'unique_ofx_transaction_v2');
            });
        }
    }

    /**
     * Reverte as migrações.
     */
    public function down()
    {
        $database = DB::connection()->getDatabaseName();

        // Verifica se a constraint composta existe
        $compositeConstraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'bank_statements' 
            AND CONSTRAINT_NAME = 'unique_ofx_transaction_v2'
        ", [$database]);

        // Se existe, remove
        if (!empty($compositeConstraints)) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->dropUnique('unique_ofx_transaction_v2');
            });
        }

        // Verifica se a constraint de file_hash não existe
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = ? 
            AND TABLE_NAME = 'bank_statements' 
            AND CONSTRAINT_NAME = 'bank_statements_file_hash_unique'
        ", [$database]);

        // Se não existe, re-adiciona
        if (empty($constraints)) {
            Schema::table('bank_statements', function (Blueprint $table) {
                $table->unique('file_hash');
            });
        }
    }
};
