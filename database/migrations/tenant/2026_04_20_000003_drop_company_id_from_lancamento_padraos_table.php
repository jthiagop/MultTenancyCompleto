<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ordem obrigatória no MySQL: a FK criada em
        // 2025_12_05_171200_add_company_id_to_lancamento_padraos_table reaproveita
        // o índice composto `(company_id, codigo)` criado em
        // 2026_04_19_000002_add_codigo_to_lancamento_padraos_table. Assim, tentar
        // dropar o unique primeiro falha com erro 1553 ("needed in a foreign key
        // constraint"). Precisamos:
        //   1) dropar a FK de company_id (se existir),
        //   2) dropar o unique composto (para não rebaixar para unique só em
        //      `codigo`, o que causaria conflitos em categorias globais),
        //   3) dropar a coluna.

        if (Schema::hasColumn('lancamento_padraos', 'company_id')) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                try {
                    $table->dropForeign(['company_id']);
                } catch (\Throwable $e) {
                    // Idempotente: em bancos já sem FK, ignora.
                }
            });
        }

        $indexExists = collect(\DB::select(
            "SHOW INDEX FROM lancamento_padraos WHERE Key_name = 'lancamento_padraos_company_codigo_unique'"
        ))->isNotEmpty();

        if ($indexExists) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->dropUnique('lancamento_padraos_company_codigo_unique');
            });
        }

        if (Schema::hasColumn('lancamento_padraos', 'company_id')) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('lancamento_padraos', 'company_id')) {
            return;
        }

        Schema::table('lancamento_padraos', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained('companies')
                ->onDelete('cascade');
        });
    }
};
