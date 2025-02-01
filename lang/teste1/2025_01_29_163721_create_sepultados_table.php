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
        Schema::create('sepultados', function (Blueprint $table) {
            $table->id();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreignId('sepultura_id')->constrained('sepulturas')->onDelete('cascade'); // Relacionamento com sepulturas
            $table->string('avatar')->nullable();
            $table->string('nome'); // Nome completo do sepultado
            $table->date('data_nascimento'); // Data de nascimento
            $table->date('data_falecimento'); // Data de falecimento
            $table->string('documento_identificacao')->nullable(); // Documento de identificação (RG, CPF, etc.)
            $table->text('informacoes_atestado_obito')->nullable(); // Informações sobre o atestado de óbito
            $table->string('familia_responsavel')->nullable(); // Família responsável
            $table->string('relacionamento')->nullable(); // Relacionamento com a pessoa sepultada
            $table->date('data_sepultamento')->nullable(); // Data do sepultamento

            // Novos campos
            $table->string('livro_sepultamento')->nullable(); // Livro de sepultamento
            $table->string('folha_sepultamento')->nullable(); // Folha de sepultamento
            $table->string('numero_sepultamento')->nullable(); // Número de sepultamento
            $table->string('causa_mortis')->nullable(); // Causa do falecimento

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepultados');
    }
};
