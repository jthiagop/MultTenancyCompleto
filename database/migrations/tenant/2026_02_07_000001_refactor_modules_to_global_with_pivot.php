<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Refatora a tabela modules de "duplicação por company" para "registro global + pivot".
     *
     * ANTES: modules tinha company_id, duplicando linhas para cada company.
     * DEPOIS: modules é global (sem company_id), company_module controla desativações.
     *
     * Lógica: Por padrão, toda company tem acesso a TODOS os módulos ativos.
     * A pivot company_module é usada apenas para DESATIVAR módulos específicos (opt-out).
     */
    public function up(): void
    {
        // 1. Criar a tabela pivot company_module
        if (!Schema::hasTable('company_module')) {
            Schema::create('company_module', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('module_id');
                $table->boolean('is_active')->default(true)->comment('Se false, módulo está desativado para esta company');
                $table->json('settings')->nullable()->comment('Configurações customizadas por company');
                $table->timestamps();

                $table->unique(['company_id', 'module_id'], 'company_module_unique');

                if (Schema::hasTable('companies')) {
                    $table->foreign('company_id')
                        ->references('id')
                        ->on('companies')
                        ->onDelete('cascade');
                }
            });
        }

        // 2. Migrar dados: deduplicar modules e popular pivot
        $this->migrateData();

        // 3. Remover company_id da tabela modules
        if (Schema::hasColumn('modules', 'company_id')) {
            // Dropar foreign key e índice único antes
            Schema::table('modules', function (Blueprint $table) {
                // SQLite não suporta dropForeign, então uso try/catch
                try {
                    $table->dropForeign(['company_id']);
                } catch (\Exception $e) {
                    // FK pode não existir (SQLite)
                }

                try {
                    $table->dropUnique('modules_company_key_unique');
                } catch (\Exception $e) {
                    // Índice pode não existir
                }
            });

            // Para SQLite, precisamos recriar a tabela sem a coluna
            if (DB::getDriverName() === 'sqlite') {
                $this->removeCompanyIdSqlite();
            } else {
                Schema::table('modules', function (Blueprint $table) {
                    $table->dropColumn('company_id');
                });
            }
        }

        // 4. Adicionar unique index no key (agora que é global)
        $this->addUniqueKeyIndex();

        // 5. Adicionar FK na pivot para modules (após deduplicação)
        if (Schema::hasTable('company_module') && !$this->hasForeignKey('company_module', 'module_id')) {
            Schema::table('company_module', function (Blueprint $table) {
                $table->foreign('module_id')
                    ->references('id')
                    ->on('modules')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Migra os dados: deduplicar módulos e popular a pivot.
     */
    private function migrateData(): void
    {
        if (!Schema::hasColumn('modules', 'company_id')) {
            return;
        }

        // Primeiro, limpar registros soft-deleted (duplicatas antigas)
        DB::table('modules')->whereNotNull('deleted_at')->delete();

        // Buscar todos os módulos atuais agrupados por key
        $modules = DB::table('modules')->get();
        $grouped = $modules->groupBy('key');

        foreach ($grouped as $key => $group) {
            // Pegar o primeiro registro como o "canônico" (preferir o que tem mais dados)
            $canonical = $group->sortByDesc(function ($m) {
                // Priorizar os que têm permission preenchido
                return $m->permission ? 1 : 0;
            })->first();

            // Para cada company_id nos duplicados, criar registro na pivot
            foreach ($group as $module) {
                if ($module->company_id) {
                    // Verificar se já existe na pivot
                    $exists = DB::table('company_module')
                        ->where('company_id', $module->company_id)
                        ->where('module_id', $canonical->id)
                        ->exists();

                    if (!$exists) {
                        DB::table('company_module')->insert([
                            'company_id' => $module->company_id,
                            'module_id' => $canonical->id,
                            'is_active' => $module->is_active ?? true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Deletar duplicados (manter apenas o canônico)
                if ($module->id !== $canonical->id) {
                    DB::table('modules')->where('id', $module->id)->delete();
                }
            }

            // Limpar company_id do canônico (será removido na coluna depois)
            DB::table('modules')->where('id', $canonical->id)->update(['company_id' => null]);
        }
    }

    /**
     * Remove company_id da tabela modules para SQLite (que não suporta DROP COLUMN nativamente).
     */
    private function removeCompanyIdSqlite(): void
    {
        // Obter todos os dados atuais
        $modules = DB::table('modules')->get();

        // Dropar a tabela
        Schema::dropIfExists('modules');

        // Recriar sem company_id
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('name');
            $table->string('route_name');
            $table->string('icon_path')->nullable();
            $table->string('icon_class')->nullable();
            $table->string('permission')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order_index')->default(0);
            $table->boolean('show_on_dashboard')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('key', 'modules_key_unique');
            $table->index(['is_active', 'show_on_dashboard', 'order_index']);
        });

        // Reinserir dados (sem company_id)
        foreach ($modules as $module) {
            DB::table('modules')->insert([
                'id' => $module->id,
                'key' => $module->key,
                'name' => $module->name,
                'route_name' => $module->route_name,
                'icon_path' => $module->icon_path,
                'icon_class' => $module->icon_class,
                'permission' => $module->permission,
                'description' => $module->description,
                'is_active' => $module->is_active,
                'order_index' => $module->order_index,
                'show_on_dashboard' => $module->show_on_dashboard,
                'metadata' => $module->metadata,
                'created_at' => $module->created_at,
                'updated_at' => $module->updated_at,
                'deleted_at' => $module->deleted_at,
            ]);
        }
    }

    /**
     * Adiciona unique index no key se não existir.
     */
    private function addUniqueKeyIndex(): void
    {
        // Para SQLite, o unique já foi criado na recriação
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        try {
            Schema::table('modules', function (Blueprint $table) {
                $table->unique('key', 'modules_key_unique');
            });
        } catch (\Exception $e) {
            // Índice já existe
        }
    }

    /**
     * Verifica se uma foreign key existe.
     */
    private function hasForeignKey(string $table, string $column): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return false; // SQLite não suporta FK check via info_schema
        }

        $fks = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = ? 
            AND COLUMN_NAME = ? 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$table, $column]);

        return !empty($fks);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Readicionar company_id na tabela modules
        if (!Schema::hasColumn('modules', 'company_id')) {
            Schema::table('modules', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            });
        }

        // Migrar dados da pivot de volta
        $pivotData = DB::table('company_module')->get();
        foreach ($pivotData as $pivot) {
            $module = DB::table('modules')->where('id', $pivot->module_id)->first();
            if ($module) {
                // Clonar módulo para cada company
                DB::table('modules')->insert([
                    'company_id' => $pivot->company_id,
                    'key' => $module->key,
                    'name' => $module->name,
                    'route_name' => $module->route_name,
                    'icon_path' => $module->icon_path,
                    'icon_class' => $module->icon_class,
                    'permission' => $module->permission,
                    'description' => $module->description,
                    'is_active' => $pivot->is_active,
                    'order_index' => $module->order_index,
                    'show_on_dashboard' => $module->show_on_dashboard,
                    'metadata' => $module->metadata,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Dropar pivot
        Schema::dropIfExists('company_module');

        // Restaurar unique index composto
        try {
            Schema::table('modules', function (Blueprint $table) {
                $table->dropUnique('modules_key_unique');
                $table->unique(['company_id', 'key'], 'modules_company_key_unique');
            });
        } catch (\Exception $e) {
            // Ignore
        }
    }
};
