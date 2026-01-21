@props([
    'tableId' => 'kt_datatable_table',
    'periodLabel' => null,
    'accountOptions' => [],
    'accountOptionsHtml' => '',
    'showAccountFilter' => true,
    'showMoreFilters' => true,
    'moreFilters' => [],
])

@php
    // Período padrão se não fornecido
    if (!$periodLabel) {
        $currentMonth = \Carbon\Carbon::now();
        $periodLabel = $currentMonth->translatedFormat('F \d\e Y');
    }

    // Usar accountOptions diretamente (array) - o componente tenant-select-button já trata isso
@endphp

<!--begin::Filtros-->
<div class="d-flex flex-wrap gap-3 align-items-end">
    <!--begin::Período-->
    <div class="me-3">
        <div class="btn-group w-100" role="group" style="display: flex;">
            <!--begin::Botão Anterior-->
            <button class="btn btn-light btn-sm btn-icon btn-light-primary" type="button"
                id="prev-period-btn-{{ $tableId }}"
                style="border-radius: 0.475rem 0 0 0.475rem; border-right: 1px solid; flex-shrink: 0;">
                <i class="bi bi-chevron-left"></i>
            </button>
            <!--end::Botão Anterior-->

            <!--begin::Botão Período (abre daterangepicker)-->
            <button class="btn btn-light btn-sm flex-grow-1 btn-light-primary" type="button"
                id="period-selector-{{ $tableId }}"
                style="border-left: none; border-right: none; border-radius: 0;">
                <span id="period-display-{{ $tableId }}">{{ $periodLabel }}</span>
            </button>
            <!--end::Botão Período-->

            <!--begin::Input oculto para daterangepicker-->
            <input type="text" class="d-none" id="kt_daterangepicker_{{ $tableId }}" />
            <!--end::Input oculto para daterangepicker-->

            <!--begin::Botão Próximo-->
            <button class="btn btn-light btn-sm btn-icon btn-light-primary" type="button"
                id="next-period-btn-{{ $tableId }}"
                style="border-radius: 0 0.475rem 0.475rem 0; border-left: 1px solid flex-shrink: 0;">
                <i class="bi bi-chevron-right"></i>
            </button>
            <!--end::Botão Próximo-->
        </div>
    </div>
    <!--end::Período-->

    <!--begin::Busca-->
    <x-tenant-button-search tableId="{{ $tableId }}"
        placeholder="Buscar no período" />
    <!--end::Busca-->

    <!--begin::Conta-->
    @if ($showAccountFilter)
        <div style="min-width: 220px;">
            <x-tenant-select-button name="account-filter-{{ $tableId }}"
                id="account-filter-{{ $tableId }}" placeholder="Escolha uma Entidade Financeira" :multiple="true"
                class="" :labelSize="'fs-7'" :options="$accountOptions" />
        </div>
    @endif
    <!--end::Conta-->

    <!--begin::Mais Filtros-->
    @if ($showMoreFilters)
        <div class="d-flex flex-wrap gap-3 align-items-end">
            <label class="form-label mb-1 fw-bold text-gray-700 d-block">&nbsp;</label>
            <button type="button" class="btn btn-sm btn-light-primary" id="more-filters-btn-{{ $tableId }}"
                data-bs-toggle="dropdown">
                Mais filtros
                <i class="bi bi-chevron-down ms-1"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3" id="more-filters-menu-{{ $tableId }}"
                style="min-width: 250px;">
                @if (isset($moreFilters) && is_array($moreFilters) && count($moreFilters) > 0)
                    @foreach ($moreFilters as $index => $filter)
                        <div class="{{ !$loop->last ? 'mb-3' : 'mb-0' }}">
                            <label
                                class="form-label fw-semibold text-gray-700 mb-2">{{ $filter['label'] ?? 'Filtro' }}</label>
                            @if (isset($filter['type']) && $filter['type'] === 'select')
                                <select class="form-select form-select-sm"
                                    id="{{ $filter['id'] ?? 'filter-' . $index . '-' . $tableId }}">
                                    @if (isset($filter['options']))
                                        @foreach ($filter['options'] as $option)
                                            <option value="{{ $option['value'] ?? $option }}">
                                                {{ $option['label'] ?? $option }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @elseif(isset($filter['type']) && $filter['type'] === 'input')
                                <input type="{{ $filter['inputType'] ?? 'text' }}"
                                    class="form-control form-control-sm"
                                    id="{{ $filter['id'] ?? 'filter-' . $index . '-' . $tableId }}"
                                    placeholder="{{ $filter['placeholder'] ?? '' }}" />
                            @else
                                {{-- Slot para filtros customizados --}}
                                @if (isset($filter['slot']))
                                    {!! $filter['slot'] !!}
                                @endif
                            @endif
                        </div>
                    @endforeach
                @else
                    {{-- Filtros padrão se não fornecidos --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-gray-700 mb-2">Situação</label>
                        <select class="form-select form-select-sm" id="situacao-filter-{{ $tableId }}">
                            <option value="">Todas</option>
                            <option value="em_aberto">Em Aberto</option>
                            <option value="atrasado">Atrasado</option>
                            <option value="previsto">Previsto</option>
                            <option value="pago_parcial">Pago Parcial</option>
                            <option value="pago">Pago</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-gray-700 mb-2">Tipo de Documento</label>
                        <select class="form-select form-select-sm" id="tipo-doc-filter-{{ $tableId }}">
                            <option value="">Todos</option>
                            <option value="nota_fiscal">Nota Fiscal</option>
                            <option value="boleto">Boleto</option>
                            <option value="recibo">Recibo</option>
                        </select>
                    </div>
                @endif
            </div>
            <!--begin::Limpar Filtros-->
            <a href="#" class="text-primary text-decoration-none" id="clear-filters-btn-{{ $tableId }}">
                <i class="bi bi-trash me-1 text-primary"></i> Limpar filtros
            </a>
            <!--end::Limpar Filtros-->
        </div>
    @endif
    <!--end::Mais Filtros-->
</div>
<!--end::Filtros-->

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verifica se jQuery está disponível
            if (typeof $ === 'undefined') {
                console.warn('[TenantDatatableFilters] jQuery não está disponível. Aguardando...');
                setTimeout(arguments.callee, 100);
                return;
            }

            // O Select2 do filtro de Conta é inicializado automaticamente pelo componente tenant-select

            // Gerenciamento de Período
            let currentStart = moment().startOf('month');
            let currentEnd = moment().endOf('month');

            const periodDisplay = document.getElementById('period-display-{{ $tableId }}');
            const prevBtn = document.getElementById('prev-period-btn-{{ $tableId }}');
            const nextBtn = document.getElementById('next-period-btn-{{ $tableId }}');
            const periodSelector = document.getElementById('period-selector-{{ $tableId }}');
            const daterangepickerInput = $('#kt_daterangepicker_{{ $tableId }}');

            // Função para atualizar o display do período
            function updatePeriodDisplay() {
                if (periodDisplay && typeof moment !== 'undefined') {
                    // Se for o mesmo mês, mostra "Janeiro de 2026"
                    if (currentStart.format('YYYY-MM') === currentEnd.format('YYYY-MM') &&
                        currentStart.date() === 1 &&
                        currentEnd.date() === currentEnd.daysInMonth()) {
                        periodDisplay.textContent = currentStart.format('MMMM [de] YYYY');
                    } else {
                        // Caso contrário, mostra o range completo
                        periodDisplay.textContent = currentStart.format('DD/MM/YYYY') + ' - ' + currentEnd.format(
                            'DD/MM/YYYY');
                    }
                }
            }

            // Função para disparar evento de mudança de período
            function triggerPeriodChange() {
                // Dispara evento customizado
                const event = new CustomEvent('periodChanged', {
                    detail: {
                        start: currentStart.clone(),
                        end: currentEnd.clone(),
                        tableId: '{{ $tableId }}'
                    }
                });
                document.dispatchEvent(event);

                // Também dispara no elemento do período para compatibilidade
                if (periodSelector) {
                    periodSelector.dispatchEvent(new CustomEvent('periodChanged', {
                        detail: {
                            start: currentStart.clone(),
                            end: currentEnd.clone(),
                            tableId: '{{ $tableId }}'
                        }
                    }));
                }
            }

            // Botão central abre o daterangepicker
            if (periodSelector) {
                periodSelector.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (daterangepickerInput.length && typeof $.fn.daterangepicker !== 'undefined') {
                        daterangepickerInput.data('daterangepicker').toggle();
                    }
                });
            }

            // Botão anterior (mês anterior)
            if (prevBtn) {
                prevBtn.addEventListener('click', function() {
                    currentStart = currentStart.clone().subtract(1, 'month').startOf('month');
                    currentEnd = currentStart.clone().endOf('month');
                    updatePeriodDisplay();
                    triggerPeriodChange();

                    // Atualiza o daterangepicker se já estiver inicializado
                    if (daterangepickerInput.length && typeof $.fn.daterangepicker !== 'undefined') {
                        daterangepickerInput.data('daterangepicker').setStartDate(currentStart);
                        daterangepickerInput.data('daterangepicker').setEndDate(currentEnd);
                    }
                });
            }

            // Botão próximo (próximo mês)
            if (nextBtn) {
                nextBtn.addEventListener('click', function() {
                    currentStart = currentStart.clone().add(1, 'month').startOf('month');
                    currentEnd = currentStart.clone().endOf('month');
                    updatePeriodDisplay();
                    triggerPeriodChange();

                    // Atualiza o daterangepicker se já estiver inicializado
                    if (daterangepickerInput.length && typeof $.fn.daterangepicker !== 'undefined') {
                        daterangepickerInput.data('daterangepicker').setStartDate(currentStart);
                        daterangepickerInput.data('daterangepicker').setEndDate(currentEnd);
                    }
                });
            }

            // Inicializar Daterangepicker
            if (daterangepickerInput.length && typeof moment !== 'undefined' && typeof $.fn.daterangepicker !==
                'undefined') {
                var start = moment().startOf('month');
                var end = moment().endOf('month');

                function cb(start, end) {
                    currentStart = start;
                    currentEnd = end;
                    updatePeriodDisplay();
                    triggerPeriodChange();
                }

                daterangepickerInput.daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        "Hoje": [moment(), moment()],
                        "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                        "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
                        "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
                        "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                        "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1,
                            "month").endOf("month")]
                    },
                    locale: {
                        format: "DD/MM/YYYY",
                        applyLabel: "Aplicar",
                        cancelLabel: "Cancelar",
                        fromLabel: "De",
                        toLabel: "Até",
                        customRangeLabel: "Personalizado",
                        weekLabel: "S",
                        daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
                        monthNames: [
                            "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                            "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
                        ],
                        firstDay: 0
                    },
                    opens: 'left',
                    drops: 'down',
                    parentEl: periodSelector ? periodSelector.closest('.card-body') || periodSelector
                        .closest('.card') : 'body'
                }, cb);

                // Reposicionar o daterangepicker quando abrir, relativo ao botão
                daterangepickerInput.on('show.daterangepicker', function(ev, picker) {
                    if (periodSelector && typeof $ !== 'undefined') {
                        const buttonRect = periodSelector.getBoundingClientRect();

                        // Posiciona o daterangepicker abaixo do botão
                        setTimeout(function() {
                            if (typeof $ !== 'undefined') {
                            const $picker = $('.daterangepicker');
                            if ($picker.length) {
                                $picker.css({
                                    'top': (buttonRect.bottom + 5) + 'px',
                                    'left': buttonRect.left + 'px',
                                    'position': 'fixed'
                                });
                                }
                            }
                        }, 10);
                    }
                });

                // Executa callback inicial
                cb(start, end);
            }

            // Event listener para o campo de pesquisa
            const searchInput = document.getElementById('search-{{ $tableId }}');
            const searchButton = document.getElementById('search-button-search-{{ $tableId }}');

            // Função para disparar evento de pesquisa
            function triggerSearch() {
                const event = new CustomEvent('searchTriggered', {
                    detail: {
                        searchValue: searchInput ? searchInput.value : '',
                        tableId: '{{ $tableId }}'
                    }
                });
                document.dispatchEvent(event);
            }

            if (searchInput) {
                // Pesquisar ao pressionar Enter no campo de busca
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        triggerSearch();
                    }
                });
            }

            // Pesquisar ao clicar no ícone de pesquisa
            if (searchButton) {
                searchButton.addEventListener('click', function() {
                    triggerSearch();
                });

                // Tornar o span clicável visualmente
                searchButton.style.cursor = 'pointer';
            }

            // Listener para quando o filtro de conta for aplicado
            document.addEventListener('selectApplied', function(e) {
                if (e.detail && e.detail.selectId && e.detail.selectId.includes(
                        'account-filter-{{ $tableId }}')) {
                    // Disparar evento para recarregar a tabela com o novo filtro
                    const event = new CustomEvent('searchTriggered', {
                        detail: {
                            search: searchInput ? searchInput.value : '',
                            tableId: '{{ $tableId }}'
                        }
                    });
                    document.dispatchEvent(event);
                }
            });
        });
    </script>
@endpush
