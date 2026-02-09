<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<x-tenant-app-layout pageTitle="Parceiros - Fornecedores e Clientes" :breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Parceiros']]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar">
            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3"></div>
            </div>
        </div>
        <!--end::Toolbar-->

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">


                <!--begin::Tab Content-->
                @includeIf("app.financeiro.parceiros.tabs.{$activeTab}")
                <!--end::Tab Content-->

            </div>
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

    <!--begin::Modal - Novo/Editar Parceiro-->
    @include('app.financeiro.parceiros.components.modal_parceiro')
    <!--end::Modal-->

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>

        <!--begin::Parceiros JS-->
        @include('app.financeiro.parceiros.scripts.parceiros-datatable')
        <!--end::Parceiros JS-->
    @endpush

</x-tenant-app-layout>
