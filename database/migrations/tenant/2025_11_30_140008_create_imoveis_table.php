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
        Schema::create('imoveis', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('bem_id')->constrained('bens')->onDelete('cascade');
            
            // Endereço e Localização
            $table->string('inscricao_municipal')->nullable();
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            
            // Dados de Cartório/Escritura
            $table->string('matricula')->nullable();
            $table->string('cartorio')->nullable();
            $table->string('livro')->nullable();
            $table->string('folha')->nullable();
            
            // Áreas
            $table->decimal('area_total', 10, 2)->nullable();
            $table->decimal('area_privativa', 10, 2)->nullable();
            
            // Dados adicionais em JSON
            $table->json('dados_adicionais')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imoveis');
    }
};
