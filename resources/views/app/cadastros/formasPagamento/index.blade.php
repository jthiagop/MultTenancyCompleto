<meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Token CSRF -->

<x-tenant-app-layout pageTitle="Cadastro de Forma de Pagamento" :breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Formas de Pagamento']]">
    {{-- *** Modal Add Forma de Pagamento *** --}}
    @include('app.components.modals.company.formaPagamento')

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid mt-5" >
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Card-->
            <div class="card pt-4 mb-6 mb-xl-9">
                <!--begin::Card header-->
                <div class="card-header border-0">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <h2>Formas de Pagamentos</h2>
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar">
                        <!--begin::Filter-->
                        <button type="button" class="btn btn-sm btn-flex btn-light-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_payment">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen035.svg-->
                            <span class="svg-icon svg-icon-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5"
                                        fill="currentColor" />
                                    <rect x="10.8891" y="17.8033" width="12" height="2" rx="1"
                                        transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                                    <rect x="6.01041" y="10.9247" width="12" height="2" rx="1"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->Add Forma Pagamento </button>
                        <!--end::Filter-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Card body-->
                <div class="card-body pt-0 pb-5">
                    <!--begin::Table-->
                    <table class="table align-middle table-row-dashed gy-5" id="kt_table_customers_payment">
                        <!--begin::Table head-->
                        <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                            <tr class="text-start text-muted text-uppercase gs-0">
                                <th class="min-w-100px">Nome</th>
                                <th>Código</th>
                                <th>Status</th>
                                <th>Taxa</th>
                                <th>Observação</th>
                                <th class="min-w-100px">Data de Criação</th>
                                <th class="text-end min-w-100px pe-4">Ações</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fs-6 fw-semibold text-gray-600">
                            @foreach ($formasPagamento as $forma)
                                <tr>
                                    <!-- Nome -->
                                    <td>{{ $forma->nome }}</td>
                                    <td>{{ $forma->codigo }}</td>
                                    <!-- Status -->
                                    <td>
                                        <span
                                            class="badge {{ $forma->ativo ? 'badge-light-success' : 'badge-light-danger' }}">
                                            {{ $forma->ativo ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <!-- Taxa -->
                                    <td>
                                        @if ($forma->tipo_taxa === 'valor_fixo')
                                            R$ {{ $forma->taxa }}
                                        @else
                                            {{ $forma->taxa }}%
                                        @endif
                                    </td>
                                    <!-- Data de Criação -->
                                    <td>{{ $forma->observacao }}</td>
                                    <td>{{ $forma->created_at->format('d/m/Y H:i') }}</td>

                                    <!-- Ações -->
                                    <td class="pe-0 text-end">
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
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                            data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Editar</a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Excluir</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
        <!--end::Contacts App- Add New Contact-->
    </div>
    <!--end::Content container-->


</x-tenant-app-layout>

<script src="/tenancy/assets/js/custom/apps/contacts/edit-contact.js"></script>
<!--begin::Vendors Javascript(used for this page only)-->
<script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>

<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/tenancy/assets/js/custom/apps/company/add-formaPagamento.js"></script>
<script src="/tenancy/assets/js/custom/apps/customers/view/payment-table.js"></script>
