<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Módulos são globais — definidos uma única vez.
     * A tabela pivot company_module controla desativações por company (opt-out).
     * Por padrão, toda company tem acesso a todos os módulos ativos.
     */
    public function run(): void
    {
        $modules = [
            [
                'key' => 'financeiro',
                'name' => 'Financeiro',
                'route_name' => 'financeiro.index',
                'icon_path' => '/tenancy/assets/media/png/financeiro.svg',
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
                'icon_path' => '/tenancy/assets/media/png/house3d.png',
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
                'icon_path' => '/tenancy/assets/media/png/contabilidade.png',
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
                'icon_path' => '/tenancy/assets/media/png/dizimo.png',
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
                'icon_path' => '/tenancy/assets/media/png/fieis.png',
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
                'icon_path' => '/tenancy/assets/media/png/lapide2.png',
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
                'icon_path' => '/tenancy/assets/media/png/secretaria.png',
                'icon_class' => 'fa-file-lines',
                'permission' => 'secretary.index',
                'description' => 'Gerenciamento de membros religiosos e secretaria',
                'order_index' => 7,
                'is_active' => true,
                'show_on_dashboard' => true,
            ],
        ];

        foreach ($modules as $moduleData) {
            $existing = Module::withTrashed()->where('key', $moduleData['key'])->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $existing->update($moduleData);
                    $this->command?->info("  ↻ Módulo '{$moduleData['name']}' restaurado.");
                } else {
                    // Atualizar campos que podem ter mudado (ex: permission, icon)
                    $existing->update(collect($moduleData)->except('key')->toArray());
                    $this->command?->info("  → Módulo '{$moduleData['name']}' atualizado.");
                }
            } else {
                Module::create($moduleData);
                $this->command?->info("  ✓ Módulo '{$moduleData['name']}' criado.");
            }
        }

        $this->command?->info("\n✓ Total de módulos: " . Module::count());
    }
}
