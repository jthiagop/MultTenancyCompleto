<!--begin::Toolbar-->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
        <!--begin::Page title-->
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Lançamentos Financeiros
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1" aria-label="Navegação do site">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted" aria-current="page">Financeiro</li>
            </ul>
        </div>
        <!--end::Page title-->
        <!--begin::Actions-->
        <div class="d-flex align-items-center gap-3">
            <div id="kt_financeiro_new_menu"
                class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                data-kt-menu="true">
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                        data-bs-target="#Dm_modal_financeiro" data-origem="Caixa"
                        aria-label="Adicionar lançamento de caixa">
                        <span class="me-2">💰</span> Lançar Caixa
                    </a>
                </div>
                <div class="menu-item px-3">
                    <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                        data-bs-target="#Dm_modal_financeiro" data-origem="Banco"
                        aria-label="Adicionar lançamento bancário">
                        <span class="me-2">🏦</span> Lançar Banco
                    </a>
                </div>
            </div>
        </div>
        </div>

</div>
<!--end::Toolbar-->
