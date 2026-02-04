<x-tenant-app-layout 
    pageTitle="Lista de Usuários" 
    :breadcrumbs="[['label' => 'Financeiro', 'url' => route('banco.list')], ['label' => 'Domus IA']]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">
        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
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
                                    <i class="fa-solid fa-magnifying-glass fs-3"></i>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" data-kt-user-table-filter="search"
                                    class="form-control form-control-solid w-250px ps-14" placeholder="Buscar usuário" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!--begin::Card title-->
                        <!--begin::Card toolbar-->
                        <div class="card-toolbar">
                            <!--begin::Toolbar-->
                            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                                <!--begin::Add user-->
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_add_user">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Novo Usuário
                                </button>
                                <!--end::Add user-->
                            </div>
                            <!--end::Toolbar-->
                            <!--begin::Modal - Add task-->
                                @include('app.components.modals.user.createUser')
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
                                            <input class="form-check-input" type="checkbox" data-kt-check="true"
                                                data-kt-check-target="#kt_table_users .form-check-input"
                                                value="1" />
                                        </div>
                                    </th>
                                    <th class="min-w-125px">Usuário</th>
                                    <th class="min-w-125px">Permição</th>
                                    <th class="min-w-125px">Ultimo Login</th>
                                    <th class="min-w-125px">Status</th>
                                    <th class="min-w-125px">Data de Ingresso</th>
                                    <th class="text-end min-w-100px">Ações</th>
                                </tr>
                                <!--end::Table row-->
                            </thead>
                            <!--end::Table head-->
                            <!--begin::Table body-->
                            <tbody class="text-gray-600 fw-semibold">
                                <!--begin::Table row-->
                                @foreach ($users as $user)
                                    <tr>
                                        <!--begin::Checkbox-->
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input" type="checkbox" value="1" />
                                            </div>
                                        </td>
                                        <!--end::Checkbox-->
                                        <!--begin::User=-->
                                        <td class="d-flex align-items-center">
                                            <!--begin:: Avatar -->
                                            <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                                <a href="../../demo1/dist/apps/user-management/users/view.html">
                                                    <div class="symbol-label">
                                                        @if ($user->avatar && !empty($user->avatar))
                                                            <img src="{{ route('file', ['path' => $user->avatar]) }}"
                                                                alt="{{ $user->name }}" class="w-100">
                                                        @else
                                                            <img src="/assets/media/avatars/300-6.jpg"
                                                                alt="{{ $user->name }}" class="w-100">
                                                        @endif
                                                    </div>
                                                </a>
                                            </div>
                                            <!--end::Avatar-->
                                            <!--begin::User details-->
                                            <div class="d-flex flex-column">
                                                <a href="../../demo1/dist/apps/user-management/users/view.html"
                                                    class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</a>
                                                <span>{{ $user->email }}</span>
                                            </div>
                                            <!--begin::User details-->
                                        </td>
                                        <!--end::User=-->
                                        <!--begin::Role=-->
                                        <td>
                                            @foreach ($user->roles as $role)
                                                <span
                                                    class="badge {{ $roleColors[$role->name] ?? 'badge-secondary' }}">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <!--end::Role=-->
                                        <!--begin::Last login=-->
                                        <td>
                                            <div class="badge badge-light fw-bold">{{ $user->last_login_formatted }}
                                            </div>
                                        </td>
                                        <!--end::Last login=-->
                                        <!--begin::Two step=-->
                                        <td>
                                            @if ($user->active)
                                                <span class="badge badge-light-success">
                                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Ativado
                                                </span>
                                            @else
                                                <span class="badge badge-light-danger">
                                                    <i class="bi bi-x-circle-fill text-danger me-1"></i> Desativado
                                                </span>
                                            @endif
                                        </td>
                                        <!--end::Two step=-->
                                        <!--begin::Joined-->
                                        <td>{{ $user->created_at->translatedFormat('d M Y, h:i a') }}
                                        </td>
                                        <!--begin::Joined-->
                                        <!--begin::Action=-->
                                        <td class="text-end">
                                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Ações
                                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                                                <span class="svg-icon svg-icon-5 m-0">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                                            fill="currentColor" />
                                                    </svg>
                                                </span>
                                                <!--end::Svg Icon--></a>
                                            <!--begin::Menu-->
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="{{ route('users.edit', $user->id) }}"
                                                        class="menu-link px-3">Editar</a>
                                                </div>
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

    @push('scripts')
        <!--begin::Vendors Javascript(used for this page only)-->
        <script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
        <!--end::Vendors Javascript-->
        <!--begin::Custom Javascript(used for this page only)-->
        <script src="/assets/js/custom/apps/user-management/users/list/table.js"></script>
        <script src="/assets/js/custom/apps/user-management/users/list/export-users.js"></script>
        <script src="/assets/js/custom/apps/user-management/users/list/add.js"></script>
        <script src="/assets/js/widgets.bundle.js"></script>
        <script src="/assets/js/custom/apps/chat/chat.js"></script>
        <script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
        <script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
        <script src="/assets/js/custom/utilities/modals/users-search.js"></script>
    @endpush
    <!--end::Custom Javascript-->
    <!--end::Javascript-->
</x-tenant-app-layout>
