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
        // Adicionar índices à tabela bank_statements
        Schema::table('bank_statements', function (Blueprint $table) {
            // Verificar se o índice já não existe
            if (!Schema::hasIndex('bank_statements', 'idx_entidade_financeira_id')) {
                $table->index('entidade_financeira_id', 'idx_entidade_financeira_id');
            }
            if (!Schema::hasIndex('bank_statements', 'idx_company_id_status')) {
                $table->index(['company_id', 'status_conciliacao'], 'idx_company_id_status');
            }
        });

        // Adicionar índices à tabela transacoes_financeiras
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            if (!Schema::hasIndex('transacoes_financeiras', 'idx_entidade_id')) {
                $table->index('entidade_id', 'idx_entidade_id');
            }
            if (!Schema::hasIndex('transacoes_financeiras', 'idx_company_id')) {
                $table->index('company_id', 'idx_company_id');
            }
        });

        // Adicionar índices à tabela entidades_financeiras
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            if (!Schema::hasIndex('entidades_financeiras', 'idx_company_id_tipo')) {
                $table->index(['company_id', 'tipo'], 'idx_company_id_tipo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_entidade_financeira_id');
            $table->dropIndexIfExists('idx_company_id_status');
        });

        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_entidade_id');
            $table->dropIndexIfExists('idx_company_id');
        });

        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_company_id_tipo');
        });
    }
};
