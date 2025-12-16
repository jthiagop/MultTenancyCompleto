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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->comment('ID da empresa (multi-tenancy)');
            $table->string('key'); // Ex: 'financeiro'
            $table->string('name'); // Ex: 'Financeiro'
            $table->string('route_name'); // Ex: 'caixa.index'
            $table->string('icon_path')->nullable(); // Ex: '/assets/media/png/financeiro.svg'
            $table->string('icon_class')->nullable(); // Ex: 'fa-money-bill'
            $table->string('permission')->nullable(); // Ex: 'financeiro.index'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order_index')->default(0);
            $table->boolean('show_on_dashboard')->default(true);
            $table->json('metadata')->nullable(); // Para configurações extras
            $table->timestamps();
            $table->softDeletes();

            // Índice composto único: company_id + key
            $table->unique(['company_id', 'key'], 'modules_company_key_unique');
            $table->index(['is_active', 'show_on_dashboard', 'order_index']);
        });

        // Adicionar foreign key se a tabela companies existir
        if (Schema::hasTable('companies')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
