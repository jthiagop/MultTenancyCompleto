<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntidadesFinanceirasTable extends Migration
{
    public function up()
    {
        Schema::create('entidades_financeiras', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Nome da entidade financeira
            $table->enum('tipo', ['caixa', 'banco', 'dizimo', 'coleta', 'doacao']); // Tipos
            $table->decimal('saldo_inicial', 15, 2)->default(0); // Saldo inicial
            $table->decimal('saldo_atual', 15, 2)->default(0); // Saldo atual
            $table->string('descricao')->nullable(); // Descrição da movimentação

            // Relacionamento com a empresa
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Controle de auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            $table->timestamps(); // created_at e updated_at

        });
    }

    public function down()
    {
        Schema::dropIfExists('entidades_financeiras');
    }
}

