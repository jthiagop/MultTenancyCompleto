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
        Schema::create('fiel_religious_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->unique();
            $table->date('data_batismo')->nullable();
            $table->string('local_batismo')->nullable();
            $table->date('data_casamento')->nullable();
            $table->string('local_casamento')->nullable();
            $table->date('data_ingresso')->nullable();
            $table->string('responsavel_ingresso')->nullable();
            $table->string('grupo_participante')->nullable();
            $table->string('ministerio')->nullable();
            $table->unsignedBigInteger('comunidade_id')->nullable()->comment('Relacionamento com comunidades');
            $table->timestamps();
            
            // Se houver tabela de comunidades, adicionar foreign key
            // $table->foreign('comunidade_id')->references('id')->on('comunidades')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_religious_data');
    }
};
