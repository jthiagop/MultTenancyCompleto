<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dfe_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Relacionamento com o documento pai
            // Nullable porque às vezes o evento chega antes do documento (raro, mas acontece)
            $table->foreignId('dfe_document_id')->nullable()->constrained('dfe_documents')->onDelete('cascade');
            
            // Dados da SEFAZ para este evento
            $table->string('chave_acesso', 44)->index(); // Redundante para performance
            $table->unsignedBigInteger('nsu'); // NSU específico deste evento
            
            // Tipos de Evento (Códigos Oficiais)
            // 110111 = Cancelamento
            // 110110 = Carta de Correção
            // 210200 = Confirmação da Operação
            // 210210 = Ciência da Operação
            $table->integer('tp_evento')->index();
            $table->string('descricao_evento'); // "Cancelamento", "Ciencia"
            $table->string('protocolo')->nullable();
            $table->dateTime('data_evento');
            $table->text('correcao_texto')->nullable()->comment('Se for carta de correção');

            // Armazenamento do XML do evento
            $table->string('xml_path')->nullable();

            $table->timestamps();

            // Cada evento tem um NSU único por empresa
            $table->unique(['company_id', 'nsu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dfe_events');
    }
};
