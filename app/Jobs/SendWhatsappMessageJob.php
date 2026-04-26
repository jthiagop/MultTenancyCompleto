<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Envia uma mensagem de texto via Meta WhatsApp Business API em background.
 *
 * Antes da Onda 3 o WhatsappChannel chamava o Http::post síncronamente —
 * o que travava a request HTTP do usuário (até 10s) e perdia o histórico
 * em caso de falha. Agora:
 *
 *  - O canal apenas dispatcha este job (resposta imediata ao usuário).
 *  - O job realiza retry exponencial (3 tentativas).
 *  - Cada envio é registrado em `notifications_log` com status sent/failed.
 */
class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 30;
    /** Backoff progressivo (segundos) entre as 3 tentativas */
    public array $backoff = [30, 120, 300];

    public function __construct(
        public readonly string  $tenantId,
        public readonly ?int    $userId,
        public readonly ?int    $companyId,
        public readonly ?string $notificationId,
        public readonly string  $waId,
        public readonly string  $text,
        /**
         * Tipo do destinatário do envio. Default 'user' (canal WhatsappChannel
         * de notificação Laravel). 'company_contact' indica envio para um
         * contato avulso do "Grupo WhatsApp" da empresa (ver
         * NotificacaoFinanceiraService::notificarGrupoWhatsapp).
         * Auditável via `meta.kind` em notifications_log.
         */
        public readonly string  $kind = 'user',

        /**
         * Quando definido, envia uma mensagem do tipo `template` (HSM
         * aprovado pela Meta) em vez de texto livre. O `text` continua
         * sendo persistido como `payload_excerpt` no log para auditoria
         * humana.
         *
         * Estrutura esperada (compatível com a Cloud API v21):
         *   [
         *     'name'       => 'lancamento_agendado_aviso',
         *     'language'   => 'pt_BR',
         *     'components' => [
         *       ['type' => 'body', 'parameters' => [
         *         ['type' => 'text', 'text' => 'pagamento'],
         *         ...
         *       ]],
         *     ],
         *   ]
         *
         * Quando null, cai no fluxo legado de texto livre (entrega só
         * funciona dentro da janela de 24h da Meta).
         *
         * @var array<string,mixed>|null
         */
        public readonly ?array  $template = null,
    ) {}

    public function handle(): void
    {
        $accessToken   = config('services.meta.token');
        $phoneNumberId = config('services.meta.phone_id');
        $apiUrl        = 'https://graph.facebook.com/v21.0';

        if (! $accessToken || ! $phoneNumberId) {
            $this->logResult('skipped', null, 'Credenciais Meta não configuradas (services.meta.token / services.meta.phone_id).');
            return;
        }

        // Monta payload conforme modo de envio: template (HSM) ou texto livre.
        $sendMode = $this->template ? 'template' : 'text';
        $payload  = $this->buildPayload();

        try {
            $response = Http::withToken($accessToken)
                ->timeout(15)
                ->post("{$apiUrl}/{$phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $body = $response->json();
                $providerId = $body['messages'][0]['id'] ?? null;
                $this->logResult('sent', $providerId, null, [
                    'send_mode'     => $sendMode,
                    'template_name' => $this->template['name'] ?? null,
                    'response'      => $body,
                ]);
                Log::info("[SendWhatsappMessageJob] Mensagem enviada para {$this->waId}.", [
                    'wamid'     => $providerId,
                    'send_mode' => $sendMode,
                    'template'  => $this->template['name'] ?? null,
                ]);
                return;
            }

            $error = $response->body();
            $this->logResult('failed', null, $error, [
                'send_mode'     => $sendMode,
                'template_name' => $this->template['name'] ?? null,
                'status'        => $response->status(),
            ]);

            // Falhas 4xx (cliente) não devem ser retentadas.
            if ($response->clientError()) {
                Log::warning("[SendWhatsappMessageJob] Erro 4xx (sem retry) para {$this->waId}: {$error}", [
                    'send_mode' => $sendMode,
                    'template'  => $this->template['name'] ?? null,
                ]);
                return;
            }

            throw new \RuntimeException("Erro Meta API: {$error}");
        } catch (\Throwable $e) {
            $this->logResult('failed', null, $e->getMessage(), [
                'send_mode'     => $sendMode,
                'template_name' => $this->template['name'] ?? null,
            ]);
            throw $e;
        }
    }

    /**
     * Constroi o payload da Cloud API conforme o modo:
     *   - template (HSM): entrega mesmo fora da janela de 24h.
     *   - text         : texto livre, só entrega dentro da janela.
     *
     * @return array<string,mixed>
     */
    private function buildPayload(): array
    {
        $base = [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $this->waId,
        ];

        if ($this->template !== null) {
            $template = [
                'name'     => $this->template['name'],
                'language' => [
                    'code' => $this->template['language'] ?? config('services.meta.whatsapp_template_language', 'pt_BR'),
                ],
            ];

            // Components são opcionais (templates sem variáveis não precisam).
            if (! empty($this->template['components']) && is_array($this->template['components'])) {
                $template['components'] = $this->template['components'];
            }

            return $base + [
                'type'     => 'template',
                'template' => $template,
            ];
        }

        return $base + [
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body'        => $this->text,
            ],
        ];
    }

    public function failed(\Throwable $exception): void
    {
        $this->logResult('failed', null, $exception->getMessage(), ['final' => true]);

        Log::error('[SendWhatsappMessageJob] Falha definitiva ao enviar WhatsApp', [
            'tenant_id'      => $this->tenantId,
            'user_id'        => $this->userId,
            'wa_id'          => $this->waId,
            'notification_id'=> $this->notificationId,
            'error'          => $exception->getMessage(),
        ]);
    }

    /**
     * Persiste o resultado em `notifications_log` (tabela tenant). Garante o
     * tenant inicializado para escrever no banco correto.
     */
    private function logResult(string $status, ?string $providerId, ?string $error, array $meta = []): void
    {
        try {
            if (! tenancy()->initialized) {
                $tenant = Tenant::find($this->tenantId);
                if ($tenant) {
                    tenancy()->initialize($tenant);
                }
            }

            DB::table('notifications_log')->insert([
                'notification_id' => $this->notificationId,
                'user_id'         => $this->userId,
                'company_id'      => $this->companyId,
                'channel'         => 'whatsapp',
                'status'          => $status,
                'provider_id'     => $providerId,
                'payload_excerpt' => mb_substr($this->text, 0, 280),
                'error'           => $error ? mb_substr($error, 0, 2000) : null,
                'meta'            => json_encode(
                    array_merge(['kind' => $this->kind], $meta),
                    JSON_UNESCAPED_UNICODE,
                ),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        } catch (\Throwable $e) {
            // Falha em logar não pode quebrar o envio. Apenas registra no log padrão.
            Log::warning('[SendWhatsappMessageJob] Falha ao persistir notifications_log', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
