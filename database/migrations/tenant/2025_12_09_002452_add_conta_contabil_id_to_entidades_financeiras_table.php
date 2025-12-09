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
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->foreignId('conta_contabil_id')
                ->nullable()
                ->after('descricao')
                ->constrained('chart_of_accounts')
                ->onDelete('set null')
                ->comment('Vinculo contábil para exportação (De/Para)');
            
            // Adicionar índice para performance
            $table->index('conta_contabil_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->dropForeign(['conta_contabil_id']);
            $table->dropIndex(['conta_contabil_id']);
            $table->dropColumn('conta_contabil_id');
        });
    }
};
