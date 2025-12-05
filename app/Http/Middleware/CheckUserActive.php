<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
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
            
            // Verifica se o usuário está ativo
            if (!$user->active) {
                // Faz logout do usuário
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                // Verifica se é uma requisição AJAX
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sua conta foi desativada. Entre em contato com o administrador.',
                        'error' => 'USER_INACTIVE'
                    ], 403);
                }
                
                // Redireciona para o login com mensagem de erro
                return redirect()->route('login')->with('error', 'Sua conta foi desativada. Entre em contato com o administrador.');
            }
        }

        return $next($request);
    }
}

