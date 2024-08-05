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
        Schema::create('cadastro_bancos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id'); // ID da empresa
            $table->string('banco'); // Nome do banco
            $table->string('name'); // Nome do banco
            $table->string('conta'); // Número da conta
            $table->string('agencia'); // Número da agência
            $table->string('digito')->nullable(); // Dígito da agência
            $table->enum('account_type', ['corrente', 'poupanca', 'aplicacao']); // Tipo de conta
            $table->text('description')->nullable(); // Descrição da conta
            $table->unsignedBigInteger('created_by'); // ID do usuário que cadastrou
            $table->timestamps(); // created_at e updated_at

            // Chaves estrangeiras
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cadastro_bancos');
    }
};
