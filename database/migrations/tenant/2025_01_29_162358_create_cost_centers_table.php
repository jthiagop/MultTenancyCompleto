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
        Schema::create('cost_centers', function (Blueprint $table) {
            $table->id(); // Chave primária
            $table->unsignedBigInteger('company_id');

            // Exemplo de campos básicos
            $table->string('code')->unique()->comment('Código identificador único do centro de custo');
            $table->string('name')->comment('Nome ou descrição do centro de custo');

            // Status: você pode usar uma string, enum, ou boolean
            $table->boolean('status')->default(true)->comment('Define se o centro de custo está ativo ou inativo');

            // Datas importantes
            $table->date('start_date')->nullable()->comment('Data de criação/início de vigência');
            $table->date('end_date')->nullable()->comment('Data de encerramento, se houver');

            // Orçamento e informações adicionais
            $table->decimal('budget', 15, 2)->default(0)->comment('Orçamento aprovado para este centro de custo');
            $table->text('observations')->nullable()->comment('Observações/Comentários adicionais');

            // Hierarquia (centro de custo pai)
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Centro de custo pai (caso seja um subcentro)');
            $table->foreign('parent_id')->references('id')->on('cost_centers')->onDelete('cascade');

            // Categoria (caso seja necessária para classificações específicas)
            $table->string('category')->nullable()->comment('Categoria ou classificação do centro de custo');

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('cost_centers');
    }
};
