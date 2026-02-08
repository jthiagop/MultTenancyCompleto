<?php

namespace App\Services;

use App\Models\Module;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Schema;

class PermissionService
{
    /**
     * Agrupa todas as permissões por módulo
     *
     * @return array
     */
    public function getPermissionsByModule(): array
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $grouped = [];

        foreach ($permissions as $permission) {
            // Extrair o módulo do nome da permissão (ex: 'financeiro.index' -> 'financeiro')
            $parts = explode('.', $permission->name);
            $module = $parts[0] ?? 'outros';

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = $permission;
        }

        return $grouped;
    }

    /**
     * Retorna permissões de um módulo específico
     *
     * @param string $module
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getModulePermissions(string $module)
    {
        return Permission::where('guard_name', 'web')
            ->where('name', 'like', $module . '.%')
            ->orderBy('name')
            ->get();
    }

    /**
     * Retorna nomes amigáveis dos módulos.
     * Busca dinamicamente da tabela modules e mescla com fallbacks
     * para módulos que não estão na tabela (ex: users, company).
     *
     * @return array
     */
    public function getModuleNames(): array
    {
        // Fallbacks para módulos que não existem na tabela modules
        // (são gerenciados apenas por permissões, sem entry no dashboard)
        $fallbackNames = [
            'company' => 'Organismos',
            'users' => 'Cadastro de Usuários',
            'notafiscal' => 'Nota Fiscal',
        ];

        // Buscar nomes do banco de dados
        $dbNames = [];
        if (Schema::hasTable('modules')) {
            $dbNames = Module::pluck('name', 'key')->toArray();
        }

        // Mesclar: banco tem prioridade, fallbacks completam
        return array_merge($fallbackNames, $dbNames);
    }

    /**
     * Retorna nomes amigáveis das ações
     *
     * @return array
     */
    public function getActionNames(): array
    {
        return [
            'index' => 'Visualizar',
            'create' => 'Criar',
            'edit' => 'Editar',
            'delete' => 'Excluir',
            'show' => 'Detalhes',
            'import' => 'Importar',
            'export' => 'Exportar',
            'store' => 'Salvar',
        ];
    }

    /**
     * Retorna nome amigável de uma permissão
     *
     * @param string $permissionName
     * @return string
     */
    public function getFriendlyPermissionName(string $permissionName): string
    {
        $parts = explode('.', $permissionName);
        $module = $parts[0] ?? '';
        $action = $parts[1] ?? '';

        $moduleNames = $this->getModuleNames();
        $actionNames = $this->getActionNames();

        $moduleName = $moduleNames[$module] ?? ucfirst($module);
        $actionName = $actionNames[$action] ?? ucfirst($action);

        // Se houver mais partes (ex: plano-contas), incluir
        if (count($parts) > 2) {
            $subModule = $parts[1];
            $subAction = $parts[2];
            $subModuleName = str_replace('-', ' ', $subModule);
            $subModuleName = ucwords($subModuleName);
            $subActionName = $actionNames[$subAction] ?? ucfirst($subAction);
            return "{$moduleName} - {$subModuleName}: {$subActionName}";
        }

        return "{$moduleName}: {$actionName}";
    }
}

