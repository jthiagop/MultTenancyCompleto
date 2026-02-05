<script>
/**
 * DrawerFormManager - Gerenciador profissional de submissão do formulário
 * 
 * Responsabilidades:
 * - Submissão AJAX com proteção contra duplo clique
 * - Exibição de erros de validação nos campos
 * - Loading overlay durante processamento
 * - Modos: Enviar, Clonar, Novo
 * 
 * @version 2.0.0
 * @author Sistema Dominus
 */
class DrawerFormManager {
    constructor() {
        // Elementos do DOM
        this.form = null;
        this.drawer = null;
        this.submitButton = null;
        this.cloneButton = null;
        this.novoButton = null;
        this.overlay = null;
        
        // Estado
        this.isSubmitting = false;
        
        // Configuração
        this.formUrl = '{{ route("banco.store") }}';
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        // Inicializa quando documento estiver pronto
        this.init();
    }
    
    /**
     * Inicializa o gerenciador
     */
    init() {
        this.bindElements();
        this.bindEvents();
        this.bindFieldValidationEvents();
        
        console.log('[DrawerFormManager] Inicializado com sucesso');
    }
    
    /**
     * Obtém referências dos elementos do DOM
     */
    bindElements() {
        this.form = document.getElementById('kt_drawer_lancamento_form');
        this.drawer = document.getElementById('kt_drawer_lancamento');
        this.submitButton = document.getElementById('kt_drawer_lancamento_submit');
        this.cloneButton = document.getElementById('kt_drawer_lancamento_clone');
        this.novoButton = document.getElementById('kt_drawer_lancamento_novo');
        
        // Se ainda não encontrou, escuta quando o drawer abrir
        if (!this.form && this.drawer) {
            this.drawer.addEventListener('kt.drawer.shown', () => this.bindElements());
        }
    }
    
    /**
     * Vincula eventos aos elementos
     */
    bindEvents() {
        // Botão Salvar
        $(document).on('click', '#kt_drawer_lancamento_submit', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.submit('enviar');
        });
        
        // Botão Salvar e Clonar
        $(document).on('click', '#kt_drawer_lancamento_clone', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.submit('clonar');
        });
        
        // Botão Salvar e Novo
        $(document).on('click', '#kt_drawer_lancamento_novo', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.submit('branco');
        });
        
        // Submit do form
        $(document).on('submit', '#kt_drawer_lancamento_form', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.submit('enviar');
        });
    }
    
    /**
     * Vincula eventos para limpar erros quando campos são preenchidos
     */
    bindFieldValidationEvents() {
        // Campos de texto e data
        const textFields = [
            '#data_competencia',
            '#descricao',
            '#valor2',
            '[name="valor"]'
        ];
        
        textFields.forEach(selector => {
            $(document).on('input change', selector, (e) => {
                this.clearFieldError(e.target);
            });
        });
        
        // Campos Select2
        const select2Fields = [
            '#entidade_id',
            '#lancamento_padraos_id',
            '#cost_center_id',
            '#tipo_documento',
            '#configuracao_recorrencia',
            '#dia_cobranca'
        ];
        
        select2Fields.forEach(selector => {
            $(document).on('change', selector, (e) => {
                this.clearFieldError(e.target);
                // Limpa também o Select2 container
                const select2Container = $(e.target).next('.select2-container');
                if (select2Container.length) {
                    select2Container.find('.select2-selection').removeClass('is-invalid');
                }
            });
        });
    }
    
    /**
     * Limpa o erro de um campo específico
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
        this.setButtonsState(false);
    }
    
    /**
     * Reabilita botões após envio
     */
    hideLoading() {
        this.setButtonsState(true);
    }
    
    /**
     * Define o estado dos botões
     */
    setButtonsState(enabled) {
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
     */
    displayErrors(errors) {
        if (!this.form || !errors) return;
        
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
     */
    validateRecurrence() {
        const checkbox = document.getElementById('flexSwitchDefault');
        if (!checkbox || !checkbox.checked) return true;
        
        let isValid = true;
        const fields = [
            { id: 'configuracao_recorrencia', message: 'O campo Configuração é obrigatório.' },
            { id: 'dia_cobranca', message: 'O campo Dia de Cobrança é obrigatório.' },
            { id: 'vencimento', message: 'O campo 1º Vencimento é obrigatório.' }
        ];
        
        fields.forEach(({ id, message }) => {
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
     * Valida campos obrigatórios básicos (feedback instantâneo)
     */
    validateRequiredFields() {
        let isValid = true;
        let firstInvalidField = null;
        let missingFields = [];
        
        // Lista de campos obrigatórios com nomes amigáveis
        const requiredFields = [
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
            // Centro de Custo removido - agora sempre tem valor default selecionado
            { 
                selector: '#tipo_documento', 
                message: 'A forma de pagamento é obrigatória.',
                fieldName: 'Forma de Pagamento',
                isSelect2: true
            }
        ];
        
        // Valida cada campo obrigatório
        requiredFields.forEach(({ selector, message, fieldName, isSelect2 }) => {
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
            const valorNumerico = this.parseValorBR(valorStr);
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
     * Converte valor do formato brasileiro para número
     * Agora com removeMaskOnSubmit: false, o Inputmask envia a string exatamente como o usuário vê
     * Exemplo: "1.991,44" → envia "1.991,44" (não remove máscara)
     * O backend será responsável por fazer a conversão correta
     */
    parseValorBR(valorStr) {
        if (!valorStr || valorStr.trim() === '') return 0;
        
        valorStr = valorStr.trim();
        
        // Se contém vírgula, é formato brasileiro (1.500,00 ou 25,00)
        if (valorStr.indexOf(',') !== -1) {
            // Remove pontos (milhares) e substitui vírgula por ponto
            const valorLimpo = valorStr.replace(/\./g, '').replace(',', '.');
            return parseFloat(valorLimpo) || 0;
        }
        
        // Se contém ponto mas não vírgula, pode ser formato americano (1234.56)
        if (valorStr.indexOf('.') !== -1 && valorStr.indexOf(',') === -1) {
            const pontos = (valorStr.match(/\./g) || []).length;
            // Se tem apenas 1 ponto, é separador decimal
            if (pontos === 1) {
                return parseFloat(valorStr) || 0;
            }
            // Múltiplos pontos = separadores de milhar, remove todos
            return parseFloat(valorStr.replace(/\./g, '')) || 0;
        }
        
        // Se não tem vírgula nem ponto, trata como número inteiro em reais
        // Exemplo: "1991" → 1991.00
        const apenasNumeros = valorStr.replace(/\D/g, '');
        return parseFloat(apenasNumeros) || 0;
    }
    
    /**
     * Prepara o FormData para envio
     * Com removeMaskOnSubmit: false, envia a string exatamente como o usuário vê
     * O backend será responsável por fazer a conversão correta
     */
    prepareFormData() {
        const formData = new FormData(this.form);
        
        // Com removeMaskOnSubmit: false, o Inputmask envia a string formatada
        // Exemplo: "1.991,44" → envia "1.991,44" (não remove máscara)
        // O backend (StoreTransacaoFinanceiraRequest) fará a conversão correta
        const valorInput = this.form.querySelector('#valor2') || this.form.querySelector('[name="valor"]');
        if (valorInput && valorInput.value) {
            const valorStr = valorInput.value || '';
            
            console.log('[prepareFormData] Valor enviado exatamente como o usuário vê', {
                'valor_original': valorStr
            });
            
            // Envia a string exatamente como está (formato brasileiro: "1.991,44")
            formData.delete('valor');
            formData.append('valor', valorStr);
        }
        
        // Garante que checkboxes booleanos sejam enviados corretamente
        const booleanFields = ['comprovacao_fiscal', 'agendado', 'pago', 'recebido'];
        booleanFields.forEach(field => {
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
     * Verifica se está no modo de edição
     * @returns {boolean}
     */
    isEditMode() {
        // Verifica campo hidden _method=PUT
        const methodInput = this.form?.querySelector('input[name="_method"]');
        if (methodInput && methodInput.value.toUpperCase() === 'PUT') {
            return true;
        }
        
        // Verifica campo hidden transacao_id
        const transacaoId = this.form?.querySelector('input[name="transacao_id"]');
        if (transacaoId && transacaoId.value) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Submete o formulário
     */
    async submit(mode = 'enviar') {
        // Proteção contra duplo clique
        if (this.isSubmitting) {
            console.warn('[DrawerFormManager] Submissão já em andamento, ignorando');
            return;
        }
        
        // Atualiza referências
        this.bindElements();
        
        if (!this.form) {
            Swal.fire({
                text: 'Erro: Formulário não encontrado.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Ok',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            return;
        }
        
        // Valida campos obrigatórios (feedback instantâneo)
        if (!this.validateRequiredFields()) return;
        
        // Valida recorrência
        if (!this.validateRecurrence()) return;
        
        // Inicia submissão
        this.isSubmitting = true;
        this.clearErrors();
        this.showLoading();
        
        // Detecta modo de edição
        const isEditing = this.isEditMode();
        
        try {
            const formData = this.prepareFormData();
            let formAction = this.form.getAttribute('action') || this.formUrl;
            
            // Se está editando, adiciona _method=PUT para method spoofing do Laravel
            if (isEditing) {
                formData.append('_method', 'PUT');
                console.log('[DrawerFormManager] Modo edição detectado, usando PUT', { action: formAction });
            }
            
            const response = await fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });
            
            if (response.ok) {
                const data = await response.json().catch(() => ({ success: true }));
                this.onSuccess(mode);
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
                Swal.fire({
                    title: 'Erro ao salvar',
                    html: error.message.replace(/\n/g, '<br>'),
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok, entendi!',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
            }
        } finally {
            this.isSubmitting = false;
            this.hideLoading();
        }
    }
    
    /**
     * Callback de sucesso
     */
    onSuccess(mode) {
        this.clearErrors();
        
        // Obtém dados do formulário para o evento
        const tipo = this.form.querySelector('[name="tipo"]')?.value || 'entrada';
        const valor = this.form.querySelector('#valor2')?.value || '0';
        
        // Emite evento global para atualizar todos os componentes
        if (window.DominusEvents) {
            DominusEvents.emit('transaction.created', { 
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
     * Recarrega a DataTable
     */
    reloadDataTable() {
        if (typeof window.reloadDataTable === 'function') {
            window.reloadDataTable();
        } else if ($.fn.DataTable) {
            const tables = $.fn.DataTable.tables({ visible: true, api: true });
            if (tables.length > 0) {
                tables.ajax.reload(null, false);
            }
        }
        console.log('[DrawerFormManager] DataTable atualizada');
    }
    
    /**
     * Reseta o formulário
     */
    resetForm() {
        if (!this.form) return;
        
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
        
        const drawer = KTDrawer.getInstance(this.drawer);
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

// Inicializa quando o documento estiver pronto
$(document).ready(function() {
    window.drawerFormManager = new DrawerFormManager();
});
</script>
