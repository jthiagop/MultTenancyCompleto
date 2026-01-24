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

    function initializeSelect2() {
        document.querySelectorAll('select[data-control="select2"]').forEach(select => {
            if (!select.classList.contains('select2-hidden-accessible')) {
                if (typeof KTSelect2 !== 'undefined') {
                    new KTSelect2(select);
                } else if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    $(select).select2();
                }
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

        fetch('/conciliacao/contas-disponiveis', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                entidade_origem_id: entidadeOrigemId,
                bank_statement_id: conciliacaoId
            })
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

                // Reinicializa Select2
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    $(selectElement).select2('destroy').select2({
                        placeholder: "Selecione a conta de destino",
                        allowClear: true
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
        if (event.target.matches('.comprovacao-fiscal-check')) {
            const checkbox = event.target;
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
    }

    // ============================================================
    // 5. ALTERNAR ENTRE VISUALIZAÇÃO E EDIÇÃO
    // ============================================================

    function handleToggleEdit(event) {
        if (event.target.matches('[data-action="toggle-edit"]')) {
            const btn = event.target;
            const conciliacaoId = btn.dataset.conciliacaoId;
            const viewDiv = document.getElementById(`viewData-${conciliacaoId}`);
            const editDiv = document.getElementById(`editForm-${conciliacaoId}`);

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
    }

    // ============================================================
    // 6. SUBMETER FORMULÁRIO CORRETO AO CLICAR EM "CONCILIAR" (SUGESTÃO)
    // ============================================================

    function handleConciliarButton(event) {
        if (event.target.matches('[data-action="conciliar"]')) {
            const btn = event.target;
            const conciliacaoId = btn.dataset.conciliacaoId;

            // Identifica qual aba está ativa
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
                formToSubmit.submit();
            } else if (formToSubmit) {
                formToSubmit.reportValidity();
            }
        }
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
                formComponent.reportValidity();
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
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Sucesso! Mostrar feedback
                    showNotification('success', data.message || 'Lançamento conciliado com sucesso!');
                    
                    // Remover a linha da tabela ou recarregar
                    setTimeout(() => {
                        location.reload(); // ou remover a linha via DOM
                    }, 1500);
                } else {
                    // Erro do servidor
                    showNotification('error', data.message || 'Erro ao conciliar');
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                showNotification('error', error.message || 'Erro ao processar requisição');
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

    function showNotification(type, message) {
        // Usar bibliotecas existentes (Toastr, Flasher, etc)
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Sucesso' : 'Erro',
                text: message,
                timer: 3000
            });
        } else {
            // Fallback simples
            alert(message);
        }
    }

    // ============================================================
    // 7. INICIALIZAÇÃO NO CARREGAMENTO
    // ============================================================

    function init() {
        // Inicializa Select2 para todos os selects
        initializeSelect2();

        // Event Delegation: Todos os listeners anexados UMA VEZ ao document
        document.addEventListener('change', handleComprovacaoFiscalCheckbox);
        document.addEventListener('click', handleToggleEdit);
        document.addEventListener('click', handleConciliarButton);
        document.addEventListener('click', handleConciliarNovoLancamento);
        document.addEventListener('shown.bs.tab', handleTabSwitching);

        // Re-inicializa Select2 quando novos elementos são adicionados dinamicamente
        const observer = new MutationObserver(() => {
            initializeSelect2();
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Aguarda o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
