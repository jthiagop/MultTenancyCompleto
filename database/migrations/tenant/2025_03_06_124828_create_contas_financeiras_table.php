<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContasFinanceirasTable extends Migration
{
    public function up()
    {
        Schema::create('contas_financeiras', function (Blueprint $table) {
            $table->id();

            // Exemplo de relação com a company, se necessário
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('fornecedor_id')->nullable()->constrained('fornecedores')->onDelete('cascade');
            $table->date('data_competencia');
            $table->text('descricao');
            $table->decimal('valor', 15, 2);
            // NOVO: Campo para definir se é receita ou despesa
            $table->enum('tipo_financeiro', ['receita', 'despesa']);
            $table->foreignId('cost_centers_id')->nullable()->constrained('cost_centers')->onDelete('set null');
            $table->foreignId('lancamento_padraos_id')->nullable()->constrained('lancamento_padraos')->onDelete('set null');
            $table->boolean('repetir')->default(false);
            $table->integer('intervalo_repeticao')->nullable();
            $table->enum('frequencia', ['diario', 'semanal', 'mensal', 'anual'])->nullable();
            $table->integer('parcelamento')->nullable();
            $table->date('data_primeiro_vencimento')->nullable();
            $table->foreignId('forma_pagamento_id')->nullable()->constrained('formas_pagamento')->onDelete('set null');
            $table->foreignId('entidade_financeira_id')->nullable()->constrained('entidades_financeiras')->onDelete('set null');
            $table->text('observacoes')->nullable();
            $table->decimal('valor_pago', 15, 2)->nullable();
            $table->decimal('juros', 15, 2)->nullable();
            $table->decimal('multa', 15, 2)->nullable();
            $table->decimal('desconto', 15, 2)->nullable();
            $table->enum('status_pagamento', ['em aberto','pendente', 'pago', 'vencido', 'cancelado'])->default('pendente');

            // Exemplo de relação com a company, se necessário
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down()
    {
        Schema::dropIfExists('contas_financeiras'); // Corrigido
    }
}
