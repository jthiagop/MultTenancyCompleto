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
        Schema::table('users', function (Blueprint $table) {
            $table->text('details')->nullable(); // Para detalhes do usuário
            $table->boolean('active')->default(1); // 1 = Ativo, 0 = Desativado
            $table->json('notifications')->nullable(); // Preferências de notificações (email/telefone)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['details', 'active', 'notifications']);
        });
    }
};
