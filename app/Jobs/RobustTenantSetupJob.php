<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RobustTenantSetupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle(): void
    {
        $this->tenant->run(function () {
            try {
                Log::info("Iniciando setup robusto para tenant: {$this->tenant->id}");

                // 1. Verificar se todas as migrations foram executadas
                $this->ensureAllMigrationsAreRun();

                // 2. Verificar se todas as tabelas necessárias existem
                $this->ensureAllTablesExist();

                // 3. Verificar se as colunas necessárias existem
                $this->ensureAllColumnsExist();

                // 4. Executar seeds se necessário
                $this->runSeedsIfNeeded();

                // 5. Criar dados essenciais
                $this->createEssentialData();

                Log::info("Setup robusto concluído para tenant: {$this->tenant->id}");

            } catch (\Exception $e) {
                Log::error("Erro no setup robusto do tenant {$this->tenant->id}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    private function ensureAllMigrationsAreRun(): void
    {
        Log::info("Verificando migrations...");

        // Executar migrations pendentes
        Artisan::call('migrate', [
            '--path' => 'database/migrations/tenant',
            '--force' => true
        ]);

        Log::info("Migrations executadas com sucesso");
    }

    private function ensureAllTablesExist(): void
    {
        Log::info("Verificando existência das tabelas...");

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
                Log::warning("Tabela {$table} não existe. Tentando executar migrations específicas...");
                
                // Tentar executar migrations específicas
                $this->runSpecificMigrations($table);
            }
        }

        Log::info("Verificação de tabelas concluída");
    }

    private function ensureAllColumnsExist(): void
    {
        Log::info("Verificando colunas necessárias...");

        // Verificar coluna description na tabela roles
        if (Schema::hasTable('roles') && !Schema::hasColumn('roles', 'description')) {
            Log::info("Adicionando coluna description à tabela roles...");
            
            Schema::table('roles', function ($table) {
                $table->text('description')->nullable()->after('guard_name');
            });
        }

        // Verificar outras colunas importantes
        $this->checkAndAddMissingColumns();

        Log::info("Verificação de colunas concluída");
    }

    private function checkAndAddMissingColumns(): void
    {
        // Verificar colunas na tabela users
        if (Schema::hasTable('users')) {
            $userColumns = ['company_id', 'avatar', 'status'];
            foreach ($userColumns as $column) {
                if (!Schema::hasColumn('users', $column)) {
                    Log::info("Adicionando coluna {$column} à tabela users...");
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
                    Log::info("Adicionando coluna {$column} à tabela companies...");
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

    private function runSpecificMigrations(string $table): void
    {
        // Mapear tabelas para suas migrations específicas
        $migrationMap = [
            'roles' => '2024_06_29_055208_create_permission_tables.php',
            'chart_of_accounts' => '2025_08_18_012527_create_chart_of_accounts_table.php',
            'companies' => '0001_01_01_000000_create_companies_table.php',
            // Adicionar outras migrations conforme necessário
        ];

        if (isset($migrationMap[$table])) {
            $migrationFile = $migrationMap[$table];
            $migrationPath = "database/migrations/tenant/{$migrationFile}";
            
            if (file_exists($migrationPath)) {
                Log::info("Executando migration específica: {$migrationFile}");
                Artisan::call('migrate', [
                    '--path' => $migrationPath,
                    '--force' => true
                ]);
            }
        }
    }

    private function runSeedsIfNeeded(): void
    {
        Log::info("Verificando necessidade de seeds...");

        // Verificar se já existem roles
        if (DB::table('roles')->count() === 0) {
            Log::info("Executando seeds...");
            Artisan::call('db:seed', [
                '--class' => 'TenantDatabaseSeeder',
                '--force' => true
            ]);
        }

        Log::info("Seeds verificados/executados");
    }

    private function createEssentialData(): void
    {
        Log::info("Criando dados essenciais...");

        // Criar avatar padrão se não existir
        $this->createDefaultAvatar();

        // Verificar se já existe um usuário
        if (User::count() === 0) {
            Log::info("Criando usuário principal...");
            
            $user = User::create([
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
                'password' => $this->tenant->password,
                'avatar' => '1253525',
                'status' => 'active'
            ]);

            // Atribuir roles
            $user->assignRole(['global', 'admin', 'admin_user', 'user']);
        }

        // Verificar se já existe uma empresa
        if (Company::count() === 0) {
            Log::info("Criando empresa principal...");
            
            $company = Company::create([
                'name' => $this->tenant->name,
                'type' => 'matriz',
                'parent_id' => null,
                'status' => 'active',
                'tags' => json_encode(['principal', 'matriz']),
                'created_by' => null,
                'updated_by' => null,
            ]);

            // Relacionar empresa ao usuário
            if (User::count() > 0) {
                $user = User::first();
                $user->companies()->attach($company->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $user->company_id = $company->id;
                $user->save();
            }
        }

        Log::info("Dados essenciais criados");
    }

    private function createDefaultAvatar(): void
    {
        // Criar avatar padrão no storage do tenant
        $avatarPath = Storage::disk('public')->path('1253525');
        
        if (!file_exists($avatarPath)) {
            Log::info("Criando avatar padrão...");
            
            // Criar diretório se não existir
            $dir = dirname($avatarPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // Criar um avatar padrão simples (SVG)
            $svgContent = '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" fill="#e5e7eb"/>
                <circle cx="50" cy="35" r="15" fill="#9ca3af"/>
                <path d="M20 80 Q50 60 80 80" stroke="#9ca3af" stroke-width="3" fill="none"/>
            </svg>';
            
            file_put_contents($avatarPath, $svgContent);
            Log::info("Avatar padrão criado em: {$avatarPath}");
        }
    }
}
