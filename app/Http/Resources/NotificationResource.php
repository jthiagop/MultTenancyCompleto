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
        $data = $this->resource->data;
        $tipo = $data['tipo'] ?? 'geral';
        $iconInfo = self::ICON_MAP[$tipo] ?? self::ICON_DEFAULT;
        $categoria = $data['categoria'] ?? ($iconInfo['categoria'] ?? 'geral');

        // Triggered by — resolve user name/avatar
        $triggeredBy = $this->resolveTriggeredBy($data['triggered_by'] ?? null);

        // Expiração
        $expiresAt = $data['expires_at'] ?? null;
        $expirationInfo = $this->calculateExpiration($expiresAt);

        return [
            'id'              => $this->resource->id,
            'icon'            => $data['icon'] ?? $iconInfo['icon'],
            'color'           => $data['color'] ?? $iconInfo['color'],
            'title'           => $data['title'] ?? 'Notificação',
            'message'         => $data['message'] ?? '',
            'action_url'      => $data['action_url'] ?? null,
            'target'          => $data['target'] ?? '_self',
            'tipo'            => $tipo,
            'categoria'       => $categoria,

            // Metadados do arquivo
            'file_type'       => $data['file_type'] ?? null,
            'file_size'       => $data['file_size'] ?? null,
            'expires_at'      => $expiresAt,
            'expires_in'      => $expirationInfo['text'],
            'expires_percent' => $expirationInfo['percent'],

            // Metadados financeiros (conta vencendo / lançamento / rateio)
            'urgencia'             => $data['urgencia'] ?? null,
            'sub_tipo'             => $data['sub_tipo'] ?? null,
            'acao'                 => $data['acao'] ?? null,
            'transacao_id'         => $data['transacao_id'] ?? null,
            'data_vencimento'      => $data['data_vencimento'] ?? null,
            'data_vencimento_iso'  => $data['data_vencimento_iso'] ?? null,
            'valor'                => isset($data['valor']) ? (float) $data['valor'] : null,
            'nome_matriz'          => $data['nome_matriz'] ?? null,

            // Estado
            'read_at'         => $this->resource->read_at?->toISOString(),
            'created_at'      => $this->resource->created_at->diffForHumans(),
            'created_at_iso'  => $this->resource->created_at->toISOString(),

            // Quem disparou
            'triggered_by'    => $triggeredBy,
        ];
    }

    /**
     * Resolve informações do usuário remetente (quem disparou a ação).
     *
     * Cache por requisição: evita N queries quando várias notificações
     * foram disparadas pelo mesmo usuário.
     */
    private function resolveTriggeredBy(?int $userId): ?array
    {
        if (!$userId) {
            return null;
        }

        /** @var array<int, array{name: string, avatar: string|null}|null> $cache */
        static $cache = [];

        if (array_key_exists($userId, $cache)) {
            return $cache[$userId];
        }

        $user = \App\Models\User::find($userId);

        $cache[$userId] = $user
            ? ['name' => $user->name, 'avatar' => $user->avatar_url]
            : null;

        return $cache[$userId];
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
