<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-labelledby="kt_modal_add_user_header" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-850px">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header text-center" id="kt_modal_add_user_header">
                <h2 class="fw-bold text-dark">Adicionar Usuário</h2>
                <button class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close"
                    aria-label="Fechar modal">
                    <i class="bi bi-x fs-2"></i>
                </button>
            </div>
            <div class="modal-body scroll-y mx-xl-10 ">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data"
                    id="kt_modal_add_user_form">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <div id="kt_modal_add_user_errors" class="alert alert-danger d-none"></div>
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">
                        <!-- Avatar -->
                        <div class="fv-row mb-8 text-center">
                            <label class="d-block fw-semibold fs-6 mb-3">Avatar</label>
                            <div class="image-input image-input-outline image-input-placeholder"
                                data-kt-image-input="true">
                                <div class="image-input-wrapper w-125px h-125px rounded-circle"
                                    style="background-image: url(/assets/media/avatars/300-31.png);"></div>
                                <label
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Alterar avatar">
                                    <i class="bi bi-pencil-fill fs-7"></i>
                                    <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="photo" />
                                </label>
                                <span
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                    title="Cancelar avatar">
                                    <i class="bi bi-x fs-2"></i>
                                </span>
                                <span
                                    class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remover avatar">
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
                            <input type="text" name="name" class="form-control form-control-solid"
                                placeholder="Ex: Frei Abelardo José" value="" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="fv-row mb-8">
                            <label class="required fw-semibold fs-6 mb-2">Email</label>
                            <input type="email" name="email" class="form-control form-control-solid"
                                placeholder="Ex: frei@gmail.com" value="" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Senha e Confirmação de Senha (Lado a Lado) -->
                        <div class="row mb-8">
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Senha</label>
                                <input type="password" name="password" id="user_password"
                                    class="form-control form-control-solid" autocomplete="new-password"
                                    placeholder="**********" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fw-semibold fs-6 mb-2">Repita a Senha</label>
                                <input type="password" name="password_confirmation" id="user_password_confirmation"
                                    class="form-control form-control-solid" autocomplete="new-password"
                                    placeholder="**********" required />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Primeiro Acesso / Troca de Senha Obrigatória -->
                        <div class="fv-row mb-8">
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="must_change_password"
                                    id="must_change_password" value="1" />
                                <label class="form-check-label fw-semibold text-dark" for="must_change_password">
                                    Usuário deve trocar a senha no primeiro acesso
                                </label>
                            </div>
                            <div class="form-text text-muted">Se marcado, o usuário será obrigado a definir uma nova
                                senha ao fazer o primeiro login.</div>
                            <x-input-error :messages="$errors->get('must_change_password')" class="mt-2" />
                        </div>

                        <!-- Filiais -->
                        <div class="fv-row mb-8">
                            <label class="fw-semibold fs-6 mb-2" for="filiais">Outros Organismos com Acesso</label>
                            <select id="filiais" name="filiais[]" class="form-select form-select-solid"
                                data-control="select2" data-placeholder="Selecione organismos adicionais..."
                                multiple="multiple" data-dropdown-parent="#kt_modal_add_user">
                                <option></option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('filiais')" class="mt-2" />
                        </div>

                        <div class="separator separator-dashed my-6"></div>

                        <!-- Permissões por Módulo -->
                        @php
                            $permissionService = new \App\Services\PermissionService();
                            $actionNames = $permissionService->getActionNames();
                            $moduleIcons = [
                                'financeiro' => '/assets/media/png/financeiro.svg',
                                'patrimonio' => '/assets/media/png/house3d.png',
                                'contabilidade' => '/assets/media/png/contabilidade.png',
                                'fieis' => '/assets/media/png/fieis.png',
                                'cemiterio' => '/assets/media/png/lapide2.png',
                                'company' => '/assets/media/svg/files/folder-document.svg',
                                'users' => '/assets/media/svg/files/folder-document.svg',
                            ];
                        @endphp

                        <div class="mb-8">
                            <label class="required fw-semibold fs-6">Permissões por Módulo</label>

                            @if (isset($permissionsByModule) && !empty($permissionsByModule))
                                <!--begin::Payment method-->
                                <div class="card card-flush mb-5 mb-lg-10" data-kt-subscriptions-form="pricing">
                                    <!--begin::Card body-->
                                    <div class="card-body pt-0">
                                        <!--begin::Options-->
                                        <div id="kt_create_new_payment_method">
                                            @foreach ($permissionsByModule as $module => $permissions)
                                                @php
                                                    $moduleName = $moduleNames[$module] ?? ucfirst($module);
                                                    $moduleIcon = $moduleIcons[$module] ?? '/assets/media/svg/card-logos/mastercard.svg';
                                                    $isFirst = $loop->first;
                                                    $collapseId = "kt_module_permissions_{$module}";
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
                                                                    @php
                                                                        $checkedCount = 0;
                                                                        $totalCount = count($permissions);
                                                                    @endphp
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
                                                                        <div class="col-md-6 col-lg-4 mb-3">
                                                                            <div class="form-check form-check-custom form-check-solid p-3 border border-gray-300 rounded">
                                                                                <input class="form-check-input permission-checkbox"
                                                                                    type="checkbox"
                                                                                    name="permissions[]"
                                                                                    value="{{ $permission->id }}"
                                                                                    data-module="{{ $module }}"
                                                                                    id="permission_{{ $permission->id }}" />
                                                                                <label class="form-check-label w-100" for="permission_{{ $permission->id }}">
                                                                                    <div class="d-flex align-items-center">
                                                                                        <span class="fw-semibold text-dark me-2">{{ $actionName }}</span>
                                                                                        <span class="badge {{ $badgeClass }} fs-8">{{ $action }}</span>
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
                    </div>

                    <!-- Ações -->
                    <div class="text-center pt-10">
                        <button type="reset" class="btn btn-light me-3"
                            data-kt-users-modal-action="cancel">Cancelar</button>
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
