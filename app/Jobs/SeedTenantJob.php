<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SeedTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $tenant;
    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->tenant->run(function () {

            // Criar o usuário
            $user = User::create([
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
                'password' => $this->tenant->password,
                'avatar' => '1253525'


            ]);

            // Atribuir o papel de administrador ao usuário
            $user->assignRole(['global', 'admin', 'admin_user', 'user']);
            
            // Dar todas as permissões ao primeiro usuário
            try {
                $allPermissions = \Spatie\Permission\Models\Permission::all();
                if ($allPermissions->count() > 0) {
                    $user->syncPermissions($allPermissions->pluck('id')->toArray());
                }
            } catch (\Exception $e) {
                // Se as permissões ainda não existirem, não é um problema
            }

            // Criar a empresa
            $company = Company::create([
                'name' => $this->tenant->name, // ou qualquer outro nome desejado
                'type' => 'matriz', // ou 'filial', conforme necessário
                'parent_id' => null, // ou defina o ID da matriz se for uma filial
                'status' => 'active', // ou qualquer outro status padrão desejado
                'tags' => json_encode(['tag1', 'tag2']), // ou qualquer outra tag desejada
                'created_by' => null, // Deixe null ou defina conforme necessário
                'updated_by' => null, // Deixe null ou defina conforme necessário
            ]);

            // Relacionar a empresa ao usuário na tabela pivot company_user
            $user->companies()->attach($company->id, [
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Relacionar a empresa ao usuário, se necessário
            $user->company_id = $company->id;
            $user->save();
        });
    }
}
