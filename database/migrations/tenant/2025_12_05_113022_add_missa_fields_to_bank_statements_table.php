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
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dateTime('transaction_datetime')->nullable()->after('dtposted');
            $table->string('source_time', 20)->nullable()->after('transaction_datetime')->comment('Origem do horário: memo ou dtposted');
            $table->boolean('conciliado_com_missa')->default(false)->after('source_time');
            $table->unsignedBigInteger('horario_missa_id')->nullable()->after('conciliado_com_missa');
            
            $table->foreign('horario_missa_id')->references('id')->on('horarios_missas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_statements', function (Blueprint $table) {
            $table->dropForeign(['horario_missa_id']);
            $table->dropColumn(['transaction_datetime', 'source_time', 'conciliado_com_missa', 'horario_missa_id']);
        });
    }
};
