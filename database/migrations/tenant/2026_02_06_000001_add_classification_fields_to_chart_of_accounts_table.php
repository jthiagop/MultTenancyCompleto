<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            // CRÍTICO: Define se aceita lançamentos (true) ou se é grupo (false)
            $table->boolean('is_analytical')
                  ->default(false)
                  ->after('type')
                  ->comment('True = Analítica (aceita lançamento), False = Sintética (apenas grupo)');
            
            // CRÍTICO: Define se é conta redutora (sinal invertido, ex: Depreciação)
            $table->boolean('is_deductible')
                  ->default(false)
                  ->after('is_analytical')
                  ->comment('Conta Redutora/Retificadora');
            
            // Código usado no sistema de destino (Alterdata)
            $table->string('external_code')
                  ->nullable()
                  ->after('is_deductible')
                  ->comment('Código Reduzido/Chamada no Alterdata');
            
            // Soft deletes para não perder histórico
            $table->softDeletes();
            
            // Índice adicional para performance em filtros por tipo
            $table->index(['company_id', 'type']);
        });

        // Adiciona índice ao campo code (se ainda não existir)
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->dropIndex(['code']);
            $table->dropIndex(['company_id', 'type']);
            $table->dropSoftDeletes();
            $table->dropColumn(['is_analytical', 'is_deductible', 'external_code']);
        });
    }
};
