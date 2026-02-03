<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adiciona transacao_parcela_id para vincular o parcelamento à transação filha
     */
    public function up(): void
    {
        Schema::table('parcelamentos', function (Blueprint $table) {
            // Verifica se a coluna já existe antes de adicionar
            if (!Schema::hasColumn('parcelamentos', 'transacao_parcela_id')) {
                $table->foreignId('transacao_parcela_id')
                    ->nullable()
                    ->after('transacao_financeira_id')
                    ->constrained('transacoes_financeiras')
                    ->onDelete('cascade')
                    ->comment('Transação financeira filha (a parcela individual)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parcelamentos', function (Blueprint $table) {
            if (Schema::hasColumn('parcelamentos', 'transacao_parcela_id')) {
                $table->dropForeign(['transacao_parcela_id']);
                $table->dropColumn('transacao_parcela_id');
            }
        });
    }
};
