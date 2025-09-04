<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Interceptar erro 419 (Page Expired) e tratar de forma elegante
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sua sessão expirou por inatividade. Faça login novamente.',
                    'error' => 'SESSION_EXPIRED',
                    'redirect' => route('login')
                ], 419);
            }

            // Para requisições normais, redirecionar para login
            return redirect()->route('login')->with('error', 'Sua sessão expirou por inatividade. Faça login novamente para continuar.');
        }
    }
}
