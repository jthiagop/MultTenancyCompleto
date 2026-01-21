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
        Schema::create('recorrencia_transacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recorrencia_id');
            $table->unsignedBigInteger('transacao_financeira_id');
            $table->unsignedBigInteger('movimentacao_id')->nullable();
            $table->date('data_geracao');
            $table->integer('numero_ocorrencia');

            $table->timestamps();

            // Foreign keys
            $table->foreign('recorrencia_id')->references('id')->on('recorrencias')->onDelete('cascade');
            $table->foreign('transacao_financeira_id')->references('id')->on('transacoes_financeiras')->onDelete('cascade');
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes')->onDelete('cascade');

            // Unique constraint para evitar duplicatas (nome curto para evitar erro de tamanho)
            $table->unique(['recorrencia_id', 'transacao_financeira_id'], 'rec_trans_unique');

            // Índices para performance
            $table->index('recorrencia_id');
            $table->index('transacao_financeira_id');
            $table->index('data_geracao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorrencia_transacoes');
    }
};
