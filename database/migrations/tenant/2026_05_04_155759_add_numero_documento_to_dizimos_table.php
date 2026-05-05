<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adiciona `numero_documento` aos lançamentos de dízimo/doação para
 * facilitar a conciliação bancária. O campo é replicado para a
 * `transacoes_financeiras.numero_documento`, que é comparado com o
 * `bank_statements.checknum` no matching automático.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dizimos', function (Blueprint $table) {
            $table->string('numero_documento', 50)
                ->nullable()
                ->after('forma_pagamento');

            $table->index(['company_id', 'numero_documento'], 'dizimos_numero_doc_idx');
        });
    }

    public function down(): void
    {
        Schema::table('dizimos', function (Blueprint $table) {
            $table->dropIndex('dizimos_numero_doc_idx');
            $table->dropColumn('numero_documento');
        });
    }
};
