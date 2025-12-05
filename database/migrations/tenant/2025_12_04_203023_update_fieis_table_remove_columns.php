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
        Schema::table('fieis', function (Blueprint $table) {
            // Remover colunas que foram movidas para tabelas relacionadas
            $table->dropColumn([
                'estado_civil',
                'profissao',
                'telefone',
                'telefone_secundario',
                'email',
                'endereco',
                'bairro',
                'cidade',
                'estado',
                'cep',
                'data_batismo',
                'local_batismo',
                'data_casamento',
                'local_casamento',
                'data_ingresso',
                'responsavel_ingresso',
                'grupo_participante',
                'ministerio',
                'dizimista',
                'valor_dizimo',
                'frequencia_dizimo',
                'ultima_contribuicao',
                'observacoes',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fieis', function (Blueprint $table) {
            // Restaurar colunas removidas
            $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable();
            $table->string('profissao')->nullable();
            $table->string('telefone')->nullable();
            $table->string('telefone_secundario')->nullable();
            $table->string('email')->nullable();
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado', 2)->nullable();
            $table->string('cep', 9)->nullable();
            $table->date('data_batismo')->nullable();
            $table->string('local_batismo')->nullable();
            $table->date('data_casamento')->nullable();
            $table->string('local_casamento')->nullable();
            $table->date('data_ingresso')->nullable();
            $table->string('responsavel_ingresso')->nullable();
            $table->string('grupo_participante')->nullable();
            $table->string('ministerio')->nullable();
            $table->boolean('dizimista')->default(false);
            $table->decimal('valor_dizimo', 10, 2)->nullable();
            $table->enum('frequencia_dizimo', ['Mensal', 'Semanal', 'Anual'])->nullable();
            $table->date('ultima_contribuicao')->nullable();
            $table->text('observacoes')->nullable();
        });
    }
};
