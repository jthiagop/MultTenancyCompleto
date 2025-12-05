                <!--begin::Header-->
                <div id="kt_app_header" class="app-header">
                    <!--begin::Header container-->
                    <div class="app-container container-fluid px-4 d-flex align-items-stretch justify-content-between"
                        id="kt_app_header_container">
                        <!--begin::Logo-->
                        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
                            <a href="/#index.html">
                                @include('app.components.application-logo')
                            </a>

                        </div>
                        <!--end::Logo-->

                        <!--begin::Header wrapper-->
                        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1"
                            id="kt_app_header_wrapper">
                            <!--begin::Menu wrapper-->
                            <div class="app-header-menu app-header-mobile-drawer align-items-stretch"
                                data-kt-drawer="true" data-kt-drawer-name="app-header-menu"
                                data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
                                data-kt-drawer-width="225px" data-kt-drawer-direction="end"
                                data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
                                data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
                                data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
                                <!--begin::Menu-->
                                <div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
                                    id="kt_app_header_menu" data-kt-menu="true">
                                    <!--begin:Menu item-->
                                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                        data-kt-menu-placement="bottom-start"
                                        class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
                                        <!--begin:Menu link-->
                                        <span class="menu-link btn btn-sm ">
                                            <span class="menu-title">Dashboard</span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div
                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0 w-100 w-lg-600px">
                                            <!--begin:Dashboards menu-->
                                            <div class="menu-state-bg menu-extended overflow-hidden overflow-lg-visible py-6"
                                                data-kt-menu-dismiss="true">
                                                <!--begin:Row-->
                                                <div class="row px-5">
                                                    <!--begin:Col-->
                                                    <div class="col-lg-6 py-1">
                                                        <!--begin:Menu item-->
                                                        <div class="menu-item p-0 m-0">
                                                            <!--begin:Menu link-->
                                                            <a href="{{ route('dashboard') }}" class="menu-link">
                                                                <span
                                                                    class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                                    <span class="svg-icon svg-icon-danger svg-icon-1">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <rect x="2" y="2" width="9"
                                                                                height="9" rx="2"
                                                                                fill="currentColor" />
                                                                            <rect opacity="0.3" x="13" y="2"
                                                                                width="9" height="9"
                                                                                rx="2" fill="currentColor" />
                                                                            <rect opacity="0.3" x="13" y="13"
                                                                                width="9" height="9"
                                                                                rx="2" fill="currentColor" />
                                                                            <rect opacity="0.3" x="2" y="13"
                                                                                width="9" height="9"
                                                                                rx="2" fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <span class="d-flex flex-column">
                                                                    <span class="fs-6 fw-semibold text-gray-800">Tela
                                                                        Inicial</span>
                                                                    <span class="fs-7 fw-semibold text-muted">Modulos
                                                                        principais</span>
                                                                </span>
                                                            </a>
                                                            <!--end:Menu link-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                    </div>
                                                    <!--end:Col-->

                                                    @if (auth()->user()->hasRole('admin'))
                                                        <!--begin::Col - Organismos (DESBLOQUEADO)-->
                                                        <div class="col-lg-6 py-1">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item p-0 m-0">
                                                                <!--begin:Menu link-->
                                                                <a href="{{ route('company.index') }}"
                                                                    class="menu-link">
                                                                    <span
                                                                        class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                        <span
                                                                            class="svg-icon svg-icon-success svg-icon-1">
                                                                            <svg width="24" height="25"
                                                                                viewBox="0 0 24 25" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path opacity="0.3"
                                                                                    d="M8.9 21L7.19999 22.6999C6.79999 23.0999 6.2 23.0999 5.8 22.6999L4.1 21H8.9ZM4 16.0999L2.3 17.8C1.9 18.2 1.9 18.7999 2.3 19.1999L4 20.9V16.0999ZM19.3 9.1999L15.8 5.6999C15.4 5.2999 14.8 5.2999 14.4 5.6999L9 11.0999V21L19.3 10.6999C19.7 10.2999 19.7 9.5999 19.3 9.1999Z"
                                                                                    fill="currentColor" />
                                                                                <path
                                                                                    d="M21 15V20C21 20.6 20.6 21 20 21H11.8L18.8 14H20C20.6 14 21 14.4 21 15ZM10 21V4C10 3.4 9.6 3 9 3H4C3.4 3 3 3.4 3 4V21C3 21.6 3.4 22 4 22H9C9.6 22 10 21.6 10 21ZM7.5 18.5C7.5 19.1 7.1 19.5 6.5 19.5C5.9 19.5 5.5 19.1 5.5 18.5C5.5 17.9 5.9 17.5 6.5 17.5C7.1 17.5 7.5 17.9 7.5 18.5Z"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                    <span class="d-flex flex-column">
                                                                        <span
                                                                            class="fs-6 fw-semibold text-gray-800">Organismos</span>
                                                                        <span
                                                                            class="fs-7 fw-semibold text-muted">Gerenciar
                                                                            empresas e filiais</span>
                                                                    </span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Col-->
                                                    @else
                                                        <!--begin::Col - Organismos (BLOQUEADO)-->
                                                        <div class="col-lg-6 py-1">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item p-0 m-0">
                                                                <!--begin:Menu link-->
                                                                <a href="#" class="menu-link disabled"
                                                                    aria-disabled="true">
                                                                    <span
                                                                        class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                        <span
                                                                            class="svg-icon svg-icon-gray-400 svg-icon-1">
                                                                            <svg width="24" height="25"
                                                                                viewBox="0 0 24 25" fill="none"
                                                                                xmlns="http://www.w3.org/2000/svg">
                                                                                <path opacity="0.3"
                                                                                    d="M8.9 21L7.19999 22.6999C6.79999 23.0999 6.2 23.0999 5.8 22.6999L4.1 21H8.9ZM4 16.0999L2.3 17.8C1.9 18.2 1.9 18.7999 2.3 19.1999L4 20.9V16.0999ZM19.3 9.1999L15.8 5.6999C15.4 5.2999 14.8 5.2999 14.4 5.6999L9 11.0999V21L19.3 10.6999C19.7 10.2999 19.7 9.5999 19.3 9.1999Z"
                                                                                    fill="currentColor" />
                                                                                <path
                                                                                    d="M21 15V20C21 20.6 20.6 21 20 21H11.8L18.8 14H20C20.6 14 21 14.4 21 15ZM10 21V4C10 3.4 9.6 3 9 3H4C3.4 3 3 3.4 3 4V21C3 21.6 3.4 22 4 22H9C9.6 22 10 21.6 10 21ZM7.5 18.5C7.5 19.1 7.1 19.5 6.5 19.5C5.9 19.5 5.5 19.1 5.5 18.5C5.5 17.9 5.9 17.5 6.5 17.5C7.1 17.5 7.5 17.9 7.5 18.5Z"
                                                                                    fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                    </span>
                                                                    <span class="d-flex flex-column">
                                                                        <span
                                                                            class="fs-6 fw-semibold text-gray-800">Organismos</span>
                                                                        <span class="fs-7 fw-semibold text-muted">Acesso
                                                                            restrito</span>
                                                                    </span>
                                                                    <span class="ms-auto">
                                                                        <i
                                                                            class="fa-solid fa-lock fs-4 text-danger"></i>
                                                                    </span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Col-->
                                                    @endif


                                                    <!--begin:Col-->
                                                    <div class="col-lg-6 py-1">
                                                        <!--begin:Menu item-->
                                                        <div class="menu-item p-0 m-0">
                                                            <!--begin:Menu link-->
                                                            <a href="{{ route('caixa.index') }}" class="menu-link">
                                                                <span
                                                                    class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                    <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/keenthemes/keen/docs/core/html/src/media/icons/duotune/finance/fin003.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-dark svg-icon-1"><svg
                                                                            width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M20 18H4C3.4 18 3 17.6 3 17V7C3 6.4 3.4 6 4 6H20C20.6 6 21 6.4 21 7V17C21 17.6 20.6 18 20 18ZM12 8C10.3 8 9 9.8 9 12C9 14.2 10.3 16 12 16C13.7 16 15 14.2 15 12C15 9.8 13.7 8 12 8Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M18 6H20C20.6 6 21 6.4 21 7V9C19.3 9 18 7.7 18 6ZM6 6H4C3.4 6 3 6.4 3 7V9C4.7 9 6 7.7 6 6ZM21 17V15C19.3 15 18 16.3 18 18H20C20.6 18 21 17.6 21 17ZM3 15V17C3 17.6 3.4 18 4 18H6C6 16.3 4.7 15 3 15Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <span class="d-flex flex-column">
                                                                    <span
                                                                        class="fs-6 fw-semibold text-gray-800">Financeiro</span>
                                                                    <span class="fs-7 fw-semibold text-muted">Controle
                                                                        de gastos</span>
                                                                </span>
                                                            </a>
                                                            <!--end:Menu link-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                    </div>
                                                    <!--end:Col-->
                                                    <!--begin:Col-->
                                                    <div class="col-lg-6 py-1">
                                                        <!--begin:Menu item-->
                                                        <div class="menu-item p-0 m-0">
                                                            <!--begin:Menu link-->
                                                            <a href="{{ route('patrimonio.index') }}"
                                                                class="menu-link">
                                                                <span
                                                                    class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                    <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm002.svg-->
                                                                    <span
                                                                        class="svg-icon svg-icon-primary svg-icon-1"><svg
                                                                            width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M13.625 22H9.625V3C9.625 2.4 10.025 2 10.625 2H12.625C13.225 2 13.625 2.4 13.625 3V22Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M19.625 10H12.625V4H19.625L21.025 6.09998C21.325 6.59998 21.325 7.30005 21.025 7.80005L19.625 10Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M3.62499 16H10.625V10H3.62499L2.225 12.1001C1.925 12.6001 1.925 13.3 2.225 13.8L3.62499 16Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </span>
                                                                <span class="d-flex flex-column">
                                                                    <span
                                                                        class="fs-6 fw-semibold text-gray-800">Patrimônio</span>
                                                                    <span class="fs-7 fw-semibold text-muted">Foro e
                                                                        Laudêmio</span>
                                                                </span>
                                                            </a>
                                                            <!--end:Menu link-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                    </div>
                                                    <!--end:Col-->
                                                </div>
                                                <!--end:Row-->
                                            </div>
                                            <!--end:Dashboards menu-->
                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                        data-kt-menu-placement="bottom-start"
                                        class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2 here show here show">

                                        <!--begin:Menu link-->
                                        <span class="menu-link btn btn-sm">
                                            <span class="menu-title">Serviços</span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div
                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">
                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/graphs/gra006.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3"
                                                                    d="M12.5 22C11.9 22 11.5 21.6 11.5 21V3C11.5 2.4 11.9 2 12.5 2C13.1 2 13.5 2.4 13.5 3V21C13.5 21.6 13.1 22 12.5 22Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M17.8 14.7C17.8 15.5 17.6 16.3 17.2 16.9C16.8 17.6 16.2 18.1 15.3 18.4C14.5 18.8 13.5 19 12.4 19C11.1 19 10 18.7 9.10001 18.2C8.50001 17.8 8.00001 17.4 7.60001 16.7C7.20001 16.1 7 15.5 7 14.9C7 14.6 7.09999 14.3 7.29999 14C7.49999 13.8 7.80001 13.6 8.20001 13.6C8.50001 13.6 8.69999 13.7 8.89999 13.9C9.09999 14.1 9.29999 14.4 9.39999 14.7C9.59999 15.1 9.8 15.5 10 15.8C10.2 16.1 10.5 16.3 10.8 16.5C11.2 16.7 11.6 16.8 12.2 16.8C13 16.8 13.7 16.6 14.2 16.2C14.7 15.8 15 15.3 15 14.8C15 14.4 14.9 14 14.6 13.7C14.3 13.4 14 13.2 13.5 13.1C13.1 13 12.5 12.8 11.8 12.6C10.8 12.4 9.99999 12.1 9.39999 11.8C8.69999 11.5 8.19999 11.1 7.79999 10.6C7.39999 10.1 7.20001 9.39998 7.20001 8.59998C7.20001 7.89998 7.39999 7.19998 7.79999 6.59998C8.19999 5.99998 8.80001 5.60005 9.60001 5.30005C10.4 5.00005 11.3 4.80005 12.3 4.80005C13.1 4.80005 13.8 4.89998 14.5 5.09998C15.1 5.29998 15.6 5.60002 16 5.90002C16.4 6.20002 16.7 6.6 16.9 7C17.1 7.4 17.2 7.69998 17.2 8.09998C17.2 8.39998 17.1 8.7 16.9 9C16.7 9.3 16.4 9.40002 16 9.40002C15.7 9.40002 15.4 9.29995 15.3 9.19995C15.2 9.09995 15 8.80002 14.8 8.40002C14.6 7.90002 14.3 7.49995 13.9 7.19995C13.5 6.89995 13 6.80005 12.2 6.80005C11.5 6.80005 10.9 7.00005 10.5 7.30005C10.1 7.60005 9.79999 8.00002 9.79999 8.40002C9.79999 8.70002 9.9 8.89998 10 9.09998C10.1 9.29998 10.4 9.49998 10.6 9.59998C10.8 9.69998 11.1 9.90002 11.4 9.90002C11.7 10 12.1 10.1 12.7 10.3C13.5 10.5 14.2 10.7 14.8 10.9C15.4 11.1 15.9 11.4 16.4 11.7C16.8 12 17.2 12.4 17.4 12.9C17.6 13.4 17.8 14 17.8 14.7Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-title">Financeiro</span>
                                                    <span class="menu-arrow"></span>
                                                </span>
                                                <!--end:Menu link-->
                                                <!--begin:Menu sub-->
                                                <div
                                                    class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                    <!--begin:Menu item-->
                                                    <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                        data-kt-menu-placement="right-start"
                                                        class="menu-item menu-lg-down-accordion">
                                                        <!--begin:Menu link-->
                                                        <span class="menu-link">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Módulo de Financeiro</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        <!--end:Menu link-->
                                                        <!--begin:Menu sub-->
                                                        <div
                                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a
                                                                    class="menu-link {{ Route::currentRouteName() == 'caixa.list' ? 'active' : '' }}"href="{{ route('caixa.list') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Transações do Caixa</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a
                                                                    class="menu-link {{ Route::currentRouteName() == 'banco.list' ? 'active' : '' }}"href="{{ route('banco.list') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Transações
                                                                        Bancárias</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Menu sub-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a
                                                            class="menu-link {{ Route::currentRouteName() == 'prestacao.list' ? 'active' : '' }}"href="{{ route('relatorios.prestacao.de.contas') }}">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Prestação de Conta</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                        data-kt-menu-placement="right-start"
                                                        class="menu-item menu-lg-down-accordion">
                                                        <!--begin:Menu link-->
                                                        <span class="menu-link">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Cadastros</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        <!--end:Menu link-->
                                                        <!--begin:Menu sub-->
                                                        <div
                                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link {{ Route::currentRouteName() == 'lancamentoPadrao.index' ? 'active' : '' }}"
                                                                    href="{{ route('lancamentoPadrao.index') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Lançamento Padrão</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link {{ Route::currentRouteName() == 'cadastroBancos.index' ? 'active' : '' }}"
                                                                    href="{{ route('cadastroBancos.index') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Bancos</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a
                                                                    class="menu-link {{ Route::currentRouteName() == 'entidades.index' ? 'active' : '' }}"href="{{ route('entidades.index') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Entidade
                                                                        Financeira</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a
                                                                    class="menu-link {{ Route::currentRouteName() == 'costCenter.index' ? 'active' : '' }}"href="{{ route('costCenter.index') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Centro de Custo</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a
                                                                    class="menu-link {{ Route::currentRouteName() == 'formas-pagamento.index' ? 'active' : '' }}"href="{{ route('formas-pagamento.index') }}">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Formas de Pagamento</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Menu sub-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                </div>
                                                <!--end:Menu sub-->
                                            </div>
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/electronics/elc002.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3"
                                                                    d="M18.0624 15.3454L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3454C4.56242 13.6454 3.76242 11.4452 4.06242 8.94525C4.56242 5.34525 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24525 19.9624 9.94525C20.0624 12.0452 19.2624 13.9454 18.0624 15.3454ZM13.0624 10.0453C13.0624 9.44534 12.6624 9.04534 12.0624 9.04534C11.4624 9.04534 11.0624 9.44534 11.0624 10.0453V13.0453H13.0624V10.0453Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M12.6624 5.54531C12.2624 5.24531 11.7624 5.24531 11.4624 5.54531L8.06241 8.04531V12.0453C8.06241 12.6453 8.46241 13.0453 9.06241 13.0453H11.0624V10.0453C11.0624 9.44531 11.4624 9.04531 12.0624 9.04531C12.6624 9.04531 13.0624 9.44531 13.0624 10.0453V13.0453H15.0624C15.6624 13.0453 16.0624 12.6453 16.0624 12.0453V8.04531L12.6624 5.54531Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-title">Patrimônio</span>
                                                    <span class="menu-arrow"></span>
                                                </span>
                                                <!--end:Menu link-->
                                                <!--begin:Menu sub-->
                                                <div
                                                    class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link {{ Route::currentRouteName() == 'patrimonio.index' ? 'active' : '' }}"
                                                            href="{{ route('patrimonio.index') }}">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Módulo Património</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/contacts/add-contact.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Add Contact</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link"
                                                            {{ Request::is('patrimonios/imoveis') ? 'active' : '' }}
                                                            href="{{ route('patrimonio.imoveis') }}">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Imóveis</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link"
                                                            {{ Request::is('patrimonio/create') ? 'active' : '' }}
                                                            href="{{ route('patrimonio.create') }}">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Acessórios</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                </div>
                                                <!--end:Menu sub-->
                                            </div>
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen002.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.3"
                                                                    d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-title">Veículos</span>
                                                    <span class="menu-arrow"></span>
                                                </span>
                                                <!--end:Menu link-->
                                                <!--begin:Menu sub-->
                                                <div
                                                    class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/list.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">My Projects</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/project.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">View Project</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/targets.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Targets</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/budget.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Budget</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/users.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Users</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/files.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Files</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/activity.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Activity</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/projects/settings.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Settings</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                </div>
                                                <!--end:Menu sub-->
                                            </div>
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm001.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3"
                                                                    d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.3"
                                                                    d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.3"
                                                                    d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-title">eCommerce</span>
                                                    <span class="menu-arrow"></span>
                                                </span>
                                                <!--end:Menu link-->
                                                <!--begin:Menu sub-->
                                                <div
                                                    class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                    <!--begin:Menu item-->
                                                    <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                        data-kt-menu-placement="right-start"
                                                        class="menu-item menu-lg-down-accordion">
                                                        <!--begin:Menu link-->
                                                        <span class="menu-link">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Catalog</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        <!--end:Menu link-->
                                                        <!--begin:Menu sub-->
                                                        <div
                                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/catalog/products.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Products</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/catalog/categories.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Categories</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/catalog/add-product.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Add Product</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/catalog/edit-product.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Edit Product</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/catalog/add-category.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Add Category</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/catalog/edit-category.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Edit Category</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Menu sub-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div data-kt-menu-trigger="click"
                                                        class="menu-item menu-accordion menu-sub-indention">
                                                        <!--begin:Menu link-->
                                                        <span class="menu-link">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Sales</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        <!--end:Menu link-->
                                                        <!--begin:Menu sub-->
                                                        <div class="menu-sub menu-sub-accordion">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/sales/listing.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Orders Listing</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/sales/details.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Order Details</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/sales/add-order.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Add Order</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/sales/edit-order.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Edit Order</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Menu sub-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div data-kt-menu-trigger="click"
                                                        class="menu-item menu-accordion menu-sub-indention">
                                                        <!--begin:Menu link-->
                                                        <span class="menu-link">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Customers</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        <!--end:Menu link-->
                                                        <!--begin:Menu sub-->
                                                        <div class="menu-sub menu-sub-accordion">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/customers/listing.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Customers Listing</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/customers/details.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Customers Details</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Menu sub-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div data-kt-menu-trigger="click"
                                                        class="menu-item menu-accordion menu-sub-indention">
                                                        <!--begin:Menu link-->
                                                        <span class="menu-link">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Reports</span>
                                                            <span class="menu-arrow"></span>
                                                        </span>
                                                        <!--end:Menu link-->
                                                        <!--begin:Menu sub-->
                                                        <div class="menu-sub menu-sub-accordion">
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/reports/view.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Products Viewed</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/reports/sales.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Sales</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/reports/returns.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Returns</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/reports/customer-orders.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Customer Orders</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                            <!--begin:Menu item-->
                                                            <div class="menu-item">
                                                                <!--begin:Menu link-->
                                                                <a class="menu-link"
                                                                    href="#apps/ecommerce/reports/shipping.html">
                                                                    <span class="menu-bullet">
                                                                        <span class="bullet bullet-dot"></span>
                                                                    </span>
                                                                    <span class="menu-title">Shipping</span>
                                                                </a>
                                                                <!--end:Menu link-->
                                                            </div>
                                                            <!--end:Menu item-->
                                                        </div>
                                                        <!--end:Menu sub-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                    <!--begin:Menu item-->
                                                    <div class="menu-item">
                                                        <!--begin:Menu link-->
                                                        <a class="menu-link" href="#apps/ecommerce/settings.html">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Settings</span>
                                                        </a>
                                                        <!--end:Menu link-->
                                                    </div>
                                                    <!--end:Menu item-->
                                                </div>
                                                <!--end:Menu sub-->
                                            </div>
                                            <!--end:Menu item-->

                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                @role('global|admin')
                                                    <span class="menu-link">
                                                        <span class="menu-icon">
                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen051.svg-->
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path opacity="0.3"
                                                                        d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                                                        fill="currentColor" />
                                                                    <path
                                                                        d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z"
                                                                        fill="currentColor" />
                                                                </svg>
                                                            </span>
                                                            <!--end::Svg Icon-->
                                                        </span>
                                                        <span class="menu-title">Cadastros</span>
                                                        <span class="menu-arrow"></span>
                                                    </span>
                                                    <!--end:Menu link-->
                                                    <!--begin:Menu sub-->
                                                    <div
                                                        class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                        <!--begin:Menu item-->
                                                        <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                            data-kt-menu-placement="right-start"
                                                            class="menu-item menu-lg-down-accordion">
                                                            <!--begin:Menu link-->
                                                            <span class="menu-link">
                                                                <span class="menu-bullet">
                                                                    <span class="bullet bullet-dot"></span>
                                                                </span>
                                                                <span class="menu-title">Usuário</span>
                                                                <span class="menu-arrow"></span>
                                                            </span>
                                                            <!--end:Menu link-->
                                                            <!--begin:Menu sub-->
                                                            <div
                                                                class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                                <!--begin:Menu item-->
                                                                <div class="menu-item">
                                                                    <!--begin:Menu link-->
                                                                    <a class="menu-link {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}"
                                                                        href="{{ route('users.index') }}">
                                                                        <span class="menu-bullet">
                                                                            <span class="bullet bullet-dot"></span>
                                                                        </span>
                                                                        <span class="menu-title">Lista de Usuário</span>
                                                                    </a>
                                                                    <!--end:Menu link-->
                                                                </div>
                                                                <!--end:Menu item-->
                                                                <!--begin:Menu item-->
                                                                <div class="menu-item">
                                                                    <!--begin:Menu link-->
                                                                    <a class="menu-link"
                                                                        href="{{ route('profile.edit') }}">
                                                                        <span class="menu-bullet">
                                                                            <span class="bullet bullet-dot"></span>
                                                                        </span>
                                                                        <span class="menu-title">Ver usuário</span>
                                                                    </a>
                                                                    <!--end:Menu link-->
                                                                </div>
                                                                <!--end:Menu item-->
                                                            </div>
                                                            <!--end:Menu sub-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                        <!--begin:Menu item-->
                                                        <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                            data-kt-menu-placement="right-start"
                                                            class="menu-item menu-lg-down-accordion">
                                                            <!--begin:Menu link-->
                                                            <span class="menu-link">
                                                                <span class="menu-bullet">
                                                                    <span class="bullet bullet-dot"></span>
                                                                </span>
                                                                <span class="menu-title">Roles</span>
                                                                <span class="menu-arrow"></span>
                                                            </span>
                                                            <!--end:Menu link-->
                                                            <!--begin:Menu sub-->
                                                            <div
                                                                class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                                <!--begin:Menu item-->
                                                                <div class="menu-item">
                                                                    <!--begin:Menu link-->
                                                                    <a class="menu-link"
                                                                        href="#apps/user-management/roles/list.html">
                                                                        <span class="menu-bullet">
                                                                            <span class="bullet bullet-dot"></span>
                                                                        </span>
                                                                        <span class="menu-title">Roles List</span>
                                                                    </a>
                                                                    <!--end:Menu link-->
                                                                </div>
                                                                <!--end:Menu item-->
                                                                <!--begin:Menu item-->
                                                                <div class="menu-item">
                                                                    <!--begin:Menu link-->
                                                                    <a class="menu-link"
                                                                        href="#apps/user-management/roles/view.html">
                                                                        <span class="menu-bullet">
                                                                            <span class="bullet bullet-dot"></span>
                                                                        </span>
                                                                        <span class="menu-title">View Roles</span>
                                                                    </a>
                                                                    <!--end:Menu link-->
                                                                </div>
                                                                <!--end:Menu item-->
                                                            </div>
                                                            <!--end:Menu sub-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                        <!--begin:Menu item-->
                                                        <div class="menu-item">
                                                            <!--begin:Menu link-->
                                                            <a class="menu-link {{ Route::currentRouteName() == 'company.index' ? 'active' : '' }}"
                                                                href="{{ route('company.index') }}">
                                                                <span class="menu-bullet">
                                                                    <span class="bullet bullet-dot"></span>
                                                                </span>
                                                                <span class="menu-title">Organismo</span>
                                                            </a>
                                                            <!--end:Menu link-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                    </div>
                                                    <!--end:Menu sub-->
                                                @endrole
                                            </div>
                                            <!--end:Menu item-->

                                        </div>
                                        <!--end:Menu sub-->
                                    </div>
                                    <!--end:Menu item-->
                                    <!--begin:Menu item-->
                                    <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                                        data-kt-menu-placement="bottom-start"
                                        class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
                                        <!--begin:Menu link-->
                                        <span class="menu-link btn btn-sm">
                                            <span class="menu-title">Configuração</span>
                                        </span>
                                        <!--end:Menu link-->
                                        <!--begin:Menu sub-->
                                        <div
                                            class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!--begin::Svg Icon | path: icons/duotune/graphs/gra006.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3"
                                                                    d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-title">Organismo</span>
                                                    <span class="menu-arrow"></span>
                                                </span>
                                                <!--end:Menu link-->
                                                <!--begin:Menu sub-->
                                                <div
                                                    class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                    <div class="menu-item">
                                                        {{-- O link agora não passa mais parâmetros e a verificação da classe 'active' foi simplificada --}}
                                                        <a class="menu-link {{ Route::currentRouteName() == 'company.edit' ? 'active' : '' }}"
                                                            href="{{ route('company.edit') }}">
                                                            <span class="menu-bullet">
                                                                <span class="bullet bullet-dot"></span>
                                                            </span>
                                                            <span class="menu-title">Personalização</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!--end:Menu sub-->
                                            </div>
                                            <!--end:Menu item-->
                                            <!--begin:Menu item-->
                                            <div data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                                data-kt-menu-placement="right-start"
                                                class="menu-item menu-lg-down-accordion">
                                                <!--begin:Menu link-->
                                                <span class="menu-link">
                                                    <span class="menu-icon">
                                                        <!-- Ícone geral de tela de login -->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3"
                                                                    d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </span>
                                                    <span class="menu-title">Tela de Login</span>
                                                    <span class="menu-arrow"></span>
                                                </span>

                                                <!-- Verifica a permissão -->
                                                @if (auth()->user()->hasRole('global'))
                                                    <!--begin:Menu sub-->
                                                    <div
                                                        class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                        <!--begin:Menu item-->
                                                        <div class="menu-item">
                                                            <!--begin:Menu link-->
                                                            <a class="menu-link {{ Route::currentRouteName() == 'telaLogin.index' ? 'active' : '' }}"
                                                                href="{{ route('telaLogin.index') }}">
                                                                <span class="menu-bullet">
                                                                    <span class="bullet bullet-dot"></span>
                                                                </span>
                                                                <span class="menu-title">Personalizar</span>

                                                                <!-- Exibe ícone baseado na permissão -->
                                                                <i class="fa-solid fa-lock-open"
                                                                    style="color: #63E6BE;"></i>
                                                            </a>
                                                            <!--end:Menu link-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                    </div>
                                                    <!--end:Menu sub-->
                                                @else
                                                    <!--begin:Menu sub-->
                                                    <div
                                                        class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
                                                        <!--begin:Menu item-->
                                                        <div class="menu-item">
                                                            <!--begin:Menu link-->
                                                            <a class="menu-link ">
                                                                <span class="menu-bullet">
                                                                    <span class="bullet bullet-dot"></span>
                                                                </span>
                                                                <span class="menu-title">Personalizar</span>

                                                                <!-- Exibe ícone baseado na permissão -->
                                                                <i class="fa-solid fa-lock"></i>
                                                            </a>
                                                            <!--end:Menu link-->
                                                        </div>
                                                        <!--end:Menu item-->
                                                    </div>
                                                    <!--end:Menu sub-->
                                                @endif
                                            </div>
                                            <!--end:Menu item-->
                                        </div>
                                        <!--end:Menu sub-->


                                    </div>
                                    <!--end:Menu item-->
                                </div>
                                <!--end::Menu-->
                            </div>
                            <!--end::Menu wrapper-->
                            <!--begin::Navbar-->
                            <div class="app-navbar flex-shrink-0">
                                <!--begin::Theme mode-->
                                @include('app.layouts.dack')
                                <!--end::Theme mode-->
                                <!--begin::User menu-->
                                @include('app.layouts.userMenu')
                                <!--end::User menu-->
                                <!--begin::Header menu toggle-->
                                <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
                                    <div class="btn btn-icon btn-active-color-primary w-35px h-35px"
                                        id="kt_app_header_menu_toggle">
                                        <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                    d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                </div>
                                <!--end::Header menu toggle-->
                            </div>
                            <!--end::Navbar-->
                        </div>
                        <!--end::Header wrapper-->

                    </div>
                    <!--end::Header container-->
                </div>
                <!--end::Header-->
                <!--begin::Wrapper-->
                <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                    <!--begin::Main-->
                    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                        <!--begin::Content wrapper-->
                        <div class="d-flex flex-column flex-column-fluid">
                            <!--begin::Toolbar-->

                            <!--end::Toolbar-->
                            <!--begin::Content-->
                            <div id="kt_app_content" class="app-content flex-column-fluid">
                                <!--begin::Content container-->
                                <!-- Page Heading -->
                                @isset($header)
                                    <header class="bg-white dark:bg-gray-800 shadow">
                                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                            {{ $header }}

                                        </div>
                                    </header>
                                @endisset

                                <!-- Page Content -->
                                <main>
                                    {{ $slot }}
                                </main>

                                <!--end::Content container-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Content wrapper-->
                        <!--begin::Footer-->
                        @include('components.footer')

                        <!--end::Footer-->
                    </div>
                    <!--end:::Main-->
                </div>
