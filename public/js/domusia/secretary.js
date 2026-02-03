/**
 * Domusia - Módulo Secretaria
 *
 * Gerencia o formulário de membros religiosos
 */
class DomusiaSecretary {
    constructor(config = {}) {
        this.config = {
            modalId: '#kt_modal_member',
            formId: '#kt_modal_member_form',
            submitBtnId: '#kt_modal_new_target_submit',
            cancelBtnId: '#kt_modal_new_target_cancel',
            storeUrl: config.storeUrl || '',
            statsUrl: config.statsUrl || '',
            csrfToken: config.csrfToken || '',
            ...config
        };

        this.modal = null;
        this.form = null;
        this.submitBtn = null;
        this.stageSelect = null;
        this.stageBlocks = null;
        this.formativaWrapper = null;
        this.isCurrentCheckboxes = null;
        this.currentMarkedOrder = null;
        this.stageOrders = config.stageOrders || {};

        this.init();
    }

    init() {
        this.cacheElements();
        this.setupEventListeners();
        this.setupFormValidation();
    }

    cacheElements() {
        this.modal = document.querySelector(this.config.modalId);
        this.form = document.querySelector(this.config.formId);
        this.submitBtn = document.querySelector(this.config.submitBtnId);
        this.stageSelect = document.querySelector('[name="current_stage_id"]');
        this.stageBlocks = document.querySelectorAll('.formation-stage-block');
        this.formativaWrapper = document.getElementById('kt_modal_member_formativa_wrapper');
        this.isCurrentCheckboxes = document.querySelectorAll('.is-current-checkbox');
        this.religiousRoleWrapper = document.getElementById('kt_religious_role_wrapper');
        
        // Estado de edição
        this.editingMemberId = null;
        this.isEditMode = false;
        
        // Modo de salvamento: 'default' | 'clone' | 'clear'
        this.saveMode = 'default';
    }

    setupEventListeners() {
        // Eventos das etapas de formação
        this.setupStageEvents();

        // Eventos dos checkboxes "Período Atual"
        this.setupIsCurrentCheckboxes();

        // Evento de submit do formulário
        this.setupFormSubmit();

        // Evento de cancelar
        this.setupCancelButton();
        
        // Eventos de ações da tabela (editar, ver, excluir)
        this.setupTableActions();
        
        // Evento de abertura do modal (para reset quando aberto via botão "Novo")
        this.setupModalEvents();
        
        // Eventos dos modos de salvamento (dropdown)
        this.setupSaveModeListeners();
    }
    
    /**
     * Configura os listeners para os modos de salvamento do dropdown
     */
    setupSaveModeListeners() {
        // Listener para as opções do dropdown
        document.querySelectorAll('[data-save-mode]').forEach(element => {
            element.addEventListener('click', (e) => {
                const mode = element.dataset.saveMode;
                
                // Se for o botão principal, não precisa fazer nada especial
                if (mode === 'default') return;
                
                e.preventDefault();
                this.saveMode = mode;
                this.handleSubmit();
            });
        });
    }
    
    /**
     * Configura eventos do modal
     */
    setupModalEvents() {
        if (!this.modal) return;
        
        // Quando o modal for escondido, resetar o formulário
        this.modal.addEventListener('hidden.bs.modal', () => {
            this.resetForm();
        });
        
        // Quando o modal for aberto via data-bs-toggle (botão Novo), garantir modo de criação
        this.modal.addEventListener('show.bs.modal', (e) => {
            // Se o modal foi aberto via data-bs-toggle (não via JS), resetar para modo de criação
            // O evento relatedTarget contém o elemento que disparou o modal
            if (e.relatedTarget && e.relatedTarget.hasAttribute('data-bs-toggle')) {
                this.editingMemberId = null;
                this.isEditMode = false;
                this.resetForm();
                this.updateModalTitle();
            }
        });
    }

    setupStageEvents() {
        if (!this.stageSelect) return;

        // Para Select2
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $(this.stageSelect).on('select2:select', () => {
                if (!this.currentMarkedOrder) {
                    this.updateVisibleStages();
                }
            });
        }

        // Para select nativo
        this.stageSelect.addEventListener('change', () => {
            if (!this.currentMarkedOrder) {
                this.updateVisibleStages();
            }
        });

        // Estado inicial
        this.updateVisibleStages();
    }

    setupIsCurrentCheckboxes() {
        this.isCurrentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.handleIsCurrentChange(checkbox);
            });
        });
    }

    updateVisibleStages() {
        const selectedStageId = this.stageSelect.value;
        const selectedOrder = this.stageOrders[selectedStageId] || 0;

        // Mostrar/esconder o wrapper completo
        if (this.formativaWrapper) {
            this.formativaWrapper.style.display = selectedStageId ? 'block' : 'none';
        }

        this.stageBlocks.forEach(block => {
            const blockOrder = parseInt(block.dataset.stageOrder);
            const maxOrder = this.currentMarkedOrder || selectedOrder;

            if (selectedStageId && blockOrder <= maxOrder) {
                block.style.display = 'block';

                // Inicializar Select2 para os selects dentro do bloco
                const selects = block.querySelectorAll('select:not(.select2-hidden-accessible)');
                selects.forEach(select => {
                    if (typeof $ !== 'undefined' && $.fn.select2) {
                        $(select).select2({
                            dropdownParent: $(this.config.modalId),
                            placeholder: 'Selecione o local',
                            allowClear: true
                        });
                    }
                });
            } else {
                block.style.display = 'none';
            }
        });

        // Mostrar/esconder seção de Função Religiosa (apenas para Votos Perpétuos - ordem 7)
        this.updateReligiousRoleVisibility(selectedOrder);
    }

    /**
     * Controla a visibilidade da seção de Função Religiosa
     * Só aparece quando a etapa selecionada for Votos Perpétuos (ordem 7)
     */
    updateReligiousRoleVisibility(selectedOrder) {
        if (this.religiousRoleWrapper) {
            const isVotosPerpétuos = selectedOrder >= 7;
            this.religiousRoleWrapper.style.display = isVotosPerpétuos ? 'block' : 'none';
        }
    }

    handleIsCurrentChange(checkbox) {
        const stageOrder = parseInt(checkbox.dataset.stageOrder);
        const stageId = checkbox.dataset.stageId;
        const stageSlug = checkbox.closest('.formation-stage-block').dataset.stageSlug;

        if (checkbox.checked) {
            // Desmarcar todos os outros checkboxes
            this.isCurrentCheckboxes.forEach(cb => {
                if (cb !== checkbox) {
                    cb.checked = false;
                    const otherBlock = cb.closest('.formation-stage-block');
                    const otherSlug = otherBlock.dataset.stageSlug;
                    this.enableEndDate(otherSlug);
                }
            });

            this.currentMarkedOrder = stageOrder;

            // Fixar o select de Etapa de Formação
            if (this.stageSelect) {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(this.stageSelect).val(stageId).trigger('change');
                    $(this.stageSelect).prop('disabled', true);
                } else {
                    this.stageSelect.value = stageId;
                    this.stageSelect.disabled = true;
                }
            }

            this.disableEndDate(stageSlug);
            this.updateVisibleStages();
        } else {
            this.currentMarkedOrder = null;

            if (this.stageSelect) {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(this.stageSelect).prop('disabled', false);
                } else {
                    this.stageSelect.disabled = false;
                }
            }

            this.enableEndDate(stageSlug);
            this.updateVisibleStages();
        }
    }

    disableEndDate(stageSlug) {
        const wrapper = document.querySelector(`.end-date-wrapper[data-stage-slug="${stageSlug}"]`);
        if (wrapper) {
            const input = wrapper.querySelector('input');
            if (input) {
                input.disabled = true;
                input.value = '';
                input.classList.add('bg-light-secondary');
            }
            wrapper.style.opacity = '0.5';
        }
    }

    enableEndDate(stageSlug) {
        const wrapper = document.querySelector(`.end-date-wrapper[data-stage-slug="${stageSlug}"]`);
        if (wrapper) {
            const input = wrapper.querySelector('input');
            if (input) {
                input.disabled = false;
                input.classList.remove('bg-light-secondary');
            }
            wrapper.style.opacity = '1';
        }
    }

    setupFormValidation() {
        // Configuração básica de validação (pode ser expandida)
        this.validator = null;

        if (typeof FormValidation !== 'undefined' && this.form) {
            this.validator = FormValidation.formValidation(this.form, {
                fields: {
                    'nome': {
                        validators: {
                            notEmpty: {
                                message: 'O nome é obrigatório'
                            }
                        }
                    },
                    'current_stage_id': {
                        validators: {
                            notEmpty: {
                                message: 'A etapa de formação é obrigatória'
                            }
                        }
                    },
                    'data_nascimento': {
                        validators: {
                            notEmpty: {
                                message: 'A data de nascimento é obrigatória'
                            }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: '.fv-row',
                        eleInvalidClass: '',
                        eleValidClass: ''
                    })
                }
            });
        }
    }

    setupFormSubmit() {
        if (!this.submitBtn || !this.form) return;

        this.submitBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });
    }

    setupCancelButton() {
        const cancelBtn = document.querySelector(this.config.cancelBtnId);
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.resetForm();
                if (this.modal) {
                    const bsModal = bootstrap.Modal.getInstance(this.modal);
                    if (bsModal) bsModal.hide();
                }
            });
        }
    }

    async handleSubmit() {
        // Validar formulário se tiver validador
        if (this.validator) {
            const status = await this.validator.validate();
            if (status !== 'Valid') {
                return;
            }
        }

        // Mostrar loading
        this.setLoading(true);

        // Coletar dados do formulário (agora retorna FormData)
        const formData = this.collectFormData();

        // Determinar URL e método baseado no modo de edição
        let url = this.config.storeUrl;
        let method = 'POST';
        
        if (this.isEditMode && this.editingMemberId) {
            url = this.config.updateUrl.replace('__ID__', this.editingMemberId);
            method = 'POST'; // Usamos POST com _method para compatibilidade com FormData
            formData.append('_method', 'PUT');
        }

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': this.config.csrfToken,
                    'Accept': 'application/json'
                    // Não definir 'Content-Type' para FormData - o browser configura automaticamente com boundary
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.handleSuccess(data);
            } else {
                this.handleError(data);
            }
        } catch (error) {
            console.error('Erro ao salvar:', error);
            this.handleError({ message: 'Erro ao processar a requisição. Tente novamente.' });
        } finally {
            this.setLoading(false);
        }
    }

    collectFormData() {
        const formData = new FormData();

        // Campos básicos
        formData.append('nome', this.form.querySelector('[name="nome"]')?.value || '');
        formData.append('current_stage_id', this.form.querySelector('[name="current_stage_id"]')?.value || '');
        formData.append('data_nascimento', this.form.querySelector('[name="data_nascimento"]')?.value || '');

        // Campos adicionais
        formData.append('funcao', this.form.querySelector('[name="funcao"]')?.value || '');
        formData.append('provincia', this.form.querySelector('[name="provincia"]')?.value || '');
        formData.append('cpf', this.form.querySelector('[name="cpf"]')?.value || '');

        // Endereço de origem
        formData.append('cep', this.form.querySelector('[name="cep"]')?.value || '');
        formData.append('bairro', this.form.querySelector('[name="bairro"]')?.value || '');
        formData.append('logradouro', this.form.querySelector('[name="logradouro"]')?.value || '');
        formData.append('numero', this.form.querySelector('[name="numero"]')?.value || '');
        formData.append('localidade', this.form.querySelector('[name="localidade"]')?.value || '');
        formData.append('uf', this.form.querySelector('[name="uf"]')?.value || '');

        // Observações e disponibilidade
        formData.append('observacoes', this.form.querySelector('[name="observacoes"]')?.value || '');
        const disponivelCheckbox = this.form.querySelector('[name="disponivel_todas_casas"]');
        formData.append('disponivel_todas_casas', disponivelCheckbox?.checked ? '1' : '0');

        // Função religiosa (apenas para Votos Perpétuos)
        const religiousRoleInput = this.form.querySelector('[name="religious_role_id"]:checked');
        if (religiousRoleInput) {
            formData.append('religious_role_id', religiousRoleInput.value);
        }

        // Avatar (arquivo)
        const avatarInput = this.form.querySelector('[name="avatar"]');
        if (avatarInput && avatarInput.files && avatarInput.files.length > 0) {
            formData.append('avatar', avatarInput.files[0]);
        }

        // Remover avatar (se marcado)
        const avatarRemoveInput = this.form.querySelector('[name="avatar_remove"]');
        if (avatarRemoveInput) {
            formData.append('avatar_remove', avatarRemoveInput.value || '0');
        }

        // Coletar dados das etapas formativas (stages_json)
        const stagesData = this.collectStagesData();
        formData.append('stages_json', JSON.stringify(stagesData));

        return formData;
    }

    collectStagesData() {
        const stages = [];

        this.stageBlocks.forEach(block => {
            // Somente incluir blocos visíveis
            if (block.style.display === 'none') return;

            const stageId = block.dataset.stageId;
            const stageSlug = block.dataset.stageSlug;

            const startDateInput = block.querySelector(`[name="stages[${stageSlug}][start_date]"]`);
            const endDateInput = block.querySelector(`[name="stages[${stageSlug}][end_date]"]`);
            const companySelect = block.querySelector(`[name="stages[${stageSlug}][company_id]"]`);
            const isCurrentCheckbox = block.querySelector(`[name="stages[${stageSlug}][is_current]"]`);

            const startDate = startDateInput?.value || '';
            const endDate = endDateInput?.value || '';
            const companyId = companySelect?.value || '';
            const isCurrent = isCurrentCheckbox?.checked ? true : false;

            // Só incluir se tiver pelo menos a data inicial preenchida
            if (startDate) {
                stages.push({
                    stage_id: stageId,
                    stage_slug: stageSlug,
                    start_date: startDate,
                    end_date: endDate,
                    company_id: companyId,
                    is_current: isCurrent
                });
            }
        });

        return stages;
    }

    handleSuccess(data) {
        const wasEditMode = this.isEditMode;
        const currentSaveMode = this.saveMode;
        
        // Resetar o modo de salvamento para o padrão
        this.saveMode = 'default';
        
        // Se estamos na página show, recarregar a página após salvar (modo padrão)
        if (this.config.isShowPage && currentSaveMode === 'default') {
            // Fechar modal
            if (this.modal) {
                const bsModal = bootstrap.Modal.getInstance(this.modal);
                if (bsModal) bsModal.hide();
            }
            
            // Mostrar toast de sucesso e recarregar
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Membro atualizado com sucesso!',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    didClose: () => {
                        window.location.reload();
                    }
                });
            } else {
                window.location.reload();
            }
            return;
        }
        
        // Recarregar DataTable e atualizar contadores (se não estiver na página show)
        if (!this.config.isShowPage) {
            this.reloadDataTable();
            this.updateTabStats();
        }
        
        // Definir mensagem de sucesso
        let successMessage = '';
        switch (currentSaveMode) {
            case 'clone':
                successMessage = 'Membro salvo! Formulário pronto para clonar.';
                break;
            case 'clear':
                successMessage = 'Membro salvo! Formulário limpo para novo cadastro.';
                break;
            default:
                successMessage = wasEditMode 
                    ? 'Membro atualizado com sucesso!' 
                    : 'Membro cadastrado com sucesso!';
        }
        
        // Tratar de acordo com o modo de salvamento
        if (currentSaveMode === 'clone') {
            // Modo Clonar: manter modal aberto com os dados (para criar novo baseado nele)
            // Resetar apenas o ID para criar um novo registro
            this.editingMemberId = null;
            this.isEditMode = false;
            this.updateModalTitle();
            
        } else if (currentSaveMode === 'clear') {
            // Modo Limpar: manter modal aberto mas limpar o formulário
            this.resetForm();
            this.updateModalTitle();
            
        } else {
            // Modo Padrão: fechar modal e resetar tudo
            if (this.modal) {
                const bsModal = bootstrap.Modal.getInstance(this.modal);
                if (bsModal) bsModal.hide();
            }
            this.resetForm();
        }
        
        // Mostrar toast de sucesso (não bloqueante)
        if (typeof toastr !== 'undefined') {
            toastr.success(successMessage);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: successMessage,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    }

    handleError(data) {
        // Mostrar mensagem de erro
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: data.message || 'Erro ao salvar o membro. Tente novamente.',
                confirmButtonText: 'OK'
            });
        } else {
            alert(data.message || 'Erro ao salvar o membro. Tente novamente.');
        }

        // Mostrar erros de validação se houver
        if (data.errors) {
            this.showValidationErrors(data.errors);
        }
    }

    showValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = this.form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentElement.querySelector('.invalid-feedback') ||
                    document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = errors[field][0];
                if (!input.parentElement.querySelector('.invalid-feedback')) {
                    input.parentElement.appendChild(feedback);
                }
            }
        });
    }

    setLoading(loading) {
        if (!this.submitBtn) return;

        if (loading) {
            this.submitBtn.setAttribute('data-kt-indicator', 'on');
            this.submitBtn.disabled = true;
        } else {
            this.submitBtn.removeAttribute('data-kt-indicator');
            this.submitBtn.disabled = false;
        }
    }

    resetForm() {
        if (this.form) {
            this.form.reset();

            // Limpar Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(this.form).find('select').val('').trigger('change');
            }

            // Limpar validação
            this.form.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            this.form.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });

            // Resetar componente de imagem do Metronic
            const imageInputElement = this.form.querySelector('[data-kt-image-input="true"]');
            if (imageInputElement) {
                // Remover classes de estado
                imageInputElement.classList.remove('image-input-changed');
                imageInputElement.classList.add('image-input-empty');
                
                // Limpar o preview da imagem
                const wrapper = imageInputElement.querySelector('.image-input-wrapper');
                if (wrapper) {
                    wrapper.style.backgroundImage = '';
                }
                
                // Limpar o input de arquivo
                const avatarInput = imageInputElement.querySelector('input[type="file"]');
                if (avatarInput) {
                    avatarInput.value = '';
                }
                
                // Limpar o input hidden de remoção
                const avatarRemoveInput = imageInputElement.querySelector('input[name="avatar_remove"]');
                if (avatarRemoveInput) {
                    avatarRemoveInput.value = '';
                }
            }

            // Resetar estado das etapas
            this.currentMarkedOrder = null;
            if (this.stageSelect) {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(this.stageSelect).prop('disabled', false);
                } else {
                    this.stageSelect.disabled = false;
                }
            }

            // Esconder wrapper de etapas
            if (this.formativaWrapper) {
                this.formativaWrapper.style.display = 'none';
            }

            // Esconder seção de função religiosa
            if (this.religiousRoleWrapper) {
                this.religiousRoleWrapper.style.display = 'none';
            }

            // Resetar checkboxes
            this.isCurrentCheckboxes.forEach(cb => {
                cb.checked = false;
            });

            // Reativar todas as datas finais
            this.stageBlocks.forEach(block => {
                const slug = block.dataset.stageSlug;
                this.enableEndDate(slug);
            });
            
            // Resetar estado de edição
            this.editingMemberId = null;
            this.isEditMode = false;
            
            // Atualizar título do modal
            this.updateModalTitle();
        }
    }
    
    /**
     * Configura os eventos de ação da tabela (editar, ver, excluir)
     */
    setupTableActions() {
        // Usar event delegation para capturar cliques em elementos dinâmicos
        document.addEventListener('click', (e) => {
            const actionElement = e.target.closest('[data-action]');
            if (!actionElement) return;
            
            const action = actionElement.dataset.action;
            const memberId = actionElement.dataset.id;
            
            if (!memberId) return;
            
            e.preventDefault();
            
            switch (action) {
                case 'edit':
                    this.openEditModal(memberId);
                    break;
                case 'view':
                    this.openViewPage(memberId);
                    break;
                case 'delete':
                    this.confirmDelete(memberId);
                    break;
            }
        });
    }
    
    /**
     * Abre o modal para edição de um membro
     */
    async openEditModal(memberId) {
        this.setLoading(true);
        
        try {
            const url = this.config.editUrl.replace('__ID__', memberId);
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.editingMemberId = memberId;
                this.isEditMode = true;
                
                // Preencher o formulário com os dados
                this.populateForm(data.member);
                
                // Atualizar título do modal
                this.updateModalTitle();
                
                // Abrir o modal
                const bsModal = new bootstrap.Modal(this.modal);
                bsModal.show();
            } else {
                this.showError(data.message || 'Erro ao carregar dados do membro.');
            }
        } catch (error) {
            console.error('Erro ao carregar membro:', error);
            this.showError('Erro ao carregar dados do membro. Tente novamente.');
        } finally {
            this.setLoading(false);
        }
    }
    
    /**
     * Redireciona para a página de visualização do membro
     */
    openViewPage(memberId) {
        const url = this.config.showUrl.replace('__ID__', memberId);
        window.location.href = url;
    }
    
    /**
     * Confirma e executa a exclusão de um membro
     */
    async confirmDelete(memberId) {
        if (typeof Swal === 'undefined') {
            if (!confirm('Deseja realmente excluir este membro?')) return;
            this.deleteMember(memberId);
            return;
        }
        
        const result = await Swal.fire({
            title: 'Confirmar exclusão',
            text: 'Deseja realmente excluir este membro? Esta ação pode ser desfeita posteriormente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        });
        
        if (result.isConfirmed) {
            this.deleteMember(memberId);
        }
    }
    
    /**
     * Executa a exclusão do membro
     */
    async deleteMember(memberId) {
        try {
            const url = this.config.deleteUrl.replace('__ID__', memberId);
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showSuccess(data.message || 'Membro excluído com sucesso!');
                this.reloadDataTable();
                this.updateTabStats();
            } else {
                this.showError(data.message || 'Erro ao excluir o membro.');
            }
        } catch (error) {
            console.error('Erro ao excluir membro:', error);
            this.showError('Erro ao excluir o membro. Tente novamente.');
        }
    }
    
    /**
     * Preenche o formulário com os dados do membro
     */
    populateForm(member) {
        if (!this.form) return;
        
        // Campos básicos
        this.setFieldValue('nome', member.nome || member.name || '');
        this.setFieldValue('data_nascimento', member.data_nascimento ? this.formatDateToBR(member.data_nascimento) : '');
        this.setFieldValue('funcao', member.funcao || member.order_registration_number || '');
        this.setFieldValue('provincia', member.provincia || '');
        this.setFieldValue('cpf', member.cpf || '');
        this.setFieldValue('observacoes', member.observacoes || '');
        
        // Select de etapa de formação
        if (member.current_stage_id) {
            this.setSelectValue('current_stage_id', member.current_stage_id);
            this.updateVisibleStages();
        }
        
        // Checkbox disponível em todas as casas
        const disponivelCheckbox = this.form.querySelector('[name="disponivel_todas_casas"]');
        if (disponivelCheckbox) {
            disponivelCheckbox.checked = member.disponivel_todas_casas !== false;
        }
        
        // Avatar
        if (member.avatar_url) {
            const imageWrapper = this.form.querySelector('.image-input-wrapper');
            const imageInputElement = this.form.querySelector('[data-kt-image-input="true"]');
            if (imageWrapper) {
                imageWrapper.style.backgroundImage = `url('${member.avatar_url}')`;
            }
            if (imageInputElement) {
                imageInputElement.classList.remove('image-input-empty');
                imageInputElement.classList.add('image-input-changed');
            }
        }
        
        // Endereço de origem (vem estruturado do backend)
        if (member.endereco) {
            this.setFieldValue('cep', member.endereco.cep || '');
            this.setFieldValue('bairro', member.endereco.bairro || '');
            this.setFieldValue('logradouro', member.endereco.rua || '');
            this.setFieldValue('numero', member.endereco.numero || '');
            this.setFieldValue('localidade', member.endereco.cidade || '');
            this.setSelectValue('uf', member.endereco.uf || '');
        }
        
        // Períodos de formação
        if (member.formation_periods && member.formation_periods.length > 0) {
            // Primeiro mostrar as etapas baseado no current_stage_id
            // (já foi feito acima com updateVisibleStages)
            
            // Aguardar um tick para garantir que os blocos estejam visíveis
            setTimeout(() => {
                this.populateFormationPeriods(member.formation_periods);
            }, 100);
        }
    }
    
    /**
     * Preenche os períodos de formação
     */
    populateFormationPeriods(periods) {
        periods.forEach(period => {
            const block = document.querySelector(`.formation-stage-block[data-stage-id="${period.formation_stage_id}"]`);
            if (!block) return;
            
            // Garantir que o bloco esteja visível
            block.style.display = 'block';
            
            // Inicializar Select2 para os selects dentro do bloco se necessário
            const selects = block.querySelectorAll('select:not(.select2-hidden-accessible)');
            selects.forEach(select => {
                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(select).select2({
                        dropdownParent: $(this.config.modalId),
                        placeholder: 'Selecione o local',
                        allowClear: true
                    });
                }
            });
            
            const slug = block.dataset.stageSlug;
            
            // Data inicial
            const startDateInput = block.querySelector(`[name="stages[${slug}][start_date]"]`);
            if (startDateInput && period.start_date) {
                startDateInput.value = this.formatDateToBR(period.start_date);
            }
            
            // Data final
            const endDateInput = block.querySelector(`[name="stages[${slug}][end_date]"]`);
            if (endDateInput && period.end_date) {
                endDateInput.value = this.formatDateToBR(period.end_date);
            }
            
            // Local/Casa
            if (period.company_id) {
                this.setSelectValue(`stages[${slug}][company_id]`, period.company_id);
            }
            
            // Período atual
            const isCurrentCheckbox = block.querySelector(`[name="stages[${slug}][is_current]"]`);
            if (isCurrentCheckbox) {
                isCurrentCheckbox.checked = period.is_current;
                if (period.is_current) {
                    this.handleIsCurrentChange(isCurrentCheckbox);
                }
            }
        });
    }
    
    /**
     * Define o valor de um campo de formulário
     */
    setFieldValue(name, value) {
        const field = this.form.querySelector(`[name="${name}"]`);
        if (field) {
            field.value = value;
        }
    }
    
    /**
     * Define o valor de um select (incluindo Select2)
     */
    setSelectValue(name, value) {
        const select = this.form.querySelector(`[name="${name}"]`);
        if (!select) return;
        
        if (typeof $ !== 'undefined' && $.fn.select2 && $(select).hasClass('select2-hidden-accessible')) {
            $(select).val(value).trigger('change');
        } else {
            select.value = value;
        }
    }
    
    /**
     * Formata data de YYYY-MM-DD para DD/MM/YYYY
     */
    formatDateToBR(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length !== 3) return dateStr;
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    
    /**
     * Atualiza o título do modal
     */
    updateModalTitle() {
        const titleElement = this.modal?.querySelector('.modal-header h1, .modal-title');
        if (titleElement) {
            titleElement.textContent = this.isEditMode ? 'Editar Membro' : 'Novo Membro';
        }
        
        // Atualizar botões do footer
        this.updateFooterButtons();
    }
    
    /**
     * Atualiza os botões do footer do modal
     * Na página show em modo edit, mostra apenas "Atualizar" sem dropdown
     */
    updateFooterButtons() {
        const btnGroup = this.modal?.querySelector('.modal-footer .btn-group');
        const submitBtn = this.modal?.querySelector('#kt_modal_new_target_submit');
        const dropdownToggle = this.modal?.querySelector('.modal-footer .dropdown-toggle-split');
        const dropdownMenu = this.modal?.querySelector('.modal-footer .dropdown-menu');
        
        if (!submitBtn) return;
        
        // Se estiver na página show e em modo de edição
        if (this.config.isShowPage && this.isEditMode) {
            // Esconder dropdown
            if (dropdownToggle) dropdownToggle.style.display = 'none';
            if (dropdownMenu) dropdownMenu.style.display = 'none';
            
            // Alterar texto do botão para "Atualizar"
            const labelSpan = submitBtn.querySelector('.indicator-label');
            if (labelSpan) {
                labelSpan.textContent = 'Atualizar';
            }
        } else {
            // Modo normal - mostrar dropdown
            if (dropdownToggle) dropdownToggle.style.display = '';
            if (dropdownMenu) dropdownMenu.style.display = '';
            
            // Restaurar texto do botão
            const labelSpan = submitBtn.querySelector('.indicator-label');
            if (labelSpan) {
                labelSpan.textContent = 'Salvar';
            }
        }
    }
    
    /**
     * Exibe mensagem de sucesso
     */
    showSuccess(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: message,
                confirmButtonText: 'OK'
            });
        } else {
            alert(message);
        }
    }
    
    /**
     * Exibe mensagem de erro
     */
    showError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message,
                confirmButtonText: 'OK'
            });
        } else {
            alert(message);
        }
    }
    
    /**
     * Atualiza os contadores das tabs
     */
    async updateTabStats() {
        // Tentar pegar a URL de stats da configuração ou do container
        let statsUrl = this.config.statsUrl;
        
        if (!statsUrl) {
            // Tentar pegar do container da página
            const container = document.querySelector('[data-stats-url]');
            if (container) {
                statsUrl = container.dataset.statsUrl;
            }
        }
        
        if (!statsUrl) return;
        
        try {
            const response = await fetch(statsUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.config.csrfToken
                }
            });
            
            const stats = await response.json();
            
            // Atualizar os contadores das tabs
            Object.keys(stats).forEach(key => {
                // Buscar o elemento do contador pelo data-tab-key
                const tabButton = document.querySelector(`[data-tab-key="${key}"]`);
                if (tabButton) {
                    const countElement = tabButton.querySelector('.segmented-tab-count');
                    if (countElement) {
                        countElement.textContent = stats[key];
                    }
                }
            });
        } catch (error) {
            console.error('Erro ao atualizar stats das tabs:', error);
        }
    }
    
    /**
     * Recarrega a DataTable
     */
    reloadDataTable() {
        // Não faz nada se estiver na página show
        if (this.config.isShowPage) return;
        
        // Tentar diferentes formas de acessar a DataTable
        if (typeof LaravelDataTables !== 'undefined' && LaravelDataTables['secretary-table']) {
            LaravelDataTables['secretary-table'].ajax.reload(null, false);
        } else if (typeof $ !== 'undefined' && $.fn.DataTable) {
            const table = $('#secretary-table').DataTable();
            if (table) {
                table.ajax.reload(null, false);
            } else {
                // Fallback para qualquer DataTable na página
                $('.dataTable').DataTable().ajax.reload(null, false);
            }
        }
    }
}

// Exportar para uso global
window.DomusiaSecretary = DomusiaSecretary;
