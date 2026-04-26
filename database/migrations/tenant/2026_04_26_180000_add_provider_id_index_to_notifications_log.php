<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Indexa `provider_id` (wamid) em notifications_log.
 *
 * O webhook de status da Meta chega com o wamid e atualiza o registro
 * correspondente ao envio. Sem o índice, cada update da Meta dispara um
 * full scan da tabela.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications_log', function (Blueprint $table) {
            $table->index('provider_id', 'idx_notifications_log_provider_id');
        });
    }

    public function down(): void
    {
        Schema::table('notifications_log', function (Blueprint $table) {
            $table->dropIndex('idx_notifications_log_provider_id');
        });
    }
};
