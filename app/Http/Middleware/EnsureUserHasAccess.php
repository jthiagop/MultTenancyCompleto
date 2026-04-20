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
        $companyId = $request->route('company_id'); // ID da empresa passado na rota (opcional)

        // Se não há company_id na rota, não há restrição a verificar
        if (!$companyId) {
            return $next($request);
        }

        // Verifica se o usuário tem acesso à company solicitada.
        // Regra:
        //   1) Está vinculado diretamente à company ($companyId); OU
        //   2) Está vinculado à matriz DA qual essa company é filial
        //      (isto é, company->parent_id aparece entre as companies do user); OU
        //   3) Está vinculado à company ($companyId) que, por sua vez, é uma matriz
        //      e a rota pede acesso a uma de suas filiais (companies.parent_id = $companyId
        //      E o user tem vínculo com essa matriz).
        //
        // IMPORTANTE: removido o `orWhere('companies.type','matriz')` solto que liberava
        // acesso a qualquer empresa para quem tivesse vínculo com QUALQUER matriz do tenant.
        $hasAccess = $user->companies()
            ->where(function ($query) use ($companyId) {
                $query->where('companies.id', $companyId)
                      ->orWhereIn('companies.id', function ($sub) use ($companyId) {
                          // matriz da qual $companyId é filha
                          $sub->select('parent_id')
                              ->from('companies')
                              ->where('id', $companyId)
                              ->whereNotNull('parent_id');
                      });
            })
            ->exists();

        if ($hasAccess) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Você não tem permissão para acessar esta área.');
    }
}
