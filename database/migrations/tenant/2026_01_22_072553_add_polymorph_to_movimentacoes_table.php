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
            // 1. Adiciona os campos polimórficos APENAS se não existirem
            // Verificar se as colunas já existem evita erro de duplicação
            if (!Schema::hasColumn('movimentacoes', 'origem_type')) {
                $table->nullableMorphs('origem');
            }
            
            // 2. Melhoria de Performance com índices compostos
            // Essencial para relatórios financeiros rápidos
            // Nota: nullableMorphs() já cria índice para origem_type + origem_id
            if (!Schema::hasIndex('movimentacoes', 'movimentacoes_entidade_id_data_index')) {
                $table->index(['entidade_id', 'data']);
            }
            
            if (!Schema::hasIndex('movimentacoes', 'movimentacoes_company_id_tipo_index')) {
                $table->index(['company_id', 'tipo']);
            }
        });
        
        // 3. Remove campo antigo se existir
        // Primeiro remove a foreign key constraint, depois a coluna
        if (Schema::hasColumn('movimentacoes', 'movimentacao_id')) {
            Schema::table('movimentacoes', function (Blueprint $table) {
                // ✅ Dropar a foreign key constraint ANTES da coluna
                try {
                    $table->dropForeign(['movimentacao_id']);
                } catch (\Exception $e) {
                    // Se a constraint não existir, continua normalmente
                }
                
                // Agora pode dropar a coluna com segurança
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
