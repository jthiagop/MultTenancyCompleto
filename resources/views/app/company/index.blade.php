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
                            Administração de Organismo</h1>
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
                            <li class="breadcrumb-item text-muted">Organismos</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <!--begin::Actions-->
                    <div class="d-flex my-0">
                        <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_new_target">Novo Organismo</a>

                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-success me-3" ><i class="bi bi-person-add"></i>Novo Usuários</a>
                    </div>
                    <!--end::Actions-->

                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Tab Content-->
                    <div class="tab-content">
                        <!--begin::Tab pane-->
                        <div id="kt_project_users_card_pane" class="tab-pane fade  ">
                            <!--begin::Row-->
                            <div class="row g-6 g-xl-9">
                                <!--begin::Col-->
                                @foreach ($companyes as $company)
                                    <div class="col-md-6 col-xxl-4">
                                        <!--begin::Card-->
                                        <div class="card">
                                            <!--begin::Card body-->
                                            <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                                <!--begin::Avatar-->
                                                <div class="symbol symbol-65px symbol-circle mb-5">
                                                    @if ($company->avatar && !empty($company->avatar))
                                                        <img src="{{ route('file', ['path' => $company->avatar]) }}"
                                                            alt="{{ $company->name }}" class="w-100">
                                                    @else
                                                        <img src="/assets/media/avatars/300-6.jpg"
                                                            alt="{{ $company->name }}" class="w-100">
                                                    @endif
                                                    <div
                                                        class="bg-success position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3">
                                                    </div>
                                                </div>
                                                <!--end::Avatar-->
                                                <!--begin::Name-->
                                                <a href="{{ route('company.show', ['company' => $company->id]) }}"
                                                    class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">{{ $company->name }}</a>
                                                <!--end::Name-->
                                                <!--begin::Position-->
                                                <div class="fw-semibold text-gray-400 mb-6">{{ $company->email }}.
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
                                @endforeach

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
                        <div id="kt_project_users_table_pane" class="tab-pane fade show active">
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
                                                    <th class="min-w-250px">Entidade</th>
                                                    <th class="min-w-90px">Status</th>
                                                    <th class="min-w-150px">Data</th>
                                                    <th class="min-w-90px">Saldo</th>
                                                    <th class="min-w-90px">Membros</th>
                                                    <th class="min-w-90px">Status</th>
                                                    <th class="min-w-50px text-end">Detalhes</th>
                                                </tr>
                                            </thead>
                                            <!--end::Head-->
                                            <!--begin::Body-->
                                            <tbody class="fs-6">
                                                <tr>
                                                    @foreach ($companyes as $company)
                                                        <td>
                                                            <!--begin::company-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Wrapper-->
                                                                <div class="me-5 position-relative">
                                                                    <!--begin::Avatar-->
                                                                    <div class="symbol symbol-35px symbol-circle">
                                                                        @if ($company->avatar && !empty($company->avatar))
                                                                            <img src="{{ route('file', ['path' => $company->avatar]) }}"
                                                                                alt="{{ $company->name }}"
                                                                                class="w-100">
                                                                        @else
                                                                            <img src="/assets/media/avatars/300-6.jpg"
                                                                                alt="{{ $company->name }}"
                                                                                class="w-100">
                                                                        @endif
                                                                    </div>
                                                                    <!--end::Avatar-->
                                                                </div>
                                                                <!--end::Wrapper-->
                                                                <!--begin::Info-->
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <a href=""
                                                                        class="mb-1 text-gray-800 text-hover-primary">{{ $company->name }}</a>
                                                                    <div class="fw-semibold fs-6 text-gray-400">
                                                                        {{ $company->email }}
                                                                    </div>
                                                                </div>
                                                                <!--end::Info-->
                                                            </div>
                                                            <!--end::User-->
                                                        </td>
                                                        <td>{{ $company->type }}</td>
                                                        <td>{{ $company->created_at->format('d/m/Y') }} </td>
                                                        <td>$449.00</td>
                                                        <td>
                                                            <!--begin::Members-->
                                                            <div class="symbol-group symbol-hover fs-8">
                                                                @foreach ($company->users->take(5) as $user)
                                                                    <div class="symbol symbol-25px symbol-circle"
                                                                        data-bs-toggle="tooltip"
                                                                        title="{{ $user->name }}">
                                                                        <img alt="{{ $user->name }}"
                                                                            src="{{ $user->avatar && $user->avatar !== 'tenant/blank.png'
                                                                                ? route('file', ['path' => $user->avatar])
                                                                                : '/assets/media/avatars/blank.png' }}" />
                                                                    </div>
                                                                @endforeach

                                                                @if ($company->users->count() > 5)
                                                                    <div class="symbol symbol-25px symbol-circle"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Mais {{ $company->users->count() - 5 }} usuários">
                                                                        <span
                                                                            class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                                                                            +{{ $company->users->count() - 5 }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                                @if ($company->users->count() < 1)
                                                                    <div class="symbol symbol-25px symbol-circle text-center"
                                                                        data-bs-toggle="tooltip"
                                                                        title="Nenhum usuário cadastrado">
                                                                        <span
                                                                            class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                                                                            {{ 0 }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <!--end::Members-->
                                                        </td>
                                                        <td>
                                                            @if ($company->status === 'active')
                                                                <span class="badge badge-success">ATIVO</span>
                                                            @else
                                                                <span class="badge badge-danger">INATIVO</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-end">
                                                            <a href="{{ route('company.show', $company->id) }}"
                                                                class="btn btn-light btn-sm">
                                                                Ver
                                                            </a>
                                                        </td>
                                                </tr>
                                                @endforeach
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
                                        <h1 class="mb-3">Buscar pelos usuários</h1>
                                        <!--end::Title-->
                                        <!--begin::Description-->
                                        <div class="text-muted fw-semibold fs-5">Se precisar de mais informações,
                                            confira nosso <a href="#" class="link-primary fw-bold">Diretório de
                                                Usuários</a>. </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Heading-->
                                    <!--begin::Users-->
                                    <div class="mb-15">
                                        <!--begin::List-->
                                        <div class="mh-375px scroll-y me-n7 pe-7">
                                            @foreach ($users as $user)
                                                <!--begin::User-->
                                                <div
                                                    class="d-flex flex-stack py-5 border-bottom border-gray-300 border-bottom-dashed">
                                                    <!--begin::Details-->
                                                    <div class="d-flex align-items-center">
                                                        <!--begin::Avatar-->
                                                        <div class="symbol symbol-35px symbol-circle">
                                                            <img alt="{{ $user->name }}"
                                                                src="{{ $user->avatar && $user->avatar !== 'tenant/blank.png'
                                                                    ? route('file', ['path' => $user->avatar])
                                                                    : '/assets/media/avatars/blank.png' }}" />
                                                        </div>
                                                        <!--end::Avatar-->
                                                        <!--begin::Details-->
                                                        <div class="ms-6">
                                                            <!--begin::Name-->
                                                            <a href="#"
                                                                class="d-flex align-items-center fs-5 fw-bold text-dark text-hover-primary">{{ $user->name }}
                                                                <span
                                                                    class="badge badge-light fs-8 fw-semibold ms-2">Art
                                                                    Director</span></a>
                                                            <!--end::Name-->
                                                            <!--begin::Email-->
                                                            <div class="fw-semibold text-muted">{{ $user->email }}
                                                            </div>
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
                                            @endforeach
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
                                                        transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
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
                                                                src="/assets/media/avatars/300-6.jpg" />
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
                                                                src="/assets/media/avatars/300-1.jpg" />
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
                                                                src="/assets/media/avatars/300-5.jpg" />
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
                                                                src="/assets/media/avatars/300-25.jpg" />
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
                                                                    src="/assets/media/avatars/300-6.jpg" />
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
                                                                    src="/assets/media/avatars/300-1.jpg" />
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
                                                                    src="/assets/media/avatars/300-5.jpg" />
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
                                                                    src="/assets/media/avatars/300-25.jpg" />
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
                                                                    src="/assets/media/avatars/300-9.jpg" />
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
                                                                    src="/assets/media/avatars/300-23.jpg" />
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
                                                                    src="/assets/media/avatars/300-12.jpg" />
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
                                                                    src="/assets/media/avatars/300-13.jpg" />
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
                                                                    src="/assets/media/avatars/300-21.jpg" />
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
                                                                    src="/assets/media/avatars/300-1.jpg" />
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
                                                    <img src="/assets/media/illustrations/sketchy-1/1.png"
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
                        <form class="form" action=" {{ route('company.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
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
                            <!--begin::Card body-->
                            <div class="card-body text-center pt-0 mb-8">
                                <!--begin::Image input-->
                                <!--begin::Image input placeholder-->
                                <style>
                                    .image-input-placeholder {
                                        background-image: url('/assets/media/svg/files/blank-image.svg');
                                    }

                                    [data-bs-theme="dark"] .image-input-placeholder {
                                        background-image: url('/assets/media/svg/files/blank-image-dark.svg');
                                    }
                                </style>
                                <!--end::Image input placeholder-->
                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                                    data-kt-image-input="true">
                                    <!--begin::Preview existing avatar-->
                                    <div class="image-input-wrapper w-150px h-150px"></div>
                                    <!--end::Preview existing avatar-->
                                    <!--begin::Label-->
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                        title="Change avatar">
                                        <i class="bi bi-pencil-fill fs-7"></i>
                                        <!--begin::Inputs-->
                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                        <!--end::Inputs-->
                                    </label>
                                    <!--end::Label-->
                                    <!--begin::Cancel-->
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                        title="Cancel avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <!--end::Cancel-->
                                    <!--begin::Remove-->
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                        title="Remove avatar">
                                        <i class="bi bi-x fs-2"></i>
                                    </span>
                                    <!--end::Remove-->
                                </div>
                                <!--end::Image input-->
                                <!--begin::Description-->
                                <div class="text-muted fs-7">Defina a imagem do organismo. Somente imagem *.png, *.jpg
                                    e *.jpeg são aceitos</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Card body-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 fv-row  mb-2">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Nome</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Digite o nome como costa na rasão social."></i>
                                </label>
                                <!--end::Label-->
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="Nome da Organização" name="name" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row g-9 mb-8">
                                <!--begin::Col-->
                                <div class="col-md-12 fv-row">
                                    <!--begin::Label-->
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">CNPJ</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Informe o número do CNPJ."></i>
                                    </label>
                                    <!--end::Label-->
                                    <input type="text" class="form-control form-control-solid" id="cnpj"
                                        placeholder="00.000.000/000.0-00" name="cnpj" />

                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Actions-->
                            <div class="text-center">
                                <button type="reset" id="kt_modal_new_target_cancel"
                                    class="btn btn-light me-3">Cancel</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">Salvar</span>

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
</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/projects/list/list.js"></script>
<script src="/assets/js/custom/apps/projects/users/users.js"></script>

<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/new-target.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<!--end::Javascript-->
