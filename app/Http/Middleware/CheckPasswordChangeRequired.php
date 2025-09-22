<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChangeRequired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (auth()->check()) {
            $user = auth()->user();
            
            // Verificar se o usuário precisa alterar a senha
            if ($user->must_change_password) {
                // Permitir acesso apenas à rota de alteração de senha e logout
                $allowedRoutes = [
                    'password.change',
                    'password.update',
                    'logout',
                    'password.change.show'
                ];
                
                $currentRoute = $request->route() ? $request->route()->getName() : null;
                
                // Se não for uma rota permitida, redirecionar para alteração de senha
                if (!in_array($currentRoute, $allowedRoutes)) {
                    return redirect()->route('password.change.show')
                        ->with('warning', 'Você deve alterar sua senha antes de continuar.');
                }
            }
        }

        return $next($request);
    }
}