<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class FixTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix {--tenant= : ID específico do tenant} {--all : Corrigir todos os tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrigir problemas de database em tenants existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Iniciando correção de databases de tenants...');

        if ($this->option('all')) {
            $this->fixAllTenants();
        } elseif ($this->option('tenant')) {
            $this->fixSpecificTenant($this->option('tenant'));
        } else {
            $this->error('Especifique --tenant=ID ou --all');
            return 1;
        }

        $this->info('✅ Correção concluída!');
        return 0;
    }

    private function fixAllTenants(): void
    {
        $tenants = Tenant::all();
        $this->info("Encontrados {$tenants->count()} tenants para corrigir.");

        $progressBar = $this->output->createProgressBar($tenants->count());
        $progressBar->start();

        foreach ($tenants as $tenant) {
            try {
                $this->fixTenant($tenant);
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("\nErro ao corrigir tenant {$tenant->id}: " . $e->getMessage());
                Log::error("Erro ao corrigir tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function fixSpecificTenant(string $tenantId): void
    {
        $tenant = Tenant::find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant com ID {$tenantId} não encontrado.");
            return;
        }

        $this->info("Corrigindo tenant: {$tenant->name} (ID: {$tenant->id})");
        $this->fixTenant($tenant);
    }

    private function fixTenant(Tenant $tenant): void
    {
        $tenant->run(function () use ($tenant) {
            $this->info("  📊 Verificando tenant: {$tenant->name}");

            // 1. Executar migrations pendentes
            $this->runPendingMigrations();

            // 2. Verificar e criar tabelas faltantes
            $this->ensureRequiredTables();

            // 3. Verificar e adicionar colunas faltantes
            $this->ensureRequiredColumns();

            // 4. Executar seeds se necessário
            $this->runSeedsIfNeeded();

            // 5. Criar dados essenciais se não existirem
            $this->createEssentialDataIfMissing();

            $this->info("  ✅ Tenant {$tenant->name} corrigido com sucesso!");
        });
    }

    private function runPendingMigrations(): void
    {
        $this->info("    🔄 Executando migrations pendentes...");
        
        try {
            Artisan::call('migrate', [
                '--path' => 'database/migrations/tenant',
                '--force' => true,
                '--quiet' => true
            ]);
        } catch (\Exception $e) {
            $this->warn("    ⚠️  Erro ao executar migrations: " . $e->getMessage());
        }
    }

    private function ensureRequiredTables(): void
    {
        $this->info("    📋 Verificando tabelas necessárias...");

        $requiredTables = [
            'users',
            'companies',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'company_user',
            'chart_of_accounts',
            'account_mappings',
            'lancamento_padraos',
            'banks',
            'caixas',
            'transacoes_financeiras',
            'anexos',
            'patrimonios',
            'fieis',
            'escrituras',
            'cemiterios',
            'sepolturas',
            'avaliadores'
        ];

        foreach ($requiredTables as $table) {
            if (!Schema::hasTable($table)) {
                $this->warn("    ⚠️  Tabela {$table} não existe. Tentando criar...");
                $this->createTableIfPossible($table);
            }
        }
    }

    private function ensureRequiredColumns(): void
    {
        $this->info("    🔧 Verificando colunas necessárias...");

        // Verificar coluna description na tabela roles
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'description')) {
            $this->info("    ➕ Adicionando coluna description à tabela roles...");
            Schema::table('roles', function ($table) {
                $table->text('description')->nullable()->after('guard_name');
            });
        }

        // Verificar colunas na tabela users
        if (Schema::hasTable('users')) {
            $userColumns = ['company_id', 'avatar', 'status'];
            foreach ($userColumns as $column) {
                if (!Schema::hasColumn('users', $column)) {
                    $this->info("    ➕ Adicionando coluna {$column} à tabela users...");
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

        // Verificar colunas na tabela companies
        if (Schema::hasTable('companies')) {
            $companyColumns = ['type', 'parent_id', 'status', 'tags', 'created_by', 'updated_by'];
            foreach ($companyColumns as $column) {
                if (!Schema::hasColumn('companies', $column)) {
                    $this->info("    ➕ Adicionando coluna {$column} à tabela companies...");
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

    private function createTableIfPossible(string $table): void
    {
        // Mapear tabelas para suas migrations específicas
        $migrationMap = [
            'roles' => '2024_06_29_055208_create_permission_tables.php',
            'chart_of_accounts' => '2025_08_18_012527_create_chart_of_accounts_table.php',
            'companies' => '0001_01_01_000000_create_companies_table.php',
        ];

        if (isset($migrationMap[$table])) {
            $migrationFile = $migrationMap[$table];
            $migrationPath = "database/migrations/tenant/{$migrationFile}";
            
            if (file_exists($migrationPath)) {
                try {
                    Artisan::call('migrate', [
                        '--path' => $migrationPath,
                        '--force' => true,
                        '--quiet' => true
                    ]);
                    $this->info("    ✅ Tabela {$table} criada com sucesso!");
                } catch (\Exception $e) {
                    $this->error("    ❌ Erro ao criar tabela {$table}: " . $e->getMessage());
                }
            }
        }
    }

    private function runSeedsIfNeeded(): void
    {
        $this->info("    🌱 Verificando seeds...");

        // Verificar se já existem roles
        if (DB::table('roles')->count() === 0) {
            $this->info("    🌱 Executando seeds...");
            try {
                Artisan::call('db:seed', [
                    '--class' => 'TenantDatabaseSeeder',
                    '--force' => true,
                    '--quiet' => true
                ]);
                $this->info("    ✅ Seeds executados com sucesso!");
            } catch (\Exception $e) {
                $this->warn("    ⚠️  Erro ao executar seeds: " . $e->getMessage());
            }
        } else {
            $this->info("    ✅ Seeds já existem.");
        }
    }

    private function createEssentialDataIfMissing(): void
    {
        $this->info("    👤 Verificando dados essenciais...");

        // Verificar se já existe um usuário
        if (DB::table('users')->count() === 0) {
            $this->info("    👤 Criando usuário principal...");
            
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
            
            // Dar todas as permissões ao primeiro usuário
            try {
                $allPermissions = \Spatie\Permission\Models\Permission::all();
                if ($allPermissions->count() > 0) {
                    $user->syncPermissions($allPermissions->pluck('id')->toArray());
                    $this->info("    ✅ Todas as permissões atribuídas ao primeiro usuário!");
                }
            } catch (\Exception $e) {
                $this->warn("    ⚠️  Erro ao atribuir permissões: " . $e->getMessage());
            }

            $this->info("    ✅ Usuário principal criado!");
        }

        // Verificar se já existe uma empresa
        if (DB::table('companies')->count() === 0) {
            $this->info("    🏢 Criando empresa principal...");
            
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

            $this->info("    ✅ Empresa principal criada!");
        }
    }
}
