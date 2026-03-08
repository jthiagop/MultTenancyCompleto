<div class="modal fade" id="kt_modal_add_user" tabindex="-1" aria-labelledby="kt_modal_add_user_header" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-1000px">
        <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header text-center" id="kt_modal_add_user_header">
                <h2 class="fw-bold text-dark">Adicionar Usuário</h2>
                <button class="btn btn-icon btn-sm btn-active-icon-primary" data-kt-users-modal-action="close"
                    aria-label="Fechar modal">
                    <i class="bi bi-x fs-2"></i>
                </button>
            </div>
            <div class="modal-body ">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data"
                    id="kt_modal_add_user_form">
                    @csrf
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <div id="kt_modal_add_user_errors" class="alert alert-danger d-none"></div>
                    <div class="d-flex flex-column scroll-y me-n7 pe-7" id="kt_modal_add_user_scroll"
                        data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}"
                        data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_modal_add_user_header"
                        data-kt-scroll-wrappers="#kt_modal_add_user_scroll" data-kt-scroll-offset="300px">

                        <!-- Avatar e Dados Principais -->
                        <div class="row mb-8">
                            <!-- Coluna do Avatar -->
                            <div class="col-md-3">
                                <div class="fv-row text-center">
                                    <label class="d-block fw-semibold fs-6 mb-3">Avatar</label>
                                    <div class="image-input image-input-outline image-input-placeholder"
                                        data-kt-image-input="true">
                                        <div class="image-input-wrapper w-125px h-125px rounded-circle mx-auto"
                                            style="background-image: url(/tenancy/assets/media/avatars/blank.png);"></div>
                                        <label
                                            class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                            title="Alterar avatar">
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
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                            title="Remover avatar">
                                            <i class="bi bi-x fs-2"></i>
                                        </span>
                                    </div>
                                    <div class="form-text text-muted mt-2">permitidos: png, jpg, jpeg.</div>
                                </div>
                            </div>

                            <!-- Coluna dos Dados Principais -->
                            <div class="col-md-9">
                                <!-- Linha 1: Nome e Email -->
                                <div class="row mb-6">
                                    <div class="col-md-6 fv-row">
                                        <label class="fw-semibold fs-6 mb-2">Nome <span class="text-danger">*</span></label>
                                        <input type="text" name="name" id="user_name" class="form-control"
                                            placeholder="Ex: Frei Abelardo José" value=""  />
                                        <div class="invalid-feedback" id="user_name_feedback"></div>
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="fw-semibold fs-6 mb-2">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="user_email" class="form-control"
                                            placeholder="Ex: frei@gmail.com" value=""  />
                                        <div class="invalid-feedback" id="user_email_feedback"></div>
                                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Linha 2: Senha e Confirmação de Senha -->
                                <div class="row mb-6">
                                    <div class="col-md-6 fv-row">
                                        <label class="fw-semibold fs-6 mb-2">Senha <span class="text-danger" id="user_password_required">*</span></label>
                                        <input type="password" name="password" id="user_password" class="form-control"
                                            autocomplete="new-password" placeholder="**********"  />
                                        <div class="invalid-feedback" id="user_password_feedback"></div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                    <div class="col-md-6 fv-row">
                                        <label class="fw-semibold fs-6 mb-2">Repita a Senha <span class="text-danger" id="user_password_conf_required">*</span></label>
                                        <input type="password" name="password_confirmation"
                                            id="user_password_confirmation" class="form-control"
                                            autocomplete="new-password" placeholder="**********"  />
                                        <div class="invalid-feedback" id="user_password_confirmation_feedback"></div>
                                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Primeiro Acesso / Troca de Senha Obrigatória -->
                                <div class="fv-row">
                                    <div class="form-check form-check-custom">
                                        <input class="form-check-input" type="checkbox" name="must_change_password"
                                            id="must_change_password" value="1" />
                                        <label class="form-check-label fw-semibold text-dark"
                                            for="must_change_password">
                                            Usuário deve trocar a senha no primeiro acesso
                                        </label>
                                    </div>

                                    <x-input-error :messages="$errors->get('must_change_password')" class="mt-2" />
                                </div>
                            </div>
                            <div class="separator separator-dashed pt-4 "></div>
                        </div>
                    </div>
                    <!--begin::Accordion-->
                    <div class="accordion my-6" id="kt_accordion_organismos">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="kt_accordion_organismos_header">
                                <button class="accordion-button fs-4 fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#kt_accordion_organismos_body"
                                    aria-expanded="true" aria-controls="kt_accordion_organismos_body">
                                    Organismos com Acesso
                                </button>
                            </h2>
                            <div id="kt_accordion_organismos_body" class="accordion-collapse collapse show"
                                aria-labelledby="kt_accordion_organismos_header" data-bs-parent="#kt_accordion_organismos">
                                <div class="accordion-body">
                                    <!-- Filiais -->
                                    <div class="fv-row mb-8">
                                        <label class="fw-semibold fs-6 mb-4">Organismos com Acesso</label>

                                        <!--begin::Switch group-->
                                        <div class="row g-3" data-kt-buttons="true">
                                            @foreach ($companies as $company)
                                                <div class="col-md-6">
                                                    <!--begin::Switch button-->
                                                    <label
                                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-4 h-100">
                                                        <div class="d-flex align-items-center me-2">
                                                            <!--begin::Switch-->
                                                            <div
                                                                class="form-check form-switch form-check-custom form-check-solid me-4">
                                                                <input class="form-check-input" type="checkbox"
                                                                    name="filiais[]" value="{{ $company->id }}" />
                                                            </div>
                                                            <!--end::Switch-->

                                                            <!--begin::Avatar-->
                                                            <div class="symbol symbol-45px me-3">
                                                                @if ($company->avatar)
                                                                    <img src="{{ route('file', ['path' => $company->avatar]) }}"
                                                                        class="rounded-circle"
                                                                        alt="{{ $company->name }}" />
                                                                @else
                                                                    <div
                                                                        class="symbol-label fs-4 bg-light-primary text-primary rounded-circle">
                                                                        {{ strtoupper(substr($company->name, 0, 2)) }}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <!--end::Avatar-->

                                                            <!--begin::Info-->
                                                            <div class="flex-grow-1">
                                                                <div
                                                                    class="d-flex align-items-center fs-6 fw-bold text-dark mb-1">
                                                                    {{ $company->name }}
                                                                </div>
                                                                <div class="fw-semibold text-muted fs-7">
                                                                    @if ($company->addresses && $company->addresses->first())
                                                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                                                        {{ $company->addresses->first()->city ?? 'Cidade não informada' }}
                                                                        @if ($company->addresses->first()->state)
                                                                            -
                                                                            {{ $company->addresses->first()->state }}
                                                                        @endif
                                                                    @else
                                                                        <i class="bi bi-building me-1"></i>
                                                                        Organismo
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <!--end::Info-->
                                                        </div>
                                                    </label>
                                                    <!--end::Switch button-->
                                                </div>
                                            @endforeach
                                        </div>
                                        <!--end::Radio group-->

                                        <x-input-error :messages="$errors->get('filiais')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Accordion-->


                    <!-- Permissões por Módulo -->
                    @php
                        $permissionService = new \App\Services\PermissionService();
                        $actionNames = $permissionService->getActionNames();
                        // $moduleIcons já vem do controller com os dados do banco
                        // Se não existir, usar fallback padrão
                        $defaultIcon = asset('tenancy/assets/media/avatars/blank.png');
                    @endphp

                    <!--begin::Accordion-->
                    <div class="accordion" id="kt_accordion_permissoes">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="kt_accordion_permissoes_header">
                                <button class="accordion-button fs-4 fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#kt_accordion_permissoes_body"
                                    aria-expanded="true" aria-controls="kt_accordion_permissoes_body">
                                    Permissões por módulo
                                </button>
                            </h2>
                            <div id="kt_accordion_permissoes_body" class="accordion-collapse collapse show"
                                aria-labelledby="kt_accordion_permissoes_header" data-bs-parent="#kt_accordion_permissoes">
                                <div class="accordion-body">
                                    <!-- Filiais -->

                                    <!--begin::Switch group-->
                                    <div class="row" data-kt-buttons="true">
                                        @if (isset($permissionsByModule) && !empty($permissionsByModule))
                                            <!--begin::Payment method-->
                                            <div class="card card-flush mb-5 mb-lg-10"
                                                data-kt-subscriptions-form="pricing">
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
                                                                        <div
                                                                            class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-3">
                                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen036.svg-->
                                                                            <span
                                                                                class="svg-icon toggle-on svg-icon-primary svg-icon-2">
                                                                                <svg width="24" height="24"
                                                                                    viewBox="0 0 24 24" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <rect opacity="0.3" x="2" y="2"
                                                                                        width="20" height="20"
                                                                                        rx="5"
                                                                                        fill="currentColor" />
                                                                                    <rect x="6.0104" y="10.9247"
                                                                                        width="12" height="2"
                                                                                        rx="1"
                                                                                        fill="currentColor" />
                                                                                </svg>
                                                                            </span>
                                                                            <!--end::Svg Icon-->
                                                                            <!--begin::Svg Icon | path: icons/duotune/general/gen035.svg-->
                                                                            <span
                                                                                class="svg-icon toggle-off svg-icon-2">
                                                                                <svg width="24" height="24"
                                                                                    viewBox="0 0 24 24" fill="none"
                                                                                    xmlns="http://www.w3.org/2000/svg">
                                                                                    <rect opacity="0.3" x="2" y="2"
                                                                                        width="20" height="20"
                                                                                        rx="5"
                                                                                        fill="currentColor" />
                                                                                    <rect x="10.8891" y="17.8033"
                                                                                        width="12" height="2"
                                                                                        rx="1"
                                                                                        transform="rotate(-90 10.8891 17.8033)"
                                                                                        fill="currentColor" />
                                                                                    <rect x="6.01041" y="10.9247"
                                                                                        width="12" height="2"
                                                                                        rx="1"
                                                                                        fill="currentColor" />
                                                                                </svg>
                                                                            </span>
                                                                            <!--end::Svg Icon-->
                                                                        </div>
                                                                        <!--end::Arrow-->
                                                                        <!--begin::Logo-->
                                                                        <img src="{{ $moduleIcon }}"
                                                                            class="w-40px me-3"
                                                                            alt="{{ $moduleName }}" />
                                                                        <!--end::Logo-->
                                                                        <!--begin::Summary-->
                                                                        <div class="me-3 flex-grow-1">
                                                                            <div
                                                                                class="d-flex align-items-center fw-bold">
                                                                                {{ $moduleName }}
                                                                                @php
                                                                                    $checkedCount = 0;
                                                                                    $totalCount = count($permissions);
                                                                                @endphp
                                                                                @if ($checkedCount > 0 && $checkedCount < $totalCount)
                                                                                    <div class="badge badge-light-warning ms-5">
                                                                                        {{ $checkedCount }}/{{ $totalCount }}
                                                                                    </div>
                                                                                @elseif ($checkedCount == $totalCount)
                                                                                    <div class="badge badge-light-success ms-5">
                                                                                        Completo
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                            <div class="text-muted">
                                                                                {{ $totalCount }} permissões
                                                                                disponíveis</div>
                                                                        </div>
                                                                        <!--end::Summary-->
                                                                    </div>
                                                                    <!--end::Toggle-->
                                                                    <!--begin::Input-->
                                                                    <div class="d-flex my-3 ms-9">
                                                                        <!--begin::Checkbox Select All-->
                                                                        <label
                                                                            class="form-check form-check-custom form-check-solid me-5">
                                                                            <input
                                                                                class="form-check-input module-select-all"
                                                                                type="checkbox"
                                                                                data-module="{{ $module }}"
                                                                                id="module_select_all_{{ $module }}" />
                                                                            <span
                                                                                class="form-check-label fw-semibold">Selecionar
                                                                                Tudo</span>
                                                                        </label>
                                                                        <!--end::Checkbox Select All-->
                                                                    </div>
                                                                    <!--end::Input-->
                                                                </div>
                                                                <!--end::Header-->
                                                                <!--begin::Body-->
                                                                <div id="{{ $collapseId }}"
                                                                    class="collapse {{ $isFirst ? 'show' : '' }} fs-6 ps-10">
                                                                    <!--begin::Details-->
                                                                    <div class="d-flex flex-wrap py-5">
                                                                        <!--begin::Col-->
                                                                        <div class="flex-equal w-100">
                                                                            <div class="row">
                                                                                @foreach ($permissions as $permission)
                                                                                    @php
                                                                                        $parts = explode(
                                                                                            '.',
                                                                                            $permission->name,
                                                                                        );
                                                                                        $action = end($parts);
                                                                                        $actionName =
                                                                                            $actionNames[$action] ??
                                                                                            ucfirst($action);

                                                                                        // Determinar badge color baseado na ação
                                                                                        $badgeClass =
                                                                                            'badge-light-primary';
                                                                                        if ($action == 'delete') {
                                                                                            $badgeClass =
                                                                                                'badge-light-danger';
                                                                                        } elseif (
                                                                                            $action == 'create' ||
                                                                                            $action == 'store'
                                                                                        ) {
                                                                                            $badgeClass =
                                                                                                'badge-light-success';
                                                                                        } elseif (
                                                                                            $action == 'edit' ||
                                                                                            $action == 'update'
                                                                                        ) {
                                                                                            $badgeClass =
                                                                                                'badge-light-warning';
                                                                                        } elseif (
                                                                                            $action == 'index' ||
                                                                                            $action == 'show'
                                                                                        ) {
                                                                                            $badgeClass =
                                                                                                'badge-light-info';
                                                                                        }
                                                                                    @endphp
                                                                                    <div
                                                                                        class="col-md-6 col-lg-4 mb-3">
                                                                                        <div
                                                                                            class="form-check form-check-custom form-check-solid p-3 border border-gray-300 rounded">
                                                                                            <input
                                                                                                class="form-check-input permission-checkbox"
                                                                                                type="checkbox"
                                                                                                name="permissions[]"
                                                                                                value="{{ $permission->id }}"
                                                                                                data-module="{{ $module }}"
                                                                                                id="permission_{{ $permission->id }}" />
                                                                                            <label
                                                                                                class="form-check-label w-100"
                                                                                                for="permission_{{ $permission->id }}">
                                                                                                <div
                                                                                                    class="d-flex align-items-center">
                                                                                                    <span
                                                                                                        class="fw-semibold text-dark me-2">{{ $actionName }}</span>
                                                                                                    <span
                                                                                                        class="badge {{ $badgeClass }} fs-8">{{ $action }}</span>
                                                                                                </div>
                                                                                                @if (count($parts) > 2)
                                                                                                    <div
                                                                                                        class="text-muted fs-7 mt-1">
                                                                                                        {{ $permission->name }}
                                                                                                    </div>
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
                                    </div>
                                    <!--end::Radio group-->
                                    <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

            </div>

            <!-- Ações -->
            <div class="modal-footer text-center">
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

            @push('scripts')
            <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Evitar duplicação de listeners
                            if (window.__userModalPermissionListenersInitialized) return;
                            window.__userModalPermissionListenersInitialized = true;

                            const form = document.getElementById('kt_modal_add_user_form');
                            const submitButton = document.getElementById('kt_modal_add_user_submit');
                            const modal = document.getElementById('kt_modal_add_user');
                            const errorsContainer = document.getElementById('kt_modal_add_user_errors');
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                            // Referências dos campos
                            const nameInput = document.getElementById('user_name');
                            const emailInput = document.getElementById('user_email');
                            const passwordInput = document.getElementById('user_password');
                            const passwordConfInput = document.getElementById('user_password_confirmation');
                            const userIdInput = document.getElementById('user_id');

                            // Estado de validação
                            let emailDuplicado = false;
                            let emailCheckTimer = null;

                            // ============================================================
                            // FUNÇÕES AUXILIARES DE VALIDAÇÃO INLINE
                            // ============================================================
                            function setFieldValid(input, feedbackId) {
                                input.classList.remove('is-invalid');
                                input.classList.add('is-valid');
                                const fb = document.getElementById(feedbackId);
                                if (fb) { fb.textContent = ''; fb.style.display = 'none'; }
                            }

                            function setFieldInvalid(input, feedbackId, message) {
                                input.classList.remove('is-valid');
                                input.classList.add('is-invalid');
                                const fb = document.getElementById(feedbackId);
                                if (fb) { fb.textContent = message; fb.style.display = 'block'; }
                            }

                            function clearField(input, feedbackId) {
                                input.classList.remove('is-valid', 'is-invalid');
                                const fb = document.getElementById(feedbackId);
                                if (fb) { fb.textContent = ''; fb.style.display = 'none'; }
                            }

                            function isEditMode() {
                                return userIdInput && userIdInput.value && userIdInput.value.trim() !== '';
                            }

                            // ============================================================
                            // VALIDAÇÃO DO NOME (blur)
                            // ============================================================
                            nameInput.addEventListener('blur', function() {
                                const val = this.value.trim();
                                if (!val) {
                                    setFieldInvalid(this, 'user_name_feedback', 'O nome é obrigatório.');
                                } else if (val.length < 3) {
                                    setFieldInvalid(this, 'user_name_feedback', 'O nome deve ter no mínimo 3 caracteres.');
                                } else {
                                    setFieldValid(this, 'user_name_feedback');
                                }
                            });

                            nameInput.addEventListener('input', function() {
                                if (this.classList.contains('is-invalid') && this.value.trim().length >= 3) {
                                    setFieldValid(this, 'user_name_feedback');
                                }
                            });

                            // ============================================================
                            // VALIDAÇÃO DO EMAIL (blur + AJAX duplicidade)
                            // ============================================================
                            emailInput.addEventListener('blur', function() {
                                const val = this.value.trim();
                                if (!val) {
                                    setFieldInvalid(this, 'user_email_feedback', 'O email é obrigatório.');
                                    return;
                                }

                                // Validar formato
                                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                if (!emailRegex.test(val)) {
                                    setFieldInvalid(this, 'user_email_feedback', 'Informe um email válido.');
                                    return;
                                }

                                // Verificar duplicidade via AJAX
                                clearTimeout(emailCheckTimer);
                                emailCheckTimer = setTimeout(() => {
                                    const bodyData = { email: val };
                                    if (isEditMode()) {
                                        bodyData.user_id = userIdInput.value;
                                    }

                                    fetch('{{ route("users.checkEmail") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrfToken,
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify(bodyData)
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (data.exists) {
                                            emailDuplicado = true;
                                            setFieldInvalid(emailInput, 'user_email_feedback', data.message || 'Este email já está em uso.');
                                        } else {
                                            emailDuplicado = false;
                                            setFieldValid(emailInput, 'user_email_feedback');
                                        }
                                    })
                                    .catch(() => {
                                        // Não bloquear se a verificação falhar
                                        emailDuplicado = false;
                                        setFieldValid(emailInput, 'user_email_feedback');
                                    });
                                }, 400);
                            });

                            emailInput.addEventListener('input', function() {
                                if (this.classList.contains('is-invalid')) {
                                    clearField(this, 'user_email_feedback');
                                    emailDuplicado = false;
                                }
                            });

                            // ============================================================
                            // VALIDAÇÃO DA SENHA (blur)
                            // ============================================================
                            passwordInput.addEventListener('blur', function() {
                                const val = this.value;

                                // Senha obrigatória apenas em criação
                                if (!isEditMode() && !val) {
                                    setFieldInvalid(this, 'user_password_feedback', 'A senha é obrigatória.');
                                    return;
                                }

                                if (val && val.length < 8) {
                                    setFieldInvalid(this, 'user_password_feedback', 'A senha deve ter no mínimo 8 caracteres.');
                                    return;
                                }

                                if (val) {
                                    setFieldValid(this, 'user_password_feedback');
                                } else {
                                    clearField(this, 'user_password_feedback');
                                }

                                // Revalidar confirmação se já estiver preenchida
                                if (passwordConfInput.value) {
                                    passwordConfInput.dispatchEvent(new Event('blur'));
                                }
                            });

                            passwordInput.addEventListener('input', function() {
                                if (this.classList.contains('is-invalid') && this.value.length >= 8) {
                                    setFieldValid(this, 'user_password_feedback');
                                }
                            });

                            // ============================================================
                            // VALIDAÇÃO DA CONFIRMAÇÃO DE SENHA (blur)
                            // ============================================================
                            passwordConfInput.addEventListener('blur', function() {
                                const passVal = passwordInput.value;
                                const confVal = this.value;

                                if (!isEditMode() && !confVal && passVal) {
                                    setFieldInvalid(this, 'user_password_confirmation_feedback', 'Confirme a senha.');
                                    return;
                                }

                                if (confVal && confVal !== passVal) {
                                    setFieldInvalid(this, 'user_password_confirmation_feedback', 'As senhas não conferem.');
                                    return;
                                }

                                if (confVal && confVal === passVal) {
                                    setFieldValid(this, 'user_password_confirmation_feedback');
                                } else {
                                    clearField(this, 'user_password_confirmation_feedback');
                                }
                            });

                            passwordConfInput.addEventListener('input', function() {
                                if (this.classList.contains('is-invalid') && this.value === passwordInput.value) {
                                    setFieldValid(this, 'user_password_confirmation_feedback');
                                }
                            });

                            // ============================================================
                            // VALIDAÇÃO NO SUBMIT (antes de enviar)
                            // ============================================================
                            function validateBeforeSubmit() {
                                let valid = true;

                                // Nome
                                if (!nameInput.value.trim()) {
                                    setFieldInvalid(nameInput, 'user_name_feedback', 'O nome é obrigatório.');
                                    valid = false;
                                } else if (nameInput.value.trim().length < 3) {
                                    setFieldInvalid(nameInput, 'user_name_feedback', 'O nome deve ter no mínimo 3 caracteres.');
                                    valid = false;
                                }

                                // Email
                                if (!emailInput.value.trim()) {
                                    setFieldInvalid(emailInput, 'user_email_feedback', 'O email é obrigatório.');
                                    valid = false;
                                } else if (emailDuplicado) {
                                    setFieldInvalid(emailInput, 'user_email_feedback', 'Este email já está em uso.');
                                    valid = false;
                                }

                                // Senha (obrigatória apenas em criação)
                                if (!isEditMode()) {
                                    if (!passwordInput.value) {
                                        setFieldInvalid(passwordInput, 'user_password_feedback', 'A senha é obrigatória.');
                                        valid = false;
                                    } else if (passwordInput.value.length < 8) {
                                        setFieldInvalid(passwordInput, 'user_password_feedback', 'A senha deve ter no mínimo 8 caracteres.');
                                        valid = false;
                                    }

                                    if (!passwordConfInput.value) {
                                        setFieldInvalid(passwordConfInput, 'user_password_confirmation_feedback', 'Confirme a senha.');
                                        valid = false;
                                    } else if (passwordConfInput.value !== passwordInput.value) {
                                        setFieldInvalid(passwordConfInput, 'user_password_confirmation_feedback', 'As senhas não conferem.');
                                        valid = false;
                                    }
                                } else {
                                    // Em edição, validar apenas se senha foi preenchida
                                    if (passwordInput.value && passwordInput.value.length < 8) {
                                        setFieldInvalid(passwordInput, 'user_password_feedback', 'A senha deve ter no mínimo 8 caracteres.');
                                        valid = false;
                                    }
                                    if (passwordInput.value && passwordConfInput.value !== passwordInput.value) {
                                        setFieldInvalid(passwordConfInput, 'user_password_confirmation_feedback', 'As senhas não conferem.');
                                        valid = false;
                                    }
                                }

                                return valid;
                            }

                            // ============================================================
                            // SUBMIT DO FORMULÁRIO VIA AJAX
                            // ============================================================
                            form.addEventListener('submit', async function(e) {
                                e.preventDefault();

                                // Validação frontend antes de enviar
                                if (!validateBeforeSubmit()) {
                                    return;
                                }

                                // Mostrar loading
                                submitButton.setAttribute('data-kt-indicator', 'on');
                                submitButton.disabled = true;

                                // Limpar erros anteriores do servidor
                                errorsContainer.classList.add('d-none');
                                errorsContainer.innerHTML = '';
                                document.querySelectorAll('.server-feedback').forEach(el => el.remove());

                                try {
                                    // Preparar FormData
                                    const formData = new FormData(form);
                                    
                                    // Adicionar permissões selecionadas
                                    const permissions = [];
                                    document.querySelectorAll('.permission-checkbox:checked').forEach(checkbox => {
                                        permissions.push(checkbox.value);
                                    });
                                    
                                    // Remover permissões antigas e adicionar as novas
                                    formData.delete('permissions[]');
                                    permissions.forEach(permissionId => {
                                        formData.append('permissions[]', permissionId);
                                    });

                                    // Adicionar filiais selecionadas
                                    const filiais = [];
                                    document.querySelectorAll('input[name="filiais[]"]:checked').forEach(checkbox => {
                                        filiais.push(checkbox.value);
                                    });
                                    
                                    // Remover filiais antigas e adicionar as novas
                                    formData.delete('filiais[]');
                                    filiais.forEach(filiaisId => {
                                        formData.append('filiais[]', filiaisId);
                                    });

                                    const response = await fetch(form.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        }
                                    });

                                    const result = await response.json();

                                    if (response.ok && result.success) {
                                        // Sucesso — limpar validação visual
                                        form.querySelectorAll('.is-valid, .is-invalid').forEach(el => {
                                            el.classList.remove('is-valid', 'is-invalid');
                                        });

                                        // Fechar modal
                                        const modalInstance = bootstrap.Modal.getInstance(modal);
                                        modalInstance.hide();
                                        
                                        // Resetar formulário
                                        form.reset();
                                        
                                        // Recarregar página ou atualizar tabela se necessário
                                        if (typeof window.usersDatatable !== 'undefined' && window.usersDatatable.ajax) {
                                            window.usersDatatable.ajax.reload();
                                        } else {
                                            window.location.reload();
                                        }
                                    } else {
                                        // Erro de validação do servidor
                                        if (result.errors) {
                                            Object.keys(result.errors).forEach(field => {
                                                const fieldErrors = result.errors[field];
                                                const fieldElement = form.querySelector(`[name="${field}"], [name="${field}[]"]`);
                                                
                                                if (fieldElement) {
                                                    fieldElement.classList.remove('is-valid');
                                                    fieldElement.classList.add('is-invalid');
                                                    
                                                    // Adicionar mensagem de erro próxima ao campo
                                                    fieldErrors.forEach(error => {
                                                        const errorDiv = document.createElement('div');
                                                        errorDiv.className = 'invalid-feedback d-block server-feedback';
                                                        errorDiv.textContent = error;
                                                        fieldElement.parentNode.appendChild(errorDiv);
                                                    });
                                                }
                                            });

                                            // Exibir resumo dos erros no topo
                                            const allErrors = Object.values(result.errors).flat();
                                            errorsContainer.innerHTML = '<strong>Corrija os erros abaixo:</strong><ul class="mb-0 mt-1">' + allErrors.map(e => '<li>' + e + '</li>').join('') + '</ul>';
                                            errorsContainer.classList.remove('d-none');
                                        } else if (result.message) {
                                            errorsContainer.innerHTML = '<strong>Erro:</strong> ' + result.message;
                                            errorsContainer.classList.remove('d-none');
                                        }
                                    }
                                } catch (error) {
                                    console.error('Erro ao enviar formulário:', error);
                                    errorsContainer.innerHTML = '<strong>Erro de conexão:</strong> Não foi possível processar a solicitação. Verifique sua conexão e tente novamente.';
                                    errorsContainer.classList.remove('d-none');
                                } finally {
                                    // Remover loading
                                    submitButton.setAttribute('data-kt-indicator', 'off');
                                    submitButton.disabled = false;
                                }
                            });

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
            @endpush
        </div>
    </div>
</div>
