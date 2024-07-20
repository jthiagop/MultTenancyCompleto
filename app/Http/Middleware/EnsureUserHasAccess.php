<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $companyId = $request->route('company_id'); // Supondo que o ID da empresa é passado na rota

        // Verifica se o usuário está associado à empresa ou a uma empresa matriz
        $hasAccess = $user->companies()->where(function($query) use ($companyId) {
            $query->where('companies.id', $companyId)
                  ->orWhere('companies.parent_id', $companyId)
                  ->orWhere('companies.type', 'matriz');
        })->exists();

        if ($hasAccess) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Você não tem permissão para acessar esta área.');
    }
}
