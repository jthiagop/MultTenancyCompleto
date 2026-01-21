<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Data de vencimento (para calcular "Atrasado")
            if (!Schema::hasColumn('transacoes_financeiras', 'data_vencimento')) {
                $table->date('data_vencimento')->nullable()->after('data_competencia');
            }

            // Data de pagamento (para calcular "Pago Parcial")
            if (!Schema::hasColumn('transacoes_financeiras', 'data_pagamento')) {
                $table->date('data_pagamento')->nullable()->after('data_vencimento');
            }

            // Campos financeiros para cálculos
            if (!Schema::hasColumn('transacoes_financeiras', 'valor_pago')) {
                $table->decimal('valor_pago', 15, 2)->default(0)->after('valor');
            }
            if (!Schema::hasColumn('transacoes_financeiras', 'juros')) {
                $table->decimal('juros', 15, 2)->default(0)->after('valor_pago');
            }
            if (!Schema::hasColumn('transacoes_financeiras', 'multa')) {
                $table->decimal('multa', 15, 2)->default(0)->after('juros');
            }
            if (!Schema::hasColumn('transacoes_financeiras', 'desconto')) {
                $table->decimal('desconto', 15, 2)->default(0)->after('multa');
            }
            if (!Schema::hasColumn('transacoes_financeiras', 'valor_a_pagar')) {
                $table->decimal('valor_a_pagar', 15, 2)->nullable()->after('desconto');
            }

            // Situação do lançamento
            if (!Schema::hasColumn('transacoes_financeiras', 'situacao')) {
               $table->string('situacao')->default('em_aberto')->after('comprovacao_fiscal');
            }

            // Status de agendamento
            if (!Schema::hasColumn('transacoes_financeiras', 'agendado')) {
                $table->boolean('agendado')->default(false)->after('situacao');
            }

            // Índices para consultas frequentes (Laravel nomeia automaticamente)
            $table->index(['company_id', 'situacao']);
            $table->index(['company_id', 'agendado']);
            $table->index(['data_vencimento', 'situacao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacoes_financeiras', function (Blueprint $table) {
            // Remove índices (Laravel nomeia automaticamente como: tabela_colunas_index)
            // Não precisamos especificar nomes customizados
            $table->dropIndex(['company_id', 'situacao']);
            $table->dropIndex(['company_id', 'agendado']);
            $table->dropIndex(['data_vencimento', 'situacao']);

            // Remove as colunas se existirem
            $columns = [
                'data_vencimento',
                'valor_pago',
                'juros',
                'multa',
                'desconto',
                'valor_a_pagar',
                'situacao',
                'agendado'
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('transacoes_financeiras', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
