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
                            Editar Organismo</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
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
                            <li class="breadcrumb-item text-muted">
                                <a class="text-muted text-hover-primary">Configuração</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Organismo</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Navbar-->
                    <div class="card card-flush mb-9" id="kt_user_profile_panel">
                        <!--begin::Hero nav-->
                        <div class="card-header rounded-top bgi-size-cover h-200px"
                            style="background-position: 100% 100%; background-image:url('/assets/media/misc/profile-head-bg1.jpg')">
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
                                        <img src="{{ $company->avatar ? route('file', ['path' => $company->avatar]) : '/assets/media/avatars/apple-touch-icon.svg' }}"
                                            alt="image" class="border border-white border-4"
                                            style="border-radius: 20px" />
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
                                                    Payments</div>
                                            </div>
                                            <!--end::Heading-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3">Create Invoice</a>
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
                                                <a href="#" class="menu-link px-3">Settings</a>
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
                                                class="text-gray-800 text-hover-primary fs-2 fw-bolder me-1">{{ $company->name }}</a>
                                            <a href="#" class="" data-bs-toggle="tooltip"
                                                data-bs-placement="right" title="Account is verified">
                                                <!--begin::Svg Icon | path: icons/duotune/general/gen026.svg-->
                                                <i class="bi bi-check-circle-fill text-success fs-2"></i>
                                                <!--end::Svg Icon-->
                                            </a>
                                        </div>
                                        <!--end::Name-->
                                        <!--begin::Text-->
                                        @if ($company->addresses)
                                            <span class="fw-bold text-gray-600 fs-6 mb-2 d-block">{{ $company->addresses->rua }},
                                                {{ $company->addresses->numero }} - {{ $company->addresses->bairro }} -
                                                {{ $company->addresses->cidade }} -
                                                {{ $company->addresses->uf }}</span>
                                        @else
                                            <span class="fw-bold text-gray-600 fs-6 mb-2 d-block">Endereço não
                                                informado</span>
                                        @endif
                                        <!--end::Text-->
                                        <!--begin::Info-->
                                        <div class="d-flex align-items-center flex-wrap fw-semibold fs-7 pe-2">
                                            <a href="#"
                                                class="d-flex align-items-center text-gray-400 text-hover-primary">UI/UX
                                                Design</a>
                                            <span class="bullet bullet-dot h-5px w-5px bg-gray-400 mx-3"></span>
                                            <a href="#"
                                                class="d-flex align-items-center text-gray-400 text-hover-primary">Austin,
                                                TX</a>
                                            <span class="bullet bullet-dot h-5px w-5px bg-gray-400 mx-3"></span>
                                            <a href="#" class="text-gray-400 text-hover-primary">3,450
                                                Followers</a>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::User-->
                                    <!--begin::Actions-->
                                    <div class="d-flex">
                                        <a href="#" class="btn btn-sm btn-light me-3"
                                            id="kt_drawer_chat_toggle">Send Message</a>
                                        <button class="btn btn-sm btn-primary" id="kt_user_follow_button">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr012.svg-->
                                            <span class="svg-icon svg-icon-3 d-none">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3"
                                                        d="M10 18C9.7 18 9.5 17.9 9.3 17.7L2.3 10.7C1.9 10.3 1.9 9.7 2.3 9.3C2.7 8.9 3.29999 8.9 3.69999 9.3L10.7 16.3C11.1 16.7 11.1 17.3 10.7 17.7C10.5 17.9 10.3 18 10 18Z"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M10 18C9.7 18 9.5 17.9 9.3 17.7C8.9 17.3 8.9 16.7 9.3 16.3L20.3 5.3C20.7 4.9 21.3 4.9 21.7 5.3C22.1 5.7 22.1 6.30002 21.7 6.70002L10.7 17.7C10.5 17.9 10.3 18 10 18Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--begin::Indicator label-->
                                            <span class="indicator-label">Follow</span>
                                            <!--end::Indicator label-->
                                            <!--begin::Indicator progress-->
                                            <span class="indicator-progress">Please wait...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            <!--end::Indicator progress-->
                                        </button>
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
                        data-kt-page-scroll-position="400" data-kt-sticky="true"
                        data-kt-sticky-name="sticky-profile-navs"
                        data-kt-sticky-offset="{default: false, lg: '200px'}"
                        data-kt-sticky-width="{target: '#kt_user_profile_panel'}" data-kt-sticky-left="auto"
                        data-kt-sticky-top="70px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <!--begin::Nav-->
                        <ul class="nav flex-wrap border-transparent" role="tablist">
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1 active"
                                    data-bs-toggle="tab" href="#kt_tab_detalhes" role="tab"
                                    aria-selected="true">Detalhes</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_editar" role="tab" aria-selected="false"
                                    id="nav-tab-editar">Editar</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_horario_missas" role="tab"
                                    aria-selected="false">Horários de Missa</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_security" role="tab"
                                    aria-selected="false">Security</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_activity" role="tab"
                                    aria-selected="false">Activity</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_billing" role="tab"
                                    aria-selected="false">Billing</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_statements" role="tab"
                                    aria-selected="false">Statements</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_referrals" role="tab"
                                    aria-selected="false">Referrals</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_api_keys" role="tab"
                                    aria-selected="false">API Keys</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1" role="presentation">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    data-bs-toggle="tab" href="#kt_tab_logs" role="tab"
                                    aria-selected="false">Logs</a>
                            </li>
                            <!--end::Nav item-->
                        </ul>
                        <!--end::Nav-->
                    </div>
                    <!--end::Nav items-->

                    <!--begin::Tab content-->
                    <div class="tab-content" id="kt_user_profile_tab_content">
                        <!--begin::Tab pane - Detalhes (padrão)-->
                        <div class="tab-pane fade show active" id="kt_tab_detalhes" role="tabpanel">
                            @include('app.company.partials.details')
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Editar-->
                        <div class="tab-pane fade" id="kt_tab_editar" role="tabpanel">
                            @include('app.company.partials.edit')
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Visão Geral-->
                        <div class="tab-pane fade" id="kt_tab_horario_missas" role="tabpanel">
                            @include('app.company.partials.horarios-missas')
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Security-->
                        <div class="tab-pane fade" id="kt_tab_security" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de Security</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Activity-->
                        <div class="tab-pane fade" id="kt_tab_activity" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de Activity</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Billing-->
                        <div class="tab-pane fade" id="kt_tab_billing" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de Billing</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Statements-->
                        <div class="tab-pane fade" id="kt_tab_statements" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de Statements</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Referrals-->
                        <div class="tab-pane fade" id="kt_tab_referrals" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de Referrals</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - API Keys-->
                        <div class="tab-pane fade" id="kt_tab_api_keys" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de API Keys</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->

                        <!--begin::Tab pane - Logs-->
                        <div class="tab-pane fade" id="kt_tab_logs" role="tabpanel">
                            <div class="card mb-5 mb-xl-10">
                                <div class="card-body p-9">
                                    <p class="text-muted">Conteúdo de Logs</p>
                                </div>
                            </div>
                        </div>
                        <!--end::Tab pane-->
                    </div>
                    <!--end::Tab content-->
                </div>
                <!--end::Basic info-->

            </div>
        </div>
    </div>
    </div>

    <script>
        // Função para ativar a tab de edição
        document.addEventListener('DOMContentLoaded', function() {
            const btnEditarOrganismo = document.getElementById('btn-editar-organismo','nav-tab-editar');
            const navTabEditar = document.getElementById('nav-tab-editar');

            if (btnEditarOrganismo && navTabEditar) {
                btnEditarOrganismo.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Ativa a tab de edição usando Bootstrap
                    const tab = new bootstrap.Tab(navTabEditar);
                    tab.show();

                    // Scroll suave para a tab
                    setTimeout(() => {
                        navTabEditar.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);
                });
            }
        });
    </script>

</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/formrepeater/formrepeater.bundle.js"></script>
<!--end::Vendors Javascript-->

<!--begin::Custom Javascript(used for this page only)-->
<script>
    // Debug: Verificar se scripts foram carregados
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DEBUG HORÁRIOS DE MISSAS ===');
        console.log('jQuery carregado:', typeof $ !== 'undefined' && typeof jQuery !== 'undefined');
        console.log('jQuery.repeater carregado:', typeof $.fn.repeater !== 'undefined');
        console.log('KTUtil carregado:', typeof KTUtil !== 'undefined');
        console.log('tempusDominus carregado:', typeof tempusDominus !== 'undefined');
        console.log('Elemento #kt_horarios_missas_repeater existe:', $('#kt_horarios_missas_repeater').length);

        // Listener para quando a tab de Horários de Missas for mostrada
        const tabTrigger = document.querySelector('[href="#kt_tab_horario_missas"]');
        if (tabTrigger) {
            console.log('Tab trigger encontrado!');

            // Inicializar quando a tab for mostrada pela primeira vez
            let tabActivated = false;
            tabTrigger.addEventListener('shown.bs.tab', function (event) {
                console.log('Tab Horários de Missas ativada!');

                if (!tabActivated) {
                    tabActivated = true;
                    setTimeout(() => {
                        console.log('Tentando inicializar KTHorariosMissas da tab...');
                        if (typeof KTHorariosMissas !== 'undefined') {
                            console.log('KTHorariosMissas encontrado!');
                            // Verificar se o repeater foi inicializado
                            const repeaterCheck = $('#kt_horarios_missas_repeater').data('repeater');
                            console.log('Repeater já inicializado?', repeaterCheck !== undefined);
                        } else {
                            console.error('KTHorariosMissas NÃO está definido!');
                        }
                    }, 100);
                }
            });
        }
    });
</script>
<script src="/assets/js/custom/apps/company/horarios-missas.js?v={{ time() }}"></script>
<!--end::Custom Javascript-->
