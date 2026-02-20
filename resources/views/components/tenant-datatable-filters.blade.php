@props([
    'tableId' => 'kt_datatable_table',
    'periodLabel' => null,
    'accountOptions' => [],
    'showAccountFilter' => true,
    'showMoreFilters' => true,
    'moreFilters' => [],
])

@php
    if (!$periodLabel) {
        $periodLabel = \Carbon\Carbon::now()->translatedFormat('F \d\e Y');
    }
@endphp

<!--begin::Filtros Wrapper-->
<div class="d-flex flex-wrap gap-3 align-items-end" id="filters-wrapper-{{ $tableId }}">
    
    <!--begin::Período-->
    <x-tenant-period-selector :tableId="$tableId" :periodLabel="$periodLabel" />
    <!--end::Período-->

    <!--begin::Busca-->
    <x-tenant-button-search tableId="{{ $tableId }}" placeholder="Buscar no período" />
    <!--end::Busca-->

    <!--begin::Conta-->
    @if ($showAccountFilter)
        <div class="tenant-filter-account-wrapper">
            <x-tenant-select-button 
                name="account-filter-{{ $tableId }}"
                id="account-filter-{{ $tableId }}" 
                placeholder="Entidade Financeira" 
                :multiple="true"
                :labelSize="'fs-7'" 
                :options="$accountOptions" />
        </div>
    @endif
    <!--end::Conta-->

    <!--begin::Mais Filtros-->
    @if ($showMoreFilters && !empty($moreFilters))
        <div class="d-flex flex-wrap gap-3 align-items-end">
            {{-- Espaçador visual --}}
            <div class="tenant-filter-separator" aria-hidden="true"></div>
            
            <div class="dropdown">
                <button type="button" 
                        class="btn btn-sm btn-light-primary dropdown-toggle" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false"
                        aria-haspopup="true"
                        aria-label="Abrir filtros adicionais">
                    <i class="bi bi-filter me-1"></i> Mais filtros
                </button>
                
                <div class="dropdown-menu dropdown-menu-end p-4 shadow-sm tenant-filter-dropdown">
                    @foreach ($moreFilters as $index => $filter)
                        <div class="mb-3">
                            <label class="form-label fs-7 fw-bold text-gray-700" 
                                   for="{{ $filter['id'] ?? 'filter-'.$index.'-'.$tableId }}">
                                {{ $filter['label'] ?? 'Filtro' }}
                            </label>
                            @if (($filter['type'] ?? '') === 'select')
                                <select class="form-select form-select-sm" 
                                        name="{{ $filter['name'] ?? '' }}" 
                                        id="{{ $filter['id'] ?? 'filter-'.$index.'-'.$tableId }}">
                                    @foreach ($filter['options'] ?? [] as $opt)
                                        <option value="{{ $opt['value'] ?? $opt }}">{{ $opt['label'] ?? $opt }}</option>
                                    @endforeach
                                </select>
                            @elseif(($filter['type'] ?? '') === 'input')
                                <input type="{{ $filter['inputType'] ?? 'text' }}" 
                                       class="form-control form-control-sm" 
                                       placeholder="{{ $filter['placeholder'] ?? '' }}" 
                                       id="{{ $filter['id'] ?? 'filter-'.$index.'-'.$tableId }}" />
                            @endif
                            @if(isset($filter['slot']))
                                {!! $filter['slot'] !!}
                            @endif
                        </div>
                    @endforeach
                    
                    <div class="separator my-2"></div>
                    <div class="d-flex justify-content-end">
                        <button type="button" 
                                class="btn btn-sm btn-link text-danger text-decoration-none" 
                                id="clear-filters-btn-{{ $tableId }}"
                                aria-label="Limpar todos os filtros">
                            <i class="bi bi-trash me-1"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--end::Mais Filtros-->
</div>
<!--end::Filtros Wrapper-->

@once
@push('styles')
<style>
    /* Tenant Datatable Filters */
    .tenant-filter-account-wrapper {
        min-width: 220px;
    }
    .tenant-filter-separator {
        height: 38px;
        border-left: 1px solid var(--bs-border-color, #e4e6ef);
        margin: 0 5px;
    }
    .tenant-filter-dropdown {
        min-width: 300px;
    }
</style>
@endpush
@endonce

@push('scripts')
<script>
    // Inicializa via função global para evitar duplicação e melhorar performance
    (function() {
        var maxRetries = 50; // ~5 segundos máximo
        var retries = 0;

        function tryInit() {
            if (typeof window.initTenantFilters === 'function') {
                window.initTenantFilters('{{ $tableId }}');
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
