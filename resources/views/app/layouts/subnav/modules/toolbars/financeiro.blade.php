
<!--begin::Actions-->
<div class="d-flex align-items-center gap-2 gap-lg-3">
    <!--begin::Prestação de Contas Button-->
    <a href="{{ route('domusia.index') }}" class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill d-flex align-items-center">
        <i class="fas fa-robot fs-5"></i>
        <span class="ms-2">Domus IA</span>
    </a>
    <!--end::Prestação de Contas Button-->

    <!--begin::Notas Button-->
    <a href="{{ route('nfe_entrada.index') }}" class="btn btn-sm btn-light-primary fw-semibold px-4 rounded-pill" active="{{ Route::is('nfe_entrada.index') }}">
        <img src="{{ global_asset('tenancy/assets/media/logos/nfe.svg') }}" class="h-20px" alt="NFe" />
        <span class="ms-2 ">Notas Fiscais</span>
    </a>
    <!--end::Notas Button-->


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

