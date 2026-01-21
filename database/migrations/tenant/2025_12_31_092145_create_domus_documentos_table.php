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
        Schema::create('domus_documentos', function (Blueprint $table) {
            $table->id();
            
            // Informações do arquivo
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo')->nullable(); // Caminho onde o arquivo foi salvo
            $table->string('tipo_arquivo'); // PDF, PNG, JPG, etc.
            $table->string('mime_type')->nullable();
            $table->bigInteger('tamanho_arquivo')->nullable();
            $table->text('base64_content')->nullable(); // Conteúdo em base64 (opcional, para preview)
            
            // Dados extraídos pela IA
            $table->string('tipo_documento')->nullable(); // NF-e, NFC-e, BOLETO, RECIBO, OUTRO
            $table->json('dados_extraidos')->nullable(); // JSON completo com todos os dados extraídos
            
            // Estabelecimento
            $table->string('estabelecimento_nome')->nullable();
            $table->string('estabelecimento_cnpj')->nullable();
            
            // Dados financeiros
            $table->date('data_emissao')->nullable();
            $table->decimal('valor_total', 15, 2)->nullable();
            $table->string('forma_pagamento')->nullable();
            
            // Status e processamento
            $table->enum('status', ['pendente', 'processado', 'erro', 'arquivado'])->default('pendente');
            $table->text('erro_processamento')->nullable(); // Mensagem de erro se houver
            $table->timestamp('processado_em')->nullable();
            
            // Relacionamentos
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name')->nullable(); // Nome do usuário que enviou
            
            // Canal de origem
            $table->enum('canal_origem', ['upload', 'whatsapp', 'email'])->default('upload');
            $table->string('remetente')->nullable(); // Email ou número do WhatsApp
            
            // Soft deletes
            $table->softDeletes();
            $table->timestamps();
            
            // Índices
            $table->index('status');
            $table->index('tipo_documento');
            $table->index('company_id');
            $table->index('user_id');
            $table->index('data_emissao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domus_documentos');
    }
};
