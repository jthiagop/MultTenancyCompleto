<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('app.modules.index');
    }

    /**
     * Get modules data for DataTables
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $modules = Module::query()
                ->ordered()
                ->select('modules.*');

            return DataTables::of($modules)
                ->addColumn('status', function ($module) {
                    return $module->is_active 
                        ? '<span class="badge badge-light-success">Ativo</span>' 
                        : '<span class="badge badge-light-danger">Inativo</span>';
                })
                ->addColumn('dashboard', function ($module) {
                    return $module->show_on_dashboard 
                        ? '<span class="badge badge-light-primary">Sim</span>' 
                        : '<span class="badge badge-light-secondary">Não</span>';
                })
                ->addColumn('actions', function ($module) {
                    $iconPath = $module->icon_path ? asset('storage/' . $module->icon_path) : asset('assets/media/avatars/blank.png');
                    return '
                        <button class="btn btn-icon btn-active-light-primary w-30px h-30px me-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#kt_modal_update_module"
                            data-module-id="' . $module->id . '"
                            data-module-name="' . htmlspecialchars($module->name) . '"
                            data-module-key="' . htmlspecialchars($module->key) . '"
                            data-module-route="' . htmlspecialchars($module->route_name) . '"
                            data-module-permission="' . htmlspecialchars($module->permission ?? '') . '"
                            data-module-description="' . htmlspecialchars($module->description ?? '') . '"
                            data-module-active="' . ($module->is_active ? '1' : '0') . '"
                            data-module-dashboard="' . ($module->show_on_dashboard ? '1' : '0') . '"
                            data-module-order="' . $module->order_index . '"
                            data-module-icon-path="' . htmlspecialchars($iconPath) . '">
                            <span class="svg-icon svg-icon-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="currentColor"/>
                                    <path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </button>
                        <button class="btn btn-icon btn-active-light-primary w-30px h-30px" 
                            data-kt-modules-table-filter="delete_row"
                            data-module-id="' . $module->id . '"
                            data-module-name="' . htmlspecialchars($module->name) . '">
                            <span class="svg-icon svg-icon-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"/>
                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"/>
                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"/>
                                </svg>
                            </span>
                        </button>
                    ';
                })
                ->editColumn('created_at', function ($module) {
                    return $module->created_at->format('d M Y, H:i');
                })
                ->rawColumns(['status', 'dashboard', 'actions'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'route_name' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'permission' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_on_dashboard' => 'boolean',
            'order_index' => 'integer',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['company_id'] = auth()->user()->company_id ?? null;

        // Processa upload do ícone
        if ($request->hasFile('icon')) {
            $file = $request->file('icon');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('modules/icons', $fileName, 'public');
            $validated['icon_path'] = $path;
        }

        // Remove o campo 'icon' do array validado antes de criar o módulo
        unset($validated['icon']);

        $module = Module::create($validated);

        // Criar permissões padrão para o módulo se não existirem
        if ($module->permission) {
            $this->createModulePermissions($module->key);
        }

        return response()->json([
            'success' => true,
            'message' => 'Módulo criado com sucesso!'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'route_name' => 'required|string|max:255',
            'icon_class' => 'nullable|string|max:255',
            'permission' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_on_dashboard' => 'boolean',
            'order_index' => 'integer',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'icon_remove' => 'nullable|boolean',
        ]);

        // Processa remoção do ícone
        if ($request->has('icon_remove') && $request->icon_remove) {
            if ($module->icon_path) {
                Storage::disk('public')->delete($module->icon_path);
            }
            $validated['icon_path'] = null;
        }

        // Processa upload do novo ícone
        if ($request->hasFile('icon')) {
            // Remove o ícone antigo se existir
            if ($module->icon_path) {
                Storage::disk('public')->delete($module->icon_path);
            }

            $file = $request->file('icon');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('modules/icons', $fileName, 'public');
            $validated['icon_path'] = $path;
        }

        // Remove campos que não devem ser salvos diretamente
        unset($validated['icon'], $validated['icon_remove']);

        $module->update($validated);

        // Criar permissões padrão para o módulo se não existirem
        if ($module->permission) {
            $this->createModulePermissions($module->key);
        }

        return response()->json([
            'success' => true,
            'message' => 'Módulo atualizado com sucesso!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();

        return response()->json([
            'success' => true,
            'message' => 'Módulo excluído com sucesso!'
        ]);
    }

    /**
     * Cria permissões padrão para um módulo
     *
     * @param string $moduleKey
     * @return void
     */
    private function createModulePermissions(string $moduleKey): void
    {
        $permissions = [
            'index' => 'Visualizar listagem do módulo',
            'create' => 'Criar registros no módulo',
            'edit' => 'Editar registros do módulo',
            'delete' => 'Excluir registros do módulo',
            'show' => 'Visualizar detalhes de registros do módulo',
        ];

        foreach ($permissions as $action => $description) {
            $permissionName = "{$moduleKey}.{$action}";
            
            Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web'],
                ['name' => $permissionName, 'guard_name' => 'web']
            );
        }
    }
}
