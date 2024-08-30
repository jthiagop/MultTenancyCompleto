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
        Schema::create('patrimonios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_rid'); // Campo para a descrição
            $table->string('descricao'); // Campo para a descrição
            $table->string('patrimonio')->nullable(); // Campo para o patrimônio
            $table->date('data')->nullable(); // Campo para a data
            $table->string('livro')->nullable(); // Campo para o livro
            $table->string('folha')->nullable(); // Campo para a folha
            $table->string('registro')->nullable(); // Campo para o registro
            $table->string('tags')->nullable(); // Campo para as tags
            $table->string('cep')->nullable(); // Campo para o CEP
            $table->string('bairro')->nullable(); // Campo para o bairro
            $table->string('logradouro')->nullable(); // Campo para a rua
            $table->string('localidade')->nullable(); // Campo para a localidade
            $table->string('uf', 2)->nullable(); // Campo para o estado com dois caracteres
            $table->text('complemento')->nullable();

            $table->timestamps();


            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimonios');
    }
};
