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
        Schema::create('sepulturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('codigo_sepultura')->nullable();
            $table->string('localizacao')->nullable();
            $table->string('tipo')->nullable(); // Ex: Gaveta, Jazigo, etc.
            $table->string('tamanho')->nullable();
            $table->date('data_aquisicao')->nullable();
            $table->enum('status', ['Disponível', 'Ocupada', 'Reservada', 'Manutenção'])->default('Disponível');
            
            // Auditoria
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('updated_by_name')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Chave estrangeira
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Índices
            $table->index('company_id');
            $table->index('status');
            $table->index('codigo_sepultura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepulturas');
    }
};
