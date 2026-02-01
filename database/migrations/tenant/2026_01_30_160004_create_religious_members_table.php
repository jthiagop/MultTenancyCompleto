<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('religious_members', function (Blueprint $table) {
            $table->id();

            // Identificação
            $table->string('name');
            $table->string('order_registration_number')->nullable();
            $table->string('avatar')->nullable();

            // Relacionamentos principais
            $table->foreignId('province_id')
                ->nullable()
                ->constrained('provinces')
                ->nullOnDelete();

            $table->foreignId('religious_role_id')
                ->nullable()
                ->constrained('religious_roles')
                ->nullOnDelete();

            $table->foreignId('current_stage_id')
                ->nullable()
                ->constrained('formation_stages')
                ->nullOnDelete();

            // Dados pessoais
            $table->date('birth_date')->nullable();

            // Profissões (votos)
            $table->date('temporary_profession_date')->nullable(); // votos simples
            $table->date('perpetual_profession_date')->nullable();  // votos perpétuos

            // Ordenações
            $table->date('diaconal_ordination_date')->nullable();
            $table->date('priestly_ordination_date')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['province_id']);
            $table->index(['religious_role_id']);
            $table->index(['current_stage_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('religious_members');
    }
};