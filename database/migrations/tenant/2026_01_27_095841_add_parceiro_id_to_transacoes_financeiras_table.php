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
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->foreignId('parceiro_id')
                ->nullable()
                ->after('entidade_id')
                ->constrained('parceiros')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['parceiro_id']);
            $table->dropColumn('parceiro_id');
        });
    }
};
