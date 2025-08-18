<x-tenant-app-layout>
    @include('app.components.modals.contabilidade.planoConta')
    @include('app.components.modals.contabilidade.mapear')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Contabilidade
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1"
                            aria-label="Navegação do site">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted" aria-current="page">Contabilidade</li>
                        </ul>
                    </div>
                    <!--end::Page title-->
                    <!--end::Page title-->
                    {{-- <!--begin::Actions-->
									<div class="d-flex align-items-center gap-2 gap-lg-3">
										<!--begin::Filter menu-->
										<div class="d-flex">
											<select name="campaign-type" data-control="select2" data-hide-search="true" class="form-select form-select-sm bg-body border-body w-175px">
												<option value="Twitter" selected="selected">Select Campaign</option>
												<option value="Twitter">Twitter Campaign</option>
												<option value="Twitter">Facebook Campaign</option>
												<option value="Twitter">Adword Campaign</option>
												<option value="Twitter">Carbon Campaign</option>
											</select>
											<a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
														<rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</a>
										</div>
										<!--end::Filter menu-->
										<!--begin::Secondary button-->
										<!--end::Secondary button-->
										<!--begin::Primary button-->
										<!--end::Primary button-->
									</div>
									<!--end::Actions--> --}}
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Card-->
                    <div class="card card-flush pb-0 bgi-position-y-center bgi-no-repeat mb-10"
                        style="background-size: auto calc(100% + 10rem); background-position-x: 100%; background-image: url('assets/media/illustrations/sketchy-1/4.png')">
                        <!--begin::Card body-->
                        										<!--begin::Card header-->
										<div class="card-header pt-10">
											<div class="d-flex align-items-center">
												<!--begin::Icon-->
												<div class="symbol symbol-circle me-5">
													<div class="symbol-label bg-transparent text-primary border border-secondary border-dashed">
														<!--begin::Svg Icon | path: icons/duotune/abstract/abs020.svg-->
														<span class="svg-icon svg-icon-2x svg-icon-primary">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M17.302 11.35L12.002 20.55H21.202C21.802 20.55 22.202 19.85 21.902 19.35L17.302 11.35Z" fill="currentColor" />
																<path opacity="0.3" d="M12.002 20.55H2.802C2.202 20.55 1.80202 19.85 2.10202 19.35L6.70203 11.45L12.002 20.55ZM11.302 3.45L6.70203 11.35H17.302L12.702 3.45C12.402 2.85 11.602 2.85 11.302 3.45Z" fill="currentColor" />
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
                <div class="tab-pane fade @if($activeTab === 'plano-contas') show active @endif" id="kt_tab_pane_plano_contas" role="tabpanel">
                    {{-- Inclui o conteúdo da tabela do plano de contas --}}
                    @include('app.contabilidade.plano_de_contas._table')
                </div>
                <!--end::Tab pane-->

                <!--begin::Tab pane-->
                <div class="tab-pane fade @if($activeTab === 'mapeamento') show active @endif" id="kt_tab_pane_mapeamento" role="tabpanel">
                    {{-- Inclui o conteúdo da tabela de mapeamento --}}
                    @include('app.contabilidade.mapeamento._table')
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
<script src="/assets/js/custom/apps/file-manager/list.js"></script>

<!--end::Vendors Javascript-->
