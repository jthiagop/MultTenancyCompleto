         <!--begin::Navbar-->
         <div class="card mb-6 mb-xl-9">
             <div class="card-body pt-9 pb-0">
                 <!--begin::Details-->
                 <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                     <!--begin::Image-->
                     <div
                         class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                         <img class="mw-50px mw-lg-75px" src="/assets/media/svg/icons/patrimonio-home.svg"
                             alt="image" />
                     </div>
                     <!--end::Image-->
                     <!--begin::Wrapper-->
                     <div class="flex-grow-1">
                         <!--begin::Head-->
                         <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                             <!--begin::Details-->
                             <div class="d-flex flex-column">
                                 <!--begin::Status-->
                                 <div class="d-flex align-items-center mb-1">
                                     <a href="#"
                                         class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">Patrimônio</a>
                                     <span class="badge badge-light-success me-auto">Em Desenvolvimento</span>
                                 </div>
                                 <!--end::Status-->
                                 <!--begin::Description-->
                                 <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">Gestão de bens
                                     imóveis, controle de foro e laudêmio </div>
                                 <!--end::Description-->
                             </div>
                             <!--end::Details-->
                             <!--begin::Actions-->
                             <div class="d-flex mb-4">
                                     @if (Request::is('patrimonios/imoveis'))
                                     <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                     data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-2" viewBox="0 0 16 16" style="vertical-align: middle;">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                      </svg>
                                      Pesquisar...</a>
                                     <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_new_foro">
                                        <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm002.svg-->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-add-fill me-2" viewBox="0 0 16 16" style="vertical-align: middle;">
                                            <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 1 1-1 0v-1h-1a.5.5 0 1 1 0-1h1v-1a.5.5 0 0 1 1 0"/>
                                            <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                                            <path d="m8 3.293 4.712 4.712A4.5 4.5 0 0 0 8.758 15H3.5A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                                        </svg>
                                        <!--end::Svg Icon-->
                                        Novo Ímovel
                                    </a>
                                    @elseif (Request::is('patrimonio'))
                                    <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                    data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-2" viewBox="0 0 16 16" style="vertical-align: middle;">
                                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                                      </svg>
                                    Pesquisar...</a>
                                    <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_new_foro">
                                    <!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm002.svg-->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-add me-2" viewBox="0 0 16 16" style="vertical-align: middle;">
                                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0"/>
                                        <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6.5a.5.5 0 0 1-1 0V1H3v14h3v-2.5a.5.5 0 0 1 .5-.5H8v4H3a1 1 0 0 1-1-1z"/>
                                        <path d="M4.5 2a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm-6 3a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm3 0a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
                                      </svg>
                                    <!--end::Svg Icon-->
                                     Novo Foro</a>
                                     @else

                                     @endif

                                 <!--begin::Menu-->
                                 <div class="me-0">
                                     <button class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                         data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                         <i class="bi bi-three-dots fs-3"></i>
                                     </button>
                                     <!--begin::Menu 3-->
                                     <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                         data-kt-menu="true">
                                         <!--begin::Heading-->
                                         <div class="menu-item px-3">
                                             <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Payments
                                             </div>
                                         </div>
                                         <!--end::Heading-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3">
                                             <a href="#" class="menu-link px-3">Create Invoice</a>
                                         </div>
                                         <!--end::Menu item-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3">
                                             <a href="#" class="menu-link flex-stack px-3">Create Payment
                                                 <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                     title="Specify a target name for future usage and reference"></i></a>
                                         </div>
                                         <!--end::Menu item-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3">
                                             <a href="#" class="menu-link px-3">Generate Bill</a>
                                         </div>
                                         <!--end::Menu item-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                             data-kt-menu-placement="right-end">
                                             <a href="#" class="menu-link px-3">
                                                 <span class="menu-title">Subscription</span>
                                                 <span class="menu-arrow"></span>
                                             </a>
                                             <!--begin::Menu sub-->
                                             <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                 <!--begin::Menu item-->
                                                 <div class="menu-item px-3">
                                                     <a href="#" class="menu-link px-3">Plans</a>
                                                 </div>
                                                 <!--end::Menu item-->
                                                 <!--begin::Menu item-->
                                                 <div class="menu-item px-3">
                                                     <a href="#" class="menu-link px-3">Billing</a>
                                                 </div>
                                                 <!--end::Menu item-->
                                                 <!--begin::Menu item-->
                                                 <div class="menu-item px-3">
                                                     <a href="#" class="menu-link px-3">Statements</a>
                                                 </div>
                                                 <!--end::Menu item-->
                                                 <!--begin::Menu separator-->
                                                 <div class="separator my-2"></div>
                                                 <!--end::Menu separator-->
                                                 <!--begin::Menu item-->
                                                 <div class="menu-item px-3">
                                                     <div class="menu-content px-3">
                                                         <!--begin::Switch-->
                                                         <label
                                                             class="form-check form-switch form-check-custom form-check-solid">
                                                             <!--begin::Input-->
                                                             <input class="form-check-input w-30px h-20px"
                                                                 type="checkbox" value="1" checked="checked"
                                                                 name="notifications" />
                                                             <!--end::Input-->
                                                             <!--end::Label-->
                                                             <span
                                                                 class="form-check-label text-muted fs-6">Recuring</span>
                                                             <!--end::Label-->
                                                         </label>
                                                         <!--end::Switch-->
                                                     </div>
                                                 </div>
                                                 <!--end::Menu item-->
                                             </div>
                                             <!--end::Menu sub-->
                                         </div>
                                         <!--end::Menu item-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3 my-1">
                                             <a href="#" class="menu-link px-3">Settings</a>
                                         </div>
                                         <!--end::Menu item-->
                                     </div>
                                     <!--end::Menu 3-->
                                 </div>
                                 <!--end::Menu-->
                             </div>
                             <!--end::Actions-->
                         </div>
                         <!--end::Head-->
                         <!--begin::Info-->
                         <div class="d-flex flex-wrap justify-content-start">
                             <!--begin::Stats-->
                             <div class="d-flex flex-wrap">
                                 <!--begin::Stat-->
                                 <div
                                     class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                     <!--begin::Number-->
                                     <div class="d-flex align-items-center">
                                         <div class="fs-4 fw-bold">29 Jan, 2023</div>
                                     </div>
                                     <!--end::Number-->
                                     <!--begin::Label-->
                                     <div class="fw-semibold fs-6 text-gray-400">Due Date</div>
                                     <!--end::Label-->
                                 </div>
                                 <!--end::Stat-->
                                 <!--begin::Stat-->
                                 <div
                                     class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                     <!--begin::Number-->
                                     <div class="d-flex align-items-center">
                                         <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                         <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                 <rect opacity="0.5" x="11" y="18" width="13" height="2"
                                                     rx="1" transform="rotate(-90 11 18)"
                                                     fill="currentColor" />
                                                 <path
                                                     d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                     fill="currentColor" />
                                             </svg>
                                         </span>
                                         <!--end::Svg Icon-->
                                         <div class="fs-4 fw-bold" data-kt-countup="true" data-kt-countup-value="75">
                                             0
                                         </div>
                                     </div>
                                     <!--end::Number-->
                                     <!--begin::Label-->
                                     <div class="fw-semibold fs-6 text-gray-400">Open Tasks</div>
                                     <!--end::Label-->
                                 </div>
                                 <!--end::Stat-->
                                 <!--begin::Stat-->
                                 <div
                                     class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                     <!--begin::Number-->
                                     <div class="d-flex align-items-center">
                                         <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                         <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                             <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                 <rect opacity="0.5" x="13" y="6" width="13" height="2"
                                                     rx="1" transform="rotate(90 13 6)"
                                                     fill="currentColor" />
                                                 <path
                                                     d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                     fill="currentColor" />
                                             </svg>
                                         </span>
                                         <!--end::Svg Icon-->
                                         <div class="fs-4 fw-bold" data-kt-countup="true"
                                             data-kt-countup-value="15000" data-kt-countup-prefix="$">0</div>
                                     </div>
                                     <!--end::Number-->
                                     <!--begin::Label-->
                                     <div class="fw-semibold fs-6 text-gray-400">Budget Spent</div>
                                     <!--end::Label-->
                                 </div>
                                 <!--end::Stat-->
                             </div>
                             <!--end::Stats-->
                             <!--begin::Users-->
                             <div class="symbol-group symbol-hover mb-3">
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Alan Warden">
                                     <span class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Michael Eberon">
                                     <img alt="Pic" src="/assets/media/avatars/300-11.jpg" />
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Michelle Swanston">
                                     <img alt="Pic" src="/assets/media/avatars/300-7.jpg" />
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Francis Mitcham">
                                     <img alt="Pic" src="/assets/media/avatars/300-20.jpg" />
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Susan Redwood">
                                     <span class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Melody Macy">
                                     <img alt="Pic" src="/assets/media/avatars/300-2.jpg" />
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Perry Matthew">
                                     <span class="symbol-label bg-info text-inverse-info fw-bold">P</span>
                                 </div>
                                 <!--end::User-->
                                 <!--begin::User-->
                                 <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                     title="Barry Walter">
                                     <img alt="Pic" src="/assets/media/avatars/300-12.jpg" />
                                 </div>
                                 <!--end::User-->
                                 <!--begin::All users-->
                                 <a href="#" class="symbol symbol-35px symbol-circle" data-bs-toggle="modal"
                                     data-bs-target="#kt_modal_view_users">
                                     <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                         data-bs-toggle="tooltip" data-bs-trigger="hover"
                                         title="View more users">+42</span>
                                 </a>
                                 <!--end::All users-->
                             </div>
                             <!--end::Users-->
                         </div>
                         <!--end::Info-->
                     </div>
                     <!--end::Wrapper-->
                 </div>
                 <!--end::Details-->
                 <div class="separator"></div>
                 <!--begin::Nav-->
                 <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('patrimonio') ? 'active' : '' }}"
                             href="{{ route('patrimonio.index') }}">Resumo</a>
                     </li>
                     <!--end::Nav item-->

                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('apps/projects/targets*') ? 'active' : '' }}"
                             href="../../demo1/dist/apps/projects/targets.html">Foros</a>
                     </li>
                     <!--end::Nav item-->

                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('patrimonios/imoveis') ? 'active' : '' }}"
                             href="{{ route('patrimonio.imoveis') }}">Imóveis</a>
                     </li>
                     <!--end::Nav item-->

                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('apps/projects/users*') ? 'active' : '' }}"
                             href="../../demo1/dist/apps/projects/users.html">Users</a>
                     </li>
                     <!--end::Nav item-->

                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('apps/projects/files*') ? 'active' : '' }}"
                             href="../../demo1/dist/apps/projects/files.html">Files</a>
                     </li>
                     <!--end::Nav item-->

                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('apps/projects/activity*') ? 'active' : '' }}"
                             href="../../demo1/dist/apps/projects/activity.html">Activity</a>
                     </li>
                     <!--end::Nav item-->

                     <!--begin::Nav item-->
                     <li class="nav-item">
                         <a class="nav-link text-active-primary py-5 me-6 {{ Request::is('patrimonio/create') ? 'active' : '' }}"
                             href="{{ route('patrimonio.create') }}">Acessórios</a>
                     </li>
                     <!--end::Nav item-->
                 </ul>
                 <!--end::Nav-->

             </div>
         </div>
         <!--end::Navbar-->
