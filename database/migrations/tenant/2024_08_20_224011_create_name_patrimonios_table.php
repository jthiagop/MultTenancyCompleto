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
        Schema::create('name_patrimonios', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cep', 9);
            $table->string('logradouro');
            $table->string('bairro');
            $table->string('localidade');
            $table->string('uf', 2);
            $table->string('ibge', 7);
            $table->string('numForo', 10);
            $table->text('complemento')->nullable();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('name_patrimonios');
    }
};
