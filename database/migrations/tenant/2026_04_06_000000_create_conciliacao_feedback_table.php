<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conciliacao_feedback', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('bank_statement_id');
            $table->string('campo', 50);
            $table->string('valor_sugerido', 255)->nullable();
            $table->string('valor_escolhido', 255)->nullable();
            $table->boolean('aceito')->default(false);
            $table->integer('confianca_original')->default(0);
            $table->string('origem_sugestao', 50)->nullable();
            $table->timestamps();

            $table->index(['company_id', 'campo']);
            $table->index(['company_id', 'origem_sugestao']);
            $table->index(['company_id', 'aceito']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conciliacao_feedback');
    }
};
