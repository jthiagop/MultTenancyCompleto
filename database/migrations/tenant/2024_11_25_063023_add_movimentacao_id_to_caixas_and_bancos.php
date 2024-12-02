<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMovimentacaoIdToCaixasAndBancos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Adicionar a coluna na tabela caixas
        Schema::table('caixas', function (Blueprint $table) {
            $table->foreignId('movimentacao_id')
                  ->nullable()
                  ->constrained('movimentacoes')
                  ->onDelete('cascade');
        });

        // Adicionar a coluna na tabela bancos
        Schema::table('bancos', function (Blueprint $table) {
            $table->foreignId('movimentacao_id')
                  ->nullable()
                  ->constrained('movimentacoes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remover a coluna da tabela caixas
        Schema::table('caixas', function (Blueprint $table) {
            $table->dropForeign(['movimentacao_id']);
            $table->dropColumn('movimentacao_id');
        });

        // Remover a coluna da tabela bancos
        Schema::table('bancos', function (Blueprint $table) {
            $table->dropForeign(['movimentacao_id']);
            $table->dropColumn('movimentacao_id');
        });
    }
}

