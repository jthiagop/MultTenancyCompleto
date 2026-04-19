<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sepultados', function (Blueprint $table) {
            $table->string('cpf', 20)->nullable()->after('nome');
            $table->string('tumulo_codigo', 50)->nullable()->after('causa_mortis');
            $table->text('observacoes')->nullable()->after('tumulo_codigo');

            // data_nascimento já existe mas é NOT NULL — tornar nullable
            $table->date('data_nascimento')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('sepultados', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'tumulo_codigo', 'observacoes']);
            $table->date('data_nascimento')->nullable(false)->change();
        });
    }
};
