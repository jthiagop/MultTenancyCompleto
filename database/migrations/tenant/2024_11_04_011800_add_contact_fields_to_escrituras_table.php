<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
/**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('escrituras', function (Blueprint $table) {
            // Campos para telefone e email do outorgante
            $table->string('outorgante_telefone')->nullable();
            $table->string('outorgante_email')->nullable();

            // Campos para telefone e email do outorgado
            $table->string('outorgado_telefone')->nullable();
            $table->string('outorgado_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('escrituras', function (Blueprint $table) {
            // Remove os campos adicionados caso a migration seja revertida
            $table->dropColumn('outorgante_telefone');
            $table->dropColumn('outorgante_email');
            $table->dropColumn('outorgado_telefone');
            $table->dropColumn('outorgado_email');
        });
    }
};
