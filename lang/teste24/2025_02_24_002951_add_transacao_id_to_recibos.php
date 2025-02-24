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
            Schema::table('recibos', function (Blueprint $table) {
                // Adiciona a chave estrangeira para o endereço
                $table->unsignedBigInteger('address_id')->nullable()->after('transacao_id');
                $table->foreign('address_id')->references('id')->on('adresses')->onDelete('set null');
            });
        }

        public function down(): void
        {
            Schema::table('recibos', function (Blueprint $table) {
                $table->dropForeign(['address_id']);
                $table->dropColumn('address_id');
            });
        }
    };
