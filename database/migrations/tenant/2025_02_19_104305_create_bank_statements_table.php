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
        Schema::create('bank_statements', function (Blueprint $table) {
            $table->id();
            // Exemplo de relação com a company, se necessário
            $table->unsignedBigInteger('company_id')->nullable();
            // Adiciona a coluna para vincular com entidades_financeiras
            $table->unsignedBigInteger('entidade_financeira_id')->nullable();
            // Informações básicas do extrato
            $table->string('bank_id')->nullable();     // Banco/agency info
            $table->string('branch_id')->nullable();   // Agência
            $table->string('account_id')->nullable();  // Conta
            $table->string('account_type')->nullable(); // Tipo (CHECKING, SAVINGS)

            // Dados da transação
            $table->string('trntype')->nullable();     // OTHER, DEBIT, CREDIT etc.
            $table->dateTime('dtposted')->nullable();  // Data/hora
            $table->decimal('amount', 10, 2)->nullable();  // Valor
            $table->string('fitid')->nullable();       // ID único da transação no OFX
            $table->string('checknum')->nullable();    // Número de cheque, se houver
            $table->string('refnum')->nullable();      // Número de referência
            $table->text('memo')->nullable();          // Descrição ou histórico
            $table->boolean('reconciled')->default(false); // Marcador de conciliação
            $table->string('status_conciliacao')->default('pendente');

            // Carimbo de tempo de criação/atualização
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            // Adiciona a chave estrangeira para garantir integridade
            $table->foreign('entidade_financeira_id')->references('id')->on('entidades_financeiras')
            ->onDelete('cascade'); // Se excluir a entidade, remove os lançamentos dela

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statements');
    }
};
