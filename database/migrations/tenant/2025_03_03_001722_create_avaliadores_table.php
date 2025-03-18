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
        Schema::create('avaliadores', function (Blueprint $table) {
            // Chave primária do avaliador
            $table->id();

            // Exemplo de relação com a company, se necessário
            $table->unsignedBigInteger('company_id')->nullable();

            // Caso o avaliador seja um usuário do sistema (com login)
            // se não precisar desse relacionamento, basta remover.
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Nome completo do avaliador
            $table->string('nome');

            // Tipo de profissional, podendo ser engenheiro, arquiteto,
            // corretor, etc.
            $table->enum('tipo_profissional', [
                'engenheiro_civil',
                'engenheiro_agronomo',
                'arquiteto',
                'corretor_imoveis',
                'outro'
            ])->default('outro');

            // Registro profissional no órgão competente (CREA, CAU, CRECI...)
            $table->string('registro_profissional')->nullable();

            // Informações de contato
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();

            // Campo para saber qual usuário do sistema criou este cadastro
            // e quem fez a última atualização (auditoria)
            // Exemplo de relação com a company, se necessário
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            // Timestamps padrão do Laravel (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('avaliadores');
    }
};
