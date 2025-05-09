<x-tenant-app-layout>
    {{-- *** Modal para Emitir Ptam *** --}}
    @include('app.components.modals.patrimonio.documentos.ptam')
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
                            Patrimônio: {{ $patrimonio->descricao }}</h1>
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
                                <a href="{{ route('patrimonio.index') }}" class="text-muted text-hover-primary">
                                    Patrimônio</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Patrimônio: {{ $patrimonio->codigo_rid }}</li>
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
                    <div class="card card-flush mb-9" id="kt_user_profile_panel">
                        <!--begin::Hero nav-->
                        <div class="card-header rounded-top bgi-size-cover h-200px"
                            style="background-position: 100% 50%; background-image:url('/assets/media/misc/profile-head-bg.png')">
                        </div>
                        <!--end::Hero nav-->
                        <!--begin::Body-->
                        <div class="card-body mt-n19">
                            <!--begin::Details-->
                            <div class="m-0">
                                <!--begin: Pic-->
                                <div class="d-flex flex-stack align-items-end pb-4 mt-n19">
                                    <div
                                        class="symbol symbol-125px symbol-lg-150px symbol-fixed position-relative mt-n3">
                                        <img class="me-5" width="75px" height="75px"
                                            src="/assets/media/png/house3d.png" alt="" />
                                        <div
                                            class="position-absolute translate-middle bottom-0 start-100 ms-n1 mb-9 bg-success rounded-circle h-15px w-15px">
                                        </div>
                                    </div>
                                    <!--begin::Toolbar-->
                                    <div class="me-0">
                                        <button
                                            class="btn btn-icon btn-sm btn-active-color-primary justify-content-end pt-3"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <i class="fonticon-settings fs-2"></i>
                                        </button>
                                        <!--begin::Menu 3-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                            data-kt-menu="true">
                                            <!--begin::Heading-->
                                            <div class="menu-item px-3">
                                                <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                    Transações</div>
                                            </div>
                                            <!--end::Heading-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Domínio Direto</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link flex-stack px-3">Create Payment
                                                    <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                        data-bs-toggle="tooltip"
                                                        title="Specify a target name for future usage and reference"></i></a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Generate Bill</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                data-kt-menu-placement="right-end">
                                                <a href="#" class="menu-link px-3">
                                                    <span class="menu-title">Subscription</span>
                                                    <span class="menu-arrow"></span>
                                                </a>
                                                <!--begin::Menu sub-->
                                                <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Plans</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Billing</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Statements</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu separator-->
                                                    <div class="separator my-2"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <div class="menu-content px-3">
                                                            <!--begin::Switch-->
                                                            <label
                                                                class="form-check form-switch form-check-custom form-check-solid">
                                                                <!--begin::Input-->
                                                                <input class="form-check-input w-30px h-20px"
                                                                    type="checkbox" value="1" checked="checked"
                                                                    name="notifications" />
                                                                <!--end::Input-->
                                                                <!--end::Label-->
                                                                <span
                                                                    class="form-check-label text-muted fs-6">Recuring</span>
                                                                <!--end::Label-->
                                                            </label>
                                                            <!--end::Switch-->
                                                        </div>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu sub-->
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3 my-1">
                                                <a href="#" class="menu-link text-danger px-3">Excluir</a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu 3-->
                                    </div>
                                    <!--end::Toolbar-->
                                </div>
                                <!--end::Pic-->
                                <!--begin::Info-->
                                <div class="d-flex flex-stack flex-wrap align-items-end">
                                    <!--begin::User-->
                                    <div class="d-flex flex-column">
                                        <!--begin::Name-->
                                        <div class="d-flex align-items-center mb-2">
                                            <a href="#"
                                                class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ $patrimonio->codigo_rid }}</a>
                                            <a href="#" class="" data-bs-toggle="tooltip"
                                                data-bs-placement="right" title="Account is verified">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                                <span class="svg-icon svg-icon-1 svg-icon-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24px"
                                                        height="24px" viewBox="0 0 24 24">
                                                        <path
                                                            d="M10.0813 3.7242C10.8849 2.16438 13.1151 2.16438 13.9187 3.7242V3.7242C14.4016 4.66147 15.4909 5.1127 16.4951 4.79139V4.79139C18.1663 4.25668 19.7433 5.83365 19.2086 7.50485V7.50485C18.8873 8.50905 19.3385 9.59842 20.2758 10.0813V10.0813C21.8356 10.8849 21.8356 13.1151 20.2758 13.9187V13.9187C19.3385 14.4016 18.8873 15.491 19.2086 16.4951V16.4951C19.7433 18.1663 18.1663 19.7433 16.4951 19.2086V19.2086C15.491 18.8873 14.4016 19.3385 13.9187 20.2758V20.2758C13.1151 21.8356 10.8849 21.8356 10.0813 20.2758V20.2758C9.59842 19.3385 8.50905 18.8873 7.50485 19.2086V19.2086C5.83365 19.7433 4.25668 18.1663 4.79139 16.4951V16.4951C5.1127 15.491 4.66147 14.4016 3.7242 13.9187V13.9187C2.16438 13.1151 2.16438 10.8849 3.7242 10.0813V10.0813C4.66147 9.59842 5.1127 8.50905 4.79139 7.50485V7.50485C4.25668 5.83365 5.83365 4.25668 7.50485 4.79139V4.79139C8.50905 5.1127 9.59842 4.66147 10.0813 3.7242V3.7242Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M14.8563 9.1903C15.0606 8.94984 15.3771 8.9385 15.6175 9.14289C15.858 9.34728 15.8229 9.66433 15.6185 9.9048L11.863 14.6558C11.6554 14.9001 11.2876 14.9258 11.048 14.7128L8.47656 12.4271C8.24068 12.2174 8.21944 11.8563 8.42911 11.6204C8.63877 11.3845 8.99996 11.3633 9.23583 11.5729L11.3706 13.4705L14.8563 9.1903Z"
                                                            fill="white" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Text-->
                                        <span
                                            class="fw-bold text-gray-600 fs-6 mb-2 d-block">{{ $patrimonio->descricao }}</span>
                                        <!--end::Text-->
                                        <!--begin::Info-->
                                        <div class="d-flex align-items-center flex-wrap fw-semibold fs-7 pe-2">
                                            <a href="#"
                                                class="d-flex align-items-center text-gray-400 text-hover-primary">{{ $patrimonio->localidade }}</a>
                                            <span class="bullet bullet-dot h-5px w-5px bg-gray-400 mx-3"></span>
                                            <a href="#"
                                                class="d-flex align-items-center text-gray-400 text-hover-primary">{{ date(' d-m-Y', strtotime($patrimonio->data)) }}
                                            </a>
                                            <span class="bullet bullet-dot h-5px w-5px bg-gray-400 mx-3"></span>
                                            <a href="#"
                                                class="text-gray-400 text-hover-primary">{{ $patrimonio->patrimonio }}</a>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::User-->
                                    <!--begin::Actions-->
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-sm btn-light me-3"
                                            id="kt_drawer_chat_toggle">Send Message</a>
                                        <!--begin::Action-->
                                        <a href="#" class="btn btn-sm btn-light me-3" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_create_app">Create App</a>
                                        <!--end::Action-->
                                    </div>
                                    <!--end::Actions-->
                                </div>
                                <!--end::Info-->
                            </div>
                            <!--end::Details-->
                        </div>
                    </div>
                    <!--end::Navbar-->
                    <!--begin::Nav items-->
                    <div id="kt_user_profile_nav" class="rounded bg-gray-200 d-flex flex-stack flex-wrap mb-9 p-2"
                        data-kt-sticky="true" data-kt-sticky-name="sticky-profile-navs"
                        data-kt-sticky-offset="{default: false, lg: '200px'}"
                        data-kt-sticky-width="{target: '#kt_user_profile_panel'}" data-kt-sticky-left="auto"
                        data-kt-sticky-top="70px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <!--begin::Nav-->
                        <ul class="nav flex-wrap border-transparent">
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1 active"
                                    data-bs-toggle="tab" href="#kt_customer_view_overview_tab">Overview</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_customer_view_overview_statements">Dados Escritura
                                </a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab"
                                    href="#kt_customer_view_overview_events_and_logs_tab">Anexos</a>
                            </li>
                            <!--end::Nav item-->
                        </ul>
                        <!--end::Nav-->
                    </div>
                    <!--end::Nav items-->

                    <!--begin:::Tab content-->
                    <div class="tab-content pt-5" id="myTabContent">
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade show active" id="kt_customer_view_overview_tab" role="tabpanel">
                            <!--begin::details View-->
                            <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
                                <!--begin::Card header-->
                                <div class="card-header cursor-pointer">
                                    <!--begin::Card title-->
                                    <div class="card-title m-0">
                                        <h3 class="fw-bold m-0">Detalhes de Patrimônio</h3>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Button-->
                                    @role('admin_user')
                                        <!-- Link habilitado com cadeado aberto para o usuário autorizado -->
                                        <a href="#" class="btn btn-sm btn-primary align-self-center"
                                            data-bs-toggle="modal" data-bs-target="#kt_modal_new_address">
                                            <i class="bi bi-unlock-fill"></i>
                                            <span>Editar</span>
                                        </a>
                                    @else
                                        <!-- Link desativado com cadeado fechado para outros usuários -->
                                        <a href="#" class="btn btn-sm btn-primary align-self-center disabled"
                                            aria-disabled="true">
                                            <i class="bi bi-lock-fill"></i>
                                            <span>Editar</span>
                                        </a>
                                    @endrole

                                    <!--end::Button-->
                                </div>
                                <!--begin::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body p-9">
                                    <!--begin::Input group-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Número RID
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="RID - Registro Mobiliario Diocesano"></i></label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 d-flex align-items-center">
                                            <span
                                                class="fw-bold fs-6 text-gray-800 me-2">{{ $patrimonio->codigo_rid }}</span>
                                            <span class="badge badge-success">Verificado</span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Row-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Descrição</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <span
                                                class="fw-bold fs-6 text-gray-800">{{ $patrimonio->descricao }}</span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Row-->
                                    <!--begin::Input group-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Território Foreiro
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="Terreno ou imóvel sobre o qual recai a obrigação de pagar uma renda ou foro ao proprietário original."></i>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <span
                                                class="fw-semibold text-gray-800 fs-6">{{ $patrimonio->patrimonio }}</span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Cidade</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <a href="#"
                                                class="fw-semibold fs-6 text-gray-800 text-hover-primary">{{ $patrimonio->localidade }}
                                                - {{ $patrimonio->uf }}</a>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Logradouro</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <span
                                                class="fw-bold fs-6 text-gray-800">{{ $patrimonio->logradouro }}</span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-7">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">CEP</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <span class="fw-bold fs-6 text-gray-800">{{ $patrimonio->cep }}</span>
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-10">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Bairro</label>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <div class="col-lg-8">
                                            <span
                                                class="fw-semibold fs-6 text-gray-800">{{ $patrimonio->bairro }}</span>
                                        </div>
                                        <!--begin::Label-->
                                    </div>
                                    <!--begin::Input group-->
                                    <div class="row mb-10">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 fw-semibold text-muted">Complemento</label>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <div class="col-lg-8">
                                            <span
                                                class="fw-semibold fs-6 text-gray-800">{{ $patrimonio->complemento }}</span>
                                        </div>
                                        <!--begin::Label-->
                                    </div>

                                    <!--end::Input group-->
                                    <div class="row d-flex rounded border border-dashed p-6 mb-10">
                                        <!--begin::Label-->
                                        <label class="col-2 fw-semibold text-muted">Livro</label>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <div class="col-2">
                                            <span
                                                class="fw-semibold fs-6 text-gray-800">{{ $patrimonio->livro }}</span>
                                        </div>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <label class="col-lg-2 fw-semibold text-muted">Folha</label>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <div class="col-lg-2">
                                            <span
                                                class="fw-semibold fs-6 text-gray-800">{{ $patrimonio->folha }}</span>
                                        </div>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <label class="col-lg-2 fw-semibold text-muted">Registro</label>
                                        <!--begin::Label-->
                                        <!--begin::Label-->
                                        <div class="col-lg-2">
                                            <span
                                                class="fw-semibold fs-6 text-gray-800">{{ $patrimonio->registro }}</span>
                                        </div>
                                        <!--begin::Label-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Notice-->
                                    <div
                                        class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                                        <!--begin::Icon-->
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                                        <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                    rx="10" fill="currentColor" />
                                                <rect x="11" y="14" width="7" height="2" rx="1"
                                                    transform="rotate(-90 11 14)" fill="currentColor" />
                                                <rect x="11" y="17" width="2" height="2" rx="1"
                                                    transform="rotate(-90 11 17)" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <!--end::Icon-->
                                        <!--begin::Wrapper-->
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <!--begin::Content-->
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">Atenção!</h4>
                                                <div class="fs-6 text-gray-700">Por favor, revise os detalhes acima. Se
                                                    houver alguma inconsistência, entre em contato <br> com o suporte ou
                                                    faça as correções necessárias.
                                                    <a class="fw-bold" href="#">Ajude-me</a>!
                                                </div>
                                            </div>
                                            <!--end::Content-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::details View-->
                            <!--begin::Row-->
                            <div class="row gy-5 g-xl-10">
                                @if ($patrimonio->escrituras->isNotEmpty())
                                    <!--begin::Col-->
                                    <div class="col-xl-7 mb-xl-10">
                                        <!--begin::Chart widget 5-->
                                        <div class="card card-flush h-lg-100">
                                            <!--begin::Header-->
                                            <div class="card-header flex-nowrap pt-5">
                                                <!--begin::Title-->
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label fw-bold text-dark">Dados da
                                                        Escritura</span>
                                                    <span class="text-gray-400 pt-2 fw-semibold fs-6">Dados dos últimos
                                                        proprietários</span>
                                                </h3>
                                                <!--end::Title-->
                                                <!--begin::Toolbar-->
                                                <div class="card-toolbar">
                                                    <!--begin::Menu-->
                                                    <button
                                                        class="btn btn-icon btn-color-gray-400 btn-active-color-primary justify-content-end"
                                                        data-kt-menu-trigger="click"
                                                        data-kt-menu-placement="bottom-end"
                                                        data-kt-menu-overflow="true">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen023.svg-->
                                                        <span class="svg-icon svg-icon-1 svg-icon-gray-300 me-n1">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect opacity="0.3" x="2" y="2" width="20"
                                                                    height="20" rx="4"
                                                                    fill="currentColor" />
                                                                <rect x="11" y="11" width="2.6" height="2.6"
                                                                    rx="1.3" fill="currentColor" />
                                                                <rect x="15" y="11" width="2.6" height="2.6"
                                                                    rx="1.3" fill="currentColor" />
                                                                <rect x="7" y="11" width="2.6" height="2.6"
                                                                    rx="1.3" fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </button>
                                                    <!--begin::Menu 2-->
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-150px"
                                                        data-kt-menu="true">
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <div class="menu-content fs-6 text-dark fw-bold px-3 py-4">
                                                                Ações rápidas</div>
                                                        </div>
                                                        <!--end::Menu item-->
                                                        <!--begin::Menu separator-->
                                                        <div class="separator mb-3 opacity-75"></div>
                                                        <!--end::Menu separator-->
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3">Cobrar Foro</a>
                                                        </div>
                                                        <!--end::Menu item-->
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3">Cobrar
                                                                Laudêmio</a>
                                                        </div>
                                                        <!--end::Menu item-->
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                            data-kt-menu-placement="right-start">
                                                            <!--begin::Menu item-->
                                                            <a href="#" class="menu-link px-3">
                                                                <span class="menu-title">New Group</span>
                                                                <span class="menu-arrow"></span>
                                                            </a>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu sub-->
                                                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3">Cobrar
                                                                        Foro</a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3">Cobrar
                                                                        Laudêmio</a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                                <!--begin::Menu item-->
                                                                <div class="menu-item px-3">
                                                                    <a href="#" class="menu-link px-3">Criar
                                                                        PTAM</a>
                                                                </div>
                                                                <!--end::Menu item-->
                                                            </div>
                                                            <!--end::Menu sub-->
                                                        </div>
                                                        <!--end::Menu item-->
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <a href="#" class="menu-link px-3">New Contact</a>
                                                        </div>
                                                        <!--end::Menu item-->
                                                        <!--begin::Menu separator-->
                                                        <div class="separator mt-3 opacity-75"></div>
                                                        <!--end::Menu separator-->
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <div class="menu-content px-3 py-3">
                                                                <a class="btn btn-primary btn-sm px-4" href="#"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#DM_modal_edit_escritura">
                                                                    Editar dados
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <!--end::Menu item-->
                                                    </div>
                                                    <!--end::Menu 2-->
                                                    <!--end::Menu-->
                                                </div>
                                                <!--end::Toolbar-->
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Body-->
                                            <div class="card-body pt-5 ps-6">
                                                <div class="separator separator-content my-5"><span
                                                        class="w-250px fw-bold">Outorgante</span></div>
                                                <!--begin::Card body-->
                                                <div class="card-body p-9">

                                                    <!--begin::Input group-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Outorgante
                                                            <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                                data-bs-toggle="tooltip"
                                                                title="Pessoa ou entidade que concede ou transfere(Vendedor)"></i></label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8 d-flex align-items-center">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()->outorgante ?? 'Sem escritura' }}
                                                            </span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Row-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Número da
                                                            Matrícula</label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()->matricula ?? 'Sem matricula' }}
                                                            </span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Row-->
                                                    <!--begin::Input group-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Data de
                                                            aquisição</label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8 fv-row">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                @if ($patrimonio->escrituras->last()?->aquisicao)
                                                                    {{ date('d/m/Y', strtotime($patrimonio->escrituras->last()->aquisicao)) }}
                                                                @else
                                                                    Sem data
                                                                @endif
                                                            </span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <div class="separator separator-content my-15"><span
                                                            class="w-250px fw-bold">Outorgado</span></div>
                                                    <!--begin::Input group-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Outorgado
                                                            <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                                data-bs-toggle="tooltip"
                                                                title="Pessoa ou entidade que recebe (comprador)"></i></label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()->outorgado ?? 'Sem Outorgado' }}
                                                            </span>
                                                        </div>
                                                        <!--end::Col-->

                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Valor de
                                                            Aquisição</label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()?->valor
                                                                    ? 'R$ ' . number_format($patrimonio->escrituras->last()->valor, 2, ',', '.')
                                                                    : 'Sem valor' }}
                                                            </span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="row mb-7">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Área
                                                            Total</label>
                                                        <!--end::Label-->
                                                        <!--begin::Col-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()?->area_total
                                                                    ? number_format($patrimonio->escrituras->last()->area_total, 2, ',', '.') . ' m²'
                                                                    : 'Sem área total' }}
                                                            </span>
                                                        </div>
                                                        <!--end::Col-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="row mb-10">
                                                        <!--begin::Label-->
                                                        <label class="col-lg-4 fw-semibold text-muted">Área
                                                            Privativa</label>
                                                        <!--begin::Label-->
                                                        <!--begin::Label-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()?->area_privativa
                                                                    ? number_format($patrimonio->escrituras->last()->area_privativa, 2, ',', '.') . ' m²'
                                                                    : 'Sem área privativa' }}
                                                            </span>
                                                        </div>
                                                        <!--begin::Label-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    <!--begin::Input group-->
                                                    <div class="row mb-10">
                                                        <!--begin::Label-->
                                                        <label
                                                            class="col-lg-4 fw-semibold text-muted">Informações</label>
                                                        <!--begin::Label-->
                                                        <!--begin::Label-->
                                                        <div class="col-lg-8">
                                                            <span class="fw-bold fs-6 text-gray-800 me-2">
                                                                {{ $patrimonio->escrituras->last()->informacoes ?? 'Sem Informações' }}
                                                            </span>
                                                        </div>
                                                        <!--begin::Label-->
                                                    </div>
                                                    <!--end::Input group-->
                                                </div>
                                                <!--end::Card body-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Chart widget 5-->
                                    </div>
                                    <!--end::Col-->
                                @else
                                    <!--begin::Col-->
                                    <div class="col-xl-7 mb-xl-10">
                                        <!--begin::Engage widget 11-->
                                        <div class="card card-flush h-xl-100"
                                            style="background-color: #202B46; background-image:url('/assets/media/svg/shapes/widget-bg-2.png')"
                                            data-bs-theme="light">
                                            <!--begin::Body-->
                                            <div
                                                class="card-body d-flex flex-column justify-content-between mt-6 bgi-no-repeat bgi-size-cover bgi-position-x-center">
                                                <!--begin::Wrapper-->
                                                <div class="mb-10">
                                                    <!--begin::Alerta de Cadastros Ausentes-->
                                                    <div class="mb-10">
                                                        <!--begin::Título-->
                                                        <div class="fs-1 fw-bold text-white text-center mb-9">
                                                            <span class="me-2">Cadastros não encontrados para o
                                                                <br />
                                                                <span class="position-relative d-inline-block">
                                                                    <a href="#"
                                                                        class="text-success opacity-75-hover">Outorgado</a>
                                                                    <!--begin::Separator-->
                                                                    <span
                                                                        class="position-absolute opacity-25 bottom-0 start-0 border-4 border-success border-bottom w-100"></span>
                                                                    <!--end::Separator-->
                                                                </span>
                                                                e
                                                                <span class="position-relative d-inline-block">
                                                                    <a href="#"
                                                                        class="text-success opacity-75-hover">Outorgante</a>
                                                                    <!--begin::Separator-->
                                                                    <span
                                                                        class="position-absolute opacity-25 bottom-0 start-0 border-4 border-success border-bottom w-100"></span>
                                                                    <!--end::Separator-->
                                                                </span>
                                                            </span>
                                                        </div>
                                                        <!--end::Título-->
                                                        <!--begin::Descrição-->
                                                        <div class="fs-4 text-white text-center mb-6">
                                                            Para dar continuidade ao processo de atualização do
                                                            patrimônio, realize<br /> o cadastro destes dados.
                                                        </div>
                                                        <!--end::Descrição-->
                                                        <!--begin::Ação-->
                                                        <div class="text-center">
                                                            <a href="#"
                                                                class="btn btn-sm btn-color-white bg-body bg-opacity-15 bg-hover-opacity-25 fw-bold fs-7"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#DM_modal_store_escritura">
                                                                Cadastrar Dados
                                                            </a>
                                                        </div>
                                                        <!--end::Ação-->
                                                    </div>
                                                    <!--end::Alerta de Cadastros Ausentes-->
                                                </div>

                                                <!--begin::Wrapper-->
                                                <!--begin::Illustration-->
                                                <img class="mx-auto h-150px h-lg-200px mb-11"
                                                    src="/assets/media/svg/illustrations/sigma/illustration-realestate.svg"
                                                    alt="" />
                                                <!--end::Illustration-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <!--end::Engage widget 11-->
                                    </div>
                                    <!--end::Col-->
                                @endif
<!--begin::Col-->
<div class="col-xl-5 mb-5 mb-xl-10">
    <!--begin::Engage widget 1-->
    <div class="card h-md-100" dir="ltr">
      <!--begin::Body-->
      <div class="card-body d-flex flex-column flex-center" style="position: relative; height: 400px; border-radius: 1px; overflow: hidden;">
        <!--begin::Heading-->
        <!-- Mapa da Página -->
        <div id="map" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; border-radius: 5px;"></div>
        <!-- Fim do Mapa da Página -->
        <!--end::Heading-->
        <!--begin::Links-->
        <div class="text-center mb-1" style="position: absolute; z-index: 10; bottom: 20px; width: 100%;">
          <!--begin::Link-->
          <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_select_location">Editar Localização</a>
          <!--end::Link-->
        </div>
        <!--end::Links-->
      </div>
      <!--end::Body-->
    </div>
    <!--end::Engage widget 1-->
  </div>
  <!--end::Col-->

                            </div>
                            <!--end::Row-->

                            <!--begin::Row-->
                            <div class="row gy-5 g-xl-10">

                                <!--begin::Col-->
                                <div class="col-xl-12">
                                    <!--begin::Table Widget 5-->
                                    <div class="card card-flush h-xl-100">
                                        <!--begin::Card header-->
                                        <div class="card-header pt-7">
                                            <!--begin::Title-->
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">Histórico de
                                                    Proprietários</span>
                                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Registro detalhado de
                                                    todos os proprietários associados ao patrimônio</span>
                                            </h3>
                                            <!--end::Title-->
                                            <!--begin::Actions-->
                                            <div class="card-toolbar">
                                                <!--begin::Filters-->
                                                <div class="d-flex flex-stack flex-wrap gap-4">
                                                    <!--begin::Destination-->
                                                    <div class="d-flex align-items-center fw-bold">
                                                        <!--begin::Label-->
                                                        <div class="text-muted fs-7 me-2">Cateogry</div>
                                                        <!--end::Label-->
                                                        <!--begin::Select-->
                                                        <select
                                                            class="form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto"
                                                            data-control="select2" data-hide-search="true"
                                                            data-dropdown-css-class="w-150px"
                                                            data-placeholder="Select an option">
                                                            <option></option>
                                                            <option value="Show All" selected="selected">Show All
                                                            </option>
                                                            <option value="a">Category A</option>
                                                            <option value="b">Category B</option>
                                                        </select>
                                                        <!--end::Select-->
                                                    </div>
                                                    <!--end::Destination-->
                                                    <!--begin::Status-->
                                                    <div class="d-flex align-items-center fw-bold">
                                                        <!--begin::Label-->
                                                        <div class="text-muted fs-7 me-2">Status</div>
                                                        <!--end::Label-->
                                                        <!--begin::Select-->
                                                        <select
                                                            class="form-select form-select-transparent text-dark fs-7 lh-1 fw-bold py-0 ps-3 w-auto"
                                                            data-control="select2" data-hide-search="true"
                                                            data-dropdown-css-class="w-150px"
                                                            data-placeholder="Select an option"
                                                            data-kt-table-widget-5="filter_status">
                                                            <option></option>
                                                            <option value="Show All" selected="selected">Show All
                                                            </option>
                                                            <option value="In Stock">In Stock</option>
                                                            <option value="Out of Stock">Out of Stock</option>
                                                            <option value="Low Stock">Low Stock</option>
                                                        </select>
                                                        <!--end::Select-->
                                                    </div>
                                                    <!--end::Status-->
                                                    <!--begin::Search-->
                                                    <a href="../../demo1/dist/apps/ecommerce/catalog/products.html"
                                                        class="btn btn-light btn-sm">View Stock</a>
                                                    <!--end::Search-->
                                                </div>
                                                <!--begin::Filters-->
                                            </div>
                                            <!--end::Actions-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body">
                                            <!--begin::Table-->
                                            <table class="table align-middle table-row-dashed fs-6 gy-3"
                                                id="kt_table_widget_5_table">
                                                <!--begin::Table head-->
                                                <thead>
                                                    <!--begin::Table row-->
                                                    <tr
                                                        class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-100px">Item</th>
                                                        <th class="text-end pe-3 min-w-100px">Product ID</th>
                                                        <th class="text-end pe-3 min-w-150px">Date Added</th>
                                                        <th class="text-end pe-3 min-w-100px">Price</th>
                                                        <th class="text-end pe-3 min-w-50px">Status</th>
                                                        <th class="text-end pe-0 min-w-25px">Qty</th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody class="fw-bold text-gray-600">
                                                    @foreach ($escrituras as $escritura)
                                                        <tr>
                                                            <!--begin::Item-->
                                                            <td>
                                                                <span
                                                                    class="text-dark text-hover-primary">{{ $escritura->outorgante }}</span>
                                                            </td>
                                                            <!--end::Item-->
                                                            <!--begin::Product ID-->
                                                            <td class="text-end">{{ $escritura->aquisicao }}</td>
                                                            <!--end::Product ID-->
                                                            <!--begin::Date added-->
                                                            <td class="text-end">
                                                                {{ \Carbon\Carbon::parse($escritura->aquisicao)->format('d M, Y') }}
                                                            </td>
                                                            <!--end::Date added-->
                                                            <!--begin::Price-->
                                                            <td class="text-end">$1,230</td>
                                                            <!--end::Price-->
                                                            <!--begin::Status-->
                                                            <td class="text-end">
                                                                <span
                                                                    class="badge py-3 px-4 fs-7 badge-light-primary">In
                                                                    Stock</span>
                                                            </td>
                                                            <!--end::Status-->
                                                            <!--begin::Qty-->
                                                            <td class="text-end" data-order="58">
                                                                <span class="text-dark fw-bold">58 PCS</span>
                                                            </td>
                                                            <!--end::Qty-->
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Table Widget 5-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->

                             {{-- *** Begin -Modal de Edição do Mapa *** --}}
                             @include('app.components.modals.patrimonio.mapas.mapa')
                             {{-- *** End - Modal de Edição do Mapa *** --}}

                        </div>
                        <!--end:::Tab pane-->
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade" id="kt_customer_view_overview_events_and_logs_tab"
                            role="tabpanel">
                            <!--begin::Card-->
                            <div class="card card-flush">
                                <!--begin::Card header-->
                                <div class="card-header pt-8">
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
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
                                            <input type="text" data-kt-filemanager-table-filter="search"
                                                class="form-control form-control-solid w-250px ps-15"
                                                placeholder="Pesquisar arquivos ..." />
                                        </div>
                                        <!--end::Search-->
                                    </div>
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Toolbar-->
                                        <div class="d-flex justify-content-end"
                                            data-kt-filemanager-table-toolbar="base">
                                            <!--begin::Back to folders-->
                                            <a href="#" class="btn btn-icon btn-light-primary me-3">
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
                                                <!--end::Svg Icon-->
                                            </a>
                                            <!--end::Back to folders-->
                                            <!--begin::Export-->
                                            <button disabled type="button" class="btn btn-light-primary me-3"
                                                id="kt_file_manager_new_folder">
                                                <!--begin::Svg Icon | path: icons/duotune/files/fil013.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.2C9.7 3 10.2 3.20001 10.4 3.60001ZM16 12H13V9C13 8.4 12.6 8 12 8C11.4 8 11 8.4 11 9V12H8C7.4 12 7 12.4 7 13C7 13.6 7.4 14 8 14H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V14H16C16.6 14 17 13.6 17 13C17 12.4 16.6 12 16 12Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.3"
                                                            d="M11 14H8C7.4 14 7 13.6 7 13C7 12.4 7.4 12 8 12H11V14ZM16 12H13V14H16C16.6 14 17 13.6 17 13C17 12.4 16.6 12 16 12Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->Nova Pasta</button>
                                            <!--end::Export-->
                                            <!--begin::Add customer-->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_upload">
                                                <!--begin::Svg Icon | path: icons/duotune/files/fil018.svg-->
                                                <span class="svg-icon svg-icon-2">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                            fill="currentColor" />
                                                        <path
                                                            d="M10.4 3.60001L12 6H21C21.6 6 22 6.4 22 7V19C22 19.6 21.6 20 21 20H3C2.4 20 2 19.6 2 19V4C2 3.4 2.4 3 3 3H9.20001C9.70001 3 10.2 3.20001 10.4 3.60001ZM16 11.6L12.7 8.29999C12.3 7.89999 11.7 7.89999 11.3 8.29999L8 11.6H11V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H16Z"
                                                            fill="currentColor" />
                                                        <path opacity="0.3"
                                                            d="M11 11.6V17C11 17.6 11.4 18 12 18C12.6 18 13 17.6 13 17V11.6H11Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->Upload de arquivos</button>
                                            <!--end::Add customer-->
                                        </div>
                                        <!--end::Toolbar-->
                                        <!--begin::Group actions-->
                                        <div class="d-flex justify-content-end align-items-center d-none"
                                            data-kt-filemanager-table-toolbar="selected">
                                            <div class="fw-bold me-5">
                                                <span class="me-2"
                                                    data-kt-filemanager-table-select="selected_count"></span>Selecionada
                                            </div>
                                            <button type="button" class="btn btn-danger"
                                                data-kt-filemanager-table-select="delete_selected">Excluir
                                                Selecionado</button>
                                        </div>
                                        <!--end::Group actions-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Table-->
                                    <table id="kt_file_manager_list" data-kt-filemanager-table="files"
                                        class="table align-middle table-row-dashed fs-6 gy-5">
                                        <!--begin::Table head-->
                                        <thead>
                                            <!--begin::Table row-->
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="w-10px pe-2">
                                                    <div
                                                        class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            data-kt-check="true"
                                                            data-kt-check-target="#kt_file_manager_list .form-check-input"
                                                            value="1" />
                                                    </div>
                                                </th>
                                                <th class="min-w-250px">Nome</th>
                                                <th class="min-w-30px">Tamanho</th>
                                                <th class="min-w-125px">Última modificação</th>
                                                <th class="min-w-125px">Última edição por</th>
                                                <th class="w-30px"></th>
                                            </tr>
                                            <!--end::Table row-->
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach ($anexos as $anexo)
                                                <tr data-file-id="{{ $anexo->id }}">
                                                    <!-- Certifique-se de que o ID está aqui -->
                                                    <!--begin::Checkbox-->
                                                    <td>
                                                        <div
                                                            class="form-check form-check-sm form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" />
                                                        </div>
                                                    </td>
                                                    <!--end::Checkbox-->
                                                    <!--begin::Name=-->
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Svg Icon | path: icons/duotune/files/fil003.svg-->
                                                            <span class="svg-icon svg-icon-2x svg-icon-primary me-4">
                                                                <svg width="24" height="24"
                                                                    viewBox="0 0 24 24" fill="none"
                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3"
                                                                        d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22Z"
                                                                        fill="currentColor" />
                                                                    <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                            <a href="{{ route('file', ['path' => $anexo->caminho_arquivo]) }}"
                                                                target="_blank"
                                                                class="text-gray-800 text-hover-primary">{{ $anexo->nome_arquivo }}</a>
                                                        </div>
                                                    </td>
                                                    <!--end::Name=-->
                                                    <!-- Beging::Tamanho -->
                                                    <td>{{ $anexo->formatted_file_size }}</td>
                                                    <!--end::Tamanho-->
                                                    <!-- Última modificação -->
                                                    <td>{{ $anexo->formatted_updated_at }}</td>
                                                    <!-- Última edição por -->
                                                    <td>{{ $anexo->uploader->name ?? 'Desconhecido' }}</td>
                                                    <!--begin::Actions-->
                                                    <td class="text-end" data-kt-filemanager-table="action_dropdown">
                                                        <div class="d-flex justify-content-end">
                                                            <!--begin::More-->
                                                            <div class="ms-2">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                                                    data-kt-menu-trigger="click"
                                                                    data-kt-menu-placement="bottom-end">
                                                                    <!-- Ícone SVG -->
                                                                    <span class="svg-icon svg-icon-5 m-0">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <rect x="10" y="10" width="4"
                                                                                height="4" rx="2"
                                                                                fill="currentColor" />
                                                                            <rect x="17" y="10" width="4"
                                                                                height="4" rx="2"
                                                                                fill="currentColor" />
                                                                            <rect x="3" y="10" width="4"
                                                                                height="4" rx="2"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                </button>

                                                                <!-- Menu Dropdown -->
                                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                                                    data-kt-menu="true">
                                                                    <div class="menu-item px-3">
                                                                        <a href="#"
                                                                            class="menu-link px-3">Download</a>
                                                                    </div>
                                                                    <div class="menu-item px-3">
                                                                        <a href="#" class="menu-link px-3"
                                                                            data-kt-filemanager-table="rename">Renomear</a>
                                                                    </div>
                                                                    <div class="menu-item px-3">
                                                                        <a href="#"
                                                                            class="menu-link text-danger px-3"
                                                                            data-kt-filemanager-table="delete_row">Excluir</a>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!--end::More-->
                                                        </div>
                                                    </td>
                                                    <!--end::Actions-->
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <script>
                                            // Passa a URL da rota de exclusão do Laravel com o placeholder ':id'
                                            const deleteUrl = "{{ route('patrimonioAnexo.destroy', ':id') }}";
                                        </script>

                                        <!--end::Table body-->
                                    </table>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                            <!--begin::Upload template-->
                            <table class="d-none">
                                <tr id="kt_file_manager_new_folder_row" data-kt-filemanager-template="upload">
                                    <td></td>
                                    <td id="kt_file_manager_add_folder_form" class="fv-row">
                                        <div class="d-flex align-items-center">
                                            <!--begin::Folder icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                            <span class="svg-icon svg-icon-2x svg-icon-primary me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Folder icon-->
                                            <!--begin:Input-->
                                            <input type="text" name="new_folder_name"
                                                placeholder="Enter the folder name"
                                                class="form-control mw-250px me-3" />
                                            <!--end:Input-->
                                            <!--begin:Submit button-->
                                            <button class="btn btn-icon btn-light-primary me-3"
                                                id="kt_file_manager_add_folder">
                                                <span class="indicator-label">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                                <span class="indicator-progress">
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle"></span>
                                                </span>
                                            </button>
                                            <!--end:Submit button-->
                                            <!--begin:Cancel button-->
                                            <button class="btn btn-icon btn-light-danger"
                                                id="kt_file_manager_cancel_folder">
                                                <span class="indicator-label">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="7.05025" y="15.5356"
                                                                width="12" height="2" rx="1"
                                                                transform="rotate(-45 7.05025 15.5356)"
                                                                fill="currentColor" />
                                                            <rect x="8.46447" y="7.05029" width="12"
                                                                height="2" rx="1"
                                                                transform="rotate(45 8.46447 7.05029)"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                                <span class="indicator-progress">
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle"></span>
                                                </span>
                                            </button>
                                            <!--end:Cancel button-->
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                            <!--end::Upload template-->
                            <!--begin::Rename template-->
                            <div class="d-none" data-kt-filemanager-template="rename">
                                <div class="fv-row">
                                    <div class="d-flex align-items-center">
                                        <span id="kt_file_manager_rename_folder_icon"></span>
                                        <input type="text" id="kt_file_manager_rename_input"
                                            name="rename_folder_name" placeholder="Enter the new folder name"
                                            class="form-control mw-250px me-3" value="" />
                                        <button class="btn btn-icon btn-light-primary me-3"
                                            id="kt_file_manager_rename_folder">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg-->
                                            <span class="svg-icon svg-icon-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <button class="btn btn-icon btn-light-danger"
                                            id="kt_file_manager_rename_folder_cancel">
                                            <span class="indicator-label">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr088.svg-->
                                                <span class="svg-icon svg-icon-1">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect opacity="0.5" x="7.05025" y="15.5356" width="12"
                                                            height="2" rx="1"
                                                            transform="rotate(-45 7.05025 15.5356)"
                                                            fill="currentColor" />
                                                        <rect x="8.46447" y="7.05029" width="12" height="2"
                                                            rx="1" transform="rotate(45 8.46447 7.05029)"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </span>
                                            <span class="indicator-progress">
                                                <span class="spinner-border spinner-border-sm align-middle"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--end::Rename template-->
                            <!--begin::Action template-->
                            <div class="d-none" data-kt-filemanager-template="action">
                                <div class="d-flex justify-content-end">
                                    <!--begin::Share link-->
                                    <div class="ms-2" data-kt-filemanger-table="copy_link">
                                        <button type="button"
                                            class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/coding/cod007.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3"
                                                        d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99737 7.5152 9.37919 7.02499 9.86804L3.47198 13.421C2.52954 14.3635 2.00009 15.6417 2.00009 16.9745C2.00009 18.3073 2.52957 19.5855 3.47202 20.528C4.41446 21.4704 5.69269 21.9999 7.02551 21.9999C8.35833 21.9999 9.63656 21.4704 10.579 20.528L14.132 16.975C14.5987 16.5084 14.9689 15.9544 15.2215 15.3447C15.4741 14.735 15.6041 14.0815 15.6041 13.4215C15.6041 12.7615 15.4741 12.108 15.2215 11.4983C14.9689 10.8886 14.5987 10.3347 14.132 9.86804Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>

                                    </div>
                                    <!--end::Share link-->
                                    <!--begin::More-->
                                    <div class="ms-2">
                                        <button type="button"
                                            class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen052.svg-->
                                            <span class="svg-icon svg-icon-5 m-0">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect x="10" y="10" width="4" height="4"
                                                        rx="2" fill="currentColor" />
                                                    <rect x="17" y="10" width="4" height="4"
                                                        rx="2" fill="currentColor" />
                                                    <rect x="3" y="10" width="4" height="4"
                                                        rx="2" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </button>
                                        <!--begin::Menu-->
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Download</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-filemanager-table="rename">Rename</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3"
                                                    data-kt-filemanager-table-filter="move_row"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#kt_modal_move_to_folder">Move to folder</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link text-danger px-3"
                                                    data-kt-filemanager-table-filter="delete_row">Delete</a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->
                                    </div>
                                    <!--end::More-->
                                </div>
                            </div>
                            <!--end::Action template-->
                            <!--begin::Checkbox template-->
                            <div class="d-none" data-kt-filemanager-template="checkbox">
                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" />
                                </div>
                            </div>
                            <!--end::Checkbox template-->
                            <!--begin::Modals-->
                            <!--begin::Modal - Upload File-->
                            <div class="modal fade" id="kt_modal_upload" tabindex="-1" aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Form-->
                                        <form class="form" id="kt_modal_upload_form"
                                            action="{{ route('patrimonioAnexo.store') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf <!-- Meta Tag para CSRF -->
                                            <meta name="csrf-token" content="{{ csrf_token() }}">
                                            <input type="hidden" name="patrimonio_id" id="patrimonio_id"
                                                value="{{ $patrimonio->id }}">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <h2 class="fw-bold">Upload de Arquivos</h2>
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                    data-bs-dismiss="modal">
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                height="2" rx="1"
                                                                transform="rotate(-45 6 17.3137)"
                                                                fill="currentColor" />
                                                            <rect x="7.41422" y="6" width="16" height="2"
                                                                rx="1" transform="rotate(45 7.41422 6)"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body pt-10 pb-15 px-lg-17">
                                                <!-- Dropzone HTML -->
                                                <div class="form-group">
                                                    <div class="dropzone dropzone-queue mb-2"
                                                        id="kt_modal_upload_dropzone">
                                                        <div class="dropzone-panel mb-4">
                                                            <a class="dropzone-select btn btn-sm btn-primary me-2">Anexar
                                                                Arquivos</a>
                                                            <a
                                                                class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload</a>
                                                            <a
                                                                class="dropzone-remove-all btn btn-sm btn-light-primary">Remover</a>
                                                        </div>

                                                        <!-- File Previews -->
                                                        <div class="dropzone-items wm-200px">
                                                            <div class="dropzone-item p-5" style="display:none">
                                                                <div class="dropzone-file">
                                                                    <div class="dropzone-filename text-dark"
                                                                        title="some_image_file_name.jpg">
                                                                        <span
                                                                            data-dz-name="">some_image_file_name.jpg</span>
                                                                        <strong>(<span
                                                                                data-dz-size="">340kb</span>)</strong>
                                                                    </div>
                                                                    <div class="dropzone-error mt-0"
                                                                        data-dz-errormessage=""></div>
                                                                </div>
                                                                <div class="dropzone-progress">
                                                                    <div class="progress bg-light-primary">
                                                                        <div class="progress-bar bg-primary"
                                                                            role="progressbar" aria-valuemin="0"
                                                                            aria-valuemax="100" aria-valuenow="0"
                                                                            data-dz-uploadprogress=""></div>
                                                                    </div>
                                                                </div>
                                                                <div class="dropzone-toolbar">
                                                                    <span class="dropzone-start">
                                                                        <i class="bi bi-play-fill fs-3"></i>
                                                                    </span>
                                                                    <span class="dropzone-cancel" data-dz-remove=""
                                                                        style="display: none;">
                                                                        <i class="bi bi-x fs-3"></i>
                                                                    </span>
                                                                    <span class="dropzone-delete" data-dz-remove="">
                                                                        <i class="bi bi-x fs-1"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="form-text fs-6 text-muted">Tamanho máximo do arquivo:
                                                        1MB por arquivo.</span>
                                                </div>
                                            </div>
                                            <!--end::Modal body-->
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Modal - Upload File-->

                            <!--begin::Modal - New Product-->
                            <div class="modal fade" id="kt_modal_move_to_folder" tabindex="-1"
                                aria-hidden="true">
                                <!--begin::Modal dialog-->
                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                    <!--begin::Modal content-->
                                    <div class="modal-content">
                                        <!--begin::Form-->
                                        <form class="form" action="#" id="kt_modal_move_to_folder_form">
                                            <!--begin::Modal header-->
                                            <div class="modal-header">
                                                <!--begin::Modal title-->
                                                <h2 class="fw-bold">Move to folder</h2>
                                                <!--end::Modal title-->
                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                                    data-bs-dismiss="modal">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                    <span class="svg-icon svg-icon-1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                                height="2" rx="1"
                                                                transform="rotate(-45 6 17.3137)"
                                                                fill="currentColor" />
                                                            <rect x="7.41422" y="6" width="16" height="2"
                                                                rx="1" transform="rotate(45 7.41422 6)"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                                <!--end::Close-->
                                            </div>
                                            <!--end::Modal header-->
                                            <!--begin::Modal body-->
                                            <div class="modal-body pt-10 pb-15 px-lg-17">
                                                <!--begin::Input group-->
                                                <div class="form-group fv-row">
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="0" id="kt_modal_move_to_folder_0" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_0">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->account
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="1" id="kt_modal_move_to_folder_1" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_1">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->apps
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="2" id="kt_modal_move_to_folder_2" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_2">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->widgets
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="3" id="kt_modal_move_to_folder_3" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_3">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->assets
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="4" id="kt_modal_move_to_folder_4" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_4">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->documentation
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="5" id="kt_modal_move_to_folder_5" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_5">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->layouts
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="6" id="kt_modal_move_to_folder_6" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_6">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->modals
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="7" id="kt_modal_move_to_folder_7" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_7">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->authentication
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="8" id="kt_modal_move_to_folder_8" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_8">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->dashboards
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                    <div class='separator separator-dashed my-5'></div>
                                                    <!--begin::Item-->
                                                    <div class="d-flex">
                                                        <!--begin::Checkbox-->
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <!--begin::Input-->
                                                            <input class="form-check-input me-3"
                                                                name="move_to_folder" type="radio"
                                                                value="9" id="kt_modal_move_to_folder_9" />
                                                            <!--end::Input-->
                                                            <!--begin::Label-->
                                                            <label class="form-check-label"
                                                                for="kt_modal_move_to_folder_9">
                                                                <div class="fw-bold">
                                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil012.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-2 svg-icon-primary me-2">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M10 4H21C21.6 4 22 4.4 22 5V7H10V4Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M9.2 3H3C2.4 3 2 3.4 2 4V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V7C22 6.4 21.6 6 21 6H12L10.4 3.60001C10.2 3.20001 9.7 3 9.2 3Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->pages
                                                                </div>
                                                            </label>
                                                            <!--end::Label-->
                                                        </div>
                                                        <!--end::Checkbox-->
                                                    </div>
                                                    <!--end::Item-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Action buttons-->
                                                <div class="d-flex flex-center mt-12">
                                                    <!--begin::Button-->
                                                    <button type="button" class="btn btn-primary"
                                                        id="kt_modal_move_to_folder_submit">
                                                        <span class="indicator-label">Save</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                    <!--end::Button-->
                                                </div>
                                                <!--begin::Action buttons-->
                                            </div>
                                            <!--end::Modal body-->
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                </div>
                            </div>
                            <!--end::Modal - Move file-->
                            <!--end::Modals-->
                        </div>
                        <!--end:::Tab pane-->
                        <!--begin:::Tab pane-->
                        <div class="tab-pane fade" id="kt_customer_view_overview_statements" role="tabpanel">
                            <!--begin::Earnings-->
                            <div class="card mb-6 mb-xl-9">
                                <!--begin::Header-->
                                <div class="card-header border-0">
                                    <div class="card-title">
                                        <h2>Dados Escritura</h2>
                                    </div>
                                </div>
                                <!--end::Header-->
                                <!--begin::Body-->
                                <div class="card-body py-0">
                                    <div class="fs-5 fw-semibold text-gray-500 mb-4">Last 30 day earnings calculated.
                                        Apart from arranging the order of topics.</div>
                                    <!--begin::Left Section-->
                                    <div class="d-flex flex-wrap flex-stack mb-5">
                                        <!--begin::Row-->
                                        <div class="d-flex flex-wrap">
                                            <!--begin::Col-->
                                            <div
                                                class="border border-dashed border-gray-300 w-150px rounded my-3 p-4 me-6">
                                                <span class="fs-1 fw-bold text-gray-800 lh-1">
                                                    <span data-kt-countup="true" data-kt-countup-value="6,840"
                                                        data-kt-countup-prefix="$">0</span>
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-1 svg-icon-success">
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
                                                </span>
                                                <span class="fs-6 fw-semibold text-muted d-block lh-1 pt-2">Net
                                                    Earnings</span>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div
                                                class="border border-dashed border-gray-300 w-125px rounded my-3 p-4 me-6">
                                                <span class="fs-1 fw-bold text-gray-800 lh-1">
                                                    <span class="" data-kt-countup="true"
                                                        data-kt-countup-value="16">0</span>%
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                    <span class="svg-icon svg-icon-1 svg-icon-danger">
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
                                                    <!--end::Svg Icon--></span>
                                                <span
                                                    class="fs-6 fw-semibold text-muted d-block lh-1 pt-2">Change</span>
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div
                                                class="border border-dashed border-gray-300 w-150px rounded my-3 p-4 me-6">
                                                <span class="fs-1 fw-bold text-gray-800 lh-1">
                                                    <span data-kt-countup="true" data-kt-countup-value="1,240"
                                                        data-kt-countup-prefix="$">0</span>
                                                    <span class="text-primary">--</span>
                                                </span>
                                                <span
                                                    class="fs-6 fw-semibold text-muted d-block lh-1 pt-2">Fees</span>
                                            </div>
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                        <a href="#"
                                            class="btn btn-sm btn-light-primary flex-shrink-0">Withdraw
                                            Earnings</a>
                                    </div>
                                    <!--end::Left Section-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <!--end::Earnings-->
                            <!--begin::Statements-->
                            <div class="card mb-6 mb-xl-9">
                                <!--begin::Header-->
                                <div class="card-header">
                                    <!--begin::Title-->
                                    <div class="card-title">
                                        <h2>Statement</h2>
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Toolbar-->
                                    <div class="card-toolbar">
                                        <!--begin::Tab nav-->
                                        <ul class="nav nav-stretch fs-5 fw-semibold nav-line-tabs nav-line-tabs-2x border-transparent"
                                            role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link text-active-primary active" data-bs-toggle="tab"
                                                    role="tab" href="#kt_customer_view_statement_1">This
                                                    Year</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link text-active-primary ms-3" data-bs-toggle="tab"
                                                    role="tab" href="#kt_customer_view_statement_2">2020</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link text-active-primary ms-3" data-bs-toggle="tab"
                                                    role="tab" href="#kt_customer_view_statement_3">2019</a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link text-active-primary ms-3" data-bs-toggle="tab"
                                                    role="tab" href="#kt_customer_view_statement_4">2018</a>
                                            </li>
                                        </ul>
                                        <!--end::Tab nav-->
                                    </div>
                                    <!--end::Toolbar-->
                                </div>
                                <!--end::Header-->
                                <!--begin::Card body-->
                                <div class="card-body pb-5">
                                    <!--begin::Tab Content-->
                                    <div id="kt_customer_view_statement_tab_content" class="tab-content">
                                        <!--begin::Tab panel-->
                                        <div id="kt_customer_view_statement_1"
                                            class="py-0 tab-pane fade show active" role="tabpanel">
                                            <!--begin::Table-->
                                            <table id="kt_customer_view_statement_table_1"
                                                class="table align-middle table-row-dashed fs-6 text-gray-600 fw-semibold gy-4">
                                                <!--begin::Table head-->
                                                <thead class="border-bottom border-gray-200">
                                                    <!--begin::Table row-->
                                                    <tr
                                                        class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="w-125px">Date</th>
                                                        <th class="w-100px">Order ID</th>
                                                        <th class="w-300px">Details</th>
                                                        <th class="w-100px">Amount</th>
                                                        <th class="w-100px text-end pe-7">Invoice</th>
                                                    </tr>
                                                    <!--end::Table row-->
                                                </thead>
                                                <!--end::Table head-->
                                                <!--begin::Table body-->
                                                <tbody>
                                                    <tr>
                                                        <td>Nov 01, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">102445788</a>
                                                        </td>
                                                        <td>Darknight transparency 36 Icons Pack</td>
                                                        <td class="text-success">$38.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Oct 24, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">423445721</a>
                                                        </td>
                                                        <td>Seller Fee</td>
                                                        <td class="text-danger">$-2.60</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Oct 08, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">312445984</a>
                                                        </td>
                                                        <td>Cartoon Mobile Emoji Phone Pack</td>
                                                        <td class="text-success">$76.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sep 15, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">312445984</a>
                                                        </td>
                                                        <td>Iphone 12 Pro Mockup Mega Bundle</td>
                                                        <td class="text-success">$5.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>May 30, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">523445943</a>
                                                        </td>
                                                        <td>Seller Fee</td>
                                                        <td class="text-danger">$-1.30</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Apr 22, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">231445943</a>
                                                        </td>
                                                        <td>Parcel Shipping / Delivery Service App</td>
                                                        <td class="text-success">$204.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Feb 09, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">426445943</a>
                                                        </td>
                                                        <td>Visual Design Illustration</td>
                                                        <td class="text-success">$31.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nov 01, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">984445943</a>
                                                        </td>
                                                        <td>Abstract Vusial Pack</td>
                                                        <td class="text-success">$52.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jan 04, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">324442313</a>
                                                        </td>
                                                        <td>Seller Fee</td>
                                                        <td class="text-danger">$-0.80</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nov 01, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">102445788</a>
                                                        </td>
                                                        <td>Darknight transparency 36 Icons Pack</td>
                                                        <td class="text-success">$38.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Oct 24, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">423445721</a>
                                                        </td>
                                                        <td>Seller Fee</td>
                                                        <td class="text-danger">$-2.60</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Oct 08, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">312445984</a>
                                                        </td>
                                                        <td>Cartoon Mobile Emoji Phone Pack</td>
                                                        <td class="text-success">$76.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sep 15, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">312445984</a>
                                                        </td>
                                                        <td>Iphone 12 Pro Mockup Mega Bundle</td>
                                                        <td class="text-success">$5.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>May 30, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">523445943</a>
                                                        </td>
                                                        <td>Seller Fee</td>
                                                        <td class="text-danger">$-1.30</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Apr 22, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">231445943</a>
                                                        </td>
                                                        <td>Parcel Shipping / Delivery Service App</td>
                                                        <td class="text-success">$204.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Feb 09, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">426445943</a>
                                                        </td>
                                                        <td>Visual Design Illustration</td>
                                                        <td class="text-success">$31.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nov 01, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">984445943</a>
                                                        </td>
                                                        <td>Abstract Vusial Pack</td>
                                                        <td class="text-success">$52.00</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jan 04, 2021</td>
                                                        <td>
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary">324442313</a>
                                                        </td>
                                                        <td>Seller Fee</td>
                                                        <td class="text-danger">$-0.80</td>
                                                        <td class="text-end">
                                                            <button
                                                                class="btn btn-sm btn-light btn-active-light-primary">Download</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <!--end::Table body-->
                                            </table>
                                            <!--end::Table-->
                                        </div>
                                        <!--end::Tab panel-->
                                    </div>
                                    <!--end::Tab Content-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Statements-->
                        </div>
                        <!--end:::Tab pane-->
                    </div>
                    <!--end:::Tab content-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->

    <!--begin::Modal - Dados do Patrimonio-->
    @include('app.components.modals.patrimonio.edit-patrimonio')
    <!--end::Modal - Dados do Patrimonio-->

    <!--begin::Modal - Dados do Editar Escritura-->
    @include('app.components.modals.patrimonio.escrituras.edit-escritura')
    <!--end::Modal - Dados do Editar Escritura-->
    @include('app.components.modals.patrimonio.escrituras.store-escritura')



    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA9tPypF5Ic2DXAMrJwiX1Dsj5FVi_SuPs&callback=initMap"></script>

</x-tenant-app-layout>
<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/file-manager/patrimonioAnexo.js"></script>
<script src="/assets/js/custom/apps/invoices/create.js"></script>

<script src="/assets/js/custom/utilities/modals/patrimonio/ptam/ptam.js"></script>


<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
