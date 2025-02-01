<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->unsignedBigInteger('banco_id')->nullable()->after('tipo');
            $table->string('agencia')->nullable()->after('banco_id');
            $table->string('conta')->nullable()->after('agencia');

            // Se quiser relacionar 'banco_id' a outra tabela, crie a foreign key:
            // $table->foreign('banco_id')->references('id')->on('cadastro_bancos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('entidades_financeiras', function (Blueprint $table) {
            $table->dropColumn('banco_id');
            $table->dropColumn('agencia');
            $table->dropColumn('conta');

            // Se criou foreign keys, lembre de remover:
            // $table->dropForeign(['banco_id']);
        });
    }
};
