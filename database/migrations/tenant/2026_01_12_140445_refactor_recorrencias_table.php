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
        // Modificar tabela recorrencias
        Schema::table('recorrencias', function (Blueprint $table) {
            // Remover foreign keys primeiro
            $table->dropForeign(['transacao_financeira_id']);
            $table->dropForeign(['movimentacao_id']);
            
            // Remover colunas
            $table->dropColumn(['transacao_financeira_id', 'movimentacao_id']);
            
            // Adicionar coluna nome
            $table->string('nome')->nullable()->after('company_id');
        });

        // Adicionar recorrencia_id em transacoes_financeiras
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->unsignedBigInteger('recorrencia_id')->nullable()->after('movimentacao_id');
            $table->foreign('recorrencia_id')->references('id')->on('recorrencias')->onDelete('set null');
            $table->index('recorrencia_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter mudanças em transacoes_financeiras
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            $table->dropForeign(['recorrencia_id']);
            $table->dropIndex(['recorrencia_id']);
            $table->dropColumn('recorrencia_id');
        });

        // Reverter mudanças em recorrencias
        Schema::table('recorrencias', function (Blueprint $table) {
            $table->dropColumn('nome');
            $table->unsignedBigInteger('transacao_financeira_id')->nullable()->after('company_id');
            $table->unsignedBigInteger('movimentacao_id')->nullable()->after('transacao_financeira_id');
            $table->foreign('transacao_financeira_id')->references('id')->on('transacoes_financeiras')->onDelete('cascade');
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes')->onDelete('cascade');
        });
    }
};
