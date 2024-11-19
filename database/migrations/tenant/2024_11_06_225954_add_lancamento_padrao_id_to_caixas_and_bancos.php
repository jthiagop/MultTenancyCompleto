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
        Schema::table('caixas', function (Blueprint $table) {
            // Adiciona a coluna 'lancamento_padrao_id' como chave estrangeira
            $table->foreignId('lancamento_padrao_id')->nullable()->constrained('lancamento_padraos')->onDelete('set null');
        });

        Schema::table('bancos', function (Blueprint $table) {
            // Adiciona a coluna 'lancamento_padrao_id' como chave estrangeira
            $table->foreignId('lancamento_padrao_id')->nullable()->constrained('lancamento_padraos')->onDelete('set null');
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
            $table->dropForeign(['lancamento_padrao_id']);
            $table->dropColumn('lancamento_padrao_id');
        });

        Schema::table('bancos', function (Blueprint $table) {
            $table->dropForeign(['lancamento_padrao_id']);
            $table->dropColumn('lancamento_padrao_id');
        });
    }
};
