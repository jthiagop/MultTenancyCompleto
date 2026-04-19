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
            // Remove a FK existente para poder alterar a coluna
            $table->dropForeign(['entidade_id']);

            // Torna nullable (cobranças de cemitério não têm entidade financeira)
            $table->unsignedBigInteger('entidade_id')->nullable()->change();

            // Recria a FK permitindo NULL
            $table->foreign('entidade_id')
                  ->references('id')
                  ->on('entidades_financeiras')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['entidade_id']);
            $table->unsignedBigInteger('entidade_id')->nullable(false)->change();
            $table->foreign('entidade_id')
                  ->references('id')
                  ->on('entidades_financeiras')
                  ->onDelete('cascade');
        });
    }
};
