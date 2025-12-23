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
        Schema::create('notafiscal_contas', function (Blueprint $table) {
            $table->id();

            // Relacionamento com a empresa
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // CNPJ da conta
            $table->string('cnpj', 14);

            // Caminho do certificado (armazenado em storage)
            $table->string('certificado_path');

            // Senha criptografada
            $table->text('certificado_senha');

            // Informações extraídas do certificado
            $table->date('certificado_validade')->nullable();
            $table->string('certificado_nome')->nullable(); // Nome extraído do certificado (CN ou O)
            $table->string('certificado_cnpj', 14)->nullable(); // CNPJ extraído do certificado

            // Cursor para download de notas na SEFAZ
            $table->string('ultimo_nsu')->default('0')->comment('Cursor para download de notas na SEFAZ');

            // Útil para desativar a consulta se o certificado vencer ou cliente cancelar
            $table->boolean('ativo')->default(true);

            // Ambiente: 1=Produção, 2=Homologação (Importante saber qual ambiente esse certificado atende)
            $table->integer('ambiente')->default(1);

            // Controle de auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            $table->timestamps();

            // Índices
            $table->index('company_id');
            $table->index('cnpj');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notafiscal_contas');
    }
};

