<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Adicionar campos extras em religious_members (sem endereço)
        Schema::table('religious_members', function (Blueprint $table) {
            $table->string('cpf', 20)->nullable()->after('order_registration_number');
            $table->text('observacoes')->nullable()->after('priestly_ordination_date');
            $table->boolean('disponivel_todas_casas')->default(true)->after('is_active');
        });

        // Criar tabela pivot para relacionar membros religiosos com endereços
        Schema::create('religious_member_address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('religious_member_id')
                ->constrained('religious_members')
                ->onDelete('cascade');
            $table->foreignId('address_id')
                ->constrained('adresses')
                ->onDelete('cascade');
            $table->string('tipo')->default('origem')->comment('origem, atual, correspondencia');
            $table->timestamps();

            $table->unique(['religious_member_id', 'address_id', 'tipo'], 'member_address_tipo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('religious_member_address');

        Schema::table('religious_members', function (Blueprint $table) {
            $table->dropColumn(['cpf', 'observacoes', 'disponivel_todas_casas']);
        });
    }
};
