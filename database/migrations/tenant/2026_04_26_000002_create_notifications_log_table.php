<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de auditoria de envios efetivos por canal externo.
 *
 *  - notifications: estado da notificação para o usuário (badge/listagem).
 *  - notifications_log: histórico detalhado de cada tentativa de envio
 *    em canais externos (whatsapp, email, broadcast). Permite saber
 *    "quem recebeu o quê e quando", inclusive em caso de falha.
 *
 * O canal "app" não precisa logar aqui — o próprio registro em
 * `notifications` já é o histórico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications_log', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Pode ser nulo nos casos em que a entrada do canal externo
            // foi feita sem persistir no banco (ex.: broadcast pontual).
            $table->uuid('notification_id')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();

            // app | email | whatsapp | broadcast
            $table->string('channel', 20);

            // queued | sent | delivered | failed | skipped
            $table->string('status', 20);

            // ID externo (ex.: wamid do WhatsApp, message-id do email).
            $table->string('provider_id', 191)->nullable();

            // Mensagem efetivamente enviada (snippet) — útil para depurar
            // sem ter que reconstruir a partir do meta.
            $table->text('payload_excerpt')->nullable();

            // Erro retornado pelo provedor.
            $table->text('error')->nullable();

            // Metadados do envio (request body, response, retries…).
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'channel', 'status']);
            $table->index(['company_id', 'channel', 'created_at']);
            $table->index('notification_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_log');
    }
};
