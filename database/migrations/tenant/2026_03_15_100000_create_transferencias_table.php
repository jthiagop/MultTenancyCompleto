<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tabela para armazenar transferências entre entidades financeiras (banco/caixa).
     * Cada transferência gera 2 registros em transacoes_financeiras:
     * - 1 saída (pago) na conta de origem
     * - 1 entrada (recebido) na conta de destino
     */
    public function up(): void
    {
        Schema::create('transferencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('cascade')
                ->comment('Empresa proprietária da transferência');

            $table->foreignId('entidade_origem_id')
                ->constrained('entidades_financeiras')
                ->onDelete('cascade')
                ->comment('Conta de origem (de onde o valor sai)');

            $table->foreignId('entidade_destino_id')
                ->constrained('entidades_financeiras')
                ->onDelete('cascade')
                ->comment('Conta de destino (para onde o valor vai)');

            $table->decimal('valor', 15, 2)
                ->comment('Valor da transferência');

            $table->date('data')
                ->comment('Data da transferência');

            $table->string('descricao', 255)
                ->nullable()
                ->comment('Descrição da transferência');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Usuário que criou a transferência');

            $table->timestamps();
            $table->softDeletes();

            // Índices para performance
            $table->index('company_id');
            $table->index('data');
            $table->index(['entidade_origem_id', 'entidade_destino_id'], 'idx_transferencia_origem_destino');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transferencias');
    }
};
