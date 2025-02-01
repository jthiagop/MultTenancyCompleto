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
            $table->string('codigo_sepultura')->unique();  // Código único para cada sepultura
            $table->string('localizacao')->nullable();  // Localização da sepultura (bloco, quadra, etc.)
            $table->enum('tipo', ['terreno', 'mausoléu', 'jazigo', 'urna columbário', 'cripta', 'cova', 'sepultura vertical', 'cemiterio familiar', 'ossário']);
            $table->decimal('tamanho', 8, 2)->nullable();  // Tamanho da sepultura (em metros quadrados)
            $table->date('data_aquisicao')->nullable();
            $table->enum('tipo', ['cemiterio familiar', 'cova', 'cripta', 'jazigo', 'mausoléu', 'ossário', 'sepultura vertical', 'terreno', 'urna columbário']);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('sepulturas');
    }
};
