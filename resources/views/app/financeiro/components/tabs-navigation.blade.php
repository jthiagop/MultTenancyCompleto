<!--begin::Card header-->
<div class="card-header align-items-center py-5 gap-2 gap-md-5">
    <!--begin::Card title-->
    <div class="card-title">
        <!--begin::Tabs-->
        <ul class="nav nav-pills nav-pills-custom mb-3">
            <!--begin::Receitas Tab-->
            <li class="nav-item mb-3 me-3 me-lg-6">
                <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-success flex-column overflow-hidden w-80px h-85px pt-5 pb-2 active"
                    id="navReceitas" data-bs-toggle="pill" href="#containerReceitas"
                    aria-label="Exibir Receitas">
                    <div class="nav-icon mb-3">
                        <i class="bi bi-arrow-up-circle fs-1" aria-hidden="true"></i>
                    </div>
                    <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">Receitas</span>
                    <span
                        class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-success"></span>
                </a>
            </li>
            <!--end::Receitas Tab-->
            <!--begin::Despesas Tab-->
            <li class="nav-item mb-3 me-3 me-lg-6">
                <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                    id="navDespesas" data-bs-toggle="pill" href="#containerDespesas"
                    aria-label="Exibir Despesas">
                    <div class="nav-icon mb-3">
                        <i class="bi bi-arrow-down-circle fs-1" aria-hidden="true"></i>
                    </div>
                    <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">Despesas</span>
                    <span
                        class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                </a>
            </li>
            <!--end::Despesas Tab-->
        </ul>
        <!--end::Tabs-->
    </div>
    <!--end::Card title-->
    <!--begin::Card toolbar-->
    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
        <!--begin::Search-->
        <div class="d-flex align-items-center position-relative my-1">
            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                        rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                    <path
                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                        fill="currentColor" />
                </svg>
            </span>
            <input type="text" data-kt-ecommerce-order-filter="search"
                class="form-control form-control-solid w-250px ps-14"
                placeholder="Pesquisar por descrição..." aria-label="Pesquisar por descrição" />
        </div>
        <!--end::Search-->
        <!--begin::Daterangepicker-->
        <input class="form-control form-control-solid w-100 mw-250px" placeholder="Selecionar período"
            id="kt_ecommerce_report_customer_orders_daterangepicker"
            aria-label="Selecionar período de datas" />
        <!--end::Daterangepicker-->
        <!--begin::New Button-->
        <button type="button" class="btn btn-light-success" data-kt-menu-trigger="click"
            data-kt-menu-placement="bottom-end" aria-label="Adicionar novo lançamento">
            <span class="svg-icon svg-icon-2">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
            Novo
        </button>
        <!--begin::Menu-->
        <div id="kt_ecommerce_report_customer_orders_export_menu"
            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
            data-kt-menu="true">
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                    data-bs-target="#Dm_modal_financeiro" data-tipo="receita"
                    aria-label="Adicionar nova receita">
                    <span class="me-2">💰</span> Receita
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                    data-bs-target="#Dm_modal_financeiro" data-tipo="despesa"
                    aria-label="Adicionar nova despesa">
                    <span class="me-2">💸</span> Despesa
                </a>
            </div>
        </div>
        <!--end::Menu-->
        <!--end::New Button-->
    </div>
    <!--end::Card toolbar-->
</div>
<!--end::Card header-->
