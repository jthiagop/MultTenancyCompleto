<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComprovacaoFiscalToCaixasAndBancosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->boolean('comprovacao_fiscal')->default(false)->after('updated_by'); // Adiciona a coluna com valor padrão false
        });

        Schema::table('bancos', function (Blueprint $table) {
            $table->boolean('comprovacao_fiscal')->default(false)->after('updated_by'); // Adiciona a coluna com valor padrão false
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->dropColumn('comprovacao_fiscal');
        });

        Schema::table('bancos', function (Blueprint $table) {
            $table->dropColumn('comprovacao_fiscal');
        });
    }
}

