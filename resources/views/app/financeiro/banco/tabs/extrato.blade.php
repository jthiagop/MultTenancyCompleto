@php
    $key = 'extrato';
    $parentId = 'kt_tab_extrato';
    $tipo = 'all';
    
    // Stats customizadas para Extrato
    $stats = [
        ['key' => 'receitas_aberto', 'label' => 'Receitas em Aberto (R$)', 'value' => '0,00', 'variant' => 'warning'],
        ['key' => 'receitas_realizadas', 'label' => 'Receitas Realizadas (R$)', 'value' => '0,00', 'variant' => 'success'],
        ['key' => 'despesas_aberto', 'label' => 'Despesas em Aberto (R$)', 'value' => '-0,00', 'variant' => 'danger'],
        ['key' => 'despesas_realizadas', 'label' => 'Despesas Realizadas (R$)', 'value' => '-0,00', 'variant' => 'danger'],
        ['key' => 'total', 'label' => 'Total do período (R$)', 'value' => '0,00', 'variant' => 'primary', 'tooltip' => true],
    ];
    
    // Colunas padrão para Extrato
    $tableColumns = [
        ['key' => 'checkbox', 'label' => '', 'width' => 'w-10px pe-2', 'orderable' => false],
        ['key' => 'data', 'label' => 'Data', 'width' => 'min-w-70px', 'orderable' => true],
        ['key' => 'descricao', 'label' => 'Descrição', 'width' => 'min-w-175px', 'orderable' => false],
        ['key' => 'situacao', 'label' => 'Situação', 'width' => 'min-w-70px', 'orderable' => false],
        ['key' => 'valor', 'label' => 'Valor (R$)', 'width' => 'min-w-50px', 'orderable' => true],
        ['key' => 'saldo', 'label' => 'Saldo (R$)', 'width' => 'min-w-50px', 'orderable' => true],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-center min-w-50px', 'orderable' => false],
    ];
    
    // Gerar ID previsível e consistente
    $tableIdFinal = 'kt_' . $key . '_table';
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
    
    // Mapear $stats para o formato do segmented-tabs-toolbar
    $mappedTabs = array_map(function ($stat) use ($tableIdFinal) {
        return [
            'key' => $stat['key'],
            'label' => $stat['label'],
            'count' => $stat['value'],
            'paneId' => "pane-stat-{$stat['key']}-{$tableIdFinal}",
        ];
    }, $stats);
@endphp

<div class="tab-pane fade show active" 
     id="{{ $parentId }}_{{ $key }}" 
     role="tabpanel" 
     data-pane-id="{{ $parentId }}_{{ $key }}"
     data-table-id="{{ $tableIdFinal }}" 
     data-filter-id="{{ $filterId }}" 
     data-key="{{ $key }}"
     data-tipo="{{ $tipo }}" 
     data-stats-url="{{ route('banco.stats.data') }}"
     data-data-url="{{ route('banco.transacoes.data') }}" 
     data-columns-json="{{ json_encode($columnsForJson) }}"
     data-default-order="{{ json_encode($defaultOrder) }}" 
     data-page-length="50">

    <x-tenant.segmented-tabs-toolbar 
        :tabs="$mappedTabs" 
        :active="request('status')" 
        id="status-tabs-{{ $tableIdFinal }}"
        :tableId="$tableIdFinal"
        :filterId="$filterId"
        :periodLabel="null"
        :accountOptions="$accountOptions ?? []"
        :showAccountFilter="true"
        :showMoreFilters="true"
        :moreFilters="[]">

        <x-slot:actionsLeft>
            <span class="text-muted fs-7 mx-2" id="selected-count-{{ $tableIdFinal }}">0 registro(s) selecionado(s)</span>
        </x-slot>

        <x-slot:actionsRight>
            <x-tenant-button-batch-actions 
                :tableId="$tableIdFinal" 
                :id="$tableIdFinal" 
                :markAsPaidRoute="route('banco.batch-mark-as-paid')" 
                :markAsOpenRoute="route('banco.batch-mark-as-open')"
                :deleteRoute="route('banco.batch-delete')" />
        </x-slot>

        <x-slot:panes>
            @foreach ($mappedTabs as $tab)
                <div class="tab-pane fade {{ request('status') === $tab['key'] || (request('status') === null && $loop->first) ? 'show active' : '' }}"
                    id="{{ $tab['paneId'] }}" role="tabpanel">
                    <!-- Tab panes vazios - a tabela está no slot tableContent -->
                </div>
            @endforeach
        </x-slot:panes>
        
        <x-slot:tableContent>
            <!--begin::Skeleton Loading-->
            <x-tenant-datatable-skeleton :tableId="$tableIdFinal" :columns="$tableColumns" />
            <!--end::Skeleton Loading-->

            <!--begin::Table Wrapper-->
            <div id="table-wrapper-{{ $tableIdFinal }}" class="d-none mt-4">
                <!--begin::Table-->
                <table class="table align-middle table-striped table-row-dashed fs-6 gy-5 mt-7"
                    id="{{ $tableIdFinal }}">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-4">
                            @foreach ($tableColumns as $column)
                                @if ($column['key'] === 'checkbox')
                                    <th class="{{ $column['width'] ?? 'text-end min-w-50px pe-6' }}">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
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
        </x-slot:tableContent>
    </x-tenant.segmented-tabs-toolbar>
</div>
