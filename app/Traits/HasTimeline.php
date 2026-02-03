<?php

namespace App\Traits;

use App\Data\TimelineEventData;
use App\Services\Secretary\TimelineService;
use Illuminate\Support\Collection;

/**
 * Trait para adicionar funcionalidades de timeline a models
 * 
 * Adicione ao seu model:
 * use App\Traits\HasTimeline;
 * 
 * Depois use:
 * $member->getTimeline()
 * $member->getTimelineStats()
 */
trait HasTimeline
{
    /**
     * Obtém a timeline completa do model
     */
    public function getTimeline(array $options = []): Collection
    {
        $service = app(TimelineService::class);
        
        return $service->getTimelineForMember($this, $options);
    }

    /**
     * Obtém as estatísticas da timeline
     */
    public function getTimelineStats(): array
    {
        $service = app(TimelineService::class);
        
        return $service->getTimelineStats($this);
    }

    /**
     * Obtém eventos da timeline por tipo
     */
    public function getTimelineByType(string $type): Collection
    {
        return $this->getTimeline()->filter(fn ($event) => $event->type === $type);
    }

    /**
     * Obtém os últimos N eventos da timeline
     */
    public function getRecentTimelineEvents(int $limit = 5): Collection
    {
        return $this->getTimeline(['limit' => $limit]);
    }

    /**
     * Verifica se tem eventos de um determinado tipo
     */
    public function hasTimelineEventType(string $type): bool
    {
        return $this->getTimelineByType($type)->isNotEmpty();
    }
}
