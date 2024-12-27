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
        Schema::create('transacoes_financeiras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->date('data_competencia');
            $table->unsignedBigInteger('entidade_id'); // Relaciona com `entidades_financeiras`
            $table->enum('tipo', ['entrada', 'saida']);
            $table->decimal('valor', 15, 2);
            $table->string('descricao', 255)->nullable();
            $table->unsignedInteger('lancamento_padrao_id')->nullable(); // Relaciona com `lancamento_padraos`
            $table->unsignedBigInteger('movimentacao_id')->nullable(); // Relaciona com `movimentacoes`
            $table->unsignedBigInteger('centro')->nullable();
            $table->string('tipo_documento', 50)->nullable();
            $table->string('numero_documento', 100)->nullable();
            $table->string('origem', 50)->nullable();
            $table->text('historico_complementar')->nullable();
            $table->boolean('comprovacao_fiscal')->default(false);

            $table->softDeletes();
            $table->timestamps();

            // Índices e chaves estrangeiras
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('entidade_id')->references('id')->on('entidades_financeiras')->onDelete('cascade');
            $table->foreign('lancamento_padrao_id')->references('id')->on('lancamento_padraos')->onDelete('set null');
            $table->foreign('movimentacao_id')->references('id')->on('movimentacoes')->onDelete('cascade');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacoes_financeiras');
    }
};
