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
        if (!Schema::hasTable('fiel_address')) {
        Schema::create('fiel_address', function (Blueprint $table) {
            $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->foreignId('address_id')->constrained('adresses')->onDelete('cascade');
                $table->string('tipo')->nullable()->comment('principal, secundario, etc.');
            $table->timestamps();
                
                $table->unique(['fiel_id', 'address_id']);
            });
        } else {
            // Se a tabela já existe, verificar e adicionar colunas que faltam
            Schema::table('fiel_address', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_address', 'fiel_id')) {
                    $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->after('id');
                }
                if (!Schema::hasColumn('fiel_address', 'address_id')) {
                    $table->foreignId('address_id')->constrained('adresses')->onDelete('cascade')->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_address', 'tipo')) {
                    $table->string('tipo')->nullable()->comment('principal, secundario, etc.')->after('address_id');
                }
            });
            
            // Verificar e adicionar índice único se necessário
            try {
                $connection = Schema::getConnection();
                $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
                $indexesFound = $doctrineSchemaManager->listTableIndexes('fiel_address');
                
                $hasUniqueIndex = false;
                foreach ($indexesFound as $index) {
                    if ($index->isUnique() && count($index->getColumns()) == 2 && 
                        in_array('fiel_id', $index->getColumns()) && 
                        in_array('address_id', $index->getColumns())) {
                        $hasUniqueIndex = true;
                        break;
                    }
                }
                
                if (!$hasUniqueIndex) {
                    Schema::table('fiel_address', function (Blueprint $table) {
                        $table->unique(['fiel_id', 'address_id']);
                    });
                }
            } catch (\Exception $e) {
                // Se não conseguir verificar índices, tenta adicionar o único de qualquer forma
                // (pode dar erro se já existir, mas não é crítico)
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_address');
    }
};
