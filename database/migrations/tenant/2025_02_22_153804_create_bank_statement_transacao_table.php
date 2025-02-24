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
        Schema::create('bank_statement_transacao', function (Blueprint $table) {
            // Chave primária
            $table->id();

            // FKs para cada tabela (bank_statements e transacoes_financeiras)
            $table->unsignedBigInteger('bank_statement_id');
            $table->unsignedBigInteger('transacao_financeira_id');

            // Exemplo de colunas extras para conciliação
            $table->decimal('valor_conciliado', 10, 2)->nullable();
            $table->string('status_conciliacao')->nullable();

            // Timestamps (created_at, updated_at)
            $table->timestamps();

            // Definindo chave estrangeira (FK) e comportamento
            $table->foreign('bank_statement_id')
                ->references('id')
                ->on('bank_statements')
                ->onDelete('cascade');

            $table->foreign('transacao_financeira_id')
                ->references('id')
                ->on('transacoes_financeiras')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_transacao');
    }
};
