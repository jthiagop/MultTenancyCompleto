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
        Schema::create('transacao_fracionamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transacao_principal_id')
                  ->constrained('transacoes_financeiras')
                  ->onDelete('cascade');
            $table->enum('tipo', ['pago', 'em_aberto'])->comment('Tipo do fracionamento');
            $table->decimal('valor', 15, 2)->comment('Valor do fracionamento');
            $table->date('data_pagamento')->nullable()->comment('Data do pagamento (quando tipo = pago)');
            $table->decimal('juros', 15, 2)->default(0)->comment('Juros do pagamento parcial');
            $table->decimal('multa', 15, 2)->default(0)->comment('Multa do pagamento parcial');
            $table->decimal('desconto', 15, 2)->default(0)->comment('Desconto do pagamento parcial');
            $table->decimal('valor_total', 15, 2)->comment('Valor total (valor + juros + multa - desconto)');
            $table->string('forma_pagamento')->nullable()->comment('Forma de pagamento utilizada');
            $table->string('conta_pagamento')->nullable()->comment('Conta utilizada para pagamento');
            $table->timestamps();

            // Índices para melhor performance
            $table->index('transacao_principal_id');
            $table->index('tipo');
            $table->index('data_pagamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacao_fracionamentos');
    }
};
