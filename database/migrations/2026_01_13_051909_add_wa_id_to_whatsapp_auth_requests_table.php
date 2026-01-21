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
        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->string('wa_id')->nullable()->after('phone_number_id')->comment('Número do WhatsApp do remetente (para resolução de tenant em mensagens normais)');
            $table->index('wa_id', 'idx_wa_id');
            $table->index(['wa_id', 'status'], 'idx_wa_id_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->dropIndex('idx_wa_id_status');
            $table->dropIndex('idx_wa_id');
            $table->dropColumn('wa_id');
        });
    }
};
