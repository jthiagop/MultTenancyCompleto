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
            $table->foreignId('conta_debito_id')->nullable()->constrained('chart_of_accounts')->after('lancamento_padrao_id');
            $table->foreignId('conta_credito_id')->nullable()->constrained('chart_of_accounts')->after('conta_debito_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Remove as chaves estrangeiras antes de remover as colunas
            $table->dropForeign(['conta_debito_id']);
            $table->dropForeign(['conta_credito_id']);

            // Remove as colunas
            $table->dropColumn(['conta_debito_id', 'conta_credito_id']);
        });
    }
};
