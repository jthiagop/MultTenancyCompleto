                <!--begin::Header-->
				<div id="kt_app_header" class="app-header">
					<!--begin::Header container-->
					<div class="app-container container-xxl d-flex align-items-stretch justify-content-between" id="kt_app_header_container">
						<!--begin::Logo-->
						<div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
							<a href="/#index.html">
                                @include('app.components.application-logo')
							</a>
						</div>
						<!--end::Logo-->
						<!--begin::Header wrapper-->
						<div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
							<!--begin::Menu wrapper-->
							<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
								<!--begin::Menu-->
								<div class="menu menu-rounded menu-column menu-lg-row my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0" id="kt_app_header_menu" data-kt-menu="true">
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-here-bg menu-lg-down-accordion me-0 me-lg-2">
										<!--begin:Menu link-->
										<span class="menu-link">
											<span class="menu-title">Dashboards</span>
											<span class="menu-arrow d-lg-none"></span>
										</span>
										<!--end:Menu link-->
										                        <!--begin:Menu sub-->
                                                                <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0 w-100 w-lg-600px">
                                                                    <!--begin:Dashboards menu-->
                                                                    <div class="menu-state-bg menu-extended overflow-hidden overflow-lg-visible py-6"
                                                                        data-kt-menu-dismiss="true">
                                                                        <!--begin:Row-->
                                                                        <div class="row px-5">
                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="{{ route('dashboard') }}" class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen025.svg-->
                                                                                            <span class="svg-icon svg-icon-danger svg-icon-1">
                                                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <rect x="2" y="2" width="9" height="9"
                                                                                                        rx="2" fill="currentColor" />
                                                                                                    <rect opacity="0.3" x="13" y="2" width="9"
                                                                                                        height="9" rx="2" fill="currentColor" />
                                                                                                    <rect opacity="0.3" x="13" y="13" width="9"
                                                                                                        height="9" rx="2" fill="currentColor" />
                                                                                                    <rect opacity="0.3" x="2" y="13" width="9"
                                                                                                        height="9" rx="2" fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Tela Inicial</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Modulos principais</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->


                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="{{ route('caixa.index') }}" class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/graphs/gra001.svg-->
                                                                                            <!--begin::Svg Icon | path: /var/www/preview.keenthemes.com/keenthemes/keen/docs/core/html/src/media/icons/duotune/finance/fin003.svg-->
                                                                                            <span class="svg-icon svg-icon-dark svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path opacity="0.3" d="M20 18H4C3.4 18 3 17.6 3 17V7C3 6.4 3.4 6 4 6H20C20.6 6 21 6.4 21 7V17C21 17.6 20.6 18 20 18ZM12 8C10.3 8 9 9.8 9 12C9 14.2 10.3 16 12 16C13.7 16 15 14.2 15 12C15 9.8 13.7 8 12 8Z" fill="currentColor"/>
                                                                                            <path d="M18 6H20C20.6 6 21 6.4 21 7V9C19.3 9 18 7.7 18 6ZM6 6H4C3.4 6 3 6.4 3 7V9C4.7 9 6 7.7 6 6ZM21 17V15C19.3 15 18 16.3 18 18H20C20.6 18 21 17.6 21 17ZM3 15V17C3 17.6 3.4 18 4 18H6C6 16.3 4.7 15 3 15Z" fill="currentColor"/>
                                                                                            </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Financeiro</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Controle de gastos</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->
                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="{{ route('patrimonio.index') }}" class="menu-link active">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm002.svg-->
                                                                                            <span class="svg-icon svg-icon-primary svg-icon-1"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                <path opacity="0.3" d="M13.625 22H9.625V3C9.625 2.4 10.025 2 10.625 2H12.625C13.225 2 13.625 2.4 13.625 3V22Z" fill="currentColor"/>
                                                                                                <path d="M19.625 10H12.625V4H19.625L21.025 6.09998C21.325 6.59998 21.325 7.30005 21.025 7.80005L19.625 10Z" fill="currentColor"/>
                                                                                                <path d="M3.62499 16H10.625V10H3.62499L2.225 12.1001C1.925 12.6001 1.925 13.3 2.225 13.8L3.62499 16Z" fill="currentColor"/>
                                                                                                </svg>
                                                                                                </span>
                                                                                                <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Patrimônio</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Foro e Laudêmio</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->

                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="/#dashboards/projects.html" class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/abstract/abs045.svg-->
                                                                                            <span class="svg-icon svg-icon-info svg-icon-1">
                                                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path
                                                                                                        d="M2 11.7127L10 14.1127L22 11.7127L14 9.31274L2 11.7127Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path opacity="0.3"
                                                                                                        d="M20.9 7.91274L2 11.7127V6.81275C2 6.11275 2.50001 5.61274 3.10001 5.51274L20.6 2.01274C21.3 1.91274 22 2.41273 22 3.11273V6.61273C22 7.21273 21.5 7.81274 20.9 7.91274ZM22 16.6127V11.7127L3.10001 15.5127C2.50001 15.6127 2 16.2127 2 16.8127V20.3127C2 21.0127 2.69999 21.6128 3.39999 21.4128L20.9 17.9128C21.5 17.8128 22 17.2127 22 16.6127Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Projects</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Tasts, graphs
                                                                                                & charts</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->


                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="/#dashboards/social.html" class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/communication/com001.svg-->
                                                                                            <span class="svg-icon svg-icon-success svg-icon-1">
                                                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path opacity="0.3"
                                                                                                        d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path
                                                                                                        d="M19 10.4C19 10.3 19 10.2 19 10C19 8.9 18.1 8 17 8H16.9C15.6 6.2 14.6 4.29995 13.9 2.19995C13.3 2.09995 12.6 2 12 2C11.9 2 11.8 2 11.7 2C12.4 4.6 13.5 7.10005 15.1 9.30005C15 9.50005 15 9.7 15 10C15 11.1 15.9 12 17 12C17.1 12 17.3 12 17.4 11.9C18.6 13 19.9 14 21.4 14.8C21.4 14.8 21.5 14.8 21.5 14.9C21.7 14.2 21.8 13.5 21.9 12.7C20.9 12.1 19.9 11.3 19 10.4Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path
                                                                                                        d="M12 15C11 13.1 10.2 11.2 9.60001 9.19995C9.90001 8.89995 10 8.4 10 8C10 7.1 9.40001 6.39998 8.70001 6.09998C8.40001 4.99998 8.20001 3.90005 8.00001 2.80005C7.30001 3.10005 6.70001 3.40002 6.20001 3.90002C6.40001 4.80002 6.50001 5.6 6.80001 6.5C6.40001 6.9 6.10001 7.4 6.10001 8C6.10001 9 6.80001 9.8 7.80001 10C8.30001 11.6 9.00001 13.2 9.70001 14.7C7.10001 13.2 4.70001 11.5 2.40001 9.5C2.20001 10.3 2.10001 11.1 2.10001 11.9C4.60001 13.9 7.30001 15.7 10.1 17.2C10.2 18.2 11 19 12 19C12.6 20 13.2 20.9 13.9 21.8C14.6 21.7 15.3 21.5 15.9 21.2C15.4 20.5 14.9 19.8 14.4 19.1C15.5 19.5 16.5 19.9 17.6 20.2C18.3 19.8 18.9 19.2 19.4 18.6C17.6 18.1 15.7 17.5 14 16.7C13.9 15.8 13.1 15 12 15Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Social</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Feeds &
                                                                                                Activities</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->
                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="/#dashboards/bidding.html" class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                                                            <span class="svg-icon svg-icon-warning svg-icon-1">
                                                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path
                                                                                                        d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path opacity="0.3"
                                                                                                        d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Bidding</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Deals & stock
                                                                                                exchange</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->
                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="/#dashboards/online-courses.html"
                                                                                        class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/art/art002.svg-->
                                                                                            <span class="svg-icon svg-icon-success svg-icon-1">
                                                                                                <svg width="24" height="25" viewBox="0 0 24 25"
                                                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path opacity="0.3"
                                                                                                        d="M8.9 21L7.19999 22.6999C6.79999 23.0999 6.2 23.0999 5.8 22.6999L4.1 21H8.9ZM4 16.0999L2.3 17.8C1.9 18.2 1.9 18.7999 2.3 19.1999L4 20.9V16.0999ZM19.3 9.1999L15.8 5.6999C15.4 5.2999 14.8 5.2999 14.4 5.6999L9 11.0999V21L19.3 10.6999C19.7 10.2999 19.7 9.5999 19.3 9.1999Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path
                                                                                                        d="M21 15V20C21 20.6 20.6 21 20 21H11.8L18.8 14H20C20.6 14 21 14.4 21 15ZM10 21V4C10 3.4 9.6 3 9 3H4C3.4 3 3 3.4 3 4V21C3 21.6 3.4 22 4 22H9C9.6 22 10 21.6 10 21ZM7.5 18.5C7.5 19.1 7.1 19.5 6.5 19.5C5.9 19.5 5.5 19.1 5.5 18.5C5.5 17.9 5.9 17.5 6.5 17.5C7.1 17.5 7.5 17.9 7.5 18.5Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Online
                                                                                                Courses</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Student
                                                                                                progress</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->
                                                                            <!--begin:Col-->
                                                                            <div class="col-lg-6 py-1">
                                                                                <!--begin:Menu item-->
                                                                                <div class="menu-item p-0 m-0">
                                                                                    <!--begin:Menu link-->
                                                                                    <a href="/#dashboards/logistics.html" class="menu-link">
                                                                                        <span
                                                                                            class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                                                                            <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm006.svg-->
                                                                                            <span class="svg-icon svg-icon-info svg-icon-1">
                                                                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                                    <path
                                                                                                        d="M20 8H16C15.4 8 15 8.4 15 9V16H10V17C10 17.6 10.4 18 11 18H16C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18H21C21.6 18 22 17.6 22 17V13L20 8Z"
                                                                                                        fill="currentColor" />
                                                                                                    <path opacity="0.3"
                                                                                                        d="M20 18C20 19.1 19.1 20 18 20C16.9 20 16 19.1 16 18C16 16.9 16.9 16 18 16C19.1 16 20 16.9 20 18ZM15 4C15 3.4 14.6 3 14 3H3C2.4 3 2 3.4 2 4V13C2 13.6 2.4 14 3 14H15V4ZM6 16C4.9 16 4 16.9 4 18C4 19.1 4.9 20 6 20C7.1 20 8 19.1 8 18C8 16.9 7.1 16 6 16Z"
                                                                                                        fill="currentColor" />
                                                                                                </svg>
                                                                                            </span>
                                                                                            <!--end::Svg Icon-->
                                                                                        </span>
                                                                                        <span class="d-flex flex-column">
                                                                                            <span class="fs-6 fw-semibold text-gray-800">Logistics</span>
                                                                                            <span class="fs-7 fw-semibold text-muted">Shipments and
                                                                                                delivery</span>
                                                                                        </span>
                                                                                    </a>
                                                                                    <!--end:Menu link-->
                                                                                </div>
                                                                                <!--end:Menu item-->
                                                                            </div>
                                                                            <!--end:Col-->
                                                                        </div>
                                                                        <!--end:Row-->
                                                                    </div>
                                                                    <!--end:Dashboards menu-->
                                                                </div>
                                                                <!--end:Menu sub-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2 here show here show">
										<!--begin:Menu link-->
										<span class="menu-link">
											<span class="menu-title">Serviços</span>
											<span class="menu-arrow d-lg-none"></span>
										</span>
										<!--end:Menu link-->
										<!--begin:Menu sub-->
										<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-250px">
											<!--begin:Menu item-->
											<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
												<!--begin:Menu link-->
												<span class="menu-link">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/general/gen002.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M4.05424 15.1982C8.34524 7.76818 13.5782 3.26318 20.9282 2.01418C21.0729 1.98837 21.2216 1.99789 21.3618 2.04193C21.502 2.08597 21.6294 2.16323 21.7333 2.26712C21.8372 2.37101 21.9144 2.49846 21.9585 2.63863C22.0025 2.7788 22.012 2.92754 21.9862 3.07218C20.7372 10.4222 16.2322 15.6552 8.80224 19.9462L4.05424 15.1982ZM3.81924 17.3372L2.63324 20.4482C2.58427 20.5765 2.5735 20.7163 2.6022 20.8507C2.63091 20.9851 2.69788 21.1082 2.79503 21.2054C2.89218 21.3025 3.01536 21.3695 3.14972 21.3982C3.28408 21.4269 3.42387 21.4161 3.55224 21.3672L6.66524 20.1802L3.81924 17.3372ZM16.5002 5.99818C16.2036 5.99818 15.9136 6.08615 15.6669 6.25097C15.4202 6.41579 15.228 6.65006 15.1144 6.92415C15.0009 7.19824 14.9712 7.49984 15.0291 7.79081C15.0869 8.08178 15.2298 8.34906 15.4396 8.55884C15.6494 8.76862 15.9166 8.91148 16.2076 8.96935C16.4986 9.02723 16.8002 8.99753 17.0743 8.884C17.3484 8.77046 17.5826 8.5782 17.7474 8.33153C17.9123 8.08486 18.0002 7.79485 18.0002 7.49818C18.0002 7.10035 17.8422 6.71882 17.5609 6.43752C17.2796 6.15621 16.8981 5.99818 16.5002 5.99818Z" fill="currentColor" />
																<path d="M4.05423 15.1982L2.24723 13.3912C2.15505 13.299 2.08547 13.1867 2.04395 13.0632C2.00243 12.9396 1.9901 12.8081 2.00793 12.679C2.02575 12.5498 2.07325 12.4266 2.14669 12.3189C2.22013 12.2112 2.31752 12.1219 2.43123 12.0582L9.15323 8.28918C7.17353 10.3717 5.4607 12.6926 4.05423 15.1982ZM8.80023 19.9442L10.6072 21.7512C10.6994 21.8434 10.8117 21.9129 10.9352 21.9545C11.0588 21.996 11.1903 22.0083 11.3195 21.9905C11.4486 21.9727 11.5718 21.9252 11.6795 21.8517C11.7872 21.7783 11.8765 21.6809 11.9402 21.5672L15.7092 14.8442C13.6269 16.8245 11.3061 18.5377 8.80023 19.9442ZM7.04023 18.1832L12.5832 12.6402C12.7381 12.4759 12.8228 12.2577 12.8195 12.032C12.8161 11.8063 12.725 11.5907 12.5653 11.4311C12.4057 11.2714 12.1901 11.1803 11.9644 11.1769C11.7387 11.1736 11.5205 11.2583 11.3562 11.4132L5.81323 16.9562L7.04023 18.1832Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Projects</span>
													<span class="menu-arrow"></span>
												</span>
												<!--end:Menu link-->
												<!--begin:Menu sub-->
												<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/list.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">My Projects</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/project.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">View Project</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/targets.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Targets</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/budget.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Budget</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/users.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Users</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/files.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Files</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/activity.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Activity</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/projects/settings.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Settings</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
												</div>
												<!--end:Menu sub-->
											</div>
											<!--end:Menu item-->
											<!--begin:Menu item-->
											<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
												<!--begin:Menu link-->
												<span class="menu-link">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm001.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M18.041 22.041C18.5932 22.041 19.041 21.5932 19.041 21.041C19.041 20.4887 18.5932 20.041 18.041 20.041C17.4887 20.041 17.041 20.4887 17.041 21.041C17.041 21.5932 17.4887 22.041 18.041 22.041Z" fill="currentColor" />
																<path opacity="0.3" d="M6.04095 22.041C6.59324 22.041 7.04095 21.5932 7.04095 21.041C7.04095 20.4887 6.59324 20.041 6.04095 20.041C5.48867 20.041 5.04095 20.4887 5.04095 21.041C5.04095 21.5932 5.48867 22.041 6.04095 22.041Z" fill="currentColor" />
																<path opacity="0.3" d="M7.04095 16.041L19.1409 15.1409C19.7409 15.1409 20.141 14.7409 20.341 14.1409L21.7409 8.34094C21.9409 7.64094 21.4409 7.04095 20.7409 7.04095H5.44095L7.04095 16.041Z" fill="currentColor" />
																<path d="M19.041 20.041H5.04096C4.74096 20.041 4.34095 19.841 4.14095 19.541C3.94095 19.241 3.94095 18.841 4.14095 18.541L6.04096 14.841L4.14095 4.64095L2.54096 3.84096C2.04096 3.64096 1.84095 3.04097 2.14095 2.54097C2.34095 2.04097 2.94096 1.84095 3.44096 2.14095L5.44096 3.14095C5.74096 3.24095 5.94096 3.54096 5.94096 3.84096L7.94096 14.841C7.94096 15.041 7.94095 15.241 7.84095 15.441L6.54096 18.041H19.041C19.641 18.041 20.041 18.441 20.041 19.041C20.041 19.641 19.641 20.041 19.041 20.041Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">eCommerce</span>
													<span class="menu-arrow"></span>
												</span>
												<!--end:Menu link-->
												<!--begin:Menu sub-->
												<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Catalog</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/catalog/products.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Products</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/catalog/categories.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Categories</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/catalog/add-product.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Add Product</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/catalog/edit-product.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Edit Product</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/catalog/add-category.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Add Category</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/catalog/edit-category.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Edit Category</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-sub-indention">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Sales</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-accordion">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/sales/listing.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Orders Listing</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/sales/details.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Order Details</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/sales/add-order.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Add Order</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/sales/edit-order.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Edit Order</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-sub-indention">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Customers</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-accordion">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/customers/listing.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Customers Listing</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/customers/details.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Customers Details</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="click" class="menu-item menu-accordion menu-sub-indention">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Reports</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-accordion">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/reports/view.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Products Viewed</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/reports/sales.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Sales</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/reports/returns.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Returns</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/reports/customer-orders.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Customer Orders</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/ecommerce/reports/shipping.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Shipping</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/ecommerce/settings.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Settings</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
												</div>
												<!--end:Menu sub-->
											</div>
											<!--end:Menu item-->
											<!--begin:Menu item-->
											<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
												<!--begin:Menu link-->
												<span class="menu-link">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/graphs/gra006.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M20 18H4C3.4 18 3 17.6 3 17V7C3 6.4 3.4 6 4 6H20C20.6 6 21 6.4 21 7V17C21 17.6 20.6 18 20 18ZM12 8C10.3 8 9 9.8 9 12C9 14.2 10.3 16 12 16C13.7 16 15 14.2 15 12C15 9.8 13.7 8 12 8Z" fill="currentColor"/>
                                                                <path d="M18 6H20C20.6 6 21 6.4 21 7V9C19.3 9 18 7.7 18 6ZM6 6H4C3.4 6 3 6.4 3 7V9C4.7 9 6 7.7 6 6ZM21 17V15C19.3 15 18 16.3 18 18H20C20.6 18 21 17.6 21 17ZM3 15V17C3 17.6 3.4 18 4 18H6C6 16.3 4.7 15 3 15Z" fill="currentColor"/>
                                                                </svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Financeiro</span>
													<span class="menu-arrow"></span>
												</span>
												<!--end:Menu link-->
												<!--begin:Menu sub-->
												<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
                                                        <a class="menu-link {{ Route::currentRouteName() == 'caixa.index' ? 'active' : '' }}"href="{{ route('caixa.index') }}">
                                                            <span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Módulo de Financeiro</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
                                                    <!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
                                                        <a class="menu-link {{ Route::currentRouteName() == 'caixa.list' ? 'active' : '' }}"href="{{ route('caixa.list') }}">
                                                            <span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Transações do Caixa</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
                                                    <!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
                                                        <a class="menu-link {{ Route::currentRouteName() == 'banco.list' ? 'active' : '' }}"href="{{ route('banco.list') }}">
                                                            <span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Transações Bancárias</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->

													<!--begin:Menu item-->
													<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Cadastros</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
                                                                <a class="menu-link {{ Route::currentRouteName() == 'lancamentoPadrao.index' ? 'active' : '' }}" href="{{ route('lancamentoPadrao.index') }}">
                                                                <span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Lançamento Padrão</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link {{ Route::currentRouteName() == 'cadastroBancos.index' ? 'active' : '' }}" href="{{ route('cadastroBancos.index') }}">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Criar Bancos</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Tutorials</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/support-center/tutorials/list.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Tutorials List</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/support-center/tutorials/post.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Tutorials Post</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/support-center/faq.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">FAQ</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/support-center/licenses.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Licenses</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/support-center/contact.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Contact Us</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
												</div>
												<!--end:Menu sub-->
											</div>
											<!--end:Menu item-->
											<!--begin:Menu item-->
											<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
												<!--begin:Menu link-->
                                                @role('global|admin')
												<span class="menu-link">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/general/gen051.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor" />
																<path d="M14.854 11.321C14.7568 11.2282 14.6388 11.1818 14.4998 11.1818H14.3333V10.2272C14.3333 9.61741 14.1041 9.09378 13.6458 8.65628C13.1875 8.21876 12.639 8 12 8C11.361 8 10.8124 8.21876 10.3541 8.65626C9.89574 9.09378 9.66663 9.61739 9.66663 10.2272V11.1818H9.49999C9.36115 11.1818 9.24306 11.2282 9.14583 11.321C9.0486 11.4138 9 11.5265 9 11.6591V14.5227C9 14.6553 9.04862 14.768 9.14583 14.8609C9.24306 14.9536 9.36115 15 9.49999 15H14.5C14.6389 15 14.7569 14.9536 14.8542 14.8609C14.9513 14.768 15 14.6553 15 14.5227V11.6591C15.0001 11.5265 14.9513 11.4138 14.854 11.321ZM13.3333 11.1818H10.6666V10.2272C10.6666 9.87594 10.7969 9.57597 11.0573 9.32743C11.3177 9.07886 11.6319 8.9546 12 8.9546C12.3681 8.9546 12.6823 9.07884 12.9427 9.32743C13.2031 9.57595 13.3333 9.87594 13.3333 10.2272V11.1818Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Cadastros</span>
													<span class="menu-arrow"></span>
												</span>
												<!--end:Menu link-->
												<!--begin:Menu sub-->
												<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Usuário</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
                                                                <a class="menu-link {{ Route::currentRouteName() == 'users.index' ? 'active' : '' }}" href="{{ route('users.index') }}">
                                                                	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Lista de Usuário</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="{{ route('profile.edit') }}">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Ver usuário</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
														<!--begin:Menu link-->
														<span class="menu-link">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Roles</span>
															<span class="menu-arrow"></span>
														</span>
														<!--end:Menu link-->
														<!--begin:Menu sub-->
														<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/user-management/roles/list.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">Roles List</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
															<!--begin:Menu item-->
															<div class="menu-item">
																<!--begin:Menu link-->
																<a class="menu-link" href="#apps/user-management/roles/view.html">
																	<span class="menu-bullet">
																		<span class="bullet bullet-dot"></span>
																	</span>
																	<span class="menu-title">View Roles</span>
																</a>
																<!--end:Menu link-->
															</div>
															<!--end:Menu item-->
														</div>
														<!--end:Menu sub-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
                                                        <a class="menu-link {{ Route::currentRouteName() == 'company.index' ? 'active' : '' }}" href="{{ route('company.index') }}">
                                                        	<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Organismo</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
												</div>
												<!--end:Menu sub-->
                                                @endrole
											</div>
											<!--end:Menu item-->
											<!--begin:Menu item-->
											<div data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item menu-lg-down-accordion">
												<!--begin:Menu link-->
												<span class="menu-link">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/electronics/elc002.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M13.625 22H9.625V3C9.625 2.4 10.025 2 10.625 2H12.625C13.225 2 13.625 2.4 13.625 3V22Z" fill="currentColor"/>
                                                                <path d="M19.625 10H12.625V4H19.625L21.025 6.09998C21.325 6.59998 21.325 7.30005 21.025 7.80005L19.625 10Z" fill="currentColor"/>
                                                                <path d="M3.62499 16H10.625V10H3.62499L2.225 12.1001C1.925 12.6001 1.925 13.3 2.225 13.8L3.62499 16Z" fill="currentColor"/>
                                                                </svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Patrimônio</span>
													<span class="menu-arrow"></span>
												</span>
												<!--end:Menu link-->
												<!--begin:Menu sub-->
												<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown menu-active-bg px-lg-2 py-lg-4 w-lg-225px">
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
                                                        <a class="menu-link {{ Route::currentRouteName() == 'patrimonio.index' ? 'active' : '' }}" href="{{ route('patrimonio.index') }}">
                                                            <span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Módulo Património</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" href="#apps/contacts/add-contact.html">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Add Contact</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" {{ Request::is('patrimonios/imoveis') ? 'active' : '' }} href="{{ route('patrimonio.imoveis') }}">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Imóveis</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
													<!--begin:Menu item-->
													<div class="menu-item">
														<!--begin:Menu link-->
														<a class="menu-link" {{ Request::is('patrimonio/create') ? 'active' : '' }} href="{{ route('patrimonio.create') }}">
															<span class="menu-bullet">
																<span class="bullet bullet-dot"></span>
															</span>
															<span class="menu-title">Acessórios</span>
														</a>
														<!--end:Menu link-->
													</div>
													<!--end:Menu item-->
												</div>
												<!--end:Menu sub-->
											</div>
											<!--end:Menu item-->
										</div>
										<!--end:Menu sub-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item ">
										<!--begin:Menu link-->
										<span class="menu-link">
											<span class="menu-title">Layouts</span>
											<span class="menu-arrow d-lg-none"></span>
										</span>
										<!--end:Menu link-->
										<!--begin:Menu sub-->
										<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown p-0 w-100 w-lg-500px">
											<!--begin:Dashboards menu-->
											<div class="menu-state-bg pt-1 pb-3 px-3 py-lg-6 px-lg-6" data-kt-menu-dismiss="true">
												<!--begin:Row-->
												<div class="row">
													<!--begin:Col-->
													<div class="col-lg-5 mb-3 pt-2">
														<!--begin:Menu item-->
														<div class="menu-item p-0 m-0">
															<!--begin:Menu link-->
															<a href="#light-sidebar.html" class="menu-link">
																<span class="menu-bullet">
																	<span class="bullet bullet-dot h-6px w-6px"></span>
																</span>
																<span class="menu-title">Light Sidebar</span>
															</a>
															<!--end:Menu link-->
														</div>
														<!--end:Menu item-->
														<!--begin:Menu item-->
														<div class="menu-item p-0 m-0">
															<!--begin:Menu link-->
															<a href="#dark-sidebar.html" class="menu-link">
																<span class="menu-bullet">
																	<span class="bullet bullet-dot h-6px w-6px"></span>
																</span>
																<span class="menu-title">Dark Sidebar</span>
															</a>
															<!--end:Menu link-->
														</div>
														<!--end:Menu item-->
														<!--begin:Menu item-->
														<div class="menu-item p-0 m-0">
															<!--begin:Menu link-->
															<a href="#light-header.html" class="menu-link active">
																<span class="menu-bullet">
																	<span class="bullet bullet-dot h-6px w-6px"></span>
																</span>
																<span class="menu-title">Light Header</span>
															</a>
															<!--end:Menu link-->
														</div>
														<!--end:Menu item-->
														<!--begin:Menu item-->
														<div class="menu-item p-0 m-0">
															<!--begin:Menu link-->
															<a href="#dark-header.html" class="menu-link">
																<span class="menu-bullet">
																	<span class="bullet bullet-dot h-6px w-6px"></span>
																</span>
																<span class="menu-title">Dark Header</span>
															</a>
															<!--end:Menu link-->
														</div>
														<!--end:Menu item-->
													</div>
													<!--end:Col-->
													<!--begin:Col-->
													<div class="col-lg-7 mb-3 pt-2 pe-lg-8">
														<img src="/assets/media/stock/900x600/74.jpg" class="rounded mw-100" alt="" />
													</div>
													<!--end:Col-->
												</div>
												<!--end:Row-->
												<div class="separator separator-dashed mx-lg-5 my-4"></div>
												<!--begin:Landing-->
												<div class="d-flex flex-stack flex-wrap flex-lg-nowrap gap-2 mx-lg-5">
													<div class="d-flex flex-column me-5">
														<div class="fs-6 fw-bold text-gray-800">Layout Builder</div>
														<div class="fs-7 fw-semibold text-muted">Customize, preview and export</div>
													</div>
													<a href="https://preview.keenthemes.com/keen/demo1/layout-builder.html" class="btn btn-sm btn-primary fw-bold">Try Builder</a>
												</div>
												<!--end:Landing-->
											</div>
											<!--end:Dashboards menu-->
										</div>
										<!--end:Menu sub-->
									</div>
									<!--end:Menu item-->
									<!--begin:Menu item-->
									<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
										<!--begin:Menu link-->
										<span class="menu-link">
											<span class="menu-title">Configuração</span>
											<span class="menu-arrow d-lg-none"></span>
										</span>
										<!--end:Menu link-->
										<!--begin:Menu sub-->
										<div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
											<!--begin:Menu item-->
											<div class="menu-item">
												<!--begin:Menu link-->
												<a class="menu-link" href="https://preview.keenthemes.com/html/keen/docs/base/utilities" target="_blank" title="Check out over 200 in-house components, plugins and ready for use solutions" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/general/gen002.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M4.05424 15.1982C8.34524 7.76818 13.5782 3.26318 20.9282 2.01418C21.0729 1.98837 21.2216 1.99789 21.3618 2.04193C21.502 2.08597 21.6294 2.16323 21.7333 2.26712C21.8372 2.37101 21.9144 2.49846 21.9585 2.63863C22.0025 2.7788 22.012 2.92754 21.9862 3.07218C20.7372 10.4222 16.2322 15.6552 8.80224 19.9462L4.05424 15.1982ZM3.81924 17.3372L2.63324 20.4482C2.58427 20.5765 2.5735 20.7163 2.6022 20.8507C2.63091 20.9851 2.69788 21.1082 2.79503 21.2054C2.89218 21.3025 3.01536 21.3695 3.14972 21.3982C3.28408 21.4269 3.42387 21.4161 3.55224 21.3672L6.66524 20.1802L3.81924 17.3372ZM16.5002 5.99818C16.2036 5.99818 15.9136 6.08615 15.6669 6.25097C15.4202 6.41579 15.228 6.65006 15.1144 6.92415C15.0009 7.19824 14.9712 7.49984 15.0291 7.79081C15.0869 8.08178 15.2298 8.34906 15.4396 8.55884C15.6494 8.76862 15.9166 8.91148 16.2076 8.96935C16.4986 9.02723 16.8002 8.99753 17.0743 8.884C17.3484 8.77046 17.5826 8.5782 17.7474 8.33153C17.9123 8.08486 18.0002 7.79485 18.0002 7.49818C18.0002 7.10035 17.8422 6.71882 17.5609 6.43752C17.2796 6.15621 16.8981 5.99818 16.5002 5.99818Z" fill="currentColor" />
																<path d="M4.05423 15.1982L2.24723 13.3912C2.15505 13.299 2.08547 13.1867 2.04395 13.0632C2.00243 12.9396 1.9901 12.8081 2.00793 12.679C2.02575 12.5498 2.07325 12.4266 2.14669 12.3189C2.22013 12.2112 2.31752 12.1219 2.43123 12.0582L9.15323 8.28918C7.17353 10.3717 5.4607 12.6926 4.05423 15.1982ZM8.80023 19.9442L10.6072 21.7512C10.6994 21.8434 10.8117 21.9129 10.9352 21.9545C11.0588 21.996 11.1903 22.0083 11.3195 21.9905C11.4486 21.9727 11.5718 21.9252 11.6795 21.8517C11.7872 21.7783 11.8765 21.6809 11.9402 21.5672L15.7092 14.8442C13.6269 16.8245 11.3061 18.5377 8.80023 19.9442ZM7.04023 18.1832L12.5832 12.6402C12.7381 12.4759 12.8228 12.2577 12.8195 12.032C12.8161 11.8063 12.725 11.5907 12.5653 11.4311C12.4057 11.2714 12.1901 11.1803 11.9644 11.1769C11.7387 11.1736 11.5205 11.2583 11.3562 11.4132L5.81323 16.9562L7.04023 18.1832Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Components</span>
												</a>
												<!--end:Menu link-->
											</div>
											<!--end:Menu item-->
											<!--begin:Menu item-->
											<div class="menu-item">
												<!--begin:Menu link-->
												<a class="menu-link" href="https://preview.keenthemes.com/html/keen/docs" target="_blank" title="Check out the complete documentation" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-dismiss="click" data-bs-placement="right">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/abstract/abs027.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M21.25 18.525L13.05 21.825C12.35 22.125 11.65 22.125 10.95 21.825L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.125C22.25 12.725 22.25 11.325 21.25 10.925L13.05 7.62502C12.35 7.32502 11.65 7.32502 10.95 7.62502L2.75 10.925C1.75 11.325 1.75 12.725 2.75 13.125L10.95 16.425C11.65 16.725 12.45 16.725 13.05 16.425Z" fill="currentColor" />
																<path d="M11.05 11.025L2.84998 7.725C1.84998 7.325 1.84998 5.925 2.84998 5.525L11.05 2.225C11.75 1.925 12.45 1.925 13.15 2.225L21.35 5.525C22.35 5.925 22.35 7.325 21.35 7.725L13.05 11.025C12.45 11.325 11.65 11.325 11.05 11.025Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Documentation</span>
												</a>
												<!--end:Menu link-->
											</div>
											<!--end:Menu item-->
											<!--begin:Menu item-->
											<div class="menu-item">
												<!--begin:Menu link-->
												<a class="menu-link" href="https://preview.keenthemes.com/html/keen/docs/getting-started/changelog" target="_blank">
													<span class="menu-icon">
														<!--begin::Svg Icon | path: icons/duotune/coding/cod003.svg-->
														<span class="svg-icon svg-icon-3">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path d="M16.95 18.9688C16.75 18.9688 16.55 18.8688 16.35 18.7688C15.85 18.4688 15.75 17.8688 16.05 17.3688L19.65 11.9688L16.05 6.56876C15.75 6.06876 15.85 5.46873 16.35 5.16873C16.85 4.86873 17.45 4.96878 17.75 5.46878L21.75 11.4688C21.95 11.7688 21.95 12.2688 21.75 12.5688L17.75 18.5688C17.55 18.7688 17.25 18.9688 16.95 18.9688ZM7.55001 18.7688C8.05001 18.4688 8.15 17.8688 7.85 17.3688L4.25001 11.9688L7.85 6.56876C8.15 6.06876 8.05001 5.46873 7.55001 5.16873C7.05001 4.86873 6.45 4.96878 6.15 5.46878L2.15 11.4688C1.95 11.7688 1.95 12.2688 2.15 12.5688L6.15 18.5688C6.35 18.8688 6.65 18.9688 6.95 18.9688C7.15 18.9688 7.35001 18.8688 7.55001 18.7688Z" fill="currentColor" />
																<path opacity="0.3" d="M10.45 18.9687C10.35 18.9687 10.25 18.9687 10.25 18.9687C9.75 18.8687 9.35 18.2688 9.55 17.7688L12.55 5.76878C12.65 5.26878 13.25 4.8687 13.75 5.0687C14.25 5.1687 14.65 5.76878 14.45 6.26878L11.45 18.2688C11.35 18.6688 10.85 18.9687 10.45 18.9687Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<span class="menu-title">Changelog v3.0.3</span>
												</a>
												<!--end:Menu link-->
											</div>
											<!--end:Menu item-->
										</div>
										<!--end:Menu sub-->
									</div>
									<!--end:Menu item-->
								</div>
								<!--end::Menu-->
							</div>
							<!--end::Menu wrapper-->
							<!--begin::Navbar-->
							<div class="app-navbar flex-shrink-0">
								<!--begin::Search-->
								<div class="app-navbar-item align-items-stretch ms-1 ms-lg-3">
									<!--begin::Search-->
									<div id="kt_header_search" class="header-search d-flex align-items-stretch" data-kt-search-keypress="true" data-kt-search-min-length="2" data-kt-search-enter="enter" data-kt-search-layout="menu" data-kt-menu-trigger="auto" data-kt-menu-overflow="false" data-kt-menu-permanent="true" data-kt-menu-placement="bottom-end">
										<!--begin::Search toggle-->
										<div class="d-flex align-items-center" data-kt-search-element="toggle" id="kt_header_search_toggle">
											<div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px">
												<!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
												<span class="svg-icon svg-icon-1">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
														<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->
											</div>
										</div>
										<!--end::Search toggle-->
										<!--begin::Menu-->
										<div data-kt-search-element="content" class="menu menu-sub menu-sub-dropdown p-7 w-325px w-md-375px">
											<!--begin::Wrapper-->
											<div data-kt-search-element="wrapper">
												<!--begin::Form-->
												<form data-kt-search-element="form" class="w-100 position-relative mb-3" autocomplete="off">
													<!--begin::Icon-->
													<!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
													<span class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 translate-middle-y ms-0">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
															<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->
													<!--end::Icon-->
													<!--begin::Input-->
													<input type="text" class="search-input form-control form-control-flush ps-10" name="search" value="" placeholder="Search..." data-kt-search-element="input" />
													<!--end::Input-->
													<!--begin::Spinner-->
													<span class="search-spinner position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-1" data-kt-search-element="spinner">
														<span class="spinner-border h-15px w-15px align-middle text-gray-400"></span>
													</span>
													<!--end::Spinner-->
													<!--begin::Reset-->
													<span class="search-reset btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none" data-kt-search-element="clear">
														<!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
														<span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
																<rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
													<!--end::Reset-->
													<!--begin::Toolbar-->
													<div class="position-absolute top-50 end-0 translate-middle-y" data-kt-search-element="toolbar">
														<!--begin::Preferences toggle-->
														<div data-kt-search-element="preferences-show" class="btn btn-icon w-20px btn-sm btn-active-color-primary me-1" data-bs-toggle="tooltip" title="Show search preferences">
															<!--begin::Svg Icon | path: icons/duotune/coding/cod001.svg-->
															<span class="svg-icon svg-icon-1">
																<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path opacity="0.3" d="M22.1 11.5V12.6C22.1 13.2 21.7 13.6 21.2 13.7L19.9 13.9C19.7 14.7 19.4 15.5 18.9 16.2L19.7 17.2999C20 17.6999 20 18.3999 19.6 18.7999L18.8 19.6C18.4 20 17.8 20 17.3 19.7L16.2 18.9C15.5 19.3 14.7 19.7 13.9 19.9L13.7 21.2C13.6 21.7 13.1 22.1 12.6 22.1H11.5C10.9 22.1 10.5 21.7 10.4 21.2L10.2 19.9C9.4 19.7 8.6 19.4 7.9 18.9L6.8 19.7C6.4 20 5.7 20 5.3 19.6L4.5 18.7999C4.1 18.3999 4.1 17.7999 4.4 17.2999L5.2 16.2C4.8 15.5 4.4 14.7 4.2 13.9L2.9 13.7C2.4 13.6 2 13.1 2 12.6V11.5C2 10.9 2.4 10.5 2.9 10.4L4.2 10.2C4.4 9.39995 4.7 8.60002 5.2 7.90002L4.4 6.79993C4.1 6.39993 4.1 5.69993 4.5 5.29993L5.3 4.5C5.7 4.1 6.3 4.10002 6.8 4.40002L7.9 5.19995C8.6 4.79995 9.4 4.39995 10.2 4.19995L10.4 2.90002C10.5 2.40002 11 2 11.5 2H12.6C13.2 2 13.6 2.40002 13.7 2.90002L13.9 4.19995C14.7 4.39995 15.5 4.69995 16.2 5.19995L17.3 4.40002C17.7 4.10002 18.4 4.1 18.8 4.5L19.6 5.29993C20 5.69993 20 6.29993 19.7 6.79993L18.9 7.90002C19.3 8.60002 19.7 9.39995 19.9 10.2L21.2 10.4C21.7 10.5 22.1 11 22.1 11.5ZM12.1 8.59998C10.2 8.59998 8.6 10.2 8.6 12.1C8.6 14 10.2 15.6 12.1 15.6C14 15.6 15.6 14 15.6 12.1C15.6 10.2 14 8.59998 12.1 8.59998Z" fill="currentColor" />
																	<path d="M17.1 12.1C17.1 14.9 14.9 17.1 12.1 17.1C9.30001 17.1 7.10001 14.9 7.10001 12.1C7.10001 9.29998 9.30001 7.09998 12.1 7.09998C14.9 7.09998 17.1 9.29998 17.1 12.1ZM12.1 10.1C11 10.1 10.1 11 10.1 12.1C10.1 13.2 11 14.1 12.1 14.1C13.2 14.1 14.1 13.2 14.1 12.1C14.1 11 13.2 10.1 12.1 10.1Z" fill="currentColor" />
																</svg>
															</span>
															<!--end::Svg Icon-->
														</div>
														<!--end::Preferences toggle-->
														<!--begin::Advanced search toggle-->
														<div data-kt-search-element="advanced-options-form-show" class="btn btn-icon w-20px btn-sm btn-active-color-primary" data-bs-toggle="tooltip" title="Show more search options">
															<!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
															<span class="svg-icon svg-icon-2">
																<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																	<path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
																</svg>
															</span>
															<!--end::Svg Icon-->
														</div>
														<!--end::Advanced search toggle-->
													</div>
													<!--end::Toolbar-->
												</form>
												<!--end::Form-->
												<!--begin::Separator-->
												<div class="separator border-gray-200 mb-6"></div>
												<!--end::Separator-->
												<!--begin::Recently viewed-->
												<div data-kt-search-element="results" class="d-none">
													<!--begin::Items-->
													<div class="scroll-y mh-200px mh-lg-350px">
														<!--begin::Category title-->
														<h3 class="fs-5 text-muted m-0 pb-5" data-kt-search-element="category-title">Users</h3>
														<!--end::Category title-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<img src="/assets/media/avatars/300-6.jpg" alt="" />
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Karina Clark</span>
																<span class="fs-7 fw-semibold text-muted">Marketing Manager</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<img src="/assets/media/avatars/300-2.jpg" alt="" />
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Olivia Bold</span>
																<span class="fs-7 fw-semibold text-muted">Software Engineer</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<img src="/assets/media/avatars/300-9.jpg" alt="" />
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Ana Clark</span>
																<span class="fs-7 fw-semibold text-muted">UI/UX Designer</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<img src="/assets/media/avatars/300-14.jpg" alt="" />
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Nick Pitola</span>
																<span class="fs-7 fw-semibold text-muted">Art Director</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<img src="/assets/media/avatars/300-11.jpg" alt="" />
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Edward Kulnic</span>
																<span class="fs-7 fw-semibold text-muted">System Administrator</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Category title-->
														<h3 class="fs-5 text-muted m-0 pt-5 pb-5" data-kt-search-element="category-title">Customers</h3>
														<!--end::Category title-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<img class="w-20px h-20px" src="/assets/media/svg/brand-logos/volicity-9.svg" alt="" />
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Company Rbranding</span>
																<span class="fs-7 fw-semibold text-muted">UI Design</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<img class="w-20px h-20px" src="/assets/media/svg/brand-logos/tvit.svg" alt="" />
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Company Re-branding</span>
																<span class="fs-7 fw-semibold text-muted">Web Development</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<img class="w-20px h-20px" src="/assets/media/svg/misc/infography.svg" alt="" />
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Business Analytics App</span>
																<span class="fs-7 fw-semibold text-muted">Administration</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<img class="w-20px h-20px" src="/assets/media/svg/brand-logos/leaf.svg" alt="" />
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">EcoLeaf App Launch</span>
																<span class="fs-7 fw-semibold text-muted">Marketing</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<img class="w-20px h-20px" src="/assets/media/svg/brand-logos/tower.svg" alt="" />
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column justify-content-start fw-semibold">
																<span class="fs-6 fw-semibold">Tower Group Website</span>
																<span class="fs-7 fw-semibold text-muted">Google Adwords</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Category title-->
														<h3 class="fs-5 text-muted m-0 pt-5 pb-5" data-kt-search-element="category-title">Projects</h3>
														<!--end::Category title-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/general/gen005.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM12.5 18C12.5 17.4 12.6 17.5 12 17.5H8.5C7.9 17.5 8 17.4 8 18C8 18.6 7.9 18.5 8.5 18.5L12 18C12.6 18 12.5 18.6 12.5 18ZM16.5 13C16.5 12.4 16.6 12.5 16 12.5H8.5C7.9 12.5 8 12.4 8 13C8 13.6 7.9 13.5 8.5 13.5H15.5C16.1 13.5 16.5 13.6 16.5 13ZM12.5 8C12.5 7.4 12.6 7.5 12 7.5H8C7.4 7.5 7.5 7.4 7.5 8C7.5 8.6 7.4 8.5 8 8.5H12C12.6 8.5 12.5 8.6 12.5 8Z" fill="currentColor" />
																			<rect x="7" y="17" width="6" height="2" rx="1" fill="currentColor" />
																			<rect x="7" y="12" width="10" height="2" rx="1" fill="currentColor" />
																			<rect x="7" y="7" width="6" height="2" rx="1" fill="currentColor" />
																			<path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<span class="fs-6 fw-semibold">Si-Fi Project by AU Themes</span>
																<span class="fs-7 fw-semibold text-muted">#45670</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect x="8" y="9" width="3" height="10" rx="1.5" fill="currentColor" />
																			<rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="currentColor" />
																			<rect x="18" y="11" width="3" height="8" rx="1.5" fill="currentColor" />
																			<rect x="3" y="13" width="3" height="6" rx="1.5" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<span class="fs-6 fw-semibold">Shopix Mobile App Planning</span>
																<span class="fs-7 fw-semibold text-muted">#45690</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/communication/com012.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor" />
																			<rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor" />
																			<rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<span class="fs-6 fw-semibold">Finance Monitoring SAAS Discussion</span>
																<span class="fs-7 fw-semibold text-muted">#21090</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
														<!--begin::Item-->
														<a href="#" class="d-flex text-dark text-hover-primary align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/communication/com006.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M16.5 9C16.5 13.125 13.125 16.5 9 16.5C4.875 16.5 1.5 13.125 1.5 9C1.5 4.875 4.875 1.5 9 1.5C13.125 1.5 16.5 4.875 16.5 9Z" fill="currentColor" />
																			<path d="M9 16.5C10.95 16.5 12.75 15.75 14.025 14.55C13.425 12.675 11.4 11.25 9 11.25C6.6 11.25 4.57499 12.675 3.97499 14.55C5.24999 15.75 7.05 16.5 9 16.5Z" fill="currentColor" />
																			<rect x="7" y="6" width="4" height="4" rx="2" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<span class="fs-6 fw-semibold">Dashboard Analitics Launch</span>
																<span class="fs-7 fw-semibold text-muted">#34560</span>
															</div>
															<!--end::Title-->
														</a>
														<!--end::Item-->
													</div>
													<!--end::Items-->
												</div>
												<!--end::Recently viewed-->
												<!--begin::Recently viewed-->
												<div class="mb-5" data-kt-search-element="main">
													<!--begin::Heading-->
													<div class="d-flex flex-stack fw-semibold mb-4">
														<!--begin::Label-->
														<span class="text-muted fs-6 me-2">Recently Searched:</span>
														<!--end::Label-->
													</div>
													<!--end::Heading-->
													<!--begin::Items-->
													<div class="scroll-y mh-200px mh-lg-325px">
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/electronics/elc004.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M2 16C2 16.6 2.4 17 3 17H21C21.6 17 22 16.6 22 16V15H2V16Z" fill="currentColor" />
																			<path opacity="0.3" d="M21 3H3C2.4 3 2 3.4 2 4V15H22V4C22 3.4 21.6 3 21 3Z" fill="currentColor" />
																			<path opacity="0.3" d="M15 17H9V20H15V17Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">BoomApp by Keenthemes</a>
																<span class="fs-7 text-muted fw-semibold">#45789</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/graphs/gra001.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M14 3V21H10V3C10 2.4 10.4 2 11 2H13C13.6 2 14 2.4 14 3ZM7 14H5C4.4 14 4 14.4 4 15V21H8V15C8 14.4 7.6 14 7 14Z" fill="currentColor" />
																			<path d="M21 20H20V8C20 7.4 19.6 7 19 7H17C16.4 7 16 7.4 16 8V20H3C2.4 20 2 20.4 2 21C2 21.6 2.4 22 3 22H21C21.6 22 22 21.6 22 21C22 20.4 21.6 20 21 20Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">"Kept API Project Meeting</a>
																<span class="fs-7 text-muted fw-semibold">#84050</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/graphs/gra006.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M13 5.91517C15.8 6.41517 18 8.81519 18 11.8152C18 12.5152 17.9 13.2152 17.6 13.9152L20.1 15.3152C20.6 15.6152 21.4 15.4152 21.6 14.8152C21.9 13.9152 22.1 12.9152 22.1 11.8152C22.1 7.01519 18.8 3.11521 14.3 2.01521C13.7 1.91521 13.1 2.31521 13.1 3.01521V5.91517H13Z" fill="currentColor" />
																			<path opacity="0.3" d="M19.1 17.0152C19.7 17.3152 19.8 18.1152 19.3 18.5152C17.5 20.5152 14.9 21.7152 12 21.7152C9.1 21.7152 6.50001 20.5152 4.70001 18.5152C4.30001 18.0152 4.39999 17.3152 4.89999 17.0152L7.39999 15.6152C8.49999 16.9152 10.2 17.8152 12 17.8152C13.8 17.8152 15.5 17.0152 16.6 15.6152L19.1 17.0152ZM6.39999 13.9151C6.19999 13.2151 6 12.5152 6 11.8152C6 8.81517 8.2 6.41515 11 5.91515V3.01519C11 2.41519 10.4 1.91519 9.79999 2.01519C5.29999 3.01519 2 7.01517 2 11.8152C2 12.8152 2.2 13.8152 2.5 14.8152C2.7 15.4152 3.4 15.7152 4 15.3152L6.39999 13.9151Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">"KPI Monitoring App Launch</a>
																<span class="fs-7 text-muted fw-semibold">#84250</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/graphs/gra002.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M20 8L12.5 5L5 14V19H20V8Z" fill="currentColor" />
																			<path d="M21 18H6V3C6 2.4 5.6 2 5 2C4.4 2 4 2.4 4 3V18H3C2.4 18 2 18.4 2 19C2 19.6 2.4 20 3 20H4V21C4 21.6 4.4 22 5 22C5.6 22 6 21.6 6 21V20H21C21.6 20 22 19.6 22 19C22 18.4 21.6 18 21 18Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">Project Reference FAQ</a>
																<span class="fs-7 text-muted fw-semibold">#67945</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/communication/com010.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M6 8.725C6 8.125 6.4 7.725 7 7.725H14L18 11.725V12.925L22 9.725L12.6 2.225C12.2 1.925 11.7 1.925 11.4 2.225L2 9.725L6 12.925V8.725Z" fill="currentColor" />
																			<path opacity="0.3" d="M22 9.72498V20.725C22 21.325 21.6 21.725 21 21.725H3C2.4 21.725 2 21.325 2 20.725V9.72498L11.4 17.225C11.8 17.525 12.3 17.525 12.6 17.225L22 9.72498ZM15 11.725H18L14 7.72498V10.725C14 11.325 14.4 11.725 15 11.725Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">"FitPro App Development</a>
																<span class="fs-7 text-muted fw-semibold">#84250</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="currentColor" />
																			<path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">Shopix Mobile App</a>
																<span class="fs-7 text-muted fw-semibold">#45690</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
														<!--begin::Item-->
														<div class="d-flex align-items-center mb-5">
															<!--begin::Symbol-->
															<div class="symbol symbol-40px me-4">
																<span class="symbol-label bg-light">
																	<!--begin::Svg Icon | path: icons/duotune/graphs/gra002.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M20 8L12.5 5L5 14V19H20V8Z" fill="currentColor" />
																			<path d="M21 18H6V3C6 2.4 5.6 2 5 2C4.4 2 4 2.4 4 3V18H3C2.4 18 2 18.4 2 19C2 19.6 2.4 20 3 20H4V21C4 21.6 4.4 22 5 22C5.6 22 6 21.6 6 21V20H21C21.6 20 22 19.6 22 19C22 18.4 21.6 18 21 18Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="d-flex flex-column">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-semibold">"Landing UI Design" Launch</a>
																<span class="fs-7 text-muted fw-semibold">#24005</span>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Item-->
													</div>
													<!--end::Items-->
												</div>
												<!--end::Recently viewed-->
												<!--begin::Empty-->
												<div data-kt-search-element="empty" class="text-center d-none">
													<!--begin::Icon-->
													<div class="pt-10 pb-10">
														<!--begin::Svg Icon | path: icons/duotune/files/fil024.svg-->
														<span class="svg-icon svg-icon-4x opacity-50">
															<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																<path opacity="0.3" d="M14 2H6C4.89543 2 4 2.89543 4 4V20C4 21.1046 4.89543 22 6 22H18C19.1046 22 20 21.1046 20 20V8L14 2Z" fill="currentColor" />
																<path d="M20 8L14 2V6C14 7.10457 14.8954 8 16 8H20Z" fill="currentColor" />
																<rect x="13.6993" y="13.6656" width="4.42828" height="1.73089" rx="0.865447" transform="rotate(45 13.6993 13.6656)" fill="currentColor" />
																<path d="M15 12C15 14.2 13.2 16 11 16C8.8 16 7 14.2 7 12C7 9.8 8.8 8 11 8C13.2 8 15 9.8 15 12ZM11 9.6C9.68 9.6 8.6 10.68 8.6 12C8.6 13.32 9.68 14.4 11 14.4C12.32 14.4 13.4 13.32 13.4 12C13.4 10.68 12.32 9.6 11 9.6Z" fill="currentColor" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</div>
													<!--end::Icon-->
													<!--begin::Message-->
													<div class="pb-15 fw-semibold">
														<h3 class="text-gray-600 fs-5 mb-2">No result found</h3>
														<div class="text-muted fs-7">Please try again with a different query</div>
													</div>
													<!--end::Message-->
												</div>
												<!--end::Empty-->
											</div>
											<!--end::Wrapper-->
											<!--begin::Preferences-->
											<form data-kt-search-element="advanced-options-form" class="pt-1 d-none">
												<!--begin::Heading-->
												<h3 class="fw-semibold text-dark mb-7">Advanced Search</h3>
												<!--end::Heading-->
												<!--begin::Input group-->
												<div class="mb-5">
													<input type="text" class="form-control form-control-sm form-control-solid" placeholder="Contains the word" name="query" />
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="mb-5">
													<!--begin::Radio group-->
													<div class="nav-group nav-group-fluid">
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="type" value="has" checked="checked" />
															<span class="btn btn-sm btn-color-muted btn-active btn-active-primary">All</span>
														</label>
														<!--end::Option-->
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="type" value="users" />
															<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">Users</span>
														</label>
														<!--end::Option-->
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="type" value="orders" />
															<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">Orders</span>
														</label>
														<!--end::Option-->
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="type" value="projects" />
															<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">Projects</span>
														</label>
														<!--end::Option-->
													</div>
													<!--end::Radio group-->
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="mb-5">
													<input type="text" name="assignedto" class="form-control form-control-sm form-control-solid" placeholder="Assigned to" value="" />
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="mb-5">
													<input type="text" name="collaborators" class="form-control form-control-sm form-control-solid" placeholder="Collaborators" value="" />
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="mb-5">
													<!--begin::Radio group-->
													<div class="nav-group nav-group-fluid">
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="attachment" value="has" checked="checked" />
															<span class="btn btn-sm btn-color-muted btn-active btn-active-primary">Has attachment</span>
														</label>
														<!--end::Option-->
														<!--begin::Option-->
														<label>
															<input type="radio" class="btn-check" name="attachment" value="any" />
															<span class="btn btn-sm btn-color-muted btn-active btn-active-primary px-4">Any</span>
														</label>
														<!--end::Option-->
													</div>
													<!--end::Radio group-->
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="mb-5">
													<select name="timezone" aria-label="Select a Timezone" data-control="select2" data-placeholder="date_period" class="form-select form-select-sm form-select-solid">
														<option value="next">Within the next</option>
														<option value="last">Within the last</option>
														<option value="between">Between</option>
														<option value="on">On</option>
													</select>
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="row mb-8">
													<!--begin::Col-->
													<div class="col-6">
														<input type="number" name="date_number" class="form-control form-control-sm form-control-solid" placeholder="Lenght" value="" />
													</div>
													<!--end::Col-->
													<!--begin::Col-->
													<div class="col-6">
														<select name="date_typer" aria-label="Select a Timezone" data-control="select2" data-placeholder="Period" class="form-select form-select-sm form-select-solid">
															<option value="days">Days</option>
															<option value="weeks">Weeks</option>
															<option value="months">Months</option>
															<option value="years">Years</option>
														</select>
													</div>
													<!--end::Col-->
												</div>
												<!--end::Input group-->
												<!--begin::Actions-->
												<div class="d-flex justify-content-end">
													<button type="reset" class="btn btn-sm btn-light fw-bold btn-active-light-primary me-2" data-kt-search-element="advanced-options-form-cancel">Cancel</button>
													<a href="#pages/search/horizontal.html" class="btn btn-sm fw-bold btn-primary" data-kt-search-element="advanced-options-form-search">Search</a>
												</div>
												<!--end::Actions-->
											</form>
											<!--end::Preferences-->
											<!--begin::Preferences-->
											<form data-kt-search-element="preferences" class="pt-1 d-none">
												<!--begin::Heading-->
												<h3 class="fw-semibold text-dark mb-7">Search Preferences</h3>
												<!--end::Heading-->
												<!--begin::Input group-->
												<div class="pb-4 border-bottom">
													<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
														<span class="form-check-label text-gray-700 fs-6 fw-semibold ms-0 me-2">Projects</span>
														<input class="form-check-input" type="checkbox" value="1" checked="checked" />
													</label>
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="py-4 border-bottom">
													<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
														<span class="form-check-label text-gray-700 fs-6 fw-semibold ms-0 me-2">Targets</span>
														<input class="form-check-input" type="checkbox" value="1" checked="checked" />
													</label>
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="py-4 border-bottom">
													<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
														<span class="form-check-label text-gray-700 fs-6 fw-semibold ms-0 me-2">Affiliate Programs</span>
														<input class="form-check-input" type="checkbox" value="1" />
													</label>
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="py-4 border-bottom">
													<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
														<span class="form-check-label text-gray-700 fs-6 fw-semibold ms-0 me-2">Referrals</span>
														<input class="form-check-input" type="checkbox" value="1" checked="checked" />
													</label>
												</div>
												<!--end::Input group-->
												<!--begin::Input group-->
												<div class="py-4 border-bottom">
													<label class="form-check form-switch form-switch-sm form-check-custom form-check-solid flex-stack">
														<span class="form-check-label text-gray-700 fs-6 fw-semibold ms-0 me-2">Users</span>
														<input class="form-check-input" type="checkbox" value="1" />
													</label>
												</div>
												<!--end::Input group-->
												<!--begin::Actions-->
												<div class="d-flex justify-content-end pt-7">
													<button type="reset" class="btn btn-sm btn-light fw-bold btn-active-light-primary me-2" data-kt-search-element="preferences-dismiss">Cancel</button>
													<button type="submit" class="btn btn-sm fw-bold btn-primary">Save Changes</button>
												</div>
												<!--end::Actions-->
											</form>
											<!--end::Preferences-->
										</div>
										<!--end::Menu-->
									</div>
									<!--end::Search-->
								</div>
								<!--end::Search-->
								<!--begin::Activities-->
								<div class="app-navbar-item ms-1 ms-lg-3">
									<!--begin::Drawer toggle-->
									<div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px" id="kt_activities_toggle">
										<!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
										<span class="svg-icon svg-icon-1">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect x="8" y="9" width="3" height="10" rx="1.5" fill="currentColor" />
												<rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="currentColor" />
												<rect x="18" y="11" width="3" height="8" rx="1.5" fill="currentColor" />
												<rect x="3" y="13" width="3" height="6" rx="1.5" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</div>
									<!--end::Drawer toggle-->
								</div>
								<!--end::Activities-->
								<!--begin::Notifications-->
								<div class="app-navbar-item ms-1 ms-lg-3">
									<!--begin::Menu- wrapper-->
									<div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
										<!--begin::Svg Icon | path: icons/duotune/general/gen022.svg-->
										<span class="svg-icon svg-icon-1">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M11.2929 2.70711C11.6834 2.31658 12.3166 2.31658 12.7071 2.70711L15.2929 5.29289C15.6834 5.68342 15.6834 6.31658 15.2929 6.70711L12.7071 9.29289C12.3166 9.68342 11.6834 9.68342 11.2929 9.29289L8.70711 6.70711C8.31658 6.31658 8.31658 5.68342 8.70711 5.29289L11.2929 2.70711Z" fill="currentColor" />
												<path d="M11.2929 14.7071C11.6834 14.3166 12.3166 14.3166 12.7071 14.7071L15.2929 17.2929C15.6834 17.6834 15.6834 18.3166 15.2929 18.7071L12.7071 21.2929C12.3166 21.6834 11.6834 21.6834 11.2929 21.2929L8.70711 18.7071C8.31658 18.3166 8.31658 17.6834 8.70711 17.2929L11.2929 14.7071Z" fill="currentColor" />
												<path opacity="0.3" d="M5.29289 8.70711C5.68342 8.31658 6.31658 8.31658 6.70711 8.70711L9.29289 11.2929C9.68342 11.6834 9.68342 12.3166 9.29289 12.7071L6.70711 15.2929C6.31658 15.6834 5.68342 15.6834 5.29289 15.2929L2.70711 12.7071C2.31658 12.3166 2.31658 11.6834 2.70711 11.2929L5.29289 8.70711Z" fill="currentColor" />
												<path opacity="0.3" d="M17.2929 8.70711C17.6834 8.31658 18.3166 8.31658 18.7071 8.70711L21.2929 11.2929C21.6834 11.6834 21.6834 12.3166 21.2929 12.7071L18.7071 15.2929C18.3166 15.6834 17.6834 15.6834 17.2929 15.2929L14.7071 12.7071C14.3166 12.3166 14.3166 11.6834 14.7071 11.2929L17.2929 8.70711Z" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</div>
									<!--begin::Menu-->
									<div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
										<!--begin::Heading-->
										<div class="d-flex flex-column bgi-no-repeat rounded-top" style="background-image:url('/assets/media/misc/menu-header-bg.jpg')">
											<!--begin::Title-->
											<h3 class="text-white fw-semibold px-9 mt-10 mb-6">Notifications
											<span class="fs-8 opacity-75 ps-3">24 reports</span></h3>
											<!--end::Title-->
											<!--begin::Tabs-->
											<ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
												<li class="nav-item">
													<a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_1">Alerts</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_2">Updates</a>
												</li>
												<li class="nav-item">
													<a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab" href="#kt_topbar_notifications_3">Logs</a>
												</li>
											</ul>
											<!--end::Tabs-->
										</div>
										<!--end::Heading-->
										<!--begin::Tab content-->
										<div class="tab-content">
											<!--begin::Tab panel-->
											<div class="tab-pane fade" id="kt_topbar_notifications_1" role="tabpanel">
												<!--begin::Items-->
												<div class="scroll-y mh-325px my-5 px-8">
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-primary">
																	<!--begin::Svg Icon | path: icons/duotune/technology/teh008.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M11 6.5C11 9 9 11 6.5 11C4 11 2 9 2 6.5C2 4 4 2 6.5 2C9 2 11 4 11 6.5ZM17.5 2C15 2 13 4 13 6.5C13 9 15 11 17.5 11C20 11 22 9 22 6.5C22 4 20 2 17.5 2ZM6.5 13C4 13 2 15 2 17.5C2 20 4 22 6.5 22C9 22 11 20 11 17.5C11 15 9 13 6.5 13ZM17.5 13C15 13 13 15 13 17.5C13 20 15 22 17.5 22C20 22 22 20 22 17.5C22 15 20 13 17.5 13Z" fill="currentColor" />
																			<path d="M17.5 16C17.5 16 17.4 16 17.5 16L16.7 15.3C16.1 14.7 15.7 13.9 15.6 13.1C15.5 12.4 15.5 11.6 15.6 10.8C15.7 9.99999 16.1 9.19998 16.7 8.59998L17.4 7.90002H17.5C18.3 7.90002 19 7.20002 19 6.40002C19 5.60002 18.3 4.90002 17.5 4.90002C16.7 4.90002 16 5.60002 16 6.40002V6.5L15.3 7.20001C14.7 7.80001 13.9 8.19999 13.1 8.29999C12.4 8.39999 11.6 8.39999 10.8 8.29999C9.99999 8.19999 9.20001 7.80001 8.60001 7.20001L7.89999 6.5V6.40002C7.89999 5.60002 7.19999 4.90002 6.39999 4.90002C5.59999 4.90002 4.89999 5.60002 4.89999 6.40002C4.89999 7.20002 5.59999 7.90002 6.39999 7.90002H6.5L7.20001 8.59998C7.80001 9.19998 8.19999 9.99999 8.29999 10.8C8.39999 11.5 8.39999 12.3 8.29999 13.1C8.19999 13.9 7.80001 14.7 7.20001 15.3L6.5 16H6.39999C5.59999 16 4.89999 16.7 4.89999 17.5C4.89999 18.3 5.59999 19 6.39999 19C7.19999 19 7.89999 18.3 7.89999 17.5V17.4L8.60001 16.7C9.20001 16.1 9.99999 15.7 10.8 15.6C11.5 15.5 12.3 15.5 13.1 15.6C13.9 15.7 14.7 16.1 15.3 16.7L16 17.4V17.5C16 18.3 16.7 19 17.5 19C18.3 19 19 18.3 19 17.5C19 16.7 18.3 16 17.5 16Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Project Alice</a>
																<div class="text-gray-400 fs-7">Phase 1 development</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">1 hr</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-danger">
																	<!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-danger">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<rect opacity="0.3" x="2" y="2" width="20" height="20" rx="10" fill="currentColor" />
																			<rect x="11" y="14" width="7" height="2" rx="1" transform="rotate(-90 11 14)" fill="currentColor" />
																			<rect x="11" y="17" width="2" height="2" rx="1" transform="rotate(-90 11 17)" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">HR Confidential</a>
																<div class="text-gray-400 fs-7">Confidential staff documents</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">2 hrs</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-warning">
																	<!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-warning">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="currentColor" />
																			<path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Company HR</a>
																<div class="text-gray-400 fs-7">Corporeate staff profiles</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">5 hrs</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-success">
																	<!--begin::Svg Icon | path: icons/duotune/files/fil023.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-success">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M5 15C3.3 15 2 13.7 2 12C2 10.3 3.3 9 5 9H5.10001C5.00001 8.7 5 8.3 5 8C5 5.2 7.2 3 10 3C11.9 3 13.5 4 14.3 5.5C14.8 5.2 15.4 5 16 5C17.7 5 19 6.3 19 8C19 8.4 18.9 8.7 18.8 9C18.9 9 18.9 9 19 9C20.7 9 22 10.3 22 12C22 13.7 20.7 15 19 15H5ZM5 12.6H13L9.7 9.29999C9.3 8.89999 8.7 8.89999 8.3 9.29999L5 12.6Z" fill="currentColor" />
																			<path d="M17 17.4V12C17 11.4 16.6 11 16 11C15.4 11 15 11.4 15 12V17.4H17Z" fill="currentColor" />
																			<path opacity="0.3" d="M12 17.4H20L16.7 20.7C16.3 21.1 15.7 21.1 15.3 20.7L12 17.4Z" fill="currentColor" />
																			<path d="M8 12.6V18C8 18.6 8.4 19 9 19C9.6 19 10 18.6 10 18V12.6H8Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Project Redux</a>
																<div class="text-gray-400 fs-7">New frontend admin theme</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">2 days</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-primary">
																	<!--begin::Svg Icon | path: icons/duotune/maps/map001.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-primary">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M6 22H4V3C4 2.4 4.4 2 5 2C5.6 2 6 2.4 6 3V22Z" fill="currentColor" />
																			<path d="M18 14H4V4H18C18.8 4 19.2 4.9 18.7 5.5L16 9L18.8 12.5C19.3 13.1 18.8 14 18 14Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Project Breafing</a>
																<div class="text-gray-400 fs-7">Product launch status update</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">21 Jan</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-info">
																	<!--begin::Svg Icon | path: icons/duotune/general/gen006.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-info">
																		<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M22 5V19C22 19.6 21.6 20 21 20H19.5L11.9 12.4C11.5 12 10.9 12 10.5 12.4L3 20C2.5 20 2 19.5 2 19V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5ZM7.5 7C6.7 7 6 7.7 6 8.5C6 9.3 6.7 10 7.5 10C8.3 10 9 9.3 9 8.5C9 7.7 8.3 7 7.5 7Z" fill="currentColor" />
																			<path d="M19.1 10C18.7 9.60001 18.1 9.60001 17.7 10L10.7 17H2V19C2 19.6 2.4 20 3 20H21C21.6 20 22 19.6 22 19V12.9L19.1 10Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Banner Assets</a>
																<div class="text-gray-400 fs-7">Collection of banner images</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">21 Jan</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center">
															<!--begin::Symbol-->
															<div class="symbol symbol-35px me-4">
																<span class="symbol-label bg-light-warning">
																	<!--begin::Svg Icon | path: icons/duotune/art/art002.svg-->
																	<span class="svg-icon svg-icon-2 svg-icon-warning">
																		<svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
																			<path opacity="0.3" d="M8.9 21L7.19999 22.6999C6.79999 23.0999 6.2 23.0999 5.8 22.6999L4.1 21H8.9ZM4 16.0999L2.3 17.8C1.9 18.2 1.9 18.7999 2.3 19.1999L4 20.9V16.0999ZM19.3 9.1999L15.8 5.6999C15.4 5.2999 14.8 5.2999 14.4 5.6999L9 11.0999V21L19.3 10.6999C19.7 10.2999 19.7 9.5999 19.3 9.1999Z" fill="currentColor" />
																			<path d="M21 15V20C21 20.6 20.6 21 20 21H11.8L18.8 14H20C20.6 14 21 14.4 21 15ZM10 21V4C10 3.4 9.6 3 9 3H4C3.4 3 3 3.4 3 4V21C3 21.6 3.4 22 4 22H9C9.6 22 10 21.6 10 21ZM7.5 18.5C7.5 19.1 7.1 19.5 6.5 19.5C5.9 19.5 5.5 19.1 5.5 18.5C5.5 17.9 5.9 17.5 6.5 17.5C7.1 17.5 7.5 17.9 7.5 18.5Z" fill="currentColor" />
																		</svg>
																	</span>
																	<!--end::Svg Icon-->
																</span>
															</div>
															<!--end::Symbol-->
															<!--begin::Title-->
															<div class="mb-0 me-2">
																<a href="#" class="fs-6 text-gray-800 text-hover-primary fw-bold">Icon Assets</a>
																<div class="text-gray-400 fs-7">Collection of SVG icons</div>
															</div>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">20 March</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
												</div>
												<!--end::Items-->
												<!--begin::View more-->
												<div class="py-3 text-center border-top">
													<a href="#pages/user-profile/activity.html" class="btn btn-color-gray-600 btn-active-color-primary">View All
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
													<span class="svg-icon svg-icon-5">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
															<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon--></a>
												</div>
												<!--end::View more-->
											</div>
											<!--end::Tab panel-->
											<!--begin::Tab panel-->
											<div class="tab-pane fade show active" id="kt_topbar_notifications_2" role="tabpanel">
												<!--begin::Wrapper-->
												<div class="d-flex flex-column px-9">
													<!--begin::Section-->
													<div class="pt-10 pb-0">
														<!--begin::Title-->
														<h3 class="text-dark text-center fw-bold">Get Pro Access</h3>
														<!--end::Title-->
														<!--begin::Text-->
														<div class="text-center text-gray-600 fw-semibold pt-1">Outlines keep you honest. They stoping you from amazing poorly about drive</div>
														<!--end::Text-->
														<!--begin::Action-->
														<div class="text-center mt-5 mb-9">
															<a href="#" class="btn btn-sm btn-primary px-6" data-bs-toggle="modal" data-bs-target="#kt_modal_upgrade_plan">Upgrade</a>
														</div>
														<!--end::Action-->
													</div>
													<!--end::Section-->
													<!--begin::Illustration-->
													<div class="text-center px-4">
														<img class="mw-100 mh-200px" alt="image" src="/assets/media/illustrations/sketchy-1/1.png" />
													</div>
													<!--end::Illustration-->
												</div>
												<!--end::Wrapper-->
											</div>
											<!--end::Tab panel-->
											<!--begin::Tab panel-->
											<div class="tab-pane fade" id="kt_topbar_notifications_3" role="tabpanel">
												<!--begin::Items-->
												<div class="scroll-y mh-325px my-5 px-8">
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-success me-4">200 OK</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">New order</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Just now</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-danger me-4">500 ERR</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">New customer</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">2 hrs</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-success me-4">200 OK</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Payment process</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">5 hrs</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-warning me-4">300 WRN</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Search query</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">2 days</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-success me-4">200 OK</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">API connection</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">1 week</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-success me-4">200 OK</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Database restore</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Mar 5</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-warning me-4">300 WRN</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">System update</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">May 15</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-warning me-4">300 WRN</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Server OS update</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Apr 3</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-warning me-4">300 WRN</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">API rollback</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Jun 30</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-danger me-4">500 ERR</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Refund process</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Jul 10</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-danger me-4">500 ERR</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Withdrawal process</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Sep 10</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
													<!--begin::Item-->
													<div class="d-flex flex-stack py-4">
														<!--begin::Section-->
														<div class="d-flex align-items-center me-2">
															<!--begin::Code-->
															<span class="w-70px badge badge-light-danger me-4">500 ERR</span>
															<!--end::Code-->
															<!--begin::Title-->
															<a href="#" class="text-gray-800 text-hover-primary fw-semibold">Mail tasks</a>
															<!--end::Title-->
														</div>
														<!--end::Section-->
														<!--begin::Label-->
														<span class="badge badge-light fs-8">Dec 10</span>
														<!--end::Label-->
													</div>
													<!--end::Item-->
												</div>
												<!--end::Items-->
												<!--begin::View more-->
												<div class="py-3 text-center border-top">
													<a href="#pages/user-profile/activity.html" class="btn btn-color-gray-600 btn-active-color-primary">View All
													<!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
													<span class="svg-icon svg-icon-5">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
															<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon--></a>
												</div>
												<!--end::View more-->
											</div>
											<!--end::Tab panel-->
										</div>
										<!--end::Tab content-->
									</div>
									<!--end::Menu-->
									<!--end::Menu wrapper-->
								</div>
								<!--end::Notifications-->
								<!--begin::Chat-->
								<div class="app-navbar-item ms-1 ms-lg-3">
									<!--begin::Menu wrapper-->
									<div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative" id="kt_drawer_chat_toggle">
										<!--begin::Svg Icon | path: icons/duotune/communication/com012.svg-->
										<span class="svg-icon svg-icon-1">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path opacity="0.3" d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z" fill="currentColor" />
												<rect x="6" y="12" width="7" height="2" rx="1" fill="currentColor" />
												<rect x="6" y="7" width="12" height="2" rx="1" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->
										<span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle top-0 start-50 animation-blink"></span>
									</div>
									<!--end::Menu wrapper-->
								</div>
								<!--end::Chat-->
								<!--begin::Languages-->

								<!--end::Languages-->
																<!--begin::Theme mode-->
                                                                @include('app.layouts.dack')
                                                                <!--end::Theme mode-->
                                                                <!--begin::User menu-->
                                                                @include('app.layouts.userMenu')
                                                                <!--end::User menu-->
								<!--begin::Header menu toggle-->
								<div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
									<div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_header_menu_toggle">
										<!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
										<span class="svg-icon svg-icon-1">
											<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="currentColor" />
												<path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="currentColor" />
											</svg>
										</span>
										<!--end::Svg Icon-->
									</div>
								</div>
								<!--end::Header menu toggle-->
							</div>
							<!--end::Navbar-->
						</div>
						<!--end::Header wrapper-->
					</div>
					<!--end::Header container-->
				</div>
				<!--end::Header-->
                            				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                    <!--begin::Main-->
                    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                        <!--begin::Content wrapper-->
                        <div class="d-flex flex-column flex-column-fluid">
                            <!--begin::Toolbar-->

                            <!--end::Toolbar-->
                            <!--begin::Content-->
                            <div id="kt_app_content" class="app-content flex-column-fluid">
                                <!--begin::Content container-->
                                <!-- Page Heading -->
                                @isset($header)
                                    <header class="bg-white dark:bg-gray-800 shadow">
                                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                            {{ $header }}
                                        </div>
                                    </header>
                                @endisset

                                <!-- Page Content -->
                                <main>
                                    {{ $slot }}
                                </main>

                                <!--end::Content container-->
                            </div>
                            <!--end::Content-->
                        </div>
                        <!--end::Content wrapper-->
                        <!--begin::Footer-->
                        @include('components.footer')
                        <!--end::Footer-->
                    </div>
                    <!--end:::Main-->
                    </div>

