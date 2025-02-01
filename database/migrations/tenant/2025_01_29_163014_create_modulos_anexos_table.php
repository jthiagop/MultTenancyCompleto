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
        Schema::create('modulos_anexos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('anexavel_id'); // ID do módulo relacionado
            $table->string('anexavel_type'); // Nome do módulo (Caixa, Banco, Patrimonio, etc.)
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo');
            $table->string('tipo_arquivo');
            $table->string('extensao_arquivo', 10)->nullable();
            $table->string('mime_type')->nullable();
            $table->bigInteger('tamanho_arquivo')->nullable();
            $table->text('descricao')->nullable();
            $table->string('status')->default('ativo');
            $table->timestamp('data_upload')->nullable()->useCurrent();
            $table->timestamp('excluido_em')->nullable();
            $table->text('comentarios')->nullable();
            $table->json('tags')->nullable();

            // Foreign keys
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();

            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modulos_anexos');
    }
};
