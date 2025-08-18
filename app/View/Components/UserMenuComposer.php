<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserMenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        // 1. Inicia as variáveis com valores padrão
        $currentUser = null;
        $activeCompany = null;
        $allCompanies = collect(); // Uma coleção vazia

        // 2. Verifica se o usuário está autenticado
        if (Auth::check()) {
            $currentUser = Auth::user();
            $allCompanies = $currentUser->companies; // Pega todas as empresas de uma vez

            if ($allCompanies->isNotEmpty()) {
                // 3. Pega o ID da empresa ativa da sessão
                $activeCompanyId = session('active_company_id');

                // 4. Tenta encontrar a empresa ativa na coleção que já buscamos
                $activeCompany = $allCompanies->find($activeCompanyId);

                // 5. Lógica de Fallback: Se não encontrou (sessão vazia ou inválida),
                //    define a primeira empresa como ativa e atualiza a sessão.
                if (!$activeCompany) {
                    $activeCompany = $allCompanies->first();
                    session(['active_company_id' => $activeCompany->id]);
                }
            }
        }

        // 6. A "mágica": envia as variáveis para a view
        $view->with([
            'currentUser'   => $currentUser,
            'activeCompany' => $activeCompany,
            'allCompanies'  => $allCompanies,
        ]);
    }
}