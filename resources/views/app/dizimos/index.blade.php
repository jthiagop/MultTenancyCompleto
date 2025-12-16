<x-tenant-app-layout>


    <!--begin::Modal-->
    @include('app.components.modals.dizimo')
    <!--end::Modal-->
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
                            Dízimo e Doações</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Dízimo e Doações</li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_dizimo">
                            <i class="fa-solid fa-plus"></i> Novo Lançamento
                        </button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Container-->
                <div id="kt_app_content_container" class="app-container container-fluid">
                    <!--begin::Estatísticas-->
                    <div class="row g-5 g-xl-8 mb-5">
                        <!-- Total Geral -->
                        <div class="col-xl-4">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #50CD89;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-coins fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                R$ {{ number_format($totalDizimos, 2, ',', '.') }}
                                            </div>
                                            <div class="text-white fw-bold fs-5">Total Geral</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total do Mês -->
                        <div class="col-xl-4">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #3E97FF;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-calendar fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                R$ {{ number_format($totalMes, 2, ',', '.') }}
                                            </div>
                                            <div class="text-white fw-bold fs-5">Total do Mês</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total de Dizimistas -->
                        <div class="col-xl-4">
                            <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end mb-5 mb-xl-10"
                                style="background-color: #F1416C;background-image:url('/assets/media/svg/shapes/widget-bg-1.png')">
                                <div class="card-body p-6">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="symbol symbol-60px me-5">
                                            <span class="symbol-label bg-white bg-opacity-25 rounded">
                                                <i class="fa-solid fa-users fs-2x text-white"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-white fw-bold fs-3 mb-1">
                                                {{ $totalDizimistas }}
                                            </div>
                                            <div class="text-white fw-bold fs-5">Dizimistas</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Estatísticas-->

                    <!--begin::Card-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <!--begin::Filtros-->
                                <div class="d-flex align-items-center position-relative my-1 gap-3">
                                    <select id="filtro_fiel" class="form-select form-select-solid w-200px">
                                        <option value="">Todos os Fiéis</option>
                                        @foreach($fieis as $fiel)
                                            <option value="{{ $fiel->id }}">{{ $fiel->nome_completo }}</option>
                                        @endforeach
                                    </select>

                                    <select id="filtro_tipo" class="form-select form-select-solid w-150px">
                                        <option value="">Todos os Tipos</option>
                                        <option value="Dízimo">Dízimo</option>
                                        <option value="Doação">Doação</option>
                                        <option value="Oferta">Oferta</option>
                                        <option value="Outro">Outro</option>
                                    </select>

                                    <input type="date" id="filtro_data_inicio" class="form-control form-control-solid w-150px" placeholder="Data Início">
                                    <input type="date" id="filtro_data_fim" class="form-control form-control-solid w-150px" placeholder="Data Fim">

                                    <button type="button" id="btn_limpar_filtros" class="btn btn-sm btn-light">
                                        <i class="fa-solid fa-xmark"></i> Limpar
                                    </button>
                                </div>
                                <!--end::Filtros-->
                            </div>
                        </div>
                        <!--end::Card header-->

                        <!--begin::Card body-->
                        <div class="card-body pt-0">
                            <!--begin::Table-->
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_dizimos_table">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                    data-kt-check-target="#kt_dizimos_table .form-check-input" value="1" />
                                            </div>
                                        </th>
                                        <th>Fiel</th>
                                        <th>Tipo</th>
                                        <th>Valor</th>
                                        <th>Data Pagamento</th>
                                        <th>Forma Pagamento</th>
                                        <th>Status</th>
                                        <th class="text-end min-w-100px">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                </tbody>
                            </table>
                            <!--end::Table-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->

    <!-- Script do modal de dízimo -->
    <script src="/assets/js/custom/utilities/modals/dizimo.js"></script>
    <!--end::Custom Javascript-->
</x-tenant-app-layout>

