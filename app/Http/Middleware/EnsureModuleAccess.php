<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autoriza acesso quando o Module correspondente permite (inclui role `global`
 * via Module::userHasPermission). Alinhado ao menu/dashboard.
 */
class EnsureModuleAccess
{
    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Alinha com Module::userHasPermission (admin/global = acesso principal).
        if ($user->hasRole('global') || $user->hasRole('admin') || $user->hasRole('admin_user')) {
            return $next($request);
        }

        $module = Module::query()->where('key', $moduleKey)->first();

        if ($module) {
            abort_unless($module->userHasPermission($user), 403);
        } else {
            abort_unless($user->can("{$moduleKey}.index"), 403);
        }

        return $next($request);
    }
}
