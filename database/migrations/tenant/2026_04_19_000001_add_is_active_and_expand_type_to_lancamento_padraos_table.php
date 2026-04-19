<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Adiciona coluna is_active
        if (Schema::hasTable('lancamento_padraos') && ! Schema::hasColumn('lancamento_padraos', 'is_active')) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('company_id');
            });
        }

        // 2. Expande o enum type para incluir os novos valores
        DB::statement("ALTER TABLE lancamento_padraos MODIFY COLUMN type ENUM('entrada','saida','ambos','transferencia','somente_contabil') NOT NULL DEFAULT 'entrada'");
    }

    public function down(): void
    {
        // Reverte o enum
        DB::statement("ALTER TABLE lancamento_padraos MODIFY COLUMN type ENUM('entrada','saida','ambos') NOT NULL DEFAULT 'entrada'");

        // Remove a coluna
        if (Schema::hasColumn('lancamento_padraos', 'is_active')) {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
