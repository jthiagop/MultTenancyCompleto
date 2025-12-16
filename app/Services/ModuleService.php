<?php

namespace App\Services;

use App\Models\Module;

class ModuleService
{
    /**
     * Lista de módulos disponíveis no sistema (busca do banco de dados)
     *
     * @param int|null $companyId
     * @return array
     */
    public function getAvailableModules(?int $companyId = null): array
    {
        $query = Module::active()->ordered();
        
        if ($companyId) {
            $query->forCompany($companyId);
        }
        
        return $query->get()
            ->map(function ($module) {
                return [
                    'key' => $module->key,
                    'name' => $module->name,
                    'route' => $module->route_name,
                    'icon' => $module->icon_path,
                    'icon_class' => $module->icon_class,
                    'permission' => $module->permission,
                    'description' => $module->description,
                ];
            })
            ->toArray();
    }

    /**
     * Retorna módulos para exibição no dashboard
     *
     * @param \App\Models\User $user
     * @param int|null $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDashboardModules($user, ?int $companyId = null)
    {
        $query = Module::active()
            ->forDashboard()
            ->ordered();
        
        if ($companyId) {
            $query->forCompany($companyId);
        }
        
        return $query->get()
            ->filter(function ($module) use ($user) {
                return $module->userHasPermission($user);
            });
    }

    /**
     * Retorna módulos que o usuário tem permissão (array formatado)
     *
     * @param \App\Models\User $user
     * @param int|null $companyId
     * @return array
     */
    public function getAuthorizedModules($user, ?int $companyId = null): array
    {
        $query = Module::active()->ordered();
        
        if ($companyId) {
            $query->forCompany($companyId);
        }
        
        return $query->get()
            ->filter(function ($module) use ($user) {
                return $module->userHasPermission($user);
            })
            ->map(function ($module) {
                return [
                    'key' => $module->key,
                    'name' => $module->name,
                    'route' => $module->route_name,
                    'icon' => $module->icon_path,
                    'icon_class' => $module->icon_class,
                    'permission' => $module->permission,
                    'description' => $module->description,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Verifica se módulo pode ser favoritado
     *
     * @param \App\Models\User $user
     * @param string $moduleKey
     * @param int|null $companyId
     * @return bool
     */
    public function canFavorite($user, string $moduleKey, ?int $companyId = null): bool
    {
        $query = Module::where('key', $moduleKey);
        
        if ($companyId) {
            $query->forCompany($companyId);
        }
        
        $module = $query->first();
        
        if (!$module || !$module->is_active) {
            return false;
        }
        
        return $module->userHasPermission($user);
    }

    /**
     * Busca dados de um módulo específico do banco
     *
     * @param string $moduleKey
     * @param int|null $companyId
     * @return \App\Models\Module|null
     */
    public function getModuleByKey(string $moduleKey, ?int $companyId = null): ?Module
    {
        $query = Module::where('key', $moduleKey);
        
        if ($companyId) {
            $query->forCompany($companyId);
        }
        
        return $query->first();
    }
}

