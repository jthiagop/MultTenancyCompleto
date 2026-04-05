<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formas_recebimento', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100);
            $table->string('codigo', 50)->unique();
            $table->boolean('ativo')->default(true);
            $table->text('observacao')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('repasses', function (Blueprint $table) {
            $table->foreignId('forma_recebimento_id')
                ->nullable()
                ->after('forma_pagamento_id')
                ->constrained('formas_recebimento')
                ->onDelete('set null')
                ->comment('Forma de recebimento do repasse');
        });
    }

    public function down(): void
    {
        Schema::table('repasses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('forma_recebimento_id');
        });
        Schema::dropIfExists('formas_recebimento');
    }
};
