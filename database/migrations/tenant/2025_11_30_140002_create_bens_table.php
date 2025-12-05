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
        Schema::create('bens', function (Blueprint $table) {
            $table->id();
            
            // Multi-tenant
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            
            // Dados Gerais (Comuns a todas as imagens)
            $table->string('descricao'); // "Descrição *" nas imagens
            $table->string('tipo'); // 'veiculo', 'imovel', 'movel'
            $table->string('centro_custo')->nullable();
            
            // Dados Financeiros/Aquisição
            $table->decimal('valor', 15, 2); 
            $table->date('data_aquisicao');
            $table->string('numero_documento')->nullable(); // NF ou Escritura
            $table->string('fornecedor')->nullable();
            
            // Estado
            $table->boolean('depreciar')->default(false); // Checkbox "Depreciar"
            $table->string('estado_bem')->nullable(); // Novo, Bom, Ruim
            
            // Dados adicionais em JSON
            $table->json('dados_adicionais')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('company_id');
            $table->index('tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bens');
    }
};
