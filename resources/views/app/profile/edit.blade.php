<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Ver detalhes do Usuário
                        </h1>
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

                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Layout-->
                    <div class="d-flex flex-column flex-lg-row">
                        <!--begin::Sidebar-->
                        <div class="flex-column flex-lg-row-auto w-lg-250px w-xl-350px mb-10">
                            <!--begin::Card-->
                            <div class="card mb-5 mb-xl-8">
                                <!--begin::Card body-->
                                <div class="card-body">
                                    <!--begin::Summary-->
                                    <!--begin::User Info-->
                                    <div class="d-flex flex-center flex-column py-5">
                                        <!--begin::Avatar-->
                                        <div class="symbol symbol-100px symbol-circle mb-7">
                                            @if (Auth::check() && Auth::user()->avatar)
                                            <img src="{{ route('file', ['path' => Auth::user()->avatar]) }}" alt="image" />
                                        @else
                                            <img src="{{ asset('path/to/default/avatar.png') }}" alt="default image" />
                                        @endif

                                        </div>
                                        <!--end::Avatar-->
                                        <!--begin::Name-->
                                        <a href="#"
                                            class="fs-3 text-gray-800 text-hover-primary fw-bold mb-3">{{ $user->name ?? '' }}</a>
                                        <!--end::Name-->
                                        <!--begin::Position-->
                                        <div class="mb-9">
                                            <!--begin::Badge-->
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="badge {{ $roleColors[$role->name] ?? 'badge-secondary' }}">{{ $role->name }}</span>
                                            @endforeach
                                            <!--begin::Badge-->
                                        </div>
                                        <!--end::Position-->
                                        <!--begin::Info-->
                                        <!--begin::Info heading-->
                                        <div class="fw-bold mb-3">Lançamentos
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="popover"
                                                data-bs-trigger="hover" data-bs-html="true"
                                                data-bs-content="Number of support tickets assigned, closed and pending this week."></i>
                                        </div>
                                        <!--end::Info heading-->
                                        <div class="d-flex flex-wrap flex-center">
                                            <!--begin::Stats-->
                                            <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                                <div class="fs-4 fw-bold text-gray-700">
                                                    <span class="w-75px">243</span>
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-success">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path
                                                                d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                                <div class="fw-semibold text-muted">Total</div>
                                            </div>
                                            <!--end::Stats-->
                                            <!--begin::Stats-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded py-3 px-3 mx-4 mb-3">
                                                <div class="fs-4 fw-bold text-gray-700">
                                                    <span class="w-50px">56</span>
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-danger">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="11" y="18" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(-90 11 18)" fill="currentColor" />
                                                            <path
                                                                d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                                <div class="fw-semibold text-muted">Saídas</div>
                                            </div>
                                            <!--end::Stats-->
                                            <!--begin::Stats-->
                                            <div class="border border-gray-300 border-dashed rounded py-3 px-3 mb-3">
                                                <div class="fs-4 fw-bold text-gray-700">
                                                    <span class="w-50px">188</span>
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-success">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path
                                                                d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </div>
                                                <div class="fw-semibold text-muted">Entradas</div>
                                            </div>
                                            <!--end::Stats-->
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::User Info-->
                                    <!--end::Summary-->
                                    <!--begin::Details toggle-->
                                    <div class="d-flex flex-stack fs-4 py-3">
                                        <div class="fw-bold rotate collapsible" data-bs-toggle="collapse"
                                            href="#kt_user_view_details" role="button" aria-expanded="false"
                                            aria-controls="kt_user_view_details">Detalhes
                                            <span class="ms-2 rotate-180">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon-->
                                            </span>
                                        </div>
                                        <span data-bs-toggle="tooltip" data-bs-trigger="hover"
                                            title="Editar detalhes do usuário">
                                            <a href="#" class="btn btn-sm btn-light-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_update_details">Editar</a>
                                        </span>
                                    </div>
                                    <!--end::Details toggle-->
                                    <div class="separator"></div>
                                    <!--begin::Details content-->
                                    <div id="kt_user_view_details" class="collapse show">
                                        <div class="pb-5 fs-6">
                                            <!--begin::Details item-->
                                            <div class="fw-bold mt-5"> ID da Conta</div>
                                            <div class="text-gray-600">ID-{{ $user->id }}</div>
                                            <!--begin::Details item-->
                                            <!--begin::Details item-->
                                            <div class="fw-bold mt-5">E-mail</div>
                                            <div class="text-gray-600">
                                                <a href="#"
                                                    class="text-gray-600 text-hover-primary">{{ $user->email }}</a>
                                            </div>
                                            <!--begin::Details item-->
                                            <!--begin::Details item-->
                                            <div class="fw-bold mt-5">Endereço</div>
                                            <div class="text-gray-600">101 Collin Street,
                                                <br />Melbourne 3000 VIC
                                                <br />Brasil
                                            </div>
                                            <!--begin::Details item-->
                                            <!--begin::Details item-->
                                            <div class="fw-bold mt-5">Idioma</div>
                                            <div class="text-gray-600">Portugues</div>
                                            <!--begin::Details item-->
                                            <!--begin::Details item-->
                                            <div class="fw-bold mt-5">Último login</div>
                                            <div class="text-gray-600">{{ $user->last_login_formatted }}</div>
                                            <!--begin::Details item-->
                                        </div>
                                    </div>
                                    <!--end::Details content-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                            <!--begin::Connected Accounts-->
                            <div class="card mb-5 mb-xl-8">
                                <!--begin::Card header-->
                                <div class="card-header border-0">
                                    <div class="card-title">
                                        <h3 class="fw-bold m-0">Connected Accounts</h3>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-2">
                                    <!--begin::Notice-->
                                    <div
                                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                        <!--begin::Icon-->
                                        <!--begin::Svg Icon | path: icons/duotune/art/art006.svg-->
                                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M22 19V17C22 16.4 21.6 16 21 16H8V3C8 2.4 7.6 2 7 2H5C4.4 2 4 2.4 4 3V19C4 19.6 4.4 20 5 20H21C21.6 20 22 19.6 22 19Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M20 5V21C20 21.6 19.6 22 19 22H17C16.4 22 16 21.6 16 21V8H8V4H19C19.6 4 20 4.4 20 5ZM3 8H4V4H3C2.4 4 2 4.4 2 5V7C2 7.6 2.4 8 3 8Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <!--end::Icon-->
                                        <!--begin::Wrapper-->
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <!--begin::Content-->
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">Ao criar uma conta, o utilizador aceita
                                                    a nossa <a href="#" class="me-1">política de
                                                        privacidade</a> e os nossos <a href="#"
                                                        class="me-1">termos de uso</a>.

                                                </div>
                                            </div>
                                            <!--end::Content-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Notice-->
                                    <!--begin::Items-->
                                    <div class="py-2">
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex">
                                                <img src="/assets/media/svg/brand-logos/google-icon.svg"
                                                    class="w-30px me-6" alt="" />
                                                <div class="d-flex flex-column">
                                                    <a href="#"
                                                        class="fs-5 text-dark text-hover-primary fw-bold">Google</a>
                                                    <div class="fs-6 fw-semibold text-muted">Planear corretamente o seu
                                                        fluxo de trabalho</div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <!--begin::Switch-->
                                                <label
                                                    class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input" name="google" type="checkbox"
                                                        value="1" id="kt_modal_connected_accounts_google"
                                                        checked="checked" />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <span class="form-check-label fw-semibold text-muted"
                                                        for="kt_modal_connected_accounts_google"></span>
                                                    <!--end::Label-->
                                                </label>
                                                <!--end::Switch-->
                                            </div>
                                        </div>
                                        <!--end::Item-->
                                        <div class="separator separator-dashed my-5"></div>
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex">
                                                <img src="/assets/media/svg/brand-logos/github.svg" class="w-30px me-6"
                                                    alt="" />
                                                <div class="d-flex flex-column">
                                                    <a href="#"
                                                        class="fs-5 text-dark text-hover-primary fw-bold">Github</a>
                                                    <div class="fs-6 fw-semibold text-muted">Fique de olho nos seus
                                                        Repositórios</div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <!--begin::Switch-->
                                                <label
                                                    class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input" name="github" type="checkbox"
                                                        value="1" id="kt_modal_connected_accounts_github"
                                                        checked="checked" />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <span class="form-check-label fw-semibold text-muted"
                                                        for="kt_modal_connected_accounts_github"></span>
                                                    <!--end::Label-->
                                                </label>
                                                <!--end::Switch-->
                                            </div>
                                        </div>
                                        <!--end::Item-->
                                        <div class="separator separator-dashed my-5"></div>
                                        <!--begin::Item-->
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex">
                                                <img src="/assets/media/svg/brand-logos/slack-icon.svg"
                                                    class="w-30px me-6" alt="" />
                                                <div class="d-flex flex-column">
                                                    <a href="#"
                                                        class="fs-5 text-dark text-hover-primary fw-bold">Slack</a>
                                                    <div class="fs-6 fw-semibold text-muted">Integrar discussões de
                                                        projectos</div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <!--begin::Switch-->
                                                <label
                                                    class="form-check form-switch form-switch-sm form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input" name="slack" type="checkbox"
                                                        value="1" id="kt_modal_connected_accounts_slack" />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <span class="form-check-label fw-semibold text-muted"
                                                        for="kt_modal_connected_accounts_slack"></span>
                                                    <!--end::Label-->
                                                </label>
                                                <!--end::Switch-->
                                            </div>
                                        </div>
                                        <!--end::Item-->
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Card body-->
                                <!--begin::Card footer-->
                                <div class="card-footer border-0 d-flex justify-content-center pt-0">
                                    <button class="btn btn-sm btn-light-primary">Salvar Alterações</button>
                                </div>
                                <!--end::Card footer-->
                            </div>
                            <!--end::Connected Accounts-->
                        </div>
                        <!--end::Sidebar-->
                        <!--begin::Content-->
                        <div class="flex-lg-row-fluid ms-lg-15">
                            <!--begin:::Tabs-->
                            <ul
                                class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8">
                                <!--begin:::Tab item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary pb-4 active" data-bs-toggle="tab"
                                        href="#kt_user_view_overview_tab">Visão Geral</a>
                                </li>
                                <!--end:::Tab item-->
                                <!--begin:::Tab item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary pb-4" data-kt-countup-tabs="true"
                                        data-bs-toggle="tab" href="#kt_user_view_overview_security">Segurança</a>
                                </li>
                                <!--end:::Tab item-->
                                <!--begin:::Tab item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary pb-4" data-bs-toggle="tab"
                                        href="#kt_user_view_overview_events_and_logs_tab">Eventos e Logs</a>
                                </li>
                                <!--end:::Tab item-->
                                <!--begin:::Tab item-->
                                <li class="nav-item ms-auto">
                                    <!--begin::Action menu-->
                                    <a href="#" class="btn btn-primary ps-7" data-kt-menu-trigger="click"
                                        data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">Actions
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                        <span class="svg-icon svg-icon-2 me-0">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon--></a>
                                    <!--begin::Menu-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold py-4 w-250px fs-6"
                                        data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">
                                                Payments</div>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="#" class="menu-link px-5">Create invoice</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="#" class="menu-link flex-stack px-5">Create payments
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                    data-bs-toggle="tooltip"
                                                    title="Specify a target name for future usage and reference"></i></a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5" data-kt-menu-trigger="hover"
                                            data-kt-menu-placement="left-start">
                                            <a href="#" class="menu-link px-5">
                                                <span class="menu-title">Subscription</span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <!--begin::Menu sub-->
                                            <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-5">Apps</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-5">Billing</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-5">Statements</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu separator-->
                                                <div class="separator my-2"></div>
                                                <!--end::Menu separator-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <div class="menu-content px-3">
                                                        <label
                                                            class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input w-30px h-20px"
                                                                type="checkbox" value="" name="notifications"
                                                                checked="checked" id="kt_user_menu_notifications" />
                                                            <span class="form-check-label text-muted fs-6"
                                                                for="kt_user_menu_notifications">Notifications</span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!--end::Menu item-->
                                            </div>
                                            <!--end::Menu sub-->
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu separator-->
                                        <div class="separator my-3"></div>
                                        <!--end::Menu separator-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <div class="menu-content text-muted pb-2 px-5 fs-7 text-uppercase">
                                                Account</div>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="#" class="menu-link px-5">Reports</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5 my-1">
                                            <a href="#" class="menu-link px-5">Account Settings</a>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="#" class="menu-link text-danger px-5">Delete
                                                customer</a>
                                        </div>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::Menu-->
                                    <!--end::Menu-->
                                </li>
                                <!--end:::Tab item-->
                            </ul>
                            <!--end:::Tabs-->
                            <!--begin:::Tab content-->
                            <div class="tab-content" id="myTabContent">
                                <!--begin:::Tab pane-->
                                <div class="tab-pane fade show active" id="kt_user_view_overview_tab"
                                    role="tabpanel">
                                    <!--begin::Card-->
                                    <div class="card card-flush mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header mt-6">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h2 class="mb-1">User's Schedule</h2>
                                                <div class="fs-6 fw-semibold text-muted">2 upcoming meetings</div>
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <button type="button" class="btn btn-light-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#kt_modal_add_schedule">
                                                    <!--SVG file not found: media/icons/duotune/art/art008.svg-->
                                                    Add Schedule</button>
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body p-9 pt-4">
                                            <!--begin::Dates-->
                                            <ul class="nav nav-pills d-flex flex-nowrap hover-scroll-x py-2">
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_0">
                                                        <span class="opacity-50 fs-7 fw-semibold">Su</span>
                                                        <span class="fs-6 fw-bolder">21</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary active"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_1">
                                                        <span class="opacity-50 fs-7 fw-semibold">Mo</span>
                                                        <span class="fs-6 fw-bolder">22</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_2">
                                                        <span class="opacity-50 fs-7 fw-semibold">Tu</span>
                                                        <span class="fs-6 fw-bolder">23</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_3">
                                                        <span class="opacity-50 fs-7 fw-semibold">We</span>
                                                        <span class="fs-6 fw-bolder">24</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_4">
                                                        <span class="opacity-50 fs-7 fw-semibold">Th</span>
                                                        <span class="fs-6 fw-bolder">25</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_5">
                                                        <span class="opacity-50 fs-7 fw-semibold">Fr</span>
                                                        <span class="fs-6 fw-bolder">26</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_6">
                                                        <span class="opacity-50 fs-7 fw-semibold">Sa</span>
                                                        <span class="fs-6 fw-bolder">27</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_7">
                                                        <span class="opacity-50 fs-7 fw-semibold">Su</span>
                                                        <span class="fs-6 fw-bolder">28</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_8">
                                                        <span class="opacity-50 fs-7 fw-semibold">Mo</span>
                                                        <span class="fs-6 fw-bolder">29</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_9">
                                                        <span class="opacity-50 fs-7 fw-semibold">Tu</span>
                                                        <span class="fs-6 fw-bolder">30</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                                <!--begin::Date-->
                                                <li class="nav-item me-1">
                                                    <a class="nav-link btn d-flex flex-column flex-center rounded-pill min-w-40px me-2 py-4 btn-active-primary"
                                                        data-bs-toggle="tab" href="#kt_schedule_day_10">
                                                        <span class="opacity-50 fs-7 fw-semibold">We</span>
                                                        <span class="fs-6 fw-bolder">31</span>
                                                    </a>
                                                </li>
                                                <!--end::Date-->
                                            </ul>
                                            <!--end::Dates-->
                                            <!--begin::Tab Content-->
                                            <div class="tab-content">
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_0" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Team
                                                                Backlog Grooming Session</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Naomi Hayabusa</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Weekly
                                                                Team Stand-Up</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Karina Clarke</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">14:30 - 15:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Team
                                                                Backlog Grooming Session</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">David Stevenson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_1" class="tab-pane fade show active">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">9:00 - 10:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">9
                                                                Degree Project Estimation Meeting</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Kendell Trevor</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">9:00 - 10:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Team
                                                                Backlog Grooming Session</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Terry Robins</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Committee
                                                                Review Approvals</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Peter Marcus</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Dashboard
                                                                UI/UX Design Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Yannis Gloverson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_2" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">12:00 - 13:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Committee
                                                                Review Approvals</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">David Stevenson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Project
                                                                Review & Testing</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Mark Randall</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">16:30 - 17:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Project
                                                                Review & Testing</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Mark Randall</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">9:00 - 10:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Dashboard
                                                                UI/UX Design Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Karina Clarke</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_3" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">12:00 - 13:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Creative
                                                                Content Initiative</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Peter Marcus</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">9
                                                                Degree Project Estimation Meeting</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Kendell Trevor</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">16:30 - 17:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">9
                                                                Degree Project Estimation Meeting</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Kendell Trevor</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_4" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">16:30 - 17:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Marketing
                                                                Campaign Discussion</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Caleb Donaldson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">9:00 - 10:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Project
                                                                Review & Testing</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Naomi Hayabusa</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">9:00 - 10:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Development
                                                                Team Capacity Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Yannis Gloverson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">14:30 - 15:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">9
                                                                Degree Project Estimation Meeting</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Caleb Donaldson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_5" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">12:00 - 13:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Development
                                                                Team Capacity Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Karina Clarke</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">11:00 - 11:45
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Marketing
                                                                Campaign Discussion</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Yannis Gloverson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">9
                                                                Degree Project Estimation Meeting</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Michael Walters</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_6" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Creative
                                                                Content Initiative</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Naomi Hayabusa</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">11:00 - 11:45
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Team
                                                                Backlog Grooming Session</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Kendell Trevor</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Committee
                                                                Review Approvals</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Walter White</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Development
                                                                Team Capacity Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Yannis Gloverson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_7" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Team
                                                                Backlog Grooming Session</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Peter Marcus</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">14:30 - 15:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Weekly
                                                                Team Stand-Up</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">David Stevenson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Lunch
                                                                & Learn Catch Up</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Yannis Gloverson</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_8" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">9
                                                                Degree Project Estimation Meeting</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Bob Harris</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Committee
                                                                Review Approvals</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Terry Robins</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">12:00 - 13:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Weekly
                                                                Team Stand-Up</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Walter White</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">12:00 - 13:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Marketing
                                                                Campaign Discussion</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Walter White</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_9" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Dashboard
                                                                UI/UX Design Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Sean Bean</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Development
                                                                Team Capacity Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Mark Randall</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">14:30 - 15:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Lunch
                                                                & Learn Catch Up</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Mark Randall</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">13:00 - 14:00
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Creative
                                                                Content Initiative</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Walter White</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                                <!--begin::Day-->
                                                <div id="kt_schedule_day_10" class="tab-pane fade show">
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">10:00 - 11:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Creative
                                                                Content Initiative</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Peter Marcus</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">9:00 - 10:00
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Development
                                                                Team Capacity Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Walter White</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">16:30 - 17:30
                                                                <span class="fs-7 text-muted text-uppercase">pm</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Weekly
                                                                Team Stand-Up</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Bob Harris</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                    <!--begin::Time-->
                                                    <div class="d-flex flex-stack position-relative mt-6">
                                                        <!--begin::Bar-->
                                                        <div
                                                            class="position-absolute h-100 w-4px bg-secondary rounded top-0 start-0">
                                                        </div>
                                                        <!--end::Bar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold ms-5">
                                                            <!--begin::Time-->
                                                            <div class="fs-7 mb-1">11:00 - 11:45
                                                                <span class="fs-7 text-muted text-uppercase">am</span>
                                                            </div>
                                                            <!--end::Time-->
                                                            <!--begin::Title-->
                                                            <a href="#"
                                                                class="fs-5 fw-bold text-dark text-hover-primary mb-2">Development
                                                                Team Capacity Review</a>
                                                            <!--end::Title-->
                                                            <!--begin::User-->
                                                            <div class="fs-7 text-muted">Lead by
                                                                <a href="#">Mark Randall</a>
                                                            </div>
                                                            <!--end::User-->
                                                        </div>
                                                        <!--end::Info-->
                                                        <!--begin::Action-->
                                                        <a href="#"
                                                            class="btn btn-light bnt-active-light-primary btn-sm">View</a>
                                                        <!--end::Action-->
                                                    </div>
                                                    <!--end::Time-->
                                                </div>
                                                <!--end::Day-->
                                            </div>
                                            <!--end::Tab Content-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                    <!--begin::Tasks-->
                                    <div class="card card-flush mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header mt-6">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h2 class="mb-1">User's Tasks</h2>
                                                <div class="fs-6 fw-semibold text-muted">Total 25 tasks in backlog
                                                </div>
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <button type="button" class="btn btn-light-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#kt_modal_add_task">
                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil005.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3"
                                                                d="M19 22H5C4.4 22 4 21.6 4 21V3C4 2.4 4.4 2 5 2H14L20 8V21C20 21.6 19.6 22 19 22ZM16 13.5L12.5 13V10C12.5 9.4 12.6 9.5 12 9.5C11.4 9.5 11.5 9.4 11.5 10L11 13L8 13.5C7.4 13.5 7 13.4 7 14C7 14.6 7.4 14.5 8 14.5H11V18C11 18.6 11.4 19 12 19C12.6 19 12.5 18.6 12.5 18V14.5L16 14C16.6 14 17 14.6 17 14C17 13.4 16.6 13.5 16 13.5Z"
                                                                fill="currentColor" />
                                                            <rect x="11" y="19" width="10" height="2"
                                                                rx="1" transform="rotate(-90 11 19)"
                                                                fill="currentColor" />
                                                            <rect x="7" y="13" width="10" height="2"
                                                                rx="1" fill="currentColor" />
                                                            <path d="M15 8H20L14 2V7C14 7.6 14.4 8 15 8Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Add Task</button>
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-column">
                                            <!--begin::Item-->
                                            <div class="d-flex align-items-center position-relative mb-7">
                                                <!--begin::Label-->
                                                <div
                                                    class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Details-->
                                                <div class="fw-semibold ms-5">
                                                    <a href="#"
                                                        class="fs-5 fw-bold text-dark text-hover-primary">Create
                                                        FureStibe branding logo</a>
                                                    <!--begin::Info-->
                                                    <div class="fs-7 text-muted">Due in 1 day
                                                        <a href="#">Karina Clark</a>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                    <span class="svg-icon svg-icon-3">
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
                                                </button>
                                                <!--begin::Task menu-->
                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                                    data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bold">Update Status</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Menu separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Form-->
                                                    <form class="form px-7 py-5"
                                                        data-kt-menu-id="kt-users-tasks-form">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="form-label fs-6 fw-semibold">Status:</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select class="form-select form-select-solid"
                                                                name="task_status" data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true" data-hide-search="true">
                                                                <option></option>
                                                                <option value="1">Approved</option>
                                                                <option value="2">Pending</option>
                                                                <option value="3">In Process</option>
                                                                <option value="4">Rejected</option>
                                                            </select>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                                data-kt-users-update-task-status="reset">Reset</button>
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-kt-users-update-task-status="submit">
                                                                <span class="indicator-label">Apply</span>
                                                                <span class="indicator-progress">Please wait...
                                                                    <span
                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>
                                                <!--end::Task menu-->
                                                <!--end::Menu-->
                                            </div>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <div class="d-flex align-items-center position-relative mb-7">
                                                <!--begin::Label-->
                                                <div
                                                    class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Details-->
                                                <div class="fw-semibold ms-5">
                                                    <a href="#"
                                                        class="fs-5 fw-bold text-dark text-hover-primary">Schedule
                                                        a meeting with FireBear CTO John</a>
                                                    <!--begin::Info-->
                                                    <div class="fs-7 text-muted">Due in 3 days
                                                        <a href="#">Rober Doe</a>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                    <span class="svg-icon svg-icon-3">
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
                                                </button>
                                                <!--begin::Task menu-->
                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                                    data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bold">Update Status</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Menu separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Form-->
                                                    <form class="form px-7 py-5"
                                                        data-kt-menu-id="kt-users-tasks-form">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="form-label fs-6 fw-semibold">Status:</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select class="form-select form-select-solid"
                                                                name="task_status" data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true" data-hide-search="true">
                                                                <option></option>
                                                                <option value="1">Approved</option>
                                                                <option value="2">Pending</option>
                                                                <option value="3">In Process</option>
                                                                <option value="4">Rejected</option>
                                                            </select>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                                data-kt-users-update-task-status="reset">Reset</button>
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-kt-users-update-task-status="submit">
                                                                <span class="indicator-label">Apply</span>
                                                                <span class="indicator-progress">Please wait...
                                                                    <span
                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>
                                                <!--end::Task menu-->
                                                <!--end::Menu-->
                                            </div>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <div class="d-flex align-items-center position-relative mb-7">
                                                <!--begin::Label-->
                                                <div
                                                    class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Details-->
                                                <div class="fw-semibold ms-5">
                                                    <a href="#"
                                                        class="fs-5 fw-bold text-dark text-hover-primary">9 Degree
                                                        Project Estimation</a>
                                                    <!--begin::Info-->
                                                    <div class="fs-7 text-muted">Due in 1 week
                                                        <a href="#">Neil Owen</a>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                    <span class="svg-icon svg-icon-3">
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
                                                </button>
                                                <!--begin::Task menu-->
                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                                    data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bold">Update Status</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Menu separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Form-->
                                                    <form class="form px-7 py-5"
                                                        data-kt-menu-id="kt-users-tasks-form">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="form-label fs-6 fw-semibold">Status:</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select class="form-select form-select-solid"
                                                                name="task_status" data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true" data-hide-search="true">
                                                                <option></option>
                                                                <option value="1">Approved</option>
                                                                <option value="2">Pending</option>
                                                                <option value="3">In Process</option>
                                                                <option value="4">Rejected</option>
                                                            </select>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                                data-kt-users-update-task-status="reset">Reset</button>
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-kt-users-update-task-status="submit">
                                                                <span class="indicator-label">Apply</span>
                                                                <span class="indicator-progress">Please wait...
                                                                    <span
                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>
                                                <!--end::Task menu-->
                                                <!--end::Menu-->
                                            </div>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <div class="d-flex align-items-center position-relative mb-7">
                                                <!--begin::Label-->
                                                <div
                                                    class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Details-->
                                                <div class="fw-semibold ms-5">
                                                    <a href="#"
                                                        class="fs-5 fw-bold text-dark text-hover-primary">Dashboard
                                                        UI & UX for Leafr CRM</a>
                                                    <!--begin::Info-->
                                                    <div class="fs-7 text-muted">Due in 1 week
                                                        <a href="#">Olivia Wild</a>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                    <span class="svg-icon svg-icon-3">
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
                                                </button>
                                                <!--begin::Task menu-->
                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                                    data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bold">Update Status</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Menu separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Form-->
                                                    <form class="form px-7 py-5"
                                                        data-kt-menu-id="kt-users-tasks-form">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="form-label fs-6 fw-semibold">Status:</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select class="form-select form-select-solid"
                                                                name="task_status" data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true" data-hide-search="true">
                                                                <option></option>
                                                                <option value="1">Approved</option>
                                                                <option value="2">Pending</option>
                                                                <option value="3">In Process</option>
                                                                <option value="4">Rejected</option>
                                                            </select>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                                data-kt-users-update-task-status="reset">Reset</button>
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-kt-users-update-task-status="submit">
                                                                <span class="indicator-label">Apply</span>
                                                                <span class="indicator-progress">Please wait...
                                                                    <span
                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>
                                                <!--end::Task menu-->
                                                <!--end::Menu-->
                                            </div>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <div class="d-flex align-items-center position-relative">
                                                <!--begin::Label-->
                                                <div
                                                    class="position-absolute top-0 start-0 rounded h-100 bg-secondary w-4px">
                                                </div>
                                                <!--end::Label-->
                                                <!--begin::Details-->
                                                <div class="fw-semibold ms-5">
                                                    <a href="#"
                                                        class="fs-5 fw-bold text-dark text-hover-primary">Mivy App
                                                        R&D, Meeting with clients</a>
                                                    <!--begin::Info-->
                                                    <div class="fs-7 text-muted">Due in 2 weeks
                                                        <a href="#">Sean Bean</a>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Menu-->
                                                <button type="button"
                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
                                                    <span class="svg-icon svg-icon-3">
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
                                                </button>
                                                <!--begin::Task menu-->
                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px"
                                                    data-kt-menu="true" data-kt-menu-id="kt-users-tasks">
                                                    <!--begin::Header-->
                                                    <div class="px-7 py-5">
                                                        <div class="fs-5 text-dark fw-bold">Update Status</div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Menu separator-->
                                                    <div class="separator border-gray-200"></div>
                                                    <!--end::Menu separator-->
                                                    <!--begin::Form-->
                                                    <form class="form px-7 py-5"
                                                        data-kt-menu-id="kt-users-tasks-form">
                                                        <!--begin::Input group-->
                                                        <div class="fv-row mb-10">
                                                            <!--begin::Label-->
                                                            <label class="form-label fs-6 fw-semibold">Status:</label>
                                                            <!--end::Label-->
                                                            <!--begin::Input-->
                                                            <select class="form-select form-select-solid"
                                                                name="task_status" data-kt-select2="true"
                                                                data-placeholder="Select option"
                                                                data-allow-clear="true" data-hide-search="true">
                                                                <option></option>
                                                                <option value="1">Approved</option>
                                                                <option value="2">Pending</option>
                                                                <option value="3">In Process</option>
                                                                <option value="4">Rejected</option>
                                                            </select>
                                                            <!--end::Input-->
                                                        </div>
                                                        <!--end::Input group-->
                                                        <!--begin::Actions-->
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-sm btn-light btn-active-light-primary me-2"
                                                                data-kt-users-update-task-status="reset">Reset</button>
                                                            <button type="submit" class="btn btn-sm btn-primary"
                                                                data-kt-users-update-task-status="submit">
                                                                <span class="indicator-label">Apply</span>
                                                                <span class="indicator-progress">Please wait...
                                                                    <span
                                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                            </button>
                                                        </div>
                                                        <!--end::Actions-->
                                                    </form>
                                                    <!--end::Form-->
                                                </div>
                                                <!--end::Task menu-->
                                                <!--end::Menu-->
                                            </div>
                                            <!--end::Item-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Tasks-->
                                </div>
                                <!--end:::Tab pane-->
                                <!--begin:::Tab pane-->
                                <div class="tab-pane fade" id="kt_user_view_overview_security" role="tabpanel">
                                    <!--begin::Card-->
                                    <div class="card pt-4 mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header border-0">
                                            <!--begin::Card title-->
                                            <div class="card-title">
                                                <h2>Perfil</h2>
                                            </div>
                                            <!--end::Card title-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0 pb-5">
                                            <!--begin::Table wrapper-->
                                            <div class="table-responsive">
                                                <!--begin::Table-->
                                                <table class="table align-middle table-row-dashed gy-5"
                                                    id="kt_table_users_login_session">
                                                    <!--begin::Table body-->
                                                    <tbody class="fs-6 fw-semibold text-gray-600">
                                                        <tr>
                                                            <td>Email</td>
                                                            <td>{{ $user->email }}</td>
                                                            <td class="text-end">
                                                                <button type="button"
                                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#kt_modal_update_email">
                                                                    <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Senha</td>
                                                            <td>******</td>
                                                            <td class="text-end">
                                                                <button type="button"
                                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#kt_modal_update_password">
                                                                    <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>Role</td>
                                                            <td>
                                                                @foreach ($user->roles as $role)
                                                                    <span
                                                                        class="badge {{ $roleColors[$role->name] ?? 'badge-secondary' }}">{{ $role->name }}
                                                                    </span>
                                                                @endforeach
                                                            </td>
                                                            <td class="text-end">
                                                                <button type="button"
                                                                    class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#kt_modal_update_role">
                                                                    <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                                    <span class="svg-icon svg-icon-3">
                                                                        <svg width="24" height="24"
                                                                            viewBox="0 0 24 24" fill="none"
                                                                            xmlns="http://www.w3.org/2000/svg">
                                                                            <path opacity="0.3"
                                                                                d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                                fill="currentColor" />
                                                                            <path
                                                                                d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                                fill="currentColor" />
                                                                        </svg>
                                                                    </span>
                                                                    <!--end::Svg Icon-->
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <!--end::Table body-->
                                                </table>
                                                <!--end::Table-->
                                            </div>
                                            <!--end::Table wrapper-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                    <!--begin::Card-->
                                    <div class="card pt-4 mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header border-0">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h2 class="mb-1">Two Step Authentication</h2>
                                                <div class="fs-6 fw-semibold text-muted">Keep your account extra
                                                    secure with a second authentication step.</div>
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <!--begin::Add-->
                                                <button type="button" class="btn btn-light-primary btn-sm"
                                                    data-kt-menu-trigger="click"
                                                    data-kt-menu-placement="bottom-end">
                                                    <!--begin::Svg Icon | path: icons/duotune/technology/teh004.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3"
                                                                d="M21 10.7192H3C2.4 10.7192 2 11.1192 2 11.7192C2 12.3192 2.4 12.7192 3 12.7192H6V14.7192C6 18.0192 8.7 20.7192 12 20.7192C15.3 20.7192 18 18.0192 18 14.7192V12.7192H21C21.6 12.7192 22 12.3192 22 11.7192C22 11.1192 21.6 10.7192 21 10.7192Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M11.6 21.9192C11.4 21.9192 11.2 21.8192 11 21.7192C10.6 21.4192 10.5 20.7191 10.8 20.3191C11.7 19.1191 12.3 17.8191 12.7 16.3191C12.8 15.8191 13.4 15.4192 13.9 15.6192C14.4 15.7192 14.8 16.3191 14.6 16.8191C14.2 18.5191 13.4 20.1192 12.4 21.5192C12.2 21.7192 11.9 21.9192 11.6 21.9192ZM8.7 19.7192C10.2 18.1192 11 15.9192 11 13.7192V8.71917C11 8.11917 11.4 7.71917 12 7.71917C12.6 7.71917 13 8.11917 13 8.71917V13.0192C13 13.6192 13.4 14.0192 14 14.0192C14.6 14.0192 15 13.6192 15 13.0192V8.71917C15 7.01917 13.7 5.71917 12 5.71917C10.3 5.71917 9 7.01917 9 8.71917V13.7192C9 15.4192 8.4 17.1191 7.2 18.3191C6.8 18.7191 6.9 19.3192 7.3 19.7192C7.5 19.9192 7.7 20.0192 8 20.0192C8.3 20.0192 8.5 19.9192 8.7 19.7192ZM6 16.7192C6.5 16.7192 7 16.2192 7 15.7192V8.71917C7 8.11917 7.1 7.51918 7.3 6.91918C7.5 6.41918 7.2 5.8192 6.7 5.6192C6.2 5.4192 5.59999 5.71917 5.39999 6.21917C5.09999 7.01917 5 7.81917 5 8.71917V15.7192V15.8191C5 16.3191 5.5 16.7192 6 16.7192ZM9 4.71917C9.5 4.31917 10.1 4.11918 10.7 3.91918C11.2 3.81918 11.5 3.21917 11.4 2.71917C11.3 2.21917 10.7 1.91916 10.2 2.01916C9.4 2.21916 8.59999 2.6192 7.89999 3.1192C7.49999 3.4192 7.4 4.11916 7.7 4.51916C7.9 4.81916 8.2 4.91918 8.5 4.91918C8.6 4.91918 8.8 4.81917 9 4.71917ZM18.2 18.9192C18.7 17.2192 19 15.5192 19 13.7192V8.71917C19 5.71917 17.1 3.1192 14.3 2.1192C13.8 1.9192 13.2 2.21917 13 2.71917C12.8 3.21917 13.1 3.81916 13.6 4.01916C15.6 4.71916 17 6.61917 17 8.71917V13.7192C17 15.3192 16.8 16.8191 16.3 18.3191C16.1 18.8191 16.4 19.4192 16.9 19.6192C17 19.6192 17.1 19.6192 17.2 19.6192C17.7 19.6192 18 19.3192 18.2 18.9192Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Add Authentication Step</button>
                                                <!--begin::Menu-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-6 w-200px py-4"
                                                    data-kt-menu="true">
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#kt_modal_add_auth_app">Use
                                                            authenticator app</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#kt_modal_add_one_time_password">Enable
                                                            one-time password</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu-->
                                                <!--end::Add-->
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pb-5">
                                            <!--begin::Item-->
                                            <div class="d-flex flex-stack">
                                                <!--begin::Content-->
                                                <div class="d-flex flex-column">
                                                    <span>SMS</span>
                                                    <span class="text-muted fs-6">+61 412 345 678</span>
                                                </div>
                                                <!--end::Content-->
                                                <!--begin::Action-->
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <!--begin::Button-->
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto me-5"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#kt_modal_add_one_time_password">
                                                        <!--begin::Svg Icon | path: icons/duotune/art/art005.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3"
                                                                    d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z"
                                                                    fill="currentColor" />
                                                                <path
                                                                    d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </button>
                                                    <!--end::Button-->
                                                    <!--begin::Button-->
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto"
                                                        id="kt_users_delete_two_step">
                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen027.svg-->
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                    d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.5"
                                                                    d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z"
                                                                    fill="currentColor" />
                                                                <path opacity="0.5"
                                                                    d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z"
                                                                    fill="currentColor" />
                                                            </svg>
                                                        </span>
                                                        <!--end::Svg Icon-->
                                                    </button>
                                                    <!--end::Button-->
                                                </div>
                                                <!--end::Action-->
                                            </div>
                                            <!--end::Item-->
                                            <!--begin:Separator-->
                                            <div class="separator separator-dashed my-5"></div>
                                            <!--end:Separator-->
                                            <!--begin::Disclaimer-->
                                            <div class="text-gray-600">If you lose your mobile device or security
                                                key, you can
                                                <a href='#' class="me-1">generate a backup code</a>to
                                                sign in to your account.
                                            </div>
                                            <!--end::Disclaimer-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                    <!--begin::Card-->
                                    <div class="card pt-4 mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header border-0">
                                            <!--begin::Card title-->
                                            <div class="card-title flex-column">
                                                <h2>Email Notifications</h2>
                                                <div class="fs-6 fw-semibold text-muted">Choose what messages
                                                    you’d like to receive for each of your accounts.</div>
                                            </div>
                                            <!--end::Card title-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body">
                                            <!--begin::Form-->
                                            <form class="form" id="kt_users_email_notification_form">
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_0" type="checkbox"
                                                            value="0" id="kt_modal_update_email_notification_0"
                                                            checked='checked' />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_0">
                                                            <div class="fw-bold">Successful Payments</div>
                                                            <div class="text-gray-600">Receive a notification for
                                                                every successful payment.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_1" type="checkbox"
                                                            value="1"
                                                            id="kt_modal_update_email_notification_1" />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_1">
                                                            <div class="fw-bold">Payouts</div>
                                                            <div class="text-gray-600">Receive a notification for
                                                                every initiated payout.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_2" type="checkbox"
                                                            value="2"
                                                            id="kt_modal_update_email_notification_2" />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_2">
                                                            <div class="fw-bold">Application fees</div>
                                                            <div class="text-gray-600">Receive a notification each
                                                                time you collect a fee from an account.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_3" type="checkbox"
                                                            value="3" id="kt_modal_update_email_notification_3"
                                                            checked='checked' />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_3">
                                                            <div class="fw-bold">Disputes</div>
                                                            <div class="text-gray-600">Receive a notification if a
                                                                payment is disputed by a customer and for dispute
                                                                resolutions.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_4" type="checkbox"
                                                            value="4" id="kt_modal_update_email_notification_4"
                                                            checked='checked' />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_4">
                                                            <div class="fw-bold">Payment reviews</div>
                                                            <div class="text-gray-600">Receive a notification if a
                                                                payment is marked as an elevated risk.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_5" type="checkbox"
                                                            value="5"
                                                            id="kt_modal_update_email_notification_5" />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_5">
                                                            <div class="fw-bold">Mentions</div>
                                                            <div class="text-gray-600">Receive a notification if a
                                                                teammate mentions you in a note.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_6" type="checkbox"
                                                            value="6"
                                                            id="kt_modal_update_email_notification_6" />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_6">
                                                            <div class="fw-bold">Invoice Mispayments</div>
                                                            <div class="text-gray-600">Receive a notification if a
                                                                customer sends an incorrect amount to pay their
                                                                invoice.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_7" type="checkbox"
                                                            value="7"
                                                            id="kt_modal_update_email_notification_7" />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_7">
                                                            <div class="fw-bold">Webhooks</div>
                                                            <div class="text-gray-600">Receive notifications about
                                                                consistently failing webhook endpoints.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <div class='separator separator-dashed my-5'></div>
                                                <!--begin::Item-->
                                                <div class="d-flex">
                                                    <!--begin::Checkbox-->
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <!--begin::Input-->
                                                        <input class="form-check-input me-3"
                                                            name="email_notification_8" type="checkbox"
                                                            value="8"
                                                            id="kt_modal_update_email_notification_8" />
                                                        <!--end::Input-->
                                                        <!--begin::Label-->
                                                        <label class="form-check-label"
                                                            for="kt_modal_update_email_notification_8">
                                                            <div class="fw-bold">Trial</div>
                                                            <div class="text-gray-600">Receive helpful tips when
                                                                you try out our products.</div>
                                                        </label>
                                                        <!--end::Label-->
                                                    </div>
                                                    <!--end::Checkbox-->
                                                </div>
                                                <!--end::Item-->
                                                <!--begin::Action buttons-->
                                                <div class="d-flex justify-content-end align-items-center mt-12">
                                                    <!--begin::Button-->
                                                    <button type="button" class="btn btn-light me-5"
                                                        id="kt_users_email_notification_cancel">Cancel</button>
                                                    <!--end::Button-->
                                                    <!--begin::Button-->
                                                    <button type="button" class="btn btn-primary"
                                                        id="kt_users_email_notification_submit">
                                                        <span class="indicator-label">Save</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span
                                                                class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                    </button>
                                                    <!--end::Button-->
                                                </div>
                                                <!--begin::Action buttons-->
                                            </form>
                                            <!--end::Form-->
                                        </div>
                                        <!--end::Card body-->
                                        <!--begin::Card footer-->
                                        <!--end::Card footer-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end:::Tab pane-->
                                <!--begin:::Tab pane-->
                                <div class="tab-pane fade" id="kt_user_view_overview_events_and_logs_tab"
                                    role="tabpanel">
                                    <!--begin::Card-->
                                    <div class="card pt-4 mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header border-0">
                                            <!--begin::Card title-->
                                            <div class="card-title">
                                                <h2>Login Sessions</h2>
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <!--begin::Filter-->
                                                <button type="button" class="btn btn-sm btn-flex btn-light-primary"
                                                    id="kt_modal_sign_out_sesions">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr077.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.3" x="4" y="11" width="12"
                                                                height="2" rx="1"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M5.86875 11.6927L7.62435 10.2297C8.09457 9.83785 8.12683 9.12683 7.69401 8.69401C7.3043 8.3043 6.67836 8.28591 6.26643 8.65206L3.34084 11.2526C2.89332 11.6504 2.89332 12.3496 3.34084 12.7474L6.26643 15.3479C6.67836 15.7141 7.3043 15.6957 7.69401 15.306C8.12683 14.8732 8.09458 14.1621 7.62435 13.7703L5.86875 12.3073C5.67684 12.1474 5.67684 11.8526 5.86875 11.6927Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M8 5V6C8 6.55228 8.44772 7 9 7C9.55228 7 10 6.55228 10 6C10 5.44772 10.4477 5 11 5H18C18.5523 5 19 5.44772 19 6V18C19 18.5523 18.5523 19 18 19H11C10.4477 19 10 18.5523 10 18C10 17.4477 9.55228 17 9 17C8.44772 17 8 17.4477 8 18V19C8 20.1046 8.89543 21 10 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3H10C8.89543 3 8 3.89543 8 5Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Sair de todas as sessões</button>
                                                <!--end::Filter-->
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body pt-0 pb-5">
                                            <!--begin::Table wrapper-->
                                            <div class="table-responsive">
                                                <!--begin::Table-->
                                                <table class="table align-middle table-row-dashed gy-5"
                                                    id="kt_table_users_login_session">
                                                    <!--begin::Table head-->
                                                    <thead class="border-bottom border-gray-200 fs-7 fw-bold">
                                                        <!--begin::Table row-->
                                                        <tr class="text-start text-muted text-uppercase gs-0">
                                                            <th class="min-w-100px">Local</th>
                                                            <th>Dispositivo</th>
                                                            <th>Endereço IP</th>
                                                            <th class="min-w-125px">Hora</th>
                                                            <th class="min-w-70px">Ações</th>
                                                        </tr>
                                                        <!--end::Table row-->
                                                    </thead>
                                                    <!--end::Table head-->
                                                    <!--begin::Table body-->
                                                    <tbody class="fs-6 fw-semibold text-gray-600">
                                                        <tr>
                                                            <!--begin::Invoice=-->
                                                            <td>Australia</td>
                                                            <!--end::Invoice=-->
                                                            <!--begin::Status=-->
                                                            <td>Chome - Windows</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Amount=-->
                                                            <td>207.46.48.64</td>
                                                            <!--end::Amount=-->
                                                            <!--begin::Date=-->
                                                            <td>23 seconds ago</td>
                                                            <!--end::Date=-->
                                                            <!--begin::Action=-->
                                                            <td>Current session</td>
                                                            <!--end::Action=-->
                                                        </tr>
                                                        <tr>
                                                            <!--begin::Invoice=-->
                                                            <td>Australia</td>
                                                            <!--end::Invoice=-->
                                                            <!--begin::Status=-->
                                                            <td>Safari - iOS</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Amount=-->
                                                            <td>207.45.45.303</td>
                                                            <!--end::Amount=-->
                                                            <!--begin::Date=-->
                                                            <td>3 days ago</td>
                                                            <!--end::Date=-->
                                                            <!--begin::Action=-->
                                                            <td>
                                                                <a href="#"
                                                                    data-kt-users-sign-out="single_user">Sign
                                                                    out</a>
                                                            </td>
                                                            <!--end::Action=-->
                                                        </tr>
                                                        <tr>
                                                            <!--begin::Invoice=-->
                                                            <td>Australia</td>
                                                            <!--end::Invoice=-->
                                                            <!--begin::Status=-->
                                                            <td>Chrome - Windows</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Amount=-->
                                                            <td>207.17.37.106</td>
                                                            <!--end::Amount=-->
                                                            <!--begin::Date=-->
                                                            <td>last week</td>
                                                            <!--end::Date=-->
                                                            <!--begin::Action=-->
                                                            <td>Expired</td>
                                                            <!--end::Action=-->
                                                        </tr>
                                                    </tbody>
                                                    <!--end::Table body-->
                                                </table>
                                                <!--end::Table-->
                                            </div>
                                            <!--end::Table wrapper-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                    <!--begin::Card-->
                                    <div class="card pt-4 mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header border-0">
                                            <!--begin::Card title-->
                                            <div class="card-title">
                                                <h2>Logs</h2>
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <!--begin::Button-->
                                                <button type="button" class="btn btn-sm btn-light-primary">
                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil021.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3"
                                                                d="M19 15C20.7 15 22 13.7 22 12C22 10.3 20.7 9 19 9C18.9 9 18.9 9 18.8 9C18.9 8.7 19 8.3 19 8C19 6.3 17.7 5 16 5C15.4 5 14.8 5.2 14.3 5.5C13.4 4 11.8 3 10 3C7.2 3 5 5.2 5 8C5 8.3 5 8.7 5.1 9H5C3.3 9 2 10.3 2 12C2 13.7 3.3 15 5 15H19Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M13 17.4V12C13 11.4 12.6 11 12 11C11.4 11 11 11.4 11 12V17.4H13Z"
                                                                fill="currentColor" />
                                                            <path opacity="0.3"
                                                                d="M8 17.4H16L12.7 20.7C12.3 21.1 11.7 21.1 11.3 20.7L8 17.4Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Download Report</button>
                                                <!--end::Button-->
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body py-0">
                                            <!--begin::Table wrapper-->
                                            <div class="table-responsive">
                                                <!--begin::Table-->
                                                <table
                                                    class="table align-middle table-row-dashed fw-semibold text-gray-600 fs-6 gy-5"
                                                    id="kt_table_users_logs">
                                                    <!--begin::Table body-->
                                                    <tbody>
                                                        <!--begin::Table row-->
                                                        <tr>
                                                            <!--begin::Badge=-->
                                                            <td class="min-w-70px">
                                                                <div class="badge badge-light-success">200 OK
                                                                </div>
                                                            </td>
                                                            <!--end::Badge=-->
                                                            <!--begin::Status=-->
                                                            <td>POST /v1/invoices/in_4172_5158/payment</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Timestamp=-->
                                                            <td class="pe-0 text-end min-w-200px">20 Dec 2023,
                                                                11:05 am</td>
                                                            <!--end::Timestamp=-->
                                                        </tr>
                                                        <!--end::Table row-->
                                                        <!--begin::Table row-->
                                                        <tr>
                                                            <!--begin::Badge=-->
                                                            <td class="min-w-70px">
                                                                <div class="badge badge-light-success">200 OK
                                                                </div>
                                                            </td>
                                                            <!--end::Badge=-->
                                                            <!--begin::Status=-->
                                                            <td>POST /v1/invoices/in_4172_5158/payment</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Timestamp=-->
                                                            <td class="pe-0 text-end min-w-200px">15 Apr 2023,
                                                                10:30 am</td>
                                                            <!--end::Timestamp=-->
                                                        </tr>
                                                        <!--end::Table row-->
                                                        <!--begin::Table row-->
                                                        <tr>
                                                            <!--begin::Badge=-->
                                                            <td class="min-w-70px">
                                                                <div class="badge badge-light-success">200 OK
                                                                </div>
                                                            </td>
                                                            <!--end::Badge=-->
                                                            <!--begin::Status=-->
                                                            <td>POST /v1/invoices/in_3436_8609/payment</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Timestamp=-->
                                                            <td class="pe-0 text-end min-w-200px">10 Mar 2023,
                                                                11:30 am</td>
                                                            <!--end::Timestamp=-->
                                                        </tr>
                                                        <!--end::Table row-->
                                                        <!--begin::Table row-->
                                                        <tr>
                                                            <!--begin::Badge=-->
                                                            <td class="min-w-70px">
                                                                <div class="badge badge-light-warning">404 WRN
                                                                </div>
                                                            </td>
                                                            <!--end::Badge=-->
                                                            <!--begin::Status=-->
                                                            <td>POST /v1/customer/c_63d9246d4b75d/not_found</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Timestamp=-->
                                                            <td class="pe-0 text-end min-w-200px">20 Jun 2023,
                                                                11:30 am</td>
                                                            <!--end::Timestamp=-->
                                                        </tr>
                                                        <!--end::Table row-->
                                                        <!--begin::Table row-->
                                                        <tr>
                                                            <!--begin::Badge=-->
                                                            <td class="min-w-70px">
                                                                <div class="badge badge-light-success">200 OK
                                                                </div>
                                                            </td>
                                                            <!--end::Badge=-->
                                                            <!--begin::Status=-->
                                                            <td>POST /v1/invoices/in_4750_9706/payment</td>
                                                            <!--end::Status=-->
                                                            <!--begin::Timestamp=-->
                                                            <td class="pe-0 text-end min-w-200px">25 Jul 2023,
                                                                5:30 pm</td>
                                                            <!--end::Timestamp=-->
                                                        </tr>
                                                        <!--end::Table row-->
                                                    </tbody>
                                                    <!--end::Table body-->
                                                </table>
                                                <!--end::Table-->
                                            </div>
                                            <!--end::Table wrapper-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                    <!--begin::Card-->
                                    <div class="card pt-4 mb-6 mb-xl-9">
                                        <!--begin::Card header-->
                                        <div class="card-header border-0">
                                            <!--begin::Card title-->
                                            <div class="card-title">
                                                <h2>Events</h2>
                                            </div>
                                            <!--end::Card title-->
                                            <!--begin::Card toolbar-->
                                            <div class="card-toolbar">
                                                <!--begin::Button-->
                                                <button type="button" class="btn btn-sm btn-light-primary">
                                                    <!--begin::Svg Icon | path: icons/duotune/files/fil021.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3"
                                                                d="M19 15C20.7 15 22 13.7 22 12C22 10.3 20.7 9 19 9C18.9 9 18.9 9 18.8 9C18.9 8.7 19 8.3 19 8C19 6.3 17.7 5 16 5C15.4 5 14.8 5.2 14.3 5.5C13.4 4 11.8 3 10 3C7.2 3 5 5.2 5 8C5 8.3 5 8.7 5.1 9H5C3.3 9 2 10.3 2 12C2 13.7 3.3 15 5 15H19Z"
                                                                fill="currentColor" />
                                                            <path
                                                                d="M13 17.4V12C13 11.4 12.6 11 12 11C11.4 11 11 11.4 11 12V17.4H13Z"
                                                                fill="currentColor" />
                                                            <path opacity="0.3"
                                                                d="M8 17.4H16L12.7 20.7C12.3 21.1 11.7 21.1 11.3 20.7L8 17.4Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->Download Report</button>
                                                <!--end::Button-->
                                            </div>
                                            <!--end::Card toolbar-->
                                        </div>
                                        <!--end::Card header-->
                                        <!--begin::Card body-->
                                        <div class="card-body py-0">
                                            <!--begin::Table-->
                                            <table
                                                class="table align-middle table-row-dashed fs-6 text-gray-600 fw-semibold gy-5"
                                                id="kt_table_customers_events">
                                                <!--begin::Table body-->
                                                <tbody>
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary me-1">Emma
                                                                Smith</a>has made payment to
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary">#XRS-45670</a>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">24 Jun
                                                            2023, 5:20 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary me-1">Emma
                                                                Smith</a>has made payment to
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary">#XRS-45670</a>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">15 Apr
                                                            2023, 5:20 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">Invoice
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary me-1">#WER-45670</a>is
                                                            <span class="badge badge-light-info">In
                                                                Progress</span>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">22 Sep
                                                            2023, 9:23 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">Invoice
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary me-1">#WER-45670</a>is
                                                            <span class="badge badge-light-info">In
                                                                Progress</span>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">25 Jul
                                                            2023, 5:20 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary me-1">Brian
                                                                Cox</a>has made payment to
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary">#OLP-45690</a>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">25 Jul
                                                            2023, 10:30 am</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">Invoice
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary me-1">#KIO-45656</a>status
                                                            has changed from
                                                            <span class="badge badge-light-succees me-1">In
                                                                Transit</span>to
                                                            <span class="badge badge-light-success">Approved</span>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">20 Jun
                                                            2023, 8:43 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">Invoice
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary me-1">#SEP-45656</a>status
                                                            has changed from
                                                            <span
                                                                class="badge badge-light-warning me-1">Pending</span>to
                                                            <span class="badge badge-light-info">In
                                                                Progress</span>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">21 Feb
                                                            2023, 8:43 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">Invoice
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary me-1">#SEP-45656</a>status
                                                            has changed from
                                                            <span
                                                                class="badge badge-light-warning me-1">Pending</span>to
                                                            <span class="badge badge-light-info">In
                                                                Progress</span>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">05 May
                                                            2023, 9:23 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary me-1">Brian
                                                                Cox</a>has made payment to
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary">#OLP-45690</a>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">15 Apr
                                                            2023, 6:05 pm</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
                                                    <!--end::Table row-->
                                                    <!--begin::Table row-->
                                                    <tr>
                                                        <!--begin::Event=-->
                                                        <td class="min-w-400px">
                                                            <a href="#"
                                                                class="text-gray-600 text-hover-primary me-1">Sean
                                                                Bean</a>has made payment to
                                                            <a href="#"
                                                                class="fw-bold text-gray-900 text-hover-primary">#XRS-45670</a>
                                                        </td>
                                                        <!--end::Event=-->
                                                        <!--begin::Timestamp=-->
                                                        <td class="pe-0 text-gray-600 text-end min-w-200px">25 Jul
                                                            2023, 11:05 am</td>
                                                        <!--end::Timestamp=-->
                                                    </tr>
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
                                <!--end:::Tab pane-->
                            </div>
                            <!--end:::Tab content-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Layout-->
                    <!--begin::Modals-->
                    <!--begin::Modal - Update user details-->
                    <div class="modal fade" id="kt_modal_update_details" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Form-->
                                <form method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data">
                                    @csrf
                                    @method('PATCH') <!-- Especifica que o método é PATCH -->
                                    @include('__massage')
                                    <!--begin::Modal header-->
                                    <div class="modal-header" id="">
                                        <!--begin::Modal title-->
                                        <h2 class="fw-bold">Atualizar Informações</h2>
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
                                    <div class="modal-body py-10 px-lg-17">
                                        <!--begin::Scroll-->
                                        <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_update_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_update_user_header" data-kt-scroll-wrappers="#kt_modal_update_user_scroll" data-kt-scroll-offset="300px">
                                            <!--begin::User toggle-->
                                            <div class="fw-bolder fs-3 rotate collapsible mb-7" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="kt_modal_update_user_user_info">Informação de Usuário
                                                <span class="ms-2 rotate-180">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </div>
                                            <!--end::User toggle-->
                                            <!--begin::User form-->
                                            <div id="kt_modal_update_user_user_info" class="collapse show">
                                                <!--begin::Input group-->
                                                <div class="mb-7 card-body text-center pt-0">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold mb-2">
                                                        <span>Atualizar Avatar</span>
                                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Arquivos permitidos: png, jpg, jpeg."></i>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input group-->
                                                    <div class="fv-row mb-7">
                                                        <!--begin::Label-->
                                                        <!--begin::Image placeholder-->
                                                        <style>.image-input-placeholder { background-image: url('/assets/media/svg/files/blank-image.svg'); } [data-bs-theme="dark"] .image-input-placeholder { background-image: url('/assets/media/svg/files/blank-image-dark.svg'); }</style>
                                                        <!--end::Image placeholder-->
                                                        <!--begin::Image input-->
                                                        <div class="image-input image-input-outline image-input-placeholder"
                                                            data-kt-image-input="true">
                                                            <!--begin::Preview existing avatar-->
                                                            <div class="image-input-wrapper w-150px h-150px"
                                                                style="background-image: url({{ route('file', ['path' => $user->avatar]) }});">
                                                            </div>
                                                            <!--end::Preview existing avatar-->
                                                            <!--begin::Label-->
                                                            <label
                                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                data-kt-image-input-action="change"
                                                                data-bs-toggle="tooltip" title="Change avatar">
                                                                <i class="bi bi-pencil-fill fs-7"></i>
                                                                <!--begin::Inputs-->
                                                                <input type="file" name="avatar"
                                                                    accept=".png, .jpg, .jpeg" />
                                                                <!--end::Inputs-->
                                                            </label>
                                                            <!--end::Label-->
                                                            <!--begin::Cancel-->
                                                            <span
                                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                data-kt-image-input-action="cancel"
                                                                data-bs-toggle="tooltip" title="Cancel avatar">
                                                                <i class="bi bi-x fs-2"></i>
                                                            </span>
                                                            <!--end::Cancel-->
                                                            <!--begin::Remove-->
                                                            <span
                                                                class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                                data-kt-image-input-action="remove"
                                                                data-bs-toggle="tooltip" title="Remove avatar">
                                                                <i class="bi bi-x fs-2"></i>
                                                            </span>
                                                            <!--end::Remove-->
                                                        </div>
                                                        <!--end::Image input-->
                                                        <!--begin::Hint-->
                                                        <div class="text-muted fs-7">Somente arquivos de imagem nos formatos *.png, *.jpg e *.jpeg são aceitos.</div>
                                                        <!--end::Hint-->
                                                    </div>
                                                    <!--end::Input group-->
                                                    @foreach ($errors->all() as $error)
                                                    <div class="alert alert-danger mt-2">
                                                        {{ $error }}
                                                    </div>
                                                    @endforeach
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="fv-row mb-7">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold mb-2">Nome</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input type="text" class="form-control form-control-solid" placeholder="" name="name" value="{{ old('name', $user->name) }}" />
                                                    <!--end::Input-->
                                                    @if ($errors->has('name'))
                                                        <div class="text-danger">{{ $errors->first('name') }}</div>
                                                    @endif
                                                </div>

                                                <div class="fv-row mb-7">
                                                    <!--end::Input-->
                                                    <x-input-label for="email" class="fs-6 fw-semibold form-label mb-2"
                                                        :value="__('Email')" />
                                                    <x-text-input id="email" name="email" type="email"
                                                        class="form-control form-control-solid" :value="old('email', $user->email)" required
                                                        autocomplete="username" />
                                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                                        <div>
                                                            <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                                                {{ __('Your email address is unverified.') }}

                                                                <button form="send-verification"
                                                                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                                                    {{ __('Click here to re-send the verification email.') }}
                                                                </button>
                                                            </p>

                                                            @if (session('status') === 'verification-link-sent')
                                                                <p
                                                                    class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                                                    {{ __('A new verification link has been sent to your email address.') }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                            </div>
                                            <!--end::User form-->
                                            <!--begin::Address toggle-->
                                            <div class="fw-bolder fs-3 rotate collapsible mb-7" data-bs-toggle="collapse" href="#kt_modal_update_user_address" role="button" aria-expanded="false" aria-controls="kt_modal_update_user_address">Detalhes de Endereço
                                                <span class="ms-2 rotate-180">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                </span>
                                            </div>
                                            <!--end::Address toggle-->
                                            <!--begin::Address form-->
                                            <div id="kt_modal_update_user_address" class="collapse show">
                                                <!--begin::Input group-->
                                                <div class="d-flex flex-column mb-7 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold mb-2">Endereço</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-solid" placeholder="" name="address1" value="{{ old('address1', $user->address1) }}" />
                                                    <!--end::Input-->
                                                    @if ($errors->has('address1'))
                                                        <div class="text-danger">{{ $errors->first('address1') }}</div>
                                                    @endif
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="row g-9 mb-7">
                                                    <!--begin::Col-->
                                                    <div class="col-md-6 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold mb-2">Rua</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input class="form-control form-control-solid" placeholder="" name="state" value="{{ old('state', $user->state) }}" />
                                                        <!--end::Input-->
                                                        @if ($errors->has('state'))
                                                            <div class="text-danger">{{ $errors->first('state') }}</div>
                                                        @endif
                                                    </div>
                                                    <!--end::Col-->
                                                    <!--begin::Col-->
                                                    <div class="col-md-6 fv-row">
                                                        <!--begin::Label-->
                                                        <label class="fs-6 fw-semibold mb-2">CEP</label>
                                                        <!--end::Label-->
                                                        <!--begin::Input-->
                                                        <input class="form-control form-control-solid" placeholder="" name="cep" value="{{ old('cep', $user->cep) }}" />
                                                        <!--end::Input-->
                                                        @if ($errors->has('cep'))
                                                            <div class="text-danger">{{ $errors->first('cep') }}</div>
                                                        @endif
                                                    </div>
                                                    <!--end::Col-->
                                                </div>
                                                <!--end::Input group-->
                                                <!--begin::Input group-->
                                                <div class="d-flex flex-column mb-7 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold mb-2">Cidade</label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <input class="form-control form-control-solid" placeholder="" name="city" value="{{ old('city', $user->city) }}" />
                                                    <!--end::Input-->
                                                    @if ($errors->has('city'))
                                                        <div class="text-danger">{{ $errors->first('city') }}</div>
                                                    @endif
                                                </div>
                                                <!--end::Input group-->

                                                <!--begin::Input group-->
                                                <div class="d-flex flex-column mb-7 fv-row">
                                                    <!--begin::Label-->
                                                    <label class="fs-6 fw-semibold mb-2">
                                                        <span>Estado</span>
                                                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="Country of origination"></i>
                                                    </label>
                                                    <!--end::Label-->
                                                    <!--begin::Input-->
                                                    <select name="uf" aria-label="Select a State" data-control="select2" data-placeholder="Select a State..." class="form-select form-select-solid" >
                                                        <option></option>
                                                        <option value="AC" {{ old('uf', $user->uf) == 'AC' ? 'selected' : '' }}>Acre</option>
                                                        <option value="AL" {{ old('uf', $user->uf) == 'AL' ? 'selected' : '' }}>Alagoas</option>
                                                        <option value="AP" {{ old('uf', $user->uf) == 'AP' ? 'selected' : '' }}>Amapá</option>
                                                        <option value="AM" {{ old('uf', $user->uf) == 'AM' ? 'selected' : '' }}>Amazonas</option>
                                                        <option value="BA" {{ old('uf', $user->uf) == 'BA' ? 'selected' : '' }}>Bahia</option>
                                                        <option value="CE" {{ old('uf', $user->uf) == 'CE' ? 'selected' : '' }}>Ceará</option>
                                                        <option value="DF" {{ old('uf', $user->uf) == 'DF' ? 'selected' : '' }}>Distrito Federal</option>
                                                        <option value="ES" {{ old('uf', $user->uf) == 'ES' ? 'selected' : '' }}>Espírito Santo</option>
                                                        <option value="GO" {{ old('uf', $user->uf) == 'GO' ? 'selected' : '' }}>Goiás</option>
                                                        <option value="MA" {{ old('uf', $user->uf) == 'MA' ? 'selected' : '' }}>Maranhão</option>
                                                        <option value="MT" {{ old('uf', $user->uf) == 'MT' ? 'selected' : '' }}>Mato Grosso</option>
                                                        <option value="MS" {{ old('uf', $user->uf) == 'MS' ? 'selected' : '' }}>Mato Grosso do Sul</option>
                                                        <option value="MG" {{ old('uf', $user->uf) == 'MG' ? 'selected' : '' }}>Minas Gerais</option>
                                                        <option value="PA" {{ old('uf', $user->uf) == 'PA' ? 'selected' : '' }}>Pará</option>
                                                        <option value="PB" {{ old('uf', $user->uf) == 'PB' ? 'selected' : '' }}>Paraíba</option>
                                                        <option value="PR" {{ old('uf', $user->uf) == 'PR' ? 'selected' : '' }}>Paraná</option>
                                                        <option value="PE" {{ old('uf', $user->uf) == 'PE' ? 'selected' : '' }}>Pernambuco</option>
                                                        <option value="PI" {{ old('uf', $user->uf) == 'PI' ? 'selected' : '' }}>Piauí</option>
                                                        <option value="RJ" {{ old('uf', $user->uf) == 'RJ' ? 'selected' : '' }}>Rio de Janeiro</option>
                                                        <option value="RN" {{ old('uf', $user->uf) == 'RN' ? 'selected' : '' }}>Rio Grande do Norte</option>
                                                        <option value="RS" {{ old('uf', $user->uf) == 'RS' ? 'selected' : '' }}>Rio Grande do Sul</option>
                                                        <option value="RO" {{ old('uf', $user->uf) == 'RO' ? 'selected' : '' }}>Rondônia</option>
                                                        <option value="RR" {{ old('uf', $user->uf) == 'RR' ? 'selected' : '' }}>Roraima</option>
                                                        <option value="SC" {{ old('uf', $user->uf) == 'SC' ? 'selected' : '' }}>Santa Catarina</option>
                                                        <option value="SP" {{ old('uf', $user->uf) == 'SP' ? 'selected' : '' }}>São Paulo</option>
                                                        <option value="SE" {{ old('uf', $user->uf) == 'SE' ? 'selected' : '' }}>Sergipe</option>
                                                        <option value="TO" {{ old('uf', $user->uf) == 'TO' ? 'selected' : '' }}>Tocantins</option>
                                                    </select>
                                                    <!--end::Input-->
                                                    @if ($errors->has('uf'))
                                                        <div class="text-danger">{{ $errors->first('uf') }}</div>
                                                    @endif
                                                </div>
                                                <!--end::Input group-->
                                            </div>
                                            <!--end::Address form-->
                                        </div>
                                        <!--end::Scroll-->
                                    </div>
                                    <!--end::Modal body-->
                                    <!--begin::Modal footer-->
                                    <div class="modal-footer flex-center">
                                        <!--begin::Button-->
                                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Descartar</button>
                                        <!--end::Button-->
                                        <!--begin::Button-->
                                        <button type="submit" class="btn btn-primary">
                                            <span class="indicator-label">Salvar</span>
                                        </button>
                                        <!--end::Button-->
                                    </div>
                                    <!--end::Modal footer-->
                                </form>

                                <!--end::Form-->
                            </div>
                        </div>
                    </div>
                    <!--end::Modal - Update user details-->
                    <!--begin::Modal - Add schedule-->
                    <div class="modal fade" id="kt_modal_add_schedule" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Add an Event</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
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
                                    <form id="kt_modal_add_schedule_form" class="form" action="#">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-semibold form-label mb-2">Event
                                                Name</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="text" class="form-control form-control-solid"
                                                name="event_name" value="" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-2">
                                                <span class="required">Date & Time</span>
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                    data-bs-toggle="popover" data-bs-trigger="hover"
                                                    data-bs-html="true" data-bs-content="Select a date & time."></i>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input class="form-control form-control-solid"
                                                placeholder="Pick date & time" name="event_datetime"
                                                id="kt_modal_add_schedule_datepicker" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-semibold form-label mb-2">Event
                                                Organiser</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="text" class="form-control form-control-solid"
                                                name="event_org" value="" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-semibold form-label mb-2">Send Event
                                                Details To</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input id="kt_modal_add_schedule_tagify" type="text"
                                                class="form-control form-control-solid" name="event_invitees"
                                                value="smith@kpmg.com, melody@altbox.com" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button type="reset" class="btn btn-light me-3"
                                                data-kt-users-modal-action="cancel">Discard</button>
                                            <button type="submit" class="btn btn-primary"
                                                data-kt-users-modal-action="submit">
                                                <span class="indicator-label">Submit</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
                    <!--end::Modal - Add schedule-->
                    <!--begin::Modal - Add task-->
                    <div class="modal fade" id="kt_modal_add_task" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Add a Task</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
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
                                    <form id="kt_modal_add_task_form" class="form" action="#">
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="required fs-6 fw-semibold form-label mb-2">Task
                                                Name</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="text" class="form-control form-control-solid"
                                                name="task_name" value="" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-2">
                                                <span class="required">Task Due Date</span>
                                                <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                    data-bs-toggle="popover" data-bs-trigger="hover"
                                                    data-bs-html="true" data-bs-content="Select a due date."></i>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input class="form-control form-control-solid" placeholder="Pick date"
                                                name="task_duedate" id="kt_modal_add_task_datepicker" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-2">Task
                                                Description</label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <textarea class="form-control form-control-solid rounded-3"></textarea>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button type="reset" class="btn btn-light me-3"
                                                data-kt-users-modal-action="cancel">Sair</button>
                                            <button type="submit" class="btn btn-primary"
                                                data-kt-users-modal-action="submit">
                                                <span class="indicator-label">Salvar</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
                    <!--begin::Modal - Update email-->
                    <div class="modal fade" id="kt_modal_update_email" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Atualize Nome e E-mail</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
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
                                    <form method="post" action="{{ route('profile.update') }}"
                                        class="mt-6 space-y-6">
                                        @csrf
                                        @method('patch')
                                        <!--begin::Notice-->
                                        <!--begin::Notice-->
                                        <div
                                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                            <!--begin::Icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                                            <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                        rx="10" fill="currentColor" />
                                                    <rect x="11" y="14" width="7" height="2"
                                                        rx="1" transform="rotate(-90 11 14)"
                                                        fill="currentColor" />
                                                    <rect x="11" y="17" width="2" height="2"
                                                        rx="1" transform="rotate(-90 11 17)"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Icon-->
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <!--begin::Content-->
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">ATENÇÃo: é necessário um endereço
                                                        de E-mail
                                                        válido para concluir a verificação.
                                                    </div>
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                        <!--end::Notice-->
                                        <!--end::Notice-->
                                        <!--begin::Input group-->

                                        <div class="fv-row mb-7">
                                            <x-input-label for="name" class="fs-6 fw-semibold form-label mb-2"
                                                :value="__('Name')" />
                                            <x-text-input id="name" name="name" type="text"
                                                class="form-control form-control-solid" :value="old('name', $user->name)" required
                                                autofocus autocomplete="name" />
                                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                        </div>

                                        <div class="fv-row mb-7">
                                            <!--end::Input-->
                                            <x-input-label for="email" class="fs-6 fw-semibold form-label mb-2"
                                                :value="__('Email')" />
                                            <x-text-input id="email" name="email" type="email"
                                                class="form-control form-control-solid" :value="old('email', $user->email)" required
                                                autocomplete="username" />
                                            <x-input-error class="mt-2" :messages="$errors->get('email')" />

                                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                                <div>
                                                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                                                        {{ __('Your email address is unverified.') }}

                                                        <button form="send-verification"
                                                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                                            {{ __('Click here to re-send the verification email.') }}
                                                        </button>
                                                    </p>

                                                    @if (session('status') === 'verification-link-sent')
                                                        <p
                                                            class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                                            {{ __('A new verification link has been sent to your email address.') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="flex items-center gap-4">
                                            <button type="button" class="btn btn-light me-3"
                                                data-bs-dismiss="modal">Sair</button>

                                            <x-primary-button
                                                class="btn btn-primary">{{ __('Salvar') }}</x-primary-button>

                                            @if (session('status') === 'profile-updated')
                                                <p x-data="{ show: true }" x-show="show" x-transition
                                                    x-init="setTimeout(() => show = false, 2000)"
                                                    class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ __('Saved.') }}</p>
                                            @endif
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
                    <!--end::Modal - Update email-->
                    <!--begin::Modal - Update password-->
                    <div class="modal fade" id="kt_modal_update_password" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Atualizar Senha</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
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
                                    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
                                        @csrf
                                        @method('put')
                                        <!--begin::Input group=-->
                                        <div class="fv-row mb-10">
                                            <x-input-label class="required form-label fs-6 mb-2"
                                                for="update_password_current_password" :value="__('Senha Atual')" />
                                            <x-text-input class="form-control form-control-lg form-control-solid"
                                                id="update_password_current_password" name="current_password"
                                                type="password" autocomplete="current-password" />
                                            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                                        </div>
                                        <!--end::Input group=-->
                                        <!--begin::Input group-->
                                        <div class="mb-10 fv-row" data-kt-password-meter="true">
                                            <!--begin::Wrapper-->
                                            <div class="mb-1">
                                                <!--begin::Label-->
                                                <x-input-label class="form-label fw-semibold fs-6 mb-2"
                                                    for="update_password_password" :value="__('Nova Senha')" />
                                                <!--end::Label-->
                                                <!--begin::Input wrapper-->
                                                <div class="position-relative mb-3">
                                                    <x-text-input id="update_password_password" name="password"
                                                        type="password"
                                                        class="form-control form-control-lg form-control-solid"
                                                        autocomplete="new-password" />
                                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                                                    <span
                                                        class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                                        data-kt-password-meter-control="visibility">
                                                        <i class="bi bi-eye-slash fs-2"></i>
                                                        <i class="bi bi-eye fs-2 d-none"></i>
                                                    </span>

                                                </div>
                                                <!--end::Input wrapper-->
                                                <!--begin::Meter-->
                                                <div class="d-flex align-items-center mb-3"
                                                    data-kt-password-meter-control="highlight">
                                                    <div
                                                        class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                    </div>
                                                    <div
                                                        class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                    </div>
                                                    <div
                                                        class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2">
                                                    </div>
                                                    <div
                                                        class="flex-grow-1 bg-secondary bg-active-success rounded h-5px">
                                                    </div>
                                                </div>
                                                <!--end::Meter-->
                                            </div>
                                            <!--end::Wrapper-->
                                            <!--begin::Hint-->
                                            <div class="text-muted">Utilize 8 ou mais caracteres com uma mistura de
                                                letras, números e símbolos.</div>
                                            <!--end::Hint-->
                                        </div>
                                        <!--end::Input group=-->
                                        <!--begin::Input group=-->
                                        <div class="fv-row mb-10">
                                            <x-input-label class="required form-label fs-6 mb-2"
                                                for="update_password_password_confirmation" :value="__('Confirme a Senha')" />
                                            <x-text-input id="update_password_password_confirmation"
                                                name="password_confirmation" type="password"
                                                class="form-control form-control-lg form-control-solid"
                                                autocomplete="new-password" />
                                            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                                        </div>
                                        <!--end::Input group=-->
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button type="button" class="btn btn-light me-3"
                                                data-bs-dismiss="modal">Sair</button>

                                            <x-primary-button
                                                class="btn btn-primary">{{ __('Salvar') }}</x-primary-button>

                                            @if (session('status') === 'password-updated')
                                                <p x-data="{ show: true }" x-show="show" x-transition
                                                    x-init="setTimeout(() => show = false, 2000)"
                                                    class="text-sm text-gray-600 dark:text-gray-400">
                                                    {{ __('Saved.') }}</p>
                                            @endif
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
                    <!--end::Modal - Update password-->
                    <!--begin::Modal - Update role-->
                    <div class="modal fade" id="kt_modal_update_role" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Update User Role</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
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
                                    <form id="kt_modal_update_role_form" class="form" action="#">
                                        <!--begin::Notice-->
                                        <!--begin::Notice-->
                                        <div
                                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                            <!--begin::Icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                                            <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                        rx="10" fill="currentColor" />
                                                    <rect x="11" y="14" width="7" height="2"
                                                        rx="1" transform="rotate(-90 11 14)"
                                                        fill="currentColor" />
                                                    <rect x="11" y="17" width="2" height="2"
                                                        rx="1" transform="rotate(-90 11 17)"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Icon-->
                                            <!--begin::Wrapper-->
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <!--begin::Content-->
                                                <div class="fw-semibold">
                                                    <div class="fs-6 text-gray-700">Observe que, ao reduzir a
                                                        classificação
                                                        de uma função de usuário, esse usuário perderá todos os
                                                        privilégios
                                                        atribuídos à função anterior.</div>
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Wrapper-->
                                        </div>
                                        <!--end::Notice-->
                                        <!--end::Notice-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-5">
                                                <span class="required">Selecione uma função de usuário</span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input row-->
                                            <div class="d-flex">
                                                <!--begin::Radio-->
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input me-3" name="user_role"
                                                        type="radio" value="0"
                                                        id="kt_modal_update_role_option_0" checked='checked' />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <label class="form-check-label"
                                                        for="kt_modal_update_role_option_0">
                                                        <div class="fw-bold text-gray-800">Global</div>
                                                        <div class="text-gray-600">Melhor para desenvolvedores ou
                                                            pessoas que usam
                                                            principalmente a API</div>
                                                    </label>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input row-->
                                            <div class='separator separator-dashed my-5'></div>
                                            <!--begin::Input row-->
                                            <div class="d-flex">
                                                <!--begin::Radio-->
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input me-3" name="user_role"
                                                        type="radio" value="1"
                                                        id="kt_modal_update_role_option_1" />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <label class="form-check-label"
                                                        for="kt_modal_update_role_option_1">
                                                        <div class="fw-bold text-gray-800">Administrador</div>
                                                        <div class="text-gray-600">Ideal para pessoas que
                                                            precisam de acesso total aos dados da empresa.</div>
                                                    </label>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input row-->
                                            <div class='separator separator-dashed my-5'></div>
                                            <!--begin::Input row-->
                                            <div class="d-flex">
                                                <!--begin::Radio-->
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input me-3" name="user_role"
                                                        type="radio" value="2"
                                                        id="kt_modal_update_role_option_2" />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <label class="form-check-label"
                                                        for="kt_modal_update_role_option_2">
                                                        <div class="fw-bold text-gray-800">Admin User</div>
                                                        <div class="text-gray-600">Ideal para funcionários
                                                            que gerencia as filias</div>
                                                    </label>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input row-->
                                            <div class='separator separator-dashed my-5'></div>
                                            <!--begin::Input row-->
                                            <div class="d-flex">
                                                <!--begin::Radio-->
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <!--begin::Input-->
                                                    <input class="form-check-input me-3" name="user_role"
                                                        type="radio" value="3"
                                                        id="kt_modal_update_role_option_3" />
                                                    <!--end::Input-->
                                                    <!--begin::Label-->
                                                    <label class="form-check-label"
                                                        for="kt_modal_update_role_option_3">
                                                        <div class="fw-bold text-gray-800">Usuários Comuns</div>
                                                        <div class="text-gray-600">Para usuários que tratam
                                                            dos dados da organização</div>
                                                    </label>
                                                    <!--end::Label-->
                                                </div>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input row-->
                                            <div class='separator separator-dashed my-5'></div>
                                            <!--begin::Input row-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button type="button" class="btn btn-light me-3"
                                                data-bs-dismiss="modal">Sair</button>

                                            <button type="submit" class="btn btn-primary"
                                                data-kt-users-modal-action="submit">
                                                <span class="indicator-label">Submit</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
                    <!--end::Modal - Update role-->
                    <!--begin::Modal - Add task-->
                    <div class="modal fade" id="kt_modal_add_auth_app" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Add Authenticator App</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <!--end::Modal header-->
                                <!--begin::Modal body-->
                                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                                    <!--begin::Content-->
                                    <div class="fw-bold d-flex flex-column justify-content-center mb-5">
                                        <!--begin::Label-->
                                        <div class="text-center mb-5" data-kt-add-auth-action="qr-code-label">
                                            Download the
                                            <a href="#">Authenticator app</a>, add a new account, then scan
                                            this barcode to set up your account.
                                        </div>
                                        <div class="text-center mb-5 d-none"
                                            data-kt-add-auth-action="text-code-label">Download the
                                            <a href="#">Authenticator app</a>, add a new account, then enter
                                            this code to set up your account.
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::QR code-->
                                        <div class="d-flex flex-center" data-kt-add-auth-action="qr-code">
                                            <img src="/assets/media/misc/qr.png" alt="Scan this QR code" />
                                        </div>
                                        <!--end::QR code-->
                                        <!--begin::Text code-->
                                        <div class="border rounded p-5 d-flex flex-center d-none"
                                            data-kt-add-auth-action="text-code">
                                            <div class="fs-1">gi2kdnb54is709j</div>
                                        </div>
                                        <!--end::Text code-->
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Action-->
                                    <div class="d-flex flex-center">
                                        <div class="btn btn-light-primary"
                                            data-kt-add-auth-action="text-code-button">Enter code manually</div>
                                        <div class="btn btn-light-primary d-none"
                                            data-kt-add-auth-action="qr-code-button">Scan barcode instead</div>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Modal body-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - Add task-->
                    <!--begin::Modal - Add task-->
                    <div class="modal fade" id="kt_modal_add_one_time_password" tabindex="-1"
                        aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header">
                                    <!--begin::Modal title-->
                                    <h2 class="fw-bold">Enable One Time Password</h2>
                                    <!--end::Modal title-->
                                    <!--begin::Close-->
                                    <div class="btn btn-icon btn-sm btn-active-icon-primary"
                                        data-kt-users-modal-action="close">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16"
                                                    height="2" rx="1"
                                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2"
                                                    rx="1" transform="rotate(45 7.41422 6)"
                                                    fill="currentColor" />
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
                                    <form class="form" id="kt_modal_add_one_time_password_form">
                                        <!--begin::Label-->
                                        <div class="fw-bold mb-9">Enter the new phone number to receive an SMS to
                                            when you log in.</div>
                                        <!--end::Label-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-2">
                                                <span class="required">Mobile number</span>
                                                <i class="fas fa-exclamation-circle ms-1 fs-7"
                                                    data-bs-toggle="tooltip"
                                                    title="A valid mobile number is required to receive the one-time password to validate your account login."></i>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="text" class="form-control form-control-solid"
                                                name="otp_mobile_number" placeholder="+6123 456 789"
                                                value="" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Separator-->
                                        <div class="separator saperator-dashed my-5"></div>
                                        <!--end::Separator-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-2">
                                                <span class="required">Email</span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="email" class="form-control form-control-solid"
                                                name="otp_email" value="smith@kpmg.com" readonly="readonly" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Input group-->
                                        <div class="fv-row mb-7">
                                            <!--begin::Label-->
                                            <label class="fs-6 fw-semibold form-label mb-2">
                                                <span class="required">Confirm password</span>
                                            </label>
                                            <!--end::Label-->
                                            <!--begin::Input-->
                                            <input type="password" class="form-control form-control-solid"
                                                name="otp_confirm_password" value="" />
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Input group-->
                                        <!--begin::Actions-->
                                        <div class="text-center pt-15">
                                            <button type="reset" class="btn btn-light me-3"
                                                data-kt-users-modal-action="cancel">Cancel</button>
                                            <button type="submit" class="btn btn-primary"
                                                data-kt-users-modal-action="submit">
                                                <span class="indicator-label">Submit</span>
                                                <span class="indicator-progress">Please wait...
                                                    <span
                                                        class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
                    <!--end::Modals-->
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
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->

<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ url('assets/js/custom/apps/user-management/users/view/view.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/update-details.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/add-schedule.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/add-task.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/update-email.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/update-password.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/update-role.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/add-auth-app.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/user-management/users/view/add-one-time-password.js') }}"></script>
<script src="{{ url('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/create-campaign.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/users-search.js') }}"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->

