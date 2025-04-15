<x-tenant-app-layout>

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid">
        <!--begin::Toolbar-->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <!--begin::Toolbar container-->
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <!--begin::Page title-->
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <!--begin::Title-->
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Visão
                        do Organismo</h1>
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
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('company.index') }}" class="text-muted text-hover-primary">Organismos</a>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <!--end::Item-->
                        <!--begin::Item-->
                        <li class="breadcrumb-item text-muted">
                            {{ $companyShow->name }}</li>
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
                <div class="card mb-9">
                    <div class="card-body pt-9 pb-0">
                        <!--begin::Details-->
                        <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                            <!--begin::Image-->
                            <div
                                class="d-flex flex-center flex-shrink-0 bg-light rounded w-100px h-100px w-lg-150px h-lg-150px me-7 mb-4">
                                <img class="img-fluid w-100 h-100 rounded"
                                    src="{{ $companyShow->avatar ? route('file', ['path' => $companyShow->avatar]) : '/public//assets/media/avatars/blank.png' }}"
                                    alt="{{ $companyShow->name }}" />
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
                                                class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">{{ $companyShow->name }}</a>
                                            <span
                                                class="badge badge-light-success me-auto">{{ $companyShow->status }}</span>
                                        </div>
                                        <!--end::Status-->
                                        <!--begin::Description-->
                                        <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">CNPJ:
                                            {{ $companyShow->cnpj }} -
                                            @if ($companyShow->addresses)
                                                {{ $companyShow->addresses->rua }},
                                                {{ $companyShow->addresses->numero }}
                                            @else
                                                <a href="#">Cadastrar endereço</a>
                                            @endif
                                        </div>
                                        <!--end::Description-->
                                    </div>
                                    <!--end::Details-->
                                    <!--begin::Actions-->
                                    <div class="d-flex mb-4">
                                        {{-- <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                                    data-bs-toggle="modal" data-bs-target="#kt_modal_users_search">Add User</a> --}}
                                        <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                            data-bs-toggle="modal" data-bs-target="#kt_modal_new_target">
                                            <i class="fa fa-user-plus"></i> Usuários
                                        </a>
                                        <a href="#" class="btn btn-sm btn-primary me-3" data-bs-toggle="modal"
                                            data-bs-target="#prestacaoConta">
                                            <i class="bi bi-file-earmark-text"></i> Relatório
                                        </a>
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
                                                    <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                        Payments</div>
                                                </div>
                                                <!--end::Heading-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link px-3">Create Invoice</a>
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                    data-kt-menu-placement="right-end">
                                                    <a href="#" class="menu-link px-3">
                                                        <span class="menu-title">Cadastro</span>
                                                        <span class="menu-arrow"></span>
                                                    </a>
                                                    <!--begin::Menu sub-->
                                                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                        <!--begin::Menu item-->
                                                        <div class="menu-item px-3">
                                                            <!-- Link que abre o modal -->


                                                            <a class="menu-link px-3 {{ $activeTab === 'entidadeFinanceira' ? 'active' : '' }}"
                                                            href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'entidadeFinanceira']) }}">
                                                            Entidade Financeira
                                                        </a>
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
                                                    </div>
                                                    <!--end::Menu sub-->
                                                </div>
                                                <!--end::Menu item-->
                                                <!--begin::Menu item-->
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link flex-stack px-3">Create Payment
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
                                                    <a href="#" class="menu-link px-3">Ajustes</a>
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
                                    @php
                                        $stats = [
                                            [
                                                'value' => $totalSaldoAtual,
                                                'label' => 'Receita Bruta',
                                                'prefix' => 'R$ ',
                                                'icon' => 'bi bi-calculator svg-icon-success', // Adicione um ícone se necessário
                                            ],
                                            [
                                                'value' => $despesasMes,
                                                'label' => 'Despesas do Mês',
                                                'prefix' => 'R$ ',
                                                'icon' => 'bi bi-arrow-down svg-icon-danger', // Ícone para despesas
                                            ],
                                            [
                                                'value' => $receitaMes,
                                                'label' => 'Receitas do Mês',
                                                'prefix' => 'R$ ',
                                                'icon' => 'bi bi-arrow-up svg-icon-success', // Ícone para receitas
                                            ],
                                        ];
                                    @endphp

                                    <div class="d-flex flex-wrap">
                                        @foreach ($stats as $stat)
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    @if ($stat['icon'])
                                                        <span class="svg-icon svg-icon-3 {{ $stat['icon'] }} me-2">
                                                            <!-- Ícone SVG personalizado pode ser inserido aqui -->
                                                        </span>
                                                    @endif
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $stat['value'] }}"
                                                        data-kt-countup-prefix="{{ $stat['prefix'] }}">0
                                                    </div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400">{{ $stat['label'] }}</div>
                                                <!--end::Label-->
                                            </div>
                                        @endforeach
                                    </div>

                                    <!--end::Stats-->
                                    <!--begin::Users-->
                                    <div class="symbol-group symbol-hover mb-3">
                                        <!--begin::User-->
                                        @foreach ($companyShow->users->take(6) as $user)
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="{{ $user->name }}">
                                                <img alt="{{ $user->name }}"
                                                    src="{{ $user->avatar && $user->avatar !== 'tenant/blank.png'
                                                        ? route('file', ['path' => $user->avatar])
                                                        : '/assets/media/avatars/blank.png' }}" />
                                            </div>
                                        @endforeach

                                        @if ($companyShow->users->count() > 6)
                                            <a href="#" class="symbol symbol-35px symbol-circle"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_view_users"
                                                title="Mais {{ $companyShow->users->count() - 5 }} usuários">
                                                <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                                    data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                    data-bs-trigger="hover" title="View more users">
                                                    +{{ $companyShow->users->count() - 5 }}
                                                </span>
                                            </a>
                                        @endif
                                        @if ($companyShow->users->count() < 1)
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Nenhum usuário cadastrado">
                                                <span class="symbol-label fs-8 fw-bold bg-light text-gray-800">
                                                    {{ 0 }}
                                                </span>
                                            </div>
                                        @endif
                                        <!--end::User-->
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
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'overview' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'overview']) }}">
                                    Panorama
                                </a>
                            </li>
                            <!--end::Nav item-->

                            <!--begin::Nav item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'financeiro' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'financeiro']) }}">
                                    Financeiro
                                </a>
                            </li>
                            <!--end::Nav item-->

                            <!--begin::Nav item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'budget' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'budget']) }}">
                                    Budget
                                </a>
                            </li>
                            <!--end::Nav item-->

                            <!--begin::Nav item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'users' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'users']) }}">
                                    Usuários
                                </a>
                            </li>
                            <!--end::Nav item-->

                            <!--begin::Nav item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'files' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'files']) }}">
                                    Files
                                </a>
                            </li>
                            <!--end::Nav item-->

                            <!--begin::Nav item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'activity' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'activity']) }}">
                                    Activity
                                </a>
                            </li>
                            <!--end::Nav item-->

                            <!--begin::Nav item-->
                            <li class="nav-item">
                                <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'settings' ? 'active' : '' }}"
                                    href="{{ route('company.show', ['company' => $companyShow->id, 'tab' => 'settings']) }}">
                                    Ajustes
                                </a>
                            </li>
                            <!--end::Nav item-->
                        </ul>
                        <!--end::Nav-->
                    </div>
                </div>
                <!--end::Navbar-->
                <!--begin::Card-->
                @includeIf("app.company.tabs.{$activeTab}")
                <!--end::Card-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content-->

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
                        <form method="POST" id="kt_modal_new_target_form" class="form"
                            action="{{ route('users.store') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="company_id" value="{{ $companyShow->id }}" />

                            <!--begin::Heading-->
                            <div class="mb-13 text-center">
                                <!--begin::Title-->
                                <h1 class="mb-3">Criar Novo Usuário</h1>
                                <!--end::Title-->
                                <!--begin::Description-->
                                <div class="text-muted fw-semibold fs-5">Preencha os campos abaixo para adicionar um
                                    novo usuário.</div>
                                <!--end::Description-->
                            </div>
                            <!--end::Heading-->
                            <div class="d-flex justify-content-center text-center">
                                <div class="fv-row mb-7 align-items-center">
                                    <!--begin::Label-->
                                    <label class="d-block fw-semibold fs-6 mb-5 text-center">Avatar</label>
                                    <!--end::Label-->

                                    <!--begin::Image placeholder-->
                                    <style>
                                        .image-input-placeholder {
                                            background-image: url('/assets/media/svg/files/blank-image.svg');
                                        }

                                        [data-bs-theme="dark"] .image-input-placeholder {
                                            background-image: url('/assets/media/svg/files/blank-image-dark.svg');
                                        }
                                    </style>
                                    <!--end::Image placeholder-->

                                    <!--begin::Image input-->
                                    <div class="image-input image-input-outline image-input-placeholder"
                                        data-kt-image-input="true">
                                        <!--begin::Preview existing avatar-->
                                        <div class="image-input-wrapper w-125px h-125px"
                                            style="background-image: url({{ old('avatar') ?? '/assets/media/avatars/blank.png' }});">
                                        </div>
                                        <!--end::Preview existing avatar-->

                                        <!--begin::Label-->
                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Change avatar">
                                            <i class="bi bi-pencil-fill fs-7"></i>
                                            <!--begin::Inputs-->
                                            <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar" />
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

                                    <!--begin::Hint-->
                                    <div class="form-text text-center">Arquivos permitidos: png, jpg, jpeg.</div>
                                    <!--end::Hint-->
                                </div>
                            </div>

                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Nome e Sobrenome</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Specify a target name for future usage and reference"></i>
                                </label>
                                <!--end::Label-->
                                <input type="text" class="form-control form-control-solid"
                                    placeholder="Enter Target Title" name="name" value="{{ old('name') }}" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">E-mail</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Specify a target name for future usage and reference"></i>
                                </label>
                                <!--end::Label-->
                                <input type="email" class="form-control form-control-solid"
                                    placeholder="exemplo@dominus.com" name="email" id="email"
                                    value="{{ old('email') }}" />
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="row g-9 mb-8">
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Senha</label>
                                    <div class="fv-row" data-kt-password-meter="true">
                                        <div class="position-relative mb-3">
                                            <!-- Campo de Senha -->
                                            <input class="form-control form-control-lg form-control-solid"
                                                type="password" placeholder="********" name="password"
                                                id="password" autocomplete="off" />
                                            <!-- Botão de Visibilidade -->
                                            <span
                                                class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                                onclick="togglePassword('password')">
                                                <i class="bi bi-eye" id="password_icon"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Col-->

                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Confirmar Senha</label>
                                    <div class="position-relative mb-3">
                                        <!-- Campo de Confirmação -->
                                        <input class="form-control form-control-lg form-control-solid" type="password"
                                            placeholder="********" name="password_confirmation"
                                            id="password_confirmation" autocomplete="off" />
                                        <!-- Botão de Visibilidade -->
                                        <span
                                            class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2"
                                            onclick="togglePassword('password_confirmation')">
                                            <i class="bi bi-eye" id="password_confirmation_icon"></i>
                                        </span>
                                    </div>
                                </div>
                                <!--end::Col-->
                                <div class="text-muted mt-2">Use 8 ou mais caracteres com uma combinação de letras,
                                    números
                                    e símbolos.</div>
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="mb-7">
                                <!--begin::Label-->
                                <label class="required fw-semibold fs-6 mb-5">Permições</label>
                                <!--end::Label-->
                                <!--begin::Roles-->
                                <!--begin::Input row-->
                                <div class="d-flex fv-row">
                                    <!--begin::Checkbox-->
                                    <div class="form-check form-check-custom form-check-solid">
                                        <!--begin::Input-->
                                        <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                            value="1" id="kt_modal_update_role_option_0" />
                                        <!--end::Input-->
                                        <!--begin::Label-->
                                        <label class="form-check-label" for="kt_modal_update_role_option_0">
                                            <div class="fw-bold text-gray-800">Global</div>
                                            <div class="text-gray-600">Melhor para
                                                desenvolvedores ou pessoas que usam
                                                principalmente a API</div>
                                        </label>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Input row-->
                                <div class='separator separator-dashed my-5'></div>
                                <!--begin::Input row-->
                                <div class="d-flex fv-row">
                                    <!--begin::Checkbox-->
                                    <div class="form-check form-check-custom form-check-solid">
                                        <!--begin::Input-->
                                        <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                            value="2" id="kt_modal_update_role_option_1" />
                                        <!--end::Input-->
                                        <!--begin::Label-->
                                        <label class="form-check-label" for="kt_modal_update_role_option_1">
                                            <div class="fw-bold text-gray-800">Administrador
                                            </div>
                                            <div class="text-gray-600">Ideal para pessoas que
                                                precisam de acesso total aos dados da empresa.
                                            </div>
                                        </label>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Input row-->
                                <div class='separator separator-dashed my-5'></div>
                                <!--begin::Input row-->
                                <div class="d-flex fv-row">
                                    <!--begin::Checkbox-->
                                    <div class="form-check form-check-custom form-check-solid">
                                        <!--begin::Input-->
                                        <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                            value="3" id="kt_modal_update_role_option_2" />
                                        <!--end::Input-->
                                        <!--begin::Label-->
                                        <label class="form-check-label" for="kt_modal_update_role_option_2">
                                            <div class="fw-bold text-gray-800">Admin User</div>
                                            <div class="text-gray-600">Ideal para funcionários
                                                que gerencia as filias </div>
                                        </label>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Input row-->
                                <div class='separator separator-dashed my-5'></div>
                                <!--begin::Input row-->
                                <div class="d-flex fv-row">
                                    <!--begin::Checkbox-->
                                    <div class="form-check form-check-custom form-check-solid">
                                        <!--begin::Input-->
                                        <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                            value="4" id="kt_modal_update_role_option_3" />
                                        <!--end::Input-->
                                        <!--begin::Label-->
                                        <label class="form-check-label" for="kt_modal_update_role_option_3">
                                            <div class="fw-bold text-gray-800">Usuários Comuns
                                            </div>
                                            <div class="text-gray-600">Para usuários que tratam
                                                dos dados da organização</div>
                                        </label>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Input row-->
                                <div class='separator separator-dashed my-5'></div>
                                <!--begin::Input row-->
                                <div class="d-flex fv-row">
                                    <!--begin::Checkbox-->
                                    <div class="form-check form-check-custom form-check-solid">
                                        <!--begin::Input-->
                                        <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                            value="5" id="kt_modal_update_role_option_4" />
                                        <!--end::Input-->
                                        <!--begin::Label-->
                                        <label class="form-check-label" for="kt_modal_update_role_option_4">
                                            <div class="fw-bold text-gray-800">Sub Usuário
                                            </div>
                                            <div class="text-gray-600">Ideal para pessoas que
                                                precisam visualizar dados de conteúdo, mas não
                                                precisa fazer quaisquer atualizações</div>
                                        </label>
                                        <!--end::Label-->
                                    </div>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Input row-->
                                <!--end::Roles-->
                            </div>
                            <!--end::Input group-->
                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8">
                                <label class="fs-6 fw-semibold mb-2">Detalhes de Usuário</label>
                                <textarea class="form-control form-control-solid" rows="3" name="details"
                                    placeholder="Adicione detalhes sobre o usuário">{{ old('details') }}</textarea>
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="d-flex flex-stack mb-8">
                                <!--begin::Label-->
                                <div class="me-5">
                                    <label class="fs-6 fw-semibold">Status do Usuário</label>
                                    <div class="fs-7 fw-semibold text-muted">
                                        O usuário será <span id="status-text">ativo</span> para acessar o sistema.
                                    </div>
                                </div>
                                <!--end::Label-->
                                <!--begin::Switch-->
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        {{ old('status', 1) ? 'checked' : '' }} name="status"
                                        id="user-status-switch" />
                                    <span class="form-check-label fw-semibold text-muted">Ativo</span>
                                </label>
                                <!--end::Switch-->
                            </div>

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const statusSwitch = document.getElementById('user-status-switch');
                                    const statusText = document.getElementById('status-text');

                                    if (statusSwitch && statusText) { // Verifica se os elementos existem
                                        function updateStatusText() {
                                            statusText.textContent = statusSwitch.checked ? 'ativo' : 'desativado';
                                        }

                                        statusSwitch.addEventListener('change', updateStatusText);
                                        updateStatusText(); // Inicializa o texto
                                    }
                                });
                            </script>

                            <!--end::Input group-->

                            <!--begin::Input group-->
                            <div class="mb-15 fv-row">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack">
                                    <!--begin::Label-->
                                    <div class="fw-semibold me-5">
                                        <label class="fs-6">Notificações</label>
                                        <div class="fs-7 text-muted">Permitir notificações por Email ou Telefone</div>
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Checkboxes-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-custom form-check-solid me-10">
                                            <input class="form-check-input" type="checkbox" name="notifications[]"
                                                value="email"
                                                {{ in_array('email', old('notifications', [])) ? 'checked' : '' }} />
                                            <span class="form-check-label fw-semibold">Email</span>
                                        </label>
                                        <!--end::Checkbox-->
                                        <!--begin::Checkbox-->
                                        <label class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="notifications[]"
                                                value="phone"
                                                {{ in_array('phone', old('notifications', [])) ? 'checked' : '' }} />
                                            <span class="form-check-label fw-semibold">Telefone</span>
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
                                <button type="submit" id="kt_modal_new_target_submit"
                                    class="btn btn-primary">Submit</button>

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

@include('app.components.modals.company.prestacao')


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->

{{-- Tab::setting --}}
<script src="/assets/js/custom/apps/company/settings/settings.js"></script>
<script src="/assets/js/custom/apps/company/settings/sales.js"></script>

<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
<script src="/assets/js/custom/utilities/modals/novo-usuario/novo-usuario.js"></script>
<script src="/assets/js/custom/apps/projects/list/list.js"></script>
<script src="/assets/js/custom/apps/ecommerce/customers/details/transaction-history.js"></script>

<!--end::Custom Javascript-->
{{-- Modal::Pestacao de Conta --}}
<script src="/assets/js/custom/utilities/modals/company/prestacaoConta.js"></script>
{{-- Tab::Usuários --}}
<script src="/assets/js/custom/apps/company/users.js"></script>
{{-- Tab:: Panorama --}}
<script src="/assets/js/custom/apps/company/project.js"></script>

<script>
    // Alternar visibilidade da senha
    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.getElementById(`${fieldId}_icon`);

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            passwordField.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
    //Placeholder do Select2 dos Estados
    $(document).ready(function() {
    $('#uf').select2({
        placeholder: "Selecione um estado",
        allowClear: true
    });
});


</script>
<script>

    $(document).ready(function() {
        // Quando o campo CEP perde o foco
        $('#cep').on('blur', function() {
            var cep = $(this).val().replace(/\D/g, '');

            if (cep !== "") {
                // Verifica se o CEP tem 8 dígitos
                var validacep = /^[0-9]{8}$/;

                if(validacep.test(cep)) {
                    // Preenche os campos com "..." enquanto carrega
                    $('#logradouro').val('...');
                    $('#bairro').val('...');
                    $('#localidade').val('...');
                    $('#uf').val('...');
                    $('#ibge').val('...');
                    $('#complemento').val('...');

                    // Faz a requisição para a API ViaCEP
                    $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

                        if (!("erro" in dados)) {
                            // Atualiza os campos com os valores da consulta
                            $('#logradouro').val(dados.logradouro);
                            $('#bairro').val(dados.bairro);
                            $('#localidade').val(dados.localidade);
                            $('#uf').val(dados.uf).trigger('change'); // Atualiza o select2
                            $('#ibge').val(dados.ibge);
                            $('#complemento').val(dados.complemento);
                        } else {
                            // CEP não encontrado
                            alert("CEP não encontrado.");
                        }
                    });
                } else {
                    alert("Formato de CEP inválido.");
                }
            } else {
                // CEP sem valor, limpa o formulário
                limpaFormularioCEP();
            }
        });

        function limpaFormularioCEP() {
            // Limpa valores do formulário de CEP
            $('#logradouro').val('');
            $('#bairro').val('');
            $('#localidade').val('');
            $('#uf').val('').trigger('change');
            $('#ibge').val('');
            $('#complemento').val('');
        }
    });
</script>
