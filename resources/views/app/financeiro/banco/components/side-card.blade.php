@props([
    'todasEntidades' => null,
    'entidadesBanco' => collect(),
    'entidadesCaixa' => collect(),
    'carouselId' => null,
    'showVariacao' => true,
])

@php
    // Compatibilidade: se todasEntidades não foi passada, faz o merge aqui
    // (para não quebrar código existente)
    if (!$todasEntidades) {
        $todasEntidades = $entidadesBanco->merge($entidadesCaixa)->values();
    }

    // Gera ID único para o carrossel se não foi fornecido
    $carouselId = $carouselId ?? 'kt_sliders_widget_2_slider_' . uniqid();

    // Garante que todasEntidades seja uma coleção
    $todasEntidades = $todasEntidades instanceof \Illuminate\Support\Collection
        ? $todasEntidades
        : collect($todasEntidades);
@endphp

<div class="col-12 col-sm-12 col-md-5">
    @if($todasEntidades->isEmpty())
        {{-- Estado vazio --}}
        <div class="card card-flush h-xl-100">
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <i class="bi bi-inbox fs-3x text-gray-400 mb-3 d-block" aria-hidden="true"></i>
                    <p class="text-gray-600 mb-0">Nenhuma entidade financeira disponível.</p>
                </div>
            </div>
        </div>
    @else
        <div id="{{ $carouselId }}"
             class="card card-flush carousel carousel-custom carousel-stretch slide h-xl-100"
             data-bs-ride="carousel"
             data-bs-interval="9000"
             role="region"
             aria-label="Carrossel de entidades financeiras">

            <div class="card-body py-3 position-relative">
                {{-- Controls --}}
                @if ($todasEntidades->count() > 1)
                    <button class="carousel-control-prev entity-carousel-control"
                            type="button"
                            data-bs-target="#{{ $carouselId }}"
                            data-bs-slide="prev"
                            aria-label="Slide anterior">
                        <i class="bi bi-chevron-compact-left fs-1 me-12" aria-hidden="true"></i>
                    </button>

                    <button class="carousel-control-next entity-carousel-control"
                            type="button"
                            data-bs-target="#{{ $carouselId }}"
                            data-bs-slide="next"
                            aria-label="Próximo slide">
                        <i class="bi bi-chevron-compact-right fs-1 ms-12" aria-hidden="true"></i>
                    </button>
                @endif

                <div class="carousel-inner" role="listbox" aria-label="Lista de entidades financeiras">
                    @foreach ($todasEntidades as $key => $entidade)
                        @include('app.financeiro.banco.components.side-card-item', [
                            'entidade' => $entidade,
                            'isActive' => $key === 0,
                            'index' => $key,
                        ])
                    @endforeach
                </div>
            </div>

            {{-- Indicators --}}
            @if ($todasEntidades->count() > 1)
                <ol class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-primary"
                    role="tablist"
                    aria-label="Indicadores de slide">
                    @foreach ($todasEntidades as $key => $entidade)
                        <li data-bs-target="#{{ $carouselId }}"
                            data-bs-slide-to="{{ $key }}"
                            class="bullet bullet-dot bg-success me-5 {{ $key === 0 ? 'active' : '' }}"
                            role="tab"
                            aria-label="Ir para slide {{ $key + 1 }}"
                            @if($key === 0) aria-selected="true" @endif>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    @endif
</div>

@push('scripts')
@once
<script>
    // Inicializar tooltips quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializa tooltips do Bootstrap
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        var tooltipList = Array.from(tooltipTriggerList).map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Reinicializa tooltips quando novos slides são carregados (para conteúdo dinâmico)
        var carouselElement = document.getElementById('{{ $carouselId }}');
        if (carouselElement) {
            carouselElement.addEventListener('slid.bs.carousel', function () {
                // Destrói tooltips antigos
                tooltipList.forEach(function(tooltip) {
                    tooltip.dispose();
                });

                // Recria tooltips para o novo conteúdo
                tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipList = Array.from(tooltipTriggerList).map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            });
        }
    });
</script>
@endonce
@endpush
