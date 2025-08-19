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
                                <h3 class="fw-bold m-0">Profile Details</h3>
                            </div>
                            <!--end::Card title-->
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_profile_details" class="collapse show">
                            <!--begin::Form-->
                            <form method="POST" action="{{ route('users.update', $user->id) }}">
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
                                                    <input type="text" name="fname"
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
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
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
                                <h3 class="fw-bold m-0">Sign-in Method</h3>
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
                                        <div class="fs-6 fw-bold mb-1">Email Address</div>
                                        <div class="fw-semibold text-gray-600">support@keenthemes.com</div>
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Edit-->
                                    <div id="kt_signin_email_edit" class="flex-row-fluid d-none">
                                        <!--begin::Form-->
                                        <form id="kt_signin_change_email" class="form" novalidate="novalidate">
                                            <div class="row mb-6">
                                                <div class="col-lg-6 mb-4 mb-lg-0">
                                                    <div class="fv-row mb-0">
                                                        <label for="emailaddress"
                                                            class="form-label fs-6 fw-bold mb-3">Enter New Email
                                                            Address</label>
                                                        <input type="email"
                                                            class="form-control form-control-lg form-control-solid"
                                                            id="emailaddress" placeholder="Email Address"
                                                            name="emailaddress" value="support@keenthemes.com" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="fv-row mb-0">
                                                        <label for="confirmemailpassword"
                                                            class="form-label fs-6 fw-bold mb-3">Confirm
                                                            Password</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="confirmemailpassword" id="confirmemailpassword" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <button id="kt_signin_submit" type="button"
                                                    class="btn btn-primary me-2 px-6">Update Email</button>
                                                <button id="kt_signin_cancel" type="button"
                                                    class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Edit-->
                                    <!--begin::Action-->
                                    <div id="kt_signin_email_button" class="ms-auto">
                                        <button class="btn btn-light btn-active-light-primary">Change Email</button>
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
                                        <div class="fs-6 fw-bold mb-1">Password</div>
                                        <div class="fw-semibold text-gray-600">************</div>
                                    </div>
                                    <!--end::Label-->
                                    <!--begin::Edit-->
                                    <div id="kt_signin_password_edit" class="flex-row-fluid d-none">
                                        <!--begin::Form-->
                                        <form id="kt_signin_change_password" class="form" novalidate="novalidate">
                                            <div class="row mb-1">
                                                <div class="col-lg-4">
                                                    <div class="fv-row mb-0">
                                                        <label for="currentpassword"
                                                            class="form-label fs-6 fw-bold mb-3">Current
                                                            Password</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="currentpassword" id="currentpassword" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="fv-row mb-0">
                                                        <label for="newpassword"
                                                            class="form-label fs-6 fw-bold mb-3">New Password</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="newpassword" id="newpassword" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="fv-row mb-0">
                                                        <label for="confirmpassword"
                                                            class="form-label fs-6 fw-bold mb-3">Confirm New
                                                            Password</label>
                                                        <input type="password"
                                                            class="form-control form-control-lg form-control-solid"
                                                            name="confirmpassword" id="confirmpassword" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-text mb-5">Password must be at least 8 character and
                                                contain symbols</div>
                                            <div class="d-flex">
                                                <button id="kt_password_submit" type="button"
                                                    class="btn btn-primary me-2 px-6">Update Password</button>
                                                <button id="kt_password_cancel" type="button"
                                                    class="btn btn-color-gray-400 btn-active-light-primary px-6">Cancel</button>
                                            </div>
                                        </form>
                                        <!--end::Form-->
                                    </div>
                                    <!--end::Edit-->
                                    <!--begin::Action-->
                                    <div id="kt_signin_password_button" class="ms-auto">
                                        <button class="btn btn-light btn-active-light-primary">Reset Password</button>
                                    </div>
                                    <!--end::Action-->
                                </div>
                                <!--end::Password-->
                                <!--begin::Notice-->
                                <div
                                    class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                    <!--begin::Icon-->
                                    <!--begin::Svg Icon | path: icons/duotune/general/gen048.svg-->
                                    <span class="svg-icon svg-icon-2tx svg-icon-primary me-4">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path opacity="0.3"
                                                d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z"
                                                fill="currentColor" />
                                            <path
                                                d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z"
                                                fill="currentColor" />
                                        </svg>
                                    </span>
                                    <!--end::Svg Icon-->
                                    <!--end::Icon-->
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                        <!--begin::Content-->
                                        <div class="mb-3 mb-md-0 fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">Secure Your Account</h4>
                                            <div class="fs-6 text-gray-700 pe-7">Two-factor authentication adds an
                                                extra layer of security to your account. To log in, in addition you'll
                                                need to provide a 6 digit code</div>
                                        </div>
                                        <!--end::Content-->
                                        <!--begin::Action-->
                                        <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap"
                                            data-bs-toggle="modal"
                                            data-bs-target="#kt_modal_two_factor_authentication">Enable</a>
                                        <!--end::Action-->
                                    </div>
                                    <!--end::Wrapper-->
                                </div>
                                <!--end::Notice-->
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
                                                        <img src="{{ route('file', ['path' => $company->avatar]) }}"
                                                            class="w-35px me-6 " alt="{{ $company->name }}" />
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
                                <h3 class="fw-bold m-0">Permissões</h3>
                            </div>
                        </div>
                        <!--begin::Card header-->
                        <!--begin::Content-->
                        <div id="kt_account_settings_email_preferences" class="collapse show">
                            <!--begin::Form-->
                            <form method="POST" action="{{ route('users.roles.update', $user->id) }}"
                                enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <!--begin::Card body-->
                                <div class="card-body border-top px-9 py-9">

                                    {{-- Pega todos os IDs das roles do usuário e coloca em um array. --}}
                                    @php
                                        $userRoleIds = $user->roles->pluck('id')->toArray();
                                    @endphp

                                    <!--begin::Input row - Global -->
                                    <div class="d-flex fv-row">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                                value="1" id="kt_modal_update_role_option_0"
                                                {{-- Verifica se o ID 1 está no array de roles do usuário --}}
                                                @if (in_array(1, $userRoleIds)) checked @endif />
                                            <label class="form-check-label" for="kt_modal_update_role_option_0">
                                                <div class="fw-bold text-gray-800">Global</div>
                                                <div class="text-gray-600">Melhor para desenvolvedores...</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class='separator separator-dashed my-5'></div>

                                    <!--begin::Input row - Administrador -->
                                    <div class="d-flex fv-row">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                                value="2" id="kt_modal_update_role_option_1"
                                                {{-- Verifica se o ID 2 está no array de roles do usuário --}}
                                                @if (in_array(2, $userRoleIds)) checked @endif />
                                            <label class="form-check-label" for="kt_modal_update_role_option_1">
                                                <div class="fw-bold text-gray-800">Administrador</div>
                                                <div class="text-gray-600">Ideal para pessoas que precisam de acesso
                                                    total...</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class='separator separator-dashed my-5'></div>

                                    <!--begin::Input row - Admin User -->
                                    <div class="d-flex fv-row">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                                value="3" id="kt_modal_update_role_option_2"
                                                {{-- Verifica se o ID 3 está no array de roles do usuário --}}
                                                @if (in_array(3, $userRoleIds)) checked @endif />
                                            <label class="form-check-label" for="kt_modal_update_role_option_2">
                                                <div class="fw-bold text-gray-800">Admin User</div>
                                                <div class="text-gray-600">Ideal para funcionários que gerenciam as
                                                    filiais</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class='separator separator-dashed my-5'></div>

                                    <!--begin::Input row - Usuários Comuns -->
                                    <div class="d-flex fv-row">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                                value="4" id="kt_modal_update_role_option_3"
                                                {{-- Verifica se o ID 4 está no array de roles do usuário --}}
                                                @if (in_array(4, $userRoleIds)) checked @endif />
                                            <label class="form-check-label" for="kt_modal_update_role_option_3">
                                                <div class="fw-bold text-gray-800">Usuários Comuns</div>
                                                <div class="text-gray-600">Para usuários que tratam dos dados da
                                                    organização</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class='separator separator-dashed my-5'></div>

                                    <!--begin::Input row - Sub Usuário -->
                                    <div class="d-flex fv-row">
                                        <div class="form-check form-check-custom form-check-solid">
                                            <input class="form-check-input me-3" name="roles[]" type="checkbox"
                                                value="5" id="kt_modal_update_role_option_4"
                                                {{-- Verifica se o ID 5 está no array de roles do usuário --}}
                                                @if (in_array(5, $userRoleIds)) checked @endif />
                                            <label class="form-check-label" for="kt_modal_update_role_option_4">
                                                <div class="fw-bold text-gray-800">Sub Usuário</div>
                                                <div class="text-gray-600">Ideal para pessoas que precisam visualizar
                                                    dados...</div>
                                            </label>
                                        </div>
                                    </div>
                                    <!--end::Input row-->
                                </div>
                                <!--end::Card body-->
                                <!--begin::Card footer-->
                                <div class="card-footer d-flex justify-content-end py-6 px-9">
                                    <button type="submit" class="btn btn-primary px-6">
                                        <i class="fas fa-sync-alt me-2"></i> Atualizar Permissões
                                    </button>
                                </div>
                                <!--end::Card footer-->
                            </form>
                            <!--end::Form-->
                        </div>
                        <!--end::Content-->

                    </div>
                    <!--end::Notifications-->
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
                                    <button class="btn btn-light btn-active-light-primary me-2">Discard</button>
                                    <button class="btn btn-primary px-6">Save Changes</button>
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
