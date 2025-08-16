<?php

namespace App\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $company = null; // Inicia a variável como nula

        if (Auth::check()) {
            $user = Auth::user();

            // Pega o ID da empresa ativa da sessão
            $activeCompanyId = session('active_company_id');

            // Busca a empresa correspondente
            $company = $user->companies()->find($activeCompanyId);

            // Fallback: se não houver empresa na sessão, pega a primeira
            if (!$company && $user->companies()->exists()) {
                $company = $user->companies()->first();
            }
        }

        // A linha mágica: compartilha a variável '$company' com a view
        $view->with('company', $company);
    }
}