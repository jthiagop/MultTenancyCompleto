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
        Schema::create('fiel_tithe', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->unique();
            $table->boolean('dizimista')->default(false);
            $table->string('codigo')->nullable();
            $table->decimal('percentual_salario', 5, 2)->nullable();
            $table->string('cartao_magnetico')->nullable();
            $table->string('missionario_dizimo')->nullable();
            $table->decimal('valor_dizimo', 10, 2)->nullable();
            $table->enum('frequencia_dizimo', ['Mensal', 'Semanal', 'Anual'])->nullable();
            $table->date('ultima_contribuicao')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_tithe');
    }
};
