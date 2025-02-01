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
            // Exemplo para renomear a coluna 'centro' para 'cost_center_id' (se a coluna 'centro' existir)
            $table->renameColumn('centro', 'cost_center_id');

            // Agora cria a FK (se já não existir)
            // Ajuste onDelete e onUpdate conforme sua regra de negócio
            $table->foreign('cost_center_id')
                ->references('id')
                ->on('cost_centers')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Primeiro removemos a FK
            $table->dropForeign(['cost_center_id']);

            // Renomeia de volta se necessário
            $table->renameColumn('cost_center_id', 'centro');
        });
    }
};
