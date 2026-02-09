{{-- Drawer para criar lançamento a partir de documento Domusia --}}
<div id="domusia_expense_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="domusia-expense"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
    data-kt-drawer-width="100%" data-kt-drawer-toggle="#domusia_expense_drawer_toggle"
    data-kt-drawer-close="#domusia_drawer_close">

    <div class="d-flex flex-column h-100">

        <!--begin::Header-->
        <div class="d-flex align-items-center justify-content-between px-6 py-4 border-bottom bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="bullet bullet-vertical h-30px" id="domusia_drawer_type_indicator"
                    style="background-color: #f1416c;"></span>
                <div>
                    <h3 class="fw-bold mb-0 fs-4" id="domusia_drawer_title">Nova Despesa</h3>
                    <span class="text-muted fs-7" id="domusia_drawer_subtitle">Preencha os dados do lançamento</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- Badge do documento de origem --}}
                <span class="badge badge-light-primary fs-8" id="domusia_drawer_doc_badge">
                    <i class="fa-solid fa-file-invoice fs-8 me-1"></i>
                    <span id="domusia_drawer_doc_type">Documento</span>
                </span>
                <button type="button" class="btn btn-sm btn-icon btn-light-danger" id="domusia_drawer_close">
                    <i class="fa-solid fa-xmark fs-3"></i>
                </button>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="flex-grow-1 overflow-hidden">
            <div class="row g-0 h-100">

                <!--begin::Col Esquerda - Visualizador de Documento-->
                <div class="col-xl-5 border-end h-100 position-relative" style="background-color: #525659;">
                    <div id="domusia_drawer_viewer" class="w-100 h-100 d-flex align-items-center justify-content-center">
                        {{-- Empty State --}}
                        <div id="domusia_drawer_empty_state" class="text-center text-white p-5">
                            <i class="fa-solid fa-file-circle-question fs-3x mb-4 opacity-50"></i>
                            <p class="fs-6 opacity-75">Nenhum documento carregado</p>
                        </div>
                        {{-- PDF Viewer --}}
                        <iframe id="domusia_drawer_pdf" class="w-100 h-100 border-0" style="display: none;"></iframe>
                        {{-- Image Viewer --}}
                        <img id="domusia_drawer_img" class="mw-100 mh-100 object-fit-contain" style="display: none;" alt="Documento" />
                    </div>
                </div>
                <!--end::Col Esquerda-->

                <!--begin::Col Direita - Formulário-->
                <div class="col-xl-7 h-100 overflow-y-auto bg-light">
                    <form id="domusia_drawer_form" method="POST" action="{{ route('transacoes-financeiras.store') }}" enctype="multipart/form-data">
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
                                    {{-- Linha 1: Fornecedor, Data, Descrição, Valor --}}
                                    <div class="row g-4 mb-5">
                                        <x-tenant-select name="fornecedor_id" id="domusia_fornecedor_id" label="Fornecedor"
                                            placeholder="Selecione um fornecedor" :minimumResultsForSearch="0"
                                            dropdown-parent="#domusia_expense_drawer" labelSize="fs-7" class="col-md-4">
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

                                        <x-tenant-date name="data_competencia" id="domusia_data_competencia"
                                            label="Data de Competência" placeholder="Informe a data" required
                                            class="col-md-2" />

                                        <x-tenant-input name="descricao" id="domusia_descricao" label="Descrição"
                                            placeholder="Informe a descrição" required class="col-md-4" />

                                        <x-tenant-currency name="valor" id="domusia_valor" label="Valor"
                                            placeholder="0,00" tooltip="Valor total do documento" class="col-md-2"
                                            required />
                                    </div>

                                    {{-- Linha 2: Entidade, Categoria, Centro de Custo --}}
                                    <div class="row g-4 mb-5">
                                        <x-tenant-select name="entidade_id" id="domusia_entidade_id"
                                            label="Entidade Financeira" required :hideSearch="true"
                                            dropdown-parent="#domusia_expense_drawer" class="col-md-4">
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
                                            dropdown-parent="#domusia_expense_drawer" labelSize="fs-7" class="col-md-4">
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

                                        <x-tenant-select name="cost_center_id" id="domusia_cost_center_id"
                                            label="Centro de Custo" :allowClear="true" required
                                            placeholder="Selecione um centro de custo" :minimumResultsForSearch="0"
                                            dropdown-parent="#domusia_expense_drawer" labelSize="fs-7" class="col-md-4">
                                            @if (isset($centrosAtivos))
                                                @foreach ($centrosAtivos as $centro)
                                                    <option value="{{ $centro->id }}">
                                                        {{ $centro->code }} - {{ $centro->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </x-tenant-select>
                                    </div>

                                    {{-- Linha 3: Forma de pagamento, Número documento, Comprovação --}}
                                    <div class="row g-4">
                                        <x-tenant-select name="tipo_documento" id="domusia_tipo_documento"
                                            label="Forma de Pagamento" placeholder="Selecione..." required
                                            :allowClear="true" :minimumResultsForSearch="0"
                                            dropdown-parent="#domusia_expense_drawer" labelSize="fs-7" class="col-md-4">
                                            @if (isset($formasPagamento))
                                                @foreach ($formasPagamento as $fp)
                                                    <option value="{{ $fp->codigo }}">
                                                        {{ $fp->id }} - {{ $fp->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </x-tenant-select>

                                        <x-tenant-input name="numero_documento" id="domusia_numero_documento"
                                            label="Número do Documento" placeholder="Nº NF / Nº Recibo"
                                            type="text" class="col-md-4" />

                                        <div class="col-md-4 fv-row d-flex align-items-end pb-2">
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

                            {{-- Card: Histórico e Observações --}}
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
        </div>
        <!--end::Body-->

        <!--begin::Footer-->
        <div class="d-flex align-items-center justify-content-between px-6 py-4 border-top bg-white">
            <div>
                <span class="text-muted fs-8" id="domusia_drawer_status_text">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Preencha os campos obrigatórios para salvar
                </span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light" id="domusia_drawer_cancel">
                    <i class="fa-solid fa-xmark me-1"></i> Cancelar
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-primary" id="domusia_drawer_submit">
                        <span class="indicator-label">
                            <i class="fa-solid fa-check me-1"></i> Salvar Lançamento
                        </span>
                        <span class="indicator-progress" style="display: none;">
                            Salvando... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item fs-7" href="#" id="domusia_drawer_submit_new">
                            <i class="fa-solid fa-plus me-2"></i> Salvar e Novo</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end::Footer-->

    </div>
</div>

@push('styles')
<style>
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
    #domusia_drawer_img {
        max-width: 95%;
        max-height: 95%;
        object-fit: contain;
        border-radius: 4px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .drawer-overlay[data-kt-drawer-name="domusia-expense"] {
        z-index: 1059;
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
            docType: document.getElementById('domusia_drawer_doc_type'),
            submitBtn: document.getElementById('domusia_drawer_submit'),
            submitNewBtn: document.getElementById('domusia_drawer_submit_new'),
            cancelBtn: document.getElementById('domusia_drawer_cancel'),
            closeBtn: document.getElementById('domusia_drawer_close'),
            statusText: document.getElementById('domusia_drawer_status_text'),
            // Viewer
            viewer: document.getElementById('domusia_drawer_viewer'),
            emptyState: document.getElementById('domusia_drawer_empty_state'),
            pdfViewer: document.getElementById('domusia_drawer_pdf'),
            imgViewer: document.getElementById('domusia_drawer_img'),
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
            this.bindEvents();
            console.log('[DomusiaDrawer] Inicializado');
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
            if (!doc) {
                if (this.el.emptyState) this.el.emptyState.style.display = 'block';
                if (this.el.pdfViewer) this.el.pdfViewer.style.display = 'none';
                if (this.el.imgViewer) this.el.imgViewer.style.display = 'none';
                return;
            }

            if (this.el.emptyState) this.el.emptyState.style.display = 'none';

            const isPdf = doc.mime_type === 'application/pdf';
            const fileUrl = doc.file_url;

            if (isPdf) {
                if (this.el.pdfViewer && fileUrl) {
                    this.el.pdfViewer.src = fileUrl + '#toolbar=1&navpanes=0&scrollbar=1';
                    this.el.pdfViewer.style.display = 'block';
                }
                if (this.el.imgViewer) this.el.imgViewer.style.display = 'none';
            } else {
                if (this.el.imgViewer && fileUrl) {
                    this.el.imgViewer.src = fileUrl;
                    this.el.imgViewer.style.display = 'block';
                }
                if (this.el.pdfViewer) this.el.pdfViewer.style.display = 'none';
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
                    this.el.aiData.textContent = `${parts[2]}/${parts[1]}/${parts[0]}`;
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
                    const formatted = `${parts[2]}/${parts[1]}/${parts[0]}`;
                    this.setInputValue('domusia_data_competencia', formatted);
                    this.setInputValue('domusia_vencimento', formatted);
                }
            }

            // Descrição
            const descricao = classif.descricao_detalhada ||
                              (item?.descricao ? item.descricao : null) ||
                              (estab.nome ? `${data.tipo_documento || 'Documento'} - ${estab.nome}` : '');
            this.setInputValue('domusia_descricao', descricao);

            // Valor - usar valor do item individual OU valor_total do financeiro
            const valor = item?.valor_unitario
                ? (item.valor_unitario * (item.quantidade || 1))
                : (fin.valor_total || 0);
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

            // Histórico complementar
            const obs = [];
            if (data.observacoes) obs.push(data.observacoes);
            if (fin.observacoes_financeiras) obs.push(fin.observacoes_financeiras);
            if (obs.length > 0) {
                this.setInputValue('domusia_historico', obs.join(' | '));
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
                'cartao de credito': ['credito', 'cartao de credito', 'cartão de crédito'],
                'cartao de debito': ['debito', 'cartao de debito', 'cartão de débito'],
                'boleto': ['boleto', 'boleto bancario', 'boleto bancário'],
                'transferencia': ['transferencia', 'transferência', 'ted', 'doc'],
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
            const card = document.querySelector(`[data-entry-index="${index}"]`);
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

            if (this.el.pdfViewer) this.el.pdfViewer.src = '';
            if (this.el.imgViewer) this.el.imgViewer.src = '';
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
            if (loading) {
                btn.querySelector('.indicator-label').style.display = 'none';
                btn.querySelector('.indicator-progress').style.display = 'inline-block';
                btn.disabled = true;
            } else {
                btn.querySelector('.indicator-label').style.display = 'inline-block';
                btn.querySelector('.indicator-progress').style.display = 'none';
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
