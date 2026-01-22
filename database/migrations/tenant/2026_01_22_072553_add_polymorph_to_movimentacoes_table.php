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
        Schema::table('movimentacoes', function (Blueprint $table) {
            // 1. Adiciona os campos polimórficos (cria 'origem_type' e 'origem_id')
            // Estou chamando de 'origem' para ficar claro: "Qual a origem desse dinheiro?"
            // Indexa automaticamente para busca rápida.
            $table->nullableMorphs('origem'); 
            
            // 2. Melhoria de Performance com índices compostos
            // Essencial para relatórios financeiros rápidos
            // Nota: nullableMorphs() já cria índice para origem_type + origem_id
            $table->index(['entidade_id', 'data']);
            $table->index(['company_id', 'tipo']);
        });
        
        // 3. Remove campo antigo se existir (em transação separada)
        // Isso evita problemas com ordem de execução
        if (Schema::hasColumn('movimentacoes', 'movimentacao_id')) {
            Schema::table('movimentacoes', function (Blueprint $table) {
                $table->dropColumn('movimentacao_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movimentacoes', function (Blueprint $table) {
            // Remove os campos polimórficos (dropMorphs remove o índice automaticamente)
            $table->dropMorphs('origem');
            // Remove os índices compostos adicionados
            $table->dropIndex(['entidade_id', 'data']);
            $table->dropIndex(['company_id', 'tipo']);
        });
    }
};
