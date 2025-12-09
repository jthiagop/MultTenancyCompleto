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
        Schema::table('lancamento_padraos', function (Blueprint $table) {
            // Adiciona company_id se não existir (necessário para multi-tenancy)
            if (!Schema::hasColumn('lancamento_padraos', 'company_id')) {
                $table->foreignId('company_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('companies')
                      ->onDelete('cascade')
                      ->comment('ID da empresa (multi-tenancy)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamento_padraos', function (Blueprint $table) {
            if (Schema::hasColumn('lancamento_padraos', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }
        });
    }
};

