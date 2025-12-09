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
        if (!Schema::hasTable('fiel_religious_data')) {
        Schema::create('fiel_religious_data', function (Blueprint $table) {
            $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->date('data_batismo')->nullable();
                $table->string('local_batismo')->nullable();
                $table->date('data_casamento')->nullable();
                $table->string('local_casamento')->nullable();
                $table->date('data_ingresso')->nullable();
                $table->string('responsavel_ingresso')->nullable();
                $table->string('grupo_participante')->nullable();
                $table->string('ministerio')->nullable();
                $table->unsignedBigInteger('comunidade_id')->nullable();
            $table->timestamps();
                
                $table->unique('fiel_id');
            });
        } else {
            // Se a tabela já existe, verificar e adicionar colunas que faltam
            Schema::table('fiel_religious_data', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_religious_data', 'data_batismo')) {
                    $table->date('data_batismo')->nullable()->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'local_batismo')) {
                    $table->string('local_batismo')->nullable()->after('data_batismo');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'data_casamento')) {
                    $table->date('data_casamento')->nullable()->after('local_batismo');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'local_casamento')) {
                    $table->string('local_casamento')->nullable()->after('data_casamento');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'data_ingresso')) {
                    $table->date('data_ingresso')->nullable()->after('local_casamento');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'responsavel_ingresso')) {
                    $table->string('responsavel_ingresso')->nullable()->after('data_ingresso');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'grupo_participante')) {
                    $table->string('grupo_participante')->nullable()->after('responsavel_ingresso');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'ministerio')) {
                    $table->string('ministerio')->nullable()->after('grupo_participante');
                }
                if (!Schema::hasColumn('fiel_religious_data', 'comunidade_id')) {
                    $table->unsignedBigInteger('comunidade_id')->nullable()->after('ministerio');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_religious_data');
    }
};
