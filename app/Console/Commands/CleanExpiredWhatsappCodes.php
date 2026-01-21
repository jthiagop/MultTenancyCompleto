<?php

namespace App\Console\Commands;

use App\Models\WhatsappAuthRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanExpiredWhatsappCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:clean-expired-codes {--dry-run : Executar sem deletar, apenas mostrar o que seria removido}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove códigos de vinculação WhatsApp expirados (válidos por 10 minutos)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $startTime = now();

        Log::info("🔄 Comando whatsapp:clean-expired-codes iniciado", [
            'dry_run' => $dryRun,
            'timestamp' => $startTime->toDateTimeString(),
        ]);

        if ($dryRun) {
            $this->info('🔍 Modo dry-run: nenhum registro será deletado');
        }

        // Contar total de registros antes da limpeza
        $totalBefore = WhatsappAuthRequest::count();
        $totalActive = WhatsappAuthRequest::where('status', 'active')->count();
        $totalWithWaId = WhatsappAuthRequest::whereNotNull('wa_id')->count();
        $totalPending = WhatsappAuthRequest::where('status', 'pending')->count();

        Log::info("📊 Estatísticas antes da limpeza", [
            'total_registros' => $totalBefore,
            'status_active' => $totalActive,
            'com_wa_id' => $totalWithWaId,
            'status_pending' => $totalPending,
        ]);

        // IMPORTANTE: Buscar apenas códigos expirados que NÃO foram vinculados
        // NUNCA deletar registros com status='active' ou que têm wa_id (já vinculados)
        // Esses registros são essenciais para o sistema encontrar o tenant nas mensagens
        $expiredCodes = WhatsappAuthRequest::where(function($query) {
                $query->whereNull('wa_id')  // Não foi vinculado ainda
                      ->where('status', '!=', 'active');  // Não está ativo
            })
            ->where('updated_at', '<', now()->subMinutes(\App\Models\WhatsappAuthRequest::EXPIRATION_MINUTES))
            ->get();

        $count = $expiredCodes->count();

        // Verificar quantos registros vinculados existem (para mostrar que foram preservados)
        $linkedRecords = WhatsappAuthRequest::where(function($query) {
            $query->where('status', 'active')
                  ->orWhereNotNull('wa_id');
        })->count();

        Log::info("🔍 Busca por registros expirados não vinculados concluída", [
            'registros_expirados_nao_vinculados' => $count,
            'registros_vinculados_preservados' => $linkedRecords,
            'expiration_minutes' => \App\Models\WhatsappAuthRequest::EXPIRATION_MINUTES,
            'cutoff_time' => now()->subMinutes(\App\Models\WhatsappAuthRequest::EXPIRATION_MINUTES)->toDateTimeString(),
        ]);

        if ($count === 0) {
            $this->info('✅ Nenhum código expirado não vinculado encontrado.');
            Log::info("✅ Comando whatsapp:clean-expired-codes concluído: nenhum registro para deletar", [
                'registros_vinculados_preservados' => $linkedRecords,
                'tempo_execucao' => now()->diffInSeconds($startTime) . ' segundos',
            ]);
            return Command::SUCCESS;
        }

        $this->info("📋 Encontrados {$count} código(s) expirado(s) não vinculado(s).");
        $this->info("🔒 {$linkedRecords} registro(s) vinculado(s) serão preservados.");

        // Logar detalhes dos registros que serão removidos
        $expiredCodesDetails = $expiredCodes->map(function ($code) {
            return [
                'id' => $code->id,
                'verification_code' => substr($code->verification_code, 0, 20) . '...',
                'tenant_id' => $code->tenant_id,
                'user_id' => $code->user_id,
                'status' => $code->status,
                'wa_id' => $code->wa_id,
                'phone_number_id' => $code->phone_number_id,
                'created_at' => $code->created_at->toDateTimeString(),
                'updated_at' => $code->updated_at->toDateTimeString(),
                'age_minutes' => now()->diffInMinutes($code->updated_at),
            ];
        })->toArray();

        Log::info("📋 Registros expirados não vinculados encontrados (serão removidos)", [
            'count' => $count,
            'registros' => $expiredCodesDetails,
        ]);

        if ($dryRun) {
            $this->table(
                ['ID', 'Código', 'Tenant ID', 'User ID', 'Status', 'wa_id', 'Criado em', 'Idade (min)'],
                $expiredCodes->map(function ($code) {
                    return [
                        $code->id,
                        substr($code->verification_code, 0, 20) . '...',
                        $code->tenant_id,
                        $code->user_id ?? 'N/A',
                        $code->status ?? 'N/A',
                        $code->wa_id ?? 'N/A',
                        $code->created_at->format('d/m/Y H:i:s'),
                        now()->diffInMinutes($code->updated_at),
                    ];
                })->toArray()
            );
            $this->info("💡 Execute sem --dry-run para remover estes registros.");
            $this->warn("⚠️  Registros com status='active' ou wa_id preenchido são SEMPRE preservados.");
            
            Log::info("🔍 Comando whatsapp:clean-expired-codes concluído (dry-run)", [
                'registros_que_seriam_removidos' => $count,
                'registros_vinculados_preservados' => $linkedRecords,
                'tempo_execucao' => now()->diffInSeconds($startTime) . ' segundos',
            ]);
        } else {
            // Deletar apenas códigos expirados que não foram vinculados
            // DUPLA VERIFICAÇÃO: NUNCA deletar registros com status='active' ou que têm wa_id
            $deleted = WhatsappAuthRequest::where(function($query) {
                    $query->whereNull('wa_id')  // Não foi vinculado ainda
                          ->where('status', '!=', 'active');  // Não está ativo
                })
                ->where('updated_at', '<', now()->subMinutes(\App\Models\WhatsappAuthRequest::EXPIRATION_MINUTES))
                ->delete();

            $totalAfter = WhatsappAuthRequest::count();
            $linkedRecordsAfter = WhatsappAuthRequest::where(function($query) {
                $query->where('status', 'active')
                      ->orWhereNotNull('wa_id');
            })->count();

            $this->info("✅ {$deleted} código(s) expirado(s) não vinculado(s) removido(s) com sucesso.");
            $this->info("🔒 {$linkedRecordsAfter} registro(s) vinculado(s) foram preservados.");
            $this->info("📊 Total de registros: {$totalBefore} → {$totalAfter} (removidos: {$deleted})");

            // Logar a ação com detalhes
            Log::info("✅ Comando whatsapp:clean-expired-codes executado com sucesso", [
                'registros_removidos' => $deleted,
                'registros_vinculados_preservados' => $linkedRecordsAfter,
                'total_antes' => $totalBefore,
                'total_depois' => $totalAfter,
                'tempo_execucao' => now()->diffInSeconds($startTime) . ' segundos',
                'registros_removidos_detalhes' => $expiredCodesDetails,
            ]);
        }

        return Command::SUCCESS;
    }
}
