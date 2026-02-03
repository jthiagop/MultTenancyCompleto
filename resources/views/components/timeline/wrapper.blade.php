{{--
    Componente Timeline Wrapper
    
    Uso:
    <x-timeline.wrapper>
        @foreach($events as $event)
            <x-timeline.item :event="$event" />
        @endforeach
    </x-timeline.wrapper>
    
    Ou com collection:
    <x-timeline.wrapper :events="$events" />
--}}

@props([
    'events' => null,
    'title' => null,
    'icon' => 'fa-solid fa-clock-rotate-left',
    'emptyMessage' => 'Nenhum evento registrado',
    'emptyIcon' => 'fa-solid fa-inbox',
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'card ' . $class]) }}>
    @if($title)
        <div class="card-header border-0">
            <h3 class="card-title fw-bold text-gray-800">
                <i class="{{ $icon }} me-2 text-primary"></i>
                {{ $title }}
            </h3>
        </div>
    @endif
    
    <div class="card-body {{ $title ? 'pt-0' : '' }}">
        @if($events && $events->count() > 0)
            <div class="timeline">
                @foreach($events as $event)
                    <x-timeline.item :event="$event" />
                @endforeach
            </div>
        @elseif($slot->isNotEmpty())
            <div class="timeline">
                {{ $slot }}
            </div>
        @else
            <div class="text-center text-gray-500 py-10">
                <i class="{{ $emptyIcon }} fs-2x mb-3 d-block"></i>
                {{ $emptyMessage }}
            </div>
        @endif
    </div>
</div>
