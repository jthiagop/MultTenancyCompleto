{{--
    Card Timeline Completa do Membro
    
    Exibe todos os eventos agregados de várias fontes:
    - Criação do membro
    - Mudanças de etapa de formação
    - Transferências entre casas
    - Datas importantes (profissões, ordenações)
    - Notas/observações
    
    Uso no Controller:
    $member = ReligiousMember::with(['formationPeriods.formationStage', 'formationPeriods.company'])->find($id);
    $timeline = $member->getTimeline(); // ou via TimelineService
    $stats = $member->getTimelineStats();
    
    Uso na View:
    @include('app.modules.secretary.partials.card-timeline', [
        'member' => $member,
        'timeline' => $timeline,
        'stats' => $stats,
    ])
--}}

@php
    // Se não foi passada a timeline, gerar do model
    $timeline = $timeline ?? $member->getTimeline();
    $stats = $stats ?? $member->getTimelineStats();
@endphp

<!--begin::Card Timeline Completa-->
<div class="card mb-5 mb-xl-8">
    <div class="card-header border-0">
        <h3 class="card-title fw-bold text-gray-800">
            <i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>
            Histórico Completo
        </h3>
        <div class="card-toolbar">
            <div class="d-flex align-items-center gap-2">
                {{-- Filtros de tipo --}}
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-light-primary active" data-timeline-filter="all">
                        Todos
                    </button>
                    <button type="button" class="btn btn-sm btn-light" data-timeline-filter="formation">
                        <i class="fa-solid fa-graduation-cap me-1"></i>Formação
                    </button>
                    <button type="button" class="btn btn-sm btn-light" data-timeline-filter="dates">
                        <i class="fa-solid fa-calendar me-1"></i>Datas
                    </button>
                    <button type="button" class="btn btn-sm btn-light" data-timeline-filter="transfer">
                        <i class="fa-solid fa-right-left me-1"></i>Transferências
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body pt-0">
        {{-- Estatísticas Resumidas --}}
        <div class="row g-4 mb-6">
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center bg-light-primary rounded p-3">
                    <i class="fa-solid fa-timeline text-primary fs-2 me-3"></i>
                    <div>
                        <div class="fs-4 fw-bold text-primary">{{ $stats['total_events'] }}</div>
                        <div class="fs-7 text-gray-600">Eventos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center bg-light-success rounded p-3">
                    <i class="fa-solid fa-graduation-cap text-success fs-2 me-3"></i>
                    <div>
                        <div class="fs-4 fw-bold text-success">{{ $stats['formation_stages'] }}</div>
                        <div class="fs-7 text-gray-600">Etapas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center bg-light-warning rounded p-3">
                    <i class="fa-solid fa-calendar-days text-warning fs-2 me-3"></i>
                    <div>
                        <div class="fs-4 fw-bold text-warning">{{ $stats['years_in_formation'] }}</div>
                        <div class="fs-7 text-gray-600">Anos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="d-flex align-items-center bg-light-{{ $stats['has_perpetual_vows'] ? 'danger' : 'info' }} rounded p-3">
                    @if($stats['has_perpetual_vows'])
                        <i class="fa-solid fa-infinity text-danger fs-2 me-3"></i>
                    @else
                        <i class="fa-solid fa-user text-info fs-2 me-3"></i>
                    @endif
                    <div>
                        <div class="fs-6 fw-bold text-{{ $stats['has_perpetual_vows'] ? 'danger' : 'info' }}">
                            {{ \Str::limit($stats['current_stage'], 15) }}
                        </div>
                        <div class="fs-7 text-gray-600">Etapa Atual</div>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Timeline --}}
        @if($timeline->count() > 0)
            <div class="timeline timeline-border-dashed" id="memberTimeline">
                @foreach($timeline as $event)
                    @php
                        // Determinar o grupo de filtro
                        $filterGroup = match(true) {
                            str_starts_with($event->type, 'formation') => 'formation',
                            str_starts_with($event->type, 'profession') || str_starts_with($event->type, 'ordination') || $event->type === 'birth' => 'dates',
                            $event->type === 'transfer' => 'transfer',
                            default => 'other'
                        };
                    @endphp
                    
                    <!--begin::Timeline item-->
                    <div class="timeline-item" data-filter-type="{{ $filterGroup }}">
                        <!--begin::Timeline line-->
                        <div class="timeline-line w-40px"></div>
                        <!--end::Timeline line-->

                        <!--begin::Timeline icon-->
                        <div class="timeline-icon symbol symbol-circle symbol-40px">
                            <div class="symbol-label bg-light-{{ $event->color }}">
                                @if($event->emoji)
                                    <span class="fs-5">{{ $event->emoji }}</span>
                                @else
                                    <i class="{{ $event->icon }} text-{{ $event->color }}"></i>
                                @endif
                            </div>
                        </div>
                        <!--end::Timeline icon-->

                        <!--begin::Timeline content-->
                        <div class="timeline-content mb-10 mt-n1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="fs-5 fw-bold text-gray-800">
                                        @if($event->emoji)
                                            <span class="me-1">{{ $event->emoji }}</span>
                                        @endif
                                        {{ $event->title }}
                                    </span>
                                    
                                    @foreach($event->badges as $badge)
                                        <span class="badge badge-light-{{ $badge['color'] ?? 'secondary' }} fs-8">
                                            {{ $badge['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex flex-column align-items-end text-end">
                                    <span class="text-gray-600 fw-semibold fs-7">
                                        {{ $event->formattedDate() }}
                                    </span>
                                    <span class="text-gray-400 fs-8">
                                        {{ $event->relativeTime() }}
                                    </span>
                                </div>
                            </div>

                            @if($event->description)
                                <div class="text-gray-600 fs-7 mb-3">
                                    {{ $event->description }}
                                </div>
                            @endif
                            
                            {{-- Metadados específicos --}}
                            @if(!empty($event->metadata))
                                @if(isset($event->metadata['company_name']))
                                    <div class="d-flex align-items-center gap-2 mt-2">
                                        <i class="fa-solid fa-location-dot text-gray-400"></i>
                                        <span class="text-gray-500 fs-7">{{ $event->metadata['company_name'] }}</span>
                                    </div>
                                @endif
                            @endif
                            
                            {{-- Arquivos --}}
                            @if(!empty($event->files))
                                <div class="d-flex flex-wrap gap-2 mt-3">
                                    @foreach($event->files as $file)
                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded p-2">
                                            <i class="fa-solid fa-file text-primary fs-6 me-2"></i>
                                            <a href="{{ $file['url'] ?? '#' }}" class="text-gray-800 text-hover-primary fs-7 fw-bold" target="_blank">
                                                {{ $file['name'] ?? 'Arquivo' }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <!--end::Timeline content-->
                    </div>
                    <!--end::Timeline item-->
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-500 py-10">
                <i class="fa-solid fa-inbox fs-2x mb-3 d-block"></i>
                Nenhum evento registrado na timeline
            </div>
        @endif
    </div>
</div>
<!--end::Card Timeline Completa-->

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtro de timeline
    const filterButtons = document.querySelectorAll('[data-timeline-filter]');
    const timelineItems = document.querySelectorAll('#memberTimeline .timeline-item');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.timelineFilter;
            
            // Atualizar botões ativos
            filterButtons.forEach(btn => {
                btn.classList.remove('btn-light-primary', 'active');
                btn.classList.add('btn-light');
            });
            this.classList.remove('btn-light');
            this.classList.add('btn-light-primary', 'active');
            
            // Filtrar itens
            timelineItems.forEach(item => {
                if (filter === 'all') {
                    item.style.display = '';
                } else {
                    const itemType = item.dataset.filterType;
                    item.style.display = (itemType === filter) ? '' : 'none';
                }
            });
        });
    });
});
</script>
@endpush
