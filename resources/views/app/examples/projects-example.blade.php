{{--
    EXEMPLO DE USO DAS NAVBARS SECUNDÁRIA E TERCIÁRIA

    Este é um exemplo de como integrar as navbars em uma página.
    Para usar em produção, copie o código relevante para sua página.
--}}

<x-tenant-app-layout>
    @php
        // Definir se as navbars devem ser exibidas
        $showSubnav = true;

        // Definir qual aba está ativa (baseado na rota ou lógica)
        $activeTab = 'projects'; // ou 'customers' ou 'account'

        // Controlar visibilidade dos dropdowns
        $showAccountDropdown = true;
        $showToolsDropdown = true;
    @endphp

    {{-- Incluir as navbars secundária e terciária --}}
    @if(isset($showSubnav) && $showSubnav === true)
        @include('app.layouts.subnav.projects', [
            'activeTab' => $activeTab,
            'showAccountDropdown' => $showAccountDropdown,
            'showToolsDropdown' => $showToolsDropdown
        ])
    @endif

    {{-- Conteúdo da página --}}
    <div class="app-container container-fluid px-4">
        <div class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div class="d-flex flex-column flex-column-fluid">
                <!--begin::Toolbar-->
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <div class="app-container container-fluid d-flex flex-stack">
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                Projects
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">Projects</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!--end::Toolbar-->

                <!--begin::Content-->
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
                    <div id="kt_app_content_container" class="app-container container-fluid">
                        <!--begin::Card-->
                        <div class="card">
                            <!--begin::Card header-->
                            <div class="card-header border-0 pt-6">
                                <div class="card-title">
                                    <h3 class="fw-bold m-0">Lista de Projects</h3>
                                </div>
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <p class="text-gray-600">
                                    Este é um exemplo de página usando as navbars secundária e terciária.
                                    As navbars aparecem logo abaixo do header principal e antes do conteúdo.
                                </p>
                                <p class="text-gray-600">
                                    O container principal usa <code>app-container container-fluid px-4</code>
                                    para manter o alinhamento correto com o header e as navbars.
                                </p>
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
            </div>
        </div>
    </div>
</x-tenant-app-layout>

