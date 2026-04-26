<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Distingue vínculos pessoais (kind='user') dos contatos do "Grupo WhatsApp"
 * cadastrados pela empresa (kind='company_contact').
 *
 *  - kind: 'user' (default) | 'company_contact'.
 *  - contact_label: rótulo livre do contato do grupo (ex.: "Tesoureiro").
 *  - user_id passa a ser NULLABLE — só faz sentido para kind='user'.
 *  - Índice composto (tenant_id, company_id, kind, status) para a listagem
 *    e a iteração de envio em massa pelas notificações financeiras.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->string('kind', 32)->default('user')->after('company_id')
                ->comment("Tipo do vínculo: 'user' = WhatsApp do usuário; 'company_contact' = número do Grupo WhatsApp da empresa.");

            $table->string('contact_label', 120)->nullable()->after('kind')
                ->comment("Rótulo do contato do grupo (apenas quando kind='company_contact'). Ex.: 'Tesoureiro'.");
        });

        // Tornar user_id nullable. user_id é NOT NULL no schema original
        // (migration 2025_12_30_035025) e queremos NULL para company_contact.
        // Usamos statement nativo para evitar dependência do doctrine/dbal.
        DB::statement('ALTER TABLE whatsapp_auth_requests MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->index(
                ['tenant_id', 'company_id', 'kind', 'status'],
                'idx_tenant_company_kind_status',
            );
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->dropIndex('idx_tenant_company_kind_status');
        });

        // Reverter user_id para NOT NULL pode falhar caso já existam linhas
        // de company_contact (user_id IS NULL). Se for o caso, a migration de
        // rollback irá falhar — comportamento intencional para evitar perda
        // de dados. Manualmente: DELETE FROM whatsapp_auth_requests WHERE
        // kind='company_contact'; antes de rodar o down().
        DB::statement('ALTER TABLE whatsapp_auth_requests MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('whatsapp_auth_requests', function (Blueprint $table) {
            $table->dropColumn(['kind', 'contact_label']);
        });
    }
};
