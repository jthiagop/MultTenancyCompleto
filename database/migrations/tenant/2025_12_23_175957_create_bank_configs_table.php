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
        Schema::create('bank_configs', function (Blueprint $table) {
            $table->id();
            
            // Identificação
            $table->string('banco_codigo', 10)->default('001'); // 001 = BB
            $table->string('nome_conta')->nullable(); // Ex: "Conta Movimento"
            $table->string('agencia', 10)->nullable();
            $table->string('conta_corrente', 20)->nullable();
            
            // Credenciais da API (Vão ser salvas criptografadas)
            // Usamos TEXT porque a string criptografada é longa
            $table->text('client_id'); 
            $table->text('client_secret');
            $table->text('developer_app_key'); // A "App Key" do portal Developers BB
            
            // Campo opcional para testes manuais no BB (MCI)
            $table->string('mci_teste')->nullable()->comment('Usado apenas header x-br-com-bb-ipa-mci em homologação');
            
            // Dados Bancários Específicos do BB (Obrigatórios para Boleto)
            $table->string('convenio', 20); // Número do Convênio
            $table->string('carteira', 10); // Ex: 17
            $table->string('variacao', 10); // Ex: 35
            
            // Configurações Gerais
            $table->string('ambiente')->default('homologacao'); // 'homologacao' ou 'producao'
            $table->boolean('ativo')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_configs');
    }
};
