<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'key' => 'financeiro',
                'name' => 'Financeiro',
                'route_name' => 'financeiro.index',
                'icon_path' => '/assets/media/png/financeiro.svg',
                'icon_class' => 'fa-money-bill',
                'permission' => 'financeiro.index',
                'description' => 'Cadastros financeiros, movimentações',
                'order_index' => 1,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
            [
                'key' => 'patrimonio',
                'name' => 'Patrimônio',
                'route_name' => 'patrimonio.index',
                'icon_path' => '/assets/media/png/house3d.png',
                'icon_class' => 'fa-building',
                'permission' => 'patrimonio.index',
                'description' => 'Gestão patrimonial, foro e laudêmio',
                'order_index' => 2,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
            [
                'key' => 'contabilidade',
                'name' => 'Contabilidade',
                'route_name' => 'contabilidade.index',
                'icon_path' => '/assets/media/png/contabilidade.png',
                'icon_class' => 'fa-calculator',
                'permission' => 'contabilidade.index',
                'description' => 'Gerenciar plano de contas e DE/PARA',
                'order_index' => 3,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
            [
                'key' => 'dizimos',
                'name' => 'Dízimo e Doações',
                'route_name' => 'dizimos.index',
                'icon_path' => '/assets/media/png/dizimo.png',
                'icon_class' => 'fa-hand-holding-dollar',
                'permission' => 'dizimos.index',
                'description' => 'Gerenciamento de dízimo e doações',
                'order_index' => 4,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
            [
                'key' => 'fieis',
                'name' => 'Cadastro de Fiéis',
                'route_name' => 'fieis.index',
                'icon_path' => '/assets/media/png/fieis.png',
                'icon_class' => 'fa-users',
                'permission' => 'fieis.index',
                'description' => 'Gerenciamento de membros e contribuições',
                'order_index' => 5,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
            [
                'key' => 'cemiterio',
                'name' => 'Cadastro de Sepulturas',
                'route_name' => 'cemiterio.index',
                'icon_path' => '/assets/media/png/lapide2.png',
                'icon_class' => 'fa-cross',
                'permission' => 'cemiterio.index',
                'description' => 'Gerenciamento de sepultamentos, manutenção e pagamentos',
                'order_index' => 6,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
            [
                'key' => 'secretary',
                'name' => 'Secretaria',
                'route_name' => 'secretary.index',
                'icon_path' => '/assets/media/png/secretaria.png',
                'icon_class' => 'fa-file-lines',
                'permission' => 'secretary.index',
                'description' => 'Gerenciamento de membros religiosos e secretaria',
                'order_index' => 7,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
        ];

        // Obter todas as companies do tenant
        $companies = [];

        if (Schema::hasTable('companies')) {
            $companies = Company::all();
        }

        if (empty($companies) || $companies->isEmpty()) {
            $this->command->warn("⚠️  Nenhuma company encontrada. Os módulos serão criados sem company_id.");
            $companies = collect([null]); // Criar sem company_id como fallback
        }

        foreach ($companies as $company) {
            $companyId = $company ? $company->id : null;
            $companyName = $company ? $company->name : 'Sem company';

            $this->command->info("\n📦 Criando módulos para: {$companyName}");

            foreach ($modules as $moduleData) {
                $moduleData['company_id'] = $companyId;

                // Buscar incluindo registros soft deleted
                $existing = Module::withTrashed()
                    ->where('company_id', $companyId)
                    ->where('key', $moduleData['key'])
                    ->first();

                if ($existing) {
                    if ($existing->trashed()) {
                        // Se estava soft deleted, restaurar e atualizar
                        $existing->restore();
                        $existing->update($moduleData);
                        $this->command->info("  ✓ Módulo '{$moduleData['name']}' restaurado.");
                    } else {
                        // Se já existe e está ativo, não fazer nada (seeder idempotente)
                        $this->command->info("  → Módulo '{$moduleData['name']}' já existe (ignorado).");
                    }
                } else {
                    Module::create($moduleData);
                    $this->command->info("  ✓ Módulo '{$moduleData['name']}' criado.");
                }
            }
        }

        $this->command->info("\n✓ Total de módulos: " . Module::count());
    }
}
