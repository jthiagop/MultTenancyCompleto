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
        Schema::create('contas_contabeis', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Código único da conta contábil
            $table->string('nome'); // Nome da conta contábil
            $table->text('descricao')->nullable(); // Descrição da conta, opcional
            $table->enum('tipo', ['ativo', 'passivo', 'patrimonio_liquido', 'receita', 'despesa']); // Tipo de conta
            $table->enum('natureza', ['debito', 'credito']); // Natureza da conta para débito ou crédito
            $table->foreignId('conta_pai_id')->nullable()->constrained('contas_contabeis')->onDelete('cascade'); // Conta pai para hierarquia
            $table->decimal('saldo_inicial', 15, 2)->default(0); // Saldo inicial da conta
            $table->decimal('saldo_atual', 15, 2)->default(0); // Saldo atual da conta
            $table->boolean('permite_lancamentos')->default(true); // Permite lançamentos diretos
            $table->enum('status', ['ativo', 'inativo'])->default('ativo'); // Status da conta contábil
            $table->unsignedTinyInteger('nivel')->default(1); // Nível hierárquico

            // Campos de auditoria
            $table->unsignedBigInteger('created_by')->nullable(); // Supondo que você já tenha a coluna created_by
            $table->unsignedBigInteger('updated_by')->nullable(); // Supondo que você já tenha a coluna updated_by

            $table->timestamps(); // Timestamps de criação e atualização

            // Definições das chaves estrangeiras
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null'); // Usuário que atualizou a conta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contas_contabeis');
    }
};

