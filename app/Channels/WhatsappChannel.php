<?php

namespace App\Channels;

use App\Jobs\SendWhatsappMessageJob;
use App\Models\WhatsappAuthRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Canal de notificação via WhatsApp (Meta Business API).
 *
 * Pós-Onda 3 este canal é estritamente assíncrono: ele apenas valida
 * que o usuário tem vínculo ativo, monta o texto e despacha
 * {@see SendWhatsappMessageJob} para a queue. O envio HTTP, retry e
 * logging em `notifications_log` acontecem no job.
 *
 * Notification que usa este canal precisa:
 *   1. Adicionar `WhatsappChannel::class` ao array via()
 *   2. Implementar a interface {@see WhatsappNotifiable} (toWhatsapp)
 *
 * Opcional — para entregar fora da janela de 24h da Meta, a Notification
 * pode também implementar `toWhatsappTemplate(object $notifiable): array`
 * retornando a estrutura de um template HSM aprovado. Quando presente
 * (e a config `services.meta.whatsapp_use_templates` estiver ligada),
 * o canal envia template em vez de texto livre. O texto livre continua
 * sendo passado como fallback para auditoria e como `payload_excerpt`
 * no `notifications_log`.
 */
class WhatsappChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! $notification instanceof WhatsappNotifiable) {
            return;
        }

        $tenantId = tenancy()->tenant?->id ?? null;
        if (! $tenantId) {
            Log::debug('[WhatsappChannel] Tenant não inicializado, ignorando envio.');
            return;
        }

        // Vínculo ativo do usuário com WhatsApp (banco central).
        $binding = WhatsappAuthRequest::query()
            ->where('tenant_id', $tenantId)
            ->where('user_id', $notifiable->getKey())
            ->where('status', 'active')
            ->whereNotNull('wa_id')
            ->first();

        if (! $binding) {
            return; // Usuário sem WhatsApp vinculado — descarta silenciosamente
        }

        $text = $notification->toWhatsapp($notifiable);

        if (empty(trim($text))) {
            return;
        }

        // company_id e notification_id quando o payload os fornecer.
        $companyId = null;
        if (is_callable([$notification, 'toArray'])) {
            try {
                /** @var array<string,mixed> $payload */
                $payload   = call_user_func([$notification, 'toArray'], $notifiable);
                $companyId = $payload['company_id'] ?? null;
            } catch (\Throwable) {
                // Algumas Notifications podem requerer contexto que não existe aqui — tolerável.
            }
        }

        // O `id` do Notification do Laravel só existe após o canal database persistir.
        // Em ambientes onde o canal database está no mesmo `via()`, ele é definido
        // antes deste canal pelo dispatcher; capturamos best-effort.
        $notificationId = $notification->id ?? null;

        // Resolve template HSM, se a Notification declarar um e a feature flag estiver ligada.
        $template = $this->resolveTemplate($notification, $notifiable);

        SendWhatsappMessageJob::dispatch(
            tenantId:       (string) $tenantId,
            userId:         (int) $notifiable->getKey(),
            companyId:      $companyId ? (int) $companyId : null,
            notificationId: $notificationId ? (string) $notificationId : null,
            waId:           (string) $binding->wa_id,
            text:           $text,
            template:       $template,
        );
    }

    /**
     * Resolve o template HSM declarado pela Notification (opcional).
     *
     * Garantias do retorno:
     *  - null: sem template; cai pro envio de texto livre (legado).
     *  - array: shape compatível com o SendWhatsappMessageJob:
     *           ['name' => ..., 'language' => 'pt_BR', 'components' => [...]]
     *
     * @return array<string,mixed>|null
     */
    private function resolveTemplate(Notification $notification, object $notifiable): ?array
    {
        if (! config('services.meta.whatsapp_use_templates', true)) {
            return null;
        }

        if (! method_exists($notification, 'toWhatsappTemplate')) {
            return null;
        }

        try {
            // Método opt-in (duck typing): só chamamos depois do
            // method_exists acima. O Intelephense não consegue inferir,
            // então o callable explícito silencia o "Undefined method".
            /** @var mixed $tpl */
            $tpl = call_user_func([$notification, 'toWhatsappTemplate'], $notifiable);
        } catch (\Throwable $e) {
            Log::warning('[WhatsappChannel] Falha ao montar template, usando texto livre.', [
                'notification' => get_class($notification),
                'error'        => $e->getMessage(),
            ]);
            return null;
        }

        if (! is_array($tpl) || empty($tpl['name'])) {
            return null;
        }

        return $tpl;
    }
}
