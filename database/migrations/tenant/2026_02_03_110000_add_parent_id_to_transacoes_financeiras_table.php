<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona parent_id para suportar parcelamentos hierárquicos:
     * - Transação PAI: valor total, situacao='parcelado'
     * - Transações FILHAS: valor individual de cada parcela, com parent_id apontando para o PAI
     */
    public function up(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Adiciona coluna parent_id para vincular parcelas à transação pai
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('transacoes_financeiras')
                ->onDelete('cascade');
            
            // Índice para melhorar performance de consultas
            $table->index('parent_id', 'idx_transacoes_parent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex('idx_transacoes_parent');
            $table->dropColumn('parent_id');
        });
    }
};
