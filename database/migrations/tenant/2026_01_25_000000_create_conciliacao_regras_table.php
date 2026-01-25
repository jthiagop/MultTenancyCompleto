<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conciliacao_regras', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('company_id')
                  ->constrained('companies')
                  ->onDelete('cascade'); 
            
            $table->string('termo_busca')->index(); 
            
            $table->foreignId('lancamento_padrao_id')
                  ->constrained('lancamento_padraos')
                  ->onDelete('cascade');
            
            $table->foreignId('parceiro_id')
                  ->nullable()
                  ->constrained('parceiros')
                  ->nullOnDelete();
                  
            $table->foreignId('cost_center_id')
                  ->nullable()
                  ->constrained('cost_centers')
                  ->nullOnDelete();
            
            $table->string('tipo_documento')->nullable(); 
            $table->string('descricao_sugerida')->nullable();
            $table->integer('prioridade')->default(0); 
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('created_by_name')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('updated_by_name')->nullable();
            
            $table->timestamps();
            
            $table->index(['company_id', 'termo_busca']);
            $table->index(['company_id', 'prioridade']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conciliacao_regras');
    }
};
