<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primeiro, adicionar a coluna company_id se não existir
        if (!Schema::hasColumn('modules', 'company_id')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id')->comment('ID da empresa (multi-tenancy)');
            });
        }

        // Verificar se o índice único do key existe e remover
        $indexes = DB::select("SHOW INDEXES FROM modules WHERE Key_name = 'modules_key_unique'");
        if (!empty($indexes)) {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropUnique(['key']);
            });
        }

        // Adicionar foreign key se a tabela companies existir e a foreign key não existir
        if (Schema::hasTable('companies') && Schema::hasColumn('modules', 'company_id')) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'modules' 
                AND COLUMN_NAME = 'company_id' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
            
            if (empty($foreignKeys)) {
                Schema::table('modules', function (Blueprint $table) {
                    $table->foreign('company_id')
                        ->references('id')
                        ->on('companies')
                        ->onDelete('cascade');
                });
            }
        }

        // Adicionar índice único composto se não existir
        $compositeIndexes = DB::select("SHOW INDEXES FROM modules WHERE Key_name = 'modules_company_key_unique'");
        if (empty($compositeIndexes) && Schema::hasColumn('modules', 'company_id')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->unique(['company_id', 'key'], 'modules_company_key_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            try {
                $table->dropUnique('modules_company_key_unique');
            } catch (\Exception $e) {
                // Índice não existe
            }
            
            try {
                $table->dropForeign(['company_id']);
            } catch (\Exception $e) {
                // Foreign key não existe
            }
        });
    }
};
