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
        Schema::create('bens_moveis', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('bem_id')->constrained('bens')->onDelete('cascade');
            
            $table->string('marca_modelo')->nullable();
            $table->string('chapa_plaqueta')->nullable(); // Identificação interna
            $table->date('garantia')->nullable();
            
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
        Schema::dropIfExists('bens_moveis');
    }
};
