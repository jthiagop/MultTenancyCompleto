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
        Schema::create('user_favorite_routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('route_name'); // Ex: 'caixa.index'
            $table->string('display_name'); // Ex: 'Financeiro'
            $table->string('icon')->nullable(); // Ex: 'fa-money-bill' ou path da imagem
            $table->string('module_key'); // Ex: 'financeiro', 'patrimonio'
            $table->integer('order_index')->default(0);
            $table->json('metadata')->nullable(); // Para futuras extensões
            $table->timestamps();

            $table->unique(['user_id', 'company_id', 'route_name'], 'user_company_route_unique');
            $table->index(['user_id', 'company_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favorite_routes');
    }
};
