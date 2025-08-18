<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            // O "DE": Qual é o nosso lançamento padrão?
            $table->foreignId('lancamento_padrao_id')->constrained('lancamento_padraos')->onDelete('cascade');

            // O "PARA": Para quais contas contábeis ele vai?
            $table->foreignId('conta_debito_id')->constrained('chart_of_accounts')->comment('Conta que será debitada');
            $table->foreignId('conta_credito_id')->constrained('chart_of_accounts')->comment('Conta que será creditada');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_mappings');
    }
};
