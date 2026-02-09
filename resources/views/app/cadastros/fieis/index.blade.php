<x-tenant-app-layout>
    <!--begin::Modal - Support Center - Create Ticket-->
    @include('app.components.modals.cadastro_fiel')
    <!--end::Modal - Support Center - Create Ticket-->
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid pt-5">
                <!--begin::Container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!--begin::Graficos-->
                    <div class="row g-5 g-xl-8">
                        <!-- HOMENS -->
                        <div class="col-xl-3">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #3E97FF;background-image:url('/tenancy/assets/media/svg/shapes/widget-bg-1.png')">
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
                                style="background-color: #F1416C;background-image:url('/tenancy/assets/media/svg/shapes/widget-bg-1.png')">
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
                                style="background-color: #FFC700;background-image:url('/tenancy/assets/media/svg/shapes/widget-bg-1.png')">
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
                                style="background-color: #50CD89;background-image:url('/tenancy/assets/media/svg/shapes/widget-bg-1.png')">
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
                                            <div class="bg-white rounded h-8px" role="progressbar" style="width: 100%;"
                                                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
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

<!--begin::Modal - Relatório de Fiéis-->
<div class="modal fade" id="kt_fieis_export_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-700px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold mb-0">Gerar Relatório de Fiéis</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x fs-1"></i>
                </div>
            </div>
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15 mt-5">
                <form id="kt_fieis_export_form" action="{{ route('fieis.relatorio.pdf') }}" method="POST"
                    target="_blank">
                    @csrf

                    <!-- Filtro 1: Tipo de Registro -->
                    <div class="fv-row mb-5">
                        <label class="fs-6 fw-semibold form-label mb-4">1. Tipo de Registro</label>
                        <div class="d-flex flex-wrap gap-5">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="tipo_registro" value="fieis"
                                    id="tipo_fieis" checked />
                                <label class="form-check-label fw-semibold" for="tipo_fieis">
                                    <i class="bi bi-people-fill fs-4 me-2"></i>Fiéis
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="tipo_registro" value="filhos"
                                    id="tipo_filhos" disabled />
                                <label class="form-check-label fw-semibold text-muted" for="tipo_filhos">
                                    <i class="bi bi-person fs-4 me-2"></i>Filhos <span
                                        class="badge badge-light-warning ms-2">Em breve</span>
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="tipo_registro" value="conjuges"
                                    id="tipo_conjuges" disabled />
                                <label class="form-check-label fw-semibold text-muted" for="tipo_conjuges">
                                    <i class="bi bi-heart-fill fs-4 me-2"></i>Cônjuges <span
                                        class="badge badge-light-warning ms-2">Em breve</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-8"></div>

                    <!-- Filtro 2: Dizimista -->
                    <div class="fv-row mb-10">
                        <label class="fs-6 fw-semibold form-label mb-4">2. Filtrar por Dízimo</label>
                        <div class="d-flex flex-wrap gap-5">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="filtro_dizimista"
                                    value="todos" id="dizimista_todos" checked />
                                <label class="form-check-label fw-semibold" for="dizimista_todos">
                                    Todos
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="filtro_dizimista"
                                    value="sim" id="dizimista_sim" />
                                <label class="form-check-label fw-semibold" for="dizimista_sim">
                                    <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>Somente Dizimistas
                                </label>
                            </div>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="filtro_dizimista"
                                    value="nao" id="dizimista_nao" />
                                <label class="form-check-label fw-semibold" for="dizimista_nao">
                                    <i class="bi bi-x-circle-fill text-secondary fs-5 me-2"></i>Não Dizimistas
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-8"></div>

                    <!-- Filtro 3: Filtro por Data/Idade -->
                    <div class="fv-row mb-10">
                        <label class="fs-6 fw-semibold form-label mb-4">3. Filtros Adicionais</label>
                        <select name="filtro_data_tipo" id="filtro_data_tipo" class="form-select form-select-solid"
                            data-control="select2" data-hide-search="true">
                            <option value="">Sem filtro adicional</option>
                            <option value="aniversariantes">Aniversariantes (Data de Nascimento)</option>
                            <option value="idade">Filtrar por Idade</option>
                            <option value="aniversario_casamento" disabled>Aniversário de Casamento (Em breve)</option>
                        </select>
                    </div>

                    <!-- Campo condicional: Aniversariantes -->
                    <div class="fv-row mb-10 d-none" id="aniversariantes_container">
                        <label class="fs-6 fw-semibold form-label mb-2">
                            Período de Aniversários
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                title="Selecione o período de datas de aniversário (dia/mês)"></i>
                        </label>
                        <input type="text" name="periodo_aniversario" id="periodo_aniversario"
                            class="form-control form-control-solid" placeholder="Selecione o período" readonly />
                        <div class="form-text">Exemplo: Aniversariantes de 01/01 até 31/12</div>
                    </div>

                    <!-- Campo condicional: Idade -->
                    <div class="fv-row mb-10 d-none" id="idade_container">
                        <label class="fs-6 fw-semibold form-label mb-2">Faixa Etária</label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fs-7">Idade Mínima</label>
                                <input type="number" name="idade_minima" id="idade_minima"
                                    class="form-control form-control-solid" placeholder="Ex: 18" min="0"
                                    max="120" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-7">Idade Máxima</label>
                                <input type="number" name="idade_maxima" id="idade_maxima"
                                    class="form-control form-control-solid" placeholder="Ex: 65" min="0"
                                    max="120" />
                            </div>
                        </div>
                        <div class="form-text mt-2">Deixe em branco para não limitar</div>
                    </div>

                    <div class="separator separator-dashed my-8"></div>

                    <!-- Filtros Complementares -->
                    <div class="row g-5 mb-10">
                        <!-- Sexo -->
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold form-label mb-2">Sexo</label>
                            <select name="sexo" class="form-select form-select-solid" data-control="select2"
                                data-hide-search="true">
                                <option value="">Todos</option>
                                <option value="M">Masculino</option>
                                <option value="F">Feminino</option>
                            </select>
                        </div>

                        <!-- Estado Civil -->
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold form-label mb-2">Estado Civil</label>
                            <select name="estado_civil" class="form-select form-select-solid" data-control="select2">
                                <option value="">Todos</option>
                                <option value="Amasiado(a)">Amasiado(a)</option>
                                <option value="Solteiro(a)">Solteiro(a)</option>
                                <option value="Casado(a)">Casado(a)</option>
                                <option value="Divorciado(a)">Divorciado(a)</option>
                                <option value="Viúvo(a)">Viúvo(a)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Opções de Apresentação -->
                    <div class="row g-5 mb-10">
                        <!-- Ordenação -->
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold form-label mb-2">Ordenar Por</label>
                            <select name="ordenar_por" class="form-select form-select-solid" data-control="select2"
                                data-hide-search="true">
                                <option value="nome">Nome</option>
                                <option value="data_nascimento">Data de Nascimento</option>
                                <option value="cpf">CPF</option>
                            </select>
                        </div>

                        <!-- Layout -->
                        <div class="col-md-6">
                            <label class="fs-6 fw-semibold form-label mb-2">Layout do Relatório</label>
                            <select name="layout_relatorio" class="form-select form-select-solid"
                                data-control="select2" data-hide-search="true">
                                <option value="resumido">Resumido (Tabela)</option>
                                <option value="detalhado">Detalhado (Cartões)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="modal-footer flex-center">
                        <button type="reset" class="btn btn-sm btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-file-pdf fs-4 me-2"></i>
                            <span class="indicator-label">Gerar PDF</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!--end::Modal - Relatório de Fiéis-->

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Moment.js e DateRangePicker -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
<script src="/tenancy/assets/js/custom/apps/customers/list/list-fiel.js"></script>
<script src="/tenancy/assets/js/custom/apps/fieis/charts.js"></script>

<!--begin::Modal Script-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configurar moment.js para português
        if (typeof moment !== 'undefined') {
            moment.locale('pt-br');
        }

        const filtroDataTipo = document.getElementById('filtro_data_tipo');
        const aniversariantesContainer = document.getElementById('aniversariantes_container');
        const idadeContainer = document.getElementById('idade_container');
        const periodoAniversarioInput = document.getElementById('periodo_aniversario');

        // Garantir que o Select2 esteja inicializado quando o modal abrir
        $('#kt_fieis_export_modal').on('shown.bs.modal', function() {
            // Reinicializar Select2 se necessário
            if (filtroDataTipo && !$(filtroDataTipo).hasClass('select2-hidden-accessible')) {
                $(filtroDataTipo).select2({
                    dropdownParent: $('#kt_fieis_export_modal'),
                    minimumResultsForSearch: Infinity
                });
            }
        });

        // Inicializar daterangepicker para aniversariantes
        if (periodoAniversarioInput && typeof $ !== 'undefined' && typeof moment !== 'undefined' && $.fn
            .daterangepicker) {
            $(periodoAniversarioInput).daterangepicker({
                locale: {
                    format: 'DD/MM',
                    separator: ' até ',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    fromLabel: 'De',
                    toLabel: 'Até',
                    customRangeLabel: 'Personalizado',
                    daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                    monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
                    ],
                    firstDay: 0
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Hoje': [moment(), moment()],
                    'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                    'Próximo Mês': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month')
                        .endOf('month')
                    ],
                    'Janeiro': [moment().month(0).startOf('month'), moment().month(0).endOf('month')],
                    'Fevereiro': [moment().month(1).startOf('month'), moment().month(1).endOf('month')],
                    'Março': [moment().month(2).startOf('month'), moment().month(2).endOf('month')],
                    'Abril': [moment().month(3).startOf('month'), moment().month(3).endOf('month')],
                    'Maio': [moment().month(4).startOf('month'), moment().month(4).endOf('month')],
                    'Junho': [moment().month(5).startOf('month'), moment().month(5).endOf('month')],
                    'Julho': [moment().month(6).startOf('month'), moment().month(6).endOf('month')],
                    'Agosto': [moment().month(7).startOf('month'), moment().month(7).endOf('month')],
                    'Setembro': [moment().month(8).startOf('month'), moment().month(8).endOf('month')],
                    'Outubro': [moment().month(9).startOf('month'), moment().month(9).endOf('month')],
                    'Novembro': [moment().month(10).startOf('month'), moment().month(10).endOf(
                        'month')],
                    'Dezembro': [moment().month(11).startOf('month'), moment().month(11).endOf(
                        'month')],
                    'Ano Todo': [moment().startOf('year'), moment().endOf('year')]
                },
                opens: 'center',
                drops: 'auto',
                autoApply: false,
                showCustomRangeLabel: true,
                alwaysShowCalendars: true
            });
        } else {
            console.warn('DateRangePicker ou suas dependências não estão disponíveis');
        }

        // Gerenciar campos condicionais
        if (filtroDataTipo) {
            // Usar evento do Select2
            $(filtroDataTipo).on('change', function() {
                const valor = $(this).val();
                console.log('Filtro selecionado:', valor); // Debug

                // Esconder todos os campos condicionais
                if (aniversariantesContainer) aniversariantesContainer.classList.add('d-none');
                if (idadeContainer) idadeContainer.classList.add('d-none');

                // Limpar valores
                if (periodoAniversarioInput) {
                    periodoAniversarioInput.value = '';
                }
                const idadeMinimaInput = document.getElementById('idade_minima');
                const idadeMaximaInput = document.getElementById('idade_maxima');
                if (idadeMinimaInput) idadeMinimaInput.value = '';
                if (idadeMaximaInput) idadeMaximaInput.value = '';

                // Mostrar campo apropriado
                switch (valor) {
                    case 'aniversariantes':
                        if (aniversariantesContainer) {
                            aniversariantesContainer.classList.remove('d-none');
                            console.log('Mostrando campo de aniversariantes'); // Debug
                        }
                        break;
                    case 'idade':
                        if (idadeContainer) {
                            idadeContainer.classList.remove('d-none');
                            console.log('Mostrando campo de idade'); // Debug
                        }
                        break;
                }
            });
        } else {
            console.error('Elemento filtro_data_tipo não encontrado');
        }

        // Validação de idade
        const idadeMinima = document.getElementById('idade_minima');
        const idadeMaxima = document.getElementById('idade_maxima');

        if (idadeMinima && idadeMaxima) {
            idadeMaxima.addEventListener('change', function() {
                const min = parseInt(idadeMinima.value) || 0;
                const max = parseInt(idadeMaxima.value) || 0;

                if (max > 0 && min > 0 && max < min) {
                    Swal.fire({
                        text: 'A idade máxima não pode ser menor que a idade mínima!',
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, entendi!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    idadeMaxima.value = '';
                }
            });
        }

        // Reset do formulário ao fechar modal
        $('#kt_fieis_export_modal').on('hidden.bs.modal', function() {
            document.getElementById('kt_fieis_export_form').reset();
            aniversariantesContainer.classList.add('d-none');
            idadeContainer.classList.add('d-none');
        });
    });
</script>
<!--end::Modal Script-->
<!--end::Custom Javascript(used for this page only)-->

<!--end::Javascript-->
