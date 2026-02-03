/**
 * Controlador de Parcelas do Drawer de Lançamento Financeiro
 * 
 * Responsável por:
 * - Geração de tabela de parcelas
 * - Recálculo automático de valores e percentuais
 * - Validação de soma de parcelas
 * - Inicialização de máscaras e datepickers
 * 
 * @module financeiro/drawer/DrawerParcelasController
 */

import {
    parseValorBrasileiro,
    formatarValorBrasileiro,
    paraCentavos,
    paraReais,
    formatarCentavos,
    dividirEmParcelas,
    calcularPercentual,
    aplicarPercentual
} from '../utils/currency.js';

/**
 * Configuração padrão do controlador
 */
const defaultConfig = {
    selectors: {
        valorTotal: '#valor2',
        vencimento: '#vencimento',
        descricao: '#descricao',
        parcelasTableBody: '#parcelas_table_body',
        parcelaRowTemplate: '#parcela-row-template',
        accordion: '#kt_accordion_parcelas',
        dropdownParent: '#kt_drawer_lancamento'
    },
    dateFormat: 'd/m/Y',
    locale: 'pt'
};

/**
 * Classe controladora de parcelas
 */
export class DrawerParcelasController {
    /**
     * @param {Object} config - Configurações customizadas
     */
    constructor(config = {}) {
        this.config = { ...defaultConfig, ...config };
        this.selectors = { ...defaultConfig.selectors, ...config.selectors };
        this.numParcelas = 0;
        this._initialized = false;
    }

    /**
     * Inicializa o controlador
     */
    init() {
        if (this._initialized) return;
        
        this._bindEvents();
        this._initialized = true;
        
        console.log('[DrawerParcelasController] Inicializado');
    }

    /**
     * Vincula eventos globais
     * @private
     */
    _bindEvents() {
        const self = this;
        const $ = window.jQuery;

        // Eventos de recálculo são delegados ao tbody
        $(document).on('input change blur', 
            `${this.selectors.parcelasTableBody} input[data-parcela-input="valor"]`, 
            function() {
                const parcelaNum = parseInt($(this).attr('data-parcela-num'));
                if (parcelaNum !== self.numParcelas) {
                    self.recalcularPorValor(parcelaNum);
                }
            }
        );

        $(document).on('input change blur', 
            `${this.selectors.parcelasTableBody} input[data-parcela-input="percentual"]`, 
            function() {
                const parcelaNum = parseInt($(this).attr('data-parcela-num'));
                if (parcelaNum !== self.numParcelas) {
                    self.recalcularPorPercentual(parcelaNum);
                }
            }
        );
    }

    /**
     * Gera as linhas da tabela de parcelas
     * @param {string} parcelamento - Valor do parcelamento (ex: "3x")
     */
    gerarParcelas(parcelamento) {
        const $ = window.jQuery;
        
        // Extrai o número de parcelas (ex: "2x" -> 2)
        const numParcelas = parseInt(parcelamento.replace('x', ''));
        if (isNaN(numParcelas) || numParcelas < 2) {
            return;
        }

        this.numParcelas = numParcelas;
        const tbody = $(this.selectors.parcelasTableBody);
        tbody.empty();

        // Obtém dados base
        const valorTotal = this._getValorTotal();
        const valorTotalCentavos = paraCentavos(valorTotal);
        const parcelasCentavos = dividirEmParcelas(valorTotalCentavos, numParcelas);
        const percentualBase = Math.floor((10000 / numParcelas)) / 100;
        const dataBase = this._getDataBase();
        const descricaoBase = $(this.selectors.descricao).val() || '';

        let somaPercentuais = 0;

        // Gera uma linha para cada parcela
        for (let i = 1; i <= numParcelas; i++) {
            const dataVencimento = this._calcularDataVencimento(dataBase, i - 1);
            const valorParcelaCentavos = parcelasCentavos[i - 1];
            const valorParcela = paraReais(valorParcelaCentavos);

            // Calcula percentual
            let percentualParcela;
            if (i === numParcelas) {
                percentualParcela = (100 - somaPercentuais).toFixed(2);
            } else {
                percentualParcela = percentualBase.toFixed(2);
                somaPercentuais += parseFloat(percentualParcela);
            }

            const valorFormatado = formatarValorBrasileiro(valorParcela);
            const isUltimaParcela = (i === numParcelas);

            // Cria a linha usando template
            const row = this._criarLinhaParcela({
                numero: i,
                numParcelas,
                dataVencimento,
                valorFormatado,
                percentualParcela,
                descricaoBase,
                isUltimaParcela
            });

            if (row) {
                tbody.append(row);
            }
        }

        // Inicializa componentes após inserção no DOM
        this._inicializarComponentes(tbody, numParcelas);
    }

    /**
     * Recalcula valores quando um valor de parcela é alterado
     * @param {number} parcelaAlterada - Número da parcela alterada
     */
    recalcularPorValor(parcelaAlterada) {
        const $ = window.jQuery;
        const valorTotal = this._getValorTotal();
        
        if (valorTotal <= 0) return;

        const valorTotalCentavos = paraCentavos(valorTotal);
        let somaCentavos = 0;

        // Soma valores das parcelas editáveis (exceto última)
        for (let i = 1; i < this.numParcelas; i++) {
            const valorStr = $(`input[data-parcela-input="valor"][data-parcela-num="${i}"]`).val() || '0';
            somaCentavos += paraCentavos(parseValorBrasileiro(valorStr));
        }

        // Valida se não excede o total
        if (somaCentavos > valorTotalCentavos) {
            this._mostrarErroValor(parcelaAlterada, valorTotal);
            return;
        }

        this._limparErrosValor();

        // Calcula última parcela
        const valorUltimaCentavos = valorTotalCentavos - somaCentavos;

        if (valorUltimaCentavos <= 0) {
            this._mostrarErroUltimaParcela();
            return;
        }

        // Atualiza última parcela
        const valorUltimaFormatado = formatarCentavos(valorUltimaCentavos);
        $(`input[data-parcela-input="valor"][data-parcela-num="${this.numParcelas}"]`)
            .val(valorUltimaFormatado)
            .removeClass('is-invalid');

        // Recalcula percentuais
        this._recalcularPercentuais(valorTotalCentavos);
    }

    /**
     * Recalcula valores quando um percentual é alterado
     * @param {number} parcelaAlterada - Número da parcela alterada
     */
    recalcularPorPercentual(parcelaAlterada) {
        const $ = window.jQuery;
        const valorTotal = this._getValorTotal();
        
        if (valorTotal <= 0) return;

        const valorTotalCentavos = paraCentavos(valorTotal);
        let somaPercentuais = 0;

        // Soma percentuais das parcelas editáveis
        for (let i = 1; i < this.numParcelas; i++) {
            const percentualStr = $(`input[data-parcela-input="percentual"][data-parcela-num="${i}"]`).val() || '0';
            somaPercentuais += parseFloat(percentualStr) || 0;
        }

        // Garante que não exceda 100%
        if (somaPercentuais > 100) {
            const percentualAtual = parseFloat(
                $(`input[data-parcela-input="percentual"][data-parcela-num="${parcelaAlterada}"]`).val()
            ) || 0;
            const diferenca = somaPercentuais - 100;
            const novoPercentual = Math.max(0, percentualAtual - diferenca);
            
            $(`input[data-parcela-input="percentual"][data-parcela-num="${parcelaAlterada}"]`)
                .val(novoPercentual.toFixed(2));
            somaPercentuais = 100;
        }

        // Calcula percentual da última parcela
        const percentualUltima = 100 - somaPercentuais;
        $(`input[data-parcela-input="percentual"][data-parcela-num="${this.numParcelas}"]`)
            .val(percentualUltima.toFixed(2));

        // Recalcula valores baseado nos percentuais
        const valoresCentavos = [];
        let somaParciaisCentavos = 0;

        for (let i = 1; i <= this.numParcelas; i++) {
            const percentualStr = $(`input[data-parcela-input="percentual"][data-parcela-num="${i}"]`).val() || '0';
            const percentual = parseFloat(percentualStr) || 0;

            let valorCentavos;
            if (i === this.numParcelas) {
                valorCentavos = valorTotalCentavos - somaParciaisCentavos;
            } else {
                valorCentavos = aplicarPercentual(valorTotalCentavos, percentual);
                somaParciaisCentavos += valorCentavos;
            }
            valoresCentavos.push(valorCentavos);
        }

        // Aplica valores formatados
        for (let i = 1; i <= this.numParcelas; i++) {
            $(`input[data-parcela-input="valor"][data-parcela-num="${i}"]`)
                .val(formatarCentavos(valoresCentavos[i - 1]));
        }
    }

    /**
     * Limpa a tabela de parcelas
     */
    limpar() {
        const $ = window.jQuery;
        $(this.selectors.parcelasTableBody).empty();
        this.numParcelas = 0;
    }

    // ==================== MÉTODOS PRIVADOS ====================

    /**
     * @private
     */
    _getValorTotal() {
        const $ = window.jQuery;
        const valorStr = $(this.selectors.valorTotal).val() || '0';
        return parseValorBrasileiro(valorStr);
    }

    /**
     * @private
     */
    _getDataBase() {
        const $ = window.jQuery;
        const dataVencimentoBase = $(this.selectors.vencimento).val();
        let dataBase = null;

        if (dataVencimentoBase) {
            const partes = dataVencimentoBase.split('/');
            if (partes.length === 3) {
                dataBase = new Date(partes[2], partes[1] - 1, partes[0]);
            }
        }

        if (!dataBase || isNaN(dataBase.getTime())) {
            dataBase = new Date();
        }

        return dataBase;
    }

    /**
     * @private
     */
    _calcularDataVencimento(dataBase, mesesAdicionar) {
        const data = new Date(dataBase);
        data.setMonth(dataBase.getMonth() + mesesAdicionar);
        
        return String(data.getDate()).padStart(2, '0') + '/' +
               String(data.getMonth() + 1).padStart(2, '0') + '/' +
               data.getFullYear();
    }

    /**
     * @private
     */
    _criarLinhaParcela(dados) {
        const template = document.getElementById(this.selectors.parcelaRowTemplate.replace('#', ''));
        if (!template) {
            console.error('[DrawerParcelasController] Template de parcela não encontrado');
            return null;
        }

        const row = template.content.cloneNode(true);
        const tr = row.querySelector('tr');
        
        tr.setAttribute('data-parcela', dados.numero);
        tr.querySelector('.parcela-numero').textContent = dados.numero;

        // Vencimento
        const inputVencimento = tr.querySelector('.parcela-vencimento');
        inputVencimento.value = dados.dataVencimento;
        inputVencimento.name = `parcelas[${dados.numero}][vencimento]`;
        inputVencimento.setAttribute('data-parcela-num', dados.numero);

        // Valor
        const inputValor = tr.querySelector('.parcela-valor');
        inputValor.value = dados.valorFormatado;
        inputValor.name = `parcelas[${dados.numero}][valor]`;
        inputValor.setAttribute('data-parcela-num', dados.numero);

        if (dados.isUltimaParcela) {
            inputValor.classList.add('bg-light');
            inputValor.readOnly = true;
            inputValor.style.cursor = 'not-allowed';
        }

        // Percentual
        const inputPercentual = tr.querySelector('.parcela-percentual');
        inputPercentual.value = dados.percentualParcela;
        inputPercentual.name = `parcelas[${dados.numero}][percentual]`;
        inputPercentual.setAttribute('data-parcela-num', dados.numero);

        if (dados.isUltimaParcela) {
            inputPercentual.classList.add('bg-light');
            inputPercentual.readOnly = true;
            inputPercentual.style.cursor = 'not-allowed';
        }

        // Forma de Pagamento
        const selectFormaPagamento = tr.querySelector('.parcela-forma-pagamento');
        selectFormaPagamento.name = `parcelas[${dados.numero}][forma_pagamento_id]`;
        selectFormaPagamento.setAttribute('data-parcela-num', dados.numero);

        // Conta de Pagamento
        const selectContaPagamento = tr.querySelector('.parcela-conta-pagamento');
        selectContaPagamento.name = `parcelas[${dados.numero}][conta_pagamento_id]`;
        selectContaPagamento.setAttribute('data-parcela-num', dados.numero);

        // Descrição
        const inputDescricao = tr.querySelector('.parcela-descricao');
        inputDescricao.value = `${dados.descricaoBase} ${dados.numero}/${dados.numParcelas}`;
        inputDescricao.name = `parcelas[${dados.numero}][descricao]`;
        inputDescricao.setAttribute('data-parcela-num', dados.numero);
        inputDescricao.setAttribute('data-descricao-base', dados.descricaoBase);

        // Agendado
        const inputAgendado = tr.querySelector('.parcela-agendado');
        inputAgendado.name = `parcelas[${dados.numero}][agendado]`;
        inputAgendado.setAttribute('data-parcela-num', dados.numero);

        return row;
    }

    /**
     * @private
     */
    _inicializarComponentes(tbody, numParcelas) {
        const self = this;
        const $ = window.jQuery;

        setTimeout(() => {
            // Flatpickr para datas
            if (typeof flatpickr !== 'undefined') {
                tbody.find('input[data-parcela-input="vencimento"]').each(function() {
                    if (!$(this).data('flatpickr-initialized')) {
                        flatpickr(this, {
                            enableTime: false,
                            dateFormat: self.config.dateFormat,
                            locale: self.config.locale,
                            allowInput: true,
                            clickOpens: true
                        });
                        $(this).data('flatpickr-initialized', true);
                    }
                });
            }

            // Inputmask para valores
            if (typeof Inputmask !== 'undefined') {
                tbody.find('input[data-parcela-input="valor"]').each(function() {
                    if (!$(this).attr('readonly') && !$(this).data('mask-initialized')) {
                        Inputmask({
                            alias: "currency",
                            groupSeparator: ".",
                            radixPoint: ",",
                            autoGroup: true,
                            digits: 2,
                            digitsOptional: false,
                            placeholder: "0,00",
                            rightAlign: false,
                            removeMaskOnSubmit: false,
                            allowMinus: false,
                            clearMaskOnLostFocus: false
                        }).mask(this);
                        $(this).data('mask-initialized', true);
                    }
                });

                // Inputmask para percentuais
                tbody.find('input[data-parcela-input="percentual"]').each(function() {
                    if (!$(this).attr('readonly') && !$(this).data('mask-initialized')) {
                        Inputmask({
                            alias: "decimal",
                            groupSeparator: "",
                            radixPoint: ".",
                            autoGroup: false,
                            digits: 2,
                            digitsOptional: false,
                            placeholder: "0.00",
                            rightAlign: false,
                            allowMinus: false,
                            min: 0,
                            max: 100
                        }).mask(this);
                        $(this).data('mask-initialized', true);
                    }
                });
            }

            // Select2 para selects
            tbody.find('select[data-parcela-input="forma_pagamento"], select[data-parcela-input="conta_pagamento"]').each(function() {
                const $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) return;

                try {
                    $select.select2({
                        dropdownParent: $(self.selectors.dropdownParent),
                        placeholder: $select.attr('data-placeholder') || 'Selecione',
                        allowClear: $select.attr('data-allow-clear') === 'true',
                        minimumResultsForSearch: 0,
                        theme: 'bootstrap5'
                    });
                } catch (error) {
                    console.error('[DrawerParcelasController] Erro ao inicializar Select2:', error);
                }
            });
        }, 100);
    }

    /**
     * @private
     */
    _recalcularPercentuais(valorTotalCentavos) {
        const $ = window.jQuery;
        let somaPercentuais = 0;

        for (let i = 1; i <= this.numParcelas; i++) {
            const valorStr = $(`input[data-parcela-input="valor"][data-parcela-num="${i}"]`).val() || '0';
            const valorParcelaCentavos = paraCentavos(parseValorBrasileiro(valorStr));

            let percentual;
            if (i === this.numParcelas) {
                percentual = (100 - somaPercentuais).toFixed(2);
            } else {
                percentual = calcularPercentual(valorParcelaCentavos, valorTotalCentavos).toFixed(2);
                somaPercentuais += parseFloat(percentual);
            }

            $(`input[data-parcela-input="percentual"][data-parcela-num="${i}"]`).val(percentual);
        }
    }

    /**
     * @private
     */
    _mostrarErroValor(parcelaNum, valorTotal) {
        const $ = window.jQuery;
        const valorInput = $(`input[data-parcela-input="valor"][data-parcela-num="${parcelaNum}"]`);
        
        valorInput.addClass('is-invalid');
        valorInput.closest('td').find('.invalid-feedback').remove();
        valorInput.closest('td').append(
            `<div class="invalid-feedback d-block">A soma das parcelas não pode exceder o valor total de R$ ${formatarValorBrasileiro(valorTotal)}</div>`
        );

        if (typeof toastr !== 'undefined') {
            toastr.error('A soma das parcelas não pode exceder o valor principal', 'Erro de Validação');
        }
    }

    /**
     * @private
     */
    _mostrarErroUltimaParcela() {
        const $ = window.jQuery;
        const valorUltimaInput = $(`input[data-parcela-input="valor"][data-parcela-num="${this.numParcelas}"]`);
        
        valorUltimaInput.addClass('is-invalid');
        valorUltimaInput.closest('td').find('.invalid-feedback').remove();
        valorUltimaInput.closest('td').append(
            '<div class="invalid-feedback d-block">A soma das parcelas excede o valor total</div>'
        );
    }

    /**
     * @private
     */
    _limparErrosValor() {
        const $ = window.jQuery;
        $('input[data-parcela-input="valor"]').removeClass('is-invalid');
        $('input[data-parcela-input="valor"]').closest('td').find('.invalid-feedback').remove();
    }
}

// Instância singleton para uso global
let instance = null;

/**
 * Obtém ou cria instância do controlador
 * @param {Object} config - Configurações opcionais
 * @returns {DrawerParcelasController}
 */
export function getParcelasController(config = {}) {
    if (!instance) {
        instance = new DrawerParcelasController(config);
    }
    return instance;
}

// Expor globalmente para compatibilidade
if (typeof window !== 'undefined') {
    window.DrawerParcelasController = DrawerParcelasController;
    window.getParcelasController = getParcelasController;
    
    // Função legada para compatibilidade
    window.gerarParcelas = function(parcelamento) {
        const controller = getParcelasController();
        controller.init();
        controller.gerarParcelas(parcelamento);
    };
}
