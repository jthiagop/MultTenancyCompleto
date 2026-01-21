<!--begin::Drawer - Novo Fornecedor-->
<style>
    #kt_drawer_fornecedor {
        z-index: 1070 !important;
    }
    #kt_drawer_fornecedor .drawer-overlay {
        z-index: 1065 !important;
    }
    /* Ensure inputs in drawer are clickable */
    #kt_drawer_fornecedor input,
    #kt_drawer_fornecedor button,
    #kt_drawer_fornecedor select,
    #kt_drawer_fornecedor textarea {
        pointer-events: auto !important;
    }
</style>
<div
    id="kt_drawer_fornecedor"
    class="bg-white"
    data-kt-drawer="true"
    data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_fornecedor_button"
    data-kt-drawer-close="#kt_drawer_fornecedor_close"
    data-kt-drawer-width="500px"
    tabindex="0"
>
    <!--begin::Card-->
    <div class="card shadow-none rounded-0 w-100">
        <!--begin::Header-->
        <div class="card-header pe-5">
            <!--begin::Title-->
            <div class="card-title">
                <h3 class="fw-bold m-0">Novo Fornecedor</h3>
            </div>
            <!--end::Title-->

            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" id="kt_drawer_fornecedor_close">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </button>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body">
            <form id="kt_drawer_fornecedor_form">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">

                <!--begin::Input group-->
                <div class="mb-10">
                    <x-tenant-label for="fornecedor_nome" required>Nome</x-tenant-label>
                    <x-tenant-input
                        name="nome"
                        id="fornecedor_nome"
                        placeholder="Digite o nome do fornecedor"
                        required
                        class="" />
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="mb-10">
                    <x-tenant-label for="fornecedor_cnpj">CNPJ</x-tenant-label>
                    <x-tenant-input
                        name="cnpj"
                        id="fornecedor_cnpj"
                        placeholder="00.000.000/0000-00"
                        class="" />
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="mb-10">
                    <x-tenant-label for="fornecedor_telefone">Telefone</x-tenant-label>
                    <x-tenant-input
                        name="telefone"
                        id="fornecedor_telefone"
                        placeholder="(00) 00000-0000"
                        class="" />
                </div>
                <!--end::Input group-->

                <!--begin::Input group-->
                <div class="mb-10">
                    <x-tenant-label for="fornecedor_email">E-mail</x-tenant-label>
                    <x-tenant-input
                        name="email"
                        id="fornecedor_email"
                        type="email"
                        placeholder="exemplo@email.com"
                        class="" />
                </div>
                <!--end::Input group-->

                <!--begin::Actions-->
                <div class="d-flex flex-stack">
                    <button type="button" class="btn btn-light" id="kt_drawer_fornecedor_cancel">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="kt_drawer_fornecedor_submit">
                        <span class="indicator-label">Salvar</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
        </div>
        <!--end::Body-->
    </div>
    <!--end::Card-->
</div>
<!--end::Drawer - Novo Fornecedor-->


