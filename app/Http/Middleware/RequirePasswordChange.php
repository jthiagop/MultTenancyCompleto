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

                // Permite acesso ao endpoint de status da autenticação (SPA React)
                if ($request->routeIs('api.auth.status') || $request->is('api/auth/status')) {
                    return $next($request);
                }

                // Permite acesso à rota de alteração de senha (POST)
                if ($request->routeIs('password.change', 'password.change.show', 'logout')) {
                    return $next($request);
                }

                // Permite acesso a rotas de logout e csrf-cookie
                if ($request->is('sanctum/csrf-cookie') || $request->is('logout')) {
                    return $next($request);
                }

                // API/React Web: retorna JSON ao invés de redirect
                if ($request->expectsJson() || $request->header('X-React-Web') === '1') {
                    return response()->json([
                        'error'   => 'PASSWORD_CHANGE_REQUIRED',
                        'message' => 'Você precisa alterar sua senha.',
                    ], 422);
                }

                // Redireciona para first-access se tentar acessar outras rotas
                return redirect()->route('first-access');
            }
        }

        return $next($request);
    }
}

