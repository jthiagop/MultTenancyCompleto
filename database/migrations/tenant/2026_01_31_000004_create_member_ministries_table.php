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
        if (Schema::hasTable('member_ministries')) {
            return;
        }
        
        Schema::create('member_ministries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                ->constrained('religious_members')
                ->cascadeOnDelete();

            $table->foreignId('ministry_type_id')
                ->constrained('ministry_types')
                ->cascadeOnDelete();

            $table->date('date');

            // Diocese: começando com texto para flexibilidade
            $table->string('diocese_name')->nullable();

            // Ministrante: texto para flexibilidade (pode ser externo ao sistema)
            $table->string('minister_name')->nullable();

            // Observações adicionais
            $table->text('notes')->nullable();

            $table->timestamps();

            // Índices para consultas frequentes
            $table->index(['member_id', 'ministry_type_id']);
            $table->index(['date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_ministries');
    }
};
