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
        $this->tenant->run(function() {
            // Criar o usuário
            $user = User::create([
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
                'password' => $this->tenant->password,
            ]);

            // Atribuir o papel de administrador ao usuário
            $user->assignRole('admin');

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

            // Relacionar a empresa ao usuário, se necessário
            $user->company_id = $company->id;
            $user->save();
        });
    }
}
