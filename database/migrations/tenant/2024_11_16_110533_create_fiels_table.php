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
        if (!Schema::hasTable('fieis')) {
            Schema::create('fieis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');

                // Informações Pessoais
                $table->string('nome_completo');
                $table->date('data_nascimento')->nullable();
                $table->enum('sexo', ['M', 'F', 'Outro'])->nullable();
                $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable();
                $table->string('profissao')->nullable();
                $table->string('cpf', 14)->unique()->nullable();
                $table->string('rg', 20)->nullable();

                // Avatar do Fiel
                $table->string('avatar')->nullable();

                // Informações de Contato
                $table->string('telefone')->nullable();
                $table->string('telefone_secundario')->nullable();
                $table->string('email')->nullable();
                $table->string('endereco')->nullable();
                $table->string('bairro')->nullable();
                $table->string('cidade')->nullable();
                $table->string('estado', 2)->nullable();
                $table->string('cep', 9)->nullable();

                // Preferências de Notificação (novo campo)
                $table->json('notifications')->nullable();

                // Informações Eclesiásticas
                $table->date('data_batismo')->nullable();
                $table->string('local_batismo')->nullable();
                $table->date('data_casamento')->nullable();
                $table->string('local_casamento')->nullable();
                $table->date('data_ingresso')->nullable();
                $table->string('responsavel_ingresso')->nullable();
                $table->string('grupo_participante')->nullable();
                $table->string('ministerio')->nullable();

                // Contribuições e Dízimos
                $table->boolean('dizimista')->default(false);
                $table->decimal('valor_dizimo', 10, 2)->nullable();
                $table->enum('frequencia_dizimo', ['Mensal', 'Semanal', 'Anual'])->nullable();
                $table->date('ultima_contribuicao')->nullable();

                // Histórico e Observações
                $table->text('observacoes')->nullable();
                $table->enum('status', ['Ativo', 'Inativo', 'Afastado'])->default('Ativo');

                // Chaves estrangeiras
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('created_by_name')->nullable();

                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('updated_by_name')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiels');
    }
};
