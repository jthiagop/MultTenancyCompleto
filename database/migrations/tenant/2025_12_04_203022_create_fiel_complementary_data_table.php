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
        if (!Schema::hasTable('fiel_complementary_data')) {
        Schema::create('fiel_complementary_data', function (Blueprint $table) {
            $table->id();
                $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade');
                $table->date('data_cadastro')->nullable();
                $table->string('profissao')->nullable();
                $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable();
                $table->string('nacionalidade')->nullable();
                $table->string('natural')->nullable();
                $table->string('uf_natural', 2)->nullable();
                $table->string('passaporte')->nullable();
                $table->string('titulo_eleitor')->nullable();
                $table->string('zona')->nullable();
                $table->string('secao')->nullable();
                $table->text('observacoes')->nullable();
            $table->timestamps();
                
                $table->unique('fiel_id');
            });
        } else {
            // Se a tabela já existe, adicionar colunas que faltam
            Schema::table('fiel_complementary_data', function (Blueprint $table) {
                if (!Schema::hasColumn('fiel_complementary_data', 'fiel_id')) {
                    $table->foreignId('fiel_id')->constrained('fieis')->onDelete('cascade')->after('id');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'data_cadastro')) {
                    $table->date('data_cadastro')->nullable()->after('fiel_id');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'profissao')) {
                    $table->string('profissao')->nullable()->after('data_cadastro');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'estado_civil')) {
                    $table->enum('estado_civil', ['Amasiado(a)', 'Solteiro(a)', 'Casado(a)', 'Viúvo(a)', 'Divorciado(a)'])->nullable()->after('profissao');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'nacionalidade')) {
                    $table->string('nacionalidade')->nullable()->after('estado_civil');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'natural')) {
                    $table->string('natural')->nullable()->after('nacionalidade');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'uf_natural')) {
                    $table->string('uf_natural', 2)->nullable()->after('natural');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'passaporte')) {
                    $table->string('passaporte')->nullable()->after('uf_natural');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'titulo_eleitor')) {
                    $table->string('titulo_eleitor')->nullable()->after('passaporte');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'zona')) {
                    $table->string('zona')->nullable()->after('titulo_eleitor');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'secao')) {
                    $table->string('secao')->nullable()->after('zona');
                }
                if (!Schema::hasColumn('fiel_complementary_data', 'observacoes')) {
                    $table->text('observacoes')->nullable()->after('secao');
                }
            });
            
            // Adicionar índice único se não existir
            try {
                $connection = Schema::getConnection();
                $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
                $indexesFound = $doctrineSchemaManager->listTableIndexes('fiel_complementary_data');
                
                $hasUniqueIndex = false;
                foreach ($indexesFound as $index) {
                    if ($index->isUnique() && in_array('fiel_id', $index->getColumns())) {
                        $hasUniqueIndex = true;
                        break;
                    }
                }
                
                if (!$hasUniqueIndex) {
                    Schema::table('fiel_complementary_data', function (Blueprint $table) {
                        $table->unique('fiel_id');
                    });
                }
            } catch (\Exception $e) {
                // Ignorar erro se não conseguir verificar índices
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiel_complementary_data');
    }
};
