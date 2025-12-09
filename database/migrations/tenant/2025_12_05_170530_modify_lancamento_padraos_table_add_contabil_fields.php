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
        Schema::table('lancamento_padraos', function (Blueprint $table) {
            // Adiciona company_id se não existir (necessário para multi-tenancy)
            if (!Schema::hasColumn('lancamento_padraos', 'company_id')) {
                $table->foreignId('company_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('companies')
                      ->onDelete('cascade')
                      ->comment('ID da empresa (multi-tenancy)');
            }

            // Adicionamos as colunas que faltam para a contabilidade
            $table->foreignId('conta_debito_id')
                  ->nullable() // Nullable por enquanto, para não dar erro nos dados que já existem
                  ->constrained('chart_of_accounts')
                  ->comment('Conta que recebe o Débito (ex: Despesa de Luz)');

            $table->foreignId('conta_credito_id')
                  ->nullable()
                  ->constrained('chart_of_accounts')
                  ->comment('Conta que recebe o Crédito (ex: Caixa ou Banco)');

            // Removemos a coluna date, pois um "Padrão" é atemporal
            $table->dropColumn('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lancamento_padraos', function (Blueprint $table) {
            $table->dropForeign(['conta_debito_id']);
            $table->dropForeign(['conta_credito_id']);
            $table->dropColumn(['conta_debito_id', 'conta_credito_id']);

            // Remove company_id se foi adicionado
            if (Schema::hasColumn('lancamento_padraos', 'company_id')) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            }

            $table->date('date')->nullable();
        });
    }
};
