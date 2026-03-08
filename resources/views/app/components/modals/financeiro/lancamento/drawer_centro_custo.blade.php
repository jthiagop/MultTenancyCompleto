<!--begin::Drawer - Novo Centro de Custo-->
<style>
    #kt_drawer_centro_custo {
        z-index: 1070 !important;
    }

    #kt_drawer_centro_custo .drawer-overlay {
        z-index: 1065 !important;
    }

    /* Ensure inputs in drawer are clickable */
    #kt_drawer_centro_custo input,
    #kt_drawer_centro_custo button,
    #kt_drawer_centro_custo select,
    #kt_drawer_centro_custo textarea {
        pointer-events: auto !important;
    }
</style>
<div class="rounded-4" id="kt_drawer_centro_custo" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_centro_custo_button" data-kt-drawer-close="#kt_drawer_centro_custo_close"
    data-kt-drawer-width="500px" tabindex="0">

    <div class="card shadow-sm w-100">
        <!--begin::Header-->
        <div class="card-header pe-5">
            <!--begin::Title-->
            <div class="card-title">
                <h3 class="fw-bold m-0" id="centro_custo_drawer_title">Novo Centro de Custo</h3>
            </div>
            <!--end::Title-->

            <!--begin::Toolbar-->
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                    id="kt_drawer_centro_custo_close">
                    <i class="bi bi-x fs-2"></i>
                </button>
            </div>
            <!--end::Toolbar-->
        </div>
        <!--end::Header-->

        <div class="card-body drawer-body ">
            <form id="kt_drawer_centro_custo_form">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">

                <!--begin::Card de campos-->
                <div class="card card-flush border border-gray-300">
                    <div class="card-body p-6">
                        <!--begin::Descrição-->
                        <div class="mb-8 text-center">
                            <div class="text-muted fw-semibold fs-6">
                                Cadastro rápido de Centro de Custo. Preencha o código e nome para criar.
                            </div>
                        </div>
                        <!--end::Descrição-->
                        <!--begin::Input group - Código-->
                        <div class="fv-row mb-6">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Código</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Código de Identificação do Centro de Custo"></i>
                            </label>
                            <input type="number" class="form-control" placeholder="Informe o código" name="code"
                                id="centro_custo_code" />
                            <div class="invalid-feedback" id="centro_custo_code_error"></div>
                        </div>
                        <!--end::Input group - Código-->

                        <!--begin::Input group - Nome-->
                        <div class="fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span class="required">Nome</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Nome do centro de custo (ex: Marketing, TI, Administrativo)"></i>
                            </label>
                            <input type="text" class="form-control" placeholder="Informe o nome do Centro de Custo"
                                name="name" id="centro_custo_name" />
                            <div class="invalid-feedback" id="centro_custo_name_error"></div>
                        </div>
                        <!--end::Input group - Nome-->
                    </div>
                </div>
                <!--end::Card de campos-->
            </form>
        </div>
        <!--end::Body-->

        <!--begin::Actions-->
        <div class="card-footer">
            <button type="button" class="btn btn-sm btn-light" id="kt_drawer_centro_custo_cancel">
                Cancelar
            </button>
            <button type="submit" class="btn btn-sm btn-primary" id="kt_drawer_centro_custo_submit"
                form="kt_drawer_centro_custo_form">
                <span class="indicator-label">
                    Salvar
                </span>
                <span class="indicator-progress">
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    Aguarde...
                </span>
            </button>
        </div>
        <!--end::Actions-->
    </div>
</div>
<!--end::Drawer - Novo Centro de Custo-->

@push('scripts')
    <script>
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                // Form Submission Logic
                const form = document.querySelector('#kt_drawer_centro_custo_form');
                const submitButton = document.querySelector('#kt_drawer_centro_custo_submit');
                const cancelButton = document.querySelector('#kt_drawer_centro_custo_cancel');
                const codeInput = document.getElementById('centro_custo_code');
                const codeError = document.getElementById('centro_custo_code_error');
                const nomeInput = document.getElementById('centro_custo_name');
                const nomeError = document.getElementById('centro_custo_name_error');

                let codeDuplicado = false;
                let checkCodeTimeout = null;

                // Função auxiliar para limpar validação de um campo
                function clearFieldError(input, errorDiv) {
                    if (input) input.classList.remove('is-invalid', 'is-valid');
                    if (errorDiv) {
                        errorDiv.textContent = '';
                        errorDiv.classList.remove('d-block');
                    }
                }

                // Função auxiliar para marcar campo com erro
                function setFieldError(input, errorDiv, message) {
                    if (input) input.classList.add('is-invalid');
                    if (errorDiv) {
                        errorDiv.textContent = message;
                        errorDiv.classList.add('d-block');
                    }
                }

                // Verificação de duplicidade do código via AJAX
                if (codeInput) {
                    codeInput.addEventListener('input', function() {
                        clearFieldError(codeInput, codeError);
                        codeDuplicado = false;
                    });

                    codeInput.addEventListener('blur', function() {
                        const valor = this.value.trim();
                        if (!valor) return;

                        if (checkCodeTimeout) clearTimeout(checkCodeTimeout);

                        checkCodeTimeout = setTimeout(function() {
                            fetch('{{ route('costCenter.checkCode') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content')
                                    },
                                    body: JSON.stringify({
                                        code: valor
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    clearFieldError(codeInput, codeError);
                                    if (data.exists) {
                                        codeDuplicado = true;
                                        setFieldError(codeInput, codeError, data.message ||
                                            'Este código já está em uso.');
                                    } else {
                                        codeDuplicado = false;
                                        codeInput.classList.add('is-valid');
                                    }
                                })
                                .catch(function() {
                                    // silently fail
                                });
                        }, 400);
                    });
                }

                // Limpa validação do nome ao digitar
                if (nomeInput) {
                    nomeInput.addEventListener('input', function() {
                        clearFieldError(nomeInput, nomeError);
                    });
                }

                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        // Limpa validações anteriores
                        clearFieldError(codeInput, codeError);
                        clearFieldError(nomeInput, nomeError);

                        let hasError = false;

                        // Validação do código (obrigatório)
                        if (!codeInput || !codeInput.value.trim()) {
                            setFieldError(codeInput, codeError, 'O código é obrigatório.');
                            hasError = true;
                        } else if (codeDuplicado) {
                            setFieldError(codeInput, codeError, 'Este código já está em uso.');
                            hasError = true;
                        }

                        // Validação do nome (obrigatório)
                        if (!nomeInput || !nomeInput.value.trim()) {
                            setFieldError(nomeInput, nomeError, 'O nome é obrigatório.');
                            hasError = true;
                        }

                        if (hasError) {
                            // Foca no primeiro campo com erro
                            const firstInvalid = form.querySelector('.is-invalid');
                            if (firstInvalid) firstInvalid.focus();
                            return;
                        }

                        // Prepare Data
                        const formData = new FormData(form);
                        const data = Object.fromEntries(formData.entries());

                        // Button State
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        fetch('{{ route('costCenter.storeAjax') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')
                                        .getAttribute('content')
                                },
                                body: JSON.stringify(data)
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result.success) {
                                    toastr.success(result.message ||
                                        'Centro de custo criado com sucesso!');

                                    // Atualizar Select2 do Centro de Custo no formulário de lançamento
                                    const novoId = result.data?.id;
                                    const novoCode = result.data?.code;
                                    const novoNome = result.data?.name;

                                    if (novoId && novoNome && typeof $ !== 'undefined') {
                                        const $target = $('#cost_center_id');

                                        if ($target.length) {
                                            // Remove option existente se houver (evita duplicação)
                                            $target.find('option[value="' + novoId + '"]').remove();

                                            // Cria texto da option
                                            var optionText = novoCode ? novoCode + ' - ' +
                                                novoNome : novoNome;

                                            // Cria nova option já selecionada
                                            const opt = new Option(optionText, novoId, true, true);

                                            // Adiciona e dispara change para atualizar o Select2
                                            $target.append(opt).trigger('change');
                                        }
                                    }

                                    // Fecha o Drawer
                                    const drawerElement = document.getElementById(
                                        'kt_drawer_centro_custo');
                                    if (drawerElement) {
                                        const drawer = KTDrawer.getInstance(drawerElement);
                                        if (drawer) {
                                            drawer.hide();
                                        } else {
                                            if (typeof KTDrawer.getOrCreateInstance ===
                                                'function') {
                                                const inst = KTDrawer.getOrCreateInstance(
                                                    drawerElement);
                                                if (inst) inst.hide();
                                            } else {
                                                const closeBtn = document.querySelector(
                                                    '#kt_drawer_centro_custo_close');
                                                if (closeBtn) closeBtn.click();
                                            }
                                        }
                                    }

                                    // Reset form
                                    form.reset();
                                    clearFieldError(codeInput, codeError);
                                    clearFieldError(nomeInput, nomeError);
                                    codeDuplicado = false;

                                    // Emite evento para outros listeners
                                    document.dispatchEvent(new CustomEvent('centro-custo-created', {
                                        detail: {
                                            id: novoId,
                                            code: novoCode,
                                            name: novoNome
                                        }
                                    }));

                                } else {
                                    toastr.error(result.message ||
                                        'Erro ao salvar centro de custo.');
                                    if (result.errors) {
                                        // Exibe erros inline nos campos
                                        if (result.errors.code) {
                                            setFieldError(codeInput, codeError, result.errors.code[
                                                0]);
                                        }
                                        if (result.errors.name) {
                                            setFieldError(nomeInput, nomeError, result.errors.name[
                                                0]);
                                        }
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Erro na requisição:', error);
                                toastr.error('Ocorreu um erro inesperado.');
                            })
                            .finally(() => {
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                            });
                    });
                }

                if (cancelButton) {
                    cancelButton.addEventListener('click', function() {
                        const closeBtn = document.querySelector('#kt_drawer_centro_custo_close');
                        if (closeBtn) closeBtn.click();
                    });
                }
            });
        })();
    </script>
@endpush
