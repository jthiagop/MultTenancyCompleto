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
                                                                    <a href="{{ route('company.show', $company->id) }}"
                                                                        class="mb-1 text-gray-800 text-hover-primary">{{ $company->name }}</a>
                                                                    <div class="fw-semibold fs-6 text-gray-400">
                                                                        {{ $company->email }}
                                                                    </div>
                                                                    <div class="fs-7 text-muted">
                                                                        {{ $company->addresses->rua ?? 'Endereço não cadastrado' }}, {{ $company->addresses->cidade ?? 'Endereço não cadastrado' }} - {{ $company->addresses->uf ?? 'Endereço não cadastrado' }}
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
                                                                                : 'assets/media/avatars/blank.png' }}" />
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
                                <i class="fa fa-times fa-2x"></i>
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
