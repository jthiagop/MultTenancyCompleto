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
            // Adiciona campo account_type após o campo conta
            $table->enum('account_type', ['corrente', 'poupanca', 'aplicacao', 'renda_fixa', 'tesouro_direto'])
                ->nullable()
                ->after('conta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->dropColumn('account_type');
        });
    }
};
