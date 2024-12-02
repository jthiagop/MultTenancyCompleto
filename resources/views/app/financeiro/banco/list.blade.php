<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<!-- DateRangePicker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">

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
                            Resumo do Banco</h1>
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
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.index') }}"
                                    class="text-muted text-hover-primary">Financeiro</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <aspan class="text-muted text-hover-primary">Movimentações Bacária</aspan>
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
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
                    </div> --}}
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Navbar-->
                    <div class="card mb-6 mb-xl-9">
                        <div class="card-body pt-9 pb-0">
                            <!--begin::Details-->
                            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                <!--begin::Image-->
                                <div
                                    class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                                    <span class="svg-icon svg-icon-7x ">
                                        <svg version="1.1" id="_x34_" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.00 512.00"
                                            xml:space="preserve" width="512px" height="512px" fill="#000000"
                                            stroke="#000000" stroke-width="4.096">
                                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"
                                                stroke="#CCCCCC" stroke-width="1.024"></g>
                                            <g id="SVGRepo_iconCarrier">
                                                <g>
                                                    <polygon style="fill:#EFEEEF;"
                                                        points="474.016,135.427 493.838,135.427 493.838,85.881 256.001,0 18.162,85.881 18.162,135.427 37.985,135.427 ">
                                                    </polygon>
                                                    <polygon style="fill:#E3E1E1;"
                                                        points="50.81,105.702 256.001,31.602 461.19,105.702 "></polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="270.627,36.883 256.001,31.602 50.81,105.702 80.063,105.702 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="434.38,189.938 444.283,189.938 444.283,170.114 365.004,170.114 365.004,189.938 374.914,189.938 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="374.914,402.988 365.004,402.988 365.004,422.81 444.283,422.81 444.283,402.988 434.38,402.988 ">
                                                    </polygon>
                                                    <rect x="374.914" y="189.938" style="fill:#D8D8D9;" width="59.465"
                                                        height="213.05"></rect>
                                                    <rect x="226.267" y="189.938" style="fill:#D8D8D9;" width="59.457"
                                                        height="213.05"></rect>
                                                    <rect x="77.62" y="189.938" style="fill:#D8D8D9;" width="59.465"
                                                        height="213.05"></rect>
                                                    <g>
                                                        <rect x="102.397" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.912" height="213.05"></rect>
                                                        <rect x="82.575" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.919" height="213.05"></rect>
                                                        <rect x="122.219" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.912" height="213.05"></rect>
                                                    </g>
                                                    <g>
                                                        <rect x="251.044" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.912" height="213.05"></rect>
                                                        <rect x="231.231" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.903" height="213.05"></rect>
                                                        <rect x="270.866" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.903" height="213.05"></rect>
                                                    </g>
                                                    <g>
                                                        <rect x="399.693" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.91" height="213.05"></rect>
                                                        <rect x="379.878" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.903" height="213.05"></rect>
                                                        <rect x="419.515" y="189.938" style="fill:#CBCBCB;"
                                                            width="9.901" height="213.05"></rect>
                                                    </g>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="285.724,189.938 295.645,189.938 295.645,170.114 216.364,170.114 216.364,189.938 226.267,189.938 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="226.267,402.988 216.364,402.988 216.364,422.81 295.645,422.81 295.645,402.988 285.724,402.988 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="137.086,189.938 146.996,189.938 146.996,170.114 67.717,170.114 67.717,189.938 77.62,189.938 ">
                                                    </polygon>
                                                    <polygon style="fill:#CBCBCB;"
                                                        points="77.62,402.988 67.717,402.988 67.717,422.81 146.996,422.81 146.996,402.988 137.086,402.988 ">
                                                    </polygon>
                                                    <g>
                                                        <polygon style="fill:#EFEEEF;"
                                                            points="37.985,462.446 18.162,462.446 18.162,512 493.838,512 493.838,462.446 474.016,462.446 ">
                                                        </polygon>
                                                        <rect x="37.985" y="422.81" style="fill:#D8D8D9;"
                                                            width="436.031" height="39.637"></rect>
                                                    </g>
                                                    <rect x="37.985" y="135.427" style="fill:#D8D8D9;" width="436.031"
                                                        height="34.687"></rect>
                                                </g>
                                            </g>
                                        </svg>
                                    </span>

                                </div>
                                <!--end::Image-->
                                <!--begin::Wrapper-->
                                <div class="flex-grow-1">
                                    <!--begin::Head-->
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                        <!--begin::Details-->
                                        <div class="d-flex flex-column">
                                            <!--begin::Status-->
                                            <div class="d-flex align-items-center mb-1">
                                                <a href="#"
                                                    class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">Busca de
                                                    movimentação bancária</a>
                                                <span class="badge badge-light-success me-auto">Ativado</span>
                                            </div>
                                            <!--end::Status-->
                                            <!--begin::Description-->
                                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">Todos os
                                                lançamentos relacionados ao Banco</div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Actions-->
                                        <div class="d-flex mb-4">
                                            <!--begin::Financeiro Button-->
                                            <a href="{{ route('caixa.index') }}"
                                                class="btn btn-sm btn-bg-light btn-active-color-primary me-3">
                                                Financeiro
                                            </a>
                                            <!--end::Financeiro Button-->

                                            <!--begin::Lançamento Button-->
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#dm_modal_novo_lancamento_banco"
                                                class="btn btn-sm btn-primary me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-plus-circle"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                                </svg>
                                                Lançamento
                                            </a>
                                            <!--end::Lançamento Button-->

                                            <!--begin::Menu-->
                                            <div class="me-0">
                                                <button
                                                    class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="bi bi-three-dots fs-3"></i>
                                                </button>

                                                <!--begin::Menu Dropdown-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Pagamentos</div>
                                                    </div>
                                                    <!--end::Heading-->

                                                    <!--begin::Menu Item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Criar Fatura</a>
                                                    </div>
                                                    <!--end::Menu Item-->

                                                    <!--begin::Menu Item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link flex-stack px-3">
                                                            Criar Pagamento
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                                data-bs-toggle="tooltip"
                                                                title="Especifique um nome de destino para uso futuro e referência"></i>
                                                        </a>
                                                    </div>
                                                    <!--end::Menu Item-->

                                                    <!--begin::Menu Item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Gerar Boleto</a>
                                                    </div>
                                                    <!--end::Menu Item-->

                                                    <!--begin::Subscription Menu-->
                                                    <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                        data-kt-menu-placement="right-end">
                                                        <a href="#" class="menu-link px-3">
                                                            <span class="menu-title">Assinatura</span>
                                                            <span class="menu-arrow"></span>
                                                        </a>

                                                        <!--begin::Menu Sub-->
                                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                            <!--begin::Menu Items-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Planos</a>
                                                            </div>
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Cobranças</a>
                                                            </div>
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Extratos</a>
                                                            </div>
                                                            <!--end::Menu Items-->

                                                            <!--begin::Menu Separator-->
                                                            <div class="separator my-2"></div>
                                                            <!--end::Menu Separator-->

                                                            <!--begin::Recurring Switch-->
                                                            <div class="menu-item px-3">
                                                                <div class="menu-content px-3">
                                                                    <label
                                                                        class="form-check form-switch form-check-custom form-check-solid">
                                                                        <input class="form-check-input w-30px h-20px"
                                                                            type="checkbox" value="1"
                                                                            checked="checked" name="notifications" />
                                                                        <span
                                                                            class="form-check-label text-muted fs-6">Recorrente</span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <!--end::Recurring Switch-->
                                                        </div>
                                                        <!--end::Menu Sub-->
                                                    </div>
                                                    <!--end::Subscription Menu-->

                                                    <!--begin::Settings Item-->
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Configurações</a>
                                                    </div>
                                                    <!--end::Settings Item-->
                                                </div>
                                                <!--end::Menu Dropdown-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Actions-->

                                    </div>
                                    <!--end::Head-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-wrap justify-content-start">
                                        <!--begin::Stats-->
                                        <div class="d-flex flex-wrap">
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $total }}"
                                                        data-kt-countup-prefix="R$ ">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400">Saldo atua</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="11" y="18" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(-90 11 18)" fill="currentColor" />
                                                            <path
                                                                d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $ValorSaidas }} "data-kt-countup-prefix="R$ ">
                                                        0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400">Saída</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path
                                                                d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $valorEntrada }}"
                                                        data-kt-countup-prefix="R$ ">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400"
                                                    data-kt-countup-prefix="R$ ">Entrada</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                        </div>
                                        <!--end::Stats-->
                                        <!--begin::Users-->
                                        <div class="symbol-group symbol-hover mb-3">
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Alan Warden">
                                                <span
                                                    class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michael Eberon">
                                                <img alt="Pic" src="/assets/media/avatars/300-11.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michelle Swanston">
                                                <img alt="Pic" src="/assets/media/avatars/300-7.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Francis Mitcham">
                                                <img alt="Pic" src="/assets/media/avatars/300-20.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Susan Redwood">
                                                <span
                                                    class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Melody Macy">
                                                <img alt="Pic" src="/assets/media/avatars/300-2.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Perry Matthew">
                                                <span class="symbol-label bg-info text-inverse-info fw-bold">P</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Barry Walter">
                                                <img alt="Pic" src="/assets/media/avatars/300-12.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::All users-->
                                            <a href="#" class="symbol symbol-35px symbol-circle"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                                    data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                    title="View more users">+42</span>
                                            </a>
                                            <!--end::All users-->
                                        </div>
                                        <!--end::Users-->
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Details-->
                            <div class="separator"></div>
                            <!--begin::Nav-->
                            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                <!-- Aba Resumo -->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6 active" data-bs-toggle="tab"
                                        href="#resumo">Resumo</a>
                                </li>
                                <!-- Aba Prestação de Contas -->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary" data-bs-toggle="tab"
                                        href="#prestacao-de-contas">Prestação de Contas</a>
                                </li>
                            </ul>
                            <!--end::Nav-->
                        </div>
                    </div>
                    <!--end::Navbar-->
                    <div class="tab-content mt-5">
                        <!-- Conteúdo da Aba Resumo -->
                        <div class="tab-pane fade show active" id="resumo">
                            <!--begin::Products-->
                            <div class="card card-flush">
                                <!--begin::Card header-->
                                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1"
                                                        transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" data-kt-ecommerce-order-filter="search"
                                                class="form-control form-control-solid w-250px ps-14"
                                                placeholder="Buscar Lançamento" />
                                        </div>
                                        <!--end::Search-->
                                        <!--begin::Export buttons-->
                                        <div id="kt_ecommerce_report_shipping_export" class="d-none"></div>
                                        <!--end::Export buttons-->
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                        <!--begin::Daterangepicker-->
                                        <input class="form-control form-control-solid w-100 mw-250px"
                                            placeholder="Pick date range"
                                            id="kt_ecommerce_report_shipping_daterangepicker" />

                                        <!--end::Daterangepicker-->
                                        <!--begin::Filter-->
                                        <div class="w-150px">
                                            <!--begin::Select2-->
                                            <select class="form-select form-select-solid" data-control="select2"
                                                data-hide-search="true" data-placeholder="Status"
                                                data-kt-ecommerce-order-filter="status">
                                                <option></option>
                                                <option value="all">Todos</option>
                                                <option value="entrada">Entrada</option>
                                                <option value="saida">Saída</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                            <!--end::Select2-->
                                        </div>
                                        <!--end::Filter-->
                                        <!--begin::Export dropdown-->
                                        <button type="button" class="btn btn-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                            <span class="svg-icon svg-icon-2">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="12.75" y="4.25" width="12"
                                                        height="2" rx="1"
                                                        transform="rotate(90 12.75 4.25)" fill="currentColor" />
                                                    <path
                                                        d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z"
                                                        fill="currentColor" />
                                                    <path opacity="0.3"
                                                        d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->Relatório</button>
                                        <!--begin::Menu-->
                                        <div id="kt_ecommerce_report_shipping_export_menu"
                                            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-ecommerce-export="excel">Exporta Excel</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-ecommerce-export="csv">Exporta CSV</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-ecommerce-export="pdf">Exporta PDF</a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                        <!--end::Export dropdown-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Table-->
                                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="kt_ecommerce_report_shipping_table">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-75px">ID</th>
                                                <th class="min-w-100px">Data</th>
                                                <th class="min-w-250px">Tipo Docuemnto</th>
                                                <th class="min-w-150px">Banco</th>
                                                <th class="min-w-500px">Documento</th>
                                                <th class="min-w-125px">Tipo</th>
                                                <th class="min-w-125px">Valor</th>
                                                <th class="min-w-75px">Origem</th>
                                                <th class="text-end min-w-100px">Ações</th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">
                                            <!--begin::Table row-->
                                            @foreach ($IfBancos as $IfBanco)
                                                <tr>
                                                    <!--begin::User=-->
                                                    <td>{{ $IfBanco->id }}</td>
                                                    <!--end::User=-->
                                                    <!--begin::Role=-->
                                                    <td>{{ date(' d-m-Y', strtotime($IfBanco->data_competencia)) }}</td>
                                                    <!--end::Role=-->
                                                    <!--begin::Last login=-->
                                                    <td>{{ $IfBanco->tipo_documento }}</td>
                                                    <!--end::Last login=-->
                                                    <!--begin::Last login=-->
                                                    <td>{{ $IfBanco->movimentacao->entidade->nome ?? 'Sem entidade associada' }}</td> <!-- Nome da entidade --></td>
                                                    <!--end::Last login=-->
                                                    <!--begin::Two step=-->
                                                    <td>{{ optional($IfBanco->lancamentoPadrao)->bancos ? optional($IfBanco->lancamentoPadrao)->description : 'N/A' }}</td>
                                                    <!--end::Two step=-->
                                                    <!--begin::Joined-->
                                                    <td>
                                                        <div
                                                            class="badge fw-bold {{ $IfBanco->tipo == 'entrada' ? 'badge-success' : 'badge-danger' }}">
                                                            {{ $IfBanco->tipo }}
                                                        </div>
                                                    </td>
                                                    <!--begin::Joined-->
                                                    <td>R$ {{ number_format($IfBanco->valor, 2, ',', '.') }}</td>
                                                    <td class="text-center">{{ $IfBanco->origem }}</td>
                                                    <!--begin::Action=-->
                                                    <td class="text-end">
                                                        <a href="#"
                                                            class="btn btn-light btn-active-light-primary btn-sm"
                                                            data-kt-menu-trigger="click"
                                                            data-kt-menu-placement="bottom-end">Ações
                                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                            <span class="svg-icon svg-icon-5 m-0">
                                                                <svg width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path
                                                                        d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon--></a>
                                                        <!--begin::Menu-->
                                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                            data-kt-menu="true">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="{{ route('banco.edit', $IfBanco->id) }}"
                                                                    class="menu-link px-3">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="16" height="16"
                                                                        fill="currentColor"
                                                                        class="bi bi-pencil-square"
                                                                        viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                                                        <path fill-rule="evenodd"
                                                                            d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                                                    </svg>
                                                                    Editar</a>
                                                                <a href="#"
                                                                    class="menu-link px-3 delete-link text-danger"
                                                                    data-id="{{ $IfBanco->id }}">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        width="16" height="16"
                                                                        fill="currentColor" class="bi bi-trash3"
                                                                        viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                                                    </svg>
                                                                    Excluir</a>
                                                                <form id="delete-form-{{ $IfBanco->id }}"
                                                                    action="{{ route('banco.destroy', $IfBanco->id) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            </div>
                                                            <!--end::Menu item-->>
                                                        </div>
                                                        <!--end::Menu-->
                                                    </td>
                                                    <!--end::Action=-->
                                                </tr>
                                            @endforeach
                                            <!--end::Table row-->
                                        </tbody>
                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Products-->
                        </div>
                        <!-- Conteúdo da Aba Prestação de Contas -->
                        <div class="tab-pane fade" id="prestacao-de-contas">
                            <div class="card card-flush">
                                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                    <div class="card-title">
                                        <h3>Prestação de Contas</h3>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Adicione o conteúdo específico para Prestação de Contas -->
                                    <p>Aqui está o conteúdo da aba Prestação de Contas.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

        @include('app.components.modals.lancar-banco')

</x-tenant-app-layout>

<script src="/assets/js/custom_script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/bancos/shipping.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>

<script src="/assets/js/custom/utilities/modals/financeiro/new-banco.js"></script>

<!--end::Custom Javascript-->
<!--end::Javascript-->

<!-- jQuery -->
<!-- Bootstrap Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<!-- Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<!-- DateRangePicker -->
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- Custom Script -->
<script src="{{ asset('js/custom_script.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
