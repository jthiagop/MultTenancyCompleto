<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class NotificationResource extends JsonResource
{
    /**
     * Mapa de ícones por tipo de notificação.
     */
    private const ICON_MAP = [
        'relatorio_gerado'      => ['icon' => 'fa-solid fa-file-pdf',              'color' => 'danger',  'categoria' => 'sistema'],
        'conta_vencendo'        => ['icon' => 'fa-solid fa-clock',                 'color' => 'warning', 'categoria' => 'financeiro'],
        'aviso_sistema'         => ['icon' => 'fa-solid fa-circle-info',           'color' => 'info',    'categoria' => 'sistema'],
        'lancamento_financeiro' => ['icon' => 'fa-solid fa-money-bill-wave',       'color' => 'success', 'categoria' => 'financeiro'],
        'repasse_criado'        => ['icon' => 'fa-solid fa-arrow-right-arrow-left','color' => 'primary', 'categoria' => 'financeiro'],
        'rateio_recebido'       => ['icon' => 'fa-solid fa-code-branch',           'color' => 'warning', 'categoria' => 'financeiro'],
    ];

    /**
     * Fallback para tipos não mapeados.
     */
    private const ICON_DEFAULT = ['icon' => 'fa-solid fa-bell', 'color' => 'primary'];

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Fonte canônica: meta (JSON nativo após Onda 2). Fallback para data
        // (TEXT legado) quando o registro ainda não foi backfilled.
        $meta = $this->resolveMeta();
        $data = $this->resource->data ?? [];
        if (! is_array($data)) {
            $decoded = is_string($data) ? json_decode($data, true) : null;
            $data = is_array($decoded) ? $decoded : [];
        }
        $payload = array_merge($data, $meta);

        $tipo = $payload['tipo'] ?? 'geral';
        $iconInfo = self::ICON_MAP[$tipo] ?? self::ICON_DEFAULT;
        $categoria = $payload['categoria'] ?? ($iconInfo['categoria'] ?? 'geral');

        $triggeredBy = $this->resolveTriggeredBy($payload['triggered_by'] ?? null);

        $expiresAt = $payload['expires_at'] ?? null;
        $expirationInfo = $this->calculateExpiration($expiresAt);

        // Prefere colunas físicas quando presentes (sem JSON_EXTRACT no front).
        $title   = $this->resource->title   ?? $payload['title']   ?? 'Notificação';
        $message = $this->resource->message ?? $payload['message'] ?? '';

        return [
            'id'              => $this->resource->id,
            'icon'            => $payload['icon'] ?? $iconInfo['icon'],
            'color'           => $payload['color'] ?? $iconInfo['color'],
            'title'           => $title,
            'message'         => $message,
            'action_url'      => $payload['action_url'] ?? null,
            'target'          => $payload['target'] ?? '_self',
            'tipo'            => $tipo,
            'categoria'       => $categoria,
            'channel'         => $this->resource->channel ?? 'app',

            // Metadados do arquivo
            'file_type'       => $payload['file_type'] ?? null,
            'file_size'       => $payload['file_size'] ?? null,
            'expires_at'      => $expiresAt,
            'expires_in'      => $expirationInfo['text'],
            'expires_percent' => $expirationInfo['percent'],

            // Metadados financeiros (conta vencendo / lançamento / rateio)
            'urgencia'             => $payload['urgencia'] ?? null,
            'sub_tipo'             => $payload['sub_tipo'] ?? null,
            'acao'                 => $payload['acao'] ?? null,
            'transacao_id'         => $payload['transacao_id'] ?? null,
            'data_vencimento'      => $payload['data_vencimento'] ?? null,
            'data_vencimento_iso'  => $payload['data_vencimento_iso'] ?? null,
            'valor'                => isset($payload['valor']) ? (float) $payload['valor'] : null,
            'nome_matriz'          => $payload['nome_matriz'] ?? null,

            // Estado
            'read_at'         => $this->resource->read_at?->toISOString(),
            'sent_at'         => isset($this->resource->sent_at) ? $this->resource->sent_at?->toISOString() : null,
            'created_at'      => $this->resource->created_at->diffForHumans(),
            'created_at_iso'  => $this->resource->created_at->toISOString(),

            // Quem disparou
            'triggered_by'    => $triggeredBy,
        ];
    }

    /**
     * Resolve o array `meta` lidando com colunas físicas, casts e legado.
     */
    private function resolveMeta(): array
    {
        $meta = $this->resource->meta ?? null;

        if (is_array($meta)) {
            return $meta;
        }

        if (is_string($meta) && $meta !== '') {
            $decoded = json_decode($meta, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Cache por request armazenado em container (evita "static" que vaza
     * entre requests no octane/swoole/long-running workers).
     */
    private function resolveTriggeredBy(?int $userId): ?array
    {
        if (!$userId) {
            return null;
        }

        $cache = app('notification.triggered_by_cache', []);

        if (! ($cache instanceof \ArrayAccess) && ! is_array($cache)) {
            $cache = [];
        }

        if (isset($cache[$userId])) {
            return $cache[$userId] ?: null;
        }

        // Permite que o controller pré-popule o cache via NotificationResource::primeTriggeredByCache().
        // Caso a notificação tenha vindo isolada, fazemos a busca pontual.
        $user = \App\Models\User::find($userId);
        $resolved = $user ? ['name' => $user->name, 'avatar' => $user->avatar_url] : null;

        if (is_array($cache)) {
            $cache[$userId] = $resolved;
            app()->instance('notification.triggered_by_cache', $cache);
        }

        return $resolved;
    }

    /**
     * Pré-popula o cache de triggered_by com uma única query batch a partir
     * de uma lista de notificações. Chamado pelo NotificationController
     * antes de transformar a coleção em JSON.
     *
     * @param iterable<\Illuminate\Notifications\DatabaseNotification> $notifications
     */
    public static function primeTriggeredByCache(iterable $notifications): void
    {
        $userIds = [];
        foreach ($notifications as $n) {
            // Fonte canônica: meta (após Onda 2). Fallback para data legado.
            $meta = $n->meta ?? null;
            if (is_string($meta)) {
                $decoded = json_decode($meta, true);
                $meta = is_array($decoded) ? $decoded : null;
            }
            if (! is_array($meta)) {
                $meta = [];
            }

            $data = $n->data ?? [];
            if (! is_array($data)) {
                $decoded = is_string($data) ? json_decode($data, true) : null;
                $data = is_array($decoded) ? $decoded : [];
            }

            $uid = $meta['triggered_by'] ?? $data['triggered_by'] ?? null;
            if ($uid) {
                $userIds[(int) $uid] = true;
            }
        }

        if (empty($userIds)) {
            app()->instance('notification.triggered_by_cache', []);
            return;
        }

        // `avatar` é necessário para o accessor `avatar_url` resolver o path
        // físico da foto via /file/. Sem ele a foto do remetente nunca aparece
        // no front (caía só no fallback de iniciais).
        $users = \App\Models\User::whereIn('id', array_keys($userIds))
            ->get(['id', 'name', 'email', 'avatar']);

        $cache = [];
        foreach ($users as $user) {
            $cache[$user->id] = ['name' => $user->name, 'avatar' => $user->avatar_url];
        }

        app()->instance('notification.triggered_by_cache', $cache);
    }

    /**
     * Calcula informações de expiração a partir de expires_at.
     * Retorna texto legível e percentual de progresso (100 = recém-criado, 0 = expirado).
     */
    private function calculateExpiration(?string $expiresAt): array
    {
        if (!$expiresAt) {
            return ['text' => null, 'percent' => null];
        }

        $now = Carbon::now();
        $expires = Carbon::parse($expiresAt);

        if ($expires->isPast()) {
            return ['text' => 'Expirado', 'percent' => 0];
        }

        $diffInHours = (int) $now->diffInHours($expires, false);
        $diffInDays = (int) $now->diffInDays($expires, false);

        // Texto legível
        if ($diffInDays >= 2) {
            $text = "Expira em {$diffInDays} dias";
        } elseif ($diffInDays >= 1) {
            $text = "Expira amanhã";
        } elseif ($diffInHours >= 1) {
            $text = "Expira em {$diffInHours}h";
        } else {
            $text = "Expira em breve";
        }

        // Percentual — supõe 5 dias (120h) de vida útil total
        $totalLifeHours = 120;
        $percent = min(100, max(0, round(($diffInHours / $totalLifeHours) * 100)));

        return ['text' => $text, 'percent' => (int) $percent];
    }
}
