/**
 * DrawerFormManager - Gerenciador de submissão do formulário de lançamento
 * 
 * Responsabilidades:
 * - Submissão AJAX com proteção contra duplo clique
 * - Exibição de erros de validação nos campos
 * - Loading overlay durante processamento
 * - Modos: Enviar, Clonar, Novo
 * 
 * @module financeiro/drawer/DrawerFormManager
 * @version 2.1.0
 */

import { parseValorBrasileiro } from '../utils/currency.js';

/**
 * Configuração padrão
 */
const defaultConfig = {
    formId: 'kt_drawer_lancamento_form',
    drawerId: 'kt_drawer_lancamento',
    submitButtonId: 'kt_drawer_lancamento_submit',
    cloneButtonId: 'kt_drawer_lancamento_clone',
    novoButtonId: 'kt_drawer_lancamento_novo',
    formUrl: '/banco',
    requiredFields: [
        { 
            selector: '#data_competencia, [name="data_competencia"]', 
            message: 'A data de competência é obrigatória.',
            fieldName: 'Data',
            isSelect2: false
        },
        { 
            selector: '#entidade_id', 
            message: 'A entidade financeira é obrigatória.',
            fieldName: 'Conta',
            isSelect2: true
        },
        { 
            selector: '#descricao, [name="descricao"]', 
            message: 'A descrição é obrigatória.',
            fieldName: 'Descrição',
            isSelect2: false
        },
        { 
            selector: '#lancamento_padraos_id, [name="lancamento_padrao_id"]', 
            message: 'A categoria é obrigatória.',
            fieldName: 'Categoria',
            isSelect2: true
        },
        { 
            selector: '#tipo_documento', 
            message: 'A forma de pagamento é obrigatória.',
            fieldName: 'Forma de Pagamento',
            isSelect2: true
        }
    ],
    booleanFields: ['comprovacao_fiscal', 'agendado', 'pago', 'recebido'],
    recurrenceFields: [
        { id: 'configuracao_recorrencia', message: 'O campo Configuração é obrigatório.' },
        { id: 'dia_cobranca', message: 'O campo Dia de Cobrança é obrigatório.' },
        { id: 'vencimento', message: 'O campo 1º Vencimento é obrigatório.' }
    ]
};

/**
 * Classe gerenciadora do formulário
 */
export class DrawerFormManager {
    /**
     * @param {Object} config - Configurações customizadas
     */
    constructor(config = {}) {
        this.config = { ...defaultConfig, ...config };
        
        // Elementos do DOM
        this.form = null;
        this.drawer = null;
        this.submitButton = null;
        this.cloneButton = null;
        this.novoButton = null;
        
        // Estado
        this.isSubmitting = false;
        
        // CSRF Token
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        this._initialized = false;
    }

    /**
     * Inicializa o gerenciador
     */
    init() {
        if (this._initialized) return this;
        
        this._bindElements();
        this._bindEvents();
        this._bindFieldValidationEvents();
        
        this._initialized = true;
        console.log('[DrawerFormManager] Inicializado com sucesso');
        
        return this;
    }

    /**
     * Obtém referências dos elementos do DOM
     * @private
     */
    _bindElements() {
        this.form = document.getElementById(this.config.formId);
        this.drawer = document.getElementById(this.config.drawerId);
        this.submitButton = document.getElementById(this.config.submitButtonId);
        this.cloneButton = document.getElementById(this.config.cloneButtonId);
        this.novoButton = document.getElementById(this.config.novoButtonId);

        // Se ainda não encontrou, escuta quando o drawer abrir
        if (!this.form && this.drawer) {
            this.drawer.addEventListener('kt.drawer.shown', () => this._bindElements());
        }
    }

    /**
     * Vincula eventos aos elementos
     * @private
     */
    _bindEvents() {
        const $ = window.jQuery;
        const self = this;

        // Botão Salvar
        $(document).on('click', `#${this.config.submitButtonId}`, function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.submit('enviar');
        });

        // Botão Salvar e Clonar
        $(document).on('click', `#${this.config.cloneButtonId}`, function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.submit('clonar');
        });

        // Botão Salvar e Novo
        $(document).on('click', `#${this.config.novoButtonId}`, function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.submit('branco');
        });

        // Submit do form
        $(document).on('submit', `#${this.config.formId}`, function(e) {
            e.preventDefault();
            e.stopPropagation();
            self.submit('enviar');
        });
    }

    /**
     * Vincula eventos para limpar erros quando campos são preenchidos
     * @private
     */
    _bindFieldValidationEvents() {
        const $ = window.jQuery;
        const self = this;

        // Campos de texto e data
        const textFields = ['#data_competencia', '#descricao', '#valor2', '[name="valor"]'];
        textFields.forEach(selector => {
            $(document).on('input change', selector, function(e) {
                self.clearFieldError(e.target);
            });
        });

        // Campos Select2
        const select2Fields = [
            '#entidade_id', '#lancamento_padraos_id', '#cost_center_id',
            '#tipo_documento', '#configuracao_recorrencia', '#dia_cobranca'
        ];
        select2Fields.forEach(selector => {
            $(document).on('change', selector, function(e) {
                self.clearFieldError(e.target);
                const select2Container = $(e.target).next('.select2-container');
                if (select2Container.length) {
                    select2Container.find('.select2-selection').removeClass('is-invalid');
                }
            });
        });
    }

    /**
     * Limpa o erro de um campo específico
     * @param {HTMLElement} field - Campo a limpar
     */
    clearFieldError(field) {
        if (!field) return;

        field.classList.remove('is-invalid');

        const container = field.closest('.fv-row') || field.parentElement;
        if (container) {
            const errorMsg = container.querySelector('.invalid-feedback');
            if (errorMsg) errorMsg.remove();
        }
    }

    /**
     * Desabilita botões durante envio
     */
    showLoading() {
        this._setButtonsState(false);
    }

    /**
     * Reabilita botões após envio
     */
    hideLoading() {
        this._setButtonsState(true);
    }

    /**
     * Define o estado dos botões
     * @private
     */
    _setButtonsState(enabled) {
        const buttons = [this.submitButton, this.cloneButton, this.novoButton];

        buttons.forEach(btn => {
            if (!btn) return;

            if (enabled) {
                btn.disabled = false;
                btn.removeAttribute('data-kt-indicator');
                btn.style.pointerEvents = 'auto';
            } else {
                btn.disabled = true;
                btn.setAttribute('data-kt-indicator', 'on');
                btn.style.pointerEvents = 'none';
            }
        });
    }

    /**
     * Limpa erros do formulário
     */
    clearErrors() {
        if (!this.form) return;
        const $ = window.jQuery;

        // Remove classes de erro
        this.form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });

        // Remove mensagens de erro
        this.form.querySelectorAll('.invalid-feedback').forEach(msg => {
            msg.remove();
        });

        // Limpa Select2
        if ($.fn.select2) {
            $(this.form).find('.select2-container').each(function() {
                $(this).removeClass('is-invalid').css('border-color', '');
            });
        }
    }

    /**
     * Exibe erros nos campos
     * @param {Object} errors - Objeto com erros por campo
     */
    displayErrors(errors) {
        if (!this.form || !errors) return;
        const $ = window.jQuery;

        this.clearErrors();
        let firstErrorField = null;

        Object.entries(errors).forEach(([fieldName, fieldErrors]) => {
            const errorMessage = Array.isArray(fieldErrors) ? fieldErrors[0] : fieldErrors;
            const field = this.form.querySelector(`[name="${fieldName}"]`);

            if (!field) return;

            if (!firstErrorField) firstErrorField = field;

            // Adiciona classe de erro
            field.classList.add('is-invalid');

            // Cria mensagem de erro
            const container = field.closest('.fv-row') || field.parentElement;
            const existingError = container.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();

            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = errorMessage;
            errorDiv.style.color = '#f1416c';
            container.appendChild(errorDiv);

            // Para Select2
            if ($.fn.select2 && $(field).hasClass('select2-hidden-accessible')) {
                const select2Container = $(field).next('.select2-container');
                if (select2Container.length) {
                    select2Container.addClass('is-invalid').css('border-color', '#f1416c');
                }
            }
        });

        // Foca no primeiro campo com erro
        if (firstErrorField) {
            setTimeout(() => {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorField.focus();
            }, 300);
        }
    }

    /**
     * Valida campos de recorrência
     * @returns {boolean}
     */
    validateRecurrence() {
        const $ = window.jQuery;
        const checkbox = document.getElementById('flexSwitchDefault');
        if (!checkbox || !checkbox.checked) return true;

        let isValid = true;

        this.config.recurrenceFields.forEach(({ id, message }) => {
            const field = $(`#${id}`);
            if (!field.length) return;

            const value = field.val();
            const container = field.closest('.fv-row') || field.closest('#configuracao-recorrencia-wrapper');

            // Remove erro anterior
            field.removeClass('is-invalid');
            container.find('.invalid-feedback').remove();
            if (field.next('.select2-container').length) {
                field.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }

            // Valida
            if (!value) {
                isValid = false;
                field.addClass('is-invalid');

                if (field.next('.select2-container').length) {
                    field.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                }

                container.append(`<div class="invalid-feedback d-block">${message}</div>`);
            }
        });

        return isValid;
    }

    /**
     * Valida campos obrigatórios básicos
     * @returns {boolean}
     */
    validateRequiredFields() {
        const $ = window.jQuery;
        let isValid = true;
        let firstInvalidField = null;
        let missingFields = [];

        // Valida cada campo obrigatório
        this.config.requiredFields.forEach(({ selector, message, fieldName, isSelect2 }) => {
            const field = this.form.querySelector(selector);
            if (!field) return;

            const value = field.value?.trim() || '';
            const container = field.closest('.fv-row') || field.parentElement;

            // Remove erro anterior
            field.classList.remove('is-invalid');
            const existingError = container.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();

            // Limpa Select2 se aplicável
            if (isSelect2 && $(field).next('.select2-container').length) {
                $(field).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }

            // Valida
            if (!value) {
                isValid = false;
                field.classList.add('is-invalid');
                missingFields.push(fieldName);

                if (!firstInvalidField) firstInvalidField = field;

                // Marca Select2 como inválido
                if (isSelect2 && $(field).next('.select2-container').length) {
                    $(field).next('.select2-container').find('.select2-selection').addClass('is-invalid');
                }

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = message;
                container.appendChild(errorDiv);
            }
        });

        // Campo Valor - obrigatório e deve ser > 0
        const valorInput = this.form.querySelector('#valor2') || this.form.querySelector('[name="valor"]');
        if (valorInput) {
            const valorStr = valorInput.value || '';
            const valorNumerico = parseValorBrasileiro(valorStr);
            const container = valorInput.closest('.fv-row') || valorInput.parentElement;

            // Remove erro anterior
            valorInput.classList.remove('is-invalid');
            const existingError = container.querySelector('.invalid-feedback');
            if (existingError) existingError.remove();

            if (!valorStr.trim() || valorNumerico <= 0) {
                isValid = false;
                valorInput.classList.add('is-invalid');

                if (!firstInvalidField) firstInvalidField = valorInput;
                missingFields.push('Valor');

                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                errorDiv.textContent = 'O valor é obrigatório e deve ser maior que zero.';
                container.appendChild(errorDiv);
            }
        }

        // Se há campos inválidos, exibe toast com lista
        if (!isValid && missingFields.length > 0) {
            const fieldsList = missingFields.join(', ');
            if (typeof toastr !== 'undefined') {
                toastr.warning(`Preencha os campos obrigatórios: ${fieldsList}`, 'Campos obrigatórios');
            }
        }

        // Foca no primeiro campo inválido
        if (firstInvalidField) {
            setTimeout(() => {
                firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }, 100);
        }

        return isValid;
    }

    /**
     * Prepara o FormData para envio
     * @returns {FormData}
     */
    prepareFormData() {
        const formData = new FormData(this.form);

        // Valor no formato brasileiro
        const valorInput = this.form.querySelector('#valor2') || this.form.querySelector('[name="valor"]');
        if (valorInput && valorInput.value) {
            formData.delete('valor');
            formData.append('valor', valorInput.value);
        }

        // Garante que checkboxes booleanos sejam enviados corretamente
        this.config.booleanFields.forEach(field => {
            const checkbox = this.form.querySelector(`#${field}_checkbox`) ||
                this.form.querySelector(`[name="${field}"]`);
            if (checkbox && checkbox.type === 'checkbox') {
                formData.delete(field);
                formData.append(field, checkbox.checked ? '1' : '0');
            }
        });

        // Garante que o tipo seja sempre enviado
        const tipoInput = this.form.querySelector('[name="tipo"]');
        if (tipoInput && tipoInput.value) {
            formData.delete('tipo');
            formData.append('tipo', tipoInput.value);
        }

        return formData;
    }

    /**
     * Submete o formulário
     * @param {string} mode - Modo de submissão: 'enviar', 'clonar', 'branco'
     */
    async submit(mode = 'enviar') {
        // Proteção contra duplo clique
        if (this.isSubmitting) {
            console.warn('[DrawerFormManager] Submissão já em andamento, ignorando');
            return;
        }

        // Atualiza referências
        this._bindElements();

        if (!this.form) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    text: 'Erro: Formulário não encontrado.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
            return;
        }

        // Valida campos obrigatórios
        if (!this.validateRequiredFields()) return;

        // Valida recorrência
        if (!this.validateRecurrence()) return;

        // Inicia submissão
        this.isSubmitting = true;
        this.clearErrors();
        this.showLoading();

        try {
            const formData = this.prepareFormData();
            const formAction = this.form.getAttribute('action') || this.config.formUrl;

            const response = await fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.ok) {
                await response.json().catch(() => ({ success: true }));
                this._onSuccess(mode);
            } else if (response.status === 422) {
                const data = await response.json();
                if (data.errors) {
                    this.displayErrors(data.errors);
                }
            } else {
                const text = await response.text();
                throw new Error(text || 'Erro ao salvar');
            }
        } catch (error) {
            if (error.message !== 'validation_error') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Erro ao salvar',
                        html: error.message.replace(/\n/g, '<br>'),
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, entendi!',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                }
            }
        } finally {
            this.isSubmitting = false;
            this.hideLoading();
        }
    }

    /**
     * Callback de sucesso
     * @private
     */
    _onSuccess(mode) {
        this.clearErrors();

        // Obtém dados do formulário para o evento
        const tipo = this.form.querySelector('[name="tipo"]')?.value || 'entrada';
        const valor = this.form.querySelector('#valor2')?.value || '0';

        // Emite evento global para atualizar todos os componentes
        if (window.DominusEvents) {
            window.DominusEvents.emit('transaction.created', {
                tipo: window.normalizeTipo ? window.normalizeTipo(tipo) : tipo,
                valor,
                mode,
                timestamp: Date.now()
            });
        }

        // Exibe toast de sucesso
        if (typeof toastr !== 'undefined') {
            toastr.success('Lançamento salvo com sucesso!', 'Sucesso');
        }

        // Processa ação baseada no modo
        switch (mode) {
            case 'clonar':
                // Mantém dados e drawer aberto
                console.log('[DrawerFormManager] Modo clonar: mantendo drawer aberto');
                break;

            case 'branco':
                // Limpa formulário e mantém drawer aberto
                this.resetForm();
                console.log('[DrawerFormManager] Modo branco: formulário limpo');
                break;

            default:
                // Fecha drawer
                this.closeDrawer();
                break;
        }
    }

    /**
     * Reseta o formulário
     */
    resetForm() {
        if (!this.form) return;
        const $ = window.jQuery;

        this.form.reset();

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
    }

    /**
     * Fecha o drawer
     */
    closeDrawer() {
        if (!this.drawer) return;
        const $ = window.jQuery;

        const drawer = window.KTDrawer?.getInstance(this.drawer);
        if (drawer) {
            drawer.hide();
        }

        // Remove backdrop/overlay se existir
        setTimeout(() => {
            $('.drawer-overlay').remove();
            $('body').removeClass('drawer-on');
            $(this.drawer).removeClass('drawer-on');
        }, 300);
    }
}

// Instância singleton
let instance = null;

/**
 * Obtém ou cria instância do gerenciador
 * @param {Object} config - Configurações opcionais
 * @returns {DrawerFormManager}
 */
export function getFormManager(config = {}) {
    if (!instance) {
        instance = new DrawerFormManager(config);
    }
    return instance;
}

// Expor globalmente para compatibilidade
if (typeof window !== 'undefined') {
    window.DrawerFormManager = DrawerFormManager;
    window.getFormManager = getFormManager;
}
