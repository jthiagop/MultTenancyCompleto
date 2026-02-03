<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabela para armazenar as parcelas de transações financeiras parceladas.
     * A transação principal (pai) fica em transacoes_financeiras com o valor TOTAL,
     * e cada parcela individual é registrada aqui.
     */
    public function up(): void
    {
        Schema::create('parcelamentos', function (Blueprint $table) {
            $table->id();
            
            // Relacionamento com a transação principal (pai)
            $table->foreignId('transacao_financeira_id')
                ->constrained('transacoes_financeiras')
                ->onDelete('cascade')
                ->comment('Transação financeira pai (valor total)');
            
            // Relacionamento com a transação filha (a parcela como TransacaoFinanceira)
            $table->foreignId('transacao_parcela_id')
                ->nullable()
                ->constrained('transacoes_financeiras')
                ->onDelete('cascade')
                ->comment('Transação financeira filha (a parcela individual)');
            
            // Identificação da parcela
            $table->unsignedSmallInteger('numero_parcela')->comment('Número da parcela (1, 2, 3...)');
            $table->unsignedSmallInteger('total_parcelas')->comment('Total de parcelas');
            
            // Datas
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            
            // Valores
            $table->decimal('valor', 15, 2)->comment('Valor original da parcela');
            $table->decimal('percentual', 5, 2)->default(0)->comment('Percentual do valor total');
            $table->decimal('valor_pago', 15, 2)->default(0);
            $table->decimal('juros', 15, 2)->default(0);
            $table->decimal('multa', 15, 2)->default(0);
            $table->decimal('desconto', 15, 2)->default(0);
            
            // Forma e conta de pagamento (podem ser diferentes para cada parcela)
            $table->foreignId('entidade_id')
                ->nullable()
                ->constrained('entidades_financeiras')
                ->onDelete('set null')
                ->comment('Forma de pagamento da parcela');
            
            $table->foreignId('conta_pagamento_id')
                ->nullable()
                ->constrained('entidades_financeiras')
                ->onDelete('set null')
                ->comment('Conta para pagamento da parcela');
            
            // Descrição (pode ser personalizada por parcela)
            $table->string('descricao', 255)->nullable();
            
            // Situação da parcela
            $table->string('situacao', 50)->default('em_aberto')
                ->comment('em_aberto, pago, recebido, atrasado, pago_parcial');
            
            // Agendamento
            $table->boolean('agendado')->default(false);
            
            // Relacionamento com movimentação (quando a parcela é paga)
            $table->foreignId('movimentacao_id')
                ->nullable()
                ->constrained('movimentacoes')
                ->onDelete('set null')
                ->comment('Movimentação criada quando a parcela é paga');
            
            // Auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices para performance
            $table->index(['transacao_financeira_id', 'numero_parcela']);
            $table->index(['situacao', 'data_vencimento']);
            $table->index('data_vencimento');
            
            // Garante unicidade: não pode ter duas parcelas com mesmo número para mesma transação
            $table->unique(['transacao_financeira_id', 'numero_parcela'], 'parcelamento_unico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcelamentos');
    }
};
