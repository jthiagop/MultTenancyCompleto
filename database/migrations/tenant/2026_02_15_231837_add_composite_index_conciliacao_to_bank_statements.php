<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índice composto para otimizar as queries recorrentes de conciliação bancária.
     * 
     * As queries principais filtram por: company_id + entidade_financeira_id + status_conciliacao
     * Com 19k+ registros e crescendo, este índice é essencial para performance.
     */
    public function up(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            // Índice composto para queries de conciliação (company_id, entidade_financeira_id, status_conciliacao)
            $table->index(
                ['company_id', 'entidade_financeira_id', 'status_conciliacao'],
                'idx_bs_company_entidade_status'
            );

            // Índice para queries com ordenação por dtposted (paginação)
            $table->index(
                ['company_id', 'entidade_financeira_id', 'status_conciliacao', 'dtposted'],
                'idx_bs_conciliacao_dtposted'
            );

            // Remove o índice antigo menos eficiente (company_id, status_conciliacao) 
            // pois o novo índice composto cobre esse caso
            $table->dropIndex('idx_company_id_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropIndex('idx_bs_company_entidade_status');
            $table->dropIndex('idx_bs_conciliacao_dtposted');
            
            // Recria o índice antigo
            $table->index(['company_id', 'status_conciliacao'], 'idx_company_id_status');
        });
    }
};
