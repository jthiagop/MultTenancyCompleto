/**
 * Reconciliation Forms - Event Delegation
 * 
 * Carregado UMA VEZ no final da página
 * Usa Event Delegation + Data Attributes em vez de loops e IDs
 * 
 * Padrão:
 * - Cada form tem: data-conciliacao-id e data-form-type
 * - EventListeners são anexados ao documento (não a elementos individuais)
 * - Seletores relativos com .closest() e .find() evitam IDs desnecessários
 */

(function() {
    'use strict';

    // ============================================================
    // 1. INICIALIZAÇÃO DE SELECT2 E COMPONENTES
    // ============================================================

    function initializeSelect2(container = document) {
        container.querySelectorAll('select[data-control="select2"]').forEach(select => {
            // ✅ Evita re-inicializar: marca com data-select2-init
            if (select.dataset.select2Init === '1') return;
            
            select.dataset.select2Init = '1';

            if (typeof KTSelect2 !== 'undefined') {
                new KTSelect2(select);
            } else if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                $(select).select2({ width: '100%' });
            }
        });
    }

    // ============================================================
    // 2. GERENCIAR ABAS DE FORMULÁRIO (Novo Lançamento, Transferência, Buscar)
    // ============================================================

    function handleTabSwitching(event) {
        // Intercepta mudanças de abas
        if (event.target.matches('[role="tab"]')) {
            const button = event.target;
            const conciliacaoId = button.closest('[data-conciliacao-id]')?.dataset.conciliacaoId;

            if (conciliacaoId && button.getAttribute('data-bs-target')) {
                const targetId = button.getAttribute('data-bs-target');
                
                // Se é a aba de Transferência, carrega contas disponíveis
                if (targetId.includes('transferencia')) {
                    const select = document.querySelector(
                        `select[data-conciliacao-id="${conciliacaoId}"][data-form-type="transferencia"]`
                    );
                    if (select && select.children.length === 1 && select.children[0].textContent === 'Carregando contas...') {
                        loadAvailableAccounts(select);
                    }
                }
            }
        }
    }

    // ============================================================
    // 3. CARREGAR CONTAS DISPONÍVEIS VIA AJAX (Transferência)
    // ============================================================

    function loadAvailableAccounts(selectElement) {
        const conciliacaoId = selectElement.dataset.conciliacaoId;
        const entidadeOrigemId = selectElement.dataset.entidadeOrigemId;

        if (!conciliacaoId || !entidadeOrigemId) return;

        // ✅ Usar querystring para GET (não body)
        const params = new URLSearchParams({
            entidade_origem_id: entidadeOrigemId,
            bank_statement_id: conciliacaoId
        });

        fetch(`/conciliacao/contas-disponiveis?${params.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.contas && data.contas.length > 0) {
                selectElement.innerHTML = '<option value="">Selecione a conta de destino</option>';
                
                data.contas.forEach(conta => {
                    const option = document.createElement('option');
                    option.value = conta.id;
                    option.textContent = conta.nome + (conta.account_type_label ? ' - ' + conta.account_type_label : '');
                    selectElement.appendChild(option);
                });

                // ✅ Reinicializa Select2 sem destruir múltiplas vezes
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    if ($(selectElement).data('select2')) {
                        $(selectElement).select2('destroy');
                    }
                    $(selectElement).select2({
                        placeholder: "Selecione a conta de destino",
                        allowClear: true,
                        width: '100%'
                    });
                }
            } else {
                selectElement.innerHTML = '<option value="">Nenhuma conta disponível</option>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar contas:', error);
            selectElement.innerHTML = '<option value="">Erro ao carregar contas</option>';
        });
    }

    // ============================================================
    // 4. GERENCIAR CHECKBOX DE COMPROVAÇÃO FISCAL
    // ============================================================

    function handleComprovacaoFiscalCheckbox(event) {
        // ✅ Usar .closest() em vez de .matches()
        const checkbox = event.target.closest('.comprovacao-fiscal-check');
        if (!checkbox) return;

        const conciliacaoId = checkbox.dataset.conciliacaoId;
        const anexoContainer = document.querySelector(
            `.anexo-container[data-conciliacao-id="${conciliacaoId}"]`
        );

        if (!anexoContainer) return;

        if (checkbox.checked) {
            anexoContainer.classList.remove('d-none');
        } else {
            anexoContainer.classList.add('d-none');
        }
    }

    // ============================================================
    // 5. ALTERNAR ENTRE VISUALIZAÇÃO E EDIÇÃO
    // ============================================================

    function handleToggleEdit(event) {
        // ✅ Usar .closest() em vez de .matches()
        const btn = event.target.closest('[data-action="toggle-edit"]');
        if (!btn) return;

        const conciliacaoId = btn.dataset.conciliacaoId;
        
        // Tentar por ID (legado) ou por classe + data-attribute
        let viewDiv = document.getElementById(`viewData-${conciliacaoId}`);
        let editDiv = document.getElementById(`editForm-${conciliacaoId}`);
        
        // Fallback: buscar por classe suggestion-view/suggestion-edit
        if (!viewDiv || !editDiv) {
            viewDiv = document.querySelector(`.suggestion-view[data-conciliacao-id="${conciliacaoId}"]`);
            editDiv = document.querySelector(`.suggestion-edit[data-conciliacao-id="${conciliacaoId}"]`);
        }

        if (!viewDiv || !editDiv) return;

        if (editDiv.classList.contains('d-none')) {
            viewDiv.classList.add('d-none');
            editDiv.classList.remove('d-none');
            editDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            editDiv.classList.add('d-none');
            viewDiv.classList.remove('d-none');
            viewDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }

    // ============================================================
    // 6. SUBMETER FORMULÁRIO CORRETO AO CLICAR EM "CONCILIAR" (SUGESTÃO)
    // ============================================================

    function handleConciliarButton(event) {
        // ✅ Usar .closest() em vez de .matches()
        const btn = event.target.closest('[data-action="conciliar"]');
        if (!btn) return;

        const conciliacaoId = btn.dataset.conciliacaoId;
        const row = btn.closest('.row[data-conciliacao-id]');
        if (!row) return;

        // ✅ Verificar se é cenário de SUGESTÃO (tem suggestion-view)
        const suggestionView = row.querySelector(`.suggestion-view[data-conciliacao-id="${conciliacaoId}"]`);
        
        if (suggestionView) {
            // Cenário: conciliar sugestão existente via pivot
            handleConciliarSugestao(btn, row, conciliacaoId);
            return;
        }

        // Cenário: formulário com abas (Novo Lançamento, Transferência, etc.)
        const activeTab = document.querySelector(
            `[data-conciliacao-id="${conciliacaoId}"] [role="tab"].active`
        );

        if (!activeTab) return;

        const targetId = activeTab.getAttribute('data-bs-target');
        let formToSubmit;

        if (targetId.includes('novo-lancamento')) {
            formToSubmit = document.querySelector(
                `form[data-conciliacao-id="${conciliacaoId}"][data-form-type="novo-lancamento"]`
            );
        } else if (targetId.includes('transferencia')) {
            formToSubmit = document.querySelector(
                `form[data-conciliacao-id="${conciliacaoId}"][data-form-type="transferencia"]`
            );
        }

        if (formToSubmit && formToSubmit.checkValidity()) {
            // Dispara evento submit que será capturado pelo handler AJAX em conciliacoes.blade.php
            const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
            formToSubmit.dispatchEvent(submitEvent);
        } else if (formToSubmit) {
            formToSubmit.reportValidity();
        }
    }

    // ============================================================
    // 6.1. CONCILIAR SUGESTÃO EXISTENTE (PIVOT)
    // ============================================================

    function handleConciliarSugestao(button, row, conciliacaoId) {
        // Buscar o formulário de edição da sugestão que contém os dados hidden
        const editForm = row.querySelector(`.edit-suggestion-form[data-conciliacao-id="${conciliacaoId}"]`);
        
        if (!editForm) {
            showNotification('error', 'Formulário de conciliação não encontrado.');
            return;
        }

        // Pegar os dados do formulário hidden
        const bankStatementId = editForm.querySelector('input[name="bank_statement_id"]')?.value;
        const transacaoId = editForm.querySelector('input[name="transacao_financeira_id"]')?.value;
        const valorConciliado = editForm.querySelector('input[name="valor_conciliado"]')?.value;

        if (!bankStatementId || !transacaoId) {
            showNotification('error', 'Dados insuficientes para conciliação.');
            return;
        }

        // Confirmação
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Conciliar transação?',
                text: 'Deseja vincular este lançamento ao extrato bancário?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, conciliar',
                cancelButtonText: 'Cancelar',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-primary me-3',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executarConciliacaoPivot(button, row, bankStatementId, transacaoId, valorConciliado);
                }
            });
        } else {
            if (confirm('Deseja vincular este lançamento ao extrato bancário?')) {
                executarConciliacaoPivot(button, row, bankStatementId, transacaoId, valorConciliado);
            }
        }
    }

    function executarConciliacaoPivot(button, row, bankStatementId, transacaoId, valorConciliado) {
        // Desabilitar botão
        button.disabled = true;
        const originalContent = button.innerHTML;
        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Conciliando...';

        // Obter CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Enviar via AJAX para a rota pivot
        fetch('/conciliacao', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                bank_statement_id: bankStatementId,
                transacao_financeira_id: transacaoId,
                valor_conciliado: valorConciliado
            })
        })
        .then(response => response.json().then(data => ({ ok: response.ok, status: response.status, data })))
        .then(({ ok, status, data }) => {
            if (ok && data.success) {
                // Remover item com animação
                row.style.transition = 'opacity 0.3s, transform 0.3s';
                row.style.opacity = '0';
                row.style.transform = 'scale(0.95)';
                
                setTimeout(() => {
                    row.remove();

                    // Reinicializa estrelas
                    if (typeof window.suggestionStarManager !== 'undefined') {
                        window.suggestionStarManager.reinitialize();
                    }
                }, 300);

                // Atualizar contadores
                if (typeof window.carregarTotalPendentes === 'function') {
                    window.carregarTotalPendentes();
                }
                if (typeof window.carregarInformacoes === 'function') {
                    window.carregarInformacoes();
                }

                // Atualiza badges das tabs internas
                if (data.data && data.data.counts) {
                    ['all', 'received', 'paid'].forEach(tabKey => {
                        const tabBadge = document.querySelector(`#conciliacao-tab-${tabKey} .badge`);
                        if (tabBadge && data.data.counts[tabKey] !== undefined) {
                            const count = data.data.counts[tabKey];
                            tabBadge.textContent = count;
                            tabBadge.style.display = count > 0 ? 'inline-block' : 'none';
                        }
                    });
                }

                showNotification('success', data.message || 'Conciliação realizada com sucesso!');
            } else {
                showNotification('error', data.message || 'Erro ao conciliar.');
            }
        })
        .catch(error => {
            console.error('Erro na conciliação:', error);
            showNotification('error', 'Erro ao processar conciliação: ' + (error.message || 'Desconhecido'));
        })
        .finally(() => {
            button.disabled = false;
            button.innerHTML = originalContent;
        });
    }

    // ============================================================
    // 7. SUBMETER FORMULÁRIO NOVO LANÇAMENTO (BOTÃO DEDICADO)
    // ============================================================

    function handleConciliarNovoLancamento(event) {
        if (event.target.closest('[data-action="conciliar-novo-lancamento"]')) {
            const button = event.target.closest('[data-action="conciliar-novo-lancamento"]');
            const conciliacaoId = button.dataset.conciliacaoId;
            
            // Buscar o formulário da aba "Novo Lançamento" dentro do mesmo row
            const row = button.closest('.row');
            const formComponent = row.querySelector('[data-form-type="novo-lancamento"]');
            
            if (!formComponent) {
                console.error('Formulário do novo lançamento não encontrado');
                return;
            }

            // Validar o formulário antes de submeter
            if (!formComponent.checkValidity()) {
                // Listar campos inválidos para ajudar o usuário
                const invalidFields = Array.from(formComponent.querySelectorAll(':invalid'));
                const fieldNames = invalidFields.map(field => {
                    const label = formComponent.querySelector(`label[for="${field.id}"]`);
                    return label ? label.textContent.trim() : field.name || 'Campo desconhecido';
                });

                // Highlight dos campos inválidos
                invalidFields.forEach(field => {
                    field.classList.add('is-invalid');
                    
                    // Criar feedback com mensagem genérica
                    const existingFeedback = field.parentElement.querySelector('.invalid-feedback');
                    if (!existingFeedback) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback d-block';
                        feedback.textContent = 'Este campo é obrigatório';
                        field.parentElement.appendChild(feedback);
                    }

                    // Remover quando corrigir
                    const removeError = function() {
                        field.classList.remove('is-invalid');
                        const fb = field.parentElement.querySelector('.invalid-feedback');
                        if (fb) fb.remove();
                        field.removeEventListener('input', removeError);
                        field.removeEventListener('change', removeError);
                    };
                    
                    field.addEventListener('input', removeError);
                    field.addEventListener('change', removeError);
                });

                showNotification('error', 'Por favor, verifique os campos indicados');
                return;
            }

            // Desabilitar botão enquanto processa
            button.disabled = true;
            const originalContent = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processando...';

            // Coletar dados via FormData (suporta arquivos)
            const formData = new FormData(formComponent);

            // Enviar via AJAX
            fetch(formComponent.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Capturar resposta mesmo com erro HTTP
                return response.json().then(data => ({
                    ok: response.ok,
                    status: response.status,
                    data: data
                }));
            })
            .then(({ ok, status, data }) => {
                if (ok) {
                    console.log('✅ [Conciliação AJAX] Resposta sucesso:', data);
                    
                    // 1. Remove o item visualmente com animação
                    const rowElement = button.closest('.row[data-conciliacao-id]');
                    if (rowElement) {
                        rowElement.style.transition = 'opacity 0.3s, transform 0.3s';
                        rowElement.style.opacity = '0';
                        rowElement.style.transform = 'scale(0.95)';
                        
                        setTimeout(() => {
                            rowElement.remove();
                            console.log('🗑️ [Conciliação AJAX] Item removido do DOM');

                            // Reinicializa estrelas após remoção
                            if (typeof window.suggestionStarManager !== 'undefined') {
                                window.suggestionStarManager.reinitialize();
                            }
                        }, 300);
                    }

                    // 2. Atualiza contadores usando funções globais de tabs.blade.php
                    if (typeof window.carregarTotalPendentes === 'function') {
                        window.carregarTotalPendentes();
                    }

                    if (typeof window.carregarInformacoes === 'function') {
                        window.carregarInformacoes();
                    }

                    // Atualiza badges das tabs internas (all, received, paid)
                    if (data.data && data.data.counts) {
                        ['all', 'received', 'paid'].forEach(tabKey => {
                            const tabBadge = document.querySelector(`#conciliacao-tab-${tabKey} .badge`);
                            if (tabBadge && data.data.counts[tabKey] !== undefined) {
                                const count = data.data.counts[tabKey];
                                tabBadge.textContent = count;
                                tabBadge.style.display = count > 0 ? 'inline-block' : 'none';
                            }
                        });
                    }

                    // 3. Sucesso! Mostrar feedback
                    showNotification('success', data.message || 'Lançamento conciliado com sucesso!');
                } else if (status === 422) {
                    // Erro de validação - exibir erros específicos abaixo de cada campo
                    const errors = data.errors || {};
                    
                    // Limpar erros anteriores
                    formComponent.querySelectorAll('.is-invalid').forEach(field => {
                        field.classList.remove('is-invalid');
                    });
                    formComponent.querySelectorAll('.invalid-feedback').forEach(fb => {
                        fb.remove();
                    });

                    // Adicionar feedback para cada campo com erro
                    Object.entries(errors).forEach(([fieldName, fieldErrors]) => {
                        if (Array.isArray(fieldErrors) && fieldErrors.length > 0) {
                            // Usar a primeira mensagem de erro
                            addFieldErrorFeedback(formComponent, fieldName, fieldErrors[0]);
                        }
                    });

                    showNotification('error', 'Por favor, verifique os campos indicados');
                } else {
                    // Outro erro
                    showNotification('error', data.message || 'Erro ao conciliar');
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                showNotification('error', 'Erro ao processar requisição: ' + (error.message || 'Desconhecido'));
            })
            .finally(() => {
                // Reabilitar botão
                button.disabled = false;
                button.innerHTML = originalContent;
            });
        }
    }

    // ============================================================
    // 8. NOTIFICAÇÕES AO USUÁRIO
    // ============================================================

    function showNotification(type, message, errors = null) {
        // Se houver erros de validação, usar apenas para toast geral
        if (errors && typeof errors === 'object') {
            const hasErrors = Object.keys(errors).length > 0;
            
            // Se tem erros, mostrar apenas o título, detalhes estarão nos campos
            if (hasErrors && type === 'error') {
                message = 'Por favor, verifique os campos indicados abaixo';
            }
        }

        // Usar bibliotecas existentes (Toastr, Flasher, etc)
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Sucesso' : 'Atenção',
                text: message,
                timer: type === 'success' ? 3000 : 0,
                allowOutsideClick: true
            });
        } else {
            // Fallback simples
            alert(message);
        }
    }

    // ============================================================
    // 9. ADICIONAR MENSAGEM DE ERRO ABAIXO DO CAMPO
    // ============================================================

    function addFieldErrorFeedback(form, fieldName, errorMessage) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field) return;

        // Remover feedback anterior se existir
        const existingFeedback = field.parentElement.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }

        // Adicionar classe is-invalid ao campo
        field.classList.add('is-invalid');

        // Criar elemento de feedback
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback d-block';
        feedback.textContent = errorMessage;

        // Inserir após o campo
        field.parentElement.appendChild(feedback);

        // Remover feedback quando o usuário corrige o campo
        const removeErrorHandler = function() {
            field.classList.remove('is-invalid');
            if (feedback.parentElement) {
                feedback.remove();
            }
            field.removeEventListener('input', removeErrorHandler);
            field.removeEventListener('change', removeErrorHandler);
        };

        field.addEventListener('input', removeErrorHandler);
        field.addEventListener('change', removeErrorHandler);
    }

    // ============================================================
    // 8. GERENCIAMENTO DE UPLOAD DE ARQUIVO (COMPONENTE TENANT-FILE-ONE)
    // ============================================================

    function updateFileUI(fileInput, file) {
        const component = fileInput.closest('.tenant-file-one-component');
        if (!component) return;

        const id = fileInput.id;
        const dropZone = component.querySelector('.tenant-file-dropzone');
        const fileInfo = document.getElementById('file-info-' + id);
        const fileNameSpan = document.getElementById('filename-' + id);
        const fileSizeSpan = document.getElementById('filesize-' + id);

        if (!dropZone || !fileInfo) return;

        if (file) {
            dropZone.classList.add('d-none');
            fileInfo.classList.remove('d-none');
            if (fileNameSpan) fileNameSpan.textContent = file.name;
            
            if (fileSizeSpan) {
                let size = file.size;
                let unit = 'B';
                if (size > 1024) { size /= 1024; unit = 'KB'; }
                if (size > 1024) { size /= 1024; unit = 'MB'; }
                fileSizeSpan.textContent = size.toFixed(0) + unit;
            }
        } else {
            dropZone.classList.remove('d-none');
            fileInfo.classList.add('d-none');
            if (fileNameSpan) fileNameSpan.textContent = '';
            if (fileSizeSpan) fileSizeSpan.textContent = '';
            fileInput.value = ''; // Limpar input
        }
    }

    function handleFileInputChange(event) {
        if (event.target.classList.contains('tenant-file-input')) {
            const input = event.target;
            if (input.files && input.files[0]) {
                updateFileUI(input, input.files[0]);
            }
        }
    }

    function handleFileDropZoneClick(event) {
        const dropZone = event.target.closest('.tenant-file-dropzone');
        if (dropZone) {
            const inputId = dropZone.dataset.inputId;
            const input = document.getElementById(inputId);
            if (input) input.click();
        }
    }

    function handleFileRemoveClick(event) {
        const btn = event.target.closest('.tenant-file-remove');
        if (btn) {
            const inputId = btn.dataset.inputId;
            const input = document.getElementById(inputId);
            if (input) updateFileUI(input, null);
        }
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDragOver(e) {
        const dropZone = e.target.closest('.tenant-file-dropzone');
        if (dropZone) {
            preventDefaults(e);
            dropZone.classList.add('bg-gray-200');
        }
    }

    function handleDragLeave(e) {
        const dropZone = e.target.closest('.tenant-file-dropzone');
        if (dropZone) {
            preventDefaults(e);
            dropZone.classList.remove('bg-gray-200');
        }
    }

    function handleDrop(e) {
        const dropZone = e.target.closest('.tenant-file-dropzone');
        if (dropZone) {
            preventDefaults(e);
            dropZone.classList.remove('bg-gray-200');
            
            const inputId = dropZone.dataset.inputId;
            const input = document.getElementById(inputId);
            
            if (input && e.dataTransfer.files && e.dataTransfer.files[0]) {
                input.files = e.dataTransfer.files;
                updateFileUI(input, e.dataTransfer.files[0]);
            }
        }
    }

    // ============================================================
    // 7. INICIALIZAÇÃO NO CARREGAMENTO
    // ============================================================

    let select2InitTimer = null;

    /**
     * Agenda inicialização debounced do Select2
     * Deve ser chamado após injetar HTML via AJAX (após carregar tab)
     */
    function scheduleSelect2Init(container = document) {
        clearTimeout(select2InitTimer);
        select2InitTimer = setTimeout(() => {
            initializeSelect2(container);
        }, 50);
    }

    function init() {
        // Inicializa Select2 para selects já presentes na página
        initializeSelect2();

        // Event Delegation: Todos os listeners anexados UMA VEZ ao document
        document.addEventListener('change', handleComprovacaoFiscalCheckbox);
        document.addEventListener('click', handleToggleEdit);
        document.addEventListener('click', handleConciliarButton);
        document.addEventListener('click', handleConciliarNovoLancamento);
        document.addEventListener('shown.bs.tab', handleTabSwitching);

        // Listeners para Upload de Arquivo
        document.addEventListener('change', handleFileInputChange);
        document.addEventListener('click', handleFileDropZoneClick);
        document.addEventListener('click', handleFileRemoveClick);
        
        // Drag and Drop (Delegate to document to catch dynamically added zones)
        document.addEventListener('dragenter', handleDragOver);
        document.addEventListener('dragover', handleDragOver);
        document.addEventListener('dragleave', handleDragLeave);
        document.addEventListener('drop', handleDrop);
    }

    // Exponha métodos globalmente para usar em AJAX
    window.scheduleSelect2Init = scheduleSelect2Init;
    window.initializeSelect2 = initializeSelect2;

    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
