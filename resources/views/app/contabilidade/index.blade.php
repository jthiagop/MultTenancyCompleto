<x-tenant-app-layout>
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
                <div id="kt_app_content_container" class="app-container container-xxl py-3 py-lg-6">
                    <!--begin::Card-->
                    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10"
                        style="background-size: auto calc(100% + 10rem); background-position-x: 100%; background-image: url('assets/media/illustrations/sketchy-1/4.png')">
                        <!--begin::Card body-->
                        <!--begin::Card header-->
                        <div class="card-header pt-10">
                            <div class="d-flex align-items-center">
                                <!--begin::Icon-->
                                <div class="symbol symbol-circle me-5">
                                    <div
                                        class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
                                        <span class="svg-icon svg-icon-2x svg-icon-primary">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M17.302 11.35L12.002 20.55H21.202C21.802 20.55 22.202 19.85 21.902 19.35L17.302 11.35Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                    d="M12.002 20.55H2.802C2.202 20.55 1.80202 19.85 2.10202 19.35L6.70203 11.45L12.002 20.55ZM11.302 3.45L6.70203 11.35H17.302L12.702 3.45C12.402 2.85 11.602 2.85 11.302 3.45Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                </div>
                                <!--end::Icon-->
                                <!--begin::Title-->
                                <div class="d-flex flex-column">
                                    <h2 class="mb-1">Gerenciamento de Contabilidade</h2>
                                </div>
                                <!--end::Title-->
                            </div>
                        </div>
                        <!--end::Card header-->
                        <div class="card-body pb-0">
                            <!--begin::Navs-->
                            <div class="d-flex overflow-auto h-55px">
                                <!--begin::Navs-->
                                <ul
                                    class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-semibold">
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
                                    <!--begin::Nav item-->
                                    <li class="nav-item mt-2">
                                        <a class="nav-link text-active-primary ms-0 me-10 py-5 @if ($activeTab === 'lancamento-padrao') active @endif"
                                            href="{{ route('contabilidade.index', ['tab' => 'lancamento-padrao']) }}">Lançamento
                                            Padrão</a>
                                    </li>
                                    <!--end::Nav item-->
                                </ul>
                                <!--begin::Navs-->
                            </div>
                            <!--begin::Navs-->
                        </div>
                        <!--end::Card body-->
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
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--begin::Custom Javascript(used for this page only)-->
<!-- Scripts específicos são carregados dentro de cada partial -->
<!--end::Vendors Javascript-->
