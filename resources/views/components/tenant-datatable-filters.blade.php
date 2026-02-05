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
    <div class="me-3">
        <div class="btn-group w-100" role="group">
            <button class="btn btn-light btn-sm btn-icon btn-light-primary" type="button"
                id="prev-period-btn-{{ $tableId }}" style="z-index: 1; position: relative;">
                <i class="bi bi-chevron-left"></i>
            </button>

            <button class="btn btn-light btn-sm flex-grow-1 btn-light-primary position-relative" type="button"
                id="period-selector-{{ $tableId }}" style="z-index: 1;">
                <span id="period-display-{{ $tableId }}">{{ $periodLabel }}</span>
                {{-- Input invisível para facilitar posicionamento do Daterangepicker --}}
                <input type="text" class="position-absolute opacity-0 top-0 start-0 w-100 h-100"
                    id="kt_daterangepicker_{{ $tableId }}" style="cursor: pointer; z-index: 10;" readonly />
            </button>

            <button class="btn btn-light btn-sm btn-icon btn-light-primary" type="button"
                id="next-period-btn-{{ $tableId }}" style="z-index: 1; position: relative;">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>
    <!--end::Período-->

    <!--begin::Busca-->
    <x-tenant-button-search tableId="{{ $tableId }}" placeholder="Buscar no período" />
    <!--end::Busca-->

    <!--begin::Conta-->
    @if ($showAccountFilter)
        <div style="min-width: 220px;">
            <x-tenant-select-button name="account-filter-{{ $tableId }}" id="account-filter-{{ $tableId }}"
                placeholder="Entidade Financeira" :multiple="true" :labelSize="'fs-7'" :options="$accountOptions" />
        </div>
    @endif
    <!--end::Conta-->

    <!--begin::Mais Filtros-->
    @if ($showMoreFilters)
        <div class="d-flex flex-wrap gap-3 align-items-end">
            {{-- Espaçador visual --}}
            <div style="height: 38px; border-left: 1px solid #e4e6ef; margin: 0 5px;"></div>

            <div class="dropdown">
                <button type="button" class="btn btn-sm btn-light-primary dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="bi bi-filter me-1"></i> Mais filtros
                </button>

                <div class="dropdown-menu dropdown-menu-end p-4 shadow-sm" style="min-width: 300px;">
                    @if (isset($moreFilters) && count($moreFilters) > 0)
                        @foreach ($moreFilters as $index => $filter)
                            <div class="mb-3">
                                <label
                                    class="form-label fs-7 fw-bold text-gray-700">{{ $filter['label'] ?? 'Filtro' }}</label>
                                @if (($filter['type'] ?? '') === 'select')
                                    <select class="form-select form-select-sm" name="{{ $filter['name'] ?? '' }}"
                                        id="{{ $filter['id'] ?? 'filter-' . $index . '-' . $tableId }}">
                                        @foreach ($filter['options'] ?? [] as $opt)
                                            <option value="{{ $opt['value'] ?? $opt }}">{{ $opt['label'] ?? $opt }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif(($filter['type'] ?? '') === 'input')
                                    <input type="{{ $filter['inputType'] ?? 'text' }}"
                                        class="form-control form-control-sm"
                                        placeholder="{{ $filter['placeholder'] ?? '' }}"
                                        id="{{ $filter['id'] ?? 'filter-' . $index . '-' . $tableId }}" />
                                @endif
                                @if (isset($filter['slot']))
                                    {!! $filter['slot'] !!}
                                @endif
                            </div>
                        @endforeach
                    @else
                        {{-- Filtros Padrão --}}
                        <div class="mb-3">
                            <label class="form-label fs-7 fw-bold text-gray-700">Situação</label>
                            <select class="form-select form-select-sm" id="situacao-filter-{{ $tableId }}">
                                <option value="">Todas</option>
                                <option value="em_aberto">Em Aberto</option>
                                <option value="atrasado">Atrasado</option>
                                <option value="pago">Pago</option>
                            </select>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none "
                    id="clear-filters-btn-{{ $tableId }}">
                    <i class="bi bi-trash me-1 text-danger"></i> Limpar Filtros
                </button>
            </div>
        </div>
    @endif
    <!--end::Mais Filtros-->
</div>
<!--end::Filtros Wrapper-->

@push('scripts')
    <script>
        // Inicializa via função global para evitar duplicação e melhorar performance
        (function() {
            function tryInit() {
                if (typeof window.initTenantFilters === 'function') {
                    window.initTenantFilters('{{ $tableId }}');
                } else {
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
