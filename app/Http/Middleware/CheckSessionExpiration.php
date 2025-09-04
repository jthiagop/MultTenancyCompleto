<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Verifica se o usuário está autenticado e a sessão está expirada
        if (Auth::check() && $request->session()->has('last_activity')) {
            $sessionLifetime = config('session.lifetime') * 60; // Convertendo para segundos
            $lastActivity = $request->session()->get('last_activity');

            if (time() - $lastActivity > $sessionLifetime) {
                // A sessão expirou
                Auth::logout(); // Faz logout
                $request->session()->invalidate(); // Invalida a sessão
                $request->session()->regenerateToken(); // Regenera o token CSRF

                // Verificar se é uma requisição AJAX
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sua sessão expirou por inatividade. Faça login novamente.',
                        'error' => 'SESSION_EXPIRED',
                        'redirect' => route('login')
                    ], 401);
                }

                // Redireciona para a página de login com uma mensagem de sessão expirada
                return redirect()->route('login')->with('error', 'Sua sessão expirou por inatividade. Faça login novamente para continuar.');
            }
        }

        // Atualiza a última atividade do usuário
        $request->session()->put('last_activity', time());

        return $next($request);
    }
}
