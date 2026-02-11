{{-- Drawer para criar lançamento a partir de documento Domusia --}}
@php
    $drawerId = 'domusia_expense_drawer';
    $formId = 'domusia_drawer_form';
    $cancelId = 'domusia_drawer_cancel';
    $submitId = 'domusia_drawer_submit';
    $submitNewId = 'domusia_drawer_submit_new';
    $closeId = 'domusia_drawer_close';

    $splitItems = [
        ['id' => $submitNewId, 'text' => 'Salvar e Novo', 'icon' => 'fa-solid fa-plus'],
    ];
@endphp

<x-tenant-drawer
    drawerId="{{ $drawerId }}"
    title="Nova Despesa"
    width="100%"
    toggleButtonId="domusia_expense_drawer_toggle"
    closeButtonId="{{ $closeId }}"
    :showCloseButton="true"
    bodyClass="p-0 overflow-hidden"
    cardClass="shadow-none rounded-0 w-100">

    {{-- ========== TOOLBAR DO HEADER ========== --}}
    <x-slot name="toolbar">
        <div class="d-flex align-items-center gap-2 me-3">
            <span class="bullet bullet-vertical h-30px" id="domusia_drawer_type_indicator"
                style="background-color: #f1416c;"></span>
            <div class="me-3">
                <span class="fw-bold fs-5" id="domusia_drawer_title">Nova Despesa</span>
                <span class="text-muted fs-7 d-block" id="domusia_drawer_subtitle">Preencha os dados do lançamento</span>
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
            <div class="col-xl-5 border-end h-100 position-relative" style="min-height: 400px;">
                <div id="drawer_viewer_wrapper" class="domus-document-viewer-wrapper position-relative w-100 h-100">

                    {{-- Container do Viewer --}}
                    <div class="position-relative" style="height: 100%; overflow: hidden; margin: 0; padding: 0 !important;">

                        {{-- Toolbar Flutuante --}}
                        <div class="domus-floating-toolbar" style="opacity: 1;">
                            <button type="button" class="domus-toolbar-btn btn-zoom-out" title="Diminuir Zoom (-)">
                                <i class="fa-solid fa-minus fs-6"></i>
                            </button>
                            <span class="domus-zoom-indicator zoom-indicator">100%</span>
                            <button type="button" class="domus-toolbar-btn btn-zoom-in" title="Aumentar Zoom (+)">
                                <i class="fa-solid fa-plus fs-6"></i>
                            </button>
                            <button type="button" class="domus-toolbar-btn btn-fit-zoom" title="Ajustar ao Container (0)">
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
                        <div id="drawer_empty_state" class="domus-empty-state d-flex flex-column align-items-center justify-content-center h-100 text-center p-10"
                            style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1; background: #2d2d2d;">
                            <i class="fa-solid fa-file-circle-question fs-3x text-gray-500 mb-4"></i>
                            <p class="text-gray-500 fw-semibold mb-0">Nenhum documento carregado</p>
                        </div>

                        {{-- Viewer Container --}}
                        <div id="drawer_viewer_container" class="domus-viewer-container"
                            style="display: none; position: absolute; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; z-index: 2; margin: 0; padding: 0;">
                            <iframe id="drawer_pdf_viewer" class="w-100 h-100 border-0 domus-viewer-pdf"
                                style="min-height: 400px; display: none; position: relative;" allowfullscreen></iframe>
                            <img id="drawer_image_viewer" class="domus-viewer-img"
                                style="display: none;" draggable="false" alt="Documento" />
                        </div>
                    </div>

                </div>
            </div>
            <!--end::Col Esquerda-->

            <!--begin::Col Direita - Formulário-->
            <div class="col-xl-7 h-100 overflow-y-auto bg-light">
                <form id="{{ $formId }}" method="POST" action="{{ route('transacoes-financeiras.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Campos hidden --}}
                    <input type="hidden" name="tipo" id="domusia_tipo" value="saida">
                    <input type="hidden" name="origem" id="domusia_origem" value="Banco">
                    <input type="hidden" name="domus_documento_id" id="domusia_documento_id" value="">

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
                                {{-- Linha 1: Fornecedor, Descrição --}}
                                <div class="row g-4 mb-5">
                                    <x-tenant-select name="fornecedor_id" id="domusia_fornecedor_id" label="Fornecedor"
                                        placeholder="Selecione um fornecedor" :minimumResultsForSearch="0"
                                        dropdown-parent="#{{ $drawerId }}" labelSize="fs-7" class="col-md-6">
                                        @if (isset($fornecedores))
                                            @foreach ($fornecedores as $fornecedor)
                                                <option value="{{ $fornecedor->id }}"
                                                    data-cnpj="{{ $fornecedor->cnpj }}"
                                                    data-cpf="{{ $fornecedor->cpf }}">
                                                    {{ $fornecedor->nome }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>

                                    <x-tenant-input name="descricao" id="domusia_descricao" label="Descrição"
                                        placeholder="Informe a descrição" required class="col-md-6" />
                                </div>

                                {{-- Linha 2: Data, Valor, Entidade Financeira, Categoria --}}
                                <div class="row g-4 mb-5">
                                    <x-tenant-date name="data_competencia" id="domusia_data_competencia"
                                        label="Data Competência" placeholder="Informe a data" required
                                        class="col-md-3" />

                                    <x-tenant-currency name="valor" id="domusia_valor" label="Valor"
                                        placeholder="0,00" tooltip="Valor total do documento" class="col-md-3"
                                        required />

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
                                        :allowClear="true" :minimumResultsForSearch="0"
                                        dropdown-parent="#{{ $drawerId }}" labelSize="fs-7" class="col-md-3">
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
                                        dropdown-parent="#{{ $drawerId }}" labelSize="fs-7" class="col-md-3">
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
                                        :allowClear="true" :minimumResultsForSearch="0"
                                        dropdown-parent="#{{ $drawerId }}" labelSize="fs-7" class="col-md-3">
                                        @if (isset($formasPagamento))
                                            @foreach ($formasPagamento as $fp)
                                                <option value="{{ $fp->codigo }}">
                                                    {{ $fp->id }} - {{ $fp->nome }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </x-tenant-select>

                                    <x-tenant-input name="numero_documento" id="domusia_numero_documento"
                                        label="Nº Documento" placeholder="Nº NF / Recibo"
                                        type="text" class="col-md-3" />

                                    <div class="col-md-3 fv-row d-flex align-items-end pb-2">
                                        <input type="hidden" name="comprovacao_fiscal" value="0">
                                        <label class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="comprovacao_fiscal"
                                                value="1" id="domusia_comprovacao_fiscal" checked />
                                            <span class="form-check-label fw-semibold text-muted fs-7">Comprovação Fiscal</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Card: Condição de Pagamento --}}
                        <div class="card border border-gray-300 mb-5">
                            <div class="card-header min-h-45px">
                                <h3 class="card-title fs-6 fw-bold">Condição de Pagamento</h3>
                            </div>
                            <div class="card-body px-6 py-5">
                                <div class="row g-4 mb-4">
                                    <div class="col-md-3 fv-row">
                                        <label class="fs-7 fw-semibold mb-2">Parcelamento</label>
                                        <select class="form-select form-select-sm" name="parcelamento"
                                            id="domusia_parcelamento">
                                            <option value="avista" selected>À Vista</option>
                                            @for ($i = 2; $i <= 24; $i++)
                                                <option value="{{ $i }}x">{{ $i }}x</option>
                                            @endfor
                                        </select>
                                    </div>

                                    <x-tenant-date name="vencimento" id="domusia_vencimento" label="Vencimento"
                                        placeholder="Data de vencimento" class="col-md-3" />

                                    <div class="col-md-3 fv-row d-flex align-items-end pb-2" id="domusia_pago_wrapper">
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="pago"
                                                value="1" id="domusia_pago_checkbox" />
                                            <label class="form-check-label fw-semibold text-muted fs-7"
                                                for="domusia_pago_checkbox" id="domusia_pago_label">
                                                Pago
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3 fv-row d-flex align-items-end pb-2">
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" name="agendado"
                                                value="1" id="domusia_agendado_checkbox" />
                                            <label class="form-check-label fw-semibold text-muted fs-7"
                                                for="domusia_agendado_checkbox">
                                                Agendado
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-4" id="domusia_valores_extras">
                                    <x-tenant-currency name="juros" id="domusia_juros" label="Juros"
                                        placeholder="0,00" class="col-md-3" />
                                    <x-tenant-currency name="multa" id="domusia_multa" label="Multa"
                                        placeholder="0,00" class="col-md-3" />
                                    <x-tenant-currency name="desconto" id="domusia_desconto" label="Desconto"
                                        placeholder="0,00" class="col-md-3" />
                                    <x-tenant-currency name="valor_pago" id="domusia_valor_pago"
                                        label="Valor Pago" placeholder="0,00" class="col-md-3"
                                        :readonly="true" />
                                </div>
                            </div>
                        </div>

                        {{-- Card: Histórico Complementar --}}
                        <div class="card border border-gray-300 mb-5">
                            <div class="card-header min-h-45px">
                                <h3 class="card-title fs-6 fw-bold">Histórico Complementar</h3>
                            </div>
                            <div class="card-body px-6 py-5">
                                <textarea class="form-control form-control-sm" name="historico_complementar"
                                    id="domusia_historico" maxlength="500" rows="3"
                                    placeholder="Observações adicionais sobre o lançamento..."></textarea>
                                <span class="fs-8 text-muted mt-1 d-block">Máximo 500 caracteres</span>
                            </div>
                        </div>

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
                <span class="text-muted fs-8" id="domusia_drawer_status_text">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Preencha os campos obrigatórios para salvar
                </span>
                <x-tenant-button
                    type="button"
                    id="{{ $cancelId }}"
                    variant="light"
                    size="sm"
                    icon="fa-solid fa-xmark"
                    iconPosition="left"
                >
                    Cancelar
                </x-tenant-button>
            </div>

            {{-- Lado Direito: Salvar (Split Button) --}}
            <div class="d-flex">
                <x-tenant-split-button
                    submitId="{{ $submitId }}"
                    submitText="Salvar Lançamento"
                    submitIcon="fa-solid fa-check"
                    variant="primary"
                    size="sm"
                    direction="dropup"
                    :items="$splitItems"
                />
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
        min-height: 45px;
        padding: 0 1rem;
    }
    #domusia_expense_drawer .card-header .card-title {
        margin: 0;
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
            console.log('[DomusiaDrawer] Inicializado', this.drawerViewer ? 'com viewer' : 'SEM viewer');
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
        },

        // --------------------------------------------------------
        // Abrir drawer com dados
        // --------------------------------------------------------
        open(entryIndex, isReceita) {
            this.currentEntryIndex = entryIndex;
            this.isReceita = isReceita;

            const doc = window.currentDocument;
            const extractedData = doc?.dados_extraidos ?
                (typeof doc.dados_extraidos === 'string' ? JSON.parse(doc.dados_extraidos) : doc.dados_extraidos)
                : null;

            const item = extractedData?.itens?.[entryIndex] || null;
            this.currentEntryData = { doc, extractedData, item };

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
            if (this.el.tipo) this.el.tipo.value = isReceita ? 'entrada' : 'saida';
            if (this.el.title) this.el.title.textContent = isReceita ? 'Nova Receita' : 'Nova Despesa';
            if (this.el.typeIndicator) this.el.typeIndicator.style.backgroundColor = isReceita ? '#50cd89' : '#f1416c';
            if (this.el.pagoLabel) this.el.pagoLabel.textContent = isReceita ? 'Recebido' : 'Pago';
            if (this.el.pagoCheckbox) this.el.pagoCheckbox.name = isReceita ? 'recebido' : 'pago';
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

            const setText = (el, value) => { if (el) el.textContent = value || '-'; };

            setText(this.el.aiFornecedor, data.estabelecimento?.nome);
            setText(this.el.aiCnpj, this.formatCNPJ(data.estabelecimento?.cnpj));
            setText(this.el.aiPgto, data.financeiro?.forma_pagamento);
            setText(this.el.aiNumdoc, data.financeiro?.numero_documento || data.nfe_info?.numero_nf);

            if (this.el.aiValor && data.financeiro?.valor_total) {
                const val = parseFloat(data.financeiro.valor_total);
                this.el.aiValor.textContent = 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

            // Descrição
            const descricao = classif.descricao_detalhada ||
                              (item?.descricao ? item.descricao : null) ||
                              (estab.nome ? (data.tipo_documento || 'Documento') + ' - ' + estab.nome : '');
            this.setInputValue('domusia_descricao', descricao);

            // Valor — Para documentos de transação única (NF-e, Cupom, etc.),
            // usar sempre o valor_total do financeiro.
            // Para itens individuais, usar valor do item.
            const singleTxTypes = ['NF-e', 'NFC-e', 'CUPOM', 'CUPOM_FISCAL', 'NOTA_FISCAL', 'FATURA_CARTAO', 'BOLETO', 'RECIBO', 'COMPROVANTE'];
            const isSingleTx = singleTxTypes.includes(data.tipo_documento);

            let valor;
            if (isSingleTx) {
                valor = fin.valor_total || 0;
            } else {
                valor = item?.valor_unitario
                    ? (item.valor_unitario * (item.quantidade || 1))
                    : (fin.valor_total || 0);
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
            if (fin.desconto > 0) this.setInputValue('domusia_desconto', this.formatMoney(fin.desconto));

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

            // Match fornecedor, forma de pagamento e categoria por dados da IA
            if (estab.cnpj) this.matchFornecedorByCNPJ(estab.cnpj);
            if (fin.forma_pagamento) this.matchFormaPagamento(fin.forma_pagamento);
            if (classif.categoria_sugerida) this.matchCategoria(classif.categoria_sugerida);

            // Parcelamento
            if (data.parcelamento?.is_parcelado && data.parcelamento.total_parcelas > 1) {
                const parcSelect = document.getElementById('domusia_parcelamento');
                if (parcSelect) parcSelect.value = data.parcelamento.total_parcelas + 'x';
            }

            this.updateValorPago();
        },

        // --------------------------------------------------------
        // Helpers de match
        // --------------------------------------------------------
        matchFornecedorByCNPJ(cnpj) {
            const cleanCnpj = cnpj.replace(/\D/g, '');
            const select = document.getElementById('domusia_fornecedor_id');
            if (!select) return;

            const options = select.querySelectorAll('option');
            for (const opt of options) {
                const optCnpj = (opt.dataset.cnpj || '').replace(/\D/g, '');
                const optCpf = (opt.dataset.cpf || '').replace(/\D/g, '');
                if ((optCnpj && optCnpj === cleanCnpj) || (optCpf && optCpf === cleanCnpj)) {
                    $(select).val(opt.value).trigger('change');
                    console.log('[DomusiaDrawer] Fornecedor matched por CNPJ:', opt.textContent.trim());
                    return;
                }
            }
            console.log('[DomusiaDrawer] Nenhum fornecedor encontrado para CNPJ:', cnpj);
        },

        matchFormaPagamento(formaPgto) {
            if (!formaPgto) return;
            const select = document.getElementById('domusia_tipo_documento');
            if (!select) return;

            const search = formaPgto.toLowerCase().trim();
            const options = select.querySelectorAll('option');

            const mapping = {
                'dinheiro': ['dinheiro', 'especie'],
                'pix': ['pix'],
                'cartao de credito': ['credito', 'cartao de credito'],
                'cartao de debito': ['debito', 'cartao de debito'],
                'boleto': ['boleto', 'boleto bancario'],
                'transferencia': ['transferencia', 'ted', 'doc'],
                'cheque': ['cheque'],
            };

            for (const opt of options) {
                const optText = opt.textContent.toLowerCase().trim();
                if (optText.includes(search) || search.includes(optText.split(' - ').pop()?.trim())) {
                    $(select).val(opt.value).trigger('change');
                    console.log('[DomusiaDrawer] Forma de pagamento matched:', opt.textContent.trim());
                    return;
                }
                for (const [code, aliases] of Object.entries(mapping)) {
                    if (aliases.some(a => search.includes(a)) && optText.includes(code)) {
                        $(select).val(opt.value).trigger('change');
                        console.log('[DomusiaDrawer] Forma de pagamento matched (alias):', opt.textContent.trim());
                        return;
                    }
                }
            }
        },

        matchCategoria(categoriaSugerida) {
            if (!categoriaSugerida) return;
            const select = document.getElementById('domusia_lancamento_padrao_id');
            if (!select) return;

            const search = categoriaSugerida.toLowerCase().trim();
            const options = select.querySelectorAll('option');
            let bestMatch = null;
            let bestScore = 0;

            for (const opt of options) {
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
                if (score > bestScore) { bestScore = score; bestMatch = opt; }
            }

            if (bestMatch && bestScore >= 1) {
                $(select).val(bestMatch.value).trigger('change');
                console.log('[DomusiaDrawer] Categoria matched (parcial):', bestMatch.textContent.trim());
            }
        },

        // --------------------------------------------------------
        // Calcular valor_pago
        // --------------------------------------------------------
        updateValorPago() {
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

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && (result.success !== false)) {
                    this.showToast('success', 'Lançamento criado com sucesso!');
                    this.markEntryAsProcessed(this.currentEntryIndex);

                    if (openNew) {
                        this.resetForm();
                    } else {
                        this.close();
                    }
                } else {
                    const errors = result.errors;
                    if (errors) {
                        const messages = Object.values(errors).flat().join('<br>');
                        this.showToast('error', 'Erro de validação', messages);
                    } else {
                        this.showToast('error', result.message || 'Erro ao criar lançamento');
                    }
                }
            } catch (error) {
                console.error('[DomusiaDrawer] Erro no submit:', error);
                this.showToast('error', 'Erro ao processar', error.message);
            } finally {
                this.isSubmitting = false;
                this.setLoading(false);
            }
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
                badge.innerHTML = '<span class="badge badge-success fs-8"><i class="fa-solid fa-check me-1"></i>Lançado</span>';
                card.style.position = 'relative';
                card.appendChild(badge);
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
            if (this.el.form) this.el.form.reset();
            $('#domusia_fornecedor_id, #domusia_entidade_id, #domusia_lancamento_padrao_id, #domusia_cost_center_id, #domusia_tipo_documento')
                .val(null).trigger('change');
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
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
            }
        },

        formatMoney(value) {
            const num = parseFloat(value) || 0;
            return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        parseMoney(str) {
            if (!str) return 0;
            return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
        },

        formatCNPJ(cnpj) {
            if (!cnpj) return null;
            const clean = cnpj.replace(/\D/g, '');
            if (clean.length === 14) return clean.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
            if (clean.length === 11) return clean.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
            return cnpj;
        },

        showToast(icon, title, html) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: icon,
                    title: title,
                    html: html || '',
                    toast: icon === 'success',
                    position: icon === 'success' ? 'top-end' : 'center',
                    showConfirmButton: icon !== 'success',
                    timer: icon === 'success' ? 3000 : undefined,
                });
            } else {
                alert(title);
            }
        },
    };

    // Inicializar
    DomusiaDrawer.init();
    window.DomusiaDrawer = DomusiaDrawer;

    // ========================================================
    // Substituir a função createTransaction global
    // ========================================================
    window.createTransaction = function(index, isReceita) {
        console.log('[DomusiaDrawer] Criar transação - Item:', index, 'Tipo:', isReceita ? 'Receita' : 'Despesa');
        if (window.DomusiaDrawer) {
            window.DomusiaDrawer.open(index, isReceita);
        }
    };

    window.createExpense = function(index) {
        window.createTransaction(index, false);
    };
});
</script>
@endpush
