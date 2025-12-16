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
        Schema::create('dizimos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
            $table->enum('tipo', ['Dízimo', 'Doação', 'Oferta', 'Outro'])->default('Dízimo');
            $table->decimal('valor', 10, 2);
            $table->date('data_pagamento');
            $table->enum('forma_pagamento', ['Dinheiro', 'PIX', 'Cartão de Débito', 'Cartão de Crédito', 'Transferência', 'Cheque', 'Outro'])->default('Dinheiro');
            $table->foreignId('entidade_financeira_id')->nullable()->constrained('entidades_financeiras')->onDelete('set null');
            $table->foreignId('movimentacao_id')->nullable()->constrained('movimentacoes')->onDelete('set null');
            $table->text('observacoes')->nullable();
            $table->string('comprovante')->nullable();
            $table->boolean('integrado_financeiro')->default(false);
            
            // Campos de auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('company_id');
            $table->index('fiel_id');
            $table->index('data_pagamento');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dizimos');
    }
};
