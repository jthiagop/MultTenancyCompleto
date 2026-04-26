<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Tenant;
use App\Notifications\ContaVencendoNotification;
use App\Services\NotificacaoFinanceiraService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Varre transações financeiras pendentes em TODOS os tenants e notifica o usuário
 * criador sobre registros atrasados, que vencem hoje ou amanhã.
 *
 * Deduplicação diária: não reenvia a mesma notificação (transação + urgência) no mesmo dia.
 */
class NotificarContasVencendo extends Command
{
    protected $signature   = 'financeiro:notificar-contas-vencendo';
    protected $description = 'Notifica usuários sobre contas atrasadas, que vencem hoje ou amanhã (multi-tenant)';

    public function handle(): void
    {
        $hoje   = Carbon::today();
        $amanha = Carbon::tomorrow();

        $totalGeral = 0;
        $tenants    = Tenant::all();
        $svc        = app(NotificacaoFinanceiraService::class);

        $this->info("Processando {$tenants->count()} tenant(s)...");

        $tenants->each(function (Tenant $tenant) use ($hoje, $amanha, &$totalGeral, $svc) {
            $tenant->run(function () use ($tenant, $hoje, $amanha, &$totalGeral, $svc) {

                $situacoesPendentes = ['em_aberto', 'atrasado', 'pago_parcial'];

                // Agrupa por company_id para evitar repetição de busca de usuários
                $transacoes = TransacaoFinanceira::query()
                    ->whereIn('situacao', $situacoesPendentes)
                    ->whereNotNull('data_vencimento')
                    ->whereNotNull('company_id')
                    ->whereDate('data_vencimento', '<=', $amanha)
                    ->orderBy('data_vencimento')
                    ->get(['id', 'descricao', 'valor', 'data_vencimento', 'tipo',
                           'situacao', 'company_id', 'created_by']);

                $totalTenant  = 0;
                // Cache de usuários por company_id para não buscar N vezes
                $usuarioCache = [];
                // Cache de horário configurado por company_id
                $horaCache    = [];
                $horaAtual    = Carbon::now()->format('H'); // "08", "09", etc.

                // Pré-carrega TODAS as notificações de contas vencendo já enviadas hoje
                // em uma única consulta. Antes, fazíamos um exists() por (transacao×urgencia×user)
                // — cenário N+1 que escalava a 1500+ queries por execução.
                $jaNotificados = DB::table('notifications')
                    ->where('type', ContaVencendoNotification::class)
                    ->whereDate('created_at', $hoje)
                    ->select(
                        'notifiable_id',
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.transacao_id')) as transacao_id"),
                        DB::raw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.urgencia')) as urgencia"),
                    )
                    ->get()
                    ->mapWithKeys(fn ($row) => [
                        "{$row->notifiable_id}|{$row->transacao_id}|{$row->urgencia}" => true,
                    ])
                    ->all();

                foreach ($transacoes as $transacao) {
                    $vencimento = Carbon::parse(
                        $transacao->getRawOriginal('data_vencimento') ?? $transacao->data_vencimento
                    )->startOfDay();

                    $urgencia = match (true) {
                        $vencimento->isBefore($hoje)  => 'atrasado',
                        $vencimento->isSameDay($hoje) => 'hoje',
                        default                       => 'amanha',
                    };

                    $companyId = $transacao->company_id;

                    // Verificar horário de notificação configurado por empresa (padrão 08:00)
                    if (! isset($horaCache[$companyId])) {
                        $company = Company::find($companyId);
                        $details = json_decode($company->details ?? '{}', true);
                        $horaCache[$companyId] = substr($details['whatsapp_hora_notificacao'] ?? '08:00', 0, 2);
                    }
                    if ($horaAtual !== $horaCache[$companyId]) {
                        continue;
                    }

                    // Cache de usuários elegíveis por empresa
                    if (! isset($usuarioCache[$companyId])) {
                        $usuarioCache[$companyId] = $svc->usuariosDaEmpresa($companyId);
                    }

                    $usuarios = $usuarioCache[$companyId];
                    if ($usuarios->isEmpty()) {
                        continue;
                    }

                    $tipoRaw = $transacao->getRawOriginal('tipo') ?? '';
                    $subTipo = in_array($tipoRaw, ['E', 'entrada']) ? 'receita' : 'despesa';

                    $notification = new ContaVencendoNotification(
                        descricao:         $transacao->descricao ?? 'Lançamento sem descrição',
                        valor:             (float) $transacao->valor,
                        dataVencimento:    $vencimento->format('d/m/Y'),
                        dataVencimentoIso: $vencimento->format('Y-m-d'),
                        subTipo:           $subTipo,
                        transacaoId:       $transacao->id,
                        companyId:         $companyId,
                        urgencia:          $urgencia,
                        triggeredBy:       $transacao->created_by,
                    );

                    foreach ($usuarios as $user) {
                        // Deduplicação O(1) com base na pré-carga acima
                        $key = "{$user->id}|{$transacao->id}|{$urgencia}";
                        if (isset($jaNotificados[$key])) {
                            continue;
                        }

                        try {
                            $user->notify(clone $notification);
                            $jaNotificados[$key] = true; // evita duplicar nas próximas iterações desta execução
                            $totalTenant++;
                        } catch (\Exception $e) {
                            Log::warning('[NotificarContasVencendo] Erro ao notificar', [
                                'tenant'       => $tenant->id,
                                'transacao_id' => $transacao->id,
                                'user_id'      => $user->id,
                                'erro'         => $e->getMessage(),
                            ]);
                        }
                    }
                }

                $totalGeral += $totalTenant;
                $this->line("  Tenant {$tenant->id}: {$totalTenant} notificação(ões) enviada(s)");
            });
        });

        $this->info("Total geral: {$totalGeral} notificação(ões) enviada(s).");
    }
}
