@php
    $key = 'secretary';
    $parentId = 'kt_tab_secretary';

    // Colunas para membros religiosos
    $tableColumns = [
        ['key' => 'checkbox', 'label' => '', 'width' => 'w-10px pe-2', 'orderable' => false],
        ['key' => 'nome', 'label' => 'Nome', 'width' => 'min-w-200px', 'orderable' => true],
        ['key' => 'provincia', 'label' => 'Província', 'width' => 'min-w-100px', 'orderable' => true],
        ['key' => 'funcao', 'label' => 'Função', 'width' => 'min-w-80px', 'orderable' => false],
        ['key' => 'etapa_atual', 'label' => 'Etapa Atual', 'width' => 'min-w-120px', 'orderable' => false],
        ['key' => 'data_chave', 'label' => 'Ordenação/Profissão', 'width' => 'min-w-100px', 'orderable' => true],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-end min-w-50px', 'orderable' => false],
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
    $defaultOrder = [[$defaultOrderCol, 'asc']];

    // Mapear as tabs já definidas para o formato do segmented-tabs-toolbar
    $tabs = [
        ['key' => 'todos', 'label' => 'Todos', 'active' => true, 'filter' => null],
        ['key' => 'presbiteros', 'label' => 'Presbíteros', 'filter' => ['role_slug' => 'presbitero']],
        ['key' => 'diaconos', 'label' => 'Diáconos', 'filter' => ['role_slug' => 'diacono']],
        ['key' => 'irmaos', 'label' => 'Irmãos', 'filter' => ['role_slug' => 'irmao']],
        ['key' => 'votos_simples', 'label' => 'Votos Simples', 'filter' => ['profession' => 'temporaria']],
    ];

    $mappedTabs = array_map(function ($tab) use ($tableIdFinal) {
        return [
            'key' => $tab['key'],
            'label' => $tab['label'],
            'count' => '0', // Será atualizado via AJAX
            'paneId' => "pane-{$tab['key']}-{$tableIdFinal}",
            'filter' => $tab['filter'],
        ];
    }, $tabs);
@endphp

<x-tenant-app-layout pageTitle="Secretaria" :breadcrumbs="[['label' => 'Secretaria']]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">

            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid show active"
                data-pane-id="secretary-pane" data-table-id="{{ $tableIdFinal }}" data-filter-id="{{ $filterId }}"
                data-key="{{ $key }}" data-tipo="secretary" data-stats-url="{{ route('secretary.stats') }}"
                data-data-url="{{ route('secretary.data') }}" data-columns-json="{{ json_encode($columnsForJson) }}"
                data-default-order="{{ json_encode($defaultOrder) }}" data-page-length="50">

                <div class="d-flex align-items-center gap-2 mb-5">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_member">
                        <i class="fa-solid fa-user-plus fs-6"></i>
                        Novo Membro
                    </button>
                </div>

                <!--begin::Drawer Cadastro Membro-->
                @include('app.modules.secretary.partials.drawer-member-form', ['tableId' => $tableIdFinal])
                <!--end::Drawer Cadastro Membro-->

                <!--begin::Modal Cadastro Membro-->
                @include('app.modules.secretary.partials.modal-member-form', [
                    'tableId' => $tableIdFinal,
                    'formationStages' => $formationStages
                ])
                <!--end::Modal Cadastro Membro-->

                <x-tenant.segmented-tabs-toolbar :tabs="$mappedTabs" :active="request('status')"
                    id="status-tabs-{{ $tableIdFinal }}" :tableId="$tableIdFinal" :filterId="$filterId" :periodLabel="null"
                    :showAccountFilter="false" :showMoreFilters="true" :moreFilters="[]">

                    <x-slot:actionsLeft>
                        <span class="text-muted fs-7 mx-2" id="selected-count-{{ $tableIdFinal }}">0 membro(s)
                            selecionado(s)</span>
                    </x-slot>

                    <x-slot:actionsRight>

                    </x-slot>

                    <x-slot:panes>
                        @foreach ($mappedTabs as $tab)
                            <div class="tab-pane fade {{ request('status') === $tab['key'] || (request('status') === null && $loop->first) ? 'show active' : '' }}"
                                id="{{ $tab['paneId'] }}" role="tabpanel"
                                data-filter-json="{{ json_encode($tab['filter']) }}">
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
                            <table class="table align-middle table-striped table-row-dashed fs-6 gy-3 mt-7"
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
                    </x-slot:tableContent>
                </x-tenant.segmented-tabs-toolbar>

            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->
    </div>

    <!--begin::Scripts Secretaria-->
    @push('scripts')
    <script src="{{ url('/js/domusia/secretary.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar módulo da secretaria
            const secretary = new DomusiaSecretary({
                modalId: '#kt_modal_member',
                formId: '#kt_modal_member_form',
                submitBtnId: '#kt_modal_new_target_submit',
                cancelBtnId: '#kt_modal_new_target_cancel',
                storeUrl: '{{ route("secretary.store") }}',
                editUrl: '{{ route("secretary.edit", "__ID__") }}',
                updateUrl: '{{ route("secretary.update", "__ID__") }}',
                deleteUrl: '{{ route("secretary.destroy", "__ID__") }}',
                showUrl: '{{ route("secretary.show", "__ID__") }}',
                statsUrl: '{{ route("secretary.stats") }}',
                csrfToken: '{{ csrf_token() }}',
                stageOrders: {
                    @foreach ($formationStages as $stage)
                    '{{ $stage->id }}': {{ $stage->sort_order }},
                    @endforeach
                }
            });
        });
    </script>
    @endpush
    <!--end::Scripts Secretaria-->
</x-tenant-app-layout>
