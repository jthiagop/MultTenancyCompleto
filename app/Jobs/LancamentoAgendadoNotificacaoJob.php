<?php

namespace App\Jobs;

use App\Models\Financeiro\TransacaoFinanceira;
use App\Models\Tenant;
use App\Notifications\LancamentoAgendadoNotification;
use App\Services\NotificacaoFinanceiraService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Dispatched no momento do save quando `agendado = true`.
 * Adiado (->delay) até o início do dia de `data_vencimento`.
 *
 * Ao executar, notifica todos os usuários financeiros da empresa
 * via banco de dados + WhatsApp (se vinculado) lembrando do vencimento.
 *
 * Uso:
 *   LancamentoAgendadoNotificacaoJob::dispatch(
 *       transacaoId: $transacao->id,
 *       companyId:   $companyId,
 *       tenantId:    tenancy()->tenant?->id,
 *       triggeredBy: Auth::id(),
 *   )->delay($vencimento->startOfDay());
 */
class LancamentoAgendadoNotificacaoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        public readonly int    $transacaoId,
        public readonly int    $companyId,
        public readonly ?string $tenantId,
        public readonly ?int   $triggeredBy = null,
    ) {}

    public function handle(NotificacaoFinanceiraService $notificacaoService): void
    {
        // Inicializa o tenant para acessar o banco de dados correto
        if ($this->tenantId) {
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                tenancy()->initialize($tenant);
            } else {
                Log::warning('[LancamentoAgendadoNotificacaoJob] Tenant não encontrado', [
                    'tenant_id'    => $this->tenantId,
                    'transacao_id' => $this->transacaoId,
                ]);
                return;
            }
        }

        $transacao = TransacaoFinanceira::where('company_id', $this->companyId)
            ->find($this->transacaoId);

        if (! $transacao) {
            Log::warning('[LancamentoAgendadoNotificacaoJob] Transação não encontrada', [
                'transacao_id' => $this->transacaoId,
                'company_id'   => $this->companyId,
            ]);
            return;
        }

        // Se o lançamento já foi baixado (pago/recebido), não notifica
        $situacao = $transacao->situacao instanceof \BackedEnum
            ? $transacao->situacao->value
            : (string) ($transacao->situacao ?? '');

        if (in_array($situacao, ['pago', 'recebido', 'desconsiderado'], true)) {
            Log::info('[LancamentoAgendadoNotificacaoJob] Lançamento já liquidado, notificação cancelada', [
                'transacao_id' => $this->transacaoId,
                'situacao'     => $situacao,
            ]);
            return;
        }

        $vencimento = $transacao->getRawOriginal('data_vencimento') ?? '';

        try {
            $notificacaoService->notificarEmpresa(
                $this->companyId,
                new LancamentoAgendadoNotification(
                    descricao:   $transacao->descricao ?? '',
                    valor:       (float) $transacao->valor,
                    tipo:        $transacao->tipo instanceof \BackedEnum
                                    ? $transacao->tipo->value
                                    : (string) $transacao->tipo,
                    vencimento:  $vencimento,
                    transacaoId: $transacao->id,
                    companyId:   $this->companyId,
                    triggeredBy: $this->triggeredBy,
                )
            );

            Log::info('[LancamentoAgendadoNotificacaoJob] Notificação enviada', [
                'transacao_id' => $this->transacaoId,
                'company_id'   => $this->companyId,
            ]);
        } catch (\Throwable $e) {
            Log::error('[LancamentoAgendadoNotificacaoJob] Falha ao notificar', [
                'transacao_id' => $this->transacaoId,
                'error'        => $e->getMessage(),
            ]);

            throw $e; // re-lança para permitir retry automático da queue
        }
    }

    /**
     * Chamado quando o job esgota tries. Garante observabilidade em failed_jobs
     * + log estruturado para auditoria — antes ficava silencioso.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('[LancamentoAgendadoNotificacaoJob] Job falhou após todas as tentativas', [
            'transacao_id' => $this->transacaoId,
            'company_id'   => $this->companyId,
            'tenant_id'    => $this->tenantId,
            'error'        => $exception->getMessage(),
        ]);
    }
}
