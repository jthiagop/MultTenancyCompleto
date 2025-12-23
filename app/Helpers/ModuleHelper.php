<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class ModuleHelper
{
    /**
     * Retorna o módulo atual baseado na rota
     *
     * @return string|null
     */
    public static function getCurrentModule(): ?string
    {
        $routeName = Route::currentRouteName();
        
        if (!$routeName) {
            return null;
        }
        
        $moduleMap = [
            'caixa.' => 'financeiro',
            'banco.' => 'financeiro',
            'contas-financeiras.' => 'financeiro',
            'conciliacao.' => 'financeiro',
            'transacoes-financeiras.' => 'financeiro',
            'nfe_entrada.' => 'financeiro',
            'patrimonio.' => 'patrimonio',
            'bem.' => 'patrimonio',
            'namePatrimonio.' => 'patrimonio',
            'patrimonioAnexo.' => 'patrimonio',
            'contabilidade.' => 'contabilidade',
            'fieis.' => 'fieis',
            'dizimos.' => 'dizimos',
            'cemiterio.' => 'cemiterio',
            'sepultura.' => 'cemiterio',
        ];
        
        foreach ($moduleMap as $prefix => $module) {
            if (str_starts_with($routeName, $prefix)) {
                return $module;
            }
        }
        
        return null;
    }
    
    /**
     * Verifica se existe subnav para o módulo
     *
     * @param string $module
     * @return bool
     */
    public static function hasSubnav(string $module): bool
    {
        $viewPath = "app.layouts.subnav.modules.{$module}";
        return view()->exists($viewPath);
    }
}
