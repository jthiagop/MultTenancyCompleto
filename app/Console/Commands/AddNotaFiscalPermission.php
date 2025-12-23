<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AddNotaFiscalPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:add-notafiscal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adiciona permissões do módulo NF-e e atribui aos administradores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adicionando permissões do módulo NF-e...');

        $permissions = [
            'notafiscal.index' => 'Visualizar listagem do módulo nota fiscal',
            'notafiscal.create' => 'Criar registros no módulo nota fiscal',
            'notafiscal.edit' => 'Editar registros do módulo nota fiscal',
            'notafiscal.delete' => 'Excluir registros do módulo nota fiscal',
            'notafiscal.show' => 'Visualizar detalhes de registros do módulo nota fiscal',
        ];

        foreach ($permissions as $name => $description) {
            $permission = Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['name' => $name, 'guard_name' => 'web']
            );
            $this->info("Permissão '{$name}' criada/verificada.");
        }

        // Atribuir permissões aos roles de administrador (se existirem)
        $adminRoles = ['global', 'admin', 'admin_user'];
        
        foreach ($adminRoles as $roleName) {
            try {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    foreach (array_keys($permissions) as $permissionName) {
                        $permission = Permission::where('name', $permissionName)->first();
                        if ($permission && !$role->hasPermissionTo($permission)) {
                            $role->givePermissionTo($permission);
                            $this->info("Permissão '{$permissionName}' atribuída ao role '{$roleName}'.");
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->warn("Role '{$roleName}' não encontrado, pulando...");
            }
        }

        // Atribuir todas as permissões aos usuários que têm roles administrativos (se existirem)
        try {
            $adminRoleNames = [];
            foreach ($adminRoles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $adminRoleNames[] = $roleName;
                }
            }

            if (!empty($adminRoleNames)) {
                $users = User::role($adminRoleNames)->get();
                foreach ($users as $user) {
                    foreach (array_keys($permissions) as $permissionName) {
                        $permission = Permission::where('name', $permissionName)->first();
                        if ($permission && !$user->hasPermissionTo($permission)) {
                            $user->givePermissionTo($permission);
                            $this->info("Permissão '{$permissionName}' atribuída ao usuário '{$user->name}'.");
                        }
                    }
                }
            } else {
                // Se não houver roles, atribuir a todos os usuários (fallback)
                $this->warn("Nenhum role administrativo encontrado. Atribuindo permissões a todos os usuários...");
                $users = User::all();
                foreach ($users as $user) {
                    foreach (array_keys($permissions) as $permissionName) {
                        $permission = Permission::where('name', $permissionName)->first();
                        if ($permission && !$user->hasPermissionTo($permission)) {
                            $user->givePermissionTo($permission);
                        }
                    }
                }
                $this->info("Permissões atribuídas a todos os usuários.");
            }
        } catch (\Exception $e) {
            $this->warn("Erro ao atribuir permissões aos usuários: " . $e->getMessage());
            // Fallback: atribuir a todos os usuários
            $users = User::all();
            foreach ($users as $user) {
                foreach (array_keys($permissions) as $permissionName) {
                    $permission = Permission::where('name', $permissionName)->first();
                    if ($permission && !$user->hasPermissionTo($permission)) {
                        $user->givePermissionTo($permission);
                    }
                }
            }
            $this->info("Permissões atribuídas a todos os usuários (fallback).");
        }

        $this->info('Permissões do módulo NF-e adicionadas e atribuídas com sucesso!');
        
        return Command::SUCCESS;
    }
}

