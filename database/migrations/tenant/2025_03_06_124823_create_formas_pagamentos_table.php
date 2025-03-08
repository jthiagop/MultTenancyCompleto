<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('formas_pagamento', function (Blueprint $table) {
            $table->id(); // Chave primária autoincrementável
            $table->string('nome', 100); // Nome da forma de pagamento
            $table->string('codigo', 10)->unique(); // Código único (ex: PIX, BOL)
            $table->boolean('ativo')->default(true); // Status (ativo/inativo)
            $table->decimal('taxa', 5, 2)->default(0.0); // Taxa associada
            $table->enum('tipo_taxa', ['valor_fixo', 'porcentagem'])->default('valor_fixo'); // Tipo de taxa
            $table->integer('prazo_liberacao')->nullable()->default(0); // Prazo de liberação em dias
            $table->string('metodo_integracao', 100)->nullable(); // Método de integração
            $table->string('icone', 255)->nullable(); // Caminho ou URL do ícone
            $table->text('observacao')->nullable(); // Observações ou detalhes adicionais


            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            $table->timestamps(); // created_at e updated_at
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('formas_pagamento');
    }
};
