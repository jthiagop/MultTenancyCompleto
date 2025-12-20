<?php

namespace App\Providers;

use App\View\Components\UserMenuComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar o namespace 'app' para componentes personalizados
        Blade::componentNamespace('App\\Components', 'app');

        // 3. Adicione esta linha para registrar o Composer
        View::composer('app.layouts.userMenu', UserMenuComposer::class);
        
        // Composer para a tela de login (Imagem de fundo aleatória)
        View::composer('app.auth.login', \App\View\Composers\LoginComposer::class);
    }
}
