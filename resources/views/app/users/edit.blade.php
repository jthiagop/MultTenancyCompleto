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
                            Editar Informações de Usuários</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted"><a href="{{ route('users.index') }}"
                                    class="text-muted text-hover-primary">Lista de usuário</a></li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <li class="breadcrumb-item text-muted"><a href="{{ route('users.edit', $user->id) }}"
                                    class="text-muted text-hover-primary">Editar usuário</a></li>
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Basic info-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_profile_details" aria-expanded="true"
                            aria-controls="kt_account_profile_details">
                            <!--begin::Card title-->
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Detalhes do Usuário</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_profile_details" class="collapse show">
                            <!--begin::Form-->
                            <form method="POST" action="{{ route('users.update', $user->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <!--begin::Card body-->
                                @include('__massage')

                                <div class="card-body border-top p-9">
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">Avatar</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Input group-->
                                            <!--begin::Label-->
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
                                                <div class="image-input-wrapper w-150px h-150px"
                                                    style="background-image: url({{ route('file', ['path' => $user->avatar]) }});">
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
                                            <div class="text-muted fs-7">Somente arquivos de imagem nos formatos *.png,
                                                *.jpg e *.jpeg são aceitos.</div>
                                            <!--end::Hint-->
                                        </div>
                                        <!--end::Input group-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nome e
                                            Sobrenome</label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8">
                                            <!--begin::Row-->
                                            <div class="row">
                                                <!--begin::Col-->
                                                <div class="col-lg-12 fv-row">
                                                    <input type="text" name="name"
                                                        class="form-control form-control-lg form-control-solid mb-3 mb-lg-0"
                                                        placeholder="Nome" value="{{ old('name', $user->name) }}"
                                                        required autofocus autocomplete="name" />
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Row-->
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                    <!--begin::Input group-->
                                    <div class="row mb-6">
                                        <!--begin::Label-->
                                        <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                            <span class="required">E-mail</span>
                                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                                title="E-mail must be active"></i>
                                        </label>
                                        <!--end::Label-->
                                        <!--begin::Col-->
                                        <div class="col-lg-8 fv-row">
                                            <input type="email" name="email"
                                                class="form-control form-control-lg form-control-solid"
                                                placeholder="E-mail" value="{{ old('email', $user->email) }}" />
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Input group-->
                                </div>
                                <!--end::Card body-->
                                <!--begin::Actions-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="submit" class="btn btn-primary">Salvar</button>
                                </div>
                                <!--end::Actions-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Basic info-->
                    <!--begin::Sign-in Method-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_signin_method">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Redefinir</h3>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_signin_method" class="collapse show">
                            <!--begin::Card body-->
                            <div class="card-body border-top p-9">
                                <!--begin::Email Address-->
                                <div class="d-flex flex-wrap align-items-center">
                                    <!--begin::Label-->
                                    <div id="kt_signin_email">
                                        <div class="fs-6 fw-bold mb-1">Email</div>
                                        <div class="fw-semibold text-gray-600">{{ $user->email }}</div>
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Edit-->
                                    <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                                        <!--begin::Form-->
                                        <form id="kt_signin_change_email" class="form" novalidate="novalidate">
                                            @csrf
                                            <div class="row mb-6">
                                                <div class="col-lg-6 mb-4 mb-lg-0">
                                                    <div class="fv-row mb-0">
                                                        <label for="emailaddress"
                                                            class="form-label fs-6 fw-bold mb-3">Informe o novo
                                                            email</label>
                                                        <input type="email"
                                                            class="form-control form-control-lg form-control-solid"
                                                            id="emailaddress" placeholder="Email" name="email"
                                                            value="{{ $user->email }}" />
                                                        <div class="fv-plugins-message-container invalid-feedback"
                                                            id="email-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="fv-row mb-0">
                                                        <label for="confirmemailpassword"
                                                            class="form-label fs-6 fw-bold mb-3">Confirme a
                                                            senha</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="password" id="confirmemailpassword"
                                                            placeholder="Digite sua senha atual" />
                                                        <div class="fv-plugins-message-container invalid-feedback"
                                                            id="password-error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <button id="kt_signin_submit" type="button"
                                                    class="btn btn-primary me-2 px-6">
                                                    <span class="indicator-label">Atualizar Email</span>
                                                    <span class="indicator-progress">Aguarde...
                                                        <span
                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                </button>
                                                <button id="kt_signin_cancel" type="button"
                                                    class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancelar</button>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Edit-->
                                    <!--begin::Action-->
                                    <div id="kt_signin_email_button" class="ms-auto">
                                        <button class="btn btn-light btn-active-light-primary">Mudar Email</button>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Email Address-->
                                <!--begin::Separator-->
                                <div class="separator separator-dashed my-6"></div>
                                <!--end::Separator-->
                                <!--begin::Password-->
                                <div class="d-flex flex-wrap align-items-center mb-10">
                                    <!--begin::Label-->
                                    <div id="kt_signin_password">
                                        <div class="fs-6 fw-bold mb-1">Senha</div>
                                        <div class="fw-semibold text-gray-600">************</div>
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Edit-->
                                    <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                                        <!--begin::Form-->
                                        <form id="kt_signin_change_password" class="form" novalidate="novalidate">
                                            @csrf
                                            <!-- Checkbox para criar senha automaticamente -->
                                            <div class="form-check mb-5">
                                                <input class="form-check-input" type="checkbox" name="automatic_password"
                                                    id="automatic_password" checked />
                                                <label class="form-check-label fw-semibold" for="automatic_password">
                                                    Criar uma senha automaticamente.
                                                </label>
                                            </div>

                                            <!-- Campo de senha manual (oculto quando automático está marcado) -->
                                            <div class="row mb-6" id="manual_password_container" style="display: none;">
                                                <div class="col-lg-6 mb-4 mb-lg-0">
                                                    <div class="fv-row mb-0">
                                                        <label for="newpassword"
                                                            class="form-label fs-6 fw-bold mb-3">Nova Senha *</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="password" id="newpassword"
                                                            placeholder="Digite a nova senha" />
                                                        <div class="fv-plugins-message-container invalid-feedback"
                                                            id="password-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="fv-row mb-0">
                                                        <label for="confirmpassword"
                                                            class="form-label fs-6 fw-bold mb-3">Confirmar Nova Senha *</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="password_confirmation" id="confirmpassword"
                                                            placeholder="Confirme a nova senha" />
                                                        <div class="fv-plugins-message-container invalid-feedback"
                                                            id="password_confirmation-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Checkbox para obrigar alteração no próximo login -->
                                            <div class="form-check mb-5">
                                                <input class="form-check-input" type="checkbox" name="require_change"
                                                    id="require_change" checked />
                                                <label class="form-check-label fw-semibold" for="require_change">
                                                    Exigir que este usuário altere a senha quando entrar pela primeira vez
                                                </label>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2 mt-5">
                                                <button id="kt_password_submit" type="button"
                                                    class="btn btn-primary btn-sm">
                                                    <span class="indicator-label">Redefinir Senha</span>
                                                    <span class="indicator-progress">Aguarde...
                                                        <span
                                                            class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                </button>
                                                <button id="kt_password_cancel" type="button"
                                                    class="btn btn-secondary btn-sm">Cancelar</button>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Edit-->
                                    <!--begin::Action-->
                                    <div id="kt_signin_password_button" class="ms-auto">
                                        <button class="btn btn-light btn-active-light-primary">Redefinir Senha</button>
                                    </div>
                                    <!--end::Action-->

                                </div>
                                <!--end::Password-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Sign-in Method-->
                    <!--begin::Connected Accounts-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_connected_accounts" aria-expanded="true"
                            aria-controls="kt_account_connected_accounts">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0 d-flex align-items-center">
                                    <i class="fas fa-building me-3 fs-2 text-primary"></i>
                                    Companhias Conectadas
                                </h3>
                            </div>
                            <span class="svg-icon svg-icon-1 rotate-180">
                                <i class="fas fa-chevron-down fs-3 text-gray-400"></i>
                            </span>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_connected_accounts" class="collapse show">
                            <!--begin::Form-->
                            <form method="POST" action="{{ route('users.filiais.update', $user->id) }}">
                                @csrf
                                @method('put')

                                <!--begin::Card body-->
                                <div class="card-body border-top p-9">
                                    <!--begin::Notice-->
                                    <div
                                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed mb-9 p-6">
                                        <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                            <i class="fas fa-info-circle fs-2x text-primary"></i>
                                        </span>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold mb-3">Gerenciamento de Acesso</h4>
                                                <div class="fs-6 text-gray-700">
                                                    Selecione as companhias às quais este usuário terá acesso.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Notice-->

                                    <!--begin::Items-->
                                    <div class="py-2">
                                        {{-- Pega todos os IDs das companhias do usuário e coloca em um array. --}}
                                        @php
                                            $userCompanyIds = $user->companies->pluck('id')->toArray();
                                        @endphp

                                        @foreach ($companies as $company)
                                            <!--begin::Item-->
                                            <div class="d-flex flex-stack">
                                                <div class="d-flex">
                                                    <div class="symbol symbol-circle symbol-35px symbol-md-40px">
                                                        @if ($company->avatar && !empty($company->avatar))
                                                            <img src="{{ route('file', ['path' => $company->avatar]) }}"
                                                                class="w-35px me-6 " alt="{{ $company->name }}" />
                                                        @else
                                                            <div
                                                                class="symbol-label fs-6 fw-semibold bg-primary text-inverse-primary">
                                                                {{ strtoupper(substr($company->name ?? 'C', 0, 1)) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span
                                                            class="fs-5 text-dark text-hover-primary fw-bold">{{ $company->name }}</span>
                                                        <div class="fs-6 fw-semibold text-gray-400">
                                                            {{ $company->addresses->rua ?? '' }},
                                                            {{ $company->addresses->cidade ?? '' }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div
                                                        class="form-check form-check-solid form-check-custom form-switch">
                                                        {{-- O name="filiais[]" envia um array de IDs --}}
                                                        <input class="form-check-input w-45px h-30px" type="checkbox"
                                                            name="filiais[]" value="{{ $company->id }}"
                                                            id="company_switch_{{ $company->id }}"
                                                            {{-- Verifica se o ID da companhia está no array de acesso do usuário --}}
                                                            @if (in_array($company->id, $userCompanyIds)) checked @endif />
                                                        <label class="form-check-label"
                                                            for="company_switch_{{ $company->id }}"></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Item-->
                                            <div class="separator separator-dashed my-5"></div>
                                        @endforeach
                                    </div>
                                    <!--end::Items-->
                                </div>
                                <!--end::Card body-->
                                <!--begin::Card footer-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="submit" class="btn btn-primary px-6">
                                        <i class="fas fa-sync-alt me-2"></i>
                                        Atualizar Acessos
                                    </button>
                                </div>
                                <!--end::Card footer-->

                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->

                    </div>
                    <!--end::Connected Accounts-->
                    <!--begin::Notifications-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_email_preferences" aria-expanded="true"
                            aria-controls="kt_account_email_preferences">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Permissões por Módulo</h3>
                            </div>
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_email_preferences" class="collapse show">
                            <!--begin::Form-->
                            <form method="POST" action="{{ route('users.permissions.update', $user->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <!--begin::Card body-->
                                <div class="card-body border-top px-9 py-9">
                                    @php
                                        $permissionService = new \App\Services\PermissionService();
                                        $actionNames = $permissionService->getActionNames();
                                        // $moduleIcons já vem do controller com os dados do banco
                                        // Se não existir, usar fallback padrão
                                        $defaultIcon = asset('assets/media/avatars/blank.png');
                                    @endphp

                                    <!--begin::Legenda-->
                                    @if(!empty($rolePermissions ?? []))
                                    <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-5 mb-6">
                                        <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4 mb-5 mb-sm-0"><span class="path1"></span><span class="path2"></span></i>
                                        <div class="d-flex flex-column pe-0 pe-sm-10">
                                            <h5 class="mb-1">Permissões por Role: <span class="badge badge-primary">{{ $user->getRoleNames()->implode(', ') }}</span></h5>
                                            <span class="text-muted fs-7">Permissões marcadas com <span class="badge badge-light-primary badge-sm"><i class="fas fa-shield-alt text-primary fs-9 me-1"></i>via role</span> são herdadas do papel do usuário. Desmarque para remover o acesso.</span>
                                        </div>
                                    </div>
                                    @endif
                                    <!--end::Legenda-->

                                    @if (isset($permissionsByModule) && !empty($permissionsByModule))
                                        <!--begin::Payment method-->
                                        <div class="card card-flush pt-3 mb-5 mb-lg-10" data-kt-subscriptions-form="pricing">
                                            <!--begin::Card body-->
                                            <div class="card-body pt-0">
                                                <!--begin::Options-->
                                                <div id="kt_create_new_payment_method">
                                                    @foreach ($permissionsByModule as $module => $permissions)
                                                        @php
                                                            $moduleName = $moduleNames[$module] ?? ucfirst($module);
                                                            // Buscar ícone do módulo do banco de dados ou usar padrão
                                                            $moduleIcon = $moduleIcons[$module] ?? $defaultIcon;
                                                            $isFirst = $loop->first;
                                                            $collapseId = "kt_module_permissions_{$module}";
                                                            $checkedCount = 0;
                                                            $totalCount = count($permissions);
                                                            foreach ($permissions as $permission) {
                                                                if (in_array($permission->id, $userPermissions ?? [])) {
                                                                    $checkedCount++;
                                                                }
                                                            }
                                                        @endphp

                                                        <!--begin::Option-->
                                                        <div class="py-1">
                                                            <!--begin::Header-->
                                                            <div class="py-3 d-flex flex-stack flex-wrap">
                                                                <!--begin::Toggle-->
                                                                <div class="d-flex align-items-center collapsible toggle {{ $isFirst ? '' : 'collapsed' }}"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#{{ $collapseId }}">
                                                                    <!--begin::Arrow-->
                                                                    <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen036.svg-->
                                                                        <span class="svg-icon toggle-on svg-icon-primary svg-icon-2">
                                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                                                                <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                        <!--begin::Svg Icon | path: icons/duotune/general/gen035.svg-->
                                                                        <span class="svg-icon toggle-off svg-icon-2">
                                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                                                                                <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                                                                                <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                    </div>
                                                                    <!--end::Arrow-->
                                                                    <!--begin::Logo-->
                                                                    <img src="{{ $moduleIcon }}" class="w-40px me-3" alt="{{ $moduleName }}" />
                                                                    <!--end::Logo-->
                                                                    <!--begin::Summary-->
                                                                    <div class="me-3 flex-grow-1">
                                                                        <div class="d-flex align-items-center fw-bold">
                                                                            {{ $moduleName }}
                                                                            @if ($checkedCount > 0 && $checkedCount < $totalCount)
                                                                                <div class="badge badge-light-warning ms-5">{{ $checkedCount }}/{{ $totalCount }}</div>
                                                                            @elseif ($checkedCount == $totalCount)
                                                                                <div class="badge badge-light-success ms-5">Completo</div>
                                                                            @endif
                                                                        </div>
                                                                        <div class="text-muted">{{ $totalCount }} permissões disponíveis</div>
                                                                    </div>
                                                                    <!--end::Summary-->
                                                                </div>
                                                                <!--end::Toggle-->
                                                                <!--begin::Input-->
                                                                <div class="d-flex my-3 ms-9">
                                                                    <!--begin::Checkbox Select All-->
                                                                    <label class="form-check form-check-custom form-check-solid me-5">
                                                                        <input class="form-check-input module-select-all" type="checkbox"
                                                                            data-module="{{ $module }}"
                                                                            id="module_select_all_{{ $module }}" />
                                                                        <span class="form-check-label fw-semibold">Selecionar Tudo</span>
                                                                    </label>
                                                                    <!--end::Checkbox Select All-->
                                                                </div>
                                                                <!--end::Input-->
                                                            </div>
                                                            <!--end::Header-->
                                                            <!--begin::Body-->
                                                            <div id="{{ $collapseId }}" class="collapse {{ $isFirst ? 'show' : '' }} fs-6 ps-10">
                                                                <!--begin::Details-->
                                                                <div class="d-flex flex-wrap py-5">
                                                                    <!--begin::Col-->
                                                                    <div class="flex-equal w-100">
                                                                        <div class="row g-3">
                                                                            @foreach ($permissions as $permission)
                                                                                @php
                                                                                    $parts = explode('.', $permission->name);
                                                                                    $action = end($parts);
                                                                                    $actionName = $actionNames[$action] ?? ucfirst($action);

                                                                                    // Determinar badge color baseado na ação
                                                                                    $badgeClass = 'badge-light-primary';
                                                                                    if ($action == 'delete') {
                                                                                        $badgeClass = 'badge-light-danger';
                                                                                    } elseif ($action == 'create' || $action == 'store') {
                                                                                        $badgeClass = 'badge-light-success';
                                                                                    } elseif ($action == 'edit' || $action == 'update') {
                                                                                        $badgeClass = 'badge-light-warning';
                                                                                    } elseif ($action == 'index' || $action == 'show') {
                                                                                        $badgeClass = 'badge-light-info';
                                                                                    }
                                                                                @endphp
                                                                                @php
                                                                                    $isFromRole = in_array($permission->id, $rolePermissions ?? []);
                                                                                    $isDirect = in_array($permission->id, $directPermissions ?? []);
                                                                                    $isChecked = in_array($permission->id, $userPermissions ?? []);
                                                                                    $borderClass = $isFromRole && !$isDirect ? 'border-primary border-dashed' : 'border-gray-300';
                                                                                @endphp
                                                                                <div class="col-md-6 col-lg-4 mb-3">
                                                                                    <div class="form-check form-check-custom form-check-solid p-3 border {{ $borderClass }} rounded">
                                                                                        <input class="form-check-input permission-checkbox"
                                                                                            type="checkbox"
                                                                                            name="permissions[]"
                                                                                            value="{{ $permission->id }}"
                                                                                            data-module="{{ $module }}"
                                                                                            id="permission_{{ $permission->id }}"
                                                                                            @if ($isChecked) checked @endif />
                                                                                        <label class="form-check-label w-100" for="permission_{{ $permission->id }}">
                                                                                            <div class="d-flex align-items-center">
                                                                                                <span class="fw-semibold text-dark me-2">{{ $actionName }}</span>
                                                                                                <span class="badge {{ $badgeClass }} fs-8">{{ $action }}</span>
                                                                                                @if($isFromRole && !$isDirect)
                                                                                                    <span class="badge badge-light-primary fs-9 ms-1"><i class="fas fa-shield-alt text-primary fs-9 me-1"></i>via role</span>
                                                                                                @endif
                                                                                            </div>
                                                                                            @if (count($parts) > 2)
                                                                                                <div class="text-muted fs-7 mt-1">{{ $permission->name }}</div>
                                                                                            @endif
                                                                                        </label>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                    <!--end::Col-->
                                                                </div>
                                                                <!--end::Details-->
                                                            </div>
                                                            <!--end::Body-->
                                                        </div>
                                                        <!--end::Option-->
                                                        @if (!$loop->last)
                                                            <div class="separator separator-dashed"></div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <!--end::Options-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                        <!--end::Payment method-->
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                            Nenhuma permissão encontrada. Execute o seeder de permissões.
                                        </div>
                                    @endif

                                    <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                                </div>
                                <!--end::Card body-->
                                <!--begin::Card footer-->
                                <div class="card-footer d-flex justify-content-between py-6 px-9">
                                    @if($isFirstUser ?? false)
                                        <form method="POST" action="{{ route('users.assign-all-permissions', $user->id) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success px-6" onclick="return confirm('Tem certeza que deseja atribuir TODAS as permissões disponíveis a este usuário? Esta ação substituirá todas as permissões atuais.')">
                                                <i class="fas fa-crown me-2"></i> Atribuir Todas as Permissões (Usuário Supremo)
                                            </button>
                                        </form>
                                    @endif
                                    <div class="ms-auto">
                                        <button type="submit" class="btn btn-primary px-6">
                                            <i class="fas fa-sync-alt me-2"></i> Atualizar Permissões
                                        </button>
                                    </div>
                                </div>
                                <!--end::Card footer-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Notifications-->

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Selecionar todos os checkboxes de um módulo
                            document.querySelectorAll('.module-select-all').forEach(function(checkbox) {
                                checkbox.addEventListener('change', function() {
                                    const module = this.getAttribute('data-module');
                                    const moduleCheckboxes = document.querySelectorAll(
                                        '.permission-checkbox[data-module="' + module + '"]'
                                    );

                                    moduleCheckboxes.forEach(function(cb) {
                                        cb.checked = checkbox.checked;
                                    });
                                });
                            });

                            // Atualizar checkbox "selecionar tudo" quando checkboxes individuais mudarem
                            document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                                checkbox.addEventListener('change', function() {
                                    const module = this.getAttribute('data-module');
                                    const moduleCheckboxes = document.querySelectorAll(
                                        '.permission-checkbox[data-module="' + module + '"]'
                                    );
                                    const moduleSelectAll = document.querySelector(
                                        '.module-select-all[data-module="' + module + '"]'
                                    );

                                    const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                                    const someChecked = Array.from(moduleCheckboxes).some(cb => cb.checked);

                                    if (moduleSelectAll) {
                                        moduleSelectAll.checked = allChecked;
                                        moduleSelectAll.indeterminate = someChecked && !allChecked;
                                    }
                                });
                            });

                            // Verificar estado inicial ao carregar (para edição)
                            document.querySelectorAll('.module-select-all').forEach(function(checkbox) {
                                const module = checkbox.getAttribute('data-module');
                                const moduleCheckboxes = document.querySelectorAll(
                                    '.permission-checkbox[data-module="' + module + '"]'
                                );

                                const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                                const someChecked = Array.from(moduleCheckboxes).some(cb => cb.checked);

                                checkbox.checked = allChecked;
                                checkbox.indeterminate = someChecked && !allChecked;
                            });
                        });
                    </script>
                    <!--begin::Notifications-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_notifications" aria-expanded="true"
                            aria-controls="kt_account_notifications">
                            <div class="card-title m-0">
                                <h3 class="fw-bold m-0">Notifications</h3>
                            </div>
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_notifications" class="collapse show">
                            <!--begin::Form-->
                            <form class="form">
                                <!--begin::Card body-->
                                <div class="card-body border-top px-9 pt-3 pb-4">
                                    <!--begin::Table-->
                                    <div class="table-responsive">
                                        <table class="table table-row-dashed border-gray-300 align-middle gy-6">
                                            <tbody class="fs-6 fw-semibold">
                                                <!--begin::Table row-->
                                                <tr>
                                                    <td class="min-w-250px fs-4 fw-bold">Notifications</td>
                                                    <td class="w-125px">
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="kt_settings_notification_email"
                                                                checked="checked" data-kt-check="true"
                                                                data-kt-check-target="[data-kt-settings-notification=email]" />
                                                            <label class="form-check-label ps-2"
                                                                for="kt_settings_notification_email">Email</label>
                                                        </div>
                                                    </td>
                                                    <td class="w-125px">
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="kt_settings_notification_phone"
                                                                checked="checked" data-kt-check="true"
                                                                data-kt-check-target="[data-kt-settings-notification=phone]" />
                                                            <label class="form-check-label ps-2"
                                                                for="kt_settings_notification_phone">Phone</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!--begin::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <td>Billing Updates</td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="1" id="billing1" checked="checked"
                                                                data-kt-settings-notification="email" />
                                                            <label class="form-check-label ps-2"
                                                                for="billing1"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="billing2" checked="checked"
                                                                data-kt-settings-notification="phone" />
                                                            <label class="form-check-label ps-2"
                                                                for="billing2"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!--begin::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <td>New Team Members</td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="team1" checked="checked"
                                                                data-kt-settings-notification="email" />
                                                            <label class="form-check-label ps-2"
                                                                for="team1"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="team2"
                                                                data-kt-settings-notification="phone" />
                                                            <label class="form-check-label ps-2"
                                                                for="team2"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!--begin::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <td>Completed Projects</td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="project1"
                                                                data-kt-settings-notification="email" />
                                                            <label class="form-check-label ps-2"
                                                                for="project1"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="project2" checked="checked"
                                                                data-kt-settings-notification="phone" />
                                                            <label class="form-check-label ps-2"
                                                                for="project2"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!--begin::Table row-->
                                                <!--begin::Table row-->
                                                <tr>
                                                    <td class="border-bottom-0">Newsletters</td>
                                                    <td class="border-bottom-0">
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="newsletter1"
                                                                data-kt-settings-notification="email" />
                                                            <label class="form-check-label ps-2"
                                                                for="newsletter1"></label>
                                                        </div>
                                                    </td>
                                                    <td class="border-bottom-0">
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="" id="newsletter2"
                                                                data-kt-settings-notification="phone" />
                                                            <label class="form-check-label ps-2"
                                                                for="newsletter2"></label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!--begin::Table row-->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end::Table-->
                                </div>
                                <!--end::Card body-->
                                <!--begin::Card footer-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button class="btn btn-primary px-6">Salvar</button>
                                </div>
                                <!--end::Card footer-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Notifications-->
                    <!--begin::Account Status-->
                    <div class="card">
                        <!--begin::Card header-->
                        <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                            data-bs-target="#kt_account_deactivate" aria-expanded="true"
                            aria-controls="kt_account_deactivate">
                            <div class="card-title m-0">
                                {{-- Título muda de acordo com o status --}}
                                <h3 class="fw-bold m-0">{{ $user->active ? 'Desativar Conta' : 'Ativar Conta' }}</h3>
                            </div>
                        </div>
                        <!--end::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_deactivate" class="collapse show">
                            <!--begin::Form-->
                            <form id="kt_account_deactivate_form" class="form" method="POST"
                                action="{{ route('users.status.update', $user->id) }}">
                                @csrf
                                @method('put')
                                <!--begin::Card body-->
                                <div class="card-body border-top p-9">
                                    <!--begin::Notice-->
                                    {{-- O aviso muda de cor e texto de acordo com o status --}}
                                    @if ($user->active)
                                        <div
                                            class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                        rx="10" fill="currentColor" />
                                                    <rect x="11" y="14" width="7" height="2" rx="1"
                                                        transform="rotate(-90 11 14)" fill="currentColor" />
                                                    <rect x="11" y="17" width="2" height="2" rx="1"
                                                        transform="rotate(-90 11 17)" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Você está prestes a desativar
                                                        esta conta</h4>
                                                    <div class="fs-6 text-gray-700">Um usuário desativado não poderá
                                                        fazer login no sistema.</div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="notice d-flex bg-light-success rounded border-success border border-dashed mb-9 p-6">
                                            <span class="svg-icon svg-icon-2tx svg-icon-success me-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                        rx="10" fill="currentColor" />
                                                    <path
                                                        d="M10.4343 12.4343L8.75 10.75C8.33579 10.3358 7.66421 10.3358 7.25 10.75C6.83579 11.1642 6.83579 11.8358 7.25 12.25L10.2929 15.2929C10.6834 15.6834 11.3166 15.6834 11.7071 15.2929L17.25 9.75C17.6642 9.33579 17.6642 8.66421 17.25 8.25C16.8358 7.83579 16.1642 7.83579 15.75 8.25L11.5657 12.4343C11.2533 12.7467 10.7467 12.7467 10.4343 12.4343Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <div class="d-flex flex-stack flex-grow-1">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Você está prestes a reativar esta
                                                        conta</h4>
                                                    <div class="fs-6 text-gray-700">Um usuário ativado poderá fazer
                                                        login no sistema novamente.</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <!--end::Notice-->

                                    <!--begin::Form input row-->
                                    <div class="form-check form-check-solid fv-row">
                                        <input name="confirm_action" class="form-check-input" type="checkbox"
                                            value="1" id="confirm_action" required />
                                        <label class="form-check-label fw-semibold ps-2 fs-6" for="confirm_action">
                                            Eu confirmo esta ação
                                        </label>
                                    </div>
                                    <!--end::Form input row-->
                                </div>
                                <!--end::Card body-->

                                <!--begin::Card footer-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    {{-- O botão muda de cor e texto de acordo com o status --}}
                                    @if ($user->active)
                                        <button type="submit" class="btn btn-danger fw-semibold">Desativar
                                            Conta</button>
                                    @else
                                        <button type="submit" class="btn btn-success fw-semibold">Reativar
                                            Conta</button>
                                    @endif
                                </div>
                                <!--end::Card footer-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Account Status-->

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end:::Main-->
    </div>
</x-tenant-app-layout>

<script src="/assets/js/custom/account/settings/signin-methods.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos do formulário
        const form = document.getElementById('kt_signin_change_email');
        const emailInput = document.getElementById('emailaddress');
        const passwordInput = document.getElementById('confirmemailpassword');
        const submitBtn = document.getElementById('kt_signin_submit');
        const cancelBtn = document.getElementById('kt_signin_cancel');

        // Elementos de exibição
        const emailDisplay = document.getElementById('kt_signin_email');
        const emailEdit = document.getElementById('kt_signin_email_edit');
        const emailButton = document.getElementById('kt_signin_email_button');

        // Elementos de erro
        const emailError = document.getElementById('email-error');
        const passwordError = document.getElementById('password-error');

        // Estado do botão
        let isSubmitting = false;

        // Função para mostrar/ocultar formulário
        function toggleEmailForm(show) {
            if (show) {
                emailDisplay.classList.add('d-none');
                emailEdit.classList.remove('d-none');
                emailButton.classList.add('d-none');
                emailInput.focus();
            } else {
                emailDisplay.classList.remove('d-none');
                emailEdit.classList.add('d-none');
                emailButton.classList.remove('d-none');
                clearForm();
            }
        }

        // Função para limpar formulário
        function clearForm() {
            form.reset();
            clearErrors();
            setButtonState(false);
        }

        // Função para limpar erros
        function clearErrors() {
            emailError.textContent = '';
            passwordError.textContent = '';
            emailInput.classList.remove('is-invalid');
            passwordInput.classList.remove('is-invalid');
        }

        // Função para mostrar erro
        function showError(field, message) {
            const errorElement = field === 'email' ? emailError : passwordError;
            const inputElement = field === 'email' ? emailInput : passwordInput;

            errorElement.textContent = message;
            inputElement.classList.add('is-invalid');
        }

        // Função para definir estado do botão
        function setButtonState(submitting) {
            isSubmitting = submitting;
            submitBtn.disabled = submitting;

            if (submitting) {
                submitBtn.setAttribute('data-kt-indicator', 'on');
            } else {
                submitBtn.removeAttribute('data-kt-indicator');
            }
        }

        // Event listener para o botão de editar
        emailButton.addEventListener('click', function() {
            toggleEmailForm(true);
        });

        // Event listener para o botão de cancelar
        cancelBtn.addEventListener('click', function() {
            toggleEmailForm(false);
        });

        // Event listener para o botão de submit
        submitBtn.addEventListener('click', function() {
            if (isSubmitting) return;

            clearErrors();

            // Validação básica
            const email = emailInput.value.trim();
            const password = passwordInput.value.trim();

            if (!email) {
                showError('email', 'O email é obrigatório.');
                return;
            }

            if (!password) {
                showError('password', 'A senha é obrigatória.');
                return;
            }

            if (email === '{{ $user->email }}') {
                showError('email', 'O novo email deve ser diferente do email atual.');
                return;
            }

            // Enviar requisição
            setButtonState(true);

            fetch('{{ route('users.email.update', $user->id) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Atualizar o email exibido
                        const emailText = emailDisplay.querySelector('.fw-semibold.text-gray-600');
                        emailText.textContent = data.new_email;

                        // Mostrar mensagem de sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: data.message,
                            confirmButtonText: 'OK'
                        });

                        // Fechar formulário
                        toggleEmailForm(false);
                    } else {
                        // Mostrar erros
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                showError(field, data.errors[field][0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: data.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um erro inesperado. Tente novamente.',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    setButtonState(false);
                });
        });

        // Validação em tempo real do email
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && email !== '{{ $user->email }}') {
                // Verificar se o email é válido
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showError('email', 'Digite um email válido.');
                } else {
                    clearErrors();
                }
            }
        });
    });

    // Script para redefinição de senha
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos do formulário de senha
        const passwordForm = document.getElementById('kt_signin_change_password');
        const passwordInput = document.getElementById('newpassword');
        const confirmPasswordInput = document.getElementById('confirmpassword');
        const submitPasswordBtn = document.getElementById('kt_password_submit');
        const cancelPasswordBtn = document.getElementById('kt_password_cancel');

        // Elementos de exibição
        const passwordDisplay = document.getElementById('kt_signin_password');
        const passwordEdit = document.getElementById('kt_signin_password_edit');
        const passwordButton = document.getElementById('kt_signin_password_button');

        // Elementos de erro
        const passwordError = document.getElementById('password-error');
        const confirmPasswordError = document.getElementById('password_confirmation-error');

        // Estado do botão
        let isSubmittingPassword = false;

        // Função para mostrar/ocultar formulário de senha
        function togglePasswordForm(show) {
            if (show) {
                passwordDisplay.classList.add('d-none');
                passwordEdit.classList.remove('d-none');
                passwordButton.classList.add('d-none');
                passwordInput.focus();
            } else {
                passwordDisplay.classList.remove('d-none');
                passwordEdit.classList.add('d-none');
                passwordButton.classList.remove('d-none');
                clearPasswordForm();
            }
        }

        // Função para limpar formulário de senha
        function clearPasswordForm() {
            passwordForm.reset();
            clearPasswordErrors();
            setPasswordButtonState(false);
        }

        // Função para limpar erros de senha
        function clearPasswordErrors() {
            passwordError.textContent = '';
            if (confirmPasswordError) {
                confirmPasswordError.textContent = '';
            }
            passwordInput.classList.remove('is-invalid');
            if (confirmPasswordInput) {
                confirmPasswordInput.classList.remove('is-invalid');
            }
        }

        // Função para mostrar erro de senha
        function showPasswordError(field, message) {
            const errorElement = field === 'password' ? passwordError : (confirmPasswordError || null);
            const inputElement = field === 'password' ? passwordInput : (confirmPasswordInput || null);

            if (errorElement) {
                errorElement.textContent = message;
            }
            if (inputElement) {
                inputElement.classList.add('is-invalid');
            }
        }

        // Função para definir estado do botão de senha
        function setPasswordButtonState(submitting) {
            isSubmittingPassword = submitting;
            submitPasswordBtn.disabled = submitting;

            if (submitting) {
                submitPasswordBtn.setAttribute('data-kt-indicator', 'on');
            } else {
                submitPasswordBtn.removeAttribute('data-kt-indicator');
            }
        }

        // Função para validar senha (mínimo 8 caracteres)
        function validatePassword(password) {
            return {
                isValid: password.length >= 8,
                length: password.length
            };
        }

        // Elementos do checkbox de senha automática
        const automaticPasswordCheckbox = document.getElementById('automatic_password');
        const manualPasswordContainer = document.getElementById('manual_password_container');

        // Função para mostrar/ocultar campo de senha manual
        function toggleManualPasswordField() {
            if (automaticPasswordCheckbox.checked) {
                manualPasswordContainer.style.display = 'none';
                passwordInput.removeAttribute('required');
                if (confirmPasswordInput) {
                    confirmPasswordInput.removeAttribute('required');
                }
            } else {
                manualPasswordContainer.style.display = 'block';
                passwordInput.setAttribute('required', 'required');
                if (confirmPasswordInput) {
                    confirmPasswordInput.setAttribute('required', 'required');
                }
            }
        }

        // Event listener para o checkbox de senha automática
        automaticPasswordCheckbox.addEventListener('change', function() {
            toggleManualPasswordField();
            clearPasswordErrors();
        });

        // Inicializar estado do campo de senha manual
        toggleManualPasswordField();

        // Event listener para o botão de redefinir senha
        passwordButton.addEventListener('click', function() {
            togglePasswordForm(true);
        });

        // Event listener para o botão de cancelar senha
        cancelPasswordBtn.addEventListener('click', function() {
            togglePasswordForm(false);
        });

        // Event listener para o botão de submit de senha
        submitPasswordBtn.addEventListener('click', function() {
            if (isSubmittingPassword) return;

            clearPasswordErrors();

            const automaticPassword = automaticPasswordCheckbox.checked;
            const requireChange = document.getElementById('require_change').checked;
            const password = passwordInput.value.trim();
            const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value.trim() : '';

            // Validação para senha manual
            if (!automaticPassword) {
                if (!password) {
                    showPasswordError('password', 'A senha é obrigatória.');
                    return;
                }

                if (!confirmPassword) {
                    showPasswordError('password_confirmation', 'A confirmação da senha é obrigatória.');
                    return;
                }

                if (password !== confirmPassword) {
                    showPasswordError('password_confirmation', 'As senhas não conferem.');
                    return;
                }

                // Validar tamanho mínimo
                const passwordValidation = validatePassword(password);
                if (!passwordValidation.isValid) {
                    showPasswordError('password', 'A senha deve ter pelo menos 8 caracteres.');
                    return;
                }
            }

            // Enviar requisição
            setPasswordButtonState(true);

            const requestBody = {
                automatic_password: automaticPassword,
                require_change: requireChange
            };

            if (!automaticPassword) {
                requestBody.password = password;
                requestBody.password_confirmation = confirmPassword;
            }

            fetch('{{ route('users.password.reset', $user->id) }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(requestBody)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Se senha foi gerada automaticamente, mostrar modal
                        if (automaticPassword && data.generated_password) {
                            document.getElementById('generated_password_display').value = data.generated_password;
                            const modal = new bootstrap.Modal(document.getElementById('kt_modal_generated_password'));
                            modal.show();

                            // Botão de copiar senha
                            document.getElementById('copy_password_btn').addEventListener('click', function() {
                                const passwordField = document.getElementById('generated_password_display');
                                passwordField.select();
                                document.execCommand('copy');

                                const btn = this;
                                const originalHtml = btn.innerHTML;
                                btn.innerHTML = '<i class="ki-duotone ki-check fs-2"><span class="path1"></span><span class="path2"></span></i> Copiado!';
                                btn.classList.remove('btn-primary');
                                btn.classList.add('btn-success');

                                setTimeout(() => {
                                    btn.innerHTML = originalHtml;
                                    btn.classList.remove('btn-success');
                                    btn.classList.add('btn-primary');
                                }, 2000);
                            });
                        } else {
                            // Mostrar mensagem de sucesso
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: data.message,
                                confirmButtonText: 'OK'
                            });
                        }

                        // Fechar formulário
                        togglePasswordForm(false);
                    } else {
                        // Mostrar erros
                        if (data.errors) {
                            Object.keys(data.errors).forEach(field => {
                                showPasswordError(field, data.errors[field][0]);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: data.message,
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Ocorreu um erro inesperado. Tente novamente.',
                        confirmButtonText: 'OK'
                    });
                })
                .finally(() => {
                    setPasswordButtonState(false);
                });
        });

        // Validação em tempo real da senha
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            if (password.length > 0) {
                const validation = validatePassword(password);
                if (!validation.isValid) {
                    showPasswordError('password', 'A senha deve ter pelo menos 8 caracteres.');
                } else {
                    clearPasswordErrors();
                }
            }
        });

        // Validação em tempo real da confirmação
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (confirmPassword.length > 0 && password !== confirmPassword) {
                showPasswordError('password_confirmation', 'As senhas não conferem.');
            } else if (confirmPassword.length > 0 && password === confirmPassword) {
                clearPasswordErrors();
            }
        });
    });
</script>

<!--begin::Modal para exibir senha gerada-->
<div class="modal fade" id="kt_modal_generated_password" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">Senha Gerada com Sucesso</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                    <i class="ki-duotone ki-shield-tick fs-2hx text-primary me-4"></i>
                    <div class="d-flex flex-column">
                        <h4 class="mb-1 text-dark">Importante!</h4>
                        <span>Copie esta senha agora. Você não poderá vê-la novamente.</span>
                    </div>
                </div>
                <div class="mb-10">
                    <label class="form-label fw-bold mb-3">Nova Senha:</label>
                    <div class="input-group input-group-solid">
                        <input type="text" class="form-control form-control-lg" id="generated_password_display" readonly />
                        <button class="btn btn-primary" type="button" id="copy_password_btn">
                           <i class="fa-solid fa-copy fs-2">
                            </i>
                            Copiar
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal para exibir senha gerada-->
