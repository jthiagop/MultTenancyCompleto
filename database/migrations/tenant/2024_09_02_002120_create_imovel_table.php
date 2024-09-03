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
        Schema::create('imovel', function (Blueprint $table) {
            $table->id();
            $table->string('descricao'); // Campo "Descrição"
            $table->string('inscricao_municipal')->nullable(); // Campo "Inscrição Municipal"
            $table->date('data')->nullable(); // Campo "Data"
            $table->string('origem')->nullable(); // Campo "Origem"
            $table->string('cep')->nullable(); // Campo "CEP"
            $table->string('bairro')->nullable(); // Campo "Bairro"
            $table->string('logradouro')->nullable(); // Campo "Rua"
            $table->string('localidade')->nullable(); // Campo "Cidade"
            $table->string('uf')->nullable(); // Campo "Estado"
            $table->text('complemento')->nullable(); // Campo "Informações Complementares"
            $table->string('outorgante')->nullable(); // Campo "Outorgante"
            $table->string('matricula')->nullable(); // Campo "Número da Matrícula"
            $table->date('data_aquisicao')->nullable(); // Campo "Data da Aquisição"
            $table->string('outorgado')->nullable(); // Campo "Outorgado"
            $table->decimal('valor', 15, 2)->nullable(); // Campo "Valor de Aquisição"
            $table->decimal('area_total', 15, 2)->nullable(); // Campo "Área Total"
            $table->decimal('area_privativa', 15, 2)->nullable(); // Campo "Área Privativa"
            $table->text('informacoes')->nullable(); // Campo "Informações"

            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');

            $table->timestamps(); // Campos para created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imovel');
    }
};
