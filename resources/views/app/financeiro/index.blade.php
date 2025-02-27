<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>
<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Lançamentos Financeiros</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Financeiro</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <!--end::Primary button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <!--begin::Referral program-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Body-->
                        <div class="card-body py-10">
                                <!--begin::Card title-->
                                <div class="card-title mb-10">
                                    <h2>Modulos de Movimentação Financeira</h2>
                                </div>
                                <!--end::Card title-->
                            <!--begin::Row-->
                            <div class="fv-row">
                                <div class="row">

                                    <!--begin::Col-->
                                    <div class="col-6 col-sm-6 col-lg-6 hover-elevate-up ">
                                        <a href="{{ route('caixa.list', ['tab' => 'lancamento']) }}"
                                            class=" btn-outline btn-outline-dashed btn-active-light d-flex align-items-center">
                                            <!--begin::Option-->
                                            <!--begin::Notice-->
                                            <div
                                                class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                                <!--begin::Svg Icon | path: icons/duotune/communication/com005.svg-->
                                                <span class="svg-icon svg-icon-5x me-5">
                                                        <img width="50" height="50" src="/assets/media/png/Cash_Register-transformed.webp" alt="">
                                                </span>
                                                <!--end::Svg Icon-->
                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                    <!--begin::Content-->
                                                    <div class="mb-3 mb-md-0 fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">Lançamento de Caixa</h4>
                                                        <div class="text-muted fw-semibold fs-6">registre todas as
                                                            transações
                                                            em
                                                            espécie</div>
                                                    </div>
                                                    <!--end::Content-->
                                                    <!--begin::Action-->
                                                    <a href="{{ route('caixa.list') }}"
                                                        class="btn btn-primary px-6 align-self-center ">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="8" y="9" width="3" height="10"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect opacity="0.5" x="13" y="5" width="3"
                                                                    height="14" rx="1.5"
                                                                    fill="currentColor" />
                                                                <rect x="18" y="11" width="3" height="8"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect x="3" y="13" width="3" height="6"
                                                                    rx="1.5" fill="currentColor" />
                                                            </svg>
                                                        </span>

                                                        Movimentação </a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                        </a>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-6 col-sm-6  hover-elevate-up ">
                                        <a href="{{ route('banco.list', ['tab' => 'lancamento']) }}"
                                            class=" btn-outline btn-outline-dashed btn-active-light d-flex align-items-center">
                                            <!--begin::Option-->
                                            <!--begin::Notice-->
                                            <div
                                                class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                                <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                                <span class="svg-icon svg-icon-5x me-5">
                                                    <img width="50" height="50" src="/assets/media/png/banco3.png" alt="">
                                            </span>

                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                    <!--begin::Content-->
                                                    <div class="mb-3 mb-md-0 fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">Lançamentos Bancários</h4>
                                                        <div class="text-muted fw-semibold fs-6">Transações realizadas
                                                            com
                                                            contas bancárias</div>
                                                    </div>
                                                    <!--end::Content-->
                                                    <!--begin::Action-->
                                                    <a href="{{ route('banco.list') }}"
                                                        class="btn btn-primary px-6 align-self-center ">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="8" y="9" width="3" height="10"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect opacity="0.5" x="13" y="5" width="3"
                                                                    height="14" rx="1.5"
                                                                    fill="currentColor" />
                                                                <rect x="18" y="11" width="3" height="8"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect x="3" y="13" width="3" height="6"
                                                                    rx="1.5" fill="currentColor" />
                                                            </svg>
                                                        </span>

                                                        Movimentação </a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                        </a>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->

                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Referral program-->
                    <!--begin::Input group-->


                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_ecommerce_customer_overview" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <div class="card-header border-0">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2>Histórico de Transações</h2>
                                </div>
                                <!--end::Card title-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0 pb-5">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed gy-4"
                                    id="kt_table_customers_payment">
                                    <!--begin::Table head-->
                                    <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                        <!--begin::Table row-->
                                        <tr class="text-start text-muted text-uppercase gs-0">
                                            <th class="min-w-30px">N. ID</th>
                                            <th class="min-w-10px">Copetencia</th>
                                            <th class="min-w-10px">Entidade</th>
                                            <th class="min-w-100px">Lancamento Padrao</th>
                                            <th class="min-w-100px">Categoria</th>
                                            <th class="min-w-50px">NF</th>
                                            <th class="min-w-100px">Tipo</th>
                                            <th class="min-w-150px">Valor</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fs-6 fw-semibold text-gray-600">
                                        @foreach ($transacoesFinanceiras as $transacaoFinanceira)
                                            <!--begin::Table row-->
                                            <tr>
                                                <!--begin::order=-->
                                                <td>
                                                    <a href="#"
                                                        class="text-gray-600 text-hover-primary mb-1">#{{ $transacaoFinanceira->id }}</a>
                                                </td>
                                                <!--end::order=-->
                                                <!--begin::Status=-->
                                                <td>
                                                    <span class="badge badge-light-success">
                                                        {{ \Carbon\Carbon::parse($transacaoFinanceira->data_competencia)->format('d M, Y') }}
                                                    </span>
                                                </td>
                                                <!--end::Status=-->
                                                <!--begin::Amount=-->
                                                <td>{{ $transacaoFinanceira->entidadeFinanceira->nome }}</td>
                                                <!--end::Amount=-->
                                                <!--begin::Amount=-->
                                                <td>{{ $transacaoFinanceira->lancamentoPadrao->description }}</td>
                                                <!--end::Amount=-->
                                                <!--begin::Amount=-->
                                                <td>{{ $transacaoFinanceira->lancamentoPadrao->category }}</td>
                                                <!--end::Amount=-->
                                                <!--begin::Amount=-->
                                                <td>
                                                    {!! $transacaoFinanceira->comprovacao_fiscal === 1
                                                        ? '<i class="fas fa-check-circle text-success" title="Tem comprovação Fiscal"></i>'
                                                        : '<i class="bi bi-x-circle-fill text-danger" title="Não tem comprovação ciscal"></i>' !!}
                                                </td>
                                                                                                <!--end::Amount=-->
                                                <!--begin::Amount=-->
                                                <td>
                                                    <span
                                                        class="badge {{ $transacaoFinanceira->tipo === 'entrada' ? 'badge-light-success' : 'badge-light-danger' }}">
                                                        {{ ucfirst($transacaoFinanceira->tipo) }}
                                                    </span>
                                                </td>
                                                <!--end::Amount=-->
                                                <!--begin::Amount=-->
                                                <td> <p class="fw-bold">R$ {{ number_format($transacaoFinanceira->valor, 2, ',', '.') }}
                                                </p></td>
                                                <!--end::Amount=-->
                                            </tr>
                                            <!--end::Table row-->
                                        @endforeach

                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                                <div class="mt-5">
                                    {{ $transacoesFinanceiras->links('vendor.pagination.custom-pagination') }}
                                </div>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->
                    <!--end::Card-->

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Modal - Upgrade plan-->

        <!--end::Modal - Upgrade plan-->
        @include('app.components.modals.lancar-caixa')
        @include('app.components.modals.lancar-banco')

    </div>
    <!--end:::Main-->




</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<script src="/assets/js/scripts.bundle.js"></script>

<script src="/assets/js/custom/utilities/modals/financeiro/new-caixa.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/new-banco.js"></script>

<!--end::Custom Javascript-->
<script src="/assets/js/custom/apps/lancamento/excluirCaixa.js"></script>

<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/user-management/users/list/table.js"></script>
<script src="/assets/js/custom/apps/user-management/users/list/export-users.js"></script>
<script src="/assets/js/custom/apps/user-management/users/list/add.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('.delete-link');

        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const id = this.getAttribute('data-id');
                const form = document.getElementById(`delete-form-${id}`);

                Swal.fire({
                    title: 'Você tem certeza?',
                    text: 'Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, exclua!',
                    cancelButtonText: 'Não, cancele',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

<script>
    var lpsData = @json($lps);
</script>
