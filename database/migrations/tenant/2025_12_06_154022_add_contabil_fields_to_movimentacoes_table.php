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
            // Adicionar data_competencia após created_at
            $table->date('data_competencia')->nullable()->after('created_at');
            
            // Adicionar foreign keys nullable
            $table->unsignedBigInteger('lancamento_padrao_id')->nullable()->after('data_competencia');
            $table->unsignedBigInteger('conta_debito_id')->nullable()->after('lancamento_padrao_id');
            $table->unsignedBigInteger('conta_credito_id')->nullable()->after('conta_debito_id');
            
            // Adicionar foreign key constraints
            $table->foreign('lancamento_padrao_id')->references('id')->on('lancamento_padraos')->onDelete('set null');
            $table->foreign('conta_debito_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
            $table->foreign('conta_credito_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimentacoes', function (Blueprint $table) {
            // Remover foreign keys primeiro
            $table->dropForeign(['lancamento_padrao_id']);
            $table->dropForeign(['conta_debito_id']);
            $table->dropForeign(['conta_credito_id']);
            
            // Remover colunas
            $table->dropColumn(['data_competencia', 'lancamento_padrao_id', 'conta_debito_id', 'conta_credito_id']);
        });
    }
};
