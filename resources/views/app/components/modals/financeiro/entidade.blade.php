<!--begin::Modal - Cadastro de Entidade Financeira-->
<div class="modal fade" id="kt_modal_entidade_financeira" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-top mw-1000px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin:Form-->
            <form id="kt_modal_entidade_financeira_form" class="form" method="POST"
                action="{{ route('entidades.store') }}" novalidate>
                @csrf
                <!--begin::Modal header-->
                <div class="modal-header justify-content-between">
                    <h3 class="modal-title fw-bold ">
                        Cadastrar Nova Entidade Financeira</h3>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <i class="bi bi-x-lg fs-3"></i>
                        </span>
                    </div>
                </div>
                <!--end::Modal header-->

                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-5 pb-15">

                    <!--begin::Accordion - Container único para todos os 3 accordions-->
                    <div class="accordion mb-5" id="kt_accordion_principal">
                        <!--begin::Item 1-->
                        <div class="accordion-item mb-4">
                            <h2 class="accordion-header" id="kt_accordion_1_header_1">
                                <button class="accordion-button fs-4 fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_1"
                                    aria-expanded="true" aria-controls="kt_accordion_1_body_1">
                                    <i class="fa-regular fa-circle-check fs-3 text-success me-3 me-3"></i>

                                    Escolha um tipo de Entidade Financeira
                                </button>
                            </h2>
                            <div id="kt_accordion_1_body_1" class="accordion-collapse collapse show"
                                aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#kt_accordion_principal">
                                <div class="accordion-body">
                                    <!--begin::Radio group-->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!--begin::Radio button-->
                                            <input type="radio" class="btn-check" name="tipo_entidade" value="banco"
                                                id="kt_plan_banco" checked />
                                            <label for="kt_plan_banco"
                                                class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5 w-100">
                                                <!--begin::Description-->
                                                <div class="d-flex align-items-center me-2">
                                                    <!--begin::Info-->
                                                    <div class="flex-grow-1">
                                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                                            Banco
                                                        </h2>
                                                        <div class="fw-semibold opacity-50">
                                                            Conecte sua conta bancária para manter o fluxo de caixa
                                                            sempre conciliado.
                                                        </div>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Description-->

                                                <!--begin::Price-->
                                                <div class="ms-5">
                                                    <span class="fs-1 fw-bold text-dark me-1 text-white">
                                                        <i class="fa-solid fa-building-columns"></i>
                                                    </span>
                                                </div>
                                                <!--end::Price-->
                                            </label>
                                            <!--end::Radio button-->
                                        </div>
                                        <div class="col-md-6">
                                            <!--begin::Radio button-->
                                            <input type="radio" class="btn-check" name="tipo_entidade" value="caixa"
                                                id="kt_plan_caixa" />
                                            <label for="kt_plan_caixa"
                                                class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5 w-100">
                                                <!--begin::Description-->
                                                <div class="d-flex align-items-center me-2">
                                                    <!--begin::Info-->
                                                    <div class="flex-grow-1">
                                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                                            Caixa
                                                        </h2>
                                                        <div class="fw-semibold opacity-50">
                                                            Registre entradas e saídas em dinheiro, como caixa
                                                            físico ou fundo fixo.
                                                        </div>
                                                    </div>
                                                    <!--end::Info-->
                                                </div>
                                                <!--end::Description-->
                                                <!--begin::Price-->
                                                <div class="ms-5">
                                                    <span class="fs-1">
                                                        <i class="fa-solid fa-cash-register fs-3"></i>
                                                    </span>
                                                </div>
                                                <!--end::Price-->
                                            </label>
                                            <!--end::Radio button-->
                                        </div>

                                    </div>

                                </div>
                                <!--begin::Footer-->
                                <div class="border-top d-flex px-6 py-3">
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-accordion-1-next"
                                        disabled data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_2"
                                        aria-expanded="false" aria-controls="kt_accordion_1_body_2">Próximo</button>
                                </div>
                                <!--end::Footer-->
                            </div>
                        </div>
                        <!--end::Item 1-->

                        <!--begin::Item 2-->
                        <div class="accordion-item mb-4">
                            <h2 class="accordion-header " id="kt_accordion_1_header_2">
                                <button class="accordion-button fs-4 fw-semibold collapsed p " type="button" disabled
                                    data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_2"
                                    aria-expanded="false" aria-controls="kt_accordion_1_body_2">
                                    <i class="fa-regular fa-circle-check fs-3 me-3" id="icon-accordion-2"></i>
                                    Preencha os dados da Entidade Financeira
                                </button>
                            </h2>
                            <div id="kt_accordion_1_body_2" class="accordion-collapse collapse"
                                aria-labelledby="kt_accordion_1_header_2" data-bs-parent="#kt_accordion_principal">
                                <!--begin::Body-->
                                <div class="accordion-body">
                                    <!-- Nome da Entidade (Caixa) -->
                                    <div class="row mb-5">
                                        <div class="col-12 fv-row" id="nome-entidade-group">
                                            <x-tenant-input name="nome" id="nome" label="Nome da Entidade"
                                                placeholder="Ex: Caixa Central" value="{{ old('nome') }}" required
                                                class="" />
                                        </div>
                                    </div>

                                    <!-- Banco (visível apenas quando banco é selecionado) -->
                                    <div class="row mb-5 d-none" id="banco-group">
                                        <div class="col-6 fv-row">
                                            <x-tenant-input name="nome_banco" id="nome_banco"
                                                label="Apelido da Conta (opcional)" placeholder="Ex: Conta Principal, Conta Salários..."
                                                value="{{ old('nome_banco') }}" class="" />
                                            <div class="text-muted fs-7 mt-1">
                                                <i class="bi bi-info-circle me-1"></i>
                                                O sistema criará automaticamente o nome baseado nos dados bancários
                                            </div>
                                        </div>
                                        <div class="col-6 fv-row">
                                            <label class="fs-5 fw-semibold mb-2 required">Banco</label>
                                            <select id="banco-select" name="bank_id" class="form-select"
                                                data-control="select2"
                                                data-dropdown-parent="#kt_modal_entidade_financeira"
                                                data-placeholder="Selecione um banco">
                                                <option></option>
                                                @isset($banks)
                                                    @foreach ($banks as $bank)
                                                        <option value="{{ $bank->id }}"
                                                            data-icon="{{ $bank->logo_url }}">
                                                            {{ $bank->name }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            @error('bank_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Agência, Conta e Natureza da Conta -->
                                    <div class="row mb-5">
                                        <div class="col-md-4 fv-row d-none" id="agencia-group">
                                            <label class="fs-5 fw-semibold mb-2 required">Agência</label>
                                            <input type="text" class="form-control"
                                                placeholder="Número da agência" name="agencia" id="agencia-input"
                                                value="{{ old('agencia') }}" />
                                            @error('agencia')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 fv-row d-none" id="conta-group">
                                            <label class="fs-5 fw-semibold mb-2 required">Conta</label>
                                            <input type="text" class="form-control" placeholder="Número da conta"
                                                name="conta" id="conta-input" value="{{ old('conta') }}" />
                                            @error('conta')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 fv-row d-none" id="account-type-group">
                                            <label class="fs-5 fw-semibold mb-2 required">Natureza da Conta</label>
                                            <select name="account_type" id="account_type" data-control="select2"
                                                data-placeholder="Selecione a natureza da conta" class="form-select">
                                                <option value="corrente"
                                                    {{ old('account_type') == 'corrente' ? 'selected' : '' }}>
                                                    Conta Corrente</option>
                                                <option value="poupanca"
                                                    {{ old('account_type') == 'poupanca' ? 'selected' : '' }}>
                                                    Poupança</option>
                                                <option value="aplicacao"
                                                    {{ old('account_type') == 'aplicacao' ? 'selected' : '' }}>
                                                    Aplicação</option>
                                                <option value="renda_fixa"
                                                    {{ old('account_type') == 'renda_fixa' ? 'selected' : '' }}>
                                                    Renda Fixa</option>
                                                <option value="tesouro_direto"
                                                    {{ old('account_type') == 'tesouro_direto' ? 'selected' : '' }}>
                                                    Tesouro Direto</option>
                                            </select>
                                            @error('account_type')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                                <!--end::Body-->
                                <!--begin::Footer-->
                                <div class="border-top d-flex px-6 py-3">
                                    <button type="button" class="btn btn-sm btn-primary" id="btn-accordion-2-next"
                                        disabled data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_3"
                                        aria-expanded="false" aria-controls="kt_accordion_1_body_3">Próximo</button>
                                </div>
                                <!--end::Footer-->
                            </div>
                        </div>
                        <!--end::Item 2-->

                        <!--begin::Item 3-->
                        <div class="accordion-item mb-4">
                            <h2 class="accordion-header" id="kt_accordion_1_header_3">
                                <button class="accordion-button fs-4 fw-semibold collapsed" type="button" disabled
                                    data-bs-toggle="collapse" data-bs-target="#kt_accordion_1_body_3"
                                    aria-expanded="false" aria-controls="kt_accordion_1_body_3">
                                    <i class="fa-regular fa-circle-check fs-3 me-3" id="icon-accordion-3"></i>

                                    Saldo e Descrição
                                </button>
                            </h2>
                            <div id="kt_accordion_1_body_3" class="accordion-collapse collapse"
                                aria-labelledby="kt_accordion_1_header_3" data-bs-parent="#kt_accordion_principal">
                                <div class="accordion-body">


                                    <!--begin::Linha Saldo Inicial / Saldo Atual-->
                                    <div class="row mb-5">
                                        <!--begin::Col-->
                                        <div class="col-md-4 fv-row">
                                            <!--begin::Label-->
                                            <label class="fs-5 fw-semibold mb-2 required">Saldo do dia Anterior</label>
                                            <!--end::Label-->
                                            <div class="position-relative d-flex align-items-center">
                                                <!--end::Icon-->
                                                <!--begin::Input group-->
                                                <div class="input-group mb-5">
                                                    <span class="input-group-text" id="basic-addon1">R$</span>
                                                    <input type="text" class="form-control"
                                                        placeholder="Ex: 1.000,00" aria-label="Username"
                                                        id="saldo_atual_input" name="saldo_atual"
                                                        aria-describedby="basic-addon1" />
                                                </div>
                                                <!--end::Input group-->
                                                @error('saldo_atual')
                                                    <div class="text-danger mt-2">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!--end::Col-->
                                        <!--begin::Col-->
                                        <div class="col-md-8 fv-row">
                                            <label class="fs-5 fw-semibold mb-2">Conta Contábil (Plano de
                                                Contas)</label>
                                            <select class="form-select form-select-solid" data-control="select2"
                                                data-dropdown-parent="#kt_modal_entidade_financeira"
                                                data-placeholder="Selecione a conta contábil..."
                                                name="conta_contabil_id" id="conta_contabil_id">
                                                <option></option>
                                                @isset($contas)
                                                    @foreach ($contas as $conta)
                                                        <option value="{{ $conta->id }}"
                                                            {{ old('conta_contabil_id') == $conta->id ? 'selected' : '' }}>
                                                            {{ $conta->code }} - {{ $conta->name }}
                                                        </option>
                                                    @endforeach
                                                @endisset
                                            </select>
                                            <div class="text-muted fs-7 mt-2">Vínculo contábil para exportação
                                                (De/Para)
                                            </div>
                                            @error('conta_contabil_id')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <!--end::Col-->
                                    </div>
                                    <!--end::Linha Saldo-->


                                    <!--begin::Descrição-->
                                    <div class="d-flex flex-column mb-5 fv-row">
                                        <label class="fs-5 fw-semibold mb-2">Descrição</label>
                                        <textarea class="form-control form-control-solid" rows="4" name="descricao"
                                            placeholder="Insira uma descrição (opcional)">{{ old('descricao') }}</textarea>
                                        @error('descricao')
                                            <div class="text-danger mt-2">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <!--end::Descrição-->

                                    <!--begin::Conta Contábil-->
                                    <div class="d-flex flex-column mb-5 fv-row">

                                    </div>
                                    <!--end::Conta Contábil-->
                                </div>
                                <!--begin::Footer-->
                                <div class="border-top d-flex px-6 py-3">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse"
                                        data-bs-target="#kt_accordion_1_body_2" aria-expanded="false"
                                        aria-controls="kt_accordion_1_body_2">Voltar</button>
                                </div>
                                <!--end::Footer-->
                            </div>
                        </div>
                        <!--end::Item 3-->
                    </div>
                    <!--end::Accordion - Container principal-->

                </div>
                <!--end::Modal body-->

                <!--begin::Actions-->
                <div class="modal-footer flex-center">
                    <button type="reset" data-bs-dismiss="modal" class="btn btn-sm btn-light me-3">
                        <i class="bi bi-x-lg me-2"></i> Cancelar
                    </button>
                    <button type="submit" id="kt_modal_entidade_financeira_submit" class="btn btn-sm btn-primary">
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
<!--end::Modal - Cadastro de Entidade Financeira-->

<!--begin::Script para lógica do formulário-->
<script>
    /**
     * Classe para gerenciar o formulário de Entidade Financeira
     * Utiliza padrão de classe ES6 com métodos organizados
     */
    class EntidadeFinanceiraForm {
        constructor() {
            // Debug flag
            this.debug = false;

            // Elementos do DOM
            this.modal = document.getElementById('kt_modal_entidade_financeira');
            this.form = document.getElementById('kt_modal_entidade_financeira_form');
            this.submitButton = document.getElementById('kt_modal_entidade_financeira_submit');

            // Grupos de campos
            this.elements = {
                // Radio buttons
                bancoPlan: document.querySelector('input[name="tipo_entidade"][value="banco"]'),
                caixaPlan: document.querySelector('input[name="tipo_entidade"][value="caixa"]'),

                // Grupos de campos
                nomeEntidadeGroup: document.getElementById('nome-entidade-group'),
                bancoGroup: document.getElementById('banco-group'),
                agenciaGroup: document.getElementById('agencia-group'),
                contaGroup: document.getElementById('conta-group'),
                accountTypeGroup: document.getElementById('account-type-group'),

                // Inputs
                nomeInput: document.querySelector('input[name="nome"]'),
                nomeBancoInput: document.getElementById('nome_banco'),
                bancoSelect: document.getElementById('banco-select'),
                agenciaInput: document.getElementById('agencia-input'),
                contaInput: document.getElementById('conta-input'),
                accountTypeSelect: document.getElementById('account_type'),
                saldoInput: document.getElementById('saldo_atual_input'),
                contaContabilSelect: document.getElementById('conta_contabil_id'),

                // Botões de navegação
                accordion1NextButton: document.getElementById('btn-accordion-1-next'),
                accordion2NextButton: document.getElementById('btn-accordion-2-next'),
                accordion2Button: document.querySelector('button[aria-controls="kt_accordion_1_body_2"]'),
                accordion3Button: document.querySelector('button[aria-controls="kt_accordion_1_body_3"]'),

                // Ícones
                iconAccordion2: document.getElementById('icon-accordion-2'),
                iconAccordion3: document.getElementById('icon-accordion-3')
            };

            // Estado do formulário
            this.isSubmitting = false;

            // Inicializar
            this.init();
        }

        /**
         * Log debug condicional
         */
        log(...args) {
            if (this.debug) {
                console.log('EntidadeFinanceiraForm:', ...args);
            }
        }

        /**
         * Inicializa todos os event listeners e configurações
         */
        init() {
            if (!this.form || !this.modal) {
                console.warn('EntidadeFinanceiraForm: Elementos não encontrados');
                return;
            }

            this.log('Inicializando...');
            this.log('Form encontrado:', this.form);
            this.log('Submit button encontrado:', this.submitButton);

            this.bindEvents();
            this.initModalEvents();
            this.toggleFields();

            this.log('Inicializado com sucesso!');
        }

        /**
         * Vincula todos os eventos aos elementos
         */
        bindEvents() {
            // Eventos dos radio buttons
            document.querySelectorAll('input[name="tipo_entidade"]').forEach(radio => {
                radio.addEventListener('change', () => this.toggleFields());
            });

            // Evento do botão próximo do accordion 1
            this.elements.accordion1NextButton?.addEventListener('click', (e) => {
                const selected = this.getSelectedType();
                if (!selected) {
                    e.preventDefault();
                    e.stopPropagation();
                    this.showError('Por favor, selecione um tipo de entidade (Banco ou Caixa)');
                    return;
                }
                // Se selecionado, pode prosseguir
                this.enableAccordion2();
            });

            // Evento do botão próximo do accordion 2
            this.elements.accordion2NextButton?.addEventListener('click', (e) => {
                if (!this.validateAccordion2()) {
                    e.preventDefault();
                    e.stopPropagation();
                } else {
                    // Se válido, habilitar accordion 3
                    this.enableAccordion3();
                }
            });

            // Eventos de validação em tempo real para accordion 2
            this.elements.nomeInput?.addEventListener('input', () => this.checkAccordion2Fields());
            this.elements.nomeBancoInput?.addEventListener('input', () => this.checkAccordion2Fields());
            this.elements.agenciaInput?.addEventListener('input', () => this.checkAccordion2Fields());
            this.elements.contaInput?.addEventListener('input', () => this.checkAccordion2Fields());

            // Eventos de validação em tempo real para accordion 3
            this.elements.saldoInput?.addEventListener('input', () => this.validateAccordion3());

            // Evento de submit do formulário
            this.form.addEventListener('submit', this.handleSubmit.bind(this));

            // Click no botão de submit
            this.submitButton?.addEventListener('click', (e) => {
                this.log('Botão submit clicado');
            });

            this.log('Eventos vinculados');
        }

        /**
         * Inicializa eventos do modal Bootstrap
         */
        initModalEvents() {
            this.modal.addEventListener('shown.bs.modal', () => {
                this.initSelect2();
                this.initMoneyMask();
                this.toggleFields();
            });

            this.modal.addEventListener('hidden.bs.modal', () => {
                this.resetForm();
            });
        }

        /**
         * Retorna o tipo selecionado (banco ou caixa)
         */
        getSelectedType() {
            const checked = document.querySelector('input[name="tipo_entidade"]:checked');
            return checked?.value || null;
        }

        /**
         * Alterna visibilidade dos campos baseado no tipo selecionado
         */
        toggleFields() {
            const selected = this.getSelectedType();

            if (!selected) {
                // Se nada selecionado, desabilitar botão próximo
                this.elements.accordion1NextButton?.setAttribute('disabled', '');
                return;
            }

            // Habilitar apenas o botão do accordion 1
            this.elements.accordion1NextButton?.removeAttribute('disabled');

            const isBanco = selected === 'banco';

            // Toggle grupos de campos com disabled/enabled
            this.toggleFieldGroup(this.elements.nomeEntidadeGroup, !isBanco, ['nome']);
            this.toggleFieldGroup(this.elements.bancoGroup, isBanco, ['nome_banco', 'bank_id']);
            this.toggleFieldGroup(this.elements.agenciaGroup, isBanco, ['agencia']);
            this.toggleFieldGroup(this.elements.contaGroup, isBanco, ['conta']);
            this.toggleFieldGroup(this.elements.accountTypeGroup, isBanco, ['account_type']);
        }

        /**
         * Toggle um grupo de campos (show/hide + disabled/enabled + required)
         */
        toggleFieldGroup(groupElement, show, fieldNames) {
            if (!groupElement) return;

            // Mostrar/esconder grupo
            groupElement.classList.toggle('d-none', !show);

            // Encontrar todos os inputs/selects no grupo
            const fields = fieldNames.map(name =>
                groupElement.querySelector(`[name="${name}"]`) ||
                document.getElementById(name) ||
                document.querySelector(`[name="${name}"]`) // Busca global se não encontrar no grupo
            ).filter(Boolean);

            fields.forEach(field => {
                if (show) {
                    // Mostrar: habilitar e marcar como obrigatório
                    field.disabled = false;
                    if (field.hasAttribute('data-was-required') || field.hasAttribute('required')) {
                        field.required = true;
                        field.setAttribute('data-was-required', 'true');
                    }
                } else {
                    // Esconder: desabilitar, limpar valor e remover required
                    field.disabled = true;
                    field.required = false;
                    field.value = '';
                    
                    // Limpar atributo name para não ser enviado no form (opcional, mas mais seguro)
                    field.setAttribute('data-original-name', field.name);
                    
                    // Se é Select2, limpar também
                    if (field.classList.contains('form-select') && typeof $ !== 'undefined' && $.fn.select2) {
                        $(field).val(null).trigger('change');
                    }

                    // Limpar erros
                    this.clearFieldError(field);
                }
            });
        }

        /**
         * Habilita o accordion 2 quando accordion 1 é validado
         */
        enableAccordion2() {
            this.elements.accordion2Button?.removeAttribute('disabled');
            this.elements.iconAccordion2?.classList.add('text-success');

            this.log('Accordion 2 habilitado');
        }

        /**
         * Habilita o accordion 3 quando accordion 2 é validado
         */
        enableAccordion3() {
            this.elements.accordion3Button?.removeAttribute('disabled');
            this.elements.iconAccordion3?.classList.add('text-success');

            this.log('Accordion 3 habilitado');
        }

        /**
         * Retorna regras de validação para Banco
         */
        getBancoValidations() {
            return [
                {
                    element: this.elements.bancoSelect,
                    message: 'Por favor, selecione um Banco'
                },
                {
                    element: this.elements.agenciaInput,
                    message: 'Por favor, preencha a Agência'
                },
                {
                    element: this.elements.contaInput,
                    message: 'Por favor, preencha a Conta'
                },
                {
                    element: this.elements.accountTypeSelect,
                    message: 'Por favor, selecione a Natureza da Conta'
                }
                // Removido: nomeBancoInput não é mais obrigatório
            ];
        }

        /**
         * Retorna regras de validação para Caixa
         */
        getCaixaValidations() {
            return [{
                element: this.elements.nomeInput,
                message: 'Por favor, preencha o Nome da Entidade'
            }];
        }

        /**
         * Valida campos do accordion 2
         */
        validateAccordion2() {
            const selected = this.getSelectedType();
            const validations = selected === 'banco' ?
                this.getBancoValidations() :
                this.getCaixaValidations();

            // Limpar erros anteriores do accordion 2
            validations.forEach(({
                element
            }) => {
                this.clearFieldError(element);
            });

            let hasError = false;
            let firstErrorElement = null;

            for (const {
                    element,
                    message
                }
                of validations) {

                if (!this.isFieldValid(element)) {
                    this.showFieldError(element, message);
                    if (!firstErrorElement) {
                        firstErrorElement = element;
                    }
                    hasError = true;
                }
            }

            if (hasError) {
                firstErrorElement?.focus();
                return false;
            }

            return true;
        }

        /**
         * Verifica se um campo está válido (funciona com selects normais e Select2)
         */
        isFieldValid(element) {
            if (!element) return false;

            // Para selects com Select2
            if (element.tagName === 'SELECT') {
                // Se usar jQuery/Select2, verificar valor via jQuery
                if (typeof $ !== 'undefined' && $(element).hasClass('select2-hidden-accessible')) {
                    const select2Value = $(element).val();
                    return select2Value && select2Value !== '' && select2Value !== null;
                }

                // Select normal
                return element.value && element.value.trim() !== '';
            }

            // Para inputs normais
            return element.value && element.value.trim() !== '';
        }

        /**
         * Verifica se campos do accordion 2 estão preenchidos e habilita próximo
         */
        checkAccordion2Fields() {
            const selected = this.getSelectedType();
            if (!selected) return;

            let allFieldsFilled = false;

            if (selected === 'banco') {
                // Para banco: verificar bank_id, agencia, conta, account_type (nome_banco é opcional)
                allFieldsFilled =
                    this.isFieldValid(this.elements.bancoSelect) &&
                    this.isFieldValid(this.elements.agenciaInput) &&
                    this.isFieldValid(this.elements.contaInput) &&
                    this.isFieldValid(this.elements.accountTypeSelect);
            } else if (selected === 'caixa') {
                // Para caixa: verificar apenas nome
                allFieldsFilled = this.isFieldValid(this.elements.nomeInput);
            }

            // Habilitar ou desabilitar botão próximo do accordion 2
            if (allFieldsFilled) {
                this.elements.accordion2NextButton?.removeAttribute('disabled');
            } else {
                this.elements.accordion2NextButton?.setAttribute('disabled', '');
            }
        }

        /**
         * Valida e libera o accordion 3
         */
        validateAccordion3() {
            const selected = this.getSelectedType();
            let isValid = false;

            if (selected === 'banco' || selected === 'caixa') {
                isValid = !!this.elements.saldoInput?.value?.trim();
            }

            if (isValid) {
                this.elements.accordion3Button?.removeAttribute('disabled');
                this.elements.iconAccordion3?.classList.add('text-success');
            } else {
                this.elements.accordion3Button?.setAttribute('disabled', '');
                this.elements.iconAccordion3?.classList.remove('text-success');
            }

            return isValid;
        }

        /**
         * Valida todo o formulário antes do submit
         */
        validateForm() {
            const selected = this.getSelectedType();

            // Limpar erros anteriores
            this.clearAllFieldErrors();

            if (!selected) {
                this.showError('Por favor, selecione o tipo de entidade (Banco ou Caixa)');
                return false;
            }

            // Validar accordion 2
            if (!this.validateAccordion2()) {
                return false;
            }

            // Validar saldo
            if (!this.isFieldValid(this.elements.saldoInput)) {
                this.showFieldError(this.elements.saldoInput, 'Por favor, preencha o Saldo do dia Anterior');
                this.elements.saldoInput?.focus();
                return false;
            }

            return true;
        }

        /**
         * Manipula o submit do formulário
         */
        async handleSubmit(e) {
            console.log('handleSubmit chamado');
            e.preventDefault();
            e.stopPropagation();

            if (this.isSubmitting) {
                console.log('Já está submetendo, ignorando...');
                return;
            }

            console.log('Validando formulário...');

            // Validar formulário
            if (!this.validateForm()) {
                console.log('Validação falhou');
                return;
            }

            console.log('Validação OK, enviando...');

            this.isSubmitting = true;
            this.setLoading(true);

            try {
                console.log('Enviando requisição...');

                const response = await this.sendRequest();
                console.log('Resposta:', response);

                this.handleSuccess(response);
            } catch (error) {
                console.error('Erro no envio:', error);
                this.handleError(error);
            } finally {
                this.isSubmitting = false;
                this.setLoading(false);
            }
        }

        /**
         * Envia requisição AJAX
         */
        async sendRequest() {
            const originalFormData = new FormData(this.form);
            const mappedFormData = new FormData();
            
            // Adicionar CSRF token
            const csrfToken = this.form.querySelector('input[name="_token"]')?.value;
            if (csrfToken) {
                mappedFormData.append('_token', csrfToken);
            }
            
            // Mapear tipo_entidade -> tipo
            const tipoEntidade = originalFormData.get('tipo_entidade');
            if (tipoEntidade) {
                mappedFormData.append('tipo', tipoEntidade);
            }
            
            // Mapear saldo_atual -> saldo_inicial (apenas uma vez)
            const saldoAtual = originalFormData.get('saldo_atual');
            if (saldoAtual && saldoAtual.trim()) {
                mappedFormData.append('saldo_inicial', saldoAtual);
            }
            
            // Adicionar campos específicos baseado no tipo selecionado
            if (tipoEntidade === 'banco') {
                // Para banco: enviar dados bancários (o backend vai gerar o nome automaticamente)
                const nomeBanco = originalFormData.get('nome_banco');  // Apelido da conta (não usado pelo backend)
                const bankId = originalFormData.get('bank_id');
                const agencia = originalFormData.get('agencia');
                const conta = originalFormData.get('conta');
                const accountType = originalFormData.get('account_type');
                
                // Para banco, o backend ignora o campo 'nome' e gera automaticamente
                // baseado em: {banco} - {tipo_conta} - Ag. {agencia} C/C {conta}
                if (bankId) mappedFormData.append('bank_id', bankId);
                if (agencia && agencia.trim()) mappedFormData.append('agencia', agencia);
                if (conta && conta.trim()) mappedFormData.append('conta', conta);
                if (accountType) mappedFormData.append('account_type', accountType);
                
                // Comentário: O backend vai ignorar qualquer 'nome' enviado para bancos
                // e vai criar: "Banco do Brasil S.A - Poupança - Ag. 1234 C/C 5678"
                
            } else if (tipoEntidade === 'caixa') {
                // Para caixa: enviar apenas nome (sem dados bancários)
                const nomeEntidade = originalFormData.get('nome');
                if (nomeEntidade && nomeEntidade.trim()) {
                    mappedFormData.append('nome', nomeEntidade);
                }
                // NÃO enviar campos bancários para caixa
            }
            
            // Adicionar campos opcionais apenas se preenchidos
            const contaContabilId = originalFormData.get('conta_contabil_id');
            const descricao = originalFormData.get('descricao');
            
            if (contaContabilId && contaContabilId.trim()) {
                mappedFormData.append('conta_contabil_id', contaContabilId);
            }
            if (descricao && descricao.trim()) {
                mappedFormData.append('descricao', descricao);
            }

            // Debug: Ver o que está sendo enviado
            console.log('Dados enviados para o servidor:');
            for (let [key, value] of mappedFormData.entries()) {
                console.log(`${key}: ${value}`);
            }

            const response = await fetch(this.form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: mappedFormData
            });

            // Verificar content-type da resposta
            const contentType = response.headers.get('content-type') || '';
            let payload;

            if (contentType.includes('application/json')) {
                payload = await response.json();
            } else {
                // Se não for JSON, tratar como erro HTML do servidor
                const text = await response.text();
                payload = {
                    message: 'Erro no servidor',
                    error: text
                };
            }

            if (!response.ok) {
                throw payload;
            }

            return payload;
        }

        /**
         * Manipula erros com mapeamento melhorado
         */
        handleError(error) {
            this.log('Erro ao salvar:', error);

            // Limpar erros anteriores
            this.clearAllFieldErrors();

            // Se houver erros de validação do servidor, mostrar inline
            if (error.errors) {
                const fieldMapping = {
                    'tipo': document.querySelector('input[name="tipo_entidade"]'),
                    'nome': this.getSelectedType() === 'banco' ? this.elements.nomeBancoInput : this.elements.nomeInput,
                    'nome_banco': this.elements.nomeBancoInput,
                    'bank_id': this.elements.bancoSelect,
                    'agencia': this.elements.agenciaInput,
                    'conta': this.elements.contaInput,
                    'account_type': this.elements.accountTypeSelect,
                    'saldo_inicial': this.elements.saldoInput,
                    'saldo_atual': this.elements.saldoInput,
                    'conta_contabil_id': this.elements.contaContabilSelect,
                    'tipo_entidade': document.querySelector('input[name="tipo_entidade"]')
                };

                let firstErrorElement = null;

                for (const [field, messages] of Object.entries(error.errors)) {
                    const element = fieldMapping[field];
                    const message = Array.isArray(messages) ? messages[0] : messages;

                    if (element) {
                        this.showFieldError(element, message);
                        if (!firstErrorElement) {
                            firstErrorElement = element;
                        }
                    }
                }

                // Focar no primeiro campo com erro
                firstErrorElement?.focus();

                // Mostrar toast resumido
                this.showError('Por favor, corrija os erros no formulário');
                return;
            }

            // Erro genérico
            let message = 'Erro ao cadastrar entidade. Tente novamente.';

            if (error.message) {
                message = error.message;
            }

            this.showError(message);
        }

        /**
         * Manipula resposta de sucesso
         */
        handleSuccess(response) {
            // Fechar modal usando getOrCreateInstance
            const modalInstance = bootstrap.Modal.getOrCreateInstance(this.modal);
            modalInstance?.hide();

            // Notificar sucesso
            this.showSuccess(response.message || 'Entidade Financeira cadastrada com sucesso!');

            // Recarregar página após sucesso (opcional)
            setTimeout(() => location.reload(), 1500);
        }

        /**
         * Alterna estado de loading do botão
         */
        setLoading(loading) {
            if (!this.submitButton) return;

            this.submitButton.disabled = loading;

            const label = this.submitButton.querySelector('.indicator-label');
            const progress = this.submitButton.querySelector('.indicator-progress');

            label?.classList.toggle('d-none', loading);
            progress?.classList.toggle('d-none', !loading);
        }

        /**
         * Exibe mensagem de erro
         */
        showError(message) {
            // Usar Toastr se disponível, senão usar alert
            if (typeof toastr !== 'undefined') {
                toastr.error(message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Atenção',
                    text: message
                });
            } else {
                alert(message);
            }
        }

        /**
         * Exibe mensagem de sucesso
         */
        showSuccess(message) {
            if (typeof toastr !== 'undefined') {
                toastr.success(message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: message,
                    timer: 1500,
                    showConfirmButton: false
                });
            } else {
                alert(message);
            }
        }

        /**
         * Exibe mensagem de erro inline no campo
         */
        showFieldError(element, message) {
            if (!element) return;

            // Adicionar classe de erro ao input/select
            element.classList.add('is-invalid');

            // Encontrar o container pai (fv-row)
            const container = element.closest('.fv-row') || element.parentElement;

            // Verificar se já existe uma mensagem de erro
            let errorDiv = container.querySelector('.invalid-feedback');

            if (!errorDiv) {
                // Criar div de erro
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';

                // Se for Select2, inserir após o container do Select2
                const select2Container = container.querySelector('.select2-container');
                if (select2Container) {
                    select2Container.insertAdjacentElement('afterend', errorDiv);
                    // Adicionar borda vermelha ao Select2
                    select2Container.querySelector('.select2-selection')?.classList.add('border-danger');
                } else {
                    element.insertAdjacentElement('afterend', errorDiv);
                }
            }

            errorDiv.textContent = message;

            // Adicionar evento para limpar erro quando o usuário digitar
            const clearError = () => {
                this.clearFieldError(element);
                element.removeEventListener('input', clearError);
                element.removeEventListener('change', clearError);
            };

            element.addEventListener('input', clearError);
            element.addEventListener('change', clearError);
        }

        /**
         * Limpa erro de um campo específico
         */
        clearFieldError(element) {
            if (!element) return;

            element.classList.remove('is-invalid');

            const container = element.closest('.fv-row') || element.parentElement;
            const errorDiv = container.querySelector('.invalid-feedback');

            if (errorDiv) {
                errorDiv.remove();
            }

            // Limpar borda do Select2
            const select2Container = container.querySelector('.select2-container');
            if (select2Container) {
                select2Container.querySelector('.select2-selection')?.classList.remove('border-danger');
            }
        }

        /**
         * Limpa todos os erros de campos
         */
        clearAllFieldErrors() {
            // Remover classe is-invalid de todos os campos
            this.form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });

            // Remover todas as mensagens de erro
            this.form.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });

            // Limpar bordas dos Select2
            this.form.querySelectorAll('.select2-selection.border-danger').forEach(el => {
                el.classList.remove('border-danger');
            });
        }

        /**
         * Inicializa Select2 do banco
         */
        initSelect2() {
            if (typeof $ === 'undefined' || !$.fn.select2) return;

            const $bancoSelect = $('#banco-select');
            const $contaContabilSelect = $('#conta_contabil_id');

            // Inicializar Select2 do banco se ainda não foi inicializado
            if (!$bancoSelect.hasClass('select2-hidden-accessible')) {
                $bancoSelect.select2({
                    dropdownParent: $(this.modal),
                    placeholder: 'Selecione um banco',
                    allowClear: true,
                    templateResult: this.formatBankOption,
                    templateSelection: this.formatBankOption
                });
            }

            // Inicializar Select2 da conta contábil se ainda não foi inicializado
            if (!$contaContabilSelect.hasClass('select2-hidden-accessible')) {
                $contaContabilSelect.select2({
                    dropdownParent: $(this.modal),
                    placeholder: 'Selecione a conta contábil...',
                    allowClear: true
                });
            }

            // Adicionar eventos change para limpar erros quando valor for selecionado
            $bancoSelect.off('change.validation').on('change.validation', () => {
                this.clearFieldError(this.elements.bancoSelect);
                this.checkAccordion2Fields(); // Verificar se pode habilitar próximo
            });

            $contaContabilSelect.off('change.validation').on('change.validation', () => {
                this.clearFieldError(this.elements.contaContabilSelect);
            });

            // Também adicionar evento no select de natureza da conta
            const $accountTypeSelect = $('#account_type');
            if ($accountTypeSelect.length && !$accountTypeSelect.hasClass('select2-hidden-accessible')) {
                $accountTypeSelect.select2({
                    dropdownParent: $(this.modal),
                    placeholder: 'Selecione a natureza da conta',
                    allowClear: true
                });
            }

            $accountTypeSelect.off('change.validation').on('change.validation', () => {
                this.clearFieldError(this.elements.accountTypeSelect);
                this.checkAccordion2Fields(); // Verificar se pode habilitar próximo
            });
        }

        /**
         * Formata opção do banco com ícone
         */
        formatBankOption(state) {
            if (!state.id) return state.text;

            const iconUrl = $(state.element).attr('data-icon');
            if (!iconUrl) return state.text;

            return $(`
                <span class="d-flex align-items-center">
                    <img src="${iconUrl}" class="me-2" style="width:24px; height:24px;" />
                    <span>${state.text}</span>
                </span>
            `);
        }

        /**
         * Inicializa máscara de dinheiro
         */
        initMoneyMask() {
            if (typeof Inputmask === 'undefined') return;

            const saldoInput = this.elements.saldoInput;
            if (!saldoInput) return;

            Inputmask('decimal', {
                alias: 'numeric',
                groupSeparator: '.',
                radixPoint: ',',
                digits: 2,
                autoGroup: true,
                rightAlign: false,
                allowMinus: true,
                oncleared: function() {
                    this.value = '';
                }
            }).mask(saldoInput);
        }

        /**
         * Reseta o formulário para o estado inicial
         */
        resetForm() {
            this.form?.reset();

            // Resetar Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#banco-select').val(null).trigger('change');
                $('#conta_contabil_id').val(null).trigger('change');
            }

            // Limpar todos os erros
            this.clearAllFieldErrors();

            // Resetar estados visuais dos accordions
            this.elements.accordion1NextButton?.setAttribute('disabled', '');
            this.elements.accordion2NextButton?.setAttribute('disabled', '');
            this.elements.accordion2Button?.setAttribute('disabled', '');
            this.elements.accordion3Button?.setAttribute('disabled', '');

            this.elements.iconAccordion2?.classList.remove('text-success');
            this.elements.iconAccordion3?.classList.remove('text-success');

            // Reabilitar todos os campos para o reset
            this.form.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = false;
                field.required = field.hasAttribute('data-was-required');
            });

            // Chamar toggleFields para aplicar estado correto baseado no radio selecionado
            this.toggleFields();

            // Abrir primeiro accordion usando getOrCreateInstance
            const firstAccordion = document.getElementById('kt_accordion_1_body_1');
            if (firstAccordion && typeof bootstrap !== 'undefined') {
                const collapse = bootstrap.Collapse.getOrCreateInstance(firstAccordion, {
                    toggle: false
                });
                collapse.show();
            }

            this.log('Formulário resetado');
        }
    }

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM carregado, inicializando EntidadeFinanceiraForm...');
            window.entidadeFinanceiraForm = new EntidadeFinanceiraForm();
        });
    } else {
        console.log('DOM já carregado, inicializando EntidadeFinanceiraForm imediatamente...');
        window.entidadeFinanceiraForm = new EntidadeFinanceiraForm();
    }
</script>
<!--end::Script para lógica do formulário-->
