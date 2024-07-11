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
        Schema::table('users', function (Blueprint $table) {
            $table->string('login_ip')->nullable();
            $table->decimal('login_latitude', 10, 7)->nullable();
            $table->decimal('login_longitude', 10, 7)->nullable();
            $table->string('login_city')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_ip');
            $table->dropColumn('login_latitude');
            $table->dropColumn('login_longitude');
            $table->dropColumn('login_city');
        });
    }

};
