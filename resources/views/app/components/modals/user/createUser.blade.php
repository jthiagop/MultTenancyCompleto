<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-labelledby="kt_modal_add_user_header" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header border-0 pb-3" id="kt_modal_add_user_header">
                <h2 class="fw-bold text-dark">Adicionar Usuário</h2>
                <button class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close" aria-label="Fechar modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </button>
            </div>
            <div class="modal-body scroll-y mx-5 mx-xl-10 my-7">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" id="kt_modal_add_user_form">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <div id="kt_modal_add_user_errors" class="alert alert-danger d-none"></div>
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header" data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <!-- Avatar -->
                        <div class="fv-row mb-8">
                            <label class="d-block fw-semibold fs-6 mb-3">Avatar</label>
                            <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true">
                                <div class="image-input-wrapper w-125px h-125px rounded-circle" style="background-image: url(/assets/media/avatars/300-31.png);"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar avatar">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="photo" />
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancelar avatar">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remover avatar">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                            </div>
                            <div class="form-text text-muted">Arquivos permitidos: png, jpg, jpeg.</div>
                        </div>

                        <!-- Exibir erros gerais -->
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger mt-2">{{ $error }}</div>
                        @endforeach

                        <!-- Nome -->
                        <div class="fv-row mb-8">
                            <label class="required fw-semibold fs-6 mb-2">Nome</label>
                            <input type="text" name="name" class="form-control form-control-solid" placeholder="Ex: Frei Abelardo José" value="" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="fv-row mb-8">
                            <label class="required fw-semibold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid" placeholder="Ex: frei@gmail.com" value="" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Senha e Confirmação de Senha (Lado a Lado) -->
                        <div class="row mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Senha</label>
                                <input type="password" name="password" id="user_password" class="form-control form-control-solid" autocomplete="new-password" placeholder="**********" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Repita a Senha</label>
                                <input type="password" name="password_confirmation" id="user_password_confirmation" class="form-control form-control-solid" autocomplete="new-password" placeholder="**********" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Primeiro Acesso / Troca de Senha Obrigatória -->
                        <div class="fv-row mb-8">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="must_change_password" id="must_change_password" value="1" />
                                <label class="form-check-label fw-semibold text-dark" for="must_change_password">
                                    Usuário deve trocar a senha no primeiro acesso
                                </label>
                            </div>
                            <div class="form-text text-muted">Se marcado, o usuário será obrigado a definir uma nova senha ao fazer o primeiro login.</div>
                            <x-input-error :messages="$errors->get('must_change_password')" class="mt-2" />
                        </div>

                        <!-- Filiais -->
                        <div class="fv-row mb-8">
                            <label class="fw-semibold fs-6 mb-2" for="filiais">Outros Organismos com Acesso</label>
                            <select id="filiais" name="filiais[]" class="form-select form-select-solid" data-control="select2" data-placeholder="Selecione organismos adicionais..." multiple="multiple" data-dropdown-parent="#kt_modal_add_user">
                                <option></option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('filiais')" class="mt-2" />
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <!-- Permissões (Roles) -->
                        @php
                            // Supondo que você passe uma variável $roles do seu controller
                            // Exemplo: $roles = \Spatie\Permission\Models\Role::all();
                            // Se os nomes estão fixos, você pode criar um array associativo.
                            $rolesExemplo = [
                                1 => [
                                    'name' => 'Global',
                                    'description' => 'Acesso total para desenvolvedores.',
                                ],
                                2 => [
                                    'name' => 'Administrador',
                                    'description' => 'Acesso total aos dados da empresa.',
                                ],
                                3 => [
                                    'name' => 'Admin User',
                                    'description' => 'Gerencia usuários e filiais.',
                                ],
                                4 => [
                                    'name' => 'Usuário Comum',
                                    'description' => 'Trata dados da organização.',
                                ],
                                5 => [
                                    'name' => 'Sub Usuário',
                                    'description' => 'Apenas visualização de dados.',
                                ],
                            ];
                        @endphp

                        <div class="mb-8">
                            <label class="required fw-semibold fs-6 mb-3">Permissões (Roles)</label>
                            @foreach ($rolesExemplo as $id => $role)
                                <div class="d-flex fv-row mb-5">
                                    <div class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input me-3" name="roles[]" type="checkbox" value="{{ $id }}" id="role_option_{{ $id }}" />
                                        <label class="form-check-label" for="role_option_{{ $id }}">
                                            <div class="fw-bold text-dark">{{ $role['name'] }}</div>
                                            <div class="text-muted">{{ $role['description'] }}</div>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3" data-kt-users-modal-action="cancel">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="kt_modal_add_user_submit">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
