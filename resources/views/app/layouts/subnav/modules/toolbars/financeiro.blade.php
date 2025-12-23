
<!--begin::Actions-->
<div class="d-flex align-items-center gap-2 gap-lg-3">
    <!--begin::Prestação de Contas Button-->
    <button class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill"
            id="kt_subnav_prestacao_contas_btn" data-bs-toggle="modal"
            data-bs-target="#modal_prestacao_contas">
        <i class="fa-solid fa-file-invoice-dollar fs-5 me-2"></i>
        Prestação de Contas
    </button>
    <!--end::Prestação de Contas Button-->

    <!--begin::Notas Button-->
    <a href="{{ route('nfe_entrada.index') }}" class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill" active="{{ Route::is('nfe_entrada.index') }}">
        <img src="{{ global_asset('assets/media/logos/nfe.svg') }}" class="h-20px me-2" alt="NFe" />
        Notas Fiscais
    </a>
    <!--end::Notas Button-->

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
            <div class="menu-item">
                <div class="menu-content">
                    <div class="fs-7 text-muted fw-semibold">Relatórios Disponíveis</div>
                </div>
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item">
                <a href="" id="kt_subnav_boletim_financeiro_btn" data-bs-toggle="modal"
            data-bs-target="#modal_boletim_financeiro" class="menu-link px-5">
                    <span class="menu-icon">
                        <i class="fa-solid fa-chart-line fs-5 me-2"></i>
                    </span>
                    <span class="menu-title">Boletim Financeiro</span>
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="#" id="kt_subnav_prestacao_contas_btn" data-bs-toggle="modal"
            data-bs-target="#modal_prestacao_contas" class="menu-link px-5">
                    <span class="menu-icon">
                        <i class="fa-solid fa-chart-pie fs-5 me-2"></i>
                    </span>
                    <span class="menu-title">Prestação de Contas</span>
                </a>
            </div>
            <div class="menu-item px-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#modal_conciliacao_bancaria" class="menu-link px-5">
                    <span class="menu-icon">
                        <i class="bi bi-arrow-left-right fs-5 me-2"></i>
                    </span>
                    <span class="menu-title">Conciliação Bancária</span>
                </a>
            </div>
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Relatórios Financeiros Dropdown-->
</div>
<!--end::Actions-->



<!--begin::New Menu-->
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
<!--end::Actions-->

