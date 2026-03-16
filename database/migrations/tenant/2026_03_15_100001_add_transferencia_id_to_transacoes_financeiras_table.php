<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adiciona transferencia_id para vincular transações financeiras a transferências.
     */
    public function up(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->foreignId('transferencia_id')
                ->nullable()
                ->after('recorrencia_id')
                ->constrained('transferencias')
                ->onDelete('cascade')
                ->comment('Transferência que originou esta transação');

            $table->index('transferencia_id', 'idx_transacoes_transferencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['transferencia_id']);
            $table->dropIndex('idx_transacoes_transferencia');
            $table->dropColumn('transferencia_id');
        });
    }
};
