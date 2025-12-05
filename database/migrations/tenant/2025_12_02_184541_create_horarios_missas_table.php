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
        if (!Schema::hasTable('horarios_missas')) {
            Schema::create('horarios_missas', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->enum('dia_semana', ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado']);
                $table->time('horario');
                $table->timestamps();

                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index(['company_id', 'dia_semana']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_missas');
    }
};

