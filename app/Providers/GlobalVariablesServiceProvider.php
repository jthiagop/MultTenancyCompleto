<?php

namespace App\Providers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        // Composer para compartilhar variáveis globais com todas as views
        View::composer('*', function ($view) {
            $currentUser = Auth::user(); // Usuário autenticado, se existir
            $defaultAvatar = 'assets/media/avatars/300-6.jpg'; // Avatar padrão

            // Inicializa a variável $company como null
            $company = null;

            if ($currentUser && Schema::hasTable('companies')) {
                // Obtém a empresa associada ao usuário autenticado, se existir
                $company = $currentUser->companies()->first(); // Use a relação entre User e Company
            }

            // Compartilha as variáveis com as views
            $view->with([
                'currentUser' => $currentUser,
                'defaultAvatar' => $defaultAvatar,
                'company' => $company,
            ]);
        });
    }
    }

