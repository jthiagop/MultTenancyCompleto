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
                            Project Users</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted"></li>
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
                    <!--begin::Navbar-->
                    <div class="card mb-8">
                        <div class="card-body pt-9 pb-0">
                            <!--begin::Details-->
                            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                <!--begin::Image-->
                                <div
                                    class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                                    <img class="mw-50px mw-lg-75px" src="/tenancy/assets/media/svg/brand-logos/volicity-9.svg"
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
                                                    class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">CRM
                                                    Dashboard</a>
                                                <span class="badge badge-light-success me-auto">In Progress</span>
                                            </div>
                                            <!--end::Status-->
                                            <!--begin::Description-->
                                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">#1 Tool to
                                                get started with Web Apps any Kind & size</div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Actions-->
                                        <div class="d-flex mb-4">
                                            <a href="#"
                                                class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">Add
                                                User</a>
                                            <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                                                data-bs-target="#kt_modal_new_target">Add Target</a>
                                            <!--begin::Menu-->
                                            <div class="me-0">
                                                <button
                                                    class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="bi bi-three-dots fs-3"></i>
                                                </button>
                                                <!--begin::Menu 3-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Payments</div>
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link flex-stack px-3">Create
                                                            Payment
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                                data-bs-toggle="tooltip"
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
                                                                <a href="#"
                                                                    class="menu-link px-3">Statements</a>
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
                                                                            type="checkbox" value="1"
                                                                            checked="checked" name="notifications" />
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
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="75">0</div>
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
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="15000" data-kt-countup-prefix="$">0
                                                    </div>
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
                                                <span
                                                    class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michael Eberon">
                                                <img alt="Pic" src="/tenancy/assets/media/avatars/300-11.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michelle Swanston">
                                                <img alt="Pic" src="/tenancy/assets/media/avatars/300-7.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Francis Mitcham">
                                                <img alt="Pic" src="/tenancy/assets/media/avatars/300-20.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Susan Redwood">
                                                <span
                                                    class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Melody Macy">
                                                <img alt="Pic" src="/tenancy/assets/media/avatars/300-2.jpg" />
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
                                                <img alt="Pic" src="/tenancy/assets/media/avatars/300-12.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::All users-->
                                            <a href="#" class="symbol symbol-35px symbol-circle"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
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
                                    <a class="nav-link text-active-primary py-5 me-6"
                                        href="../../demo1/dist/apps/projects/project.html">Overview</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6"
                                        href="../../demo1/dist/apps/projects/targets.html">Targets</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6"
                                        href="../../demo1/dist/apps/projects/budget.html">Budget</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6 active"
                                        href="../../demo1/dist/apps/projects/users.html">Users</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6"
                                        href="../../demo1/dist/apps/projects/files.html">Files</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6"
                                        href="../../demo1/dist/apps/projects/activity.html">Activity</a>
                                </li>
                                <!--end::Nav item-->
                                <!--begin::Nav item-->
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary py-5 me-6"
                                        href="../../demo1/dist/apps/projects/settings.html">Settings</a>
                                </li>
                                <!--end::Nav item-->
                            </ul>
                            <!--end::Nav-->
                        </div>
                    </div>
                    <!--end::Navbar-->
                    <!--begin::Toolbar-->
                    <div class="d-flex flex-wrap flex-stack pb-7">
                        <!--begin::Title-->
                        <div class="d-flex flex-wrap align-items-center my-1">
                            <h3 class="fw-bold me-5 my-1">Users (38)</h3>
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-3 position-absolute ms-3">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                            rx="1" transform="rotate(45 17.0365 15.1223)"
                                            fill="currentColor" />
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" id="kt_filter_search"
                                    class="form-control form-control-sm border-body bg-body w-150px ps-10"
                                    placeholder="Search" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--end::Title-->
                        <!--begin::Controls-->
                        <div class="d-flex flex-wrap my-1">
                            <!--begin::Tab nav-->
                            <ul class="nav nav-pills me-6 mb-2 mb-sm-0">
                                <li class="nav-item m-0">
                                    <a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary me-3 active"
                                        data-bs-toggle="tab" href="#kt_project_users_card_pane">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen024.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px"
                                                viewBox="0 0 24 24">
                                                <g stroke="none" stroke-width="1" fill="none"
                                                    fill-rule="evenodd">
                                                    <rect x="5" y="5" width="5" height="5" rx="1"
                                                        fill="currentColor" />
                                                    <rect x="14" y="5" width="5" height="5" rx="1"
                                                        fill="currentColor" opacity="0.3" />
                                                    <rect x="5" y="14" width="5" height="5" rx="1"
                                                        fill="currentColor" opacity="0.3" />
                                                    <rect x="14" y="14" width="5" height="5" rx="1"
                                                        fill="currentColor" opacity="0.3" />
                                                </g>
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </li>
                                <li class="nav-item m-0">
                                    <a class="btn btn-sm btn-icon btn-light btn-color-muted btn-active-primary"
                                        data-bs-toggle="tab" href="#kt_project_users_table_pane">
                                        <!--begin::Svg Icon | path: icons/duotune/abstract/abs015.svg-->
                                        <span class="svg-icon svg-icon-2">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                                                    fill="currentColor" />
                                                <path opacity="0.3"
                                                    d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </a>
                                </li>
                            </ul>
                            <!--end::Tab nav-->
                            <!--begin::Actions-->
                            <div class="d-flex my-0">
                                <!--begin::Select-->
                                <select name="status" data-control="select2" data-hide-search="true"
                                    data-placeholder="Filter"
                                    class="form-select form-select-sm border-body bg-body w-150px me-5">
                                    <option value="1">Recently Updated</option>
                                    <option value="2">Last Month</option>
                                    <option value="3">Last Quarter</option>
                                    <option value="4">Last Year</option>
                                </select>
                                <!--end::Select-->
                                <!--begin::Select-->
                                <select name="status" data-control="select2" data-hide-search="true"
                                    data-placeholder="Export"
                                    class="form-select form-select-sm border-body bg-body w-100px">
                                    <option value="1">Excel</option>
                                    <option value="1">PDF</option>
                                    <option value="2">Print</option>
                                </select>
                                <!--end::Select-->
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Controls-->
                    </div>
                    <!--end::Toolbar-->
                    <!--begin::Tab Content-->
                    <div class="tab-content">
                        <!--begin::Tab pane-->
                        <div id="kt_project_users_card_pane" class="tab-pane fade show active">
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9">
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <img src="/tenancy/assets/media//avatars/300-2.jpg" alt="image" />
                                                <div
                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3">
                                                </div>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Karina
                                                Clark</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Art Director at Novica Co.
                                            </div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <span
                                                    class="symbol-label fs-2x fw-semibold text-primary bg-light-primary">S</span>
                                                <div
                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3">
                                                </div>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Sean
                                                Bean</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Developer at Loop Inc</div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <img src="/tenancy/assets/media//avatars/300-1.jpg" alt="image" />
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Alan
                                                Johnson</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Web Designer at Nextop Ltd.
                                            </div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <img src="/tenancy/assets/media//avatars/300-14.jpg" alt="image" />
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Robert
                                                Doe</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Marketing Analytic at Avito
                                                Ltd.</div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <img src="/tenancy/assets/media//avatars/300-6.jpg" alt="image" />
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Olivia
                                                Wild</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Art Director at Seal Inc.</div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <span
                                                    class="symbol-label fs-2x fw-semibold text-warning bg-light-warning">A</span>
                                                <div
                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3">
                                                </div>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Adam
                                                Williams</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">System Arcitect at Wolto Co.
                                            </div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <span
                                                    class="symbol-label fs-2x fw-semibold text-info bg-light-info">P</span>
                                                <div
                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3">
                                                </div>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Peter
                                                Marcus</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Art Director at Novica Co.
                                            </div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <span
                                                    class="symbol-label fs-2x fw-semibold text-success bg-light-success">N</span>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Neil
                                                Owen</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Accountant at Numbers Co.</div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 col-xxl-4">
                                    <!--begin::Card-->
                                    <div class="card">
                                        <!--begin::Card body-->
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <!--begin::Avatar-->
                                            <div class="symbol symbol-65px symbol-circle mb-5">
                                                <img src="/tenancy/assets/media//avatars/300-12.jpg" alt="image" />
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::Name-->
                                            <a href="#"
                                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">Benjamin
                                                Jacob</a>
                                            <!--end::Name-->
                                            <!--begin::Position-->
                                            <div class="fw-semibold text-gray-400 mb-6">Art Director at Novica Co.
                                            </div>
                                            <!--end::Position-->
                                            <!--begin::Info-->
                                            <div class="d-flex flex-center flex-wrap">
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$14,560</div>
                                                    <div class="fw-semibold text-gray-400">Earnings</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">23</div>
                                                    <div class="fw-semibold text-gray-400">Tasks</div>
                                                </div>
                                                <!--end::Stats-->
                                                <!--begin::Stats-->
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-80px py-3 px-4 mx-2 mb-3">
                                                    <div class="fs-6 fw-bold text-gray-700">$236,400</div>
                                                    <div class="fw-semibold text-gray-400">Sales</div>
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                    <!--end::Card-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Row-->
                            <!--begin::Pagination-->
                            <div class="d-flex flex-stack flex-wrap pt-10">
                                <div class="fs-6 fw-semibold text-gray-700">Showing 1 to 10 of 50 entries</div>
                                <!--begin::Pages-->
                                <ul class="pagination">
                                    <li class="page-item previous">
                                        <a href="#" class="page-link">
                                            <i class="previous"></i>
                                        </a>
                                    </li>
                                    <li class="page-item active">
                                        <a href="#" class="page-link">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">2</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">3</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">4</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">5</a>
                                    </li>
                                    <li class="page-item">
                                        <a href="#" class="page-link">6</a>
                                    </li>
                                    <li class="page-item next">
                                        <a href="#" class="page-link">
                                            <i class="next"></i>
                                        </a>
                                    </li>
                                </ul>
                                <!--end::Pages-->
                            </div>
                            <!--end::Pagination-->
                        </div>
                        <!--end::Tab pane-->
                        <!--begin::Tab pane-->
                        <div id="kt_project_users_table_pane" class="tab-pane fade">
                            <!--begin::Card-->
                            <div class="card card-flush">
                                <!--begin::Card body-->
                                <div class="card-body pt-0">
                                    <!--begin::Table container-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <table id="kt_project_users_table"
                                            class="table table-row-bordered table-row-dashed gy-4 align-middle fw-bold">
                                            <!--begin::Head-->
                                            <thead class="fs-7 text-gray-400 text-uppercase">
                                                <tr>
                                                    <th class="min-w-250px">Manager</th>
                                                    <th class="min-w-150px">Date</th>
                                                    <th class="min-w-90px">Amount</th>
                                                    <th class="min-w-90px">Status</th>
                                                    <th class="min-w-50px text-end">Details</th>
                                                </tr>
                                            </thead>
                                            <!--end::Head-->
                                            <!--begin::Body-->
                                            <tbody class="fs-6">
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    smith@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$893.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Melody
                                                                    Macy</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    melody@altbox.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Sep 22, 2023</td>
                                                    <td>$950.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-1.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Max
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">max@kt.com
                                                                </div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$449.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-5.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Sean
                                                                    Bean</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    sean@dellito.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Oct 25, 2023</td>
                                                    <td>$932.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-25.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Brian
                                                                    Cox</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    brian@exchange.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Sep 22, 2023</td>
                                                    <td>$842.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Mikaela
                                                                    Collins</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">mik@pex.com
                                                                </div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Aug 19, 2023</td>
                                                    <td>$553.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-warning fw-bold px-4 py-3">Pending</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-9.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Francis
                                                                    Mitcham</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    f.mit@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Oct 25, 2023</td>
                                                    <td>$532.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-danger text-danger fw-semibold">O</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Olivia
                                                                    Wild</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    olivia@corpmail.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Nov 10, 2023</td>
                                                    <td>$665.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Neil
                                                                    Owen</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    owen.neil@gmail.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Dec 20, 2023</td>
                                                    <td>$711.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-23.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Dan
                                                                    Wilson</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    dam@consilting.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Jun 20, 2023</td>
                                                    <td>$582.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Bold</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    emma@intenso.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Nov 10, 2023</td>
                                                    <td>$853.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-12.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Ana
                                                                    Crown</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    ana.cf@limtel.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Jul 25, 2023</td>
                                                    <td>$973.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-warning fw-bold px-4 py-3">Pending</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-info text-info fw-semibold">A</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Robert
                                                                    Doe</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    robert@benko.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Jul 25, 2023</td>
                                                    <td>$849.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-13.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">John
                                                                    Miller</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    miller@mapple.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Jun 24, 2023</td>
                                                    <td>$583.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-success text-success fw-semibold">L</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Lucy
                                                                    Kunic</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    lucy.m@fentech.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$931.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-21.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Ethan
                                                                    Wilder</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    ethan@loop.com.au</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$556.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-21.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Ethan
                                                                    Wilder</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    ethan@loop.com.au</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$608.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    smith@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$879.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    smith@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Mar 10, 2023</td>
                                                    <td>$900.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-1.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Max
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">max@kt.com
                                                                </div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Oct 25, 2023</td>
                                                    <td>$411.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-12.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Ana
                                                                    Crown</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    ana.cf@limtel.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$628.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-9.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Francis
                                                                    Mitcham</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    f.mit@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Apr 15, 2023</td>
                                                    <td>$882.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-warning fw-bold px-4 py-3">Pending</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    smith@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Sep 22, 2023</td>
                                                    <td>$795.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-25.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Brian
                                                                    Cox</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    brian@exchange.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Jul 25, 2023</td>
                                                    <td>$857.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-success fw-bold px-4 py-3">Approved</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Bold</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    emma@intenso.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$871.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <span
                                                                        class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                                </div>
                                                                <!--end::Avatar-->
                                                                <!--begin::Online-->
                                                                <div
                                                                    class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n1 mt-n1">
                                                                </div>
                                                                <!--end::Online-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Melody
                                                                    Macy</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    melody@altbox.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$960.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-warning fw-bold px-4 py-3">Pending</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-25.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Brian
                                                                    Cox</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    brian@exchange.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$570.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-5.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Sean
                                                                    Bean</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    sean@dellito.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Oct 25, 2023</td>
                                                    <td>$835.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    smith@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>Jun 20, 2023</td>
                                                    <td>$951.00</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-danger fw-bold px-4 py-3">Rejected</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <!--begin::User-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Wrapper-->
                                                            <div class="me-5 position-relative">
                                                                <!--begin::Avatar-->
                                                                <div class="symbol symbol-35px symbol-circle">
                                                                    <img alt="Pic"
                                                                        src="/tenancy/assets/media/avatars/300-9.jpg" />
                                                                </div>
                                                                <!--end::Avatar-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                            <!--begin::Info-->
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <a href=""
                                                                    class="mb-1 text-gray-800 text-hover-primary">Francis
                                                                    Mitcham</a>
                                                                <div class="fw-semibold fs-6 text-gray-400">
                                                                    f.mit@kpmg.com</div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                        <!--end::User-->
                                                    </td>
                                                    <td>May 05, 2023</td>
                                                    <td>$409.00</td>
                                                    <td>
                                                        <span class="badge badge-light-info fw-bold px-4 py-3">In
                                                            progress</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="#" class="btn btn-light btn-sm">View</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                            <!--end::Body-->
                                        </table>
                                        <!--end::Table-->
                                    </div>
                                    <!--end::Table container-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Card-->
                        </div>
                        <!--end::Tab pane-->
                    </div>
                    <!--end::Tab Content-->
                    <!--begin::Modals-->
                    <!--begin::Modal - View Users-->
                    <div class="modal fade" id="kt_modal_view_users" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header pb-0 border-0 justify-content-end">
                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                                    rx="1" transform="rotate(-45 6 17.3137)"
                                                    fill="currentColor" />
                                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                    <!--end::Close-->
                                </div>
                                <!--begin::Modal header-->
                                <!--begin::Modal body-->
                                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                    <!--begin::Heading-->
                                    <div class="text-center mb-13">
                                        <!--begin::Title-->
                                        <h1 class="mb-3">Browse Users</h1>
                                        <!--end::Title-->
                                        <!--begin::Description-->
                                        <div class="text-muted fw-semibold fs-5">If you need more info, please check
                                            out our
                                            <a href="#" class="link-primary fw-bold">Users Directory</a>.
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Heading-->
                                    <!--begin::Users-->
                                    <div class="mb-15">
                                        <!--begin::List-->
                                        <div class="mh-375px scroll-y me-n7 pe-7">
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Emma
                                                            Smith
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Art
                                                                Director</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">smith@kpmg.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$23,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Melody
                                                            Macy
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Marketing
                                                                Analytic</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">melody@altbox.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$50,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="/tenancy/assets/media/avatars/300-1.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Max
                                                            Smith
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Software
                                                                Enginer</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">max@kt.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$75,900</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="/tenancy/assets/media/avatars/300-5.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Sean
                                                            Bean
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Web
                                                                Developer</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">sean@dellito.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$10,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic"
                                                            src="/tenancy/assets/media/avatars/300-25.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Brian
                                                            Cox
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">UI/UX
                                                                Designer</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">brian@exchange.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$20,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Mikaela
                                                            Collins
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Head
                                                                Of Marketing</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">mik@pex.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$9,300</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic" src="/tenancy/assets/media/avatars/300-9.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Francis
                                                            Mitcham
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Software
                                                                Arcitect</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">f.mit@kpmg.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$15,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-danger text-danger fw-semibold">O</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Olivia
                                                            Wild
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">System
                                                                Admin</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">olivia@corpmail.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$23,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Neil
                                                            Owen
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Account
                                                                Manager</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">owen.neil@gmail.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$45,800</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic"
                                                            src="/tenancy/assets/media/avatars/300-23.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Dan
                                                            Wilson
                                                            <span class="badge badge-light fs-8 fw-semibold ms-2">Web
                                                                Desinger</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">dam@consilting.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$90,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Emma
                                                            Bold
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Corporate
                                                                Finance</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">emma@intenso.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$5,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div
                                                class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <img alt="Pic"
                                                            src="/tenancy/assets/media/avatars/300-12.jpg" />
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Ana
                                                            Crown
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Customer
                                                                Relationship</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">ana.cf@limtel.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$70,000</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="d-flex flex-stack py-5">
                                                <!--begin::Details-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Avatar-->
                                                    <div class="symbol symbol-35px symbol-circle">
                                                        <span
                                                            class="symbol-label bg-light-info text-info fw-semibold">A</span>
                                                    </div>
                                                    <!--end::Avatar-->
                                                    <!--begin::Details-->
                                                    <div class="ms-6">
                                                        <!--begin::Name-->
                                                        <a href="#"
                                                            class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">Robert
                                                            Doe
                                                            <span
                                                                class="badge badge-light fs-8 fw-semibold ms-2">Marketing
                                                                Executive</span></a>
                                                        <!--end::Name-->
                                                        <!--begin::Email-->
                                                        <div class="fw-semibold text-muted">robert@benko.com</div>
                                                        <!--end::Email-->
                                                    </div>
                                                    <!--end::Details-->
                                                </div>
                                                <!--end::Details-->
                                                <!--begin::Stats-->
                                                <div class="d-flex">
                                                    <!--begin::Sales-->
                                                    <div class="text-end">
                                                        <div class="fs-5 fw-bold text-dark">$45,500</div>
                                                        <div class="fs-7 text-muted">Sales</div>
                                                    </div>
                                                    <!--end::Sales-->
                                                </div>
                                                <!--end::Stats-->
                                            </div>
                                            <!--end::User-->
                                        </div>
                                        <!--end::List-->
                                    </div>
                                    <!--end::Users-->
                                    <!--begin::Notice-->
                                    <div class="d-flex justify-content-between">
                                        <!--begin::Label-->
                                        <div class="fw-semibold">
                                            <label class="fs-6">Adding Users by Team Members</label>
                                            <div class="fs-7 text-muted">If you need more info, please check budget
                                                planning</div>
                                        </div>
                                        <!--end::Label-->
                                        <!--begin::Switch-->
                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value=""
                                                checked="checked" />
                                            <span class="form-check-label fw-semibold text-muted">Allowed</span>
                                        </label>
                                        <!--end::Switch-->
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Modal body-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - View Users-->
                    <!--begin::Modal - Users Search-->
                    <div class="modal fade" id="kt_modal_users_search" tabindex="-1" aria-hidden="true">
                        <!--begin::Modal dialog-->
                        <div class="modal-dialog modal-dialog-centered mw-650px">
                            <!--begin::Modal content-->
                            <div class="modal-content">
                                <!--begin::Modal header-->
                                <div class="modal-header pb-0 border-0 justify-content-end">
                                    <!--begin::Close-->
                                    <div class="btn btn-sm btn-icon btn-active-color-primary"
                                        data-bs-dismiss="modal">
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
                                <!--begin::Modal header-->
                                <!--begin::Modal body-->
                                <div class="modal-body scroll-y mx-5 mx-xl-18 pt-0 pb-15">
                                    <!--begin::Content-->
                                    <div class="text-center mb-13">
                                        <h1 class="mb-3">Search Users</h1>
                                        <div class="text-muted fw-semibold fs-5">Invite Collaborators To Your Project
                                        </div>
                                    </div>
                                    <!--end::Content-->
                                    <!--begin::Search-->
                                    <div id="kt_modal_users_search_handler" data-kt-search-keypress="true"
                                        data-kt-search-min-length="2" data-kt-search-enter="enter"
                                        data-kt-search-layout="inline">
                                        <!--begin::Form-->
                                        <form data-kt-search-element="form" class="w-100 position-relative mb-5"
                                            autocomplete="off">
                                            <!--begin::Hidden input(Added to disable form autocomplete)-->
                                            <input type="hidden" />
                                            <!--end::Hidden input-->
                                            <!--begin::Icon-->
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span
                                                class="svg-icon svg-icon-2 svg-icon-lg-1 svg-icon-gray-500 position-absolute top-50 ms-5 translate-middle-y">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1"
                                                        transform="rotate(45 17.0365 15.1223)"
                                                        fill="currentColor" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <!--end::Icon-->
                                            <!--begin::Input-->
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid px-15"
                                                name="search" value=""
                                                placeholder="Search by username, full name or email..."
                                                data-kt-search-element="input" />
                                            <!--end::Input-->
                                            <!--begin::Spinner-->
                                            <span
                                                class="position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5"
                                                data-kt-search-element="spinner">
                                                <span
                                                    class="spinner-border h-15px w-15px align-middle text-muted"></span>
                                            </span>
                                            <!--end::Spinner-->
                                            <!--begin::Reset-->
                                            <span
                                                class="btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 me-5 d-none"
                                                data-kt-search-element="clear">
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                                                <span class="svg-icon svg-icon-2 svg-icon-lg-1 me-0">
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
                                            </span>
                                            <!--end::Reset-->
                                        </form>
                                        <!--end::Form-->
                                        <!--begin::Wrapper-->
                                        <div class="py-5">
                                            <!--begin::Suggestions-->
                                            <div data-kt-search-element="suggestions">
                                                <!--begin::Heading-->
                                                <h3 class="fw-semibold mb-5">Recently searched:</h3>
                                                <!--end::Heading-->
                                                <!--begin::Users-->
                                                <div class="mh-375px scroll-y me-n7 pe-7">
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Emma Smith</span>
                                                            <span class="badge badge-light">Art Director</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <span
                                                                class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Melody Macy</span>
                                                            <span class="badge badge-light">Marketing Analytic</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="/tenancy/assets/media/avatars/300-1.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Max Smith</span>
                                                            <span class="badge badge-light">Software Enginer</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="/tenancy/assets/media/avatars/300-5.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Sean Bean</span>
                                                            <span class="badge badge-light">Web Developer</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                    <!--begin::User-->
                                                    <a href="#"
                                                        class="d-flex align-items-center p-3 rounded bg-state-light bg-state-opacity-50 mb-1">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle me-5">
                                                            <img alt="Pic"
                                                                src="/tenancy/assets/media/avatars/300-25.jpg" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Info-->
                                                        <div class="fw-semibold">
                                                            <span class="fs-6 text-gray-800 me-2">Brian Cox</span>
                                                            <span class="badge badge-light">UI/UX Designer</span>
                                                        </div>
                                                        <!--end::Info-->
                                                    </a>
                                                    <!--end::User-->
                                                </div>
                                                <!--end::Users-->
                                            </div>
                                            <!--end::Suggestions-->
                                            <!--begin::Results(add d-none to below element to hide the users list by default)-->
                                            <div data-kt-search-element="results" class="d-none">
                                                <!--begin::Users-->
                                                <div class="mh-375px scroll-y me-n7 pe-7">
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="0">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='0']"
                                                                    value="0" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-6.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma
                                                                    Smith</a>
                                                                <div class="fw-semibold text-muted">smith@kpmg.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="1">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='1']"
                                                                    value="1" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-danger text-danger fw-semibold">M</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Melody
                                                                    Macy</a>
                                                                <div class="fw-semibold text-muted">melody@altbox.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="2">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='2']"
                                                                    value="2" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-1.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Max
                                                                    Smith</a>
                                                                <div class="fw-semibold text-muted">max@kt.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="3">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='3']"
                                                                    value="3" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-5.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Sean
                                                                    Bean</a>
                                                                <div class="fw-semibold text-muted">sean@dellito.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="4">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='4']"
                                                                    value="4" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-25.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Brian
                                                                    Cox</a>
                                                                <div class="fw-semibold text-muted">brian@exchange.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="5">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='5']"
                                                                    value="5" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-warning text-warning fw-semibold">C</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Mikaela
                                                                    Collins</a>
                                                                <div class="fw-semibold text-muted">mik@pex.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="6">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='6']"
                                                                    value="6" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-9.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Francis
                                                                    Mitcham</a>
                                                                <div class="fw-semibold text-muted">f.mit@kpmg.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="7">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='7']"
                                                                    value="7" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-danger text-danger fw-semibold">O</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Olivia
                                                                    Wild</a>
                                                                <div class="fw-semibold text-muted">
                                                                    olivia@corpmail.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="8">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='8']"
                                                                    value="8" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-primary text-primary fw-semibold">N</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Neil
                                                                    Owen</a>
                                                                <div class="fw-semibold text-muted">
                                                                    owen.neil@gmail.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="9">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='9']"
                                                                    value="9" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-23.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Dan
                                                                    Wilson</a>
                                                                <div class="fw-semibold text-muted">dam@consilting.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="10">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='10']"
                                                                    value="10" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-danger text-danger fw-semibold">E</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Emma
                                                                    Bold</a>
                                                                <div class="fw-semibold text-muted">emma@intenso.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="11">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='11']"
                                                                    value="11" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-12.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ana
                                                                    Crown</a>
                                                                <div class="fw-semibold text-muted">ana.cf@limtel.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="12">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='12']"
                                                                    value="12" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-info text-info fw-semibold">A</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Robert
                                                                    Doe</a>
                                                                <div class="fw-semibold text-muted">robert@benko.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="13">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='13']"
                                                                    value="13" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-13.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">John
                                                                    Miller</a>
                                                                <div class="fw-semibold text-muted">miller@mapple.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="14">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='14']"
                                                                    value="14" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <span
                                                                    class="symbol-label bg-light-success text-success fw-semibold">L</span>
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Lucy
                                                                    Kunic</a>
                                                                <div class="fw-semibold text-muted">lucy.m@fentech.com
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2" selected="selected">Owner
                                                                </option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="15">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='15']"
                                                                    value="15" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-21.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Ethan
                                                                    Wilder</a>
                                                                <div class="fw-semibold text-muted">ethan@loop.com.au
                                                                </div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1" selected="selected">Guest
                                                                </option>
                                                                <option value="2">Owner</option>
                                                                <option value="3">Can Edit</option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="border-bottom border-gray-300 border-bottom-dashed">
                                                    </div>
                                                    <!--end::Separator-->
                                                    <!--begin::User-->
                                                    <div class="rounded d-flex flex-stack bg-active-lighten p-4"
                                                        data-user-id="16">
                                                        <!--begin::Details-->
                                                        <div class="d-flex align-items-center">
                                                            <!--begin::Checkbox-->
                                                            <label
                                                                class="form-check form-check-custom form-check-solid me-5">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="users" data-kt-check="true"
                                                                    data-kt-check-target="[data-user-id='16']"
                                                                    value="16" />
                                                            </label>
                                                            <!--end::Checkbox-->
                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-35px symbol-circle">
                                                                <img alt="Pic"
                                                                    src="/tenancy/assets/media/avatars/300-1.jpg" />
                                                            </div>
                                                            <!--end::Avatar-->
                                                            <!--begin::Details-->
                                                            <div class="ms-5">
                                                                <a href="#"
                                                                    class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">Max
                                                                    Smith</a>
                                                                <div class="fw-semibold text-muted">max@kt.com</div>
                                                            </div>
                                                            <!--end::Details-->
                                                        </div>
                                                        <!--end::Details-->
                                                        <!--begin::Access menu-->
                                                        <div class="ms-2 w-100px">
                                                            <select
                                                                class="form-select form-select-solid form-select-sm"
                                                                data-control="select2" data-hide-search="true">
                                                                <option value="1">Guest</option>
                                                                <option value="2">Owner</option>
                                                                <option value="3" selected="selected">Can Edit
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <!--end::Access menu-->
                                                    </div>
                                                    <!--end::User-->
                                                </div>
                                                <!--end::Users-->
                                                <!--begin::Actions-->
                                                <div class="d-flex flex-center mt-15">
                                                    <button type="reset" id="kt_modal_users_search_reset"
                                                        data-bs-dismiss="modal"
                                                        class="btn btn-active-light me-3">Cancel</button>
                                                    <button type="submit" id="kt_modal_users_search_submit"
                                                        class="btn btn-primary">Add Selected Users</button>
                                                </div>
                                                <!--end::Actions-->
                                            </div>
                                            <!--end::Results-->
                                            <!--begin::Empty-->
                                            <div data-kt-search-element="empty" class="text-center d-none">
                                                <!--begin::Message-->
                                                <div class="fw-semibold py-10">
                                                    <div class="text-gray-600 fs-3 mb-2">No users found</div>
                                                    <div class="text-muted fs-6">Try to search by username, full name
                                                        or email...</div>
                                                </div>
                                                <!--end::Message-->
                                                <!--begin::Illustration-->
                                                <div class="text-center px-5">
                                                    <img src="/tenancy/assets/media/illustrations/sketchy-1/1.png"
                                                        alt="" class="w-100 h-200px h-sm-325px" />
                                                </div>
                                                <!--end::Illustration-->
                                            </div>
                                            <!--end::Empty-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Search-->
                                </div>
                                <!--end::Modal body-->
                            </div>
                            <!--end::Modal content-->
                        </div>
                        <!--end::Modal dialog-->
                    </div>
                    <!--end::Modal - Users Search-->
                    <!--end::Modals-->
                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Modal - New Target-->
        <div class="modal fade" id="kt_modal_new_target" tabindex="-1" aria-hidden="true">
            <!--begin::Modal dialog-->
            <div class="modal-dialog modal-dialog-centered mw-650px">
                <!--begin::Modal content-->
                <div class="modal-content rounded">
                    <!--begin::Modal header-->
                    <div class="modal-header pb-0 border-0 justify-content-end">
                        <!--begin::Close-->
                        <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                        rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                        transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                        </div>
                        <!--end::Close-->
                    </div>
                    <!--begin::Modal header-->
                    <!--begin::Modal body-->
                    <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                        <!--begin:Form-->
                        <form id="kt_modal_new_target_form" class="form" action="#">
                            <!--begin::Heading-->
                            <div class="mb-13 text-center">
                                <!--begin::Title-->
                                <h1 class="mb-3">Cadastro de Organismos</h1>
                                <!--end::Title-->
                                <!--begin::Description-->
                                <div class="text-muted fw-semibold fs-5">Se precisar de mais informações, consulte
                                    <a href="#" class="fw-bold link-primary">Novos Organismos</a>.
                                </div>
                                <!--end::Description-->
                            </div>
                            <!--end::Heading-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Nome</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Digite o nome como costa na rasão social."></i>
                                </label>
                                <!--end::Label-->
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="Enter Target Title" name="name" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row g-9 mb-8">
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Assign</label>
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-hide-search="true" data-placeholder="Select a Team Member"
                                        name="target_assign">
                                        <option value="">Select user...</option>
                                        <option value="1">Karina Clark</option>
                                        <option value="2">Robert Doe</option>
                                        <option value="3">Niel Owen</option>
                                        <option value="4">Olivia Wild</option>
                                        <option value="5">Sean Bean</option>
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Due Date</label>
                                    <!--begin::Input-->
                                    <div class="position-relative d-flex align-items-center">
                                        <!--begin::Icon-->
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <!--end::Icon-->
                                        <!--begin::Datepicker-->
                                        <input class="form-control form-control-solid ps-12"
                                            placeholder="Selecione uma data" name="due_date" />
                                        <!--end::Datepicker-->
                                    </div>
                                    <!--end::Input-->
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8">
                                <label class="fs-6 fw-semibold mb-2">Target Details</label>
                                <textarea class="form-control form-control-solid" rows="3" name="target_details"
                                    placeholder="Type Target Details"></textarea>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Tags</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Specify a target priorty"></i>
                                </label>
                                <!--end::Label-->
                                <input class="form-control form-control-solid" value="Important, Urgent"
                                    name="tags" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-stack mb-8">
                                <!--begin::Label-->
                                <div class="me-5">
                                    <label class="fs-6 fw-semibold">Adding Users by Team Members</label>
                                    <div class="fs-7 fw-semibold text-muted">If you need more info, please check
                                        budget planning</div>
                                </div>
                                <!--end::Label-->
                                <!--begin::Switch-->
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        checked="checked" />
                                    <span class="form-check-label fw-semibold text-muted">Allowed</span>
                                </label>
                                <!--end::Switch-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-15 fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Label-->
                                    <div class="fw-semibold me-5">
                                        <label class="fs-6">Notifications</label>
                                        <div class="fs-7 text-muted">Allow Notifications by Phone or Email</div>
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Checkboxes-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-custom form-check-solid me-10">
                                            <input class="form-check-input h-20px w-20px" type="checkbox"
                                                name="communication[]" value="email" checked="checked" />
                                            <span class="form-check-label fw-semibold">Email</span>
                                        </label>
                                        <!--end::Checkbox-->
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input h-20px w-20px" type="checkbox"
                                                name="communication[]" value="phone" />
                                            <span class="form-check-label fw-semibold">Phone</span>
                                        </label>
                                        <!--end::Checkbox-->
                                    </div>
                                    <!--end::Checkboxes-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="text-center">
                                <button type="reset" id="kt_modal_new_target_cancel"
                                    class="btn btn-light me-3">Cancel</button>
                                <button type="submit" id="kt_modal_new_target_submit" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Por favor, aguarde...
                                        <span
                                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            <!--end::Actions-->
                        </form>
                        <!--end:Form-->
                    </div>
                    <!--end::Modal body-->
                </div>
                <!--end::Modal content-->
            </div>
            <!--end::Modal dialog-->
        </div>
        <!--end::Modal - New Target-->
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Filial') }}
                <x-btn-link class="ml-4 float-right" href="{{ route('filial.create') }}">Add Filial</x-btn-link>
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    cidade
                                </th>

                                <th scope="col" class="px-6 py-3 text-right">
                                    Açôes
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($filiais as $tenant)
                                <tr
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">
                                        {{ $tenant->id }}

                                    </td>
                                    <th scope="row"
                                        class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $tenant->name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $tenant->addresses->cidade }}

                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="#"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/tenancy/assets/js/custom/apps/projects/list/list.js"></script>
<script src="/tenancy/assets/js/custom/apps/projects/users/users.js"></script>
<script src="/tenancy/assets/js/widgets.bundle.js"></script>
<script src="/tenancy/assets/js/custom/apps/chat/chat.js"></script>
<script src="/tenancy/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/tenancy/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/tenancy/assets/js/custom/utilities/modals/users-search.js"></script>
<script src="/tenancy/assets/js/custom/utilities/modals/new-target.js"></script>
<script src="/tenancy/assets/plugins/global/plugins.bundle.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
