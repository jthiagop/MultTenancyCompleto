<x-tenant-app-layout
    pageTitle="Notas Fiscais de Entrada"
    :breadcrumbs="array(
        array('label' => 'Financeiro', 'url' => route('caixa.index')),
        array('label' => 'Notas Fiscais')
    )">
    <!--begin::Main-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid mt-7">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card">
                <!--begin::Card header-->
                <div class="card-header border-0 pt-6">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <i class="fa-solid fa-search"></i>
                            </span>
                            <input type="text" data-kt-customer-table-filter="search"
                                class="form-control form-control-solid w-250px ps-15" placeholder="Buscar Notas Fiscais de Entrada" />
                        </div>
                        <!--end::Search-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Toolbar-->
                        <div class="d-flex align-items-center gap-3 flex-nowrap" data-kt-customer-table-toolbar="base">
                            <!--begin::Filter Status-->
                            <div class="w-150px">
                                <select class="form-select form-select-solid" data-control="select2"
                                    data-hide-search="true" data-placeholder="Status"
                                    data-kt-ecommerce-order-filter="status">
                                    <option></option>
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="locked">Locked</option>
                                </select>
                            </div>
                            <!--end::Filter Status-->

                            <!--begin::Período Inicial-->
                            <div class="position-relative">
                                <div class="position-relative d-flex align-items-center">
                                    <span class="svg-icon svg-icon-2 position-absolute ms-4">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </span>
                                    <input class="form-control form-control-solid ps-12"
                                        placeholder="Período Inicial"
                                        name="data_inicial"
                                        id="nfe_data_inicial" />
                                </div>
                            </div>
                            <!--end::Período Inicial-->

                            <!--begin::Período Final-->
                            <div class="position-relative">
                                <div class="position-relative d-flex align-items-center">
                                    <span class="svg-icon svg-icon-2 position-absolute ms-4">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </span>
                                    <input class="form-control form-control-solid ps-12"
                                        placeholder="Período Final"
                                        name="data_final"
                                        id="nfe_data_final" />
                                </div>
                            </div>
                            <!--end::Período Final-->

                            <!--begin::Buscar Button-->
                            <button type="button" class="btn btn-primary btn-sm" id="kt_nfe_buscar_btn">
                                <i class="bi bi-search"></i> Buscar
                            </button>
                            <!--end::Buscar Button-->
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Group actions-->
                        <div class="d-flex justify-content-end align-items-center d-none"
                            data-kt-customer-table-toolbar="selected">
                            <div class="fw-bold me-5">
                                <span class="me-2" data-kt-customer-table-select="selected_count"></span>Selected
                            </div>
                            <button type="button" class="btn btn-danger btn-sm"
                                data-kt-customer-table-select="delete_selected">Delete Selected</button>
                        </div>
                        <!--end::Group actions-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                        <!--begin::Table head-->
                        <thead>
                            <!--begin::Table row-->
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="w-10px pe-2">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input" type="checkbox" data-kt-check="true"
                                            data-kt-check-target="#kt_customers_table .form-check-input"
                                            value="1" />
                                    </div>
                                </th>
                                <th class="min-w-100px">Data Emissão</th>
                                <th class="min-w-150px">Chave de Acesso</th>
                                <th class="min-w-200px">Emitente</th>
                                <th class="min-w-100px">Valor</th>
                                <th class="min-w-100px">Status Sistema</th>
                                <th class="min-w-100px">Ambiente</th>
                                <th class="text-end min-w-100px">Ações</th>
                            </tr>
                            <!--end::Table row-->
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-600">
                            @forelse ($documentos as $doc)
                                <tr>
                                    <!--begin::Checkbox-->
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="{{ $doc->id }}" />
                                        </div>
                                    </td>
                                    <!--end::Checkbox-->
                                    <!--begin::Data Emissão-->
                                    <td>
                                        {{ $doc->data_emissao ? $doc->data_emissao->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <!--end::Data Emissão-->
                                    <!--begin::Chave de Acesso-->
                                    <td>
                                        <span class="badge badge-light fw-bold" title="{{ $doc->chave_acesso }}">
                                            {{ substr($doc->chave_acesso, 0, 4) }}...{{ substr($doc->chave_acesso, -4) }}
                                        </span>
                                    </td>
                                    <!--end::Chave de Acesso-->
                                    <!--begin::Emitente-->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold mb-1">{{ $doc->emitente_nome ?? 'Sem Nome' }}</span>
                                            <span class="text-gray-500">{{ $doc->emitente_cnpj }}</span>
                                        </div>
                                    </td>
                                    <!--end::Emitente-->
                                    <!--begin::Valor-->
                                    <td>
                                        R$ {{ number_format($doc->valor_total, 2, ',', '.') }}
                                    </td>
                                    <!--end::Valor-->
                                    <!--begin::Status Sistema-->
                                    <td>
                                        @if ($doc->status_sistema == 'novo')
                                            <span class="badge badge-light-warning">Novo (Resumo)</span>
                                        @elseif($doc->status_sistema == 'downloaded')
                                            <span class="badge badge-light-success">XML Baixado</span>
                                        @else
                                            <span class="badge badge-light-primary">{{ ucfirst($doc->status_sistema) }}</span>
                                        @endif
                                    </td>
                                    <!--end::Status Sistema-->
                                    <!--begin::Ambiente-->
                                    <td>
                                        @if ($doc->tp_amb == 1)
                                            <span class="badge badge-light-success">Produção</span>
                                        @else
                                            <span class="badge badge-light-warning">Homologação</span>
                                        @endif
                                    </td>
                                    <!--end::Ambiente-->
                                    <!--begin::Action-->
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                        </a>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Detalhes</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            @if($doc->xml_completo)
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3">Baixar XML</a>
                                                </div>
                                            @endif
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </td>
                                    <!--end::Action-->
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-10">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fa-solid fa-file-invoice fs-3x text-gray-400 mb-4"></i>
                                            <span class="fs-5 fw-semibold">Nenhuma nota fiscal encontrada.</span>
                                            <span class="fs-7 text-gray-500 mt-2">Use o filtro de data acima para buscar notas fiscais.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
    <!--end::Content-->

</x-tenant-app-layout>

<script src="/tenancy/assets/js/custom/apps/nfe-entrada/listing.js"></script>
