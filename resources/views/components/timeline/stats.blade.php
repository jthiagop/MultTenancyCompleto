{{--
    Componente Timeline Stats (Resumo/Estatísticas)
    
    Uso:
    <x-timeline.stats :member="$member" :stats="$timelineStats" />
--}}

@props([
    'stats' => [],
    'member' => null,
])

@php
    $totalEvents = $stats['total_events'] ?? 0;
    $formationStages = $stats['formation_stages'] ?? 0;
    $yearsInFormation = $stats['years_in_formation'] ?? 0;
    $currentStage = $stats['current_stage'] ?? 'Não definido';
    $hasPerpetualVows = $stats['has_perpetual_vows'] ?? false;
    $isOrdained = $stats['is_ordained'] ?? false;
@endphp

<div {{ $attributes->merge(['class' => 'row g-5 g-xl-8']) }}>
    {{-- Total de Eventos --}}
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-primary hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <span class="svg-icon svg-icon-primary svg-icon-3x ms-n1">
                    <i class="fa-solid fa-timeline text-primary fs-2x"></i>
                </span>
                <div class="text-primary fw-bold fs-2 mb-2 mt-5">{{ $totalEvents }}</div>
                <div class="fw-semibold text-gray-600">Eventos na Timeline</div>
            </div>
        </div>
    </div>
    
    {{-- Etapas de Formação --}}
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-success hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <span class="svg-icon svg-icon-success svg-icon-3x ms-n1">
                    <i class="fa-solid fa-graduation-cap text-success fs-2x"></i>
                </span>
                <div class="text-success fw-bold fs-2 mb-2 mt-5">{{ $formationStages }}</div>
                <div class="fw-semibold text-gray-600">Etapas de Formação</div>
            </div>
        </div>
    </div>
    
    {{-- Anos em Formação --}}
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-warning hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <span class="svg-icon svg-icon-warning svg-icon-3x ms-n1">
                    <i class="fa-solid fa-calendar-days text-warning fs-2x"></i>
                </span>
                <div class="text-warning fw-bold fs-2 mb-2 mt-5">{{ $yearsInFormation }} anos</div>
                <div class="fw-semibold text-gray-600">Em Formação</div>
            </div>
        </div>
    </div>
    
    {{-- Status Atual --}}
    <div class="col-xl-3 col-md-6">
        <div class="card bg-light-{{ $hasPerpetualVows ? 'danger' : ($isOrdained ? 'info' : 'secondary') }} hoverable card-xl-stretch mb-xl-8">
            <div class="card-body">
                <span class="svg-icon svg-icon-3x ms-n1">
                    @if($hasPerpetualVows)
                        <i class="fa-solid fa-infinity text-danger fs-2x"></i>
                    @elseif($isOrdained)
                        <i class="fa-solid fa-cross text-info fs-2x"></i>
                    @else
                        <i class="fa-solid fa-user text-secondary fs-2x"></i>
                    @endif
                </span>
                <div class="text-{{ $hasPerpetualVows ? 'danger' : ($isOrdained ? 'info' : 'secondary') }} fw-bold fs-5 mb-2 mt-5">
                    {{ $currentStage }}
                </div>
                <div class="fw-semibold text-gray-600">Etapa Atual</div>
            </div>
        </div>
    </div>
</div>
