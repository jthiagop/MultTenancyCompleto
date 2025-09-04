<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Só verificar se estamos em um tenant
        if (!tenant()) {
            return $next($request);
        }

        try {
            // Verificar se o tenant está configurado corretamente
            if (!$this->isTenantProperlySetup()) {
                Log::warning("Tenant " . tenant()->id . " não está configurado corretamente. Executando correção automática...");
                
                // Executar correção automática
                $this->fixTenantSetup();
                
                // Redirecionar para a mesma página após correção
                return redirect()->to($request->fullUrl())->with('success', 'Configuração do tenant corrigida automaticamente.');
            }
        } catch (\Exception $e) {
            Log::error("Erro ao verificar setup do tenant: " . $e->getMessage());
        }

        return $next($request);
    }

    private function isTenantProperlySetup(): bool
    {
        // Verificar se as tabelas essenciais existem
        $essentialTables = ['users', 'companies', 'roles', 'permissions'];
        foreach ($essentialTables as $table) {
            if (!Schema::hasTable($table)) {
                return false;
            }
        }

        // Verificar se as colunas essenciais existem
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'description')) {
            return false;
        }

        if (Schema::hasTable('users')) {
            $userColumns = ['company_id', 'avatar', 'status'];
            foreach ($userColumns as $column) {
                if (!Schema::hasColumn('users', $column)) {
                    return false;
                }
            }
        }

        if (Schema::hasTable('companies')) {
            $companyColumns = ['type', 'parent_id', 'status', 'tags', 'created_by', 'updated_by'];
            foreach ($companyColumns as $column) {
                if (!Schema::hasColumn('companies', $column)) {
                    return false;
                }
            }
        }

        // Verificar se existem dados essenciais
        if (DB::table('users')->count() === 0) {
            return false;
        }

        if (DB::table('companies')->count() === 0) {
            return false;
        }

        if (DB::table('roles')->count() === 0) {
            return false;
        }

        return true;
    }

    private function fixTenantSetup(): void
    {
        // Executar migrations pendentes
        \Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--force' => true,
            '--quiet' => true
        ]);

        // Adicionar colunas faltantes
        $this->addMissingColumns();

        // Executar seeds se necessário
        if (DB::table('roles')->count() === 0) {
            \Artisan::call('db:seed', [
                '--class' => 'TenantDatabaseSeeder',
                '--force' => true,
                '--quiet' => true
            ]);
        }

        // Criar dados essenciais se não existirem
        $this->createEssentialData();
    }

    private function addMissingColumns(): void
    {
        // Adicionar coluna description na tabela roles
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'description')) {
            Schema::table('roles', function ($table) {
                $table->text('description')->nullable()->after('guard_name');
            });
        }

        // Adicionar colunas na tabela users
        if (Schema::hasTable('users')) {
            $userColumns = ['company_id', 'avatar', 'status'];
            foreach ($userColumns as $column) {
                if (!Schema::hasColumn('users', $column)) {
                    Schema::table('users', function ($table) use ($column) {
                        if ($column === 'company_id') {
                            $table->unsignedBigInteger($column)->nullable()->after('id');
                        } elseif ($column === 'avatar') {
                            $table->string($column)->nullable()->after('email');
                        } elseif ($column === 'status') {
                            $table->enum($column, ['active', 'inactive'])->default('active')->after('avatar');
                        }
                    });
                }
            }
        }

        // Adicionar colunas na tabela companies
        if (Schema::hasTable('companies')) {
            $companyColumns = ['type', 'parent_id', 'status', 'tags', 'created_by', 'updated_by'];
            foreach ($companyColumns as $column) {
                if (!Schema::hasColumn('companies', $column)) {
                    Schema::table('companies', function ($table) use ($column) {
                        if ($column === 'type') {
                            $table->enum($column, ['matriz', 'filial'])->default('matriz')->after('name');
                        } elseif ($column === 'parent_id') {
                            $table->unsignedBigInteger($column)->nullable()->after('type');
                        } elseif ($column === 'status') {
                            $table->enum($column, ['active', 'inactive'])->default('active')->after('parent_id');
                        } elseif ($column === 'tags') {
                            $table->json($column)->nullable()->after('status');
                        } elseif (in_array($column, ['created_by', 'updated_by'])) {
                            $table->unsignedBigInteger($column)->nullable()->after('tags');
                        }
                    });
                }
            }
        }
    }

    private function createEssentialData(): void
    {
        // Criar usuário se não existir
        if (DB::table('users')->count() === 0) {
            $user = \App\Models\User::create([
                'name' => 'Administrador',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'avatar' => '1253525',
                'status' => 'active'
            ]);

            // Atribuir roles se existirem
            if (DB::table('roles')->count() > 0) {
                $user->assignRole(['global', 'admin', 'admin_user', 'user']);
            }
        }

        // Criar empresa se não existir
        if (DB::table('companies')->count() === 0) {
            $company = \App\Models\Company::create([
                'name' => 'Empresa Principal',
                'type' => 'matriz',
                'parent_id' => null,
                'status' => 'active',
                'tags' => json_encode(['principal', 'matriz']),
                'created_by' => null,
                'updated_by' => null,
            ]);

            // Relacionar empresa ao usuário
            if (DB::table('users')->count() > 0) {
                $user = \App\Models\User::first();
                $user->companies()->attach($company->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $user->company_id = $company->id;
                $user->save();
            }
        }
    }
}
