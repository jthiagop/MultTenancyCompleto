<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sepultado_responsaveis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sepultado_id')->constrained('sepultados')->onDelete('cascade');
            $table->string('nome');
            $table->string('telefone', 20)->nullable();
            $table->string('cep', 10)->nullable();
            $table->string('logradouro')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->timestamps();

            $table->index('sepultado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepultado_responsaveis');
    }
};
