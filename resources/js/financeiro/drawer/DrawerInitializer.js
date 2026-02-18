/**
 * DrawerInitializer - Inicializador central do Drawer de Lançamento
 * 
 * Responsável por:
 * - Normalização de tipos de transação
 * - Atualização dinâmica de labels
 * - Inicialização de Select2 dentro do drawer
 * - Gerenciamento de estado visual
 * 
 * @module financeiro/drawer/DrawerInitializer
 * @version 2.0.0
 */

import { parseValorBrasileiro, formatarValorBrasileiro } from '../utils/currency.js';

/**
 * Configuração padrão
 */
const defaultConfig = {
    drawerId: 'kt_drawer_lancamento',
    formId: 'kt_drawer_lancamento_form',
    tipoInputId: 'tipo',
    tipoFinanceiroInputId: 'tipo_financeiro',
    fornecedorSelectId: 'fornecedor_id',
    fornecedorDrawerId: 'kt_drawer_fornecedor',
    recorrenciaDrawerId: 'kt_drawer_recorrencia',
    
    // Textos por tipo
    labels: {
        receita: {
            fornecedor: 'Cliente',
            placeholder: 'Selecione um cliente',
            addButton: 'Adicionar Cliente',
            drawerTitle: 'Novo Cliente',
            title: 'Nova Receita'
        },
        despesa: {
            fornecedor: 'Fornecedor',
            placeholder: 'Selecione um fornecedor',
            addButton: 'Adicionar Fornecedor',
            drawerTitle: 'Novo Fornecedor',
            title: 'Nova Despesa'
        }
    }
};

/**
 * Classe inicializadora do drawer
 */
export class DrawerInitializer {
    /**
     * @param {Object} config - Configurações customizadas
     */
    constructor(config = {}) {
        this.config = { ...defaultConfig, ...config };
        this._initialized = false;
        this._currentTipo = null;
    }

    /**
     * Inicializa o drawer
     */
    init() {
        if (this._initialized) return this;
        
        this._bindEvents();
        this._initialized = true;
        
        console.log('[DrawerInitializer] Inicializado');
        return this;
    }

    /**
     * Normaliza tipos de transação
     * @param {string} raw - entrada, saida, receita, despesa
     * @returns {string} receita ou despesa
     */
    normalizeTipo(raw) {
        if (!raw) return 'despesa';
        if (raw === 'entrada') return 'receita';
        if (raw === 'saida') return 'despesa';
        return raw;
    }

    /**
     * Obtém tipo atual do formulário
     * @returns {string}
     */
    getTipoAtual() {
        const $ = window.jQuery;
        const tipoInput = $(`#${this.config.tipoInputId}`);
        const tipoFinanceiroInput = $(`#${this.config.tipoFinanceiroInputId}`);
        
        if (tipoInput.length && tipoInput.val()) {
            return this.normalizeTipo(tipoInput.val());
        }
        if (tipoFinanceiroInput.length && tipoFinanceiroInput.val()) {
            return this.normalizeTipo(tipoFinanceiroInput.val());
        }
        return 'despesa';
    }

    /**
     * Atualiza labels baseado no tipo de transação
     * @param {string} tipo - receita ou despesa
     */
    updateLabels(tipo) {
        const $ = window.jQuery;
        tipo = this.normalizeTipo(tipo || this.getTipoAtual());
        this._currentTipo = tipo;
        

        const labels = this.config.labels[tipo] || this.config.labels.despesa;

        // Atualiza label do select de fornecedor
        const fornecedorSelect = $(`#${this.config.fornecedorSelectId}`);
        if (fornecedorSelect.length) {
            this._updateFornecedorLabel(fornecedorSelect, labels);

            // Limpa seleção se o parceiro atual não pertence mais ao filtro
            this._validarSelecaoFornecedor(fornecedorSelect, tipo);
        }

        // Atualiza título do drawer de fornecedor
        const fornecedorDrawerTitle = $(`#${this.config.fornecedorDrawerId} .card-title h3, #fornecedor_drawer_title`);
        if (fornecedorDrawerTitle.length) {
            fornecedorDrawerTitle.text(labels.drawerTitle);
        }

        // Armazena texto do botão para uso posterior
        window.fornecedorButtonText = labels.addButton;
    }

    /**
     * Abre o drawer para um tipo específico
     * @param {string} tipo - receita ou despesa
     * @param {string} origem - Banco ou Caixa
     */
    abrirDrawer(tipo, origem = 'Banco') {
        const $ = window.jQuery;
        tipo = this.normalizeTipo(tipo);
        
        console.log('[DrawerInitializer] Abrindo drawer para tipo:', tipo);

        // Limpa formulário antes
        if (typeof window.limparFormularioDrawerCompleto === 'function') {
            window.limparFormularioDrawerCompleto();
        }

        const drawer = $(`#${this.config.drawerId}`);
        const form = $(`#${this.config.formId}`);
        const drawerTitle = drawer.find('.card-title').first();
        const tipoFinanceiroInput = $(`#${this.config.tipoFinanceiroInputId}`);
        const tipoInput = $(`#${this.config.tipoInputId}`);
        const origemInput = $('#origem');

        // Define valores dos campos hidden
        const labels = this.config.labels[tipo];
        if (drawerTitle.length && labels) {
            drawerTitle.text(labels.title);
        }
        
        tipoFinanceiroInput.val(tipo);
        tipoInput.val(tipo === 'receita' ? 'entrada' : 'saida');
        this._currentTipo = tipo;

        // Atualiza labels
        this.updateLabels(tipo);

        // Atualiza origem
        if (origemInput.length) {
            origemInput.val(origem);
        }

        // Atualiza action do form
        const routeBase = origem === 'Caixa' ? '/caixa' : '/banco';
        form.attr('action', routeBase);

        // Abre o drawer
        const drawerElement = document.getElementById(this.config.drawerId);
        const drawerInstance = window.KTDrawer?.getInstance(drawerElement);
        
        if (drawerInstance) {
            drawerInstance.show();

            // Inicializa componentes após abrir
            setTimeout(() => {
                this.initSelect2();
                this.updateLabels(tipo);
                this.inicializarEstadoVisual();
            }, 300);
        }
    }

    /**
     * Inicializa Select2 nos selects do drawer
     */
    initSelect2() {
        const $ = window.jQuery;
        const self = this;
        const drawer = $(`#${this.config.drawerId}`);

        if (!drawer.length || typeof $.fn.select2 === 'undefined') return;

        const selects = drawer.find('select[data-control="select2"]');

        selects.each(function() {
            const $select = $(this);
            const selectId = $select.attr('id') || $select.attr('name');

            // Destroi se já inicializado
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            // Prepara placeholder
            let placeholder = $select.attr('data-placeholder') || 'Selecione';
            if (selectId === self.config.fornecedorSelectId) {
                const tipo = self.getTipoAtual();
                const labels = self.config.labels[tipo];
                placeholder = labels?.placeholder || placeholder;
            }

            const options = {
                dropdownParent: drawer,
                placeholder,
                allowClear: $select.attr('data-allow-clear') === 'true',
                minimumResultsForSearch: $select.attr('data-hide-search') === 'true' ? Infinity : 0,
                width: '100%',
                theme: 'bootstrap5'
            };

            // Matcher personalizado para filtrar parceiros por natureza
            if (selectId === self.config.fornecedorSelectId) {

                options.matcher = function(params, data) {
                    // Sempre exibe a option placeholder
                    if (!data.id) return data;

                    const tipo = self._currentTipo || self.getTipoAtual();
                    const naturezasPermitidas = tipo === 'receita'
                        ? ['cliente', 'ambos']
                        : ['fornecedor', 'ambos'];

                    const natureza = (data.element?.getAttribute('data-natureza') || '').toLowerCase();


                    // Filtra por natureza
                    if (!naturezasPermitidas.includes(natureza)) return null;

                    // Aplica filtro de texto (busca do usuário)
                    if (!params.term || params.term.trim() === '') return data;

                    const term = params.term.toLowerCase();
                    if (data.text.toLowerCase().indexOf(term) > -1) return data;

                    return null;
                };
            }

            // Template para entidade_id (ícones)
            if (selectId === 'entidade_id') {
                const formatWithIcon = (item) => {
                    if (!item.id) return item.text;
                    const iconUrl = item.element?.getAttribute('data-kt-select2-icon');
                    if (!iconUrl) return item.text;
                    return $(`<span><img src="${iconUrl}" class="rounded h-20px me-2" alt="icon"/>${item.text}</span>`);
                };
                options.templateSelection = formatWithIcon;
                options.templateResult = formatWithIcon;
            }

            try {
                $select.select2(options);

                // Adiciona botão de adicionar para fornecedor
                if (selectId === self.config.fornecedorSelectId) {
                    self._addFornecedorButton($select);
                }

                // Adiciona botão para configuração de recorrência
                if (selectId === 'configuracao_recorrencia') {
                    self._addRecorrenciaButton($select);
                }
            } catch (error) {
                console.error('[DrawerInitializer] Erro ao inicializar Select2:', error);
            }
        });
    }

    /**
     * Inicializa estado visual do drawer
     */
    inicializarEstadoVisual() {
        const $ = window.jQuery;
        
        // Reinicializa tooltips se a função existir
        if (typeof window.initializeDrawerTooltips === 'function') {
            window.initializeDrawerTooltips();
        }

        // Atualiza visibilidade de checkboxes
        if (typeof window.toggleCheckboxesByTipo === 'function') {
            window.toggleCheckboxesByTipo();
        }
        if (typeof window.toggleCheckboxPago === 'function') {
            window.toggleCheckboxPago();
        }
    }

    /**
     * Limpa completamente o formulário
     */
    limparFormulario() {
        const $ = window.jQuery;
        const form = $(`#${this.config.formId}`);
        
        if (!form.length) return;

        // Reset nativo
        form[0].reset();

        // Limpa Select2
        if ($.fn.select2) {
            const selectsToReset = [
                '#entidade_id', '#lancamento_padraos_id', '#tipo_documento',
                '#fornecedor_id', '#cost_center_id', '#parcelamento',
                '#configuracao_recorrencia'
            ];
            $(selectsToReset.join(', ')).val(null).trigger('change');
        }

        // Esconde accordions
        $('#kt_accordion_previsao_pagamento, #kt_accordion_parcelas, #kt_accordion_informacoes_pagamento').hide();

        // Limpa tabela de parcelas
        $('#parcelas_table_body').empty();

        // Limpa erros
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * Valida se o parceiro atualmente selecionado é compatível com o tipo.
     * Se não for, limpa a seleção. O filtro visual fica por conta do matcher do Select2.
     * 
     * @private
     * @param {jQuery} $select - O elemento select do fornecedor
     * @param {string} tipo - 'receita' ou 'despesa'
     */
    _validarSelecaoFornecedor($select, tipo) {
        const valorAtual = $select.val();
        if (!valorAtual) return;

        const naturezasPermitidas = tipo === 'receita'
            ? ['cliente', 'ambos']
            : ['fornecedor', 'ambos'];

        const $selectedOption = $select.find(`option[value="${valorAtual}"]`);
        const natureza = ($selectedOption.attr('data-natureza') || '').toLowerCase();

        if (!naturezasPermitidas.includes(natureza)) {
            $select.val(null).trigger('change');
        }
    }

    /**
     * @private
     */
    _bindEvents() {
        const $ = window.jQuery;
        const self = this;

        // Mudança de tipo
        $(document).on('change', `#${this.config.tipoInputId}, #${this.config.tipoFinanceiroInputId}`, function() {
            const tipo = self.getTipoAtual();
            self.updateLabels(tipo);
        });

        // Quando drawer abrir
        $(document).on('kt.drawer.show', `#${this.config.drawerId}`, function() {
            setTimeout(() => {
                self.initSelect2();
                self.inicializarEstadoVisual();
            }, 200);
        });
    }

    /**
     * @private
     */
    _updateFornecedorLabel(fornecedorSelect, labels) {
        const $ = window.jQuery;
        
        // Atualiza label
        let labelElement = $(`label[for="${this.config.fornecedorSelectId}"]`);
        if (!labelElement.length) {
            labelElement = fornecedorSelect.closest('.fv-row, .col-md-4, .col-md-6').find('label').first();
        }

        if (labelElement.length) {
            const requiredSpan = labelElement.find('span.required');
            if (requiredSpan.length) {
                requiredSpan.text(labels.fornecedor);
            } else if (labelElement.hasClass('required')) {
                labelElement.html(`<span class="required">${labels.fornecedor}</span>`);
            } else {
                labelElement.text(labels.fornecedor);
            }
        }

        // Atualiza placeholder
        fornecedorSelect.attr('data-placeholder', labels.placeholder);
        if (fornecedorSelect.hasClass('select2-hidden-accessible')) {
            const $container = fornecedorSelect.next('.select2-container');
            const $placeholder = $container.find('.select2-selection__placeholder');
            if ($placeholder.length && !fornecedorSelect.val()) {
                $placeholder.text(labels.placeholder);
            }
        }
    }

    /**
     * @private
     */
    _addFornecedorButton($select) {
        const $ = window.jQuery;
        const self = this;

        $select.off('select2:open.drawerInit').on('select2:open.drawerInit', function() {
            setTimeout(() => {
                const $dropdown = $('.select2-container--open');
                const $results = $dropdown.find('.select2-results');
                if (!$results.length) return;

                // Remove botão anterior
                $results.find('.select2-add-fornecedor-footer').remove();

                const tipo = self.getTipoAtual();
                const buttonText = self.config.labels[tipo]?.addButton || 'Adicionar';

                const $footer = $('<div class="select2-add-fornecedor-footer border-top p-2 text-center"></div>');
                const $button = $(`<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus"></i> ${buttonText}</button>`);
                
                $footer.append($button);
                $results.append($footer);

                $button.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $select.select2('close');

                    // Define tipo no drawer de fornecedor
                    const parceiroTipo = tipo === 'receita' ? 'cliente' : 'fornecedor';
                    $('#parceiro_tipo_hidden').val(parceiroTipo);
                    window.__drawerTargetSelect = `#${self.config.fornecedorSelectId}`;

                    // Abre drawer de fornecedor
                    const fornecedorDrawer = document.getElementById(self.config.fornecedorDrawerId);
                    const drawerInstance = window.KTDrawer?.getInstance(fornecedorDrawer);
                    if (drawerInstance) {
                        drawerInstance.show();
                    }
                });
            }, 50);
        });
    }

    /**
     * @private
     */
    _addRecorrenciaButton($select) {
        const $ = window.jQuery;
        const self = this;

        $select.off('select2:open.drawerInit').on('select2:open.drawerInit', function() {
            setTimeout(() => {
                const $dropdown = $('.select2-container--open');
                const $results = $dropdown.find('.select2-results');
                if (!$results.length) return;

                // Remove botão anterior
                $results.find('.select2-add-recorrencia-footer').remove();

                const $footer = $('<div class="select2-add-recorrencia-footer border-top p-2 text-center"></div>');
                const $button = $('<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus"></i> Adicionar Configuração de Recorrência</button>');
                
                $footer.append($button);
                $results.append($footer);

                $button.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $select.select2('close');

                    const recorrenciaDrawer = document.getElementById(self.config.recorrenciaDrawerId);
                    const drawerInstance = window.KTDrawer?.getInstance(recorrenciaDrawer);
                    if (drawerInstance) {
                        drawerInstance.show();
                    }
                });
            }, 50);
        });
    }
}

// Instância singleton
let instance = null;

/**
 * Obtém ou cria instância do inicializador
 * @param {Object} config - Configurações opcionais
 * @returns {DrawerInitializer}
 */
export function getDrawerInitializer(config = {}) {
    if (!instance) {
        instance = new DrawerInitializer(config);
    }
    return instance;
}

// Expor globalmente para compatibilidade
if (typeof window !== 'undefined') {
    window.DrawerInitializer = DrawerInitializer;
    window.getDrawerInitializer = getDrawerInitializer;
    
    // Funções legadas para compatibilidade
    window.normalizeTipo = function(raw) {
        return getDrawerInitializer().normalizeTipo(raw);
    };
    
    window.updateFornecedorLabels = function(tipo) {
        getDrawerInitializer().updateLabels(tipo);
    };
    
    window.inicializarEstadoDrawer = function() {
        getDrawerInitializer().inicializarEstadoVisual();
    };
}
