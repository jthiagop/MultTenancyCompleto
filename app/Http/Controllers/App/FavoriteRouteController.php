<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\UserFavoriteRoute;
use App\Services\ModuleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class FavoriteRouteController extends Controller
{
    protected $moduleService;

    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * API: Lista módulos disponíveis para favoritar
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableModules(Request $request)
    {
        $user = Auth::user();
        $companyId = session('active_company_id');
        $modules = $this->moduleService->getAuthorizedModules($user, $companyId);
        
        // Remover módulos já favoritados
        $favorited = UserFavoriteRoute::forCurrentUser()
            ->pluck('module_key')
            ->toArray();
        
        $available = array_filter($modules, function ($module) use ($favorited) {
            return !in_array($module['key'], $favorited);
        });
        
        return response()->json(['modules' => array_values($available)]);
    }

    /**
     * Adicionar aos favoritos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'module_key' => 'required|string',
        ]);

        $user = Auth::user();
        $companyId = session('active_company_id');

        if (!$companyId) {
            return response()->json(['error' => 'Nenhuma empresa ativa encontrada.'], 400);
        }

        // Verificar permissão
        if (!$this->moduleService->canFavorite($user, $validated['module_key'], $companyId)) {
            return response()->json(['error' => 'Você não tem permissão para este módulo'], 403);
        }

        // Buscar dados do módulo
        $module = $this->moduleService->getModuleByKey($validated['module_key'], $companyId);

        if (!$module) {
            return response()->json(['error' => 'Módulo não encontrado'], 404);
        }

        // Verificar se a rota existe
        if (!Route::has($module->route_name)) {
            return response()->json(['error' => 'Rota não encontrada'], 404);
        }

        // Verificar se já existe
        $existing = UserFavoriteRoute::where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->where('module_key', $validated['module_key'])
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Módulo já está nos favoritos'], 422);
        }

        // Criar favorito
        $maxOrder = UserFavoriteRoute::forCurrentUser()->max('order_index') ?? 0;
        
        $favorite = UserFavoriteRoute::create([
            'user_id' => $user->id,
            'company_id' => $companyId,
            'route_name' => $module->route_name,
            'display_name' => $module->name,
            'icon' => $module->icon_path ?? $module->icon_class,
            'module_key' => $module->key,
            'order_index' => $maxOrder + 1,
        ]);

        return response()->json(['success' => true, 'favorite' => $favorite]);
    }

    /**
     * Remover dos favoritos
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $favorite = UserFavoriteRoute::where('user_id', Auth::id())
            ->where('company_id', session('active_company_id'))
            ->findOrFail($id);

        $favorite->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reordenar favoritos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.order_index' => 'required|integer',
        ]);

        foreach ($validated['items'] as $item) {
            UserFavoriteRoute::where('id', $item['id'])
                ->where('user_id', Auth::id())
                ->where('company_id', session('active_company_id'))
                ->update(['order_index' => $item['order_index']]);
        }

        return response()->json(['success' => true]);
    }
}

