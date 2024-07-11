<?php

namespace App\Providers;

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
        }
    }

