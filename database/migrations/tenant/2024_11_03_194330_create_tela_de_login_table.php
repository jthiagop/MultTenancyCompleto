<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelaDeLoginTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tela_de_login', function (Blueprint $table) {
            $table->id();
            $table->string('imagem_caminho'); // Caminho da imagem no sistema
            $table->string('descricao')->nullable(); // Descrição da imagem
            $table->string('localidade')->nullable(); // Localidade associada ao cliente
            $table->date('data_upload')->default(now()); // Data de upload da imagem
            $table->foreignId('upload_usuario_id')
            ->nullable() // Permitir valores nulos na coluna
            ->constrained('users')
            ->onDelete('set null'); // ID do usuário que fez o upload
            $table->enum('status', ['ativo', 'inativo'])->default('ativo'); // Status da imagem
            $table->json('tags')->nullable(); // Tags associadas, caso seja necessário
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('users');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tela_de_login');
    }
};
