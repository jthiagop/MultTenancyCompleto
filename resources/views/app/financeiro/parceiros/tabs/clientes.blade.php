@php
    $key = 'clientes';
    $parentId = 'kt_tab_parceiros';

    $stats = [
        ['key' => 'todos', 'label' => 'Total Clientes', 'value' => '0'],
        ['key' => 'com_cpf', 'label' => 'Com CPF', 'value' => '0'],
        ['key' => 'sem_cpf', 'label' => 'Sem CPF', 'value' => '0'],
    ];

    $tableColumns = [
        ['key' => 'checkbox', 'label' => '', 'width' => 'w-10px pe-2', 'orderable' => false],
        ['key' => 'nome', 'label' => 'Nome', 'width' => 'min-w-200px', 'orderable' => true],
        ['key' => 'documento', 'label' => 'CPF', 'width' => 'min-w-130px', 'orderable' => false],
        ['key' => 'telefone', 'label' => 'Telefone', 'width' => 'min-w-120px', 'orderable' => false],
        ['key' => 'email', 'label' => 'E-mail', 'width' => 'min-w-150px', 'orderable' => false],
        ['key' => 'cidade', 'label' => 'Cidade / UF', 'width' => 'min-w-120px', 'orderable' => false],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-end min-w-80px', 'orderable' => false],
    ];

    $tableIdFinal = 'kt_parceiros_' . $key . '_table';
    $filterId = $tableIdFinal;

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
     data-tab="{{ $key }}">

    <x-tenant.segmented-tabs-toolbar
        :tabs="$mappedTabs"
        :active="'todos'"
        id="status-tabs-{{ $tableIdFinal }}"
        :tableId="$tableIdFinal"
        :filterId="$filterId"
        :periodLabel="null"
        :accountOptions="[]"
        :showAccountFilter="false"
        :showMoreFilters="false"
        :moreFilters="[]">

        <x-slot:actionsLeft>
            <span class="text-muted fs-7 mx-2" id="selected-count-{{ $tableIdFinal }}">0 registro(s) selecionado(s)</span>
        </x-slot>

        <x-slot:actionsRight>
            {{-- Reservado para ações em lote --}}
        </x-slot>

        <x-slot:panes>
            @foreach ($mappedTabs as $tab)
                <div class="tab-pane fade {{ $tab['key'] === 'todos' ? 'show active' : '' }}"
                    id="{{ $tab['paneId'] }}" role="tabpanel">
                </div>
            @endforeach
        </x-slot:panes>

        <x-slot:tableContent>
            <!--begin::Skeleton Loading-->
            <x-tenant-datatable-skeleton :tableId="$tableIdFinal" :columns="$tableColumns" />
            <!--end::Skeleton Loading-->

            <!--begin::Table Wrapper-->
            <div id="table-wrapper-{{ $tableIdFinal }}" class="d-none mt-4">
                <table class="table align-middle table-striped table-row-dashed fs-6 gy-5 mt-7"
                    id="{{ $tableIdFinal }}">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-4">
                            @foreach ($tableColumns as $column)
                                @if ($column['key'] === 'checkbox')
                                    <th class="{{ $column['width'] ?? 'w-10px pe-2' }}">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox"
                                                data-kt-check="true"
                                                data-kt-check-target="#{{ $tableIdFinal }} .form-check-input"
                                                value="1" />
                                        </div>
                                    </th>
                                @elseif($column['key'] === 'acoes')
                                    <th class="{{ $column['width'] ?? 'text-end min-w-80px' }}">{{ $column['label'] }}</th>
                                @else
                                    <th class="{{ $column['width'] ?? '' }}">{{ $column['label'] }}</th>
                                @endif
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold"></tbody>
                </table>
            </div>
            <!--end::Table Wrapper-->
        </x-slot:tableContent>
    </x-tenant.segmented-tabs-toolbar>
</div>
