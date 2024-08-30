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
        Schema::create('escrituras', function (Blueprint $table) {
            $table->id();
            $table->string('outorgante')->nullable(); // Pessoa ou entidade que concede ou transfere (vendedor)
            $table->string('matricula')->nullable(); // Número da Matrícula
            $table->date('aquisicao')->nullable(); // Data da Aquisição
            $table->string('outorgado')->nullable(); // Pessoa ou entidade que recebe (comprador)
            $table->decimal('valor', 15, 2)->nullable(); // Valor da Aquisição em R$
            $table->decimal('area_total', 15, 2)->nullable(); // Área Total
            $table->decimal('area_privativa', 15, 2)->nullable(); // Área Privativa
            $table->text('informacoes')->nullable(); // Mais detalhes sobre o foro, com limite de 250 caracteres

            $table->foreignId('patrimonio_id')->constrained('patrimonios')->onDelete('cascade');

            // Campos para rastrear quem criou e quem atualizou
            $table->unsignedBigInteger('created_by')->nullable(); // Para rastrear o criador do registro
            $table->unsignedBigInteger('updated_by')->nullable(); // Para rastrear o atualizador do registro

            // Índices e chaves estrangeiras
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrituras');
    }
};
