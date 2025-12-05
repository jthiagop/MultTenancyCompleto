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
        Schema::create('fiel_spouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->unique();
            $table->foreignId('fiel_conjuge_id')->nullable()->constrained('fieis')->onDelete('set null')->comment('Se for outro fiel cadastrado');
            $table->string('nome_conjuge')->nullable();
            $table->date('data_nascimento')->nullable();
            $table->boolean('ocultar_ano')->default(false);
            $table->string('profissao')->nullable();
            $table->boolean('dizimista')->default(false);
            $table->string('codigo_dizimista')->nullable();
            $table->string('cartao_magnetico')->nullable();
            $table->decimal('percentual_salario', 5, 2)->nullable();
            $table->boolean('criar_ficha')->default(false)->comment('Criar uma nova ficha para o cônjuge');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_spouse');
    }
};
