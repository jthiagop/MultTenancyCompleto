
<x-tenant-app-layout>



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
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Referrals</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Account</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
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
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Nav items-->
                    <div id="kt_user_profile_nav" class="rounded bg-gray-200 d-flex flex-stack flex-wrap mb-9 p-2"
                        data-kt-page-scroll-position="400" data-kt-sticky="true"
                        data-kt-sticky-name="sticky-profile-navs"
                        data-kt-sticky-offset="{default: false, lg: '200px'}"
                        data-kt-sticky-width="{target: '#kt_user_profile_panel'}" data-kt-sticky-left="auto"
                        data-kt-sticky-top="70px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95">
                        <!--begin::Nav-->
                        <ul class="nav flex-wrap border-transparent">
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="{{ route('caixa.create') }}"> <label for=""><img src="assets/media/icons/duotune/finance/fin008.svg"/>
                                    </i></label> Movimentar Caixa</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/settings.html"> <label for=""><img src="assets/media/icons/duotune/finance/fin001.svg"/></label> Movimentar Banco</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/security.html">Security</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm kt_table_users-color-gray-600 bg-state-body kt_table_users-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/activity.html">Activity</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/billing.html">Billing</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/statements.html">Statements</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1 active"
                                    href="../../demo1/dist/account/referrals.html">Referrals</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/api-keys.html">API Keys</a>
                            </li>
                            <!--end::Nav item-->
                            <!--begin::Nav item-->
                            <li class="nav-item my-1">
                                <a class="btn btn-sm btn-color-gray-600 bg-state-body btn-active-color-gray-800 fw-bolder fw-bold fs-6 fs-lg-base nav-link px-3 px-lg-4 mx-1"
                                    href="../../demo1/dist/account/logs.html">Logs</a>
                            </li>
                            <!--end::Nav item-->
                        </ul>
                        <!--end::Nav-->
                    </div>
                    <!--end::Nav items-->
                    <!--begin::Referral program-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Body-->
                        <div class="card-body py-10">
                            <!--begin::Stats-->
                            <div class="row">
                                <!--begin::Col-->
                                <div class="col">
                                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                        <span class="fs-4 fw-semibold text-info pb-1 px-2">Net Earnings</span>
                                        <span class="fs-lg-2tx fw-bold d-flex justify-content-center">$
                                            <span data-kt-countup="true"
                                                data-kt-countup-value="63,240.00">0</span></span>
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col">
                                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                        <span class="fs-4 fw-semibold text-success pb-1 px-2">Balance</span>
                                        <span class="fs-lg-2tx fw-bold d-flex justify-content-center">$
                                            <span data-kt-countup="true"
                                                data-kt-countup-value="8,530.00">0</span></span>
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col">
                                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                        <span class="fs-4 fw-semibold text-danger d-block">Failed Attempts</span>
                                        <span class="fs-2hx fw-bold text-gray-900" data-kt-countup="true" data-kt-countup-value="291">0</span>
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col">
                                    <div class="border border-dashed border-gray-300 text-center min-w-125px rounded pt-4 pb-2 my-3">
                                        <span class="fs-4 fw-semibold text-primary pb-1 px-2">Referral Signups</span>
                                        <span class="fs-lg-2tx fw-bold d-flex justify-content-center">$
                                            <span data-kt-countup="true"
                                                data-kt-countup-value="783&quot;">0</span></span>
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Referral program-->
							<!--begin::Toolbar-->
							<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
								<!--begin::Toolbar container-->
								<div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
									<!--begin::Page title-->
									<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
										<!--begin::Title-->
										<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Últimos Lançamentos</h1>
										<!--end::Title-->
										<!--begin::Breadcrumb-->
										<ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
											<!--begin::Item-->
											<li class="breadcrumb-item text-muted">
                                            Caixa local e bancarios
											</li>
											<!--end::Item-->
											<!--begin::Item-->
											<li class="breadcrumb-item">
												<span class="bullet bg-gray-400 w-5px h-2px"></span>
											</li>
											<!--end::Item-->
											<!--begin::Item-->
											<li class="breadcrumb-item text-muted">User Management</li>
											<!--end::Item-->
											<!--begin::Item-->
											<li class="breadcrumb-item">
												<span class="bullet bg-gray-400 w-5px h-2px"></span>
											</li>
											<!--end::Item-->
											<!--begin::Item-->
											<li class="breadcrumb-item text-muted">Users</li>
											<!--end::Item-->
										</ul>
										<!--end::Breadcrumb-->
									</div>
									<!--end::Page title-->
									<!--begin::Actions-->
									{{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
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
									</div> --}}
									<!--end::Actions-->
								</div>
								<!--end::Toolbar container-->
							</div>
							<!--end::Toolbar-->

									<!--begin::Card-->
									<div class="card">
										<!--begin::Card header-->
										<div class="card-header border-0 pt-6">
											<!--begin::Card title-->
											<div class="card-title">
												<!--begin::Search-->
												<div class="d-flex align-items-center position-relative my-1">
													<!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
													<span class="svg-icon svg-icon-1 position-absolute ms-6">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
															<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->
													<input type="text" data-kt-user-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search user" />
												</div>
												<!--end::Search-->
											</div>
											<!--begin::Card title-->
											<!--begin::Card toolbar-->
											<div class="card-toolbar">
												<!--begin::Toolbar-->
												<div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
													<!--begin::Filter-->
													<button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
													<!--begin::Svg Icon | path: icons/duotune/general/gen031.svg-->
													<span class="svg-icon svg-icon-2">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->Filter</button>
													<!--begin::Menu 1-->
													<div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
														<!--begin::Header-->
														<div class="px-7 py-5">
															<div class="fs-5 text-dark fw-bold">Filter Options</div>
														</div>
														<!--end::Header-->
														<!--begin::Separator-->
														<div class="separator border-gray-200"></div>
														<!--end::Separator-->
														<!--begin::Content-->
														<div class="px-7 py-5" data-kt-user-table-filter="form">
															<!--begin::Input group-->
															<div class="mb-10">
																<label class="form-label fs-6 fw-semibold">Role:</label>
																<select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="role" data-hide-search="true">
																	<option></option>
																	<option value="Administrator">Administrator</option>
																	<option value="Analyst">Analyst</option>
																	<option value="Developer">Developer</option>
																	<option value="Support">Support</option>
																	<option value="Trial">Trial</option>
																</select>
															</div>
															<!--end::Input group-->
															<!--begin::Input group-->
															<div class="mb-10">
																<label class="form-label fs-6 fw-semibold">Two Step Verification:</label>
																<select class="form-select form-select-solid fw-bold" data-kt-select2="true" data-placeholder="Select option" data-allow-clear="true" data-kt-user-table-filter="two-step" data-hide-search="true">
																	<option></option>
																	<option value="Enabled">Filtrar</option>
																</select>
															</div>
															<!--end::Input group-->
															<!--begin::Actions-->
															<div class="d-flex justify-content-end">
																<button type="reset" class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
																<button type="submit" class="btn btn-primary fw-semibold px-6" data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
															</div>
															<!--end::Actions-->
														</div>
														<!--end::Content-->
													</div>
													<!--end::Menu 1-->
													<!--end::Filter-->
													<!--begin::Export-->
													<button type="button" class="btn btn-light-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_export_users">
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
													<span class="svg-icon svg-icon-2">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
															<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
															<path opacity="0.3" d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->Exportar</button>
													<!--end::Export-->
													<!--begin::Add user-->
													{{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
													<span class="svg-icon svg-icon-2">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
															<rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->Add User</button>
													<!--end::Add user--> --}}
												</div>
												<!--end::Toolbar-->
												<!--begin::Group actions-->
												<div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
													<div class="fw-bold me-5">
													<span class="me-2" data-kt-user-table-select="selected_count"></span>Selecioado</div>
													<button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Excluir Selecionado</button>
												</div>
												<!--end::Group actions-->
												<!--begin::Modal - Adjust Balance-->
												<div class="modal fade" id="kt_modal_export_users" tabindex="-1" aria-hidden="true">
													<!--begin::Modal dialog-->
													<div class="modal-dialog modal-dialog-centered mw-650px">
														<!--begin::Modal content-->
														<div class="modal-content">
															<!--begin::Modal header-->
															<div class="modal-header">
																<!--begin::Modal title-->
																<h2 class="fw-bold">Export Users</h2>
																<!--end::Modal title-->
																<!--begin::Close-->
																<div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
																			<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</div>
																<!--end::Close-->
															</div>
															<!--end::Modal header-->
															<!--begin::Modal body-->
															<div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
																<!--begin::Form-->
																<form id="kt_modal_export_users_form" class="form" action="#">
																	<!--begin::Input group-->
																	<div class="fv-row mb-10">
																		<!--begin::Label-->
																		<label class="fs-6 fw-semibold form-label mb-2">Select Roles:</label>
																		<!--end::Label-->
																		<!--begin::Input-->
																		<select name="role" data-control="select2" data-placeholder="Select a role" data-hide-search="true" class="form-select form-select-solid fw-bold">
																			<option></option>
																			<option value="Administrator">Administrator</option>
																			<option value="Analyst">Analyst</option>
																			<option value="Developer">Developer</option>
																			<option value="Support">Support</option>
																			<option value="Trial">Trial</option>
																		</select>
																		<!--end::Input-->
																	</div>
																	<!--end::Input group-->
																	<!--begin::Input group-->
																	<div class="fv-row mb-10">
																		<!--begin::Label-->
																		<label class="required fs-6 fw-semibold form-label mb-2">Select Export Format:</label>
																		<!--end::Label-->
																		<!--begin::Input-->
																		<select name="format" data-control="select2" data-placeholder="Select a format" data-hide-search="true" class="form-select form-select-solid fw-bold">
																			<option></option>
																			<option value="excel">Excel</option>
																			<option value="pdf">PDF</option>
																			<option value="cvs">CVS</option>
																			<option value="zip">ZIP</option>
																		</select>
																		<!--end::Input-->
																	</div>
																	<!--end::Input group-->
																	<!--begin::Actions-->
																	<div class="text-center">
																		<button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
																		<button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
																			<span class="indicator-label">Submit</span>
																			<span class="indicator-progress">Please wait...
																			<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
																		</button>
																	</div>
																	<!--end::Actions-->
																</form>
																<!--end::Form-->
															</div>
															<!--end::Modal body-->
														</div>
														<!--end::Modal content-->
													</div>
													<!--end::Modal dialog-->
												</div>
												<!--end::Modal - New Card-->
												<!--begin::Modal - Add task-->
												<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-hidden="true">
													<!--begin::Modal dialog-->
													<div class="modal-dialog modal-dialog-centered mw-650px">
														<!--begin::Modal content-->
														<div class="modal-content">
															<!--begin::Modal header-->
															<div class="modal-header" id="kt_modal_add_user_header">
																<!--begin::Modal title-->
																<h2 class="fw-bold">Add User</h2>
																<!--end::Modal title-->
																<!--begin::Close-->
																<div class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close">
																	<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
																	<span class="svg-icon svg-icon-1">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
																			<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</div>
																<!--end::Close-->
															</div>
															<!--end::Modal header-->
															<!--begin::Modal body-->
															<div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
																<!--begin::Form-->
																<form id="kt_modal_add_user_form" class="form" action="#">
																	<!--begin::Scroll-->
																	<div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
																		<!--begin::Input group-->
																		<div class="fv-row mb-7">
																			<!--begin::Label-->
																			<label class="d-block fw-semibold fs-6 mb-5">Avatar</label>
																			<!--end::Label-->
																			<!--begin::Image placeholder-->
																			<style>.image-input-placeholder { background-image: url('assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }</style>
																			<!--end::Image placeholder-->
																			<!--begin::Image input-->
																			<div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
																				<!--begin::Preview existing avatar-->
																				<div class="image-input-wrapper w-125px h-125px" style="background-image: url(assets/media/avatars/300-6.jpg);"></div>
																				<!--end::Preview existing avatar-->
																				<!--begin::Label-->
																				<label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
																					<i class="bi bi-pencil-fill fs-7"></i>
																					<!--begin::Inputs-->
																					<input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
																					<input type="hidden" name="avatar_remove" />
																					<!--end::Inputs-->
																				</label>
																				<!--end::Label-->
																				<!--begin::Cancel-->
																				<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
																					<i class="bi bi-x fs-2"></i>
																				</span>
																				<!--end::Cancel-->
																				<!--begin::Remove-->
																				<span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
																					<i class="bi bi-x fs-2"></i>
																				</span>
																				<!--end::Remove-->
																			</div>
																			<!--end::Image input-->
																			<!--begin::Hint-->
																			<div class="form-text">Allowed file types: png, jpg, jpeg.</div>
																			<!--end::Hint-->
																		</div>
																		<!--end::Input group-->
																		<!--begin::Input group-->
																		<div class="fv-row mb-7">
																			<!--begin::Label-->
																			<label class="required fw-semibold fs-6 mb-2">Full Name</label>
																			<!--end::Label-->
																			<!--begin::Input-->
																			<input type="text" name="user_name" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="Full name" value="Emma Smith" />
																			<!--end::Input-->
																		</div>
																		<!--end::Input group-->
																		<!--begin::Input group-->
																		<div class="fv-row mb-7">
																			<!--begin::Label-->
																			<label class="required fw-semibold fs-6 mb-2">Email</label>
																			<!--end::Label-->
																			<!--begin::Input-->
																			<input type="email" name="user_email" class="form-control form-control-solid mb-3 mb-lg-0" placeholder="example@domain.com" value="smith@kpmg.com" />
																			<!--end::Input-->
																		</div>
																		<!--end::Input group-->
																		<!--begin::Input group-->
																		<div class="mb-7">
																			<!--begin::Label-->
																			<label class="required fw-semibold fs-6 mb-5">Role</label>
																			<!--end::Label-->
																			<!--begin::Roles-->
																			<!--begin::Input row-->
																			<div class="d-flex fv-row">
																				<!--begin::Radio-->
																				<div class="form-check form-check-custom form-check-solid">
																					<!--begin::Input-->
																					<input class="form-check-input me-3" name="user_role" type="radio" value="0" id="kt_modal_update_role_option_0" checked='checked' />
																					<!--end::Input-->
																					<!--begin::Label-->
																					<label class="form-check-label" for="kt_modal_update_role_option_0">
																						<div class="fw-bold text-gray-800">Administrator</div>
																						<div class="text-gray-600">Best for business owners and company administrators</div>
																					</label>
																					<!--end::Label-->
																				</div>
																				<!--end::Radio-->
																			</div>
																			<!--end::Input row-->
																			<div class='separator separator-dashed my-5'></div>
																			<!--begin::Input row-->
																			<div class="d-flex fv-row">
																				<!--begin::Radio-->
																				<div class="form-check form-check-custom form-check-solid">
																					<!--begin::Input-->
																					<input class="form-check-input me-3" name="user_role" type="radio" value="1" id="kt_modal_update_role_option_1" />
																					<!--end::Input-->
																					<!--begin::Label-->
																					<label class="form-check-label" for="kt_modal_update_role_option_1">
																						<div class="fw-bold text-gray-800">Developer</div>
																						<div class="text-gray-600">Best for developers or people primarily using the API</div>
																					</label>
																					<!--end::Label-->
																				</div>
																				<!--end::Radio-->
																			</div>
																			<!--end::Input row-->
																			<div class='separator separator-dashed my-5'></div>
																			<!--begin::Input row-->
																			<div class="d-flex fv-row">
																				<!--begin::Radio-->
																				<div class="form-check form-check-custom form-check-solid">
																					<!--begin::Input-->
																					<input class="form-check-input me-3" name="user_role" type="radio" value="2" id="kt_modal_update_role_option_2" />
																					<!--end::Input-->
																					<!--begin::Label-->
																					<label class="form-check-label" for="kt_modal_update_role_option_2">
																						<div class="fw-bold text-gray-800">Analyst</div>
																						<div class="text-gray-600">Best for people who need full access to analytics data, but don't need to update business settings</div>
																					</label>
																					<!--end::Label-->
																				</div>
																				<!--end::Radio-->
																			</div>
																			<!--end::Input row-->
																			<div class='separator separator-dashed my-5'></div>
																			<!--begin::Input row-->
																			<div class="d-flex fv-row">
																				<!--begin::Radio-->
																				<div class="form-check form-check-custom form-check-solid">
																					<!--begin::Input-->
																					<input class="form-check-input me-3" name="user_role" type="radio" value="3" id="kt_modal_update_role_option_3" />
																					<!--end::Input-->
																					<!--begin::Label-->
																					<label class="form-check-label" for="kt_modal_update_role_option_3">
																						<div class="fw-bold text-gray-800">Support</div>
																						<div class="text-gray-600">Best for employees who regularly refund payments and respond to disputes</div>
																					</label>
																					<!--end::Label-->
																				</div>
																				<!--end::Radio-->
																			</div>
																			<!--end::Input row-->
																			<div class='separator separator-dashed my-5'></div>
																			<!--begin::Input row-->
																			<div class="d-flex fv-row">
																				<!--begin::Radio-->
																				<div class="form-check form-check-custom form-check-solid">
																					<!--begin::Input-->
																					<input class="form-check-input me-3" name="user_role" type="radio" value="4" id="kt_modal_update_role_option_4" />
																					<!--end::Input-->
																					<!--begin::Label-->
																					<label class="form-check-label" for="kt_modal_update_role_option_4">
																						<div class="fw-bold text-gray-800">Trial</div>
																						<div class="text-gray-600">Best for people who need to preview content data, but don't need to make any updates</div>
																					</label>
																					<!--end::Label-->
																				</div>
																				<!--end::Radio-->
																			</div>
																			<!--end::Input row-->
																			<!--end::Roles-->
																		</div>
																		<!--end::Input group-->
																	</div>
																	<!--end::Scroll-->
																	<!--begin::Actions-->
																	<div class="text-center pt-15">
																		<button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Discard</button>
																		<button type="submit" class="btn btn-primary" data-kt-users-modal-action="submit">
																			<span class="indicator-label">Submit</span>
																			<span class="indicator-progress">Please wait...
																			<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
																		</button>
																	</div>
																	<!--end::Actions-->
																</form>
																<!--end::Form-->
															</div>
															<!--end::Modal body-->
														</div>
														<!--end::Modal content-->
													</div>
													<!--end::Modal dialog-->
												</div>
												<!--end::Modal - Add task-->
											</div>
											<!--end::Card toolbar-->
										</div>
										<!--end::Card header-->
										<!--begin::Card body-->
										<div class="card-body py-4">
											<!--begin::Table-->
											<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
												<!--begin::Table head-->
												<thead>
													<!--begin::Table row-->
													<tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
														<th class="w-10px pe-2">
															<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
																<input class="form-check-input" type="checkbox" data-kt-check="true" data-kt-check-target="#kt_table_users .form-check-input" value="1" />
															</div>
														</th>
														<th class="min-w-125px">ID</th>
														<th class="min-w-125px">Data</th>
														<th class="min-w-125px">Tipo Docuemnto</th>
														<th class="min-w-125px">Documento</th>
														<th class="min-w-125px">Tipo</th>
														<th class="min-w-125px">Valor</th>
														<th class="min-w-125px">Origem</th>
														<th class="text-end min-w-100px">Ações</th>
													</tr>
													<!--end::Table row-->
												</thead>
												<!--end::Table head-->
												<!--begin::Table body-->
												<tbody class="text-gray-600 fw-semibold">
													<!--begin::Table row-->
                                                    @foreach ( $caixas as $caixa )
													<tr>
														<!--begin::Checkbox-->      
														<td>
															<div class="form-check form-check-sm form-check-custom form-check-solid">
																<input class="form-check-input" type="checkbox" value="{{ $caixa->id }}" />
															</div>
														</td>
														<!--end::Checkbox-->
														<!--begin::User=-->
														<td>{{ $caixa->id }}</td>
														<!--end::User=-->
														<!--begin::Role=-->
														<td>{{ $caixa->data_competencia }}</td>
														<!--end::Role=-->
														<!--begin::Last login=-->
														<td>{{ $caixa->tipo_documento }}
														</td>
														<!--end::Last login=-->
														<!--begin::Two step=-->
														<td>{{ $caixa->lancamento_padrao }}</td>
														<!--end::Two step=-->
														<!--begin::Joined-->
														<td>    
                                                            <div class="badge fw-bold {{ $caixa->tipo == 'entrada' ? 'badge-success' : 'badge-danger' }}">
                                                                {{ $caixa->tipo }}
                                                            </div>
                                                        </td>
														<!--begin::Joined-->
                                                        <td>R$ {{ number_format($caixa->valor, 2, ',', '.') }}</td>
                                                        <td></td>
														<!--begin::Action=-->
														<td class="text-end">
															<a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações
															<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
															<span class="svg-icon svg-icon-5 m-0">
																<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
																</svg>
															</span>
															<!--end::Svg Icon--></a>
															<!--begin::Menu-->
															<div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
																<!--begin::Menu item-->
																<div class="menu-item px-3">
																	<a href="{{ route('caixa.edit', $caixa->id) }}" class="menu-link px-3">Editar</a>
																</div>
																<!--end::Menu item-->
																<!--begin::Menu item-->
																<div class="menu-item px-3">
                                                                    <a class="dropdown-item" data-bs-toggle="modal" href="#staticBackdrop"
                                                                    data-bs-target="#staticBackdrop"><i
                                                                        class="fa-regular fa-trash-can px-1"></i> Excluir</a>																</div>
																<!--end::Menu item-->
															</div>
															<!--end::Menu-->
														</td>
														<!--end::Action=-->
													</tr>
                                                    @endforeach
													<!--end::Table row-->
												</tbody>
												<!--end::Table body-->
											</table>
											<!--end::Table-->
										</div>
										<!--end::Card body-->
									</div>
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
		<script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
		<!--end::Vendors Javascript-->
		<!--begin::Custom Javascript(used for this page only)-->
		<script src="assets/js/custom/pages/user-profile/general.js"></script>
		<script src="assets/js/custom/account/settings/signin-methods.js"></script>
		<script src="assets/js/custom/account/security/security-summary.js"></script>
		<script src="assets/js/custom/account/security/license-usage.js"></script>
		<script src="assets/js/custom/account/settings/deactivate-account.js"></script>
		<script src="assets/js/widgets.bundle.js"></script>
		<script src="assets/js/custom/apps/chat/chat.js"></script>
		<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
		<script src="assets/js/custom/utilities/modals/create-campaign.js"></script>
		<script src="assets/js/custom/utilities/modals/users-search.js"></script>
		<!--end::Custom Javascript-->

		<!--begin::Custom Javascript(used for this page only)-->
		<script src="assets/js/custom/apps/user-management/users/list/table.js"></script>
		<script src="assets/js/custom/apps/user-management/users/list/export-users.js"></script>
		<script src="assets/js/custom/apps/user-management/users/list/add.js"></script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
<!--end::Javascript-->
<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="staticBackdropLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content modal-danger">
        <form action="{{ route('caixa.destroy', $caixa->id)}}" method="post">
            <?php echo csrf_field(); ?>
            <?php echo method_field('delete'); ?>
            <div class="modal-body modal-center">
                <h3 class="text-danger mb-5">
                    <i class="fa fa-exclamation-triangle"></i> ATENÇÂO
                </h3>
                <p>
                    Você está preste a excluir uma lançamento! Deseja realmente excluir este registro?
                </p>

                <small>
                    <div id="mensagem-excluir"></div>
                </small>

                <input type="hidden" class="form-control" name="id-excluir" id="id-excluir" value="97">
            </div>
            <div class="modal-footer"><button class="btn btn-success" type="button"
                    data-bs-dismiss="modal">Não</button><button class="btn btn-danger" type='submit'>Sim</button>
            </div>
        </form>
    </div>
</div>
</div>