<?php

namespace App\Channels;

use App\Models\WhatsappAuthRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Canal de notificação via WhatsApp (Meta Business API).
 *
 * Uso em qualquer Notification:
 *   1. Adicionar WhatsappChannel::class ao array via()
 *   2. Implementar a interface WhatsappNotifiable (toWhatsapp)
 *
 * O canal verifica automaticamente se o usuário tem um vínculo
 * ativo (WhatsappAuthRequest com status='active') antes de enviar.
 * Se não tiver, a mensagem é silenciosamente descartada.
 */
class WhatsappChannel
{
    /**
     * @param  object        $notifiable  Normalmente uma instância de User
     * @param  Notification  $notification
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // A notificação precisa implementar WhatsappNotifiable
        if (! $notification instanceof WhatsappNotifiable) {
            return;
        }

        // Só envia se o tenant estiver inicializado
        $tenantId = tenancy()->tenant?->id ?? null;
        if (! $tenantId) {
            Log::debug('[WhatsappChannel] Tenant não inicializado, ignorando envio.');
            return;
        }

        // Busca o vínculo ativo do usuário no banco central
        $binding = WhatsappAuthRequest::on('mysql')
            ->where('tenant_id', $tenantId)
            ->where('user_id', $notifiable->getKey())
            ->where('status', 'active')
            ->whereNotNull('wa_id')
            ->first();

        if (! $binding) {
            return; // Usuário sem WhatsApp vinculado — descarta silenciosamente
        }

        $text = $notification->toWhatsapp($notifiable); // @phpstan-ignore-line guaranteed by instanceof above

        if (empty(trim($text))) {
            return;
        }

        $this->sendTextMessage($binding->wa_id, $text);
    }

    private function sendTextMessage(string $to, string $text): void
    {
        $accessToken  = config('services.meta.token');
        $phoneNumberId = config('services.meta.phone_id');
        $apiUrl       = 'https://graph.facebook.com/v21.0';

        if (! $accessToken || ! $phoneNumberId) {
            Log::warning('[WhatsappChannel] Credenciais Meta não configuradas (services.meta.token / services.meta.phone_id).');
            return;
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("{$apiUrl}/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type'    => 'individual',
                    'to'                => $to,
                    'type'              => 'text',
                    'text'              => [
                        'preview_url' => false,
                        'body'        => $text,
                    ],
                ]);

            if ($response->successful()) {
                Log::info("[WhatsappChannel] Mensagem enviada para {$to}.");
            } else {
                Log::error("[WhatsappChannel] Erro ao enviar para {$to}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("[WhatsappChannel] Exceção ao enviar para {$to}: " . $e->getMessage());
        }
    }
}
