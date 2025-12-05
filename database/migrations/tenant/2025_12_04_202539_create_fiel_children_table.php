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
        Schema::create('fiel_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
            $table->string('nome');
            $table->date('data_nascimento')->nullable();
            $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable();
            $table->enum('sexo', ['M', 'F', 'Outro'])->nullable();
            $table->timestamps();
            
            $table->index('fiel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_children');
    }
};
