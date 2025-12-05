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
                            Patrimônio</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Ínicio</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('patrimonio.imoveis') }}" class="text-muted text-hover-primary">
                                    Patrimônio</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                Ímoveis
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->

                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Navbar-->
                    @include('app.components.card-body-patrimonio', ['active' => true])
                    <!--end::Navbar-->

                    <!--begin::Table-->
                    <div class="card card-flush mt-6 mt-xl-9">
                        <!--begin::Card header-->
                        <div class="card-header mt-5">
                            <!--begin::Card title-->
                            <div class="card-title flex-column">
                                <h3 class="fw-bold mb-1">Lista Patrimônio Foreiro</h3>
                                {{-- <div class="fs-6 text-gray-400">Total $260,300 sepnt so far</div> --}}
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar my-1">
                                <!--begin::Select-->
                                <div class="me-6 my-1">
                                    <select id="kt_filter_year" name="year" data-control="select2"
                                        data-hide-search="true"
                                        class="w-125px form-select form-select-solid form-select-sm">
                                        <option value="All" selected="selected">All time</option>
                                        <option value="thisyear">This year</option>
                                        <option value="thismonth">This month</option>
                                        <option value="lastmonth">Last month</option>
                                        <option value="last90days">Last 90 days</option>
                                    </select>
                                </div>
                                <!--end::Select-->
                                <!--begin::Select-->
                                <div class="me-4 my-1">
                                    <select id="kt_filter_orders" name="orders" data-control="select2"
                                        data-hide-search="true"
                                        class="w-125px form-select form-select-solid form-select-sm">
                                        <option value="All" selected="selected">All Orders</option>
                                        <option value="Approved">Approved</option>
                                        <option value="Declined">Declined</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="In Transit">In Transit</option>
                                    </select>
                                </div>
                                <!--end::Select-->
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative me-4">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-3 position-absolute ms-3">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                height="2" rx="1" transform="rotate(45 17.0365 15.1223)"
                                                fill="currentColor" />
                                            <path
                                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" id="kt_filter_search"
                                        class="form-control form-control-solid form-select-sm w-150px ps-9"
                                        placeholder="Pesquisar..." />
                                </div>
                                <!--end::Search-->
                                <div class=" my-1">
                                    <a href="{{ route('patrimonio.imprimir', request()->query()) }}" target="_blank"
                                        class="btn btn-sm btn-primary me-3">
                                        <i class="bi bi-printer"></i> Imprimir PDF
                                    </a>
                                </div>
                            </div>
                            <!--begin::Card toolbar-->
                        </div>
                        <!--end::Card header-->
                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table container-->
                            <div class="table-responsive">
                                <!--begin::Table-->
                                <table id="kt_profile_overview_table"
                                    class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                                    <!--begin::Head-->
                                    <thead class="fs-7 text-gray-400 text-uppercase">
                                        <tr>
                                            <th class="min-w-50px">RID</th>
                                            <th class="min-w-150px">Manager</th>
                                            <th class="min-w-90px">Cidade</th>
                                            <th class="min-w-90px">Bairro</th>
                                            <th class="min-w-150px">Date</th>
                                            <th class="min-w-50px text-end">Details</th>
                                        </tr>
                                    </thead>
                                    <!--end::Head-->
                                    <!--begin::Body-->
                                    <tbody class="fs-6">

                                    </tbody>

                                    <!--end::Body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Table container-->
                            <!--begin::Pagination-->
                            <div class="d-flex justify-content-end mt-5">
                            </div>
                            <!--end::Pagination-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->


    <!--begin::Modals-->
    @include('app.components.modals.patrimonio.modal_imovel')
    <!--end::Modals-->
</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="/assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/patrimonio/imovel.js"></script>
<script src="/assets/js/custom/utilities/modals/new-address-imovel.js"></script>

<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
<script src="/assets/js/custom/utilities/modals/new-imovel.js"></script>
<script src="/assets/plugins/custom/jquery.mask/jquery.mask.min.js"></script>
<script src="/assets/js/toasts.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
