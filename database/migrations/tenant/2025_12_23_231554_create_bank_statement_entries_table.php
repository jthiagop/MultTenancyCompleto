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
        Schema::create('bank_statement_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Vínculo com o lote (Saber de onde veio essa linha)
            $table->foreignId('import_id')->constrained('bank_statement_imports')->onDelete('cascade');
            
            // Dados Bancários Reais
            $table->date('posted_at'); // Data do lançamento
            $table->string('description'); // Histórico/Memo
            $table->string('document_number')->nullable(); // Nº Doc / Ref
            
            // Valores (Otimizado)
            // amount_signed: Crédito positivo (100.00), Débito negativo (-100.00)
            // Isso facilita muito somar saldos com SUM()
            $table->decimal('amount', 15, 2); // Valor absoluto
            $table->string('type'); // 'CREDIT' ou 'DEBIT'
            $table->decimal('amount_signed', 15, 2); 
            
            // Saldo após transação (Auditoria do BB)
            $table->decimal('balance_after', 15, 2)->nullable();
            
            // Hash Único da LINHA (Obrigatório para API não duplicar)
            // MD5(data + valor + documento + tipo)
            $table->string('unique_hash')->index(); 
            
            // Status de Conciliação
            $table->string('status_conciliacao')->default('pendente'); // pendente, conciliado, ignorado
            
            // JSON para guardar dados extras do BB que não tem coluna (ex: código de barras, pagador)
            $table->json('bank_metadata')->nullable();

            $table->timestamps();
            
            // Trava de segurança para não duplicar a mesma linha na mesma conta
            $table->unique(['import_id', 'unique_hash'], 'unique_entry_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_statement_entries');
    }
};
