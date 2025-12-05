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
        Schema::table('horarios_missas', function (Blueprint $table) {
            $table->integer('intervalo')->default(90)->after('horario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('horarios_missas', function (Blueprint $table) {
            $table->dropColumn('intervalo');
        });
    }
};
