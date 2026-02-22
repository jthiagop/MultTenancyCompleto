<!--begin::Modal - Cadastrar/Editar Conta Contábil-->
<div class="modal fade" id="kt_modal_new_account" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header ">
                <!--begin::Heading-->
                <div>
                    <h2 class="fw-bolder mb-1" id="modal-title">Cadastrar Nova Conta Contábil</h2>
                    <div class="text-muted fw-semibold fs-6" id="modal-subtitle">Adicione uma nova conta ao seu plano de
                        contas.</div>
                </div>
                <!--end::Heading-->
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->

            <!--begin:Form-->
            <form id="kt_modal_new_account_form" class="form" method="POST">
                @csrf
                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-15 pt-4 pb-10">

                    <!--begin::Seção 1 - Identificação-->
                    <div class="fw-bold fs-5 text-gray-800 mb-5">
                        <i class="bi bi-tag fs-5 me-2 text-primary"></i>Identificação
                    </div>

                    <!--begin::Nome da Conta-->
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="required fs-6 fw-semibold mb-2">Nome da Conta</label>
                        <input type="text" class="form-control " placeholder="Ex: Caixa Geral da Matriz"
                            name="name" value="{{ old('name') }}" />
                        <div class="fv-plugins-message-container invalid-feedback" id="name-error"></div>
                    </div>
                    <!--end::Nome da Conta-->

                    <!--begin::Código de Integração-->
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="fs-6 fw-semibold mb-2">
                            Código de Integração
                            <span class="ms-1" data-bs-toggle="tooltip" title="Código reduzido usado para integração com sistemas externos (Ex: Alterdata)">
                                <i class="bi bi-question-circle text-muted fs-7"></i>
                            </span>
                        </label>
                        <input type="text" class="form-control" placeholder="Ex: 1001"
                            name="external_code" id="external_code_input" value="{{ old('external_code') }}" />
                        <div class="form-text">
                            <i class="bi bi-link-45deg text-primary"></i> Código usado para exportação/importação com sistemas externos
                        </div>
                        <div class="fv-plugins-message-container invalid-feedback" id="external_code-error"></div>
                    </div>
                    <!--end::Código de Integração-->

                    <!--begin::Seção 2 - Hierarquia e Classificação-->
                    <div class="fw-bold fs-5 text-gray-800 mb-5">
                        <i class="bi bi-diagram-3 fs-5 me-2 text-primary"></i>Hierarquia e Classificação
                    </div>

                    <!--begin::Conta Pai-->
                    <div class="d-flex flex-column mb-7 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Conta Pai</label>
                        <select class="form-select" data-control="select2" data-dropdown-parent="#kt_modal_new_account"
                            data-placeholder="Selecione uma conta pai (opcional)" name="parent_id" id="parent_id_select"
                            data-allow-clear="true">
                            <option></option>
                            @isset($contas)
                                @foreach ($contas as $conta)
                                    <option value="{{ $conta->id }}" data-type="{{ $conta->type }}"
                                        {{ old('parent_id') == $conta->id ? 'selected' : '' }}>
                                        {{ $conta->code }} - {{ $conta->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="form-text">Deixe em branco para criar uma conta raiz (nível superior)</div>
                        <div class="fv-plugins-message-container invalid-feedback" id="parent_id-error"></div>

                        <!--begin::Separator-->
                        <div class="separator my-6"></div>
                        <!--begin::Código + Tipo (lado a lado)-->
                        <div class="row g-9 mb-7">
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Código da Conta</label>
                                <input type="text" id="account_code_mask" class="form-control "
                                    placeholder="Ex: 1.01.01.001" name="code" value="{{ old('code') }}" />
                                <div class="fv-plugins-message-container invalid-feedback" id="code-error"></div>
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                                <select class="form-select" data-control="select2" data-hide-search="true"
                                    data-dropdown-parent="#kt_modal_new_account" data-placeholder="Selecione o tipo"
                                    name="type" id="type_select">
                                    <option></option>
                                    <option value="ativo" {{ old('type') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="passivo" {{ old('type') == 'passivo' ? 'selected' : '' }}>Passivo
                                    </option>
                                    <option value="patrimonio_liquido"
                                        {{ old('type') == 'patrimonio_liquido' ? 'selected' : '' }}>Patrimônio Líquido
                                    </option>
                                    <option value="receita" {{ old('type') == 'receita' ? 'selected' : '' }}>Receita
                                    </option>
                                    <option value="despesa" {{ old('type') == 'despesa' ? 'selected' : '' }}>Despesa
                                    </option>
                                </select>
                                <div class="form-text" id="type-hint" style="display: none;">
                                    <i class="bi bi-info-circle text-info"></i> Tipo herdado da conta pai
                                </div>
                                <div class="fv-plugins-message-container invalid-feedback" id="type-error"></div>
                            </div>
                            <div class="form-text" id="code-hint">
                                <i class="bi bi-magic text-primary"></i> Sugerido automaticamente ao selecionar
                                Conta Pai
                            </div>
                        </div>
                        <!--end::Código + Tipo-->


                    </div>
                    <!--end::Conta Pai-->

                    <!--begin::Classificação da Conta-->
                    <div class="d-flex flex-column fv-row">
                        <label class="required fs-6 fw-semibold mb-3">Classificação da Conta</label>
                        <div class="row g-5">
                            <!--begin::Card Sintética-->
                            <div class="col-md-6">
                                <label
                                    class="d-flex align-items-start p-5 rounded border border-dashed border-gray-300 cursor-pointer card-classificacao"
                                    for="type_sintetica">
                                    <span class="form-check form-check-custom me-4 mt-1">
                                        <input class="form-check-input" type="radio" value="0"
                                            name="allows_posting" id="type_sintetica" checked />
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            <i class="bi bi-folder2 me-1 text-warning"></i> Sintética
                                        </span>
                                        <span class="text-muted fs-7 mt-1">Agrupa outras contas. Não recebe
                                            lançamentos.</span>
                                    </span>
                                </label>
                            </div>
                            <!--end::Card Sintética-->
                            <!--begin::Card Analítica-->
                            <div class="col-md-6">
                                <label
                                    class="d-flex align-items-start p-5 rounded border border-dashed border-gray-300 cursor-pointer card-classificacao"
                                    for="type_analitica">
                                    <span class="form-check form-check-custom me-4 mt-1">
                                        <input class="form-check-input" type="radio" value="1"
                                            name="allows_posting" id="type_analitica" />
                                    </span>
                                    <span class="d-flex flex-column">
                                        <span class="fw-bolder text-gray-800 fs-6">
                                            <i class="bi bi-journal-text me-1 text-success"></i> Analítica
                                        </span>
                                        <span class="text-muted fs-7 mt-1">Aceita lançamentos financeiros
                                            diretamente.</span>
                                    </span>
                                </label>
                            </div>
                            <!--end::Card Analítica-->
                        </div>
                        <div class="fv-plugins-message-container invalid-feedback" id="allows_posting-error"></div>
                    </div>
                    <!--end::Classificação da Conta-->

                </div>
                <!--end::Modal body-->

                <!--begin::Actions-->
                <div class="modal-footer border-top py-5 px-9">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <span class="indicator-label">
                            <i class="bi bi-check-lg me-1"></i> Salvar Conta
                        </span>
                        <span class="indicator-progress">Por favor, aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
                <!--end::Actions-->
            </form>
            <!--end:Form-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal-->

<!--begin::Styles-->
<style>
    .card-classificacao {
        transition: all 0.2s ease;
    }

    .card-classificacao:hover {
        border-color: var(--bs-primary) !important;
        background-color: var(--bs-light);
    }

    .card-classificacao:has(input:checked) {
        border-color: var(--bs-primary) !important;
        background-color: var(--bs-primary-light, rgba(var(--bs-primary-rgb), 0.08));
    }
</style>
<!--end::Styles-->

<!--begin::Scripts-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('kt_modal_new_account_form');
        const submitBtn = document.getElementById('submit-btn');
        const indicator = submitBtn.querySelector('.indicator-progress');
        const label = submitBtn.querySelector('.indicator-label');

        // Base URL para o resource (gerada pelo Blade com prefixo tenant correto)
        const planoContasBaseUrl = "{{ route('contabilidade.plano-contas.index') }}";

        // Inicializa o modal para criação
        function initCreateModal() {
            form.action = "{{ route('contabilidade.plano-contas.store') }}";
            form.method = 'POST';

            // Remove campo _method se existir
            const methodField = form.querySelector('input[name="_method"]');
            if (methodField) methodField.remove();

            // Limpa os campos
            form.reset();

            // Reseta allows_posting para sintética por padrão
            document.getElementById('type_sintetica').checked = true;

            // Atualiza título
            document.getElementById('modal-title').textContent = 'Cadastrar Nova Conta Contábil';
            document.getElementById('modal-subtitle').textContent =
                'Adicione uma nova conta ao seu plano de contas.';

            // Limpa erros
            clearErrors();

            // Esconde hint do tipo
            document.getElementById('type-hint').style.display = 'none';
        }

        // Inicializa o modal para edição
        function initEditModal(contaData) {
            form.action = `${planoContasBaseUrl}/${contaData.id}`;
            form.method = 'POST';

            // Adiciona método PUT
            let methodField = form.querySelector('input[name="_method"]');
            if (!methodField) {
                methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                form.appendChild(methodField);
            }
            methodField.value = 'PUT';

            // Preenche os campos básicos
            document.getElementById('account_code_mask').value = contaData.code;
            form.querySelector('input[name="name"]').value = contaData.name;
            document.getElementById('external_code_input').value = contaData.external_code || '';

            // Preenche e atualiza o select de tipo (Select2)
            const typeSelect = form.querySelector('select[name="type"]');
            typeSelect.value = contaData.type;
            $(typeSelect).trigger('change'); // Atualiza o Select2

            // Preenche e atualiza o select de conta pai (Select2)
            const parentSelect = form.querySelector('select[name="parent_id"]');
            parentSelect.value = contaData.parent_id || '';
            $(parentSelect).trigger('change'); // Atualiza o Select2

            // Preenche classificação (is_analytical vem do banco de dados)
            if (contaData.is_analytical == 1 || contaData.is_analytical === true) {
                document.getElementById('type_analitica').checked = true;
            } else {
                document.getElementById('type_sintetica').checked = true;
            }

            // Atualiza título
            document.getElementById('modal-title').textContent = 'Editar Conta Contábil';
            document.getElementById('modal-subtitle').textContent = 'Edite os dados da conta contábil.';

            // Limpa erros
            clearErrors();

            // Esconde hint do tipo
            document.getElementById('type-hint').style.display = 'none';
        }

        // Auto-preenche o tipo E o código baseado na conta pai selecionada
        const parentSelect = document.getElementById('parent_id_select');
        const typeSelect = document.getElementById('type_select');
        const typeHint = document.getElementById('type-hint');
        const codeInput = document.getElementById('account_code_mask');

        // Função para buscar o próximo código disponível
        async function fetchNextCode(parentId) {
            try {
                const url = new URL('{{ route('contabilidade.plano-contas.next-code') }}', window.location
                    .origin);
                if (parentId) {
                    url.searchParams.append('parent_id', parentId);
                }

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });

                const data = await response.json();

                if (data.success && data.next_code) {
                    codeInput.value = data.next_code;
                    // Adiciona destaque visual temporário
                    codeInput.classList.add('border-success');
                    setTimeout(() => codeInput.classList.remove('border-success'), 1500);
                }
            } catch (error) {
                console.error('Erro ao buscar próximo código:', error);
            }
        }

        if (parentSelect && typeSelect) {
            // Usando evento do Select2 para melhor compatibilidade
            $(parentSelect).on('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const parentId = selectedOption?.value || null;

                if (selectedOption && selectedOption.value) {
                    const parentType = selectedOption.getAttribute('data-type');

                    if (parentType) {
                        // Preenche automaticamente o tipo
                        typeSelect.value = parentType;

                        // Dispara o evento change do Select2
                        $(typeSelect).trigger('change');

                        // Mostra hint
                        typeHint.style.display = 'block';
                    } else {
                        typeHint.style.display = 'none';
                    }
                } else {
                    // Limpa o tipo se não houver pai selecionado
                    typeHint.style.display = 'none';
                }

                // Busca o próximo código disponível (só para criação)
                const isEditing = form.querySelector('input[name="_method"]')?.value === 'PUT';
                if (!isEditing) {
                    fetchNextCode(parentId);
                }
            });
        }

        // Limpa mensagens de erro
        function clearErrors() {
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }

        // Exibe erros de validação
        function showErrors(errors) {
            clearErrors();

            Object.keys(errors).forEach(field => {
                const input = form.querySelector(`[name="${field}"]`);
                const errorDiv = document.getElementById(`${field}-error`);

                if (input && errorDiv) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = errors[field][0];
                    errorDiv.style.display = 'block';
                }
            });
        }

        // Manipula o envio do formulário
        let isSubmitting = false;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Proteção contra duplo-clique
            if (isSubmitting) return;

            // Limpa erros anteriores
            clearErrors();

            // ─── Validação client-side ───
            const clientErrors = {};

            const nameValue = form.querySelector('input[name="name"]').value.trim();
            const codeValue = document.getElementById('account_code_mask').value.trim();
            const typeValue = form.querySelector('select[name="type"]').value;

            if (!nameValue) {
                clientErrors.name = ['O nome da conta é obrigatório.'];
            }
            if (!codeValue) {
                clientErrors.code = ['O código da conta é obrigatório.'];
            }
            if (!typeValue) {
                clientErrors.type = ['O tipo da conta é obrigatório.'];
            }

            if (Object.keys(clientErrors).length > 0) {
                showErrors(clientErrors);
                return;
            }

            // Marca como enviando e mostra loading
            isSubmitting = true;
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;
            indicator.style.display = 'inline-block';
            label.style.display = 'none';

            // Monta payload manualmente para garantir que Select2 e radios sejam capturados
            const isEditing = form.querySelector('input[name="_method"]')?.value === 'PUT';
            const payload = {
                code: document.getElementById('account_code_mask').value,
                name: form.querySelector('input[name="name"]').value,
                type: form.querySelector('select[name="type"]').value,
                parent_id: form.querySelector('select[name="parent_id"]').value || null,
                allows_posting: form.querySelector('input[name="allows_posting"]:checked')?.value || '0',
                external_code: document.getElementById('external_code_input').value || null,
            };

            if (isEditing) {
                payload._method = 'PUT';
            }

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => {
                    return response.json().then(data => ({
                        ok: response.ok,
                        status: response.status,
                        data: data
                    }));
                })
                .then(({ ok, status, data }) => {
                    if (ok && (data.message || data.success)) {
                        // Sucesso: fecha o modal e mostra toast
                        const modalEl = document.getElementById('kt_modal_new_account');
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        // Recarrega apenas a tabela (sem reload da página toda)
                        if (typeof window.reloadPlanoContasTable === 'function') {
                            window.reloadPlanoContasTable();
                        }

                        // Toast de sucesso imediato
                        toastr.options = {
                            closeButton: true,
                            progressBar: true,
                            positionClass: 'toastr-top-right',
                            timeOut: 4000,
                        };
                        toastr.success(data.message || 'Operação realizada com sucesso!');
                    } else if (status === 422 && data.errors) {
                        // Erros de validação
                        showErrors(data.errors);
                    } else {
                        // Erro do servidor (403, 500, etc)
                        toastr.error(data.message || 'Ocorreu um erro inesperado.');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    toastr.error('Erro de comunicação com o servidor. Tente novamente.');
                })
                .finally(() => {
                    // Remove loading e reabilita o botão
                    isSubmitting = false;
                    submitBtn.disabled = false;
                    submitBtn.removeAttribute('data-kt-indicator');
                    indicator.style.display = 'none';
                    label.style.display = 'inline-block';
                });
        });

        // Inicializa modal para criação quando aberto
        document.querySelector('[data-bs-target="#kt_modal_new_account"]').addEventListener('click',
            function() {
                initCreateModal();
            });

        // Função global para edição (chamada pelo JavaScript da tabela)
        window.editPlanoConta = function(contaData) {
            initEditModal(contaData);
            const modal = new bootstrap.Modal(document.getElementById('kt_modal_new_account'));
            modal.show();
        };
    });
</script>
<!--end::Scripts-->
