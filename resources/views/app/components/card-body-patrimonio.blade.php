         <!--begin::Navbar-->
         <div class="card mb-6 mb-xl-9">
             <div class="card-body pt-9 pb-0">
                 <!--begin::Details-->
                 <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                     <!--begin::Image-->
                     <div
                         class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                         {{-- <img class="mw-50px mw-lg-75px" src="/assets/media/svg/icons/patrimonio-home.svg" alt="image" /> --}}
                         <img class="mw-75px mw-lg-150px mh-75px mh-lg-150px" src="/assets/media/png/house3d.png"
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
                                         <span class="svg-icon svg-icon-2">
                                            <i class="bi bi-search"></i>
                                         </span>
                                         Pesquisar...</a>
                                     <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                                         data-bs-target="#kt_modal_new_imovel">
                                         <i class="bi bi-house-add-fill"></i>
                                         <span class="fw-normal">Cadastro de Imóveis</span>
                                     </a>
                                 @elseif (Request::is('patrimonio'))
                                     <a href="#" class="btn btn-sm btn-bg-light btn-active-color-success me-3"
                                         data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">
                                         <span class="svg-icon svg-icon-2">
                                            <i class="bi bi-search"></i>
                                         </span>
                                         Pesquisar...</a>
                                     <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                                     data-bs-target="#kt_modal_new_foro">
                                         <i class="bi bi-house-add-fill"></i>
                                         <span class="fw-normal">Cadastro de Imóveis Foreiros</span>
                                     </a>
                                     @elseif (Request::is('patrimonios/bens-moveis'))
                                     <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                         data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">
                                         <span class="svg-icon svg-icon-2">
                                            <i class="bi bi-search "></i>
                                         </span>
                                         Pesquisar...</a>
                                     <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                                         data-bs-target="#kt_modal_new_bens_moveis">
                                         <i class="bi bi-box-seam "></i>
                                         <span class="fw-normal">Cadastro de Bens Móveis</span>
                                     </a>
                                     @elseif (Request::is('patrimonios/veiculos'))
                                     <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                         data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">
                                         <span class="svg-icon svg-icon-2">
                                            <i class="bi bi-search "></i>
                                         </span>
                                         Pesquisar...</a>
                                     <a href="#" class="btn btn-sm btn-warning me-3" data-bs-toggle="modal"
                                         data-bs-target="#kt_modal_new_veiculos">
                                         <i class="bi bi-car-front "></i>
                                         <span class="fw-normal">Cadastro de Veículos</span>
                                     </a>
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
                                             <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Menu de Ações
                                             </div>
                                         </div>
                                         <!--end::Heading-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3">
                                             <a href="#" class="menu-link px-3 " data-bs-toggle="modal"
                                                 data-bs-target="#Dm_modal_Avaliador">Criar Avaliador</a>
                                         </div>
                                         <!--end::Menu item-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3">
                                            <a class="menu-link px-3 text-active-primary py-1 me-6 {{ Request::is('patrimonios/filtrar') ? 'active' : '' }}"
                                            href="{{ route('patrimonio.filtrar') }}">Filtrar Patrimônios
                                        </a>
                                         </div>
                                         <!--end::Menu item-->
                                         <!--begin::Menu item-->
                                         <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                             data-kt-menu-placement="right-end">
                                             <a href="#" class="menu-link px-3">
                                                 <span class="menu-title">Acessórios</span>
                                                 <span class="menu-arrow"></span>
                                             </a>
                                             <!--begin::Menu sub-->
                                             <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                 <!--begin::Menu item-->
                                                 <div class="menu-item px-3">
                                                    <a class="menu-link px-3 text-active-primary py-1 me-6 {{ Request::is('patrimonio/create') ? 'active' : '' }}"
                                                    href="{{ route('patrimonio.create') }}">
                                                    Cadastror de Territorio Foreiro
                                                </a>
                                                 </div>
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
                                     class="border border-gray-300 border-dashed rounded min-w-150px py-3 px-4 me-6 mb-3">
                                     <!--begin::Number-->
                                     <div class="d-flex align-items-center">
                                         <i class="bi bi-calendar2-date-fill fs-4 me-2 text-success"></i>
                                         <div class="fs-4 fw-bold">{{ now()->format('d M, Y') }}</div>
                                     </div>
                                     <!--end::Number-->
                                     <!--begin::Label-->
                                     <div class="fw-semibold fs-6 text-gray-400">Data Atual</div>
                                     <!--end::Label-->
                                 </div>
                                 <!--end::Stat-->
                                 <!--begin::Stat-->
                                 <div
                                     class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                     <!--begin::Number-->
                                     <div class="d-flex align-items-center">
                                         <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                         <i class="bi bi-house-fill fs-4 me-2 text-success"></i>
                                         <!--end::Svg Icon-->
                                         <div class="fs-4 fw-bold" data-kt-countup="true"
                                             data-kt-countup-value="1234567898">
                                             1234567898 <!-- Exibe o total dinamicamente -->
                                         </div>
                                     </div>
                                     <!--end::Number-->
                                     <!--begin::Label-->
                                     <div class="fw-semibold fs-6 text-gray-400">Total de Patrimônios</div>
                                     <!-- Atualize o label -->
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
                         <a class="nav-link text-active-primary py-3 me-3 {{ Request::is('patrimonio') ? 'active' : '' }}"
                             href="{{ route('patrimonio.index') }}">
                             <i class="bi bi-speedometer2 me-1"></i> Resumo
                         </a>
                     </li>
                     <!--end::Nav item-->

                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-3 me-3 {{ Request::is('patrimonios/bens-moveis') ? 'active' : '' }}"
                            href="{{ route('patrimonio.bens-moveis') }}">
                            <i class="bi bi-box-seam me-1"></i> Bens Móveis
                        </a>
                    </li>
                    <!--end::Nav item-->

                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-3 me-3 {{ Request::is('patrimonios/imoveis') ? 'active' : '' }}"
                            href="{{ route('patrimonio.imoveis') }}">
                            <i class="bi bi-house-door me-1"></i> Imóveis
                        </a>
                    </li>
                    <!--end::Nav item-->

                    <!--begin::Nav item-->
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-3 me-3 {{ Request::is('patrimonios/veiculos') ? 'active' : '' }}"
                            href="{{ route('patrimonio.veiculos') }}">
                            <i class="bi bi-car-front me-1"></i> Veículos
                        </a>
                    </li>
                    <!--end::Nav item-->
                 </ul>

                 <!--end::Nav-->

             </div>
         </div>
         <!--end::Navbar-->
