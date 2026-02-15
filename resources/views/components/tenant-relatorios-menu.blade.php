@props([
    'size' => 'sm',
    'variant' => 'light',
    'placement' => 'bottom-start',
])

<!--begin::Menu Relatórios-->
<div>
    <!--begin::Toggle-->
    <button type="button" class="btn btn-{{ $size }} btn-{{ $variant }} rotate"
        data-kt-menu-trigger="click"
        data-kt-menu-placement="{{ $placement }}"
        data-kt-menu-offset="10px, 10px">
        <i class="fa-regular fa-file-lines fs-5 me-2"></i>
        Relatórios
        <span class="svg-icon fs-3 rotate-180 ms-3 me-0">
            <i class="fa-solid fa-chevron-down"></i>
        </span>
    </button>
    <!--end::Toggle-->

    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-auto min-w-200 mw-300px"
        data-kt-menu="true">
        <!--begin::Menu header-->
        <div class="menu-item px-3">
            <div class="menu-content fs-6 text-gray-900 fw-bold px-3 py-4">Gerar Relatórios</div>
        </div>
        <!--end::Menu header-->

        <!--begin::Menu separator-->
        <div class="separator mb-3 opacity-75"></div>
        <!--end::Menu separator-->

        <!--begin::Menu item - Boletim Financeiro-->
        <div class="menu-item px-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modal_boletim_financeiro" class="menu-link px-3">
                Boletim Financeiro
            </a>
        </div>
        <!--end::Menu item-->

        <!--begin::Menu item - Prestação de Contas-->
        <div class="menu-item px-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modal_prestacao_contas" class="menu-link px-3">
                Prestação de Contas
            </a>
        </div>
        <!--end::Menu item-->

        <!--begin::Menu item - Conciliação Bancária-->
        <div class="menu-item px-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modal_conciliacao_bancaria" class="menu-link px-3">
                Conciliação Bancária
            </a>
        </div>
        <!--end::Menu item-->

        {{-- Slot para itens extras --}}
        {{ $slot }}

        <!--begin::Menu separator-->
        <div class="separator mt-3 opacity-75"></div>
        <!--end::Menu separator-->

        <!--begin::Menu item - Agendar-->
        <div class="menu-item px-3">
            <div class="menu-content px-3 py-3">
                <a class="btn btn-light-primary btn-sm px-4" href="#">
                    Agendar Relatórios
                </a>
            </div>
        </div>
        <!--end::Menu item-->
    </div>
    <!--end::Menu-->
</div>
<!--end::Menu Relatórios-->
