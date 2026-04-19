<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona reembolso_par_id para vincular bidirecionalmente:
     * - a saída criada na filial (origem='rateio')
     * - a entrada criada na matriz (origem='reembolso_rateio')
     *
     * Quando a filial paga seu boleto e a matriz marca a entrada como recebida,
     * o ciclo de reembolso intercompany fica completo e rastreável.
     */
    public function up(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->unsignedBigInteger('reembolso_par_id')->nullable()->after('rateio_origem_id');

            $table->foreign('reembolso_par_id')
                ->references('id')->on('transacoes_financeiras')
                ->nullOnDelete();

            $table->index('reembolso_par_id');
        });
    }

    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['reembolso_par_id']);
            $table->dropIndex(['reembolso_par_id']);
            $table->dropColumn('reembolso_par_id');
        });
    }
};
