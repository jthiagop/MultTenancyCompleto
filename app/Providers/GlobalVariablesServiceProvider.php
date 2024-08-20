<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
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

        public function boot()
        {
            View::composer('*', function ($view) {
                // Obter o usuário atualmente autenticado, ou qualquer lógica para obter o usuário desejado
                $user = auth()->user();

                // Definir variáveis para a visualização
                $view->with('currentUser', $user);
                $view->with('defaultAvatar', 'assets/media/avatars/300-6.jpg');
            });

                    // Compartilhar a variável company com todas as views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                // Busca a company associada ao usuário logado
                $company = Company::first();

                // Compartilha a variável com todas as views
                $view->with('company', $company);
            }
        });
        }
    }

