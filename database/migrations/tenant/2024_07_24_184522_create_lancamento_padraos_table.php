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
        Schema::create('lancamento_padraos', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['entrada', 'saida']); // Tipo de lançamento
            $table->string('description')->nullable(); // Descrição do lançamento
            $table->date('date'); // Data do lançamento
            $table->string('category')->nullable(); // Categoria do lançamento
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Referência ao usuário que fez o lançamento
            $table->timestamps(); // Cria as colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lancamento_padraos');
    }
};
