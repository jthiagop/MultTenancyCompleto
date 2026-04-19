<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacoes_rateios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transacao_financeira_id');
            $table->unsignedBigInteger('filial_id');
            $table->unsignedBigInteger('centro_custo_id')->nullable();
            $table->unsignedBigInteger('lancamento_padrao_id')->nullable();
            $table->decimal('valor', 15, 2);
            $table->decimal('percentual', 5, 2);
            $table->timestamps();

            $table->foreign('transacao_financeira_id')
                ->references('id')->on('transacoes_financeiras')
                ->onDelete('cascade');

            $table->foreign('filial_id')
                ->references('id')->on('companies')
                ->onDelete('cascade');

            $table->foreign('centro_custo_id')
                ->references('id')->on('cost_centers')
                ->nullOnDelete();

            $table->foreign('lancamento_padrao_id')
                ->references('id')->on('lancamento_padraos')
                ->nullOnDelete();

            $table->index('transacao_financeira_id');
            $table->index('filial_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacoes_rateios');
    }
};
