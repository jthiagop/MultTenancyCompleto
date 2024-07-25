<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id'); // Adiciona a coluna company_id
            $table->date('data_competencia');
            $table->string('descricao');
            $table->decimal('valor', 15, 2);
            $table->enum('tipo', ['entrada', 'saida']);
            $table->string('lancamento_padrao');
            $table->string('centro')->nullable();
            $table->string('tipo_documento')->nullable();
            $table->string('numero_documento')->nullable();
            $table->text('historico_complementar')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('caixa');
    }
};
