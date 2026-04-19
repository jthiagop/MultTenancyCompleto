<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->unsignedBigInteger('rateio_origem_id')->nullable()->after('transferencia_id');

            $table->foreign('rateio_origem_id')
                ->references('id')->on('transacoes_financeiras')
                ->nullOnDelete();

            $table->index('rateio_origem_id');
        });
    }

    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['rateio_origem_id']);
            $table->dropIndex(['rateio_origem_id']);
            $table->dropColumn('rateio_origem_id');
        });
    }
};
