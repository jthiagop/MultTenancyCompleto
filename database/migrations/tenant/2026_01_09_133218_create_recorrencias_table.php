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
        Schema::create('recorrencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('transacao_financeira_id')->nullable();
            $table->unsignedBigInteger('movimentacao_id')->nullable();
            $table->integer('intervalo_repeticao');
            $table->enum('frequencia', ['diario', 'semanal', 'mensal', 'anual']);
            $table->integer('total_ocorrencias');
            $table->integer('ocorrencias_geradas')->default(0);
            $table->date('data_proxima_geracao');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultima_execucao')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('transacao_financeira_id')->references('id')->on('transacoes_financeiras')->onDelete('cascade');
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes')->onDelete('cascade');

            // Índices para performance
            $table->index('company_id');
            $table->index('ativo');
            $table->index('data_proxima_geracao');
            $table->index(['ativo', 'data_proxima_geracao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recorrencias');
    }
};
