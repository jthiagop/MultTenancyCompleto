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
        Schema::create('sepultados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreignId('sepultura_id')->constrained('sepulturas')->onDelete('cascade');
            
            // Informações pessoais
            $table->string('nome');
            $table->string('avatar')->nullable();
            $table->date('data_nascimento');
            $table->date('data_falecimento');
            $table->date('data_sepultamento');
            
            // Informações do óbito
            $table->string('causa_mortis')->nullable();
            $table->string('documento_identificacao')->nullable();
            $table->text('informacoes_atestado_obito')->nullable();
            
            // Informações do sepultamento
            $table->string('livro_sepultamento', 20)->nullable();
            $table->string('folha_sepultamento', 20)->nullable();
            $table->string('numero_sepultamento', 20)->nullable();
            
            // Informações da família
            $table->string('familia_responsavel')->nullable();
            $table->string('relacionamento', 100)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Chave estrangeira
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Índices
            $table->index('company_id');
            $table->index('sepultura_id');
            $table->index('nome');
            $table->index('data_falecimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepultados');
    }
};
