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
        // Cria a tabela de mensagens processadas do WhatsApp
        Schema::create('whatsapp_messages_processed', function (Blueprint $table) {
            $table->id();
            $table->string('wamid')->unique(); // WhatsApp Message ID (único globalmente)
            $table->timestamp('processed_at'); // Data/hora do processamento

            $table->index('processed_at'); // Índice para limpeza de registros antigos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages_processed');
    }
};
