<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repasse_itens', function (Blueprint $table) {
            $table->foreignId('entidade_destino_id')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('repasse_itens', function (Blueprint $table) {
            $table->foreignId('entidade_destino_id')
                ->nullable(false)
                ->change();
        });
    }
};
