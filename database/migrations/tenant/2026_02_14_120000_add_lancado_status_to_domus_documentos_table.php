<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Converte a coluna status de ENUM para STRING.
     * Isso permite adicionar novos status diretamente no código
     * (via PHP Enum StatusDomusDocumento) sem precisar de novas migrations.
     */
    public function up(): void
    {
        // 1. Corrigir registros com status vazio ou nulo antes de converter
        \DB::table('domus_documentos')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'pendente']);

        // 2. Converter coluna de ENUM para STRING
        Schema::table('domus_documentos', function (Blueprint $table) {
            $table->string('status', 30)->default('pendente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverter status não suportados pelo enum original
        \DB::table('domus_documentos')
            ->where('status', 'lancado')
            ->update(['status' => 'processado']);

        \DB::statement("ALTER TABLE domus_documentos MODIFY COLUMN status ENUM('pendente', 'processado', 'erro', 'arquivado') DEFAULT 'pendente'");
    }
};
