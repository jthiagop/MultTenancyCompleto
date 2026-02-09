<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<x-tenant-app-layout pageTitle="Parceiros - Fornecedores e Clientes" :breadcrumbs="[['label' => 'Financeiro'], ['label' => 'Parceiros']]">

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

                <!--begin::Main Card com Tabs-->
                <div class="row no-gutters">
                    <div class="12 col-sm-12 col-md-12">
                        <div class="card mb-1 mb-xl-9">
                            <div class="card-header hover-scroll-x overflow-x-auto">
                                <div class="d-flex align-items-center justify-content-between flex-nowrap w-100" style="min-width: max-content;">
                                    <!--begin::Nav-->
                                    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold flex-nowrap">
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'todos' ? 'active' : '' }}"
                                                href="{{ route('parceiros.index', ['tab' => 'todos']) }}">
                                                <i class="bi bi-people fs-4 me-2"></i> Todos
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'fornecedores' ? 'active' : '' }}"
                                                href="{{ route('parceiros.index', ['tab' => 'fornecedores']) }}">
                                                <i class="bi bi-building fs-4 me-2"></i> Fornecedores
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'clientes' ? 'active' : '' }}"
                                                href="{{ route('parceiros.index', ['tab' => 'clientes']) }}">
                                                <i class="bi bi-person-check fs-4 me-2"></i> Clientes
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'inativos' ? 'active' : '' }}"
                                                href="{{ route('parceiros.index', ['tab' => 'inativos']) }}">
                                                <i class="bi bi-person-slash fs-4 me-2"></i> Inativos
                                            </a>
                                        </li>
                                    </ul>
                                    <!--end::Nav-->

                                    <!--begin::Actions-->
                                    <div class="card-toolbar flex-nowrap ms-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-sm btn-primary" id="btn-novo-parceiro" 
                                                    data-bs-toggle="modal" data-bs-target="#modal_parceiro">
                                                <i class="bi bi-plus-circle fs-4 me-1"></i>
                                                Novo Parceiro
                                            </button>
                                        </div>
                                    </div>
                                    <!--end::Actions-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Main Card-->

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
