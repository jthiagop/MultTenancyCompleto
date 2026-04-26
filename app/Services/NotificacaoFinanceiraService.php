<?php

namespace App\Services;

use App\Channels\WhatsappNotifiable;
use App\Jobs\SendWhatsappMessageJob;
use App\Models\User;
use App\Models\WhatsappAuthRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Centraliza o envio de notificações financeiras para uma empresa.
 *
 * Regras de negócio:
 *  - Notificações são enviadas a TODOS os usuários da company que possuem
 *    a permissão "financeiro.index" — não apenas a quem criou o registro.
 *  - Cada usuário recebe sua própria cópia na tabela `notifications`,
 *    permitindo leitura e arquivamento independentes.
 *  - O campo `company_id` no payload é usado pelo NotificationController
 *    para filtrar as notificações por empresa ativa na sessão.
 */
class NotificacaoFinanceiraService
{
    /**
     * Retorna todos os usuários da company com permissão financeiro.index.
     *
     * @param  int|null  $companyId  Null → retorna array vazio (sem envio)
     * @return Collection<int, User>
     */
    public function usuariosDaEmpresa(?int $companyId): Collection
    {
        if (! $companyId) {
            return collect();
        }

        return User::permission('financeiro.index')
            ->whereHas('companies', fn ($q) => $q->where('company_id', $companyId))
            ->get();
    }

    /**
     * Envia uma notificação para todos os usuários financeiros da empresa.
     *
     * @param  int|null      $companyId   ID da empresa
     * @param  Notification  $notification  Instância da notificação a enviar
     * @return int  Número de usuários notificados
     */
    public function notificarEmpresa(?int $companyId, Notification $notification): int
    {
        $usuarios = $this->usuariosDaEmpresa($companyId);

        if ($usuarios->isEmpty()) {
            Log::debug('[NotificacaoFinanceira] Nenhum usuário elegível', ['company_id' => $companyId]);
            return 0;
        }

        $total = 0;

        foreach ($usuarios as $user) {
            try {
                $user->notify(clone $notification);
                $total++;
            } catch (\Exception $e) {
                Log::warning('[NotificacaoFinanceira] Erro ao notificar usuário', [
                    'user_id'    => $user->id,
                    'company_id' => $companyId,
                    'erro'       => $e->getMessage(),
                ]);
            }
        }

        // Envio paralelo para o "Grupo WhatsApp" da empresa.
        // Despacha 1 mensagem por contato (independente da quantidade de users
        // notificados) — evita o canal WhatsappChannel para não duplicar envio.
        $this->notificarGrupoWhatsapp($companyId, $notification);

        return $total;
    }

    /**
     * Despacha o texto da notificação (toWhatsapp) para cada contato do
     * "Grupo WhatsApp" cadastrado na empresa (whatsapp_auth_requests com
     * kind='company_contact', status='active', wa_id != null).
     *
     * Não usa o WhatsappChannel: aqui não há User notifiable. Despachamos
     * direto o SendWhatsappMessageJob — que aceita userId nullable e já loga
     * em notifications_log com company_id.
     */
    protected function notificarGrupoWhatsapp(?int $companyId, Notification $notification): int
    {
        if (! $companyId) {
            return 0;
        }

        if (! $notification instanceof WhatsappNotifiable) {
            return 0;
        }

        $tenantId = tenancy()->tenant?->id;
        if (! $tenantId) {
            return 0;
        }

        try {
            $contatos = WhatsappAuthRequest::query()
                ->where('tenant_id', $tenantId)
                ->where('company_id', $companyId)
                ->where('kind', WhatsappAuthRequest::KIND_COMPANY_CONTACT)
                ->where('status', 'active')
                ->whereNotNull('wa_id')
                ->get(['id', 'wa_id', 'contact_label']);
        } catch (\Throwable $e) {
            Log::warning('[NotificacaoFinanceira] Falha ao consultar Grupo WhatsApp', [
                'company_id' => $companyId,
                'erro'       => $e->getMessage(),
            ]);
            return 0;
        }

        if ($contatos->isEmpty()) {
            return 0;
        }

        // Para o texto do toWhatsapp(), o $notifiable é apenas um stub —
        // ambas notificações financeiras já não dependem do nome do user.
        $stub = new \stdClass();

        try {
            $texto = $notification->toWhatsapp($stub);
        } catch (\Throwable $e) {
            Log::warning('[NotificacaoFinanceira] toWhatsapp falhou para grupo', [
                'company_id' => $companyId,
                'erro'       => $e->getMessage(),
            ]);
            return 0;
        }

        if (empty(trim((string) $texto))) {
            return 0;
        }

        // Resolve template HSM para envio fora da janela de 24h da Meta.
        // Mesma regra do WhatsappChannel: opt-in via toWhatsappTemplate()
        // e a flag services.meta.whatsapp_use_templates.
        $template = null;
        if (
            config('services.meta.whatsapp_use_templates', true) &&
            method_exists($notification, 'toWhatsappTemplate')
        ) {
            try {
                /** @var mixed $tpl */
                $tpl = call_user_func([$notification, 'toWhatsappTemplate'], $stub);
                if (is_array($tpl) && ! empty($tpl['name'])) {
                    $template = $tpl;
                }
            } catch (\Throwable $e) {
                Log::warning('[NotificacaoFinanceira] toWhatsappTemplate falhou para grupo, usando texto livre.', [
                    'company_id' => $companyId,
                    'erro'       => $e->getMessage(),
                ]);
            }
        }

        $enviados = 0;
        foreach ($contatos as $contato) {
            try {
                SendWhatsappMessageJob::dispatch(
                    tenantId:       (string) $tenantId,
                    userId:         null,
                    companyId:      (int) $companyId,
                    notificationId: null,
                    waId:           (string) $contato->wa_id,
                    text:           $texto,
                    kind:           WhatsappAuthRequest::KIND_COMPANY_CONTACT,
                    template:       $template,
                );
                $enviados++;
            } catch (\Throwable $e) {
                Log::warning('[NotificacaoFinanceira] Erro ao despachar para contato do grupo', [
                    'auth_request_id' => $contato->id,
                    'wa_id'           => $contato->wa_id,
                    'erro'            => $e->getMessage(),
                ]);
            }
        }

        if ($enviados > 0) {
            Log::info('[NotificacaoFinanceira] Mensagens despachadas para Grupo WhatsApp', [
                'company_id'        => $companyId,
                'contatos_alvo'     => $contatos->count(),
                'jobs_despachados'  => $enviados,
            ]);
        }

        return $enviados;
    }
}
