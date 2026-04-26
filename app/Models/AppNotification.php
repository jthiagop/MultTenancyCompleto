<?php

namespace App\Models;

use App\Observers\AppNotificationObserver;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

/**
 * Model Eloquent customizado para a tabela `notifications`.
 *
 * Estende {@see DatabaseNotification} (do Laravel) e adiciona:
 *
 *  - cast nativo de `meta` para array;
 *  - sincronização automática de colunas físicas (company_id, title,
 *    message, channel, meta) a partir do payload `data` no momento da
 *    criação. Assim NENHUMA classe Notification precisa ser reescrita
 *    para popular as colunas novas.
 *
 * O front-end e os filtros de listagem podem (e devem) ler diretamente
 * de `company_id`, `title`, `message` etc. — sem mais JSON_EXTRACT.
 *
 * Possui {@see AppNotificationObserver} acoplado: cada create/update/delete
 * dispara {@see \App\Events\NotificationCountChanged} via broadcast para
 * atualizar o badge do React em tempo real.
 */
#[ObservedBy([AppNotificationObserver::class])]
class AppNotification extends DatabaseNotification
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'notifications';

    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'company_id',
        'title',
        'message',
        'channel',
        'data',
        'meta',
        'read_at',
        'sent_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'meta'    => 'array',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Sincroniza as colunas físicas a partir do payload em `data`
     * antes de persistir. Mantém compatibilidade total com o Laravel
     * Notification core (que só conhece `data`).
     */
    protected static function booted(): void
    {
        static::creating(function (self $notification): void {
            $data = $notification->data;

            if (! is_array($data)) {
                $decoded = is_string($data) ? json_decode($data, true) : null;
                $data = is_array($decoded) ? $decoded : [];
            }

            if ($notification->company_id === null && isset($data['company_id'])) {
                $notification->company_id = (int) $data['company_id'] ?: null;
            }

            if (empty($notification->title) && isset($data['title'])) {
                $notification->title = mb_substr((string) $data['title'], 0, 191);
            }

            if (empty($notification->message) && isset($data['message'])) {
                $notification->message = (string) $data['message'];
            }

            if (empty($notification->channel)) {
                // Sempre cria pelo canal database; se a Notification dispatchar também
                // por whatsapp/email, o registro continua sendo "app" do ponto de vista
                // do banco — o canal externo é registrado em notifications_log.
                $notification->channel = 'app';
            }

            if (empty($notification->meta)) {
                // O array `meta` recebe os campos do payload que não são "primários".
                // Mantemos `data` intacto para compat. backward; `meta` é fonte canônica
                // para novos consumidores.
                $notification->meta = collect($data)
                    ->except(['title', 'message', 'company_id'])
                    ->all();
            }

            if ($notification->sent_at === null) {
                $notification->sent_at = now();
            }
        });
    }

    /**
     * Escopo: notificações de uma empresa (lê coluna física, sem JSON_EXTRACT).
     */
    public function scopeForCompany($query, ?int $companyId)
    {
        if (! $companyId) {
            return $query;
        }

        // Para a janela de transição mantemos `orWhereNull` apenas em registros
        // antigos (criados antes da migration). Após o backfill, esse fallback
        // deve ser removido.
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)
              ->orWhereNull('company_id');
        });
    }

    /**
     * Escopo: notificações cujo expires_at já passou são removidas da listagem.
     * Lê de meta (JSON nativo) ou de data (legado), nessa ordem.
     */
    public function scopeNotExpired($query)
    {
        $now = now()->toIso8601String();

        return $query->where(function ($q) use ($now) {
            $q->whereRaw("(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta, data), '$.expires_at')) IS NULL"
                . " OR JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta, data), '$.expires_at')) >= ?)", [$now]);
        });
    }
}
