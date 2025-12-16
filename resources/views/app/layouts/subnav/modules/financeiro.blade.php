<!--begin::Prestação de Contas Button-->
<button class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill"
        id="kt_subnav_prestacao_contas_btn" data-bs-toggle="modal"
        data-bs-target="#modal_prestacao_contas">
    <i class="fa-solid fa-file-invoice-dollar fs-5 me-2"></i>
    Prestação de Contas
</button>
<!--end::Prestação de Contas Button-->

<!--begin::Boletim Financeiro Button-->
<button class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill"
        id="kt_subnav_boletim_financeiro_btn">
    <i class="fa-solid fa-chart-line fs-5 me-2"></i>
    Boletim Financeiro
</button>
<!--end::Boletim Financeiro Button-->

<!--begin::Relatórios Financeiros Dropdown-->
<div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
     data-kt-menu-placement="bottom-start">
    <button class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill">
        <i class="fa-solid fa-file-chart-column fs-5 me-2"></i>
        Relatórios Financeiros
        <i class="fa-solid fa-chevron-down fs-5 ms-2"></i>
    </button>
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-250px"
         data-kt-menu="true">
        <div class="menu-item px-3">
            <div class="menu-content">
                <div class="fs-7 text-muted fw-semibold px-3 py-2">Relatórios Disponíveis</div>
            </div>
        </div>
        <div class="separator my-2"></div>
        <div class="menu-item px-3">
            <a href="#" class="menu-link px-5">
                <span class="menu-icon">
                    <i class="fa-solid fa-calendar fs-6"></i>
                </span>
                <span class="menu-title">Demonstrativo Anual</span>
            </a>
        </div>
        <div class="menu-item px-3">
            <a href="#" class="menu-link px-5">
                <span class="menu-icon">
                    <i class="fa-solid fa-chart-pie fs-6"></i>
                </span>
                <span class="menu-title">Resumo Financeiro</span>
            </a>
        </div>
    </div>
    <!--end::Menu-->
</div>
<!--end::Relatórios Financeiros Dropdown-->
