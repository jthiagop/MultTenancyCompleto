{{-- Drawer para criar lançamento a partir de documento Domusia --}}
@php
    $drawerId = 'domusia_expense_drawer';
    $formId = 'domusia_drawer_form';
    $cancelId = 'domusia_drawer_cancel';
    $submitId = 'domusia_drawer_submit';
    $submitNewId = 'domusia_drawer_submit_new';
    $closeId = 'domusia_drawer_close';

    $splitItems = [['id' => $submitNewId, 'text' => 'Salvar e Novo', 'icon' => 'fa-solid fa-plus']];
@endphp

<x-tenant-drawer drawerId="{{ $drawerId }}" width="100%" toggleButtonId="domusia_expense_drawer_toggle"
    closeButtonId="{{ $closeId }}" :showCloseButton="true" bodyClass="p-0 overflow-hidden"
    cardClass="shadow-none rounded-0 w-100">

    {{-- ========== HEADER CUSTOMIZADO ========== --}}
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            <span class="bullet bullet-vertical h-30px" id="domusia_drawer_type_indicator"
                style="background-color: #f1416c;"></span>
            <div>
                <span class="fw-bold fs-5" id="domusia_drawer_title">Nova Despesa</span>
                <span class="text-muted fs-7 d-block" id="domusia_drawer_subtitle">Preencha os dados do
                    lançamento</span>
            </div>
            <span class="badge badge-light-primary fs-8" id="domusia_drawer_doc_badge">
                <i class="fa-solid fa-file-invoice fs-8 me-1"></i>
                <span id="domusia_drawer_doc_type">Documento</span>
            </span>
        </div>
    </x-slot>

    {{-- ========== BODY ========== --}}
    <x-slot name="body">
        <div class="row g-0 h-100" style="min-height: calc(100vh - 130px);">

            <!--begin::Col Esquerda - Visualizador de Documento-->
            <div class="col-xl-4 border-end h-100 position-relative" style="min-height: 400px;">
                <div id="drawer_viewer_wrapper" class="domus-document-viewer-wrapper position-relative w-100 h-100">

                    {{-- Container do Viewer --}}
                    <div class="position-relative"
                        style="height: 100%; overflow: hidden; margin: 0; padding: 0 !important;">

                        {{-- Toolbar Flutuante --}}
                        <div class="domus-floating-toolbar" style="opacity: 1;">
                            <button type="button" class="domus-toolbar-btn btn-zoom-out" title="Diminuir Zoom (-)">
                                <i class="fa-solid fa-minus fs-6"></i>
                            </button>
                            <span class="domus-zoom-indicator zoom-indicator">100%</span>
                            <button type="button" class="domus-toolbar-btn btn-zoom-in" title="Aumentar Zoom (+)">
                                <i class="fa-solid fa-plus fs-6"></i>
                            </button>
                            <button type="button" class="domus-toolbar-btn btn-fit-zoom"
                                title="Ajustar ao Container (0)">
                                <i class="fa-solid fa-expand fs-6"></i>
                            </button>
                            <div class="domus-toolbar-divider"></div>
                            <button type="button" class="domus-toolbar-btn btn-rotate-left" title="Girar Esquerda">
                                <i class="fa-solid fa-rotate-left fs-6"></i>
                            </button>
                            <button type="button" class="domus-toolbar-btn btn-rotate-right" title="Girar Direita">
                                <i class="fa-solid fa-rotate-right fs-6"></i>
                            </button>
                        </div>

                        {{-- Empty State --}}
                        <div id="drawer_empty_state"
                            class="domus-empty-state d-flex flex-column align-items-center justify-content-center h-100 text-center p-10"
                            style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; background: #2d2d2d;">
                            <i class="fa-solid fa-file-circle-question fs-3x text-gray-500 mb-4"></i>
                            <p class="text-gray-500 fw-semibold mb-0">Nenhum documento carregado</p>
                        </div>

                        {{-- Viewer Container --}}
                        <div id="drawer_viewer_container" class="domus-viewer-container"
                            style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: 2; margin: 0; padding: 0;">
                            <iframe id="drawer_pdf_viewer" class="w-100 h-100 border-0 domus-viewer-pdf"
                                style="min-height: 400px; display: none; position: relative;" allowfullscreen></iframe>
                            <img id="drawer_image_viewer" class="domus-viewer-img" style="display: none;"
                                draggable="false" alt="Documento" />
                        </div>
                    </div>

                </div>
            </div>
            <!--end::Col Esquerda-->

            <!--begin::Col Direita - Formulário-->
            <div class="col-xl-8 h-100 overflow-y-auto bg-light">
                <form id="{{ $formId }}" method="POST" action="{{ route('transacoes-financeiras.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    {{-- Campos hidden --}}
                    <input type="hidden" name="tipo" id="domusia_tipo" value="saida">
                    <input type="hidden" name="origem" id="domusia_origem" value="Banco">
                    <input type="hidden" name="domus_documento_id" id="domusia_documento_id" value="">
                    <input type="hidden" name="novo_parceiro_nome" id="domusia_novo_parceiro_nome" value="">
                    <input type="hidden" name="novo_parceiro_cnpj" id="domusia_novo_parceiro_cnpj" value="">

                    <div class="p-6 pb-0">

                        {{-- Card: Informações Identificadas pela IA --}}
                        <div class="card border border-dashed border-primary mb-5" id="domusia_ai_summary_card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <i class="fa-solid fa-robot text-primary fs-4"></i>
                                    <span class="fw-bold text-primary fs-6">Dados identificados pela IA</span>
                                </div>
                                <div class="row g-3" id="domusia_ai_summary">
                                    <div class="col-6">
                                        <span class="text-muted fs-8 d-block">Fornecedor</span>
                                        <span class="fw-semibold fs-7" id="domusia_ai_fornecedor">-</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="text-muted fs-8 d-block">Valor Total</span>
                                        <span class="fw-bold fs-6 text-danger" id="domusia_ai_valor">-</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="text-muted fs-8 d-block">Data Emissão</span>
                                        <span class="fw-semibold fs-7" id="domusia_ai_data">-</span>
                                    </div>
                                    <div class="col-6">
                                        <span class="text-muted fs-8 d-block">CNPJ</span>
                                        <span class="fw-semibold fs-7 font-monospace" id="domusia_ai_cnpj">-</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="text-muted fs-8 d-block">Forma Pgto</span>
                                        <span class="fw-semibold fs-7" id="domusia_ai_pgto">-</span>
                                    </div>
                                    <div class="col-3">
                                        <span class="text-muted fs-8 d-block">Nº Documento</span>
                                        <span class="fw-semibold fs-7" id="domusia_ai_numdoc">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card: Informações do Lançamento --}}
                        <div class="card border border-gray-300 mb-5">
                            <div class="card-header min-h-45px">
                                <h3 class="card-title fs-6 fw-bold">Informações do Lançamento</h3>
                            </div>
                            <div class="card-body px-6 py-5">
                                {{-- Linha 1: Fornecedor/Cliente, Descrição --}}
                                <div class="row g-4 mb-5">
                                    <x-tenant-select name="fornecedor_id" id="domusia_fornecedor_id"
                                        label="Fornecedor" placeholder="Selecione um fornecedor" :minimumResultsForSearch="0"
                                        dropdown-parent="#{{ $drawerId }}" labelSize="fs-7" class="col-md-5">
                                        @if (isset($fornecedores))
                                            @foreach ($fornecedores as $fornecedor)
                                                <option value="{{ $fornecedor->id }}"
                                                    data-cnpj="{{ $fornecedor->cnpj }}"
                                                    data-cpf="{{ $fornecedor->cpf }}"
                                                    data-natureza="{{ $fornecedor->natureza }}">
                                                    {{ $fornecedor->nome }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>

                                    <x-tenant-input name="descricao" id="domusia_descricao" label="Descrição"
                                        placeholder="Informe a descrição" required class="col-md-4" />

                                    <x-tenant-currency name="valor" id="domusia_valor" label="Valor"
                                        placeholder="0,00" tooltip="Valor total do documento" class="col-md-3"
                                        required />
                                </div>

                                {{-- Linha 2: Data, Valor, Entidade Financeira, Categoria --}}
                                <div class="row g-4 mb-5">
                                    <x-tenant-date name="data_competencia" id="domusia_data_competencia"
                                        label="Data Competência" placeholder="Informe a data" required
                                        class="col-md-3" />

                                    <x-tenant-select name="entidade_id" id="domusia_entidade_id"
                                        label="Entidade Financeira" required :hideSearch="true"
                                        dropdown-parent="#{{ $drawerId }}" class="col-md-3">
                                        @if (isset($entidadesBanco) && $entidadesBanco->isNotEmpty())
                                            @foreach ($entidadesBanco as $entidade)
                                                <option value="{{ $entidade->id }}"
                                                    data-nome="{{ $entidade->nome }}" data-origem="Banco">
                                                    {{ $entidade->agencia }} - {{ $entidade->conta }}
                                                </option>
                                            @endforeach
                                        @endif
                                        @if (isset($entidadesCaixa) && $entidadesCaixa->isNotEmpty())
                                            @foreach ($entidadesCaixa as $entidade)
                                                <option value="{{ $entidade->id }}"
                                                    data-nome="{{ $entidade->nome }}" data-origem="Caixa">
                                                    {{ $entidade->nome }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>

                                    <x-tenant-select name="lancamento_padrao_id" id="domusia_lancamento_padrao_id"
                                        label="Categoria" placeholder="Escolha uma categoria..." required
                                        :allowClear="true" :minimumResultsForSearch="0" dropdown-parent="#{{ $drawerId }}"
                                        labelSize="fs-7" class="col-md-6" :showSuggestionStar="true">
                                        @if (isset($lps))
                                            @foreach ($lps as $lp)
                                                <option value="{{ $lp->id }}"
                                                    data-description="{{ $lp->description }}"
                                                    data-type="{{ $lp->type }}">
                                                    {{ $lp->id }} - {{ $lp->description }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>
                                </div>

                                {{-- Linha 3: Centro de Custo, Forma Pgto, Nº Documento, Comprovação --}}
                                <div class="row g-4">
                                    <x-tenant-select name="cost_center_id" id="domusia_cost_center_id"
                                        label="Centro de Custo" :allowClear="true" required
                                        placeholder="Selecione um centro de custo" :minimumResultsForSearch="0"
                                        dropdown-parent="#{{ $drawerId }}" labelSize="fs-7" class="col-md-3"
                                        :showSuggestionStar="true">
                                        @if (isset($centrosAtivos))
                                            @foreach ($centrosAtivos as $centro)
                                                <option value="{{ $centro->id }}">
                                                    {{ $centro->code }} - {{ $centro->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>

                                    <x-tenant-select name="tipo_documento" id="domusia_tipo_documento"
                                        label="Forma de Pagamento" placeholder="Selecione..." required
                                        :allowClear="true" :minimumResultsForSearch="0" dropdown-parent="#{{ $drawerId }}"
                                        labelSize="fs-7" class="col-md-5" :showSuggestionStar="true">
                                        @if (isset($formasPagamento))
                                            @foreach ($formasPagamento as $fp)
                                                <option value="{{ $fp->codigo }}" data-id="{{ $fp->id }}">
                                                    {{ $fp->id }} - {{ $fp->nome }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>

                                    <x-tenant-input name="numero_documento" id="domusia_numero_documento"
                                        label="Nº Documento" placeholder="Nº NF / Recibo" type="text"
                                        class="col-md-3" />

                                </div>
                            </div>
                        </div>

                        {{-- Card: Condição de Pagamento --}}
                        @include(
                            'app.components.modals.financeiro.lancamento.components.card-condicao-pagamento-simple',
                            [
                                'idPrefix' => 'domusia_',
                                'maxParcelas' => 24,
                                'showValoresExtras' => true,
                                'dropdownParent' => '#' . $drawerId,
                            ]
                        )

                        {{-- Card: Histórico Complementar --}}
                        @include(
                            'app.components.modals.financeiro.lancamento.components.card-historico-complementar',
                            [
                                'idPrefix' => 'domusia_',
                            ]
                        )

                    </div>
                </form>
            </div>
            <!--end::Col Direita-->

        </div>
    </x-slot>

    {{-- ========== FOOTER ========== --}}
    <x-slot name="footer">
        <div class="d-flex justify-content-between align-items-center w-100">
            {{-- Lado Esquerdo: Status + Cancelar --}}
            <div class="d-flex align-items-center gap-3">
                <x-tenant-button type="button" id="{{ $cancelId }}" variant="light" size="sm"
                    icon="fa-solid fa-xmark" iconPosition="left">
                    Cancelar
                </x-tenant-button>
                <span class="text-muted fs-8" id="domusia_drawer_status_text">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Preencha os campos obrigatórios para salvar
                </span>
            </div>

            {{-- Lado Direito: Salvar (Split Button) --}}
            <div class="d-flex">
                <x-tenant-split-button submitId="{{ $submitId }}" submitText="Salvar Lançamento"
                    submitIcon="fa-solid fa-check" variant="primary" size="sm" direction="dropup"
                    :items="$splitItems" />
            </div>
        </div>
    </x-slot>

</x-tenant-drawer>

@push('styles')
    <style>
        /* ======= Estilos específicos do Drawer Domusia ======= */
        #domusia_expense_drawer {
            z-index: 1060;
        }

        #domusia_expense_drawer .card-header {
            min-height: 50px;
            padding: 0.5rem 1rem;
        }

        /* Toolbar sempre visível dentro do drawer */
        #drawer_viewer_wrapper .domus-floating-toolbar {
            opacity: 1 !important;
        }

        .drawer-overlay[data-kt-drawer-name="domusia-expense"] {
            z-index: 1059;
        }

        /* Body do drawer sem padding e sem scroll (cada coluna gerencia o seu) */
        #domusia_expense_drawer_body {
            padding: 0 !important;
            overflow: hidden !important;
        }

        #domusia_expense_drawer_body .scroll-y {
            overflow: visible !important;
            height: 100% !important;
            max-height: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Footer alinhamento */
        #domusia_expense_drawer_footer {
            text-align: left !important;
            padding: 12px 20px !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ========================================================
            // DOMUSIA DRAWER - Controller Principal
            // ========================================================
            const DomusiaDrawer = {
                // Estado
                currentEntryIndex: null,
                currentEntryData: null,
                isReceita: false,
                isSubmitting: false,

                // Elementos
                el: {
                    drawer: document.getElementById('domusia_expense_drawer'),
                    form: document.getElementById('domusia_drawer_form'),
                    title: document.getElementById('domusia_drawer_title'),
                    subtitle: document.getElementById('domusia_drawer_subtitle'),
                    typeIndicator: document.getElementById('domusia_drawer_type_indicator'),
                    docBadge: document.getElementById('domusia_drawer_doc_badge'),
                    submitBtn: document.getElementById('domusia_drawer_submit'),
                    submitNewBtn: document.getElementById('domusia_drawer_submit_new'),
                    cancelBtn: document.getElementById('domusia_drawer_cancel'),
                    closeBtn: document.getElementById('domusia_drawer_close'),
                    statusText: document.getElementById('domusia_drawer_status_text'),
                    docType: document.getElementById('domusia_drawer_doc_type'),
                    // AI Summary
                    aiCard: document.getElementById('domusia_ai_summary_card'),
                    aiFornecedor: document.getElementById('domusia_ai_fornecedor'),
                    aiValor: document.getElementById('domusia_ai_valor'),
                    aiData: document.getElementById('domusia_ai_data'),
                    aiCnpj: document.getElementById('domusia_ai_cnpj'),
                    aiPgto: document.getElementById('domusia_ai_pgto'),
                    aiNumdoc: document.getElementById('domusia_ai_numdoc'),
                    // Form fields
                    tipo: document.getElementById('domusia_tipo'),
                    origem: document.getElementById('domusia_origem'),
                    documentoId: document.getElementById('domusia_documento_id'),
                    pagoCheckbox: document.getElementById('domusia_pago_checkbox'),
                    pagoLabel: document.getElementById('domusia_pago_label'),
                },

                // --------------------------------------------------------
                // Inicializar
                // --------------------------------------------------------
                init() {
                    if (typeof DomusDocumentViewer !== 'undefined') {
                        this.drawerViewer = new DomusDocumentViewer('drawer_viewer_wrapper');
                    }
                    this.bindEvents();
                    console.log('[DomusiaDrawer] Inicializado', this.drawerViewer ? 'com viewer' :
                    'SEM viewer');
                },

                // --------------------------------------------------------
                // Bind de eventos
                // --------------------------------------------------------
                bindEvents() {
                    if (this.el.cancelBtn) {
                        this.el.cancelBtn.addEventListener('click', () => this.close());
                    }
                    if (this.el.closeBtn) {
                        this.el.closeBtn.addEventListener('click', () => this.close());
                    }
                    if (this.el.submitBtn) {
                        this.el.submitBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.submit(false);
                        });
                    }
                    if (this.el.submitNewBtn) {
                        this.el.submitNewBtn.addEventListener('click', (e) => {
                            e.preventDefault();
                            this.submit(true);
                        });
                    }
                    if (this.el.pagoCheckbox) {
                        this.el.pagoCheckbox.addEventListener('change', () => {
                            this.toggleValoresExtras();
                            this.updateValorPago();
                        });
                    }

                    // Recalcular valor_pago quando juros/multa/desconto mudam
                    ['domusia_juros', 'domusia_multa', 'domusia_desconto'].forEach(id => {
                        const input = document.getElementById(id);
                        if (input) {
                            input.addEventListener('input', () => this.updateValorPago());
                            input.addEventListener('change', () => this.updateValorPago());
                        }
                    });

                    // Atualizar origem baseado na entidade selecionada
                    const entidadeSelect = document.getElementById('domusia_entidade_id');
                    if (entidadeSelect) {
                        $(entidadeSelect).on('change', function() {
                            const selected = $(this).find(':selected');
                            const origem = selected.data('origem') || 'Banco';
                            const origemInput = document.getElementById('domusia_origem');
                            if (origemInput) origemInput.value = origem;
                        });
                    }

                    // Parcelamento: gerar tabela de parcelas quando >= 2x
                    const parcelamentoSelect = document.getElementById('domusia_parcelamento');
                    if (parcelamentoSelect) {
                        $(parcelamentoSelect).on('change', () => {
                            const val = $(parcelamentoSelect).val();
                            this.onParcelamentoChange(val);
                        });
                    }

                    // Auto-regenerar parcelas quando valor ou descrição mudam
                    const valorInput = document.getElementById('domusia_valor');
                    if (valorInput) {
                        valorInput.addEventListener('change', () => this.tentarRegerarParcelas());
                    }
                    const descInput = document.getElementById('domusia_descricao');
                    if (descInput) {
                        descInput.addEventListener('change', () => this.tentarRegerarParcelas());
                    }
                },

                // --------------------------------------------------------
                // Abrir drawer com dados
                // --------------------------------------------------------
                open(entryIndex, isReceita) {
                    this.currentEntryIndex = entryIndex;
                    this.isReceita = isReceita;

                    const doc = window.currentDocument;
                    const extractedData = doc?.dados_extraidos ?
                        (typeof doc.dados_extraidos === 'string' ? JSON.parse(doc.dados_extraidos) : doc
                            .dados_extraidos) :
                        null;

                    const item = extractedData?.itens?.[entryIndex] || null;
                    this.currentEntryData = {
                        doc,
                        extractedData,
                        item
                    };

                    this.resetForm();
                    this.setupType(isReceita);
                    this.loadViewer(doc);
                    this.fillAISummary(extractedData);
                    this.prefillForm(extractedData, item, isReceita);
                    this.show();
                },

                // --------------------------------------------------------
                // Configurar tipo (despesa/receita)
                // --------------------------------------------------------
                setupType(isReceita) {
                    const tipo = isReceita ? 'entrada' : 'saida';
                    if (this.el.tipo) this.el.tipo.value = tipo;
                    if (this.el.title) this.el.title.textContent = isReceita ? 'Nova Receita' : 'Nova Despesa';
                    if (this.el.typeIndicator) this.el.typeIndicator.style.backgroundColor = isReceita ?
                        '#50cd89' : '#f1416c';
                    if (this.el.pagoLabel) this.el.pagoLabel.textContent = isReceita ? 'Recebido' : 'Pago';
                    if (this.el.pagoCheckbox) this.el.pagoCheckbox.name = isReceita ? 'recebido' : 'pago';

                    // Filtrar categorias pelo tipo (entrada/saida)
                    this.filterCategoriasByTipo(tipo);

                    // Filtrar e configurar select de parceiros (fornecedor/cliente)
                    this.setupParceiroSelect(isReceita);
                },

                // --------------------------------------------------------
                // Configurar select de parceiros (fornecedor/cliente)
                // --------------------------------------------------------
                setupParceiroSelect(isReceita) {
                    const select = document.getElementById('domusia_fornecedor_id');
                    if (!select) return;

                    const $select = $(select);
                    const self = this;

                    // Definir label e placeholder baseado no tipo
                    const naturezaFiltro = isReceita ? 'cliente' : 'fornecedor';
                    const label = isReceita ? 'Cliente' : 'Fornecedor';
                    const placeholder = isReceita ? 'Selecione um cliente' : 'Selecione um fornecedor';
                    const addButtonText = isReceita ? 'Adicionar Cliente' : 'Adicionar Fornecedor';

                    // Armazenar para uso posterior
                    this.currentNaturezaFiltro = naturezaFiltro;
                    this.currentAddButtonText = addButtonText;

                    // Atualizar label
                    const labelEl = document.querySelector('label[for="domusia_fornecedor_id"]');
                    if (labelEl) {
                        labelEl.textContent = label;
                    }

                    // Resetar seleção atual
                    $select.val(null).trigger('change.select2');

                    // Reinicializar Select2 com matcher para filtrar por natureza
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    $select.select2({
                        dropdownParent: $('#{{ $drawerId }}'),
                        placeholder: placeholder,
                        allowClear: true,
                        minimumResultsForSearch: 0,
                        width: '100%',
                        theme: 'bootstrap5',
                        matcher: function(params, data) {
                            if (!data.element) return null;

                            const natureza = $(data.element).data('natureza');

                            // Filtrar: mostrar apenas se natureza corresponde ou é 'ambos'
                            if (natureza && natureza !== self.currentNaturezaFiltro && natureza !==
                                'ambos') {
                                return null;
                            }

                            // Se não há termo de busca, retorna o item
                            if ($.trim(params.term) === '') {
                                return data;
                            }

                            // Busca padrão no texto
                            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                return data;
                            }

                            return null;
                        }
                    });

                    // Adicionar botão de cadastro no dropdown
                    $select.off('select2:open.domusia').on('select2:open.domusia', function() {
                        setTimeout(function() {
                            const $dropdown = $('.select2-container--open');
                            const $results = $dropdown.find('.select2-results');

                            if ($results.length === 0) return;

                            // Remove botão anterior se existir
                            $results.find('.select2-add-parceiro-footer').remove();

                            // Adiciona footer com botão
                            const $footer = $(
                                '<div class="select2-add-parceiro-footer border-top p-2 text-center"></div>'
                                );
                            const $button = $(
                                '<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus me-1"></i>' +
                                self.currentAddButtonText + '</button>');
                            $footer.append($button);
                            $results.append($footer);

                            // Evento de clique no botão
                            $button.on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();

                                // Fecha o Select2
                                $select.select2('close');

                                // Define qual select deve ser atualizado ao salvar
                                window.__drawerTargetSelect = '#domusia_fornecedor_id';

                                // Define o tipo no hidden field do drawer de fornecedor
                                const parceiroTipo = self.currentNaturezaFiltro;
                                $('#parceiro_natureza_hidden').val(parceiroTipo);

                                // Atualiza título do drawer
                                const drawerTitle = self.isReceita ? 'Novo Cliente' :
                                    'Novo Fornecedor';
                                $('#fornecedor_drawer_title').text(drawerTitle);

                                console.log('[DomusiaDrawer] Abrindo drawer para:',
                                    parceiroTipo);

                                // Abre o drawer de fornecedor
                                const drawerEl = document.getElementById(
                                    'kt_drawer_fornecedor');
                                if (drawerEl) {
                                    let drawer = KTDrawer.getInstance(drawerEl);
                                    if (!drawer) drawer = new KTDrawer(drawerEl);
                                    if (drawer && typeof drawer.show === 'function') {
                                        drawer.show();
                                    }
                                }
                            });
                        }, 50);
                    });

                    console.log('[DomusiaDrawer] Parceiros filtrados para natureza:', naturezaFiltro);
                },

                // --------------------------------------------------------
                // Filtrar opções de categoria pelo tipo
                // --------------------------------------------------------
                filterCategoriasByTipo(tipo) {
                    const select = document.getElementById('domusia_lancamento_padrao_id');
                    if (!select) return;

                    const $select = $(select);

                    // Armazenar o tipo atual para uso no matcher
                    this.currentTipoFilter = tipo;

                    // Resetar seleção atual
                    $select.val(null).trigger('change.select2');

                    // Reinicializar Select2 com matcher customizado
                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                    const self = this;
                    $select.select2({
                        dropdownParent: $('#{{ $drawerId }}'),
                        placeholder: 'Escolha uma categoria...',
                        allowClear: true,
                        minimumResultsForSearch: 0,
                        width: '100%',
                        theme: 'bootstrap5',
                        matcher: function(params, data) {
                            // Se não há termo de busca, apenas filtrar pelo tipo
                            if (!data.element) return null;

                            const optionType = $(data.element).data('type');

                            // Não exibir opções que não correspondem ao tipo
                            if (optionType && optionType !== self.currentTipoFilter) {
                                return null;
                            }

                            // Se não há termo de busca, retorna o item
                            if ($.trim(params.term) === '') {
                                return data;
                            }

                            // Busca padrão no texto
                            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                                return data;
                            }

                            return null;
                        }
                    });

                    console.log('[DomusiaDrawer] Categorias filtradas para tipo:', tipo);
                },

                // --------------------------------------------------------
                // Carregar documento no viewer do drawer
                // --------------------------------------------------------
                loadViewer(doc) {
                    if (!doc) return;

                    // Delegar resolução de URL inteiramente ao DomusDocumentViewer
                    if (this.drawerViewer) {
                        this.drawerViewer.load(doc);
                    }

                    if (this.el.docType) this.el.docType.textContent = doc.tipo_documento || 'Documento';
                },

                // --------------------------------------------------------
                // Preencher resumo IA
                // --------------------------------------------------------
                fillAISummary(data) {
                    if (!data) {
                        if (this.el.aiCard) this.el.aiCard.style.display = 'none';
                        return;
                    }
                    if (this.el.aiCard) this.el.aiCard.style.display = 'block';

                    const setText = (el, value) => {
                        if (el) el.textContent = value || '-';
                    };

                    setText(this.el.aiFornecedor, data.estabelecimento?.nome);
                    setText(this.el.aiCnpj, this.formatCNPJ(data.estabelecimento?.cnpj));
                    setText(this.el.aiPgto, data.financeiro?.forma_pagamento);
                    setText(this.el.aiNumdoc, data.financeiro?.numero_documento || data.nfe_info?.numero_nf);

                    if (this.el.aiValor && data.financeiro?.valor_total) {
                        const val = parseFloat(data.financeiro.valor_total);
                        this.el.aiValor.textContent = 'R$ ' + val.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    } else {
                        setText(this.el.aiValor, '-');
                    }

                    if (this.el.aiData && data.financeiro?.data_emissao) {
                        const parts = data.financeiro.data_emissao.split('-');
                        if (parts.length === 3) {
                            this.el.aiData.textContent = parts[2] + '/' + parts[1] + '/' + parts[0];
                        } else {
                            this.el.aiData.textContent = data.financeiro.data_emissao;
                        }
                    } else {
                        setText(this.el.aiData, '-');
                    }
                },

                // --------------------------------------------------------
                // Pre-fill form com dados extraídos
                // --------------------------------------------------------
                prefillForm(data, item, isReceita) {
                    if (!data) return;

                    const fin = data.financeiro || {};
                    const estab = data.estabelecimento || {};
                    const classif = data.classificacao || {};
                    const nfe = data.nfe_info || {};

                    // Documento ID
                    const doc = window.currentDocument;
                    if (this.el.documentoId && doc?.id) {
                        this.el.documentoId.value = doc.id;
                    }

                    // Data de competência (formato dd/mm/yyyy)
                    if (fin.data_emissao) {
                        const parts = fin.data_emissao.split('-');
                        if (parts.length === 3) {
                            const formatted = parts[2] + '/' + parts[1] + '/' + parts[0];
                            this.setInputValue('domusia_data_competencia', formatted);
                            this.setInputValue('domusia_vencimento', formatted);
                        }
                    }

                    // Data de vencimento — usar da IA se disponível (boletos, faturas)
                    if (fin.data_vencimento) {
                        const vencParts = fin.data_vencimento.split('-');
                        if (vencParts.length === 3) {
                            const vencFormatted = vencParts[2] + '/' + vencParts[1] + '/' + vencParts[0];
                            this.setInputValue('domusia_vencimento', vencFormatted);
                        }
                    }

                    // Descrição
                    const descricao = classif.descricao_detalhada ||
                        (item?.descricao ? item.descricao : null) ||
                        (estab.nome ? (data.tipo_documento || 'Documento') + ' - ' + estab.nome : '');
                    this.setInputValue('domusia_descricao', descricao);

                    // Valor — Para documentos de transação única (NF-e, Cupom, etc.),
                    // usar sempre o valor_total do financeiro.
                    // Para itens individuais, usar valor do item.
                    const singleTxTypes = ['NF-e', 'NFC-e', 'CUPOM', 'CUPOM_FISCAL', 'NOTA_FISCAL',
                        'FATURA_CARTAO', 'BOLETO', 'RECIBO', 'COMPROVANTE'
                    ];
                    const isSingleTx = singleTxTypes.includes(data.tipo_documento);

                    let valor;
                    if (isSingleTx) {
                        valor = fin.valor_total || 0;
                    } else {
                        valor = item?.valor_unitario ?
                            (item.valor_unitario * (item.quantidade || 1)) :
                            (fin.valor_total || 0);
                    }
                    if (valor > 0) {
                        this.setInputValue('domusia_valor', this.formatMoney(valor));
                    }

                    // Número do documento
                    const numDoc = fin.numero_documento || nfe.numero_nf || '';
                    this.setInputValue('domusia_numero_documento', numDoc);

                    // Juros, Multa, Desconto
                    if (fin.juros > 0) this.setInputValue('domusia_juros', this.formatMoney(fin.juros));
                    if (fin.multa > 0) this.setInputValue('domusia_multa', this.formatMoney(fin.multa));
                    if (fin.desconto > 0) this.setInputValue('domusia_desconto', this.formatMoney(fin
                    .desconto));

                    // Histórico complementar — Produtos da nota + observações
                    const historicoParts = [];

                    const itens = data.itens || [];
                    if (itens.length > 0) {
                        historicoParts.push('ITENS:');
                        itens.forEach((it, idx) => {
                            const desc = (it.descricao || 'Item ' + (idx + 1)).trim();
                            const qtd = it.quantidade || 1;
                            const vlrUnit = parseFloat(it.valor_unitario || 0);
                            const subtotal = qtd * vlrUnit;

                            let linha = qtd + 'x ' + desc;
                            if (vlrUnit > 0) {
                                linha += ' (R$ ' + vlrUnit.toFixed(2).replace('.', ',');
                                if (qtd > 1) {
                                    linha += ' = R$ ' + subtotal.toFixed(2).replace('.', ',');
                                }
                                linha += ')';
                            }
                            historicoParts.push(linha);
                        });
                    }

                    if (data.observacoes) historicoParts.push(data.observacoes);
                    if (fin.observacoes_financeiras) historicoParts.push(fin.observacoes_financeiras);

                    if (historicoParts.length > 0) {
                        const historicoText = historicoParts.join('\n');
                        this.setInputValue('domusia_historico', historicoText.substring(0, 500));
                    }

                    // Comprovação fiscal
                    const docsFiscais = ['NF-e', 'NFC-e', 'CUPOM', 'FATURA_CARTAO'];
                    const checkbox = document.getElementById('domusia_comprovacao_fiscal');
                    if (checkbox) checkbox.checked = docsFiscais.includes(data.tipo_documento);

                    // Match fornecedor por CNPJ
                    if (estab.cnpj) this.matchFornecedorByCNPJ(estab.cnpj, estab.nome);

                    // Forma de pagamento — usar ID direto da IA, senão fallback textual
                    if (fin.forma_pagamento_id) {
                        this.matchFormaPagamentoById(fin.forma_pagamento_id);
                    } else if (fin.forma_pagamento) {
                        this.matchFormaPagamento(fin.forma_pagamento);
                    }

                    // Lançamento padrão — usar ID direto da IA, senão fallback textual
                    if (classif.lancamento_padrao_id) {
                        this.matchCategoriaById(classif.lancamento_padrao_id);
                    } else if (classif.categoria_sugerida) {
                        this.matchCategoria(classif.categoria_sugerida);
                    }

                    // Parcelamento
                    if (data.parcelamento?.is_parcelado && data.parcelamento.total_parcelas > 1) {
                        const parcSelect = document.getElementById('domusia_parcelamento');
                        if (parcSelect) parcSelect.value = data.parcelamento.total_parcelas + 'x';
                    }

                    this.updateValorPago();

                    // Buscar sugestões do histórico após um pequeno delay (para garantir que os selects foram atualizados)
                    setTimeout(() => {
                        if (window.DomusiaDrawerSuggestion) {
                            window.DomusiaDrawerSuggestion.fetchSuggestion();
                        }
                    }, 500);
                },

                // --------------------------------------------------------
                // Helpers de match
                // --------------------------------------------------------
                matchFornecedorByCNPJ(cnpj, nomeFornecedor) {
                    const cleanCnpj = cnpj.replace(/\D/g, '');
                    const select = document.getElementById('domusia_fornecedor_id');
                    if (!select) return;

                    // Limpar dados de novo parceiro de chamadas anteriores
                    this.clearNovoParceiro();

                    const naturezaFiltro = this.currentNaturezaFiltro || (this.isReceita ? 'cliente' : 'fornecedor');
                    const options = select.querySelectorAll('option');

                    // 1. Tentar match por CNPJ/CPF nos parceiros existentes
                    for (const opt of options) {
                        const natureza = opt.dataset.natureza;
                        if (natureza && natureza !== naturezaFiltro && natureza !== 'ambos') {
                            continue;
                        }

                        const optCnpj = (opt.dataset.cnpj || '').replace(/\D/g, '');
                        const optCpf = (opt.dataset.cpf || '').replace(/\D/g, '');
                        if ((optCnpj && optCnpj === cleanCnpj) || (optCpf && optCpf === cleanCnpj)) {
                            $(select).val(opt.value).trigger('change');
                            return;
                        }
                    }

                    // 2. CNPJ não encontrado → criar option virtual "(Novo)"
                    if (nomeFornecedor && cleanCnpj) {
                        this.createNovoParceiro(select, nomeFornecedor, cleanCnpj);
                    }
                },

                // --------------------------------------------------------
                // Criar option virtual de novo parceiro no Select2
                // --------------------------------------------------------
                createNovoParceiro(select, nome, cnpj) {
                    const $select = $(select);

                    // Remover option virtual anterior se existir
                    select.querySelector('option[value="__novo__"]')?.remove();

                    // Criar nova option com tag "(Novo)"
                    const option = new Option(nome + ' (Novo)', '__novo__', true, true);
                    option.dataset.cnpj = cnpj;
                    option.dataset.natureza = this.currentNaturezaFiltro || 'fornecedor';
                    option.dataset.isNovo = 'true';
                    select.appendChild(option);

                    // Selecionar e disparar evento
                    $select.trigger('change');

                    // Preencher hidden fields para o backend
                    document.getElementById('domusia_novo_parceiro_nome').value = nome;
                    document.getElementById('domusia_novo_parceiro_cnpj').value = cnpj;

                    // Marcar visualmente no Select2 que é novo
                    setTimeout(() => {
                        const rendered = select.closest('.fv-row')?.querySelector('.select2-selection__rendered');
                        if (rendered) {
                            rendered.innerHTML = `<span>${nome} <span class="badge badge-sm badge-light-warning ms-1">Novo</span></span>`;
                        }
                    }, 50);
                },

                // --------------------------------------------------------
                // Limpar dados de novo parceiro
                // --------------------------------------------------------
                clearNovoParceiro() {
                    document.getElementById('domusia_novo_parceiro_nome').value = '';
                    document.getElementById('domusia_novo_parceiro_cnpj').value = '';
                    const select = document.getElementById('domusia_fornecedor_id');
                    if (select) {
                        select.querySelector('option[value="__novo__"]')?.remove();
                    }
                },

                matchFormaPagamentoById(formaId) {
                    const select = document.getElementById('domusia_tipo_documento');
                    if (!select || !formaId) return;

                    // Buscar option cujo data-id corresponda ao ID da forma de pagamento
                    for (const opt of select.querySelectorAll('option')) {
                        if (opt.dataset.id == formaId || opt.value == formaId) {
                            $(select).val(opt.value).trigger('change');
                            return;
                        }
                    }
                    // Se não encontrou por ID, não faz nada (o fallback textual será chamado se necessário)
                },

                matchFormaPagamento(formaPgto) {
                    if (!formaPgto) return;
                    const select = document.getElementById('domusia_tipo_documento');
                    if (!select) return;

                    const search = this.normalizeText(formaPgto);
                    const options = select.querySelectorAll('option');

                    // Mapeamento: termos que a IA retorna → códigos do banco
                    const aiToCode = {
                        'pix': 'PIX',
                        'dinheiro': 'DINHEIRO',
                        'especie': 'DINHEIRO',
                        'cartao de credito': 'CC_OUTROS',
                        'credito': 'CC_OUTROS',
                        'cartao de debito': 'CD_OUTROS',
                        'debito': 'CD_OUTROS',
                        'boleto': 'BOLETO',
                        'boleto bancario': 'BOLETO',
                        'cheque': 'CHEQUE',
                        'transferencia': 'TRANSFERENCIA',
                        'transferencia bancaria': 'TRANSFERENCIA',
                        'ted': 'TRANSFERENCIA',
                        'doc': 'TRANSFERENCIA',
                        'deposito': 'DEPOSITO',
                        'deposito bancario': 'DEPOSITO',
                        'debito automatico': 'DEBITO_AUTO',
                        'voucher': 'VALE_REFEICAO',
                        'vale': 'VALE_REFEICAO',
                        'vale alimentacao': 'VALE_ALIMENTACAO',
                        'vale refeicao': 'VALE_REFEICAO',
                        'vale combustivel': 'VALE_COMBUSTIVEL',
                        'vale presente': 'VALE_PRESENTE',
                        'cashback': 'CASHBACK',
                        'carteira digital': 'CARTEIRA',
                    };

                    // 1. Tentar match direto por código
                    const matchedCode = aiToCode[search];
                    if (matchedCode) {
                        for (const opt of options) {
                            if (opt.value === matchedCode) {
                                $(select).val(opt.value).trigger('change');
                                return;
                            }
                        }
                    }

                    // 2. Tentar match parcial nos termos do mapeamento
                    for (const [term, code] of Object.entries(aiToCode)) {
                        if (search.includes(term) || term.includes(search)) {
                            for (const opt of options) {
                                if (opt.value === code) {
                                    $(select).val(opt.value).trigger('change');
                                    return;
                                }
                            }
                        }
                    }

                    // 3. Fallback: busca textual no nome da opção
                    for (const opt of options) {
                        const optText = this.normalizeText(opt.textContent);
                        if (optText.includes(search) || search.includes(optText.split(' - ').pop()?.trim())) {
                            $(select).val(opt.value).trigger('change');
                            return;
                        }
                    }
                },

                // Normalizar texto para comparação (remove acentos e lowercase)
                normalizeText(text) {
                    return (text || '').toLowerCase().trim()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                },

                matchCategoriaById(lancamentoPadraoId) {
                    const select = document.getElementById('domusia_lancamento_padrao_id');
                    if (!select || !lancamentoPadraoId) return;

                    // Buscar option cujo value corresponda ao ID do lançamento padrão
                    for (const opt of select.querySelectorAll('option')) {
                        if (opt.value == lancamentoPadraoId) {
                            $(select).val(opt.value).trigger('change');
                            return;
                        }
                    }
                },

                matchCategoria(categoriaSugerida) {
                    if (!categoriaSugerida) return;
                    const select = document.getElementById('domusia_lancamento_padrao_id');
                    if (!select) return;

                    const search = categoriaSugerida.toLowerCase().trim();
                    const options = select.querySelectorAll('option');
                    const tipoAtual = this.currentTipoFilter || (this.isReceita ? 'entrada' : 'saida');
                    let bestMatch = null;
                    let bestScore = 0;

                    for (const opt of options) {
                        // Ignorar opções que não são do tipo atual
                        const optType = opt.dataset.type;
                        if (optType && optType !== tipoAtual) continue;

                        const optText = opt.textContent.toLowerCase().trim();
                        const optDesc = (opt.dataset.description || '').toLowerCase();

                        if (optDesc === search || optText.includes(search)) {
                            $(select).val(opt.value).trigger('change');
                            console.log('[DomusiaDrawer] Categoria matched:', opt.textContent.trim());
                            return;
                        }

                        const searchWords = search.split(/\s+/);
                        let score = 0;
                        for (const word of searchWords) {
                            if (word.length > 2 && (optDesc.includes(word) || optText.includes(word))) score++;
                        }
                        if (score > bestScore) {
                            bestScore = score;
                            bestMatch = opt;
                        }
                    }

                    if (bestMatch && bestScore >= 1) {
                        $(select).val(bestMatch.value).trigger('change');
                        console.log('[DomusiaDrawer] Categoria matched (parcial):', bestMatch.textContent
                        .trim());
                    }
                },

                // --------------------------------------------------------
                // Parcelamento — handler de change
                // --------------------------------------------------------
                onParcelamentoChange(val) {
                    const accordion = document.getElementById('domusia_parcelas_accordion');
                    const pagoWrapper = document.getElementById('domusia_pago_wrapper');
                    const agendadoWrapper = document.getElementById('domusia_agendado_wrapper');

                    if (val && val !== 'avista' && val !== '1x') {
                        const numParcelas = parseInt(val.replace('x', ''));
                        if (!isNaN(numParcelas) && numParcelas >= 2) {
                            // Validar campos obrigatórios antes de gerar parcelas
                            const camposObrigatorios = [
                                { id: 'domusia_descricao', label: 'Descrição', isSelect: false },
                                { id: 'domusia_valor', label: 'Valor', isSelect: false, isMoney: true },
                                { id: 'domusia_entidade_id', label: 'Entidade Financeira', isSelect: true },
                                { id: 'domusia_vencimento', label: 'Data de Vencimento', isSelect: false },
                            ];

                            let todosPreenchidos = true;
                            this.clearAllErrors();

                            camposObrigatorios.forEach(campo => {
                                const el = document.getElementById(campo.id);
                                if (!el) return;

                                let vazio = false;
                                if (campo.isSelect) {
                                    vazio = !$(el).val();
                                } else if (campo.isMoney) {
                                    vazio = this.parseMoney(el.value || '0') <= 0;
                                } else {
                                    vazio = !(el.value || '').trim();
                                }

                                if (vazio) {
                                    this.showFieldError(campo.id, campo.label + ' é obrigatório para parcelamento');
                                    todosPreenchidos = false;
                                }
                            });

                            if (!todosPreenchidos) {
                                if (accordion) accordion.style.display = 'none';
                                // Voltar para "À Vista" sem disparar novo change
                                const $parcSelect = $('#domusia_parcelamento');
                                $parcSelect.off('change').val('avista').trigger('change.select2');
                                // Rebind após o reset
                                setTimeout(() => {
                                    $parcSelect.on('change', () => {
                                        this.onParcelamentoChange($parcSelect.val());
                                    });
                                }, 50);

                                // Scroll para o primeiro campo com erro
                                const primeiroErro = document.querySelector('#domusia_drawer_form .is-invalid');
                                if (primeiroErro) {
                                    primeiroErro.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                                return;
                            }

                            // Gerar parcelas e mostrar accordion
                            this.gerarParcelas(numParcelas);
                            if (accordion) accordion.style.display = 'block';

                            // Ocultar checkboxes Pago e Agendado (parcelas têm controle individual)
                            // Usar d-none pois d-flex tem !important e vence style.display
                            if (pagoWrapper) {
                                pagoWrapper.classList.add('d-none');
                                pagoWrapper.classList.remove('d-flex');
                            }
                            if (agendadoWrapper) {
                                agendadoWrapper.classList.add('d-none');
                                agendadoWrapper.classList.remove('d-flex');
                            }
                            if (this.el.pagoCheckbox) {
                                this.el.pagoCheckbox.checked = false;
                                this.toggleValoresExtras();
                            }
                            const agendadoCheckbox = document.getElementById('domusia_agendado_checkbox');
                            if (agendadoCheckbox) agendadoCheckbox.checked = false;
                        }
                    } else {
                        // À Vista ou 1x: limpar parcelas e restaurar checkboxes
                        if (accordion) accordion.style.display = 'none';
                        const tbody = document.getElementById('domusia_parcelas_table_body');
                        if (tbody) tbody.innerHTML = '';
                        if (pagoWrapper) {
                            pagoWrapper.classList.remove('d-none');
                            pagoWrapper.classList.add('d-flex');
                        }
                        if (agendadoWrapper) {
                            agendadoWrapper.classList.remove('d-none');
                            agendadoWrapper.classList.add('d-flex');
                        }
                    }
                },

                // --------------------------------------------------------
                // Gerar linhas de parcelas
                // --------------------------------------------------------
                gerarParcelas(numParcelas) {
                    const tbody = document.getElementById('domusia_parcelas_table_body');
                    if (!tbody) return;
                    tbody.innerHTML = '';

                    const valorTotalStr = document.getElementById('domusia_valor')?.value || '0';
                    const valorTotal = this.parseMoney(valorTotalStr);
                    const valorPorParcela = valorTotal / numParcelas;
                    const percentualPorParcela = 100 / numParcelas;

                    // Data de vencimento base
                    const vencimentoStr = document.getElementById('domusia_vencimento')?.value || '';
                    let dataBase = null;
                    if (vencimentoStr) {
                        const partes = vencimentoStr.split('/');
                        if (partes.length === 3) {
                            dataBase = new Date(parseInt(partes[2]), parseInt(partes[1]) - 1, parseInt(partes[0]));
                        }
                    }
                    if (!dataBase || isNaN(dataBase.getTime())) {
                        dataBase = new Date();
                    }

                    const descricaoBase = document.getElementById('domusia_descricao')?.value || '';

                    // Template
                    const template = document.getElementById('domusia_parcela_row_template');
                    if (!template) {
                        console.warn('[DomusiaDrawer] Template de parcela não encontrado');
                        return;
                    }



                    for (let i = 1; i <= numParcelas; i++) {
                        // Calcular data de vencimento (+1 mês por parcela)
                        const dataVenc = new Date(dataBase);
                        dataVenc.setMonth(dataBase.getMonth() + (i - 1));
                        const dataFormatada = String(dataVenc.getDate()).padStart(2, '0') + '/' +
                            String(dataVenc.getMonth() + 1).padStart(2, '0') + '/' +
                            dataVenc.getFullYear();

                        // Valor (última parcela = resto)
                        const valorParcela = (i === numParcelas)
                            ? valorTotal - (Math.round(valorPorParcela * 100) / 100) * (numParcelas - 1)
                            : Math.round(valorPorParcela * 100) / 100;

                        // Percentual (última = resto)
                        const percentualParcela = (i === numParcelas)
                            ? (100 - (Math.round(percentualPorParcela * 100) / 100) * (numParcelas - 1)).toFixed(2)
                            : (Math.round(percentualPorParcela * 100) / 100).toFixed(2);

                        // Clonar template
                        const row = template.content.cloneNode(true);
                        const tr = row.querySelector('tr');
                        tr.setAttribute('data-parcela', i);

                        // Número
                        tr.querySelector('.parcela-numero').textContent = i;

                        // Vencimento
                        const inputVenc = tr.querySelector('.parcela-vencimento');
                        inputVenc.value = dataFormatada;
                        inputVenc.name = `parcelas[${i}][vencimento]`;
                        inputVenc.setAttribute('data-parcela-num', i);

                        // Valor
                        const inputValor = tr.querySelector('.parcela-valor');
                        inputValor.value = this.formatMoney(valorParcela);
                        inputValor.name = `parcelas[${i}][valor]`;
                        inputValor.setAttribute('data-parcela-num', i);
                        if (i === numParcelas) {
                            inputValor.classList.add('bg-light');
                            inputValor.readOnly = true;
                            inputValor.style.cursor = 'not-allowed';
                        }

                        // Percentual
                        const inputPerc = tr.querySelector('.parcela-percentual');
                        inputPerc.value = percentualParcela;
                        inputPerc.name = `parcelas[${i}][percentual]`;
                        inputPerc.setAttribute('data-parcela-num', i);
                        if (i === numParcelas) {
                            inputPerc.classList.add('bg-light');
                            inputPerc.readOnly = true;
                            inputPerc.style.cursor = 'not-allowed';
                        }

                        // Descrição
                        const inputDesc = tr.querySelector('.parcela-descricao');
                        inputDesc.value = `${descricaoBase} ${i}/${numParcelas}`;
                        inputDesc.name = `parcelas[${i}][descricao]`;
                        inputDesc.setAttribute('data-parcela-num', i);
                        inputDesc.setAttribute('data-descricao-base', descricaoBase);

                        // Agendado
                        const inputAgendado = tr.querySelector('.parcela-agendado');
                        inputAgendado.name = `parcelas[${i}][agendado]`;
                        inputAgendado.setAttribute('data-parcela-num', i);

                        tbody.appendChild(row);
                    }

                    // Badge de contagem
                    const badge = document.getElementById('domusia_parcelas_count_badge');
                    if (badge) badge.textContent = numParcelas + 'x';

                    // Inicializar flatpickr nos campos de vencimento
                    setTimeout(() => {
                        const $tbody = $(tbody);

                        // Flatpickr
                        $tbody.find('input[data-parcela-input="vencimento"]').each(function() {
                            // Inputmask de data dd/mm/yyyy
                            if (typeof Inputmask !== 'undefined' && !$(this).data('date-mask-initialized')) {
                                Inputmask({
                                    alias: 'datetime',
                                    inputFormat: 'dd/mm/yyyy',
                                    placeholder: 'dd/mm/aaaa',
                                    clearMaskOnLostFocus: false
                                }).mask(this);
                                $(this).data('date-mask-initialized', true);
                            }
                            // Flatpickr
                            if (!$(this).data('flatpickr-initialized') && typeof flatpickr !== 'undefined') {
                                const config = { enableTime: false, dateFormat: 'd/m/Y', allowInput: true };
                                if (flatpickr.l10ns && flatpickr.l10ns.pt) config.locale = 'pt';
                                flatpickr(this, config);
                                $(this).data('flatpickr-initialized', true);
                            }
                        });

                        // Inputmask moeda
                        if (typeof Inputmask !== 'undefined') {
                            $tbody.find('input[data-parcela-input="valor"]').each(function() {
                                if (!$(this).attr('readonly') && !$(this).data('mask-initialized')) {
                                    Inputmask({
                                        alias: 'currency', groupSeparator: '.', radixPoint: ',',
                                        autoGroup: true, digits: 2, digitsOptional: false,
                                        placeholder: '0,00', rightAlign: false,
                                        removeMaskOnSubmit: false, allowMinus: false,
                                        clearMaskOnLostFocus: false
                                    }).mask(this);
                                    $(this).data('mask-initialized', true);
                                }
                            });

                            $tbody.find('input[data-parcela-input="percentual"]').each(function() {
                                if (!$(this).attr('readonly') && !$(this).data('mask-initialized')) {
                                    Inputmask({
                                        alias: 'decimal', groupSeparator: '', radixPoint: '.',
                                        autoGroup: false, digits: 2, digitsOptional: false,
                                        placeholder: '0.00', rightAlign: false,
                                        allowMinus: false, min: 0, max: 100
                                    }).mask(this);
                                    $(this).data('mask-initialized', true);
                                }
                            });
                        }

                        // Event listeners para recálculo
                        this.adicionarEventListenersParcelas(numParcelas);
                    }, 100);
                },

                // --------------------------------------------------------
                // Event listeners de recálculo de parcelas
                // --------------------------------------------------------
                adicionarEventListenersParcelas(numParcelas) {
                    const $tbody = $('#domusia_parcelas_table_body');

                    // Remove listeners anteriores
                    $tbody.off('input change blur', 'input[data-parcela-input="valor"]');
                    $tbody.off('input change blur', 'input[data-parcela-input="percentual"]');

                    const self = this;

                    // Quando valor de uma parcela muda
                    $tbody.on('input change blur', 'input[data-parcela-input="valor"]', function() {
                        const parcelaNum = parseInt($(this).attr('data-parcela-num'));
                        if (parcelaNum === numParcelas) return;
                        self.recalcularPorValor(numParcelas);
                    });

                    // Quando percentual de uma parcela muda
                    $tbody.on('input change blur', 'input[data-parcela-input="percentual"]', function() {
                        const parcelaNum = parseInt($(this).attr('data-parcela-num'));
                        if (parcelaNum === numParcelas) return;
                        self.recalcularPorPercentual(numParcelas);
                    });
                },

                // --------------------------------------------------------
                // Recalcular última parcela quando valor é editado
                // --------------------------------------------------------
                recalcularPorValor(numParcelas) {
                    const valorTotal = this.parseMoney(document.getElementById('domusia_valor')?.value || '0');
                    if (valorTotal <= 0) return;

                    let somaValores = 0;
                    for (let i = 1; i < numParcelas; i++) {
                        const input = $(`input[data-parcela-input="valor"][data-parcela-num="${i}"]`);
                        somaValores += this.parseMoney(input.val() || '0');
                    }

                    // Validação
                    if (somaValores > valorTotal) {
                        if (typeof toastr !== 'undefined') {
                            toastr.error('A soma das parcelas não pode exceder o valor total', 'Erro');
                        }
                        return;
                    }

                    const valorUltima = valorTotal - somaValores;
                    $(`input[data-parcela-input="valor"][data-parcela-num="${numParcelas}"]`)
                        .val(this.formatMoney(valorUltima));

                    // Recalcular percentuais
                    for (let i = 1; i <= numParcelas; i++) {
                        const input = $(`input[data-parcela-input="valor"][data-parcela-num="${i}"]`);
                        const valor = this.parseMoney(input.val() || '0');
                        const percentual = ((valor / valorTotal) * 100).toFixed(2);
                        $(`input[data-parcela-input="percentual"][data-parcela-num="${i}"]`).val(percentual);
                    }
                },

                // --------------------------------------------------------
                // Recalcular última parcela quando percentual é editado
                // --------------------------------------------------------
                recalcularPorPercentual(numParcelas) {
                    const valorTotal = this.parseMoney(document.getElementById('domusia_valor')?.value || '0');
                    if (valorTotal <= 0) return;

                    let somaPerc = 0;
                    for (let i = 1; i < numParcelas; i++) {
                        const input = $(`input[data-parcela-input="percentual"][data-parcela-num="${i}"]`);
                        somaPerc += parseFloat(input.val() || '0');
                    }

                    if (somaPerc > 100) somaPerc = 100;
                    const percUltima = (100 - somaPerc).toFixed(2);
                    $(`input[data-parcela-input="percentual"][data-parcela-num="${numParcelas}"]`).val(percUltima);

                    // Recalcular valores
                    for (let i = 1; i <= numParcelas; i++) {
                        const input = $(`input[data-parcela-input="percentual"][data-parcela-num="${i}"]`);
                        const perc = parseFloat(input.val() || '0');
                        const valor = (valorTotal * perc) / 100;
                        $(`input[data-parcela-input="valor"][data-parcela-num="${i}"]`).val(this.formatMoney(valor));
                    }
                },

                // --------------------------------------------------------
                // Tentar regenerar parcelas automaticamente
                // --------------------------------------------------------
                tentarRegerarParcelas() {
                    const parcelamento = document.getElementById('domusia_parcelamento')?.value;
                    if (parcelamento && parcelamento !== 'avista' && parcelamento !== '1x') {
                        const numParcelas = parseInt(parcelamento.replace('x', ''));
                        if (!isNaN(numParcelas) && numParcelas >= 2) {
                            this.gerarParcelas(numParcelas);
                        }
                    }
                },

                // --------------------------------------------------------
                // Mostrar/Esconder valores extras (juros, multa, desconto)
                // --------------------------------------------------------
                toggleValoresExtras() {
                    const valoresExtras = document.getElementById('domusia_valores_extras');
                    const isPago = this.el.pagoCheckbox?.checked;

                    if (valoresExtras) {
                        if (isPago) {
                            $(valoresExtras).slideDown(200);
                            this.updateValorPago();
                        } else {
                            $(valoresExtras).slideUp(200);
                            // Limpar valores quando desmarca pago
                            this.setInputValue('domusia_juros', '');
                            this.setInputValue('domusia_multa', '');
                            this.setInputValue('domusia_desconto', '');
                            this.setInputValue('domusia_valor_pago', '');
                        }
                    }
                },

                // --------------------------------------------------------
                // Calcular valor_pago
                // --------------------------------------------------------
                updateValorPago() {
                    // Só calcula se pago estiver marcado
                    if (!this.el.pagoCheckbox?.checked) return;

                    const toNum = (s) => parseFloat((s || '0').replace(/\./g, '').replace(',', '.')) || 0;
                    const valor = toNum(document.getElementById('domusia_valor')?.value);
                    const juros = toNum(document.getElementById('domusia_juros')?.value);
                    const multa = toNum(document.getElementById('domusia_multa')?.value);
                    const desconto = toNum(document.getElementById('domusia_desconto')?.value);
                    const valorPago = valor + juros + multa - desconto;
                    this.setInputValue('domusia_valor_pago', this.formatMoney(Math.max(0, valorPago)));
                },

                // --------------------------------------------------------
                // Submit AJAX
                // --------------------------------------------------------
                async submit(openNew = false) {
                    if (this.isSubmitting) return;

                    // Limpar erros anteriores
                    this.clearAllErrors();

                    // Validação frontend antes de enviar
                    if (!this.validateForm()) {
                        return;
                    }

                    this.isSubmitting = true;
                    this.setLoading(true);

                    try {
                        const form = this.el.form;
                        if (!form) throw new Error('Formulário não encontrado');

                        const formData = new FormData(form);

                        // Converter data_competencia de dd/mm/yyyy para dd-mm-yyyy
                        const dataComp = formData.get('data_competencia');
                        if (dataComp) formData.set('data_competencia', dataComp.replace(/\//g, '-'));

                        // Converter campos monetários para decimal
                        ['valor', 'juros', 'multa', 'desconto', 'valor_pago'].forEach(field => {
                            const val = formData.get(field);
                            if (val) formData.set(field, this.parseMoney(val).toString());
                        });

                        // Converter valores monetários das parcelas
                        const parcelamentoVal = formData.get('parcelamento');
                        if (parcelamentoVal && parcelamentoVal !== 'avista' && parcelamentoVal !== '1x') {
                            const rows = document.querySelectorAll('#domusia_parcelas_table_body tr[data-parcela]');
                            rows.forEach(row => {
                                const num = row.getAttribute('data-parcela');
                                const valorKey = `parcelas[${num}][valor]`;
                                const val = formData.get(valorKey);
                                if (val) {
                                    formData.set(valorKey, this.parseMoney(val).toString());
                                }
                                // Garantir que agendado seja enviado como 0 se não marcado
                                const agendadoKey = `parcelas[${num}][agendado]`;
                                if (!formData.has(agendadoKey)) {
                                    formData.set(agendadoKey, '0');
                                }
                            });
                        }

                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content') || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData,
                        });

                        const result = await response.json();

                        if (response.ok && (result.success !== false)) {
                            this.showSuccessNotification('Lançamento criado com sucesso!');
                            this.markEntryAsProcessed(this.currentEntryIndex);

                            // Remover documento da lista de pendentes
                            // Fallback: se o backend não retornar domus_documento_id, usar o do hidden input
                            const docIdToRemove = result.domus_documento_id ||
                                document.getElementById('domusia_documento_id')?.value ||
                                window.currentDocument?.id;
                            this.removeDocumentFromList(docIdToRemove);

                            if (openNew) {
                                this.resetForm();
                            } else {
                                this.close();
                            }
                        } else {
                            const errors = result.errors;
                            if (errors) {
                                this.showFieldErrors(errors);
                            } else {
                                this.showGeneralError(result.message || 'Erro ao criar lançamento');
                            }
                        }
                    } catch (error) {
                        console.error('[DomusiaDrawer] Erro no submit:', error);
                        this.showGeneralError('Erro ao processar: ' + error.message);
                    } finally {
                        this.isSubmitting = false;
                        this.setLoading(false);
                    }
                },

                // --------------------------------------------------------
                // Validação do formulário
                // --------------------------------------------------------
                validateForm() {
                    let isValid = true;
                    const requiredFields = [{
                            id: 'domusia_descricao',
                            name: 'descricao',
                            label: 'Descrição'
                        },
                        {
                            id: 'domusia_valor',
                            name: 'valor',
                            label: 'Valor'
                        },
                        {
                            id: 'domusia_data_competencia',
                            name: 'data_competencia',
                            label: 'Data Competência'
                        },
                        {
                            id: 'domusia_entidade_id',
                            name: 'entidade_id',
                            label: 'Entidade Financeira',
                            isSelect: true
                        },
                        {
                            id: 'domusia_lancamento_padrao_id',
                            name: 'lancamento_padrao_id',
                            label: 'Categoria',
                            isSelect: true
                        },
                        {
                            id: 'domusia_cost_center_id',
                            name: 'cost_center_id',
                            label: 'Centro de Custo',
                            isSelect: true
                        },
                        {
                            id: 'domusia_tipo_documento',
                            name: 'tipo_documento',
                            label: 'Forma de Pagamento',
                            isSelect: true
                        },
                    ];

                    requiredFields.forEach(field => {
                        const el = document.getElementById(field.id);
                        if (!el) return;

                        let value = field.isSelect ? $(el).val() : el.value.trim();

                        if (!value || value === '') {
                            this.showFieldError(field.id, field.label + ' é obrigatório');
                            isValid = false;
                        }
                    });

                    return isValid;
                },

                // --------------------------------------------------------
                // Exibir erro em um campo específico
                // --------------------------------------------------------
                showFieldError(fieldId, message) {
                    const el = document.getElementById(fieldId);
                    if (!el) return;

                    // Encontrar o container fv-row pai
                    const container = el.closest('.fv-row') || el.closest('.col-md-3') || el.closest(
                            '.col-md-4') || el.closest('.col-md-5') || el.closest('.col-md-6') || el
                        .parentElement;

                    // Adicionar classe de erro no input/select
                    el.classList.add('is-invalid');

                    // Para Select2, adicionar classe no container do Select2
                    const select2Container = container.querySelector('.select2-container');
                    if (select2Container) {
                        select2Container.classList.add('is-invalid');
                        select2Container.style.border = '1px solid #f1416c';
                        select2Container.style.borderRadius = '0.475rem';
                    }

                    // Remover mensagem de erro anterior se existir
                    const existingError = container.querySelector('.fv-plugins-message-container');
                    if (existingError) existingError.remove();

                    // Criar e adicionar mensagem de erro
                    const errorDiv = document.createElement('div');
                    errorDiv.className =
                        'fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback';
                    errorDiv.style.display = 'block';
                    errorDiv.innerHTML = '<div class="fv-help-block"><span role="alert">' + message +
                        '</span></div>';
                    container.appendChild(errorDiv);
                },

                // --------------------------------------------------------
                // Exibir erros retornados pelo backend
                // --------------------------------------------------------
                showFieldErrors(errors) {
                    // Mapeamento de nomes de campos do backend para IDs do formulário
                    const fieldMapping = {
                        'descricao': 'domusia_descricao',
                        'valor': 'domusia_valor',
                        'data_competencia': 'domusia_data_competencia',
                        'entidade_id': 'domusia_entidade_id',
                        'lancamento_padrao_id': 'domusia_lancamento_padrao_id',
                        'cost_center_id': 'domusia_cost_center_id',
                        'tipo_documento': 'domusia_tipo_documento',
                        'fornecedor_id': 'domusia_fornecedor_id',
                        'numero_documento': 'domusia_numero_documento',
                        'vencimento': 'domusia_vencimento',
                        'historico_complementar': 'domusia_historico',
                        'parcelamento': 'domusia_parcelamento',
                    };

                    // Mapeamento de campo da parcela → data-parcela-input
                    const parcelaInputMap = {
                        'vencimento': 'vencimento',
                        'valor': 'valor',
                        'percentual': 'percentual',
                        'descricao': 'descricao',
                    };

                    Object.entries(errors).forEach(([field, messages]) => {
                        const message = Array.isArray(messages) ? messages[0] : messages;

                        // Verificar se é erro de parcela (ex: parcelas.1.vencimento, parcelas.2.valor)
                        const parcelaMatch = field.match(/^parcelas\.(\d+)\.(\w+)$/);
                        if (parcelaMatch) {
                            const parcelaNum = parcelaMatch[1];
                            const parcelaCampo = parcelaMatch[2];
                            const inputType = parcelaInputMap[parcelaCampo];

                            if (inputType) {
                                // Buscar o input da parcela na tabela
                                const input = document.querySelector(
                                    `[data-parcela-input="${inputType}"][data-parcela-num="${parcelaNum}"]`
                                );
                                if (input) {
                                    input.classList.add('is-invalid');
                                    input.style.borderColor = '#f1416c';

                                    // Adicionar tooltip com mensagem de erro
                                    input.setAttribute('title', message);
                                    input.setAttribute('data-bs-toggle', 'tooltip');
                                    input.setAttribute('data-bs-placement', 'top');

                                    // Inicializar tooltip se Bootstrap disponível
                                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                                        new bootstrap.Tooltip(input);
                                    }
                                }
                            }
                            return;
                        }

                        // Campo normal do formulário
                        const fieldId = fieldMapping[field] || 'domusia_' + field;
                        this.showFieldError(fieldId, message);
                    });

                    // Se há erros de parcelas, mostrar toastr resumido
                    const parcelaErrors = Object.keys(errors).filter(k => k.startsWith('parcelas.'));
                    if (parcelaErrors.length > 0 && typeof toastr !== 'undefined') {
                        toastr.error('Verifique os campos destacados nas parcelas', 'Erro nas parcelas');
                    }

                    // Scroll para o primeiro erro
                    const firstError = document.querySelector('#domusia_drawer_form .is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                },

                // --------------------------------------------------------
                // Limpar todos os erros
                // --------------------------------------------------------
                clearAllErrors() {
                    // Remover classe is-invalid de todos os inputs/selects
                    this.el.form.querySelectorAll('.is-invalid').forEach(el => {
                        el.classList.remove('is-invalid');
                        el.style.borderColor = '';
                    });

                    // Remover estilos de erro dos Select2
                    this.el.form.querySelectorAll('.select2-container').forEach(el => {
                        el.classList.remove('is-invalid');
                        el.style.border = '';
                    });

                    // Remover todas as mensagens de erro
                    this.el.form.querySelectorAll('.fv-plugins-message-container').forEach(el => {
                        el.remove();
                    });

                    // Limpar tooltips de erro das parcelas
                    this.el.form.querySelectorAll('[data-parcela-input]').forEach(el => {
                        el.removeAttribute('title');
                        el.removeAttribute('data-bs-toggle');
                        el.removeAttribute('data-bs-placement');
                        // Destruir tooltip se existir
                        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                            const tooltip = bootstrap.Tooltip.getInstance(el);
                            if (tooltip) tooltip.dispose();
                        }
                    });

                    // Limpar erro geral
                    if (this.el.statusText) {
                        this.el.statusText.innerHTML =
                            '<i class="fa-solid fa-circle-info me-1"></i>Preencha os campos obrigatórios para salvar';
                        this.el.statusText.classList.remove('text-danger');
                        this.el.statusText.classList.add('text-muted');
                    }
                },

                // --------------------------------------------------------
                // Exibir erro geral
                // --------------------------------------------------------
                showGeneralError(message) {
                    if (this.el.statusText) {
                        this.el.statusText.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-1"></i>' +
                            message;
                        this.el.statusText.classList.remove('text-muted');
                        this.el.statusText.classList.add('text-danger');
                    }
                },

                // --------------------------------------------------------
                // Exibir notificação de sucesso (toast simples)
                // --------------------------------------------------------
                showSuccessNotification(message) {
                    // Criar toast de sucesso
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed top-0 end-0 p-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                <div class="toast show align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa-solid fa-check-circle me-2"></i>${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
                    document.body.appendChild(toast);

                    // Remover após 3 segundos
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                },

                // --------------------------------------------------------
                // Marcar entrada como processada
                // --------------------------------------------------------
                markEntryAsProcessed(index) {
                    const card = document.querySelector('[data-entry-index="' + index + '"]');
                    if (card) {
                        card.classList.add('opacity-50');
                        card.style.pointerEvents = 'none';
                        const badge = document.createElement('div');
                        badge.className = 'position-absolute top-0 end-0 m-3';
                        badge.innerHTML =
                            '<span class="badge badge-success fs-8"><i class="fa-solid fa-check me-1"></i>Lançado</span>';
                        card.style.position = 'relative';
                        card.appendChild(badge);
                    }
                },

                // --------------------------------------------------------
                // Remover documento da lista de pendentes
                // --------------------------------------------------------
                removeDocumentFromList(documentoId) {
                    if (!documentoId) return;

                    // Buscar o item na lista (suporta data-document-id e data-id)
                    const listItem = document.querySelector(
                            `.pending-document-item[data-document-id="${documentoId}"]`) ||
                        document.querySelector(`.pending-document-item[data-id="${documentoId}"]`);

                    if (listItem) {
                        // Animação de fade out antes de remover
                        listItem.style.transition = 'all 0.3s ease-out';
                        listItem.style.opacity = '0';
                        listItem.style.transform = 'translateX(-20px)';

                        setTimeout(() => {
                            listItem.remove();

                            // Atualizar contador de documentos pendentes
                            this.updatePendingCount();

                            // Se não houver mais documentos, mostrar empty state
                            this.checkEmptyState();

                            console.log('[DomusiaDrawer] Documento removido da lista:', documentoId);
                        }, 300);
                    }

                    // Sincronizar arrays internos do DomusiaPendentes
                    if (window.domusiaPendentesInstance) {
                        const idNum = parseInt(documentoId);
                        window.domusiaPendentesInstance.documentosCarregados =
                            window.domusiaPendentesInstance.documentosCarregados.filter(d => d.id !== idNum);
                        window.domusiaPendentesInstance.documentList =
                            window.domusiaPendentesInstance.documentList.filter(d => d.id !== idNum);
                    }

                    // Limpar o documento atual se for o mesmo
                    if (window.currentDocument && window.currentDocument.id == documentoId) {
                        window.currentDocument = null;
                    }
                },

                // --------------------------------------------------------
                // Atualizar contador de documentos pendentes
                // --------------------------------------------------------
                updatePendingCount() {
                    const pendingItems = document.querySelectorAll('.pending-document-item');
                    const count = pendingItems.length;

                    // Atualizar badge principal (pendentes.blade.php)
                    const countBadge = document.getElementById('documentosCountBadge');
                    if (countBadge) {
                        countBadge.textContent = count + ' restantes';
                    }

                    // Atualizar via DomusiaPendentes (se disponível)
                    if (window.domusiaPendentesInstance && typeof window.domusiaPendentesInstance
                        .updateCountBadge === 'function') {
                        window.domusiaPendentesInstance.updateCountBadge(count);
                    }
                },

                // --------------------------------------------------------
                // Verificar e mostrar empty state se não houver documentos
                // --------------------------------------------------------
                checkEmptyState() {
                    const pendingItems = document.querySelectorAll('.pending-document-item');
                    const pendingList = document.getElementById('pendingDocumentsList');

                    if (pendingItems.length === 0 && pendingList) {
                        pendingList.innerHTML = `
                    <div class="text-center py-10 text-muted">
                        <i class="fa-solid fa-check-circle fs-3x text-success mb-4 d-block"></i>
                        <div class="fw-bold text-gray-700 fs-5 mb-2">Tudo em dia!</div>
                        <div class="text-gray-500">Não há documentos pendentes de lançamento.</div>
                    </div>
                `;
                    }
                },

                // --------------------------------------------------------
                // Show/Hide drawer
                // --------------------------------------------------------
                show() {
                    const drawerEl = this.el.drawer;
                    if (!drawerEl) return;

                    let drawer = KTDrawer.getInstance(drawerEl);
                    if (!drawer) drawer = new KTDrawer(drawerEl);

                    if (drawer && typeof drawer.show === 'function') {
                        drawer.show();
                    } else {
                        drawerEl.classList.add('drawer-on');
                        document.body.classList.add('overflow-hidden');
                    }
                },

                close() {
                    const drawerEl = this.el.drawer;
                    if (!drawerEl) return;

                    let drawer = KTDrawer.getInstance(drawerEl);
                    if (drawer && typeof drawer.hide === 'function') {
                        drawer.hide();
                    } else {
                        drawerEl.classList.remove('drawer-on');
                        document.body.classList.remove('overflow-hidden');
                    }
                },

                // --------------------------------------------------------
                // Reset form
                // --------------------------------------------------------
                resetForm() {
                    // Limpar dados de novo parceiro antes do reset
                    this.clearNovoParceiro();

                    if (this.el.form) this.el.form.reset();
                    $('#domusia_fornecedor_id, #domusia_entidade_id, #domusia_lancamento_padrao_id, #domusia_cost_center_id, #domusia_tipo_documento')
                        .val(null).trigger('change');

                    // Resetar parcelamento para "À Vista"
                    $('#domusia_parcelamento').val('avista').trigger('change.select2');
                    const accordion = document.getElementById('domusia_parcelas_accordion');
                    if (accordion) accordion.style.display = 'none';
                    const tbody = document.getElementById('domusia_parcelas_table_body');
                    if (tbody) tbody.innerHTML = '';
                    const pagoWrapper = document.getElementById('domusia_pago_wrapper');
                    if (pagoWrapper) {
                        pagoWrapper.classList.remove('d-none');
                        pagoWrapper.classList.add('d-flex');
                    }
                    const agendadoWrapper = document.getElementById('domusia_agendado_wrapper');
                    if (agendadoWrapper) {
                        agendadoWrapper.classList.remove('d-none');
                        agendadoWrapper.classList.add('d-flex');
                    }

                    // Limpar erros de validação

                    // Limpar sugestões
                    if (window.DomusiaDrawerSuggestion) {
                        window.DomusiaDrawerSuggestion.clear();
                    }
                },

                // --------------------------------------------------------
                // UI Helpers
                // --------------------------------------------------------
                setLoading(loading) {
                    const btn = this.el.submitBtn;
                    if (!btn) return;
                    const labelEl = btn.querySelector('.indicator-label');
                    const progressEl = btn.querySelector('.indicator-progress');
                    if (loading) {
                        if (labelEl) labelEl.style.display = 'none';
                        if (progressEl) progressEl.style.display = 'inline-block';
                        btn.disabled = true;
                    } else {
                        if (labelEl) labelEl.style.display = 'inline-block';
                        if (progressEl) progressEl.style.display = 'none';
                        btn.disabled = false;
                    }
                },

                setInputValue(id, value) {
                    const el = document.getElementById(id);
                    if (el) {
                        el.value = value || '';
                        el.dispatchEvent(new Event('input', {
                            bubbles: true
                        }));
                        el.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                },

                formatMoney(value) {
                    const num = parseFloat(value) || 0;
                    return num.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                },

                parseMoney(str) {
                    if (!str) return 0;
                    return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
                },

                formatCNPJ(cnpj) {
                    if (!cnpj) return null;
                    const clean = cnpj.replace(/\D/g, '');
                    if (clean.length === 14) return clean.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,
                        '$1.$2.$3/$4-$5');
                    if (clean.length === 11) return clean.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/,
                        '$1.$2.$3-$4');
                    return cnpj;
                },
            };

            // Inicializar
            DomusiaDrawer.init();
            window.DomusiaDrawer = DomusiaDrawer;

            // ========================================================
            // Sistema de Sugestão baseado em Histórico
            // ========================================================
            const DomusiaDrawerSuggestion = {
                debounceTimer: null,
                lastSuggestion: null,

                init() {
                    this.bindEvents();
                    console.log('[DomusiaDrawerSuggestion] Sistema de sugestão inicializado');
                },

                bindEvents() {
                    const self = this;

                    // Trigger: mudança no parceiro
                    const parceiroSelect = document.getElementById('domusia_fornecedor_id');
                    if (parceiroSelect) {
                        $(parceiroSelect).on('change', function() {
                            self.fetchSuggestion();
                        });
                    }

                    // Trigger: digitação na descrição (com debounce)
                    const descricaoInput = document.getElementById('domusia_descricao');
                    if (descricaoInput) {
                        descricaoInput.addEventListener('input', function() {
                            clearTimeout(self.debounceTimer);
                            self.debounceTimer = setTimeout(() => {
                                self.fetchSuggestion();
                            }, 1000);
                        });
                    }
                },

                async fetchSuggestion() {
                    const parceiroId = $('#domusia_fornecedor_id').val();
                    const descricao = document.getElementById('domusia_descricao')?.value || '';
                    const valor = document.getElementById('domusia_valor')?.value || '';

                    // Precisa ter pelo menos parceiro ou descrição
                    if (!parceiroId && descricao.length < 3) {
                        return;
                    }

                    try {
                        const response = await fetch(`{{ route('banco.sugestao') }}?` +
                            new URLSearchParams({
                                parceiro_id: parceiroId || '',
                                descricao: descricao,
                                valor: valor
                            }), {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                        if (!response.ok) return;

                        const sugestao = await response.json();
                        this.applySuggestion(sugestao);
                    } catch (error) {
                        console.error('[DomusiaDrawerSuggestion] Erro ao buscar sugestão:', error);
                    }
                },

                applySuggestion(sugestao) {
                    if (!sugestao || sugestao.confianca < 50) {
                        console.log('[DomusiaDrawerSuggestion] Sugestão ignorada (confiança < 50%):', sugestao
                            ?.confianca);
                        return;
                    }

                    console.log('[DomusiaDrawerSuggestion] Aplicando sugestão:', sugestao);
                    this.lastSuggestion = sugestao;

                    const origemTexto = this.getOrigemTexto(sugestao.origem_sugestao);

                    // Aplicar categoria (lancamento_padrao_id)
                    if (sugestao.lancamento_padrao_id) {
                        const $catSelect = $('#domusia_lancamento_padrao_id');
                        const currentVal = $catSelect.val();
                        const tipoAtual = DomusiaDrawer.currentTipoFilter || (DomusiaDrawer.isReceita ?
                            'entrada' : 'saida');

                        // Verificar se a categoria sugerida é compatível com o tipo atual
                        const $option = $catSelect.find(`option[value="${sugestao.lancamento_padrao_id}"]`);
                        const optionType = $option.data('type');

                        if ($option.length && optionType === tipoAtual) {
                            // Só preenche se estiver vazio
                            if (!currentVal) {
                                $catSelect.val(sugestao.lancamento_padrao_id).trigger('change');
                            }

                            // Registrar estrela
                            if (typeof suggestionStarManager !== 'undefined') {
                                suggestionStarManager.addStar(
                                    'domusia_lancamento_padrao_id',
                                    sugestao.lancamento_padrao_id.toString(),
                                    `<strong>Sugestão da IA</strong><br>${origemTexto}<br>Confiança: ${sugestao.confianca}%`
                                );
                            }
                        }
                    }

                    // Aplicar centro de custo (cost_center_id)
                    if (sugestao.cost_center_id) {
                        const $ccSelect = $('#domusia_cost_center_id');
                        const currentVal = $ccSelect.val();

                        // Só preenche se estiver vazio
                        if (!currentVal) {
                            $ccSelect.val(sugestao.cost_center_id).trigger('change');
                        }

                        // Registrar estrela
                        if (typeof suggestionStarManager !== 'undefined') {
                            suggestionStarManager.addStar(
                                'domusia_cost_center_id',
                                sugestao.cost_center_id.toString(),
                                `<strong>Sugestão da IA</strong><br>${origemTexto}<br>Confiança: ${sugestao.confianca}%`
                            );
                        }
                    }

                    // Aplicar tipo de documento / forma de pagamento
                    if (sugestao.tipo_documento) {
                        const $tipoSelect = $('#domusia_tipo_documento');
                        const currentVal = $tipoSelect.val();

                        // Só preenche se estiver vazio
                        if (!currentVal) {
                            $tipoSelect.val(sugestao.tipo_documento).trigger('change');
                        }

                        // Registrar estrela
                        if (typeof suggestionStarManager !== 'undefined') {
                            suggestionStarManager.addStar(
                                'domusia_tipo_documento',
                                sugestao.tipo_documento.toString(),
                                `<strong>Sugestão da IA</strong><br>${origemTexto}<br>Confiança: ${sugestao.confianca}%`
                            );
                        }
                    }

                    // Aplicar descrição sugerida (apenas se campo estiver vazio)
                    if (sugestao.descricao) {
                        const descInput = document.getElementById('domusia_descricao');
                        if (descInput && !descInput.value.trim()) {
                            descInput.value = sugestao.descricao;
                        }
                    }
                },

                getOrigemTexto(origem) {
                    const origens = {
                        'regra': 'Baseado em regra configurada',
                        'historico_parceiro': 'Baseado no histórico do parceiro',
                        'historico_texto': 'Baseado em lançamentos similares',
                        'padrao': 'Baseado em padrões do sistema'
                    };
                    return origens[origem] || 'Baseado no histórico';
                },

                // Limpar sugestões (chamado no reset do form)
                clear() {
                    this.lastSuggestion = null;
                }
            };

            DomusiaDrawerSuggestion.init();
            window.DomusiaDrawerSuggestion = DomusiaDrawerSuggestion;



            // ========================================================
            // Substituir a função createTransaction global
            // ========================================================
            window.createTransaction = function(index, isReceita) {
                console.log('[DomusiaDrawer] Criar transação - Item:', index, 'Tipo:', isReceita ? 'Receita' :
                    'Despesa');
                if (window.DomusiaDrawer) {
                    window.DomusiaDrawer.open(index, isReceita);
                }
            };

            window.createExpense = function(index) {
                window.createTransaction(index, false);
            };

            // ========================================================
            // Listener para quando um parceiro é criado no drawer
            // ========================================================
            document.addEventListener('parceiro-created', function(e) {
                const detail = e.detail;
                if (!detail || !detail.id) return;

                // Verificar se o select alvo é o nosso
                if (window.__drawerTargetSelect !== '#domusia_fornecedor_id') return;

                console.log('[DomusiaDrawer] Parceiro criado:', detail);

                // Atualizar o select com os atributos corretos
                const $select = $('#domusia_fornecedor_id');
                if ($select.length) {
                    // A opção já foi adicionada pelo drawer_fornecedor, apenas precisamos
                    // adicionar os data attributes se necessário
                    const $option = $select.find(`option[value="${detail.id}"]`);
                    if ($option.length && detail.type) {
                        // Definir natureza baseada no tipo do parceiro
                        $option.attr('data-natureza', detail.type);
                    }
                }
            });
        });
    </script>
@endpush
