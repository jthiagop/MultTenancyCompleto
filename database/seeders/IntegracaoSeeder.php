<?php

namespace Database\Seeders;

use App\Models\Integracao;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class IntegracaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Cria integrações padrão (pendentes) para todos os usuários do tenant
     */
    public function run(): void
    {
        // Obter número do sistema Dominus do .env
        $destinatario = env('META_WHATSAPP_NUMBER', '558183797797');

        // Buscar todos os usuários do tenant
        $users = User::all();

        if ($users->isEmpty()) {
            Log::info('IntegracaoSeeder: Nenhum usuário encontrado. Pulando criação de integrações.');
            return;
        }

        // Tipos de integração disponíveis (apenas WhatsApp está ativo por enquanto)
        $tiposIntegracao = [
            'whatsapp' => true,  // Ativo
            'dda' => false,      // Desativado (para futuro)
            'email' => false,    // Desativado (para futuro)
        ];

        foreach ($users as $user) {
            foreach ($tiposIntegracao as $tipo => $ativo) {
                // Criar integração apenas se estiver ativa
                if ($ativo) {
                    // Verificar se já existe integração deste tipo para o usuário
                    $integracaoExistente = Integracao::where('user_id', $user->id)
                        ->where('tipo', $tipo)
                        ->first();

                    if (!$integracaoExistente) {
                        Integracao::create([
                            'tipo' => $tipo,
                            'status' => 'pendente',
                            'remetente' => null,
                            'destinatario' => $destinatario,
                            'user_id' => $user->id,
                        ]);

                        Log::info("Integração {$tipo} criada para usuário {$user->id} ({$user->name})");
                    } else {
                        Log::info("Integração {$tipo} já existe para usuário {$user->id}. Pulando criação.");
                    }
                }
            }
        }

        Log::info("IntegracaoSeeder concluído. Integrações criadas para {$users->count()} usuário(s).");
    }
}
