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
        if (!Schema::hasTable('fiel_children')) {
            Schema::create('fiel_children', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->string('nome');
                $table->date('data_nascimento')->nullable();
                $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable();
                $table->enum('sexo', ['M', 'F', 'Outro'])->nullable();
                $table->timestamps();
            });
        } else {
            // Se a tabela já existe, verificar e adicionar colunas que faltam
            Schema::table('fiel_children', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_children', 'nome')) {
                    $table->string('nome')->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_children', 'data_nascimento')) {
                    $table->date('data_nascimento')->nullable()->after('nome');
                }
                if (!Schema::hasColumn('fiel_children', 'estado_civil')) {
                    $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable()->after('data_nascimento');
                }
                if (!Schema::hasColumn('fiel_children', 'sexo')) {
                    $table->enum('sexo', ['M', 'F', 'Outro'])->nullable()->after('estado_civil');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_children');
    }
};
