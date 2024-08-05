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
        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id'); // Adiciona a coluna company_id
            $table->date('data_competencia');
            $table->unsignedBigInteger('banco_id')->nullable(); // Campo opcional para identificar o banco
            $table->decimal('valor', 15, 2);
            $table->enum('tipo', ['entrada', 'saida']);
            $table->string('descricao');
            $table->string('lancamento_padrao');
            $table->string('centro')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->string('origem')->nullable();
            $table->text('historico_complementar')->nullable();
            $table->softDeletes();
            $table->timestamps();


            $table->unsignedBigInteger('created_by')->nullable(); // Para rastrear o criador do registro
            $table->unsignedBigInteger('updated_by')->nullable(); // Para rastrear o atualizador do registro

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Índices e chaves estrangeiras adicionais
            $table->foreign('banco_id')->references('id')->on('cadastro_bancos')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bancos');
    }
};
