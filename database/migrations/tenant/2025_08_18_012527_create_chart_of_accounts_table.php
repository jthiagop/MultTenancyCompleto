<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->string('code')->comment('Código da conta, ex: 1.01.01.001');
            $table->string('name')->comment('Nome da conta, ex: Caixa Geral');
            $table->enum('type', ['ativo', 'passivo', 'patrimonio_liquido', 'receita', 'despesa']);
            
            // Para estrutura de árvore (hierarquia)
            $table->foreignId('parent_id')->nullable()->constrained('chart_of_accounts')->onDelete('cascade');

            $table->timestamps();

            // Garante que o código da conta seja único por empresa
            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
