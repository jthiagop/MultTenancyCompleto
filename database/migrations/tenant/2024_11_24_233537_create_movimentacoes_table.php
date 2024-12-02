<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimentacoesTable extends Migration
{
    public function up()
    {
        Schema::create('movimentacoes', function (Blueprint $table) {
            $table->id();

            // Relacionamento com entidades financeiras
            $table->foreignId('entidade_id')->constrained('entidades_financeiras')->onDelete('cascade');

            // Tipo de movimentação (entrada ou saída)
            $table->enum('tipo', ['entrada', 'saida']);

            // Valor e descrição da movimentação
            $table->decimal('valor', 15, 2);
            $table->string('descricao')->nullable();

            $table->foreignId('movimentacao_id')->nullable()->constrained('users')->onDelete('set null');


            // Relacionamento com a empresa
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Controle de auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            // Timestamps
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('movimentacoes');
    }
}

