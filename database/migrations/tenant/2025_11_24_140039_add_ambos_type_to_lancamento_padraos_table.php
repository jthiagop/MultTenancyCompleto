<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para MySQL/MariaDB, precisamos recriar a coluna enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lancamento_padraos MODIFY COLUMN type ENUM('entrada', 'saida', 'ambos') NOT NULL");
        } else {
            // Para PostgreSQL ou outros bancos
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->enum('type', ['entrada', 'saida', 'ambos'])->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter para o enum original
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE lancamento_padraos MODIFY COLUMN type ENUM('entrada', 'saida') NOT NULL");
        } else {
            Schema::table('lancamento_padraos', function (Blueprint $table) {
                $table->enum('type', ['entrada', 'saida'])->change();
            });
        }
    }
};
