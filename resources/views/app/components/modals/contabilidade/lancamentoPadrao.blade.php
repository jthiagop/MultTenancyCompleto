<!--begin::Modal - Cadastro de Lançamento Padrão-->
<div class="modal fade" id="kt_modal_lancamento_padrao" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-top mw-900px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin:Form-->
            <form id="kt_modal_lancamento_padrao_form" class="form" method="POST"
                action="{{ route('lancamentoPadrao.store') }}"
                data-original-action="{{ route('lancamentoPadrao.store') }}">
                @csrf
                <input type="hidden" name="lancamento_padrao_id" id="lancamento_padrao_id" value="">
                <!--begin::Modal header-->
                <div class="modal-header justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge fs-6 px-4 py-3" id="badge_tipo_lancamento">
                            <i class="bi bi-arrow-down-circle me-1"></i> Despesa (Saída)
                        </span>
                        <h3 class="modal-title fw-bold mb-0" id="kt_modal_lancamento_padrao_title">Cadastro de Lançamento Padrão</h3>
                    </div>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <i class="bi bi-x-lg fs-3"></i>
                        </span>
                    </div>
                </div>
                <!--end::Modal header-->

                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-15 ">
                    <!--begin::Hidden Type Field-->
                    <input type="hidden" name="type" id="type_hidden" value="saida">
                    <!--end::Hidden Type Field-->
                    <!--begin::Row - Tipo e Categoria-->
                    <div class="row mb-8 g-5">
                        <!--begin::Col - Tipo-->
                        <div class="col-md-8 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Descrição</label>
                            <input type="text" class="form-control" name="description" id="description"
                                placeholder="Ex: Pagamento de Internet Fibra Otica" value="{{ old('description') }}" />
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span class="error-message text-danger fs-7" id="error-description"
                                        style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Col-->

                        <!--begin::Col - Categoria-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Subcategoria</label>
                            <select class="form-select" data-control="select2"
                                data-dropdown-parent="#kt_modal_lancamento_padrao"
                                data-placeholder="Selecione a categoria..." name="category" id="category">
                                <option></option>
                                <option value="Administrativo"
                                    {{ old('category') === 'Administrativo' ? 'selected' : '' }}>
                                    Administrativo</option>
                                <option value="Alimentação" {{ old('category') === 'Alimentação' ? 'selected' : '' }}>
                                    Alimentação</option>
                                <option value="Cerimônias" {{ old('category') === 'Cerimônias' ? 'selected' : '' }}>
                                    Cerimônias</option>
                                <option value="Comércio" {{ old('category') === 'Comércio' ? 'selected' : '' }}>Comércio
                                </option>
                                <option value="Coletas" {{ old('category') === 'Coletas' ? 'selected' : '' }}>Coletas
                                </option>
                                <option value="Comunicação" {{ old('category') === 'Comunicação' ? 'selected' : '' }}>
                                    Comunicação</option>
                                <option value="Contribuições"
                                    {{ old('category') === 'Contribuições' ? 'selected' : '' }}>
                                    Contribuições</option>
                                <option value="Doações" {{ old('category') === 'Doações' ? 'selected' : '' }}>Doações
                                </option>
                                <option value="Educação" {{ old('category') === 'Educação' ? 'selected' : '' }}>
                                    Educação
                                </option>
                                <option value="Equipamentos"
                                    {{ old('category') === 'Equipamentos' ? 'selected' : '' }}>
                                    Equipamentos</option>
                                <option value="Eventos" {{ old('category') === 'Eventos' ? 'selected' : '' }}>Eventos
                                </option>
                                <option value="Intenções" {{ old('category') === 'Intenções' ? 'selected' : '' }}>
                                    Intenções
                                </option>
                                <option value="Liturgia" {{ old('category') === 'Liturgia' ? 'selected' : '' }}>
                                    Liturgia
                                </option>
                                <option value="Manutenção" {{ old('category') === 'Manutenção' ? 'selected' : '' }}>
                                    Manutenção</option>
                                <option value="Material de escritório"
                                    {{ old('category') === 'Material de escritório' ? 'selected' : '' }}>Material de
                                    escritório</option>
                                <option value="Pessoal" {{ old('category') === 'Pessoal' ? 'selected' : '' }}>Pessoal
                                </option>
                                <option value="Rendimentos" {{ old('category') === 'Rendimentos' ? 'selected' : '' }}>
                                    Rendimentos</option>
                                <option value="Saúde" {{ old('category') === 'Saúde' ? 'selected' : '' }}>Saúde
                                </option>
                                <option value="Serviços essenciais"
                                    {{ old('category') === 'Serviços essenciais' ? 'selected' : '' }}>Serviços
                                    essenciais
                                </option>
                                <option value="Suprimentos" {{ old('category') === 'Suprimentos' ? 'selected' : '' }}>
                                    Suprimentos</option>
                                <option value="Financeiro" {{ old('category') === 'Financeiro' ? 'selected' : '' }}>
                                    Financeiro</option>
                                <option value="Transporte" {{ old('category') === 'Transporte' ? 'selected' : '' }}>
                                    Transporte</option>
                                <option value="Telecomunicações"
                                    {{ old('category') === 'Telecomunicações' ? 'selected' : '' }}>Telecomunicações
                                </option>
                            </select>
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span class="error-message text-danger fs-7" id="error-category"
                                        style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                    <!--begin::Heading - Regras Contábeis-->
                    <div class="mb-8">
                        <h3 class="fw-bold text-gray-800 mb-3">Regras Contábeis</h3>
                    </div>
                    <div class="row mb-8">
                        <!--begin::Input group - Conta de Débito-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold ">Conta de Débito (Onde aplica o
                                recurso?)</label>
                            <select class="form-select" data-control="select2"
                                data-dropdown-parent="#kt_modal_lancamento_padrao"
                                data-placeholder="Selecione a conta de débito..." name="conta_debito_id"
                                id="conta_debito_id">
                                <option></option>
                                @isset($contas)
                                    @foreach ($contas as $conta)
                                        <option value="{{ $conta->id }}" data-type="{{ $conta->type }}">
                                            {{ $conta->code }} - {{ $conta->name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-7 mt-2">Selecione a conta onde o recurso será aplicado.</div>
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span class="error-message text-danger fs-7" id="error-conta_debito_id"
                                        style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Conta de Crédito-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Conta de Crédito (De onde sai o
                                recurso?)</label>
                            <select class="form-select" data-control="select2"
                                data-dropdown-parent="#kt_modal_lancamento_padrao"
                                data-placeholder="Selecione a conta de crédito..." name="conta_credito_id"
                                id="conta_credito_id">
                                <option></option>
                                <option value="0" data-type="banco_caixa">-- Usar a conta do Banco/Caixa
                                    selecionado --
                                </option>
                                @isset($contas)
                                    @foreach ($contas as $conta)
                                        <option value="{{ $conta->id }}" data-type="{{ $conta->type }}">
                                            {{ $conta->code }} - {{ $conta->name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            <div class="text-muted fs-7 mt-2">Deixe a opção especial se o pagamento pode sair de
                                qualquer
                                banco.</div>
                            <div class="fv-plugins-message-container">
                                <div class="fv-help-block">
                                    <span class="error-message text-danger fs-7" id="error-conta_credito_id"
                                        style="display: none;"></span>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->
                    </div>

                </div>
                <!--end::Modal body-->
                <!--begin::Actions-->
                <div class="modal-footer flex-center">
                    <button type="reset" data-bs-dismiss="modal" class="btn btn-sm btn-light me-3">
                        <i class="bi bi-x-lg me-2"></i> Cancelar
                    </button>
                    <button type="submit" id="kt_modal_lancamento_padrao_submit" class="btn btn-sm btn-primary">
                        <span class="indicator-label"> <i class="bi bi-save me-2"></i> Salvar</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
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
<!--end::Modal - Cadastro de Lançamento Padrão-->
<!--begin::Script para filtrar contas baseado no tipo-->
<script>
    (function() {
        'use strict';

        let todasContasDebito = [];
        let todasContasCredito = [];

        function initFiltroContas() {
            const tipoHidden = document.getElementById('type_hidden');
            const badgeTipo = document.getElementById('badge_tipo_lancamento');
            const contaDebitoSelect = document.getElementById('conta_debito_id');
            const contaCreditoSelect = document.getElementById('conta_credito_id');

            if (!tipoHidden || !contaDebitoSelect || !contaCreditoSelect) {
                return;
            }

            // Armazena todas as opções originais na primeira vez
            if (todasContasDebito.length === 0) {
                todasContasDebito = Array.from(contaDebitoSelect.querySelectorAll('option')).map(opt => ({
                    value: opt.value,
                    text: opt.textContent,
                    type: opt.getAttribute('data-type')
                }));
            }

            if (todasContasCredito.length === 0) {
                todasContasCredito = Array.from(contaCreditoSelect.querySelectorAll('option')).map(opt => ({
                    value: opt.value,
                    text: opt.textContent,
                    type: opt.getAttribute('data-type')
                }));
            }

            // Função para atualizar o badge visual do tipo
            function atualizarBadgeTipo(tipo) {
                if (!badgeTipo) return;
                
                if (tipo === 'entrada') {
                    badgeTipo.className = 'badge badge-light-success fs-6 px-4 py-3';
                    badgeTipo.innerHTML = '<i class="bi bi-arrow-up-circle me-1 text-success"></i> Receita (Entrada)';
                } else {
                    badgeTipo.className = 'badge badge-light-danger fs-6 px-4 py-3';
                    badgeTipo.innerHTML = '<i class="bi bi-arrow-down-circle me-1 text-danger"></i> Despesa (Saída)';
                }
            }

            // Função para filtrar opções baseado no tipo
            function filtrarContas() {
                const tipoSelecionado = tipoHidden.value || 'saida';
                
                // Atualiza o badge visual
                atualizarBadgeTipo(tipoSelecionado);

                // Limpa e recria as opções de Débito
                contaDebitoSelect.innerHTML = '<option></option>';
                todasContasDebito.forEach(opt => {
                    if (opt.value === '') {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = '';
                        contaDebitoSelect.appendChild(option);
                    } else {
                        if (tipoSelecionado === 'saida') {
                            // Saída: Débito = despesa
                            if (opt.type === 'despesa') {
                                const option = document.createElement('option');
                                option.value = opt.value;
                                option.textContent = opt.text;
                                option.setAttribute('data-type', opt.type);
                                contaDebitoSelect.appendChild(option);
                            }
                        } else {
                            // Entrada: Débito = ativo
                            if (opt.type === 'ativo') {
                                const option = document.createElement('option');
                                option.value = opt.value;
                                option.textContent = opt.text;
                                option.setAttribute('data-type', opt.type);
                                contaDebitoSelect.appendChild(option);
                            }
                        }
                    }
                });

                // Limpa e recria as opções de Crédito
                contaCreditoSelect.innerHTML = '<option></option>';
                todasContasCredito.forEach(opt => {
                    if (opt.value === '') {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = '';
                        contaCreditoSelect.appendChild(option);
                    } else if (opt.value === '0' && opt.type === 'banco_caixa') {
                        // Sempre inclui a opção especial
                        const option = document.createElement('option');
                        option.value = opt.value;
                        option.textContent = opt.text;
                        option.setAttribute('data-type', opt.type);
                        contaCreditoSelect.appendChild(option);
                    } else {
                        if (tipoSelecionado === 'saida') {
                            // Saída: Crédito = ativo
                            if (opt.type === 'ativo') {
                                const option = document.createElement('option');
                                option.value = opt.value;
                                option.textContent = opt.text;
                                option.setAttribute('data-type', opt.type);
                                contaCreditoSelect.appendChild(option);
                            }
                        } else {
                            // Entrada: Crédito = receita
                            if (opt.type === 'receita') {
                                const option = document.createElement('option');
                                option.value = opt.value;
                                option.textContent = opt.text;
                                option.setAttribute('data-type', opt.type);
                                contaCreditoSelect.appendChild(option);
                            }
                        }
                    }
                });

                // Reinicializa o Select2 para atualizar a interface
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    if ($(contaDebitoSelect).hasClass('select2-hidden-accessible')) {
                        $(contaDebitoSelect).select2('destroy');
                    }
                    if ($(contaCreditoSelect).hasClass('select2-hidden-accessible')) {
                        $(contaCreditoSelect).select2('destroy');
                    }

                    // Reinicializa Select2
                    $(contaDebitoSelect).select2({
                        dropdownParent: $('#kt_modal_lancamento_padrao'),
                        placeholder: 'Selecione a conta de débito...'
                    });
                    $(contaCreditoSelect).select2({
                        dropdownParent: $('#kt_modal_lancamento_padrao'),
                        placeholder: 'Selecione a conta de crédito...'
                    });
                }
            }

            // Filtra quando o modal é aberto
            const modal = document.getElementById('kt_modal_lancamento_padrao');
            if (modal) {
                modal.addEventListener('show.bs.modal', function(event) {
                    // Verifica se há um tipo pré-selecionado via data attribute
                    const trigger = event.relatedTarget;
                    if (trigger && trigger.dataset.lancamentoType) {
                        const preSelectedType = trigger.dataset.lancamentoType;
                        tipoHidden.value = preSelectedType;
                    }

                    setTimeout(function() {
                        filtrarContas();
                    }, 300);
                });

                // Filtra na inicialização se o modal já estiver visível
                if (modal.classList.contains('show')) {
                    setTimeout(filtrarContas, 100);
                }
            }
        }

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initFiltroContas);
        } else {
            initFiltroContas();
        }
    })();
</script>
<!--end::Script para filtrar contas baseado no tipo-->

<!--begin::Script para validação AJAX em tempo real-->
<script>
    (function() {
        'use strict';

        // Mapeamento de campos para seus IDs de erro
        const fieldErrorMap = {
            'description': 'error-description',
            'type': 'error-type',
            'category': 'error-category',
            'conta_debito_id': 'error-conta_debito_id',
            'conta_credito_id': 'error-conta_credito_id'
        };

        // Função para validar campo via AJAX
        function validateField(fieldName, fieldValue) {
            const errorElement = document.getElementById(fieldErrorMap[fieldName]);
            if (!errorElement) return;

            // Limpa erro anterior
            errorElement.style.display = 'none';
            errorElement.textContent = '';

            // Remove classes de erro do campo
            const fieldElement = document.getElementById(fieldName) || document.querySelector('[name="' +
                fieldName + '"]');
            if (fieldElement) {
                fieldElement.classList.remove('is-invalid');
                fieldElement.classList.add('is-valid');
            }

            // Se o campo estiver vazio e não for obrigatório, não valida
            if (!fieldValue || fieldValue === '' || fieldValue === null) {
                // Verifica se é obrigatório
                if (fieldName === 'description' || fieldName === 'type' || fieldName === 'category') {
                    // Campos obrigatórios: valida mesmo se vazio
                } else {
                    // Campos opcionais: não valida se vazio
                    return;
                }
            }

            // Prepara dados para envio
            const formData = new FormData();
            formData.append('field', fieldName);
            formData.append('value', fieldValue);
            formData.append('_token', document.querySelector('input[name="_token"]').value);

            // Faz requisição AJAX
            fetch('{{ route('lancamentoPadrao.validate-field') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        // Exibe erro
                        errorElement.textContent = data.message;
                        errorElement.style.display = 'block';

                        // Adiciona classe de erro ao campo
                        if (fieldElement) {
                            fieldElement.classList.remove('is-valid');
                            fieldElement.classList.add('is-invalid');
                        }
                    } else {
                        // Remove erro se válido
                        errorElement.style.display = 'none';
                        if (fieldElement) {
                            fieldElement.classList.remove('is-invalid');
                            if (fieldValue && fieldValue !== '') {
                                fieldElement.classList.add('is-valid');
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao validar campo:', error);
                });
        }

        // Função para inicializar validação AJAX
        function initAjaxValidation() {
            // Campo Descrição
            const descriptionField = document.getElementById('description');
            if (descriptionField) {
                let descriptionTimeout;
                descriptionField.addEventListener('blur', function() {
                    clearTimeout(descriptionTimeout);
                    validateField('description', this.value);
                });
                descriptionField.addEventListener('input', function() {
                    clearTimeout(descriptionTimeout);
                    descriptionTimeout = setTimeout(() => {
                        if (this.value.length > 0) {
                            validateField('description', this.value);
                        }
                    }, 500); // Debounce de 500ms
                });
            }

            // Campo Tipo (hidden field - validação automática quando o modal abre)
            const typeHidden = document.getElementById('type_hidden');
            if (typeHidden && typeHidden.value) {
                validateField('type', typeHidden.value);
            }

            // Campo Categoria (Select2)
            const categorySelect = document.getElementById('category');
            if (categorySelect) {
                // Para Select2, usa evento change do jQuery
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(categorySelect).on('change', function() {
                        validateField('category', $(this).val());
                    });
                } else {
                    categorySelect.addEventListener('change', function() {
                        validateField('category', this.value);
                    });
                }
            }

            // Campo Conta de Débito (Select2)
            const contaDebitoSelect = document.getElementById('conta_debito_id');
            if (contaDebitoSelect) {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(contaDebitoSelect).on('change', function() {
                        validateField('conta_debito_id', $(this).val());
                    });
                } else {
                    contaDebitoSelect.addEventListener('change', function() {
                        validateField('conta_debito_id', this.value);
                    });
                }
            }

            // Campo Conta de Crédito (Select2)
            const contaCreditoSelect = document.getElementById('conta_credito_id');
            if (contaCreditoSelect) {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(contaCreditoSelect).on('change', function() {
                        validateField('conta_credito_id', $(this).val());
                    });
                } else {
                    contaCreditoSelect.addEventListener('change', function() {
                        validateField('conta_credito_id', this.value);
                    });
                }
            }
        }

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                // Aguarda um pouco para garantir que o Select2 esteja inicializado
                setTimeout(initAjaxValidation, 500);
            });
        } else {
            setTimeout(initAjaxValidation, 500);
        }

        // Reinicializa quando o modal é aberto
        const modal = document.getElementById('kt_modal_lancamento_padrao');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                setTimeout(initAjaxValidation, 300);
            });
        }
    })();
</script>
<!--end::Script para validação AJAX em tempo real-->

<!--begin::Script para submit do formulário via AJAX-->
<script>
    (function() {
        'use strict';

        let formSubmitInitialized = false; // Flag para evitar múltiplas inicializações

        function initFormSubmit() {
            const form = document.getElementById('kt_modal_lancamento_padrao_form');
            const submitButton = document.getElementById('kt_modal_lancamento_padrao_submit');
            const modalElement = document.getElementById('kt_modal_lancamento_padrao');

            if (!form || !submitButton) {
                return;
            }

            // Se já foi inicializado, não adiciona novamente o event listener
            if (formSubmitInitialized) {
                return;
            }

            formSubmitInitialized = true; // Marca como inicializado

            // Previne o submit padrão do formulário
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Valida todos os campos obrigatórios antes de enviar
                const description = document.getElementById('description').value.trim();
                const type = document.querySelector('input[name="type"]:checked');
                const category = document.getElementById('category');
                const contaDebito = document.getElementById('conta_debito_id');
                const contaCredito = document.getElementById('conta_credito_id');

                let hasErrors = false;

                // Limpa erros anteriores
                document.querySelectorAll('.error-message').forEach(el => {
                    el.style.display = 'none';
                    el.textContent = '';
                });
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });

                // Valida descrição
                if (!description) {
                    const errorEl = document.getElementById('error-description');
                    if (errorEl) {
                        errorEl.textContent = 'O nome do lançamento é obrigatório.';
                        errorEl.style.display = 'block';
                    }
                    document.getElementById('description').classList.add('is-invalid');
                    hasErrors = true;
                }

                // Valida tipo
                if (!type) {
                    const errorEl = document.getElementById('error-type');
                    if (errorEl) {
                        errorEl.textContent = 'O tipo do lançamento é obrigatório.';
                        errorEl.style.display = 'block';
                    }
                    hasErrors = true;
                }

                // Valida categoria
                const categoryValue = category ? (typeof $ !== 'undefined' && $(category).hasClass(
                    'select2-hidden-accessible') ? $(category).val() : category.value) : '';
                if (!categoryValue) {
                    const errorEl = document.getElementById('error-category');
                    if (errorEl) {
                        errorEl.textContent = 'A categoria é obrigatória.';
                        errorEl.style.display = 'block';
                    }
                    if (category) category.classList.add('is-invalid');
                    hasErrors = true;
                }

                // Valida conta de débito
                const contaDebitoValue = contaDebito ? (typeof $ !== 'undefined' && $(contaDebito).hasClass(
                    'select2-hidden-accessible') ? $(contaDebito).val() : contaDebito.value) : '';
                if (!contaDebitoValue) {
                    const errorEl = document.getElementById('error-conta_debito_id');
                    if (errorEl) {
                        errorEl.textContent = 'A conta de débito é obrigatória.';
                        errorEl.style.display = 'block';
                    }
                    if (contaDebito) contaDebito.classList.add('is-invalid');
                    hasErrors = true;
                }

                // Valida conta de crédito (permite "0" que significa usar conta do banco/caixa)
                const contaCreditoValue = contaCredito ? (typeof $ !== 'undefined' && $(contaCredito)
                        .hasClass('select2-hidden-accessible') ? $(contaCredito).val() : contaCredito.value
                        ) : '';
                if (contaCreditoValue === null || contaCreditoValue === '' || contaCreditoValue ===
                    undefined) {
                    const errorEl = document.getElementById('error-conta_credito_id');
                    if (errorEl) {
                        errorEl.textContent = 'A conta de crédito é obrigatória.';
                        errorEl.style.display = 'block';
                    }
                    if (contaCredito) contaCredito.classList.add('is-invalid');
                    hasErrors = true;
                }

                if (hasErrors) {
                    // Scroll para o primeiro erro
                    const firstError = form.querySelector('.is-invalid, .error-message[style*="block"]');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                    return;
                }

                // Verificar se é edição ou criação
                const lancamentoId = document.getElementById('lancamento_padrao_id').value;
                const isEdit = lancamentoId && lancamentoId !== '';

                // Ajustar action e method do formulário
                let formAction = form.action;
                let formMethod = 'POST';

                if (isEdit) {
                    formAction = form.getAttribute('data-original-action').replace('/lancamentoPadrao',
                        '/lancamentoPadrao/' + lancamentoId);
                    formMethod = 'PUT';
                } else {
                    formAction = form.getAttribute('data-original-action');
                }

                // Prepara FormData
                const formData = new FormData(form);

                // Adicionar method spoofing se for PUT
                if (isEdit) {
                    formData.append('_method', 'PUT');
                }

                // Ativa indicador de loading
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                // Envia via AJAX
                fetch(formAction, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        }
                    })
                    .then(response => {
                        if (response.redirected) {
                            // Se houver redirect, segue o redirect
                            window.location.href = response.url;
                            return null;
                        }
                        return response.json().catch(() => {
                            // Se não for JSON, retorna null
                            return null;
                        });
                    })
                    .then(data => {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;

                        // Se response foi redirect, data será null
                        if (data === null) {
                            return;
                        }

                        // Trata erros de validação do Laravel
                        if (data && data.errors) {
                            // Exibe erros de validação
                            Object.keys(data.errors).forEach(field => {
                                const errorMessages = data.errors[field];
                                const errorEl = document.getElementById('error-' + field);
                                const fieldEl = document.getElementById(field) || document
                                    .querySelector('[name="' + field + '"]');

                                if (errorEl && errorMessages && errorMessages.length > 0) {
                                    errorEl.textContent = errorMessages[0];
                                    errorEl.style.display = 'block';
                                }

                                if (fieldEl) {
                                    fieldEl.classList.add('is-invalid');
                                    fieldEl.classList.remove('is-valid');
                                }
                            });

                            // Scroll para o primeiro erro
                            const firstError = form.querySelector(
                                '.is-invalid, .error-message[style*="block"]');
                            if (firstError) {
                                firstError.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    text: 'Por favor, corrija os erros no formulário.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, entendi!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                            return;
                        }

                        if (data && data.success !== undefined && data.success) {
                            // Sucesso
                            const isEdit = document.getElementById('lancamento_padrao_id').value !== '';
                            const successMessage = isEdit ? 'Lançamento atualizado com sucesso!' :
                                'Lançamento cadastrado com sucesso!';

                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    text: data.message || successMessage,
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, entendi!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(function() {
                                    // Fecha o modal
                                    if (modalElement) {
                                        const modal = bootstrap.Modal.getInstance(modalElement);
                                        if (modal) {
                                            modal.hide();
                                        }
                                    }
                                    // Reseta o formulário usando função global
                                    if (typeof window.resetLancamentoPadraoModal ===
                                        'function') {
                                        window.resetLancamentoPadraoModal();
                                    } else {
                                        form.reset();
                                        document.getElementById('lancamento_padrao_id').value =
                                            '';
                                    }
                                    // Limpa mensagens de erro
                                    document.querySelectorAll('.error-message').forEach(el => {
                                        el.style.display = 'none';
                                        el.textContent = '';
                                    });
                                    document.querySelectorAll('.is-invalid, .is-valid').forEach(
                                        el => {
                                            el.classList.remove('is-invalid', 'is-valid');
                                        });
                                    // Recarrega a página ou atualiza a tabela
                                    if (typeof window.location !== 'undefined') {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                // Fallback se Swal não estiver disponível
                                alert(data.message || 'Lançamento cadastrado com sucesso!');
                                if (modalElement) {
                                    const modal = bootstrap.Modal.getInstance(modalElement);
                                    if (modal) {
                                        modal.hide();
                                    }
                                }
                                form.reset();
                                window.location.reload();
                            }
                        } else {
                            // Erro genérico
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    text: data.message ||
                                        'Erro ao cadastrar lançamento. Por favor, tente novamente.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok, entendi!',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            } else {
                                alert(data.message ||
                                    'Erro ao cadastrar lançamento. Por favor, tente novamente.');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao enviar formulário:', error);
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: 'Erro ao enviar formulário. Por favor, tente novamente.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, entendi!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        } else {
                            alert('Erro ao enviar formulário. Por favor, tente novamente.');
                        }
                    });
            });
        }

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(initFormSubmit, 500);
            });
        } else {
            setTimeout(initFormSubmit, 500);
        }

        // NÃO reinicializa quando o modal é aberto (removido para evitar duplicação)
        // O event listener já está anexado e permanece ativo
    })();
</script>
<!--end::Script para submit do formulário via AJAX-->

<!--begin::Script para edição de lançamento padrão-->
<script>
    // Função global para abrir modal em modo de edição
    window.openLancamentoPadraoForEdit = function(lancamentoId) {
        const modal = document.getElementById('kt_modal_lancamento_padrao');
        const form = document.getElementById('kt_modal_lancamento_padrao_form');
        const modalTitle = document.getElementById('kt_modal_lancamento_padrao_title');

        if (!modal || !form) {
            console.error('Modal ou formulário não encontrado');
            return;
        }

        // Mostrar loading
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Carregando...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // Buscar dados do lançamento
        fetch(`/lancamentoPadrao/${lancamentoId}/edit`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }

                if (data.success && data.data) {
                    const lp = data.data;

                    // Preencher campos básicos do formulário
                    document.getElementById('lancamento_padrao_id').value = lp.id;
                    document.getElementById('description').value = lp.description || '';

                    // Selecionar tipo (radio) - fazer isso antes de abrir o modal para filtrar contas
                    if (lp.type) {
                        const typeRadio = document.querySelector(`input[name="type"][value="${lp.type}"]`);
                        if (typeRadio) {
                            typeRadio.checked = true;
                            // Disparar evento change para filtrar contas
                            if (typeof Event !== 'undefined') {
                                typeRadio.dispatchEvent(new Event('change', {
                                    bubbles: true
                                }));
                            } else if (typeof jQuery !== 'undefined') {
                                $(typeRadio).trigger('change');
                            }
                        }
                    }

                    // Atualizar título do modal
                    if (modalTitle) {
                        modalTitle.textContent = 'Editar Lançamento Padrão';
                    }

                    // Limpar erros anteriores
                    document.querySelectorAll('.error-message').forEach(el => {
                        el.style.display = 'none';
                        el.textContent = '';
                    });
                    document.querySelectorAll('.is-invalid').forEach(el => {
                        el.classList.remove('is-invalid');
                    });

                    // Abrir modal primeiro
                    const bootstrapModal = new bootstrap.Modal(modal);
                    bootstrapModal.show();

                    // Aguardar o modal abrir completamente antes de preencher Select2
                    modal.addEventListener('shown.bs.modal', function fillSelectsAfterModalOpen() {
                        // Remover o listener para não executar múltiplas vezes
                        modal.removeEventListener('shown.bs.modal', fillSelectsAfterModalOpen);

                        // Função auxiliar para inicializar Select2 se necessário
                        function ensureSelect2Initialized(selectElement, options) {
                            if (typeof $ !== 'undefined' && $.fn.select2 && selectElement) {
                                if (!$(selectElement).hasClass('select2-hidden-accessible')) {
                                    // Tentar inicializar usando KTUtil se disponível
                                    if (typeof KTUtil !== 'undefined' && KTUtil.initSelect2) {
                                        KTUtil.initSelect2(selectElement);
                                    } else {
                                        // Fallback para inicialização manual
                                        $(selectElement).select2(options || {
                                            dropdownParent: $('#kt_modal_lancamento_padrao')
                                        });
                                    }
                                }
                            }
                        }

                        // Aguardar um pouco mais para garantir que Select2 está pronto
                        setTimeout(function() {
                            // Selecionar categoria (Select2)
                            const categorySelect = document.getElementById('category');
                            if (categorySelect && lp.category) {
                                ensureSelect2Initialized(categorySelect, {
                                    dropdownParent: $('#kt_modal_lancamento_padrao')
                                });
                                if (typeof $ !== 'undefined' && $.fn.select2) {
                                    $(categorySelect).val(String(lp.category)).trigger(
                                    'change');
                                } else {
                                    categorySelect.value = lp.category;
                                }
                            }

                            // Selecionar conta de débito (Select2)
                            const contaDebitoSelect = document.getElementById(
                            'conta_debito_id');
                            if (contaDebitoSelect && lp.conta_debito_id) {
                                const debitoValue = String(lp.conta_debito_id);

                                // Verificar se a opção existe
                                const debitoOption = Array.from(contaDebitoSelect.options).find(
                                    opt => opt.value === debitoValue);

                                if (debitoOption) {
                                    ensureSelect2Initialized(contaDebitoSelect, {
                                        dropdownParent: $(
                                            '#kt_modal_lancamento_padrao'),
                                        placeholder: 'Selecione a conta de débito...'
                                    });

                                    if (typeof $ !== 'undefined' && $.fn.select2) {
                                        // Aguardar um pouco mais para garantir que está pronto
                                        setTimeout(function() {
                                            // Verificar novamente se Select2 está inicializado
                                            if ($(contaDebitoSelect).hasClass(
                                                    'select2-hidden-accessible')) {
                                                $(contaDebitoSelect).val(debitoValue)
                                                    .trigger('change.select2');
                                            } else {
                                                // Se ainda não estiver, definir o valor nativo e inicializar depois
                                                contaDebitoSelect.value = debitoValue;
                                                ensureSelect2Initialized(
                                                    contaDebitoSelect, {
                                                        dropdownParent: $(
                                                            '#kt_modal_lancamento_padrao'
                                                            ),
                                                        placeholder: 'Selecione a conta de débito...'
                                                    });
                                                $(contaDebitoSelect).trigger(
                                                    'change.select2');
                                            }
                                        }, 200);
                                    } else {
                                        contaDebitoSelect.value = debitoValue;
                                    }
                                } else {
                                    console.warn('Opção de débito não encontrada no select:',
                                        debitoValue);
                                }
                            }

                            // Selecionar conta de crédito (Select2)
                            const contaCreditoSelect = document.getElementById(
                                'conta_credito_id');
                            if (contaCreditoSelect) {
                                // Converter null/undefined para '0' se necessário
                                let contaCreditoValue = '0';
                                if (lp.conta_credito_id !== null && lp.conta_credito_id !==
                                    undefined && lp.conta_credito_id !== '' && lp
                                    .conta_credito_id !== 0) {
                                    contaCreditoValue = String(lp.conta_credito_id);
                                }

                                // Verificar se a opção existe
                                const creditoOption = Array.from(contaCreditoSelect.options)
                                    .find(opt => opt.value === contaCreditoValue);

                                if (creditoOption) {
                                    ensureSelect2Initialized(contaCreditoSelect, {
                                        dropdownParent: $(
                                            '#kt_modal_lancamento_padrao'),
                                        placeholder: 'Selecione a conta de crédito...'
                                    });

                                    if (typeof $ !== 'undefined' && $.fn.select2) {
                                        // Aguardar um pouco mais para garantir que está pronto
                                        setTimeout(function() {
                                            // Verificar novamente se Select2 está inicializado
                                            if ($(contaCreditoSelect).hasClass(
                                                    'select2-hidden-accessible')) {
                                                $(contaCreditoSelect).val(
                                                    contaCreditoValue).trigger(
                                                    'change.select2');
                                            } else {
                                                // Se ainda não estiver, definir o valor nativo e inicializar depois
                                                contaCreditoSelect.value =
                                                    contaCreditoValue;
                                                ensureSelect2Initialized(
                                                    contaCreditoSelect, {
                                                        dropdownParent: $(
                                                            '#kt_modal_lancamento_padrao'
                                                            ),
                                                        placeholder: 'Selecione a conta de crédito...'
                                                    });
                                                $(contaCreditoSelect).trigger(
                                                    'change.select2');
                                            }
                                        }, 250);
                                    } else {
                                        contaCreditoSelect.value = contaCreditoValue;
                                    }
                                } else {
                                    console.warn('Opção de crédito não encontrada no select:',
                                        contaCreditoValue);
                                }
                            }
                        }, 400); // Aguardar 400ms após o modal abrir
                    }, {
                        once: true
                    });
                } else {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: 'Erro ao carregar dados do lançamento.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK, entendi!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                }
            })
            .catch(error => {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                    Swal.fire({
                        text: 'Erro ao carregar dados do lançamento.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'OK, entendi!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
                console.error('Erro ao carregar lançamento:', error);
            });
    };

    // Função para resetar modal para modo de criação
    window.resetLancamentoPadraoModal = function() {
        const form = document.getElementById('kt_modal_lancamento_padrao_form');
        const modalTitle = document.getElementById('kt_modal_lancamento_padrao_title');

        if (form) {
            form.reset();
            document.getElementById('lancamento_padrao_id').value = '';

            // Resetar Select2
            if (typeof $ !== 'undefined') {
                $('#category').val(null).trigger('change');
                $('#conta_debito_id').val(null).trigger('change');
                $('#conta_credito_id').val(null).trigger('change');
            }

            // Resetar action
            form.action = form.getAttribute('data-original-action');

            // Remover method spoofing se existir
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                methodInput.remove();
            }
        }

        if (modalTitle) {
            modalTitle.textContent = 'Cadastro de Lançamento Padrão';
        }

        // Limpar erros
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
    };

    // Resetar modal quando fechar
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('kt_modal_lancamento_padrao');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                window.resetLancamentoPadraoModal();
            });
        }
    });
</script>
<!--end::Script para edição de lançamento padrão-->
