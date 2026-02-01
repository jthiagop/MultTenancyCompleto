<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('member_formation_periods', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('religious_member_id')
                ->constrained('religious_members')
                ->cascadeOnDelete();

            $table->foreignId('formation_stage_id')
                ->constrained('formation_stages')
                ->cascadeOnDelete();

            // Local - pode ser uma company (casa/convento) ou texto livre
            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('place_text')->nullable(); // Para locais não cadastrados (ex: "Recife - Grupo Vocacional")

            // Datas do período
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Nullable = período atual/em andamento

            // Dados adicionais
            $table->text('notes')->nullable();
            $table->boolean('is_current')->default(false); // Marca se é o período atual

            $table->timestamps();

            // Índices para consultas frequentes
            $table->index(['religious_member_id', 'formation_stage_id'], 'member_stage_idx');
            $table->index(['company_id']);
            $table->index(['is_current']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_formation_periods');
    }
};
