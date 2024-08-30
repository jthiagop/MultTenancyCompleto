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
        Schema::create('patrimonio_anexos', function (Blueprint $table) {
            $table->id(); // ID único para cada anexo
            $table->unsignedBigInteger('patrimonio_id'); // Relacionamento com a tabela 'patrimonios'
            $table->string('nome_arquivo'); // Nome original do arquivo
            $table->string('caminho_arquivo'); // Caminho onde o arquivo está armazenado
            $table->string('tipo_arquivo'); // Tipo MIME do arquivo (ex: image/jpeg, application/pdf)
            $table->bigInteger('tamanho_arquivo'); // Tamanho do arquivo em bytes
            $table->string('descricao')->nullable(); // Descrição opcional do arquivo
            $table->unsignedBigInteger('uploaded_by')->nullable(); // ID do usuário que fez o upload
            $table->timestamps(); // created_at e updated_at

            // Chaves estrangeiras e índices
            $table->foreign('patrimonio_id')->references('id')->on('patrimonios')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patrimonio_anexos');
    }
};
