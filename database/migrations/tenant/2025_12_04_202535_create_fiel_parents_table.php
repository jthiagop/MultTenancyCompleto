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
        Schema::create('fiel_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->unique();
            $table->string('nome_pai')->nullable();
            $table->string('nome_mae')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_parents');
    }
};
