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
        Schema::table('recibos', function (Blueprint $table) {
            if (Schema::hasColumn('recibos', 'tipo_pessoa')) {
                $table->dropColumn('tipo_pessoa');
            }
        });
    }

    public function down()
    {
        Schema::table('recibos', function (Blueprint $table) {
            $table->string('tipo_pessoa')->nullable();
        });
    }
};
