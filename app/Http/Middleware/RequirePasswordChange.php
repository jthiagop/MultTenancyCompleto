<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está autenticado
        if (Auth::check()) {
            $user = Auth::user();
            
            // Verifica se o usuário precisa trocar a senha
            if ($user->must_change_password) {
                // Permite acesso à rota first-access (GET e POST)
                if ($request->routeIs('first-access') || $request->is('first-access')) {
                    return $next($request);
                }
                
                // Redireciona para first-access se tentar acessar outras rotas
                return redirect()->route('first-access');
            }
        }

        return $next($request);
    }
}

