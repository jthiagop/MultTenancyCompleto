{{--
    Componente Timeline Item
    
    Uso com TimelineEventData:
    <x-timeline.item :event="$event" />
    
    Uso manual com props:
    <x-timeline.item 
        type="creation"
        title="Membro Cadastrado"
        :date="$member->created_at"
        description="Registro inicial"
        icon="fa-solid fa-user-plus"
        color="primary"
        emoji="✨"
    />
--}}

@props([
    'event' => null,
    'type' => 'generic',
    'title' => 'Evento',
    'date' => null,
    'time' => null,
    'description' => null,
    'icon' => 'fa-solid fa-circle',
    'color' => 'secondary',
    'emoji' => null,
    'user' => null,
    'badges' => [],
    'files' => [],
    'metadata' => [],
    'showRelativeTime' => true,
    'showDate' => true,
    'compact' => false,
])

@php
    // Se um evento foi passado, extrair as propriedades
    if ($event) {
        if ($event instanceof \App\Data\TimelineEventData) {
            $type = $event->type;
            $title = $event->title;
            $date = $event->date;
            $description = $event->description;
            $icon = $event->icon;
            $color = $event->color;
            $emoji = $event->emoji;
            $user = $event->user;
            $badges = $event->badges;
            $files = $event->files;
            $metadata = $event->metadata;
        } elseif (is_array($event)) {
            $type = $event['type'] ?? $type;
            $title = $event['title'] ?? $title;
            $date = isset($event['date']) ? \Carbon\Carbon::parse($event['date']) : $date;
            $description = $event['description'] ?? $description;
            $icon = $event['icon'] ?? $icon;
            $color = $event['color'] ?? $color;
            $emoji = $event['emoji'] ?? $emoji;
            $user = $event['user'] ?? $user;
            $badges = $event['badges'] ?? $badges;
            $files = $event['files'] ?? $files;
            $metadata = $event['metadata'] ?? $metadata;
        }
    }
    
    $formattedDate = $date ? $date->format('d/m/Y') : '';
    $formattedTime = $date ? $date->format('H:i') : '';
    $relativeTime = $date ? $date->diffForHumans() : '';
@endphp

<div class="timeline-item" data-type="{{ $type }}">
    <!--begin::Timeline line-->
    <div class="timeline-line w-40px"></div>
    <!--end::Timeline line-->

    <!--begin::Timeline icon-->
    <div class="timeline-icon symbol symbol-circle symbol-40px">
        <div class="symbol-label bg-light-{{ $color }}">
            @if($emoji)
                <span class="fs-4">{{ $emoji }}</span>
            @else
                <i class="{{ $icon }} text-{{ $color }}"></i>
            @endif
        </div>
    </div>
    <!--end::Timeline icon-->

    <!--begin::Timeline content-->
    <div class="timeline-content {{ $compact ? 'mb-6' : 'mb-10' }} mt-n1">
        <!--begin::Header-->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Emoji + Título --}}
                <span class="fs-5 fw-bold text-gray-800">
                    @if($emoji)
                        <span class="me-1">{{ $emoji }}</span>
                    @endif
                    {{ $title }}
                </span>
                
                {{-- Badges --}}
                @foreach($badges as $badge)
                    <span class="badge badge-light-{{ $badge['color'] ?? 'secondary' }} fs-8">
                        {{ $badge['label'] }}
                    </span>
                @endforeach
            </div>
            
            {{-- Data/Hora --}}
            @if($showDate && $date)
                <div class="d-flex flex-column align-items-end text-end">
                    <span class="text-gray-600 fw-semibold fs-7">{{ $formattedDate }}</span>
                    @if($showRelativeTime)
                        <span class="text-gray-400 fs-8">{{ $relativeTime }}</span>
                    @endif
                </div>
            @endif
        </div>
        <!--end::Header-->

        {{-- Descrição --}}
        @if($description)
            <div class="text-gray-600 fs-7 mb-3">
                {{ $description }}
            </div>
        @endif

        {{-- Usuário e Metadados --}}
        @if($user || !empty($files))
            <div class="d-flex flex-wrap align-items-center gap-3 mt-3">
                {{-- Autor/Usuário --}}
                @if($user)
                    <div class="d-flex align-items-center gap-2">
                        <div class="symbol symbol-25px symbol-circle" data-bs-toggle="tooltip" title="Por: {{ is_array($user) ? $user['name'] : $user->name }}">
                            @php
                                $userName = is_array($user) ? $user['name'] : $user->name;
                                $userAvatar = is_array($user) ? ($user['avatar'] ?? null) : ($user->avatar ?? null);
                            @endphp
                            @if($userAvatar)
                                <img src="{{ route('file', ['path' => $userAvatar]) }}" alt="{{ $userName }}">
                            @else
                                <span class="symbol-label bg-light-{{ $color }} text-{{ $color }} fs-8 fw-bold">
                                    {{ substr($userName, 0, 1) }}
                                </span>
                            @endif
                        </div>
                        <span class="text-gray-500 fs-8">{{ $userName }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Arquivos anexados --}}
        @if(!empty($files))
            <div class="d-flex flex-wrap gap-2 mt-3">
                @foreach($files as $file)
                    <div class="d-flex align-items-center border border-dashed border-gray-300 rounded p-2">
                        <i class="fa-solid fa-file text-primary fs-6 me-2"></i>
                        <a href="{{ $file['url'] ?? '#' }}" class="text-gray-800 text-hover-primary fs-7 fw-bold" target="_blank">
                            {{ $file['name'] ?? 'Arquivo' }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Slot para conteúdo customizado --}}
        {{ $slot ?? '' }}
    </div>
    <!--end::Timeline content-->
</div>
