<x-tenant-app-layout>
    {{-- @include('app.layouts.subnav.projects', [
        'activeTab' => 'projects',
        'showAccountDropdown' => true,
        'showToolsDropdown' => true
    ]) --}}
    <!--begin::Modal - Support Center - Create Ticket-->
    @include('app.components.modals.cadastro_fiel')
    <!--end::Modal - Support Center - Create Ticket-->
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Cadastro de Fiéis</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Cadastro de Fiéis</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Export-->
                        <button type="button" class="btn btn-sm btn-light-primary me-3" data-bs-toggle="modal"
                            data-bs-target="#kt_customers_export_modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                            <span class="svg-icon svg-icon-2">
                                <i class="fa-solid fa-file-export"></i>
                            </span>
                            <!--end::Svg Icon-->Export</button>
                        <!--end::Export-->
                        <!--begin::Add user-->
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_new_ticket">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                            <span class="svg-icon svg-icon-2">
                                <i class="fa-solid fa-user-plus"></i> Novo Fiél
                            </span>
                            <!--end::Svg Icon-->
                        </button>
                        <!--end::Add user-->
                    </div>
                    <!--end::Actions-->
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!--begin::Graficos-->
                    <div class="row g-5 g-xl-8">
                        <!-- HOMENS -->
                        <div class="col-xl-3">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #3E97FF;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <!--begin::Card body-->
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-person fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Content-->
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                {{ $totalHomens }} / {{ $porcentagemHomens }}%
                                            </div>
                                            <div class="text-white fw-bold fs-5">HOMENS</div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--begin::Progress-->
                                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                                        <div
                                            class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                            <span>{{ $totalHomens }} de {{ $totalFieis }}</span>
                                            <span>{{ $porcentagemHomens }}%</span>
                                        </div>
                                        <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                            <div class="bg-white rounded h-8px" role="progressbar"
                                                style="width: {{ $porcentagemHomens }}%;"
                                                aria-valuenow="{{ $porcentagemHomens }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <!--end::Progress-->
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>

                        <!-- MULHERES -->
                        <div class="col-xl-3">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #F1416C;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <!--begin::Card body-->
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-person-dress fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Content-->
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                {{ $totalMulheres }} / {{ $porcentagemMulheres }}%
                                            </div>
                                            <div class="text-white fw-bold fs-5">MULHERES</div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--begin::Progress-->
                                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                                        <div
                                            class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                            <span>{{ $totalMulheres }} de {{ $totalFieis }}</span>
                                            <span>{{ $porcentagemMulheres }}%</span>
                                        </div>
                                        <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                            <div class="bg-white rounded h-8px" role="progressbar"
                                                style="width: {{ $porcentagemMulheres }}%;"
                                                aria-valuenow="{{ $porcentagemMulheres }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <!--end::Progress-->
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>

                        <!-- DIZIMISTAS -->
                        <div class="col-xl-3">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #FFC700;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <!--begin::Card body-->
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-hand-holding-heart fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Content-->
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                {{ $totalDizimistas }} / {{ $porcentagemDizimistas }}%
                                            </div>
                                            <div class="text-white fw-bold fs-5">DIZIMISTAS</div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--begin::Progress-->
                                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                                        <div
                                            class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                            <span>{{ $totalDizimistas }} de {{ $totalFieis }}</span>
                                            <span>{{ $porcentagemDizimistas }}%</span>
                                        </div>
                                        <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                            <div class="bg-white rounded h-8px" role="progressbar"
                                                style="width: {{ $porcentagemDizimistas }}%;"
                                                aria-valuenow="{{ $porcentagemDizimistas }}" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <!--end::Progress-->
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>

                        <!-- FIEIS (TOTAL) -->
                        <div class="col-xl-3">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #50CD89;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <!--begin::Card body-->
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <!--begin::Icon-->
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-users fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <!--end::Icon-->
                                        <!--begin::Content-->
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                {{ $totalFieis }}
                                            </div>
                                            <div class="text-white fw-bold fs-5">FIEIS</div>
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--begin::Progress-->
                                    <div class="d-flex align-items-center flex-column mt-3 w-100">
                                        <div
                                            class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                                            <span>Total Cadastrado</span>
                                            <span>100%</span>
                                        </div>
                                        <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                            <div class="bg-white rounded h-8px" role="progressbar"
                                                style="width: 100%;" aria-valuenow="100" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                    <!--end::Progress-->
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                    <!--end::Graficos-->
                </div>
                <!--end::Container-->
                <!--charts -->
                <div class="app-container container-fluid">
                    <div class="row g-5 g-xl-8 mb-5 mb-xl-10">
                        <!-- Gráfico 1: Fiéis por Faixa Etária -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Fiéis por Faixa Etária</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="kt_chart_faixa_etaria" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Gráfico 2: Por Estado Civil -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Por Estado Civil</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="kt_chart_estado_civil" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- Gráfico 3: Por Profissão -->
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Por Profissão</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="kt_chart_profissao" class="mh-400px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::charts -->
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 pt-6">
                            <!--begin::Card title-->
                            <div class="card-title">
                                <!--begin::Search-->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                    <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <input type="text" data-kt-customer-table-filter="search"
                                        class="form-control form-control-solid w-250px ps-15"
                                        placeholder="Buscar Fiél" />
                                </div>
                                <!--end::Search-->
                            </div>
                            <!--begin::Card title-->
                            <!--begin::Card toolbar-->
                            <div class="card-toolbar">
                                <!--begin::Toolbar-->
                                <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                    <!--begin::Filter-->
                                    <button type="button" class="btn btn-sm btn-light-primary me-3"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <i class="fa-solid fa-filter"></i>
                                        </span>
                                        <!--end::Svg Icon-->Filter</button>
                                    <!--begin::Menu 1-->
                                    <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px"
                                        data-kt-menu="true" id="kt-toolbar-filter">
                                        <!--begin::Header-->
                                        <div class="px-7 py-5">
                                            <div class="fs-4 text-dark fw-bold">Filter Options</div>
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Separator-->
                                        <div class="separator border-gray-200"></div>
                                        <!--end::Separator-->
                                        <!--begin::Content-->
                                        <div class="px-7 py-5">
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <!--begin::Label-->
                                                <label class="form-label fs-5 fw-bold mb-3">Month:</label>
                                                <!--end::Label-->
                                                <!--begin::Input-->
                                                <select class="form-select form-select-solid fw-bold"
                                                    data-kt-select2="true" data-placeholder="Select option"
                                                    data-allow-clear="true" data-kt-customer-table-filter="month"
                                                    data-dropdown-parent="#kt-toolbar-filter">
                                                    <option></option>
                                                    <option value="aug">August</option>
                                                    <option value="sep">September</option>
                                                    <option value="oct">October</option>
                                                    <option value="nov">November</option>
                                                    <option value="dec">December</option>
                                                </select>
                                                <!--end::Input-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Input group-->
                                            <div class="mb-10">
                                                <!--begin::Label-->
                                                <label class="form-label fs-5 fw-bold mb-3">Payment Type:</label>
                                                <!--end::Label-->
                                                <!--begin::Options-->
                                                <div class="d-flex flex-column flex-wrap fw-bold"
                                                    data-kt-customer-table-filter="payment_type">
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="all" checked="checked" />
                                                        <span class="form-check-label text-gray-600">All</span>
                                                    </label>
                                                    <!--end::Option-->
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid mb-3 me-5">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="visa" />
                                                        <span class="form-check-label text-gray-600">Visa</span>
                                                    </label>
                                                    <!--end::Option-->
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid mb-3">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="mastercard" />
                                                        <span class="form-check-label text-gray-600">Mastercard</span>
                                                    </label>
                                                    <!--end::Option-->
                                                    <!--begin::Option-->
                                                    <label
                                                        class="form-check form-check-sm form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="radio"
                                                            name="payment_type" value="american_express" />
                                                        <span class="form-check-label text-gray-600">American
                                                            Express</span>
                                                    </label>
                                                    <!--end::Option-->
                                                </div>
                                                <!--end::Options-->
                                            </div>
                                            <!--end::Input group-->
                                            <!--begin::Actions-->
                                            <div class="d-flex justify-content-end">
                                                <button type="reset"
                                                    class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-customer-table-filter="reset">Reset</button>
                                                <button type="submit" class="btn btn-sm btn-primary"
                                                    data-kt-menu-dismiss="true"
                                                    data-kt-customer-table-filter="filter">Apply</button>
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Content-->
                                    </div>
                                    <!--end::Menu 1-->
                                    <!--end::Filter-->
                                </div>
                                <!--end::Toolbar-->
                                <!--begin::Group actions-->
                                <div class="d-flex justify-content-end align-items-center d-none"
                                    data-kt-customer-table-toolbar="selected">
                                    <div class="fw-bold me-5">
                                        <span class="me-2"
                                            data-kt-customer-table-select="selected_count"></span>Selected
                                    </div>
                                    <button type="button" class="btn btn-sm btn-danger"
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
                                            <div
                                                class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_customers_table .form-check-input"
                                                    value="1" />
                                            </div>
                                        </th>
                                        <th class="min-w-200px">Nome do Fiél / Endereço</th>
                                        <th class="min-w-125px">Nascimento</th>
                                        <th class="min-w-100px">Sexo</th>
                                        <th class="min-w-125px">CPF</th>
                                        <th class="min-w-125px">RG</th>
                                        <th class="min-w-150px">Email</th>
                                        <th class="min-w-125px">Telefone</th>
                                        <th class="min-w-100px">Dizimista</th>
                                        <th class="text-end min-w-70px">Ações</th>
                                    </tr>
                                    <!--end::Table row-->
                                </thead>
                                <!--end::Table head-->
                                <!--begin::Table body-->
                                <tbody class="fw-bold text-gray-600">
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
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->
</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script>
    // Variáveis globais para rotas
    window.fieisRoutes = {
        edit: '{{ route('fieis.edit', ':id') }}',
        update: '{{ route('fieis.update', ':id') }}',
        store: '{{ route('fieis.store') }}',
        chartsData: '{{ route('fieis.charts.data') }}'
    };
</script>
<script src="/assets/js/custom/apps/customers/list/list-fiel.js"></script>
<script src="/assets/js/custom/apps/fieis/charts.js"></script>
<!--end::Custom Javascript(used for this page only)-->

<!--end::Javascript-->
