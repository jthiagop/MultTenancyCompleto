<?php

// app/Http/Middleware/SetActiveCompany.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetActiveCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $activeCompanyId = session('active_company_id');

            // Se não houver empresa ativa na sessão, define a primeira como padrão
            if (!$activeCompanyId && $user->companies()->exists()) {
                $activeCompanyId = $user->companies()->first()->id;
                session(['active_company_id' => $activeCompanyId]);
            }

            // Se o usuário não tiver acesso à empresa na sessão, limpa a sessão
            if ($activeCompanyId && !$user->companies()->where('id', $activeCompanyId)->exists()) {
                session()->forget('active_company_id');
                // Redireciona ou lança um erro, se preferir
                return redirect('/')->with('error', 'Empresa inválida na sessão.'); 
            }
        }

        return $next($request);
    }
}
