<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <!--begin::Toolbar container-->
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex align-items-center flex-wrap me-3">
            <!--begin::Logo-->
            <div class="symbol symbol-45px me-3">
                @if (!empty($company->avatar))
                    <img class="rounded-circle" alt="Logo"
                        src="{{ route('file', ['path' => $company->avatar]) }}" />
                @else
                    <img class="rounded-circle" alt="Logo" src="/assets/media/png/perfil.svg" />
                @endif
            </div>
            <!--end::Logo-->
            <!--begin::Text Group-->
            <div>
                <!--begin::Title-->
                <h1 class="page-heading text-dark fw-bold fs-3 my-0">{{ $company->name }}</h1>
                <!--end::Title-->
                <!--begin::Breadcrumb-->
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <span>CNPJ: </span>
                        @if (!empty($company->cnpj))
                            <a href="{{ route('company.edit', ['company' => $company->id]) }}"
                                class="text-muted text-hover-primary ms-1">{{ $company->cnpj }}</a>
                        @else
                            <a href="{{ route('company.edit', ['company' => $company->id]) }}"
                                class=" text-hover-primary ms-1">Editar informações</a>
                        @endif
                    </li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Text Group-->
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center">
            <!-- Data e Hora -->
            <div class="border border-transparent">

                <div id="datetime" class="me-4 text-muted fs-7 fw-semibold"></div>
            </div>
            <!-- Botão para Criar Campanha -->
            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                <span class="svg-icon svg-icon-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                            fill="currentColor" />
                    </svg>
                </span>
            </a>
        </div>

        <!--end::Actions-->
    </div>
    <!--end::Toolbar container-->
</div>
