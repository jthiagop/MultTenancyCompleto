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
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_recibo')->unique();
            $table->date('data_emissao');
            $table->decimal('valor', 10, 2); // Ex: 710.00
            $table->enum('tipo_transacao', ['pagamento', 'recebimento']);

            // Dados da pessoa/fornecedor
            $table->enum('tipo_pessoa', ['fisica', 'juridica']);
            $table->string('cpf_cnpj', 20)->nullable();
            $table->string('nome', 100);
            $table->string('endereco')->nullable();
            $table->string('cidade', 50)->nullable();
            $table->string('estado', 2)->nullable();

            // Descritivo
            $table->text('referente')->nullable();

            // Foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recibos');
    }
};
