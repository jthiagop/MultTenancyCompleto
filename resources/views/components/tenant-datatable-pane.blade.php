@props([
    'key' => '',
    'parentId' => 'kt_tab_datatable',
    'active' => false,
    'stats' => null,
    'tipo' => 'entrada', // entrada ou saida
    'tableId' => null,
    'periodLabel' => null,
    'accountOptions' => [],
    'showAccountFilter' => true,
    'showMoreFilters' => true,
    'moreFilters' => [],
    'columns' => null, // Array de definições de colunas customizadas (null = usar defaults baseado no key)
])

@php
    // Labels dinâmicos baseados no tipo (entrada/saida)
    $labelPagoRecebido = $tipo === 'entrada' ? 'Recebidos (R$)' : 'Pagos (R$)';

    // Stats padrão se não fornecidos
    $defaultStats = [
        ['key' => 'vencidos', 'label' => 'Vencidos (R$)', 'value' => '0,00', 'variant' => 'danger'],
        ['key' => 'hoje', 'label' => 'Vencem hoje (R$)', 'value' => '0,00', 'variant' => 'danger'],
        ['key' => 'a_vencer', 'label' => 'A vencer (R$)', 'value' => '0,00', 'variant' => 'primary'],
        [
            'key' => $tipo === 'entrada' ? 'recebidos' : 'pagos',
            'label' => $labelPagoRecebido,
            'value' => '0,00',
            'variant' => 'success',
        ],
        [
            'key' => 'total',
            'label' => 'Total do período (R$)',
            'value' => '0,00',
            'variant' => 'primary',
            'tooltip' => true,
        ],
    ];

    // Colunas padrão para Contas a Receber/Pagar
    $defaultColumnsContas = [
        ['key' => 'checkbox', 'label' => '', 'width' => 'w-25px text-end', 'orderable' => false],
        ['key' => 'vencimento', 'label' => 'Vencimento', 'width' => 'min-w-70px', 'orderable' => true],
        ['key' => 'descricao', 'label' => 'Descrição', 'width' => 'min-w-175px', 'orderable' => false],
        ['key' => 'total', 'label' => 'Total (R$)', 'width' => 'min-w-50px', 'orderable' => true],
        ['key' => 'a_pagar', 'label' => 'A pagar (R$)', 'width' => 'min-w-50px', 'orderable' => true],
        ['key' => 'situacao', 'label' => 'Situação', 'width' => 'min-w-70px', 'orderable' => false],
        ['key' => 'origem', 'label' => 'Origem', 'width' => 'min-w-70px', 'orderable' => false],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-start min-w-50px', 'orderable' => false],
    ];

    // Colunas padrão para Extrato
    $defaultColumnsExtrato = [
        ['key' => 'checkbox', 'label' => '', 'width' => 'w-25px text-end', 'orderable' => false],
        ['key' => 'data', 'label' => 'Data', 'width' => 'min-w-70px', 'orderable' => true],
        ['key' => 'descricao', 'label' => 'Descrição', 'width' => 'min-w-175px', 'orderable' => false],
        ['key' => 'situacao', 'label' => 'Situação', 'width' => 'min-w-70px', 'orderable' => false],
        ['key' => 'valor', 'label' => 'Valor (R$)', 'width' => 'min-w-50px', 'orderable' => true],
        ['key' => 'saldo', 'label' => 'Saldo (R$)', 'width' => 'min-w-50px', 'orderable' => true],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-start min-w-50px', 'orderable' => false],
    ];

    // Determinar colunas padrão baseado no key
    if ($columns === null) {
        if ($key === 'extrato') {
            $tableColumns = $defaultColumnsExtrato;
        } else {
            $tableColumns = $defaultColumnsContas;
        }
    } else {
        $tableColumns = $columns;
    }

    // Determinar stats padrão
    if ($stats === null) {
        if ($key === 'extrato') {
            $stats = [
                [
                    'key' => 'total',
                    'label' => 'Total do período (R$)',
                    'value' => '0,00',
                    'variant' => 'primary',
                    'tooltip' => true,
                ],
            ];
        } else {
            $stats = $defaultStats;
        }
    }

    // Garantir que todas as stats tenham 'key'
    foreach ($stats as $index => $stat) {
        if (!isset($stat['key'])) {
            $keys = ['vencidos', 'hoje', 'a_vencer', 'recebidos', 'total'];
            $stats[$index]['key'] = $keys[$index] ?? 'stat_' . $index;
        }
    }

    // Gerar ID previsível e consistente
    // Se tableId foi passado explicitamente, usar ele; senão gerar baseado em key
    $tableIdFinal = $tableId ?: 'kt_' . $key . '_table';

    // O filterId é o mesmo que tableId para manter consistência
    $filterId = $tableIdFinal;

    // Preparar colunas para JSON (adicionar índice para mapeamento)
    $columnsForJson = array_map(
        function ($col, $index) {
            return array_merge($col, ['index' => $index]);
        },
        $tableColumns,
        array_keys($tableColumns),
    );

    // Determinar ordem padrão (primeira coluna ordenável ou índice 1)
    $defaultOrderCol = 1;
    foreach ($tableColumns as $idx => $col) {
        if ($col['key'] !== 'checkbox' && ($col['orderable'] ?? true)) {
            $defaultOrderCol = $idx;
            break;
        }
    }
    $defaultOrder = [[$defaultOrderCol, 'desc']];
@endphp

<div class="tab-pane fade tenant-datatable-pane {{ $active ? 'show active' : '' }}"
    id="{{ $parentId }}_{{ $key }}" role="tabpanel" data-pane-id="{{ $parentId }}_{{ $key }}"
    data-table-id="{{ $tableIdFinal }}" data-filter-id="{{ $filterId }}" data-key="{{ $key }}"
    data-tipo="{{ $tipo }}" data-stats-url="{{ route('banco.stats.data') }}"
    data-data-url="{{ route('banco.transacoes.data') }}" data-columns-json="{{ json_encode($columnsForJson) }}"
    data-default-order="{{ json_encode($defaultOrder) }}" data-page-length="50">

    <!--begin::Cards Resumo (Tabs)-->
    @php
        // Mapear $stats (financeiro) para o formato do segmented-tabs (conciliação/geral)
        $mappedTabs = array_map(function ($stat) use ($tableIdFinal) {
            return [
                'key' => $stat['key'],
                'label' => $stat['label'],
                'count' => $stat['value'], // O componente novo usa 'count' para o valor grande
                'paneId' => "pane-stat-{$stat['key']}-{$tableIdFinal}",
            ];
        }, $stats);
    @endphp

    <div class="card mb-5">
        <div class="card-body">
            <x-tenant-datatable-filters :tableId="$tableIdFinal" :periodLabel="$periodLabel" :accountOptions="$accountOptions" :showAccountFilter="$showAccountFilter"
                :showMoreFilters="$showMoreFilters" :moreFilters="$moreFilters" />
        </div>
    </div>

    <x-tenant.segmented-tabs-toolbar :tabs="$mappedTabs" :active="request('status')" id="status-tabs-{{ $tableIdFinal }}">

        <x-slot:actionsLeft>
            <span class="text-muted fs-7 mx-2" id="selected-count-{{ $tableIdFinal }}">0 registro(s)
                selecionado(s)</span>
        </x-slot>

        <x-slot:actionsRight>
            <x-tenant-button-batch-actions :tableId="$tableIdFinal" :id="$tableIdFinal" :markAsPaidRoute="route('banco.batch-mark-as-paid')" :markAsOpenRoute="route('banco.batch-mark-as-open')"
                :deleteRoute="route('banco.batch-delete')" />


        </x-slot>

        <x-slot:panes>
            @foreach ($mappedTabs as $tab)
                <div class="tab-pane fade {{ request('status') === $tab['key'] || (request('status') === null && $loop->first) ? 'show active' : '' }}"
                    id="{{ $tab['paneId'] }}" role="tabpanel">
                    @if ($loop->first || request('status') === $tab['key'])
                        <!--begin::Skeleton Loading-->
                        <x-tenant-datatable-skeleton :tableId="$tableIdFinal" :columns="$tableColumns" />
                        <!--end::Skeleton Loading-->

                        <!--begin::Table Wrapper-->
                        <div id="table-wrapper-{{ $tableIdFinal }}" class="d-none px-6">
                            <!--begin::Table-->
                            <table class="table align-middle table-striped table-row-dashed fs-6 gy-5 mt-7"
                                id="{{ $tableIdFinal }}">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-4">
                                        @foreach ($tableColumns as $column)
                                            @if ($column['key'] === 'checkbox')
                                                <th class="{{ $column['width'] ?? 'text-end min-w-50px pe-6' }}">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#{{ $tableIdFinal }} .form-check-input"
                                                            value="1" />
                                                    </div>
                                                </th>
                                            @elseif($column['key'] === 'acoes')
                                                <th class="{{ $column['width'] ?? 'text-center min-w-50px' }}">
                                                    {{ $column['label'] }}</th>
                                            @else
                                                <th class="{{ $column['width'] ?? '' }}">{{ $column['label'] }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    <!-- Dados serão carregados via DataTables -->
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Table Wrapper-->
                    @endif
                </div>
            @endforeach
        </x-slot:panes>
    </x-tenant.segmented-tabs-toolbar>
    <!--end::Cards Resumo-->
    <!--end::Cards Resumo-->

    {{ $slot }}
</div>
