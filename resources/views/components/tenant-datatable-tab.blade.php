@props([
    'tabs' => [],
    'active' => '',
    'param' => 'tab',
    'preserveQuery' => true,
    'tableId' => 'kt_datatable_table',
    'periodLabel' => null,
    'accountOptions' => [],
    'showAccountFilter' => true,
    'showMoreFilters' => true,
    'moreFilters' => [],
])

@php
    // Extrair valores das props
    $paramValue = $param ?? 'tab';
    $preserveQueryValue = $preserveQuery ?? true;

    // Função para gerar URL com querystring
    $generateUrl = function ($key) use ($paramValue, $preserveQueryValue) {
        $currentUrl = url()->current();

        if ($preserveQueryValue) {
            // Preserva todos os parâmetros atuais e substitui apenas o param
            $queryParams = request()->query();
            $queryParams[$paramValue] = $key;
            return $currentUrl . '?' . http_build_query($queryParams);
        } else {
            // Gera URL apenas com param=key
            return $currentUrl . '?' . http_build_query([$paramValue => $key]);
        }
    };

    // Mapeamento de variants para cores do Bootstrap (texto e borda)
    // Cores baseadas no Metronic / Bootstrap padrão
    $variantMap = [
        'danger' => [
            'text' => 'text-danger',
            'border' => '#f1416c',
        ],
        'success' => [
            'text' => 'text-success',
            'border' => '#50cd89',
        ],
        'primary' => [
            'text' => 'text-primary',
            'border' => '#009ef7',
        ],
        'info' => [
            'text' => 'text-info',
            'border' => '#7239ea',
        ],
        'warning' => [
            'text' => 'text-warning',
            'border' => '#ffc700',
        ],
        'secondary' => [
            'text' => 'text-gray-800',
            'border' => '#e4e6ef',
        ],
    ];
@endphp

<!--begin::Card Filters-->
<div class="card mb-3">
    <div class="card-body">
        <x-tenant-datatable-filters
            :tableId="$tableId"
            :periodLabel="$periodLabel"
            :accountOptions="$accountOptions"
            :showAccountFilter="$showAccountFilter"
            :showMoreFilters="$showMoreFilters"
            :moreFilters="$moreFilters"
        />
    </div>
</div>
<!--end::Card Filters-->
<div class="card mb-5 mb-xl-10">
    <div class="card-body p-0">
        {{-- Tabs Navigation --}}
        <div class="d-flex flex-wrap flex-sm-nowrap mb-5">
            @forelse($tabs as $tab)
                @php
                    $isActive = $active === $tab['key'];
                    $variant = $tab['variant'] ?? 'primary';
                    $variantConfig = $variantMap[$variant] ?? $variantMap['primary'];
                    $textClass = $variantConfig['text'];
                    $borderColor = $variantConfig['border'];
                @endphp

                {{-- Container do Item --}}
                <div class="flex-grow-1 {{ !$loop->last ? 'border-end border-gray-200' : '' }}">
                    <a href="{{ $generateUrl($tab['key']) }}"
                        data-tab-key="{{ $tab['key'] }}"
                        data-status-tab="{{ $tab['key'] }}"
                        data-active-color="{{ $borderColor }}"
                        class="d-block p-4 text-decoration-none bg-hover-light transition-all h-100 {{ $isActive ? 'active' : '' }}"
                        style="border-top: 3px solid {{ $isActive ? $borderColor : 'transparent' }}; transition: all 0.2s ease;">

                        <div class="d-flex flex-column align-items-center text-center">
                            {{-- Label e Icone --}}
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fs-7 fw-bold text-gray-600 text-uppercase">{{ $tab['label'] }}</span>

                                {{-- Icone opcional ou Hint --}}
                                @if (isset($tab['hint']) || isset($tab['icon']))
                                    <span class="ms-1" data-bs-toggle="tooltip" title="{{ $tab['hint'] ?? '' }}">
                                        <i class="{{ $tab['icon'] ?? 'bi bi-question-circle' }} fs-7 text-gray-400"></i>
                                    </span>
                                @endif
                            </div>

                            {{-- Valor --}}
                            @php
                                // Detectar valores negativos e aplicar cor vermelha
                                $isNegative = str_starts_with($tab['value'], '-');
                                $finalTextClass = $isNegative ? 'text-danger' : $textClass;
                            @endphp
                            <div class="fs-2 fw-bold {{ $finalTextClass }}">
                                {{ $tab['value'] }}
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="p-6 text-center text-muted w-100">
                    Nenhuma aba disponível
                </div>
            @endforelse
        </div>
        <!--end::Tabs Navigation-->
        <!--begin::Separator-->
        <div class="separator separator-dashed"></div>
        <!--end::Separator-->
        <!--begin::Table Content-->
        <div class="card-body pt-0">
            {{ $slot }}
        </div>
        <!--end::Table Content-->
    </div>


