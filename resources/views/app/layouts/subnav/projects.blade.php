@php
    // Parâmetros padrão
    $activeTab = $activeTab ?? 'projects';
    $showAccountDropdown = $showAccountDropdown ?? true;
    $showToolsDropdown = $showToolsDropdown ?? true;
@endphp

<!--begin::Navbar Secundária-->
<div class="app-subnav-secondary bg-body-dark dark:bg-gray-900 border-bottom" id="kt_app_subnav_secondary">
    <!--begin::Container-->
    <div class="app-container container-fluid px-4">
        <!--begin::Navbar wrapper-->
        <div class="d-flex align-items-center justify-content-between h-60px flex-wrap gap-3">
            <!--begin::Mobile menu toggle-->
            <div class="d-flex d-lg-none align-items-center">
                <button class="btn btn-sm btn-icon btn-active-color-primary" id="kt_subnav_mobile_toggle">
                    <i class="fa-solid fa-bars fs-1"></i>
                </button>
            </div>
            <!--end::Mobile menu toggle-->

            <!--begin::Left side-->
            <div class="d-flex align-items-center gap-3 flex-wrap d-none d-lg-flex" id="kt_subnav_left_side">
                <!--begin::Account Dropdown-->
                @if($showAccountDropdown)
                <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start">
                    <a href="#" class="btn btn-sm btn-flex btn-color-gray-700 btn-active-color-primary fw-semibold px-4">
                        <span class="menu-title">Modulos</span>
                        <i class="fa-solid fa-chevron-down fs-9 px-2"></i>
                    </a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-200px"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <a href="{{ route('caixa.index') }}" class="menu-link px-5">
                                <i class="fa-solid fa-money-bill fs-5 me-5"></i>
                                <span class="menu-title">Financeiro</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="{{ route('patrimonio.index') }}" class="menu-link px-5">
                                <i class="fa-solid fa-building fs-5 me-5"></i>
                                <span class="menu-title">Patrimônio</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="{{ route('contabilidade.index') }}" class="menu-link px-5">
                                <i class="fa-solid fa-user fs-5 me-5"></i>
                                <span class="menu-title">Contabilidade</span>
                            </a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-3">
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                               class="menu-link px-5">
                                <span class="menu-title">Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                @endif
                <!--end::Account Dropdown-->

                <!--begin::Tabs-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-semibold flex-nowrap overflow-auto">
                    <!--begin::Nav item - Projects-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-4 {{ $activeTab === 'projects' ? 'active' : '' }}"
                           href="#">
                            Projects
                        </a>
                    </li>
                    <!--end::Nav item-->
                    <!--begin::Nav item - Customers-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-4 {{ $activeTab === 'customers' ? 'active' : '' }}"
                           href="#">
                            Customers
                        </a>
                    </li>
                    <!--end::Nav item-->
                </ul>
                <!--end::Tabs-->

                <!--begin::Add New Button-->
                <a href="#" class="btn btn-sm btn-primary fw-semibold px-4">
                    <i class="fa-solid fa-plus fs-2"></i>
                    Add New
                </a>
                <!--end::Add New Button-->
            </div>
            <!--end::Left side-->

            <!--begin::Right side-->
            <div class="d-flex align-items-center gap-3 flex-wrap ms-auto d-none d-lg-flex" id="kt_subnav_right_side">
                <!--begin::Extensions Button-->
                <a href="#" class="btn btn-sm btn-flex btn-color-gray-700 btn-active-color-primary fw-semibold px-4">
                    <i class="fa-solid fa-plus fs-5 me-2"></i>
                    Extensions
                </a>
                <!--end::Extensions Button-->

                <!--begin::Tools Dropdown-->
                @if($showToolsDropdown)
                <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-end">
                    <a href="#" class="btn btn-sm btn-flex btn-color-gray-700 btn-active-color-primary fw-semibold px-4">
                        <span class="menu-title">Tools</span>
                        <i class="fa-solid fa-chevron-down fs-5 ms-2"></i>
                    </a>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-200px"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">Export</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">Import</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">Settings</span>
                            </a>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">Help</span>
                            </a>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                @endif
                <!--end::Tools Dropdown-->
            </div>
            <!--end::Right side-->
        </div>
        <!--end::Navbar wrapper-->
    </div>
    <!--end::Container-->
</div>
<!--end::Navbar Secundária-->

<!--begin::Navbar Terciária-->
<div class="app-subnav-tertiary bg-white dark:bg-gray-800 border-bottom" id="kt_app_subnav_tertiary">
    <!--begin::Container-->
    <div class="app-container container-fluid px-4">
        <!--begin::Navbar wrapper-->
        <div class="d-flex align-items-center h-50px overflow-x-auto overflow-y-hidden">
            <!--begin::Left side - Filters/Actions-->
            <div class="d-flex align-items-center gap-3 flex-nowrap" style="min-width: max-content;">
                <!--begin::Views Button-->
                <button class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill active"
                        id="kt_subnav_views_btn">
                    <i class="fa-solid fa-table fs-5 me-2"></i>
                    Views
                </button>
                <!--end::Views Button-->

                <!--begin::My Widgets Dropdown-->
                <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start">
                    <button class="btn btn-sm btn-light fw-semibold px-4 rounded-pill">
                        <i class="fa-solid fa-square-check fs-5 me-2"></i>
                        My Widgets
                        <i class="fa-solid fa-chevron-down fs-5 ms-2"></i>
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-250px"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-7 text-muted fw-semibold px-3 py-2">Widgets Disponíveis</div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">Widget 1</span>
                            </a>
                        </div>
                        <div class="menu-item px-3">
                            <a href="#" class="menu-link px-5">
                                <span class="menu-title">Widget 2</span>
                            </a>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::My Widgets Dropdown-->

                <!--begin::Hide Fields Dropdown-->
                <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start">
                    <button class="btn btn-sm btn-light fw-semibold px-4 rounded-pill">
                        <i class="fa-solid fa-eye-slash fs-5 me-2"></i>
                        Hide Fields
                        <i class="fa-solid fa-chevron-down fs-5 ms-2"></i>
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-250px"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-7 text-muted fw-semibold px-3 py-2">Campos Visíveis</div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="" id="field1" checked />
                                    <label class="form-check-label" for="field1">Campo 1</label>
                                </div>
                            </div>
                        </div>
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="" id="field2" checked />
                                    <label class="form-check-label" for="field2">Campo 2</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Hide Fields Dropdown-->

                <!--begin::Filter Dropdown-->
                <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start">
                    <button class="btn btn-sm btn-light fw-semibold px-4 rounded-pill">
                        <i class="fa-solid fa-filter fs-5 me-2"></i>
                        Filter
                        <i class="fa-solid fa-chevron-down fs-5 ms-2"></i>
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-300px"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-7 text-muted fw-semibold px-3 py-2">Filtros</div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-7 text-muted px-3 py-2">Nenhum filtro aplicado</div>
                                <div class="fs-7 text-primary px-3 py-2">
                                    <a href="#" class="text-primary">Aplicar filtros</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Filter Dropdown-->

                <!--begin::Sort Dropdown-->
                <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                     data-kt-menu-placement="bottom-start">
                    <button class="btn btn-sm btn-light fw-semibold px-4 rounded-pill">
                        <i class="fa-solid fa-arrow-down-up fs-5 me-2"></i>
                        Sort
                        <i class="fa-solid fa-chevron-down fs-5 ms-2"></i>
                    </button>
                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-250px"
                         data-kt-menu="true">
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-7 text-muted fw-semibold px-3 py-2">Ordenar por</div>
                            </div>
                        </div>
                        <div class="separator my-2"></div>
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-7 text-muted px-3 py-2">Ordenar por...</div>
                                <div class="fs-7 text-primary px-3 py-2">
                                    <a href="#" class="text-primary">Definir ordenação</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Sort Dropdown-->
            </div>
            <!--end::Left side-->

            <!--begin::Right side - Search (Optional)-->
            <div class="d-flex align-items-center ms-auto">
                <div class="d-flex align-items-center position-relative">
                    <i class="fa-solid fa-magnifying-glass fs-2 text-gray-500 position-absolute start-0 ms-3"></i>
                    <input type="text" class="form-control form-control-sm form-control-solid ps-10"
                           placeholder="Buscar..." style="min-width: 200px;" />
                </div>
            </div>
            <!--end::Right side-->
        </div>
        <!--end::Navbar wrapper-->
    </div>
    <!--end::Container-->
</div>
<!--end::Navbar Terciária-->

<!--begin::Mobile Menu Drawer-->
<div class="app-subnav-mobile-drawer d-lg-none" data-kt-drawer="true" data-kt-drawer-name="app-subnav-mobile"
     data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
     data-kt-drawer-width="275px" data-kt-drawer-direction="end"
     data-kt-drawer-toggle="#kt_subnav_mobile_toggle">
    <!--begin::Menu-->
    <div class="menu menu-column menu-rounded menu-sub-indention px-3 py-4" data-kt-menu="true">
        <!--begin::Menu item - Account-->
        @if($showAccountDropdown)
        <div data-kt-menu-trigger="click" class="menu-item here show">
            <span class="menu-link">
                <span class="menu-title">Account</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Settings</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('profile.edit') }}">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Profile</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Preferences</span>
                    </a>
                </div>
                <div class="separator my-2"></div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Logout</span>
                    </a>
                    <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
        @endif
        <!--end::Menu item-->

        <!--begin::Menu item - Projects-->
        <div class="menu-item">
            <a class="menu-link {{ $activeTab === 'projects' ? 'active' : '' }}" href="#">
                <span class="menu-title">Projects</span>
            </a>
        </div>
        <!--end::Menu item-->

        <!--begin::Menu item - Customers-->
        <div class="menu-item">
            <a class="menu-link {{ $activeTab === 'customers' ? 'active' : '' }}" href="#">
                <span class="menu-title">Customers</span>
            </a>
        </div>
        <!--end::Menu item-->

        <!--begin::Menu item - Add New-->
        <div class="menu-item">
            <a class="menu-link" href="#">
                <span class="menu-title">+ Add New</span>
            </a>
        </div>
        <!--end::Menu item-->

        <!--begin::Menu item - Extensions-->
        <div class="menu-item">
            <a class="menu-link" href="#">
                <span class="menu-title">+ Extensions</span>
            </a>
        </div>
        <!--end::Menu item-->

        <!--begin::Menu item - Tools-->
        @if($showToolsDropdown)
        <div data-kt-menu-trigger="click" class="menu-item">
            <span class="menu-link">
                <span class="menu-title">Tools</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Export</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Import</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Settings</span>
                    </a>
                </div>
                <div class="separator my-2"></div>
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                        <span class="menu-title">Help</span>
                    </a>
                </div>
            </div>
        </div>
        @endif
        <!--end::Menu item-->
    </div>
    <!--end::Menu-->
</div>
<!--end::Mobile Menu Drawer-->

