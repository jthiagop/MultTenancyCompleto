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
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Adiciona a coluna para a conta de débito, após a coluna 'lancamento_padrao_id'
            $table->foreignId('conta_debito_id')
                  ->nullable()
                  ->constrained('chart_of_accounts')
                  ->onDelete('set null') // Se a conta for apagada, o ID na transação fica nulo
                  ->after('lancamento_padrao_id');

            // Adiciona a coluna para a conta de crédito, após a de débito
            $table->foreignId('conta_credito_id')
                  ->nullable()
                  ->constrained('chart_of_accounts')
                  ->onDelete('set null') // Se a conta for apagada, o ID na transação fica nulo
                  ->after('conta_debito_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Remove as chaves estrangeiras primeiro para evitar erros
            $table->dropForeign(['conta_debito_id']);
            $table->dropForeign(['conta_credito_id']);

            // Remove as colunas
            $table->dropColumn(['conta_debito_id', 'conta_credito_id']);
        });
    }
};
