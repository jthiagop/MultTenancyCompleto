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
        if (!Schema::hasTable('fiel_parents')) {
            Schema::create('fiel_parents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->string('nome_pai')->nullable();
                $table->string('nome_mae')->nullable();
                $table->timestamps();
                
                $table->unique('fiel_id');
            });
        } else {
            // Se a tabela já existe, verificar se tem todas as colunas
            Schema::table('fiel_parents', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_parents', 'nome_pai')) {
                    $table->string('nome_pai')->nullable()->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_parents', 'nome_mae')) {
                    $table->string('nome_mae')->nullable()->after('nome_pai');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_parents');
    }
};
