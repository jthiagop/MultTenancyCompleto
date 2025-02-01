<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
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
