<?php

namespace App\Data;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Data Transfer Object para eventos da timeline
 */
class TimelineEventData implements Arrayable
{
    public function __construct(
        public readonly string $type,
        public readonly string $title,
        public readonly Carbon $date,
        public readonly ?string $description = null,
        public readonly string $icon = 'fa-solid fa-circle',
        public readonly string $color = 'secondary',
        public readonly ?string $emoji = null,
        public readonly ?User $user = null,
        public readonly array $badges = [],
        public readonly array $files = [],
        public readonly array $metadata = [],
    ) {}

    /**
     * Cria evento a partir de array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'] ?? 'generic',
            title: $data['title'] ?? 'Evento',
            date: $data['date'] instanceof Carbon ? $data['date'] : Carbon::parse($data['date']),
            description: $data['description'] ?? null,
            icon: $data['icon'] ?? 'fa-solid fa-circle',
            color: $data['color'] ?? 'secondary',
            emoji: $data['emoji'] ?? null,
            user: $data['user'] ?? null,
            badges: $data['badges'] ?? [],
            files: $data['files'] ?? [],
            metadata: $data['metadata'] ?? [],
        );
    }

    /**
     * Formata a data para exibição
     */
    public function formattedDate(): string
    {
        return $this->date->format('d/m/Y');
    }

    /**
     * Formata a hora para exibição
     */
    public function formattedTime(): string
    {
        return $this->date->format('H:i');
    }

    /**
     * Retorna o tempo relativo (ex: "há 2 dias")
     */
    public function relativeTime(): string
    {
        return $this->date->diffForHumans();
    }

    /**
     * Converte para array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'date' => $this->date->toIso8601String(),
            'formatted_date' => $this->formattedDate(),
            'formatted_time' => $this->formattedTime(),
            'relative_time' => $this->relativeTime(),
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'emoji' => $this->emoji,
            'user' => $this->user?->only(['id', 'name', 'avatar']),
            'badges' => $this->badges,
            'files' => $this->files,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * Cria coleção de eventos ordenada por data
     */
    public static function collection(array $events, string $order = 'desc'): Collection
    {
        $collection = collect($events)->map(function ($event) {
            return $event instanceof self ? $event : self::fromArray($event);
        });

        return $order === 'desc' 
            ? $collection->sortByDesc('date')->values()
            : $collection->sortBy('date')->values();
    }
}
