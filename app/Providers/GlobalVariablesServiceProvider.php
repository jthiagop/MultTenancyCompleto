<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class GlobalVariablesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartilha variáveis com todas as views usando a lógica correta da sessão.
        View::composer('*', function ($view) {
            
            // 1. Inicia as variáveis com valores padrão
            $currentUser = Auth::user();
            $activeCompany = null;
            $allCompanies = collect(); // Uma coleção vazia
            $defaultAvatar = 'assets/media/avatars/300-6.jpg';

            // 2. Apenas executa a lógica se o usuário estiver logado
            if ($currentUser && Schema::hasTable('companies')) {
                
                $allCompanies = $currentUser->companies; // Pega todas as empresas de uma vez

                if ($allCompanies->isNotEmpty()) {
                    // 3. Pega o ID da empresa ativa da sessão
                    $activeCompanyId = session('active_company_id');

                    // 4. Tenta encontrar a empresa ativa na coleção que já buscamos
                    $activeCompany = $allCompanies->find($activeCompanyId);

                    // 5. Lógica de Fallback: Se não encontrou (sessão vazia ou inválida),
                    //    define a primeira empresa como ativa e ATUALIZA a sessão para o futuro.
                    if (!$activeCompany) {
                        $activeCompany = $allCompanies->first();
                        session(['active_company_id' => $activeCompany->id]);
                    }
                }
            }

            // 6. Compartilha as variáveis CORRETAS com todas as views
            $view->with([
                'currentUser'   => $currentUser,
                'defaultAvatar' => $defaultAvatar,
                'company'       => $activeCompany, // Pode continuar usando 'company' ou mudar para 'activeCompany'
                'activeCompany' => $activeCompany, // Compartilhando com o nome novo e mais claro
                'allCompanies'  => $allCompanies,
            ]);
        });
    }
}