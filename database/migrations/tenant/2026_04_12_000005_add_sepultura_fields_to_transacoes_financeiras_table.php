<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->unsignedBigInteger('sepultura_id')->nullable()->after('reembolso_par_id');
            $table->unsignedBigInteger('sepultado_id')->nullable()->after('sepultura_id');

            $table->index('sepultura_id', 'idx_tf_sepultura_id');
            $table->index('sepultado_id', 'idx_tf_sepultado_id');
        });
    }

    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropIndex('idx_tf_sepultura_id');
            $table->dropIndex('idx_tf_sepultado_id');
            $table->dropColumn(['sepultura_id', 'sepultado_id']);
        });
    }
};
