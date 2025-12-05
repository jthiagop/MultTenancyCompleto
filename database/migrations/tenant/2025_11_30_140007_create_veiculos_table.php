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
        Schema::create('veiculos', function (Blueprint $table) {
            $table->id();
            
            // A Chave Estrangeira (O elo de ligação)
            $table->foreignId('bem_id')->constrained('bens')->onDelete('cascade');
            
            // Campos específicos da Imagem 1
            $table->string('placa')->unique()->nullable();
            $table->string('renavam')->nullable();
            $table->string('chassi')->nullable();
            $table->string('combustivel')->nullable(); // Gasolina, Diesel...
            $table->integer('ano_modelo')->nullable();
            $table->integer('ano_fabricacao')->nullable();
            $table->string('cor')->nullable();
            $table->string('crlv')->nullable(); // Documento
            
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
        Schema::dropIfExists('veiculos');
    }
};
