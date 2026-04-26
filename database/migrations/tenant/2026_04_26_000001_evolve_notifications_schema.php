<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Evolui a tabela `notifications` para o modelo declarado no plano de
 * refatoração:
 *
 *  - colunas físicas (company_id, title, message, channel, sent_at, meta)
 *  - data convertida de TEXT → JSON nativo (MySQL >= 5.7)
 *  - índices compostos para os hot-paths do front (badge / listagem)
 *
 * O backfill das colunas físicas a partir de `data` é feito por um
 * comando artisan dedicado: `notifications:backfill-columns`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('notifiable_id');
            }
            if (! Schema::hasColumn('notifications', 'title')) {
                $table->string('title', 191)->nullable()->after('company_id');
            }
            if (! Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->nullable()->after('title');
            }
            if (! Schema::hasColumn('notifications', 'channel')) {
                // 'app' | 'email' | 'whatsapp' | 'broadcast' | 'multi'
                $table->string('channel', 20)->default('app')->after('message');
            }
            if (! Schema::hasColumn('notifications', 'meta')) {
                $table->json('meta')->nullable()->after('channel');
            }
            if (! Schema::hasColumn('notifications', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('read_at');
            }
        });

        // Converte `data` (TEXT) → JSON nativo em MySQL.
        // SQLite / outros bancos: mantém TEXT (mais portável p/ testes).
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql') {
            // Algumas instalações pré-existentes podem ter dados malformados;
            // preferimos modificar para JSON tolerante (NULL aceitável).
            try {
                DB::statement('ALTER TABLE notifications MODIFY `data` JSON NULL');
            } catch (\Throwable $e) {
                // Fallback: mantém TEXT mas registra o motivo no log.
                logger()->warning('[evolve_notifications_schema] Falha ao converter data → JSON nativo', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Índices compostos. O índice de morphs já existe via $table->morphs() acima
        // (notifiable_type + notifiable_id), mas precisamos de combinações por
        // company_id e read_at — caminho quente do badge/listagem.
        Schema::table('notifications', function (Blueprint $table) {
            try {
                $table->index(['notifiable_id', 'company_id', 'read_at'], 'idx_notif_user_company_read');
            } catch (\Throwable $e) {
                // índice já existente — ignora
            }
            try {
                $table->index(['company_id', 'created_at'], 'idx_notif_company_created');
            } catch (\Throwable $e) {
            }
            try {
                $table->index(['type'], 'idx_notif_type');
            } catch (\Throwable $e) {
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('notifications')) {
            return;
        }

        Schema::table('notifications', function (Blueprint $table) {
            // Drops são tolerantes a não-existência
            foreach (['idx_notif_user_company_read', 'idx_notif_company_created', 'idx_notif_type'] as $idx) {
                try {
                    $table->dropIndex($idx);
                } catch (\Throwable $e) {
                }
            }

            foreach (['sent_at', 'meta', 'channel', 'message', 'title', 'company_id'] as $col) {
                if (Schema::hasColumn('notifications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            try {
                DB::statement('ALTER TABLE notifications MODIFY `data` TEXT NULL');
            } catch (\Throwable $e) {
                // ignora — provavelmente já é TEXT
            }
        }
    }
};
