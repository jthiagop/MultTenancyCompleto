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
        Schema::create('profissoes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255)->unique();
            $table->text('descricao')->nullable();
            $table->integer('popularidade')->default(0)->comment('Ranking de popularidade');
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profissoes');
    }
};
