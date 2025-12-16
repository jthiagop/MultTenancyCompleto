@php
    // Parâmetros padrão
    $activeTab = $activeTab ?? 'projects';
    $showAccountDropdown = $showAccountDropdown ?? true;
    $showToolsDropdown = $showToolsDropdown ?? true;
    $showBulkEditButton = $showBulkEditButton ?? null; // null = auto-detect, true/false = força exibição/ocultação

    // Buscar favoritos do usuário atual
    $favorites = auth()->user()->authorizedFavorites ?? collect();
    $currentRouteName = Route::currentRouteName();

    // Determinar módulo ativo baseado na rota atual
    $activeModule = null;
    foreach ($favorites as $favorite) {
        if (str_starts_with($currentRouteName, $favorite->module_key . '.')) {
            $activeModule = $favorite->id;
            break;
        }
    }

    // Auto-detectar se deve mostrar o botão de edição em massa
    // Baseado nas rotas de lançamentos padrões e contabilidade
    if ($showBulkEditButton === null) {
        $showBulkEditButton = str_starts_with($currentRouteName, 'lancamentoPadrao.')
            || str_starts_with($currentRouteName, 'contabilidade.')
            || $currentRouteName === 'contabilidade.index';
    }
@endphp

<!--begin::Navbar Secundária-->
<div class="app-subnav-secondary app-header-secundary" id="kt_app_subnav_secondary">
    <!--begin::Container-->
    <div class="app-container container-fluid px-4">
        <!--begin::Navbar wrapper-->
        <div class="d-flex align-items-center justify-content-between h-50px flex-wrap gap-3">
            <!--begin::Mobile menu toggle-->
            <div class="d-flex d-lg-none align-items-center">
                <button class="btn btn-sm btn-icon btn-active-color-primary" id="kt_subnav_mobile_toggle">
                    <i class="fa-solid fa-bars fs-1"></i>
                </button>
            </div>
            <!--end::Mobile menu toggle-->

            <!--begin::Left side-->
            <div class="d-flex align-items-center gap-3 flex-wrap d-none d-lg-flex" id="kt_subnav_left_side">


                <!--begin::Tabs-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-7 fw-semibold flex-nowrap overflow-auto"
                    id="kt_favorite_tabs">
                    @forelse($favorites as $favorite)
                        <li class="nav-item position-relative remove-favorite-item mb-1"
                            data-favorite-id="{{ $favorite->id }}">
                            <a class="nav-link text-active-primary me-4 d-flex align-items-center {{ $activeModule === $favorite->id ? 'active' : '' }}"
                                href="{{ route($favorite->route_name) }}">
                                @if ($favorite->icon && str_contains($favorite->icon, '/'))
                                    <img src="{{ $favorite->icon }}" alt="{{ $favorite->display_name }}" class="me-2"
                                        style="width: 20px; height: 20px;">
                                @elseif($favorite->icon)
                                    <i class="fa-solid {{ $favorite->icon }} me-2"></i>
                                @endif
                                <span class="fs-7 fw-semibold text-white">{{ $favorite->display_name }}</span>
                                <a type="button" class="btn btn-sm btn-icon btn-active-color-primary remove-favorite"
                                    data-favorite-id="{{ $favorite->id }}" title="Remover dos favoritos"
                                    onclick="event.preventDefault(); event.stopPropagation();">
                                    <i class="bi bi-star-fill fs-7"></i>
                                </a>
                            </a>
                        </li>
                    @empty
                    @endforelse
                </ul>
                <!--end::Tabs-->

                <!--begin::Add Favorite Button-->
                <div class="d-flex align-items-center" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    <button class="btn btn-sm btn-icon btn-active-color-primary" id="btn_add_favorite">
                        <i class="bi bi-plus fs-2"></i>
                    </button>

                    <!--begin::Menu-->
                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg fw-semibold py-4 fs-6 w-350px"
                        data-kt-menu="true" id="kt_add_favorite_menu">
                        <div class="menu-item px-3">
                            <div class="menu-content">
                                <div class="fs-6 fw-bold text-dark px-3 py-2">Adicionar aos Favoritos</div>
                                <div class="fs-7 text-muted px-3 pb-2">Selecione um módulo que você tem acesso</div>
                            </div>
                        </div>
                        <div class="separator my-"></div>

                        <div id="available-modules-list" class="px-3">
                            <div class="text-center py-5">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Carregando módulos...
                            </div>
                        </div>
                    </div>
                    <!--end::Menu-->
                </div>
                <!--end::Add Favorite Button-->
            </div>
            <!--end::Left side-->

            <!--begin::Right side-->
            <div class="d-flex align-items-center gap-3 flex-wrap ms-auto d-none d-lg-flex" id="kt_subnav_right_side">
                <!--begin::Extensions Button-->
                <a href="#"
                    class="btn btn-sm btn-flex btn-color-gray-700 btn-active-color-primary fw-semibold px-4">
                    <i class="fa-solid fa-plus fs-5 me-2"></i>
                    Extensions
                </a>
                <!--end::Extensions Button-->

                <!--begin::Tools Dropdown-->
                @if ($showToolsDropdown)
                    <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                        data-kt-menu-placement="bottom-end">
                        <a href="#"
                            class="btn btn-sm btn-flex btn-color-gray-700 btn-active-color-primary fw-semibold px-4">
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

@php
    // Verificar se existe subnav específica para o módulo atual
    $currentModule = \App\Helpers\ModuleHelper::getCurrentModule();
    $hasModuleSubnav = $currentModule && \App\Helpers\ModuleHelper::hasSubnav($currentModule);

    // Verificar se existe toolbar para o módulo atual
    $hasModuleToolbar = false;
    if ($currentModule) {
        $toolbarPath = "app.layouts.subnav.modules.toolbars.{$currentModule}";
        $hasModuleToolbar = view()->exists($toolbarPath);
    }
@endphp

@if ($hasModuleToolbar || $hasModuleSubnav)
    <!--begin::Navbar Terciária com Toolbar-->
    <div class="app-subnav-tertiary border-bottom">
        <div class="app-container container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between gap-4 py-3">
                <!--begin::Left side - Toolbar-->
                @if ($hasModuleToolbar)
                    <div class="flex-grow-1 d-flex align-items-center flex-stack">
                        @include("app.layouts.subnav.modules.toolbars.{$currentModule}")
                    </div>
                @endif
                <!--end::Left side-->

                <!--begin::Right side - Subnav Actions-->
                <div class="d-flex align-items-center gap-3 flex-nowrap flex-shrink-0">
                    @if ($hasModuleSubnav)
                        @include("app.layouts.subnav.modules.{$currentModule}")
                    @else


                        <!--begin::Search Dropdown-->
                        @if($showBulkEditButton)
                        <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                            data-kt-menu-placement="bottom-start">
                            <!--begin::Toolbar-->
                            <div class="me-0">
                                <button class="btn btn-icon btn-sm btn-active-color-primary justify-content-end"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="fonticon-settings fs-2"></i>
                                </button>
                                <!--begin::Menu 3-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                    data-kt-menu="true">
                                    <!--begin::Heading-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Ações em Massa
                                        </div>
                                    </div>
                                    <!--end::Heading-->

                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="#" class="menu-link flex-stack px-3" data-bs-toggle="modal" data-bs-target="#kt_modal_lancamento_padrao_bulk">Editar em Massa
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                title="Edite os campos selecionados em massa"></i></a>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu 3-->
                            </div>
                            <!--end::Toolbar-->
                        </div>
                        <!--end::Search Dropdown-->
                        @endif
                    @endif
                </div>
                <!--end::Right side-->
            </div>
        </div>
    </div>

@endif
