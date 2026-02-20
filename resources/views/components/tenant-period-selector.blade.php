@props([
    'id' => null,
    'tableId' => null,
    'periodLabel' => null,
    'showAllPeriod' => false,
])

@php
    $selectorId = $id ?? $tableId ?? 'default';

    if (!$periodLabel) {
        $periodLabel = \Carbon\Carbon::now()->translatedFormat('F \d\e Y');
    }
@endphp

<!--begin::Período-->
<div class="d-flex align-items-center gap-2">
    <div>
        <div class="btn-group w-100" role="group" aria-label="Navegação de período">
            <button class="btn btn-light btn-sm btn-icon btn-light-primary tenant-filter-period-nav"
                    type="button"
                    id="prev-period-btn-{{ $selectorId }}"
                    aria-label="Período anterior">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button class="btn btn-light btn-sm flex-grow-1 btn-light-primary position-relative tenant-filter-period-nav"
                    type="button"
                    id="period-selector-{{ $selectorId }}"
                    aria-label="Selecionar período">
                <span id="period-display-{{ $selectorId }}">{{ $periodLabel }}</span>
                {{-- Input invisível para facilitar posicionamento do Daterangepicker --}}
                <input type="text"
                       class="position-absolute opacity-0 top-0 start-0 w-100 h-100"
                       id="kt_daterangepicker_{{ $selectorId }}"
                       style="cursor: pointer; z-index: 10;"
                       readonly
                       tabindex="-1"
                       aria-hidden="true" />
            </button>

            <button class="btn btn-light btn-sm btn-icon btn-light-primary tenant-filter-period-nav"
                    type="button"
                    id="next-period-btn-{{ $selectorId }}"
                    aria-label="Próximo período">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    @if($showAllPeriod)
        <button class="btn btn-light btn-sm btn-light-primary tenant-period-all-btn"
                type="button"
                id="period-all-btn-{{ $selectorId }}"
                aria-label="Todo o período">
            <i class="bi bi-calendar3 me-1"></i>Todo
        </button>
    @endif
</div>
<!--end::Período-->

{{-- Estilos inline (layout não possui @stack('styles')) --}}
<style>
    .tenant-filter-period-nav {
        z-index: 1;
        position: relative;
    }
    .tenant-period-all-btn.active {
        background-color: var(--bs-primary) !important;
        color: #fff !important;
        border-color: var(--bs-primary) !important;
    }
</style>

@push('scripts')
<script>
    (function() {
        var maxRetries = 50;
        var retries = 0;

        function tryInit() {
            if (typeof window.initTenantPeriod === 'function') {
                window.initTenantPeriod('{{ $selectorId }}');
            } else if (retries++ < maxRetries) {
                setTimeout(tryInit, 100);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryInit);
        } else {
            tryInit();
        }
    })();
</script>
@endpush
