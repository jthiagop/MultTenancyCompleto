@php
    $key = 'clientes';
    $tableId = 'kt_parceiros_clientes_table';
    
    $tableColumns = [
        ['key' => 'nome', 'label' => 'Nome', 'width' => 'min-w-200px'],
        ['key' => 'documento', 'label' => 'CPF', 'width' => 'min-w-130px'],
        ['key' => 'telefone', 'label' => 'Telefone', 'width' => 'min-w-120px'],
        ['key' => 'email', 'label' => 'E-mail', 'width' => 'min-w-150px'],
        ['key' => 'cidade', 'label' => 'Cidade / UF', 'width' => 'min-w-120px'],
        ['key' => 'acoes', 'label' => 'Ações', 'width' => 'text-end min-w-80px'],
    ];
@endphp

<div class="tab-pane fade show active" id="kt_tab_parceiros_{{ $key }}" role="tabpanel"
     data-tab="{{ $key }}" data-table-id="{{ $tableId }}">
    
    <!--begin::Card-->
    <div class="card card-flush mt-4">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center position-relative my-1">
                    <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                    <input type="text" class="form-control form-control-solid w-250px ps-12 parceiro-search"
                           data-table="{{ $tableId }}"
                           placeholder="Buscar cliente..." />
                </div>
            </div>
            <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                <span class="text-muted fs-7" id="count-{{ $tableId }}"></span>
            </div>
        </div>
        <div class="card-body pt-0">
            <!--begin::Table-->
            <table class="table align-middle table-striped table-row-dashed fs-6 gy-5" id="{{ $tableId }}">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-4">
                        @foreach ($tableColumns as $column)
                            <th class="{{ $column['width'] ?? '' }}">{{ $column['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    <!-- Dados carregados via DataTables -->
                </tbody>
            </table>
            <!--end::Table-->
        </div>
    </div>
    <!--end::Card-->
</div>
