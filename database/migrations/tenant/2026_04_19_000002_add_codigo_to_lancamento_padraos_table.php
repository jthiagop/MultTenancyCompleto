<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona a coluna `codigo` na tabela lancamento_padraos.
     *
     * - Nullable (muitos tenants ainda não usarão código próprio).
     * - Unique por (company_id, codigo): no MySQL, múltiplos NULLs são aceitos,
     *   então só impede duplicidade quando o usuário informa um código.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lancamento_padraos')) {
            return;
        }

        if (! Schema::hasColumn('lancamento_padraos', 'codigo')) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                // Posição: logo depois de company_id para agrupar com campos de identificação.
                $table->string('codigo', 50)
                    ->nullable()
                    ->after('company_id')
                    ->comment('Código opcional informado pelo usuário (único por empresa quando preenchido).');
            });
        }

        // Cria o índice único composto (idempotente).
        $indexExists = collect(Schema::getConnection()
            ->select(
                "SHOW INDEX FROM lancamento_padraos WHERE Key_name = 'lancamento_padraos_company_codigo_unique'"
            ))->isNotEmpty();

        if (! $indexExists) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->unique(['company_id', 'codigo'], 'lancamento_padraos_company_codigo_unique');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('lancamento_padraos')) {
            return;
        }

        $indexExists = collect(Schema::getConnection()
            ->select(
                "SHOW INDEX FROM lancamento_padraos WHERE Key_name = 'lancamento_padraos_company_codigo_unique'"
            ))->isNotEmpty();

        if ($indexExists) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->dropUnique('lancamento_padraos_company_codigo_unique');
            });
        }

        if (Schema::hasColumn('lancamento_padraos', 'codigo')) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->dropColumn('codigo');
            });
        }
    }
};
