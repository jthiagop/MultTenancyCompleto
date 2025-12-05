<!--begin::Modal - Cadastrar/Editar Conta Contábil-->
<div class="modal fade" id="kt_modal_new_account" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <i class="fa-solid fa-xmark"></i>
                    </span>
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin:Form-->
                <form id="kt_modal_new_account_form" class="form" method="POST">
                    @csrf
                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <h1 class="mb-3" id="modal-title">Cadastrar Nova Conta Contábil</h1>
                        <div class="text-muted fw-semibold fs-5" id="modal-subtitle">Adicione uma nova conta ao seu plano de contas.</div>
                    </div>
                    <!--end::Heading-->

                    <!--begin::Input group-->
                    <div class="row g-9 mb-8">
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Código da Conta</label>
                            <input type="text" id="account_code_mask" class="form-control form-control-solid"
                                placeholder="Ex: 1.01.01.001" name="code" value="{{ old('code') }}" />
                            <div class="fv-plugins-message-container invalid-feedback" id="code-error"></div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Tipo</label>
                            <select class="form-select form-select-solid" data-control="select2" data-hide-search="true"
                                data-dropdown-parent="#kt_modal_new_account" data-placeholder="Selecione o tipo" name="type">
                                <option></option>
                                <option value="ativo" {{ old('type') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="passivo" {{ old('type') == 'passivo' ? 'selected' : '' }}>Passivo</option>
                                <option value="patrimonio_liquido" {{ old('type') == 'patrimonio_liquido' ? 'selected' : '' }}>Patrimônio Líquido</option>
                                <option value="receita" {{ old('type') == 'receita' ? 'selected' : '' }}>Receita</option>
                                <option value="despesa" {{ old('type') == 'despesa' ? 'selected' : '' }}>Despesa</option>
                            </select>
                            <div class="fv-plugins-message-container invalid-feedback" id="type-error"></div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Nome da Conta</label>
                        <input type="text" class="form-control form-control-solid"
                            placeholder="Ex: Caixa Geral da Matriz" name="name" value="{{ old('name') }}" />
                        <div class="fv-plugins-message-container invalid-feedback" id="name-error"></div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="fs-6 fw-semibold mb-2">Conta Pai (Hierarquia)</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-dropdown-parent="#kt_modal_new_account" data-placeholder="Selecione uma conta pai (opcional)" name="parent_id">
                            <option></option>
                            @isset($contas)
                                @foreach ($contas as $conta)
                                    <option value="{{ $conta->id }}" {{ old('parent_id') == $conta->id ? 'selected' : '' }}>
                                        {{ $conta->code }} - {{ $conta->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <div class="fv-plugins-message-container invalid-feedback" id="parent_id-error"></div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <span class="indicator-label">Salvar Conta</span>
                            <span class="indicator-progress">Por favor, aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
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
<!--end::Modal-->

<!--begin::Scripts-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('kt_modal_new_account_form');
    const submitBtn = document.getElementById('submit-btn');
    const indicator = submitBtn.querySelector('.indicator-progress');
    const label = submitBtn.querySelector('.indicator-label');

    // Inicializa o modal para criação
    function initCreateModal() {
        form.action = "{{ route('contabilidade.plano-contas.store') }}";
        form.method = 'POST';

        // Remove campo _method se existir
        const methodField = form.querySelector('input[name="_method"]');
        if (methodField) methodField.remove();

        // Limpa os campos
        form.reset();

        // Atualiza título
        document.getElementById('modal-title').textContent = 'Cadastrar Nova Conta Contábil';
        document.getElementById('modal-subtitle').textContent = 'Adicione uma nova conta ao seu plano de contas.';

        // Limpa erros
        clearErrors();
    }

    // Inicializa o modal para edição
    function initEditModal(contaData) {
        form.action = `/contabilidade/plano-contas/${contaData.id}`;
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

        // Preenche os campos
        document.getElementById('account_code_mask').value = contaData.code;
        form.querySelector('select[name="type"]').value = contaData.type;
        form.querySelector('input[name="name"]').value = contaData.name;
        form.querySelector('select[name="parent_id"]').value = contaData.parent_id || '';

        // Atualiza título
        document.getElementById('modal-title').textContent = 'Editar Conta Contábil';
        document.getElementById('modal-subtitle').textContent = 'Edite os dados da conta contábil.';

        // Limpa erros
        clearErrors();
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
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Mostra loading
        submitBtn.setAttribute('data-kt-indicator', 'on');
        indicator.style.display = 'inline-block';
        label.style.display = 'none';

        // Limpa erros anteriores
        clearErrors();

        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                // Sucesso
                Swal.fire({
                    text: data.message,
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok!",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                }).then(() => {
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                });
            } else if (data.errors) {
                // Erros de validação
                showErrors(data.errors);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                text: "Ocorreu um erro inesperado. Tente novamente.",
                icon: "error",
                buttonsStyling: false,
                confirmButtonText: "Ok!",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
            });
        })
        .finally(() => {
            // Remove loading
            submitBtn.removeAttribute('data-kt-indicator');
            indicator.style.display = 'none';
            label.style.display = 'inline-block';
        });
    });

    // Inicializa modal para criação quando aberto
    document.querySelector('[data-bs-target="#kt_modal_new_account"]').addEventListener('click', function() {
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
