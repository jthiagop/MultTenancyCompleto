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
        if (!Schema::hasTable('fiel_contacts')) {
        Schema::create('fiel_contacts', function (Blueprint $table) {
            $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->enum('tipo', ['telefone', 'telefone_secundario', 'email'])->default('telefone');
                $table->string('valor');
                $table->boolean('principal')->default(false);
            $table->timestamps();
        });
        } else {
            // Se a tabela já existe, adicionar colunas que faltam
            Schema::table('fiel_contacts', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_contacts', 'fiel_id')) {
                    $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->after('id');
                }
                if (!Schema::hasColumn('fiel_contacts', 'tipo')) {
                    $table->enum('tipo', ['telefone', 'telefone_secundario', 'email'])->default('telefone')->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_contacts', 'valor')) {
                    $table->string('valor')->after('tipo');
                }
                if (!Schema::hasColumn('fiel_contacts', 'principal')) {
                    $table->boolean('principal')->default(false)->after('valor');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_contacts');
    }
};
