    <x-tenant-app-layout pageTitle="Contabilidade" :breadcrumbs="[['label' => 'Categorias']]">

        @include('app.components.modals.contabilidade.planoConta')
        @include('app.components.modals.contabilidade.mapear')
        @include('app.components.modals.contabilidade.lancamentoPadrao')
        @include('app.components.modals.contabilidade.lancamentoPadraoBulk')
        <!--begin::Main-->
        <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
            <!--begin::Content wrapper-->
            <div class="d-flex flex-column flex-column-fluid">
                <!--begin::Content-->
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Content container-->
                    <div id="kt_app_content_container" class="app-container container-fluid py-3 py-lg-6">
                        <!--begin::Card-->
                        <div class="pb-0 bgi-position-y-center bgi-no-repeat mb-3">
                            <!--begin::Card body-->
                            <!--begin::Navs Container-->
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <!--begin::Navs-->
                                <ul
                                    class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-semibold mb-0">
                                    <!--begin::Nav item-->
                                    <li class="nav-item mt-2">
                                        <a class="nav-link text-active-primary ms-0 me-10 py-5 @if ($activeTab === 'lancamento-padrao') active @endif"
                                            href="{{ route('contabilidade.index', ['tab' => 'lancamento-padrao']) }}">Categoria</a>
                                    </li>
                                    <!--end::Nav item-->
                                    <!--begin::Nav item-->
                                    <li class="nav-item mt-2">
                                        <a class="nav-link text-active-primary ms-0 me-10 py-5 @if ($activeTab === 'plano-contas') active @endif"
                                            href="{{ route('contabilidade.index', ['tab' => 'plano-contas']) }}">Plano
                                            de Contas</a>
                                    </li>
                                    <!--end::Nav item-->
                                    <!--begin::Nav item-->
                                    <li class="nav-item mt-2">
                                        <a class="nav-link text-active-primary ms-0 me-10 py-5 @if ($activeTab === 'mapeamento') active @endif"
                                            href="{{ route('contabilidade.index', ['tab' => 'mapeamento']) }}">Mapeamento
                                            (DE/PARA)</a>
                                    </li>
                                    <!--end::Nav item-->
                                </ul>
                                <!--end::Navs-->
                                
                                <!--begin::Action Buttons-->
                                <div class="d-flex gap-3">
                                    @if ($activeTab === 'lancamento-padrao')
                                        <!--begin::Dropdown Nova Categoria-->
                                        <div class="dropdown">
                                            <button type="button" class="btn btn-sm btn-success dropdown-toggle" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-plus-circle fs-5 me-1"></i>
                                                <span class="text-nowrap">Nova Categoria</span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center py-3" href="#"
                                                        data-bs-toggle="modal" data-bs-target="#kt_modal_lancamento_padrao"
                                                        data-lancamento-type="entrada">
                                                        <span class="bullet bullet-dot bg-success me-3 h-10px w-10px"></span>
                                                        <span class="fw-semibold">Receita (Entrada)</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center py-3" href="#"
                                                        data-bs-toggle="modal" data-bs-target="#kt_modal_lancamento_padrao"
                                                        data-lancamento-type="saida">
                                                        <span class="bullet bullet-dot bg-danger me-3 h-10px w-10px"></span>
                                                        <span class="fw-semibold">Despesa (Saída)</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <!--end::Dropdown Nova Categoria-->
                                    @elseif ($activeTab === 'plano-contas')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_new_account">
                                            <i class="bi bi-plus-circle fs-5 me-1"></i>
                                            <span class="text-nowrap">Nova Conta</span>
                                        </button>
                                    @endif
                                </div>
                                <!--end::Action Buttons-->
                            </div>
                            <!--end::Navs Container-->
                        </div>
                        <!--end::Card-->
                        <!--begin::Card-->
                        <!--begin::Tab content-->
                        <div class="tab-content" id="myTabContent">
                            <!--begin::Tab pane-->
                            <div class="tab-pane fade @if ($activeTab === 'plano-contas') show active @endif"
                                id="kt_tab_pane_plano_contas" role="tabpanel">
                                {{-- Inclui o conteúdo da tabela do plano de contas --}}
                                @include('app.contabilidade.plano_de_contas._table')
                            </div>
                            <!--end::Tab pane-->

                            <!--begin::Tab pane-->
                            <div class="tab-pane fade @if ($activeTab === 'mapeamento') show active @endif"
                                id="kt_tab_pane_mapeamento" role="tabpanel">
                                {{-- Inclui o conteúdo da tabela de mapeamento --}}
                                @include('app.contabilidade.mapeamento._table')
                            </div>
                            <!--end::Tab pane-->
                            <!--begin::Tab pane-->
                            <div class="tab-pane fade @if ($activeTab === 'lancamento-padrao') show active @endif"
                                id="kt_tab_pane_lancamento_padrao" role="tabpanel">
                                {{-- Inclui o conteúdo da tabela de lançamentos padrão --}}
                                @include('app.contabilidade.lancamento_padrao._table')
                            </div>
                            <!--end::Tab pane-->
                        </div>
                        <!--end::Tab content-->
                        <!--end::Card-->
                    </div>
                    <!--end::Content container-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Content wrapper-->
        </div>
        <!--end:::Main-->
    </x-tenant-app-layout>

    <!--begin::Vendors Javascript(used for this page only)-->
    <script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>
    <!--begin::Custom Javascript(used for this page only)-->
    <!-- Scripts específicos são carregados dentro de cada partial -->
    <!--end::Vendors Javascript-->
