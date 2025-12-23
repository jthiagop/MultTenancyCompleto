<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dfe_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Identificação Única na SEFAZ
            $table->string('chave_acesso', 44)->index(); 
            $table->unsignedBigInteger('nsu')->comment('Último NSU que alterou este documento');
            
            // Definições do Tipo
            $table->unsignedTinyInteger('modelo')->default(55)->comment('55=NFe, 57=CTe, 65=NFCe');
            $table->unsignedTinyInteger('tp_amb')->default(1)->comment('1=Produção, 2=Homologação');
            $table->string('schema_xml', 30)->comment('Ex: resNFe, procNFe');

            // Atores
            $table->string('emitente_nome')->nullable();
            $table->string('emitente_cnpj', 14)->nullable()->index();
            $table->string('emitente_ie')->nullable();
            // Se for CTe, poderia ter tomador, mas pode deixar genérico por enquanto

            // Dados Financeiros/Fiscais
            $table->dateTime('data_emissao')->nullable();
            $table->decimal('valor_total', 15, 2)->nullable();
            
            // Status do Processo Interno
            // ciencia = demos ciência na operação
            // download = já temos o XML full
            // importado = já virou conta a pagar/estoque
            $table->string('status_sistema')->default('novo')->index(); 

            // Armazenamento
            $table->boolean('xml_completo')->default(false);
            $table->string('xml_path')->nullable()->comment('Caminho no Storage');
            $table->string('xml_hash', 64)->nullable();

            $table->timestamps();

            // Regras de Unicidade
            // Uma empresa não pode ter duas vezes a mesma chave (evita duplicidade)
            $table->unique(['company_id', 'chave_acesso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dfe_documents');
    }
};
