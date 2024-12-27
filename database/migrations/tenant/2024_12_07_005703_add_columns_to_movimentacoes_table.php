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
        Schema::table('movimentacoes', function (Blueprint $table) {
            // Adiciona novas colunas à tabela 'movimentacoes'
            $table->date('data')->nullable(); // Data personalizada da movimentação
            $table->string('categoria', 50)->nullable(); // Categoria da movimentação (ex: despesas, receitas)
            $table->enum('status', ['pendente', 'concluida', 'cancelada'])->default('pendente'); // Status da movimentação
            $table->softDeletes(); // Suporte a soft deletes (adiciona a coluna deleted_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimentacoes', function (Blueprint $table) {
            // Remove as colunas adicionadas no método 'up'
            $table->dropColumn('data');
            $table->dropColumn('categoria');
            $table->dropColumn('status');
            $table->dropSoftDeletes(); // Remove a coluna deleted_at
        });
    }
};
