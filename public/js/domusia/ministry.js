"use strict";

/**
 * Classe para gerenciar os Ministérios do Membro Religioso
 */
class MemberMinistryManager {
    constructor(memberId, storeUrl, updateUrl) {
        this.memberId = memberId;
        this.storeUrl = storeUrl;
        this.updateUrl = updateUrl;
        this.modal = null;
        this.form = null;
        this.submitBtn = null;
        this.titleElement = null;
        
        this.init();
    }

    init() {
        // Elementos do DOM
        this.modal = document.getElementById('kt_modal_ministry');
        this.form = document.getElementById('kt_modal_ministry_form');
        this.submitBtn = document.getElementById('kt_modal_ministry_submit');
        this.titleElement = document.getElementById('kt_modal_ministry_title');

        if (!this.modal || !this.form) {
            console.warn('MemberMinistryManager: Modal ou formulário não encontrado');
            return;
        }

        this.bindEvents();
    }

    bindEvents() {
        // Evento de clique nos botões de editar
        document.querySelectorAll('.btn-edit-ministry').forEach(btn => {
            btn.addEventListener('click', (e) => this.handleEditClick(e));
        });

        // Evento de submit do formulário
        if (this.submitBtn) {
            this.submitBtn.addEventListener('click', (e) => this.handleSubmit(e));
        }

        // Limpar formulário quando o modal fechar
        if (this.modal) {
            this.modal.addEventListener('hidden.bs.modal', () => this.resetForm());
        }
    }

    handleEditClick(e) {
        const btn = e.currentTarget;
        
        // Preencher os campos hidden
        document.getElementById('ministry_id').value = btn.dataset.ministryId || '';
        document.getElementById('ministry_type_id').value = btn.dataset.ministryTypeId || '';
        
        // Preencher os campos do formulário
        const dateInput = this.form.querySelector('[name="ministry_date"]');
        const ministerInput = this.form.querySelector('[name="minister_name"]');
        const dioceseInput = this.form.querySelector('[name="diocese_name"]');
        const notesInput = this.form.querySelector('[name="ministry_notes"]');

        if (dateInput) dateInput.value = btn.dataset.date || '';
        if (ministerInput) ministerInput.value = btn.dataset.ministerName || '';
        if (dioceseInput) dioceseInput.value = btn.dataset.dioceseName || '';
        if (notesInput) notesInput.value = btn.dataset.notes || '';

        // Atualizar título do modal
        const typeName = btn.dataset.ministryTypeName || 'Ministério';
        const isEdit = btn.dataset.ministryId ? 'Editar' : 'Registrar';
        if (this.titleElement) {
            this.titleElement.textContent = `${isEdit} ${typeName}`;
        }
    }

    async handleSubmit(e) {
        e.preventDefault();

        const ministryId = document.getElementById('ministry_id').value;
        const ministryTypeId = document.getElementById('ministry_type_id').value;

        // Coletar e formatar data
        let dateValue = this.form.querySelector('[name="ministry_date"]')?.value || '';
        
        // Converter de dd/mm/yyyy para yyyy-mm-dd se necessário
        if (dateValue && dateValue.includes('/')) {
            const parts = dateValue.split('/');
            if (parts.length === 3) {
                dateValue = `${parts[2]}-${parts[1]}-${parts[0]}`;
            }
        }

        // Coletar dados do formulário
        const formData = {
            ministry_type_id: ministryTypeId,
            ministry_date: dateValue,
            minister_name: this.form.querySelector('[name="minister_name"]')?.value || '',
            diocese_name: this.form.querySelector('[name="diocese_name"]')?.value || '',
            ministry_notes: this.form.querySelector('[name="ministry_notes"]')?.value || '',
        };

        // Validação básica
        if (!formData.ministry_date || !formData.minister_name || !formData.diocese_name) {
            if (typeof toastr !== 'undefined') {
                toastr.warning('Por favor, preencha todos os campos obrigatórios.');
            } else {
                alert('Por favor, preencha todos os campos obrigatórios.');
            }
            return;
        }

        // Determinar URL e método
        let url = this.storeUrl;
        let method = 'POST';
        
        if (ministryId) {
            url = this.updateUrl.replace('__MINISTRY_ID__', ministryId);
            method = 'PUT';
        }

        // Mostrar loading no botão
        this.setLoading(true);

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(formData),
            });

            const result = await response.json();

            if (result.success) {
                // Atualizar a linha da tabela
                this.updateTableRow(result.ministry);

                // Fechar modal
                const bsModal = bootstrap.Modal.getInstance(this.modal);
                if (bsModal) bsModal.hide();

                // Mostrar toast de sucesso
                if (typeof toastr !== 'undefined') {
                    toastr.success(result.message);
                }
            } else {
                // Mostrar toast de erro
                if (typeof toastr !== 'undefined') {
                    toastr.error(result.message || 'Ocorreu um erro ao salvar.');
                } else {
                    alert(result.message || 'Ocorreu um erro ao salvar.');
                }
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            if (typeof toastr !== 'undefined') {
                toastr.error('Ocorreu um erro de conexão. Tente novamente.');
            } else {
                alert('Ocorreu um erro de conexão. Tente novamente.');
            }
        } finally {
            this.setLoading(false);
        }
    }

    updateTableRow(ministry) {
        // Encontrar a linha da tabela pelo ministry_type_id
        const row = document.querySelector(`tr[data-ministry-type-id="${ministry.ministry_type_id}"]`);
        
        if (!row) {
            console.warn('Linha da tabela não encontrada para ministry_type_id:', ministry.ministry_type_id);
            return;
        }

        // Atualizar células
        const cells = row.querySelectorAll('td');
        
        // Célula de data (índice 1)
        if (cells[1]) {
            cells[1].innerHTML = `<span class="text-gray-800">${ministry.date_formatted}</span>`;
        }

        // Célula de ministrante (índice 2)
        if (cells[2]) {
            cells[2].innerHTML = ministry.minister_name 
                ? `<span class="text-gray-600">${ministry.minister_name}</span>`
                : `<span class="text-muted">-</span>`;
        }

        // Célula de diocese (índice 3)
        if (cells[3]) {
            cells[3].innerHTML = ministry.diocese_name 
                ? `<span class="text-gray-600">${ministry.diocese_name}</span>`
                : `<span class="text-muted">-</span>`;
        }

        // Atualizar data-attributes do botão de edição
        const editBtn = row.querySelector('.btn-edit-ministry');
        if (editBtn) {
            editBtn.dataset.ministryId = ministry.id;
            editBtn.dataset.date = ministry.date;
            editBtn.dataset.ministerName = ministry.minister_name || '';
            editBtn.dataset.dioceseName = ministry.diocese_name || '';
            editBtn.dataset.notes = ministry.notes || '';
        }
    }

    setLoading(isLoading) {
        if (!this.submitBtn) return;

        if (isLoading) {
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
        }
        document.getElementById('ministry_id').value = '';
        document.getElementById('ministry_type_id').value = '';
        
        if (this.titleElement) {
            this.titleElement.textContent = 'Registrar Ministério';
        }
    }
}

// Exportar para uso global
window.MemberMinistryManager = MemberMinistryManager;
