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
        if (!Schema::hasTable('fiel_tithe')) {
            Schema::create('fiel_tithe', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->boolean('dizimista')->default(false);
                $table->string('codigo')->nullable();
                $table->decimal('percentual_salario', 5, 2)->nullable();
                $table->string('cartao_magnetico')->nullable();
                $table->string('missionario_dizimo')->nullable();
                $table->decimal('valor_dizimo', 10, 2)->nullable();
                $table->enum('frequencia_dizimo', ['Mensal', 'Semanal', 'Anual'])->nullable();
                $table->date('ultima_contribuicao')->nullable();
                $table->timestamps();
                
                $table->unique('fiel_id');
            });
        } else {
            // Se a tabela já existe, verificar e adicionar colunas que faltam
            Schema::table('fiel_tithe', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_tithe', 'dizimista')) {
                    $table->boolean('dizimista')->default(false)->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_tithe', 'codigo')) {
                    $table->string('codigo')->nullable()->after('dizimista');
                }
                if (!Schema::hasColumn('fiel_tithe', 'percentual_salario')) {
                    $table->decimal('percentual_salario', 5, 2)->nullable()->after('codigo');
                }
                if (!Schema::hasColumn('fiel_tithe', 'cartao_magnetico')) {
                    $table->string('cartao_magnetico')->nullable()->after('percentual_salario');
                }
                if (!Schema::hasColumn('fiel_tithe', 'missionario_dizimo')) {
                    $table->string('missionario_dizimo')->nullable()->after('cartao_magnetico');
                }
                if (!Schema::hasColumn('fiel_tithe', 'valor_dizimo')) {
                    $table->decimal('valor_dizimo', 10, 2)->nullable()->after('missionario_dizimo');
                }
                if (!Schema::hasColumn('fiel_tithe', 'frequencia_dizimo')) {
                    $table->enum('frequencia_dizimo', ['Mensal', 'Semanal', 'Anual'])->nullable()->after('valor_dizimo');
                }
                if (!Schema::hasColumn('fiel_tithe', 'ultima_contribuicao')) {
                    $table->date('ultima_contribuicao')->nullable()->after('frequencia_dizimo');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_tithe');
    }
};
