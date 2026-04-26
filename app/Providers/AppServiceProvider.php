<?php

namespace App\Providers;

use App\Listeners\UpdateLastLogin;
use App\Models\AppNotification;
use App\Models\FormasPagamento;
use App\Models\LancamentoPadrao;
use App\Observers\AiPromptCacheObserver;
use App\Policies\NotificationPolicy;
use App\View\Components\UserMenuComposer;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
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

        // Middleware `guest` (RedirectIfAuthenticated): quando um usuário autenticado
        // acessa /login, /register etc., é redirecionado para o dashboard React.
        // Padrão do Laravel enviaria para /home (inexistente).
        RedirectIfAuthenticated::redirectUsing(fn () => '/app/dashboard');

        // Invalidar o cache do system prompt da Domus IA sempre que
        // formas de pagamento ou lançamentos padrão mudarem — caso
        // contrário a IA usaria listas obsoletas por até 5 minutos.
        FormasPagamento::observe(AiPromptCacheObserver::class);
        LancamentoPadrao::observe(AiPromptCacheObserver::class);

        // Registro explícito do listener Login → UpdateLastLogin.
        // Em Laravel 11 a descoberta automática de listeners costuma cobrir esse caso,
        // mas registrar manualmente garante o disparo mesmo se o cache de eventos
        // (event:cache) for invalidado.
        Event::listen(Login::class, UpdateLastLogin::class);

        // Policy de notificações — autorização centralizada para que novos
        // endpoints não precisem repetir a checagem manual de ownership.
        Gate::policy(AppNotification::class, NotificationPolicy::class);
    }
}
