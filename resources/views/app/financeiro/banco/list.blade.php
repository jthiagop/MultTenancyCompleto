<!-- CSS do Kendo (tema) -->
<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />

<!-- jQuery (obrigatório) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Kendo UI (JS principal) -->
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
                    <div class="row no-gutters">
                        <div class="12 col-sm-12 col-md-8">
                            <div class="card mb-6 mb-xl-9">
                                <div class="card-body pt-9 pb-0">
                                    <!--begin::Details-->
                                    <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                        <!--begin::Image-->
                                        <div
                                            class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                                            <img class="img-fluid w-100 h-100 rounded" src="/assets/media/png/banco.png"
                                                alt="image" />
                                        </div>
                                        <!--end::Image-->
                                        <!--begin::Wrapper-->
                                        <div class="flex-grow-1">
                                            <!--begin::Head-->
                                            <div
                                                class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                                <!--begin::Details-->
                                                <div class="d-flex flex-column">
                                                    <!--begin::Status-->
                                                    <div class="d-flex align-items-center mb-1">
                                                        <a href="#"
                                                            class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">Busca
                                                            de
                                                            movimentação bancária</a>
                                                        {{-- <span class="badge badge-light-success me-auto">Ativado</span> --}}
                                                    </div>
                                                    <!--end::Status-->
                                                    <!--begin::Description-->
                                                    <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">
                                                        Todos os
                                                        lançamentos relacionados ao Banco</div>
                                                    <!--end::Description-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Actions-->
                                                <div class="d-flex mb-4">
                                                    {{-- <!--begin::Financeiro Button-->
                                                    <a href="{{ route('caixa.index') }}"
                                                        class="btn btn-sm btn-bg-light btn-active-color-primary me-3">
                                                        Financeiro
                                                    </a>
                                                    <!--end::Financeiro Button--> --}}

                                                    <!--begin::Lançamento Button-->
                                                    <a href="{{ route('banco.list', ['tab' => 'lancamento']) }}"
                                                        class="btn btn-sm btn-primary me-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-plus-circle"
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
                                                            data-kt-menu-trigger="click"
                                                            data-kt-menu-placement="bottom-end">
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
                                                                <a href="#" class="menu-link px-3">Criar
                                                                    Fatura</a>
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
                                                                <a href="#" class="menu-link px-3">Gerar
                                                                    Boleto</a>
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
                                                                        <a href="#"
                                                                            class="menu-link px-3">Planos</a>
                                                                    </div>
                                                                    <div class="menu-item px-3">
                                                                        <a href="#"
                                                                            class="menu-link px-3">Cobranças</a>
                                                                    </div>
                                                                    <div class="menu-item px-3">
                                                                        <a href="#"
                                                                            class="menu-link px-3">Extratos</a>
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
                                                                                <input
                                                                                    class="form-check-input w-30px h-20px"
                                                                                    type="checkbox" value="1"
                                                                                    checked="checked"
                                                                                    name="notifications" />
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
                                                                <a href="#"
                                                                    class="menu-link px-3">Configurações</a>
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
                                                            <div class="fs-4 fw-bold">R$ {{ number_format($total, 2, ',', '.') }}</div>
                                                        </div>
                                                        <!--end::Number-->
                                                        <!--begin::Label-->
                                                        <div class="fw-semibold fs-6 text-gray-400">Saldo Total</div>
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
                                                            <div class="fs-4 fw-bold">R$ {{ number_format($ValorSaidas, 2, ',', '.') }}</div>
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
                                                                <svg width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <rect opacity="0.5" x="13" y="6" width="13"
                                                                        height="2" rx="1"
                                                                        transform="rotate(90 13 6)"
                                                                        fill="currentColor" />
                                                                    <path
                                                                        d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                            <div class="fs-4 fw-bold">R$ {{ number_format($valorEntrada, 2, ',', '.') }}</div>
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
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Details-->
                                    <div class="separator"></div>
                                    <!--begin::Nav-->
                                    <ul
                                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'overview' ? 'active' : '' }}"
                                                href="{{ route('banco.list', ['tab' => 'overview']) }}">
                                                Gestão
                                            </a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'lancamento' ? 'active' : '' }}"
                                                href="{{ route('banco.list', ['tab' => 'lancamento']) }}">
                                                Lançamento
                                            </a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'registros' ? 'active' : '' }}"
                                                href="{{ route('banco.list', ['tab' => 'registros']) }}">
                                                Registros
                                            </a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'bancos' ? 'active' : '' }}"
                                                href="{{ route('banco.list', ['tab' => 'bancos']) }}">
                                                Bancos
                                            </a>
                                        </li>
                                        <!--end::Nav item-->
                                    </ul>
                                    <!--end::Nav-->
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-4"> <!--begin::Row-->
                            <div class="card mb-6 mb-xl-9">
                                <!--begin::Col-->
                                <div class="col-xl-12 mb-xl-6">
                                    <!--begin::Slider Widget 2-->
                                    <div id="kt_sliders_widget_2_slider"
                                        class="card card-flush carousel carousel-custom carousel-stretch slide h-xl-100"
                                        data-bs-ride="carousel" data-bs-interval="6000">
                                        <!--begin::Header-->
                                        <div class="card-header pt-5">
                                            <!--begin::Title-->
                                            <h4 class="card-title d-flex align-items-start flex-column">
                                                <span class="card-label fw-bold text-gray-800">Lista de Bancos</span>
                                                <span class="text-gray-400 mt-1 fw-bold fs-7">Exibindo
                                                    {{ count($entidadesBanco) }} bancos</span>
                                            </h4>
                                            <!--end::Title-->
                                            <!--begin::Toolbar-->
                                            <div class="card-toolbar">
                                                <!--begin::Carousel Indicators-->
                                                <ol
                                                    class="p-0 m-0 carousel-indicators carousel-indicators-bullet carousel-indicators-active-success">
                                                    @foreach ($entidadesBanco as $key => $entidade)
                                                        <li data-bs-target="#kt_sliders_widget_2_slider"
                                                            data-bs-slide-to="{{ $key }}"
                                                            class="@if ($key == 0) active @endif ms-1">
                                                        </li>
                                                    @endforeach
                                                </ol>
                                                <!--end::Carousel Indicators-->
                                            </div>
                                            <!--end::Toolbar-->
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div class="card-body py-6">
                                            <!--begin::Carousel-->
                                            <div class="carousel-inner">
                                                <!--begin::Itens do Carrossel-->
                                                @foreach ($entidadesBanco as $key => $entidade)
                                                    <div
                                                        class="carousel-item @if ($key == 0) active show @endif">
                                                        <!--begin::Wrapper-->
                                                        <div class="d-flex align-items-center mb-9">
                                                            <!--begin::Symbol-->
                                                            <div class="symbol symbol-70px symbol-circle me-5">
                                                                <span class="symbol-label bg-light-primary">
                                                                    <!-- Custom Icon based on the bank -->
                                                                    <span
                                                                        class="svg-icon svg-icon-3x svg-icon-primary">
                                                                        <!-- You can add a custom icon or keep it as is -->
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path
                                                                                d="M12 2L9 5H15L12 2ZM4 8H20L12 22L4 8Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                </span>
                                                            </div>
                                                            <!--end::Symbol-->

                                                            <!--begin::Info-->
                                                            <div class="m-0">
                                                                <!--begin::Subtitle-->
                                                                <h4 class="fw-bold text-gray-800 mb-3">
                                                                    {{ $entidade->nome }}</h4>
                                                                <!--end::Subtitle-->

                                                                <!--begin::Items-->
                                                                <div class="d-flex d-grid gap-5">
                                                                    <!--begin::Item-->
                                                                    <div class="d-flex flex-column flex-shrink-0 me-4">
                                                                        <!--begin::Info-->
                                                                        <div class="d-flex align-items-center">
                                                                            <!--begin::Currency-->
                                                                            <span
                                                                                class="fs-4 fw-semibold text-gray-400 me-1 align-self-start">R$</span>
                                                                            <!--end::Currency-->
                                                                            <!--begin::Amount-->
                                                                            <span
                                                                                class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ number_format($entidade->saldo_atual, 2, ',', '.') }}</span>
                                                                            <!--end::Amount-->
                                                                            <!--begin::Badge-->
                                                                            <span
                                                                                class="badge badge-light-success fs-base">
                                                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                                                <span
                                                                                    class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                                                    <svg width="24" height="24"
                                                                                        viewBox="0 0 24 24"
                                                                                        fill="none"
                                                                                        xmlns="http://www.w3.org/2000/svg">
                                                                                        <rect opacity="0.5" x="13"
                                                                                            y="6" width="13"
                                                                                            height="2"
                                                                                            rx="1"
                                                                                            transform="rotate(90 13 6)"
                                                                                            fill="currentColor" />
                                                                                        <path
                                                                                            d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                                            fill="currentColor" />
                                                                                    </svg>
                                                                                </span>
                                                                                <!--end::Svg Icon-->2.2%</span>
                                                                            <!--end::Badge-->
                                                                        </div>
                                                                        <!--end::Info-->
                                                                    </div>
                                                                    <!--end::Item-->
                                                                </div>
                                                                <!--end::Items-->
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::Wrapper-->

                                                        <!--begin::Action-->
                                                        <div class="m-0">
                                                            <a href="#"
                                                                class="btn btn-sm btn-light me-2 mb-2">Detalhes</a>
                                                            <a href="#"
                                                                class="btn btn-sm btn-success mb-2">Entrar no Banco</a>
                                                        </div>
                                                        <!--end::Action-->
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Slider Widget 2-->

                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                        </div>
                    </div>
                    <!--end::Navbar-->
                    @includeIf("app.financeiro.banco.tabs.{$activeTab}")

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

        @include('app.components.modals.lancar-banco')
        <!--end::Modal - Upgrade plan-->
        <script>
            var lpsData = @json($lps);
        </script>
</x-tenant-app-layout>

<script src="/assets/js/custom_script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<script src="/assets/js/custom/utilities/modals/financeiro/moduloAnexos.js"></script>
<script src="/assets/js/custom/apps/ecommerce/reports/customer-orders/banco-customer-orders.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/new-banco.js"></script>

<script src="/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<script src="/assets/js/custom/apps/bancos/form-dropzone.js"></script>

<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/bancos/shipping.js"></script>
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>


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
