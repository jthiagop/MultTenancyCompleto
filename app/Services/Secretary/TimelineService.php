<?php

namespace App\Services\Secretary;

use App\Data\TimelineEventData;
use App\Models\ReligiousMember;
use App\Enums\FormationStageAppearance;
use Illuminate\Support\Collection;

/**
 * Service para agregar e formatar eventos da timeline de membros religiosos
 */
class TimelineService
{
    /**
     * Obtém todos os eventos da timeline de um membro
     */
    public function getTimelineForMember(ReligiousMember $member, array $options = []): Collection
    {
        $events = collect();

        // Opções padrão
        $options = array_merge([
            'include_creation' => true,
            'include_formation' => true,
            'include_transfers' => true,
            'include_dates' => true,
            'include_ministries' => true,
            'include_notes' => true,
            'limit' => null,
            'order' => 'desc',
        ], $options);

        // 1. Evento de criação do registro
        if ($options['include_creation']) {
            $events = $events->merge($this->getCreationEvents($member));
        }

        // 2. Eventos de formação (mudanças de etapa)
        if ($options['include_formation']) {
            $events = $events->merge($this->getFormationEvents($member));
        }

        // 3. Eventos de transferência entre casas
        if ($options['include_transfers']) {
            $events = $events->merge($this->getTransferEvents($member));
        }

        // 4. Datas importantes (profissões, ordenações)
        if ($options['include_dates']) {
            $events = $events->merge($this->getImportantDatesEvents($member));
        }

        // 5. Ministérios recebidos
        if ($options['include_ministries']) {
            $events = $events->merge($this->getMinistryEvents($member));
        }

        // 6. Notas/Observações (se houver modelo de notas)
        if ($options['include_notes']) {
            $events = $events->merge($this->getNotesEvents($member));
        }

        // Ordenar
        $events = $options['order'] === 'desc' 
            ? $events->sortByDesc(fn ($e) => $e->date)
            : $events->sortBy(fn ($e) => $e->date);

        // Limitar se necessário
        if ($options['limit']) {
            $events = $events->take($options['limit']);
        }

        return $events->values();
    }

    /**
     * Evento de criação do registro
     */
    protected function getCreationEvents(ReligiousMember $member): Collection
    {
        $events = collect();

        $events->push(new TimelineEventData(
            type: 'creation',
            title: 'Membro Cadastrado',
            date: $member->created_at,
            description: 'Registro inicial no sistema',
            icon: 'fa-solid fa-user-plus',
            color: 'primary',
            emoji: '✨',
            user: $member->creator ?? null, // Se tiver relação com quem criou
            badges: [
                ['label' => 'Novo Cadastro', 'color' => 'primary']
            ],
            metadata: [
                'registration_number' => $member->order_registration_number,
            ]
        ));

        return $events;
    }

    /**
     * Eventos de mudança de etapa de formação
     */
    protected function getFormationEvents(ReligiousMember $member): Collection
    {
        $events = collect();

        foreach ($member->formationPeriods as $index => $period) {
            $stageInfo = FormationStageAppearance::getAppearance($period->formationStage?->slug);
            
            $badges = [];
            if ($period->is_current) {
                $badges[] = ['label' => 'Atual', 'color' => 'success'];
            }

            // Evento de início da etapa
            $events->push(new TimelineEventData(
                type: 'formation_start',
                title: $period->formationStage?->name ?? 'Etapa de Formação',
                date: $period->start_date,
                description: $this->formatFormationDescription($period),
                icon: $stageInfo['icon'],
                color: $period->is_current ? 'success' : $stageInfo['color'],
                emoji: $stageInfo['emoji'],
                badges: $badges,
                metadata: [
                    'stage_id' => $period->formation_stage_id,
                    'stage_slug' => $period->formationStage?->slug,
                    'company_id' => $period->company_id,
                    'company_name' => $period->company?->name,
                    'place' => $period->place,
                    'period_id' => $period->id,
                ]
            ));

            // Evento de fim da etapa (se houver)
            if ($period->end_date && !$period->is_current) {
                $events->push(new TimelineEventData(
                    type: 'formation_end',
                    title: "Conclusão: {$period->formationStage?->name}",
                    date: $period->end_date,
                    description: "Período encerrado após " . $this->calculateDuration($period->start_date, $period->end_date),
                    icon: 'fa-solid fa-check-circle',
                    color: 'success',
                    emoji: '✅',
                    metadata: [
                        'stage_id' => $period->formation_stage_id,
                        'period_id' => $period->id,
                    ]
                ));
            }
        }

        return $events;
    }

    /**
     * Eventos de transferência entre casas/comunidades
     */
    protected function getTransferEvents(ReligiousMember $member): Collection
    {
        $events = collect();

        // Agrupa períodos por mudança de company
        $periods = $member->formationPeriods()
            ->whereNotNull('company_id')
            ->orderBy('start_date')
            ->get();

        $previousCompany = null;

        foreach ($periods as $period) {
            if ($previousCompany && $previousCompany !== $period->company_id) {
                $events->push(new TimelineEventData(
                    type: 'transfer',
                    title: 'Transferência de Casa',
                    date: $period->start_date,
                    description: "Transferido para {$period->company?->name}",
                    icon: 'fa-solid fa-right-left',
                    color: 'info',
                    emoji: '🏠',
                    badges: [
                        ['label' => 'Transferência', 'color' => 'info']
                    ],
                    metadata: [
                        'from_company_id' => $previousCompany,
                        'to_company_id' => $period->company_id,
                        'to_company_name' => $period->company?->name,
                    ]
                ));
            }
            $previousCompany = $period->company_id;
        }

        return $events;
    }

    /**
     * Eventos de datas importantes (profissões, ordenações)
     */
    protected function getImportantDatesEvents(ReligiousMember $member): Collection
    {
        $events = collect();

        // Profissão Temporária
        if ($member->temporary_profession_date) {
            $events->push(new TimelineEventData(
                type: 'profession_temporary',
                title: 'Profissão Temporária',
                date: $member->temporary_profession_date,
                description: 'Primeira profissão dos votos religiosos',
                icon: 'fa-solid fa-hands-praying',
                color: 'info',
                emoji: '🙏',
                badges: [
                    ['label' => 'Marco Importante', 'color' => 'warning']
                ]
            ));
        }

        // Profissão Perpétua
        if ($member->perpetual_profession_date) {
            $events->push(new TimelineEventData(
                type: 'profession_perpetual',
                title: 'Profissão Perpétua',
                date: $member->perpetual_profession_date,
                description: 'Consagração definitiva à vida religiosa',
                icon: 'fa-solid fa-infinity',
                color: 'danger',
                emoji: '♾️',
                badges: [
                    ['label' => 'Votos Perpétuos', 'color' => 'danger']
                ]
            ));
        }

        // Ordenação Diaconal
        if ($member->diaconal_ordination_date) {
            $events->push(new TimelineEventData(
                type: 'ordination_diaconal',
                title: 'Ordenação Diaconal',
                date: $member->diaconal_ordination_date,
                description: 'Ordenado ao ministério do Diaconato',
                icon: 'fa-solid fa-book-bible',
                color: 'primary',
                emoji: '📖',
                badges: [
                    ['label' => 'Diácono', 'color' => 'primary']
                ]
            ));
        }

        // Ordenação Presbiteral
        if ($member->priestly_ordination_date) {
            $events->push(new TimelineEventData(
                type: 'ordination_priestly',
                title: 'Ordenação Presbiteral',
                date: $member->priestly_ordination_date,
                description: 'Ordenado ao ministério do Presbiterado',
                icon: 'fa-solid fa-cross',
                color: 'warning',
                emoji: '✝️',
                badges: [
                    ['label' => 'Padre', 'color' => 'warning']
                ]
            ));
        }

        // Data de Nascimento (aniversário)
        if ($member->birth_date) {
            $events->push(new TimelineEventData(
                type: 'birth',
                title: 'Nascimento',
                date: $member->birth_date,
                description: 'Data de nascimento do membro',
                icon: 'fa-solid fa-cake-candles',
                color: 'success',
                emoji: '🎂',
                metadata: [
                    'age' => $member->birth_date->age,
                ]
            ));
        }

        return $events;
    }

    /**
     * Eventos de ministérios recebidos
     */
    protected function getMinistryEvents(ReligiousMember $member): Collection
    {
        $events = collect();

        // Configuração de aparência por tipo de ministério
        $ministryAppearance = [
            'leitorado' => [
                'icon' => 'fa-solid fa-book-open-reader',
                'color' => 'info',
                'emoji' => '📖',
            ],
            'acolitato' => [
                'icon' => 'fa-solid fa-fire',
                'color' => 'warning',
                'emoji' => '🕯️',
            ],
            'diaconato' => [
                'icon' => 'fa-solid fa-hands-helping',
                'color' => 'primary',
                'emoji' => '🙌',
            ],
            'presbiterato' => [
                'icon' => 'fa-solid fa-cross',
                'color' => 'danger',
                'emoji' => '✝️',
            ],
            'episcopado' => [
                'icon' => 'fa-solid fa-crown',
                'color' => 'purple',
                'emoji' => '👑',
            ],
        ];

        $defaultAppearance = [
            'icon' => 'fa-solid fa-hands-praying',
            'color' => 'secondary',
            'emoji' => '🙏',
        ];

        foreach ($member->ministries as $ministry) {
            $slug = $ministry->type?->slug ?? 'default';
            $appearance = $ministryAppearance[$slug] ?? $defaultAppearance;

            $description = [];
            if ($ministry->minister_name) {
                $description[] = "Ministro: {$ministry->minister_name}";
            }
            if ($ministry->diocese_name) {
                $description[] = "Diocese: {$ministry->diocese_name}";
            }
            if ($ministry->notes) {
                $description[] = $ministry->notes;
            }

            $events->push(new TimelineEventData(
                type: 'ministry',
                title: $ministry->type?->name ?? 'Ministério',
                date: $ministry->date,
                description: implode(' • ', $description) ?: 'Ministério conferido',
                icon: $appearance['icon'],
                color: $appearance['color'],
                emoji: $appearance['emoji'],
                badges: [
                    ['label' => 'Ministério', 'color' => $appearance['color']]
                ],
                metadata: [
                    'ministry_id' => $ministry->id,
                    'ministry_type_id' => $ministry->ministry_type_id,
                    'ministry_type_slug' => $slug,
                    'minister_name' => $ministry->minister_name,
                    'diocese_name' => $ministry->diocese_name,
                ]
            ));
        }

        return $events;
    }

    /**
     * Eventos de notas/observações (se o modelo existir)
     */
    protected function getNotesEvents(ReligiousMember $member): Collection
    {
        $events = collect();

        // Se houver um modelo de notas relacionado
        if (method_exists($member, 'publicNotes') && $member->publicNotes->count() > 0) {
            foreach ($member->publicNotes as $note) {
                $events->push(new TimelineEventData(
                    type: 'note',
                    title: $note->title ?? $note->type_name,
                    date: $note->created_at,
                    description: \Str::limit($note->content, 150),
                    icon: $note->type_icon,
                    color: $note->type_color,
                    emoji: '📝',
                    user: $note->author,
                    badges: [
                        ['label' => $note->type_name, 'color' => $note->type_color]
                    ],
                    metadata: [
                        'note_id' => $note->id,
                        'note_type' => $note->type,
                        'full_content' => $note->content,
                    ]
                ));
            }
        }

        // Se houver observações no próprio membro (campo texto simples)
        if ($member->observacoes && $member->updated_at && $member->publicNotes->count() === 0) {
            $events->push(new TimelineEventData(
                type: 'observation',
                title: 'Observações Registradas',
                date: $member->updated_at,
                description: \Str::limit($member->observacoes, 150),
                icon: 'fa-solid fa-comment-dots',
                color: 'secondary',
                emoji: '💬',
            ));
        }

        return $events;
    }

    /**
     * Formata descrição do período de formação
     */
    protected function formatFormationDescription($period): string
    {
        $parts = [];

        if ($period->company) {
            $parts[] = "Local: {$period->company->name}";
        } elseif ($period->place_text) {
            $parts[] = "Local: {$period->place_text}";
        }

        if ($period->notes) {
            $parts[] = \Str::limit($period->notes, 100);
        }

        return implode(' • ', $parts) ?: 'Início do período de formação';
    }

    /**
     * Calcula duração entre duas datas
     */
    protected function calculateDuration($start, $end): string
    {
        $diff = $start->diff($end);
        
        $parts = [];
        if ($diff->y > 0) {
            $parts[] = $diff->y . ' ' . ($diff->y === 1 ? 'ano' : 'anos');
        }
        if ($diff->m > 0) {
            $parts[] = $diff->m . ' ' . ($diff->m === 1 ? 'mês' : 'meses');
        }
        if (empty($parts) && $diff->d > 0) {
            $parts[] = $diff->d . ' ' . ($diff->d === 1 ? 'dia' : 'dias');
        }

        return implode(' e ', $parts) ?: 'menos de um dia';
    }

    /**
     * Obtém estatísticas resumidas da timeline
     */
    public function getTimelineStats(ReligiousMember $member): array
    {
        $events = $this->getTimelineForMember($member);
        
        return [
            'total_events' => $events->count(),
            'formation_stages' => $member->formationPeriods->count(),
            'years_in_formation' => $this->calculateYearsInFormation($member),
            'current_stage' => $member->currentStage?->name ?? 'Não definido',
            'has_perpetual_vows' => $member->perpetual_profession_date !== null,
            'is_ordained' => $member->priestly_ordination_date !== null || $member->diaconal_ordination_date !== null,
            'event_types' => $events->groupBy('type')->map->count(),
        ];
    }

    /**
     * Calcula anos em formação
     */
    protected function calculateYearsInFormation(ReligiousMember $member): float
    {
        $firstPeriod = $member->formationPeriods()->orderBy('start_date')->first();
        
        if (!$firstPeriod) {
            return 0;
        }

        $start = $firstPeriod->start_date;
        $end = now();

        return round($start->diffInYears($end), 1);
    }
}
