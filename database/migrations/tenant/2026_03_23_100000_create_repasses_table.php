<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela mestre de repasses entre matriz e filiais.
     *
     * Cada repasse pode gerar N itens (1 por filial destino).
     * Cada item gera transações financeiras (saída na matriz, entrada na filial).
     */
    public function up(): void
    {
        Schema::create('repasses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_origem_id')
                ->constrained('companies')
                ->onDelete('cascade')
                ->comment('Empresa que envia (matriz)');

            $table->foreignId('entidade_origem_id')
                ->constrained('entidades_financeiras')
                ->onDelete('cascade')
                ->comment('Conta bancária/caixa de saída da matriz');

            $table->enum('tipo', ['rateio', 'repasse_direto'])
                ->default('repasse_direto')
                ->comment('rateio = divide entre filiais; repasse_direto = valor fixo');

            $table->enum('criterio_rateio', ['percentual', 'valor_fixo'])
                ->default('valor_fixo')
                ->comment('Como o valor é distribuído entre as filiais');

            $table->decimal('valor_total', 15, 2)
                ->comment('Montante total a ser distribuído');

            $table->date('data_emissao')
                ->comment('Data de emissão do repasse');

            $table->date('data_entrada')
                ->nullable()
                ->comment('Data de entrada/recebimento');

            $table->date('data_vencimento')
                ->nullable()
                ->comment('Data de vencimento do repasse');

            $table->string('competencia', 20)
                ->nullable()
                ->comment('Mês/ano de competência (ex: 03/2026)');

            $table->string('tipo_documento', 50)
                ->nullable()
                ->comment('Tipo do documento (boleto, pix, etc.)');

            $table->string('numero_documento', 100)
                ->nullable()
                ->comment('Número do documento');

            $table->foreignId('forma_pagamento_id')
                ->nullable()
                ->constrained('formas_pagamento')
                ->onDelete('set null')
                ->comment('Forma de pagamento utilizada');

            $table->string('descricao', 500)
                ->nullable()
                ->comment('Descrição/justificativa do repasse');

            $table->enum('status', ['pendente', 'executado', 'cancelado'])
                ->default('pendente')
                ->comment('Status do repasse');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Usuário que criou o repasse');

            $table->timestamps();
            $table->softDeletes();

            $table->index('company_origem_id', 'idx_repasses_company_origem');
            $table->index('status', 'idx_repasses_status');
            $table->index('data_emissao', 'idx_repasses_data_emissao');
            $table->index('competencia', 'idx_repasses_competencia');
        });

        Schema::create('repasse_itens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('repasse_id')
                ->constrained('repasses')
                ->onDelete('cascade')
                ->comment('Repasse pai');

            $table->foreignId('company_destino_id')
                ->constrained('companies')
                ->onDelete('cascade')
                ->comment('Filial destino');

            $table->foreignId('entidade_destino_id')
                ->constrained('entidades_financeiras')
                ->onDelete('cascade')
                ->comment('Conta bancária/caixa de recebimento na filial');

            $table->decimal('percentual', 5, 2)
                ->nullable()
                ->comment('Percentual do rateio (quando critério = percentual)');

            $table->decimal('valor', 15, 2)
                ->comment('Valor do repasse para esta filial');

            $table->foreignId('transacao_saida_id')
                ->nullable()
                ->constrained('transacoes_financeiras')
                ->onDelete('set null')
                ->comment('Transação de saída gerada na matriz');

            $table->foreignId('transacao_entrada_id')
                ->nullable()
                ->constrained('transacoes_financeiras')
                ->onDelete('set null')
                ->comment('Transação de entrada gerada na filial');

            $table->foreignId('movimentacao_saida_id')
                ->nullable()
                ->constrained('movimentacoes')
                ->onDelete('set null')
                ->comment('Movimentação de saída (saldo da matriz)');

            $table->foreignId('movimentacao_entrada_id')
                ->nullable()
                ->constrained('movimentacoes')
                ->onDelete('set null')
                ->comment('Movimentação de entrada (saldo da filial)');

            $table->timestamps();

            $table->index('repasse_id', 'idx_repasse_itens_repasse');
            $table->index('company_destino_id', 'idx_repasse_itens_destino');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repasse_itens');
        Schema::dropIfExists('repasses');
    }
};
