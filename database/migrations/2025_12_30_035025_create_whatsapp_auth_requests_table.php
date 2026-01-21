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
        Schema::create('whatsapp_auth_requests', function (Blueprint $table) {
            $table->id();
            $table->string('verification_code')->unique(); // Código de verificação para vinculação
            $table->string('tenant_id'); // ID do tenant
            $table->string('waba_id')->nullable(); // ID do WABA (opcional)
            $table->string('phone_number_id')->nullable(); // ID do número de telefone (muito importante para roteamento)
            $table->text('access_token')->nullable(); // Token de acesso (opcional, se cada tenant tiver seu próprio token)
            $table->unsignedBigInteger('user_id'); // ID do usuário
            $table->string('status')->default('pending'); // pending, active, inactive

            $table->timestamps();

            // Índices para melhorar performance nas buscas
            $table->index(['verification_code', 'tenant_id'], 'idx_verification_code_tenant');
            $table->index('phone_number_id', 'idx_phone_number_id'); // Índice para busca rápida por phone_number_id
            $table->index(['phone_number_id', 'status'], 'idx_phone_number_status'); // Índice composto para busca ativa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_auth_requests');
    }
};
