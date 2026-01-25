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
        if (!Schema::hasTable('tenant_filials')) {
            Schema::create('tenant_filials', function (Blueprint $table) {
                $table->bigIncrements('id')->primary();


                $table->string('name')->unique();
                $table->uuid('uuid')->nullable();
                $table->string('photo')->nullable();
                $table->timestamps();
                $table->json('data')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_filials');
    }
};
