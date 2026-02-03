/**
 * Tenant DataTable Pane Module
 * 
 * Inicializa e gerencia múltiplos panes de DataTables de forma isolada e escalável.
 * Cada pane é identificado por data-pane-id e funciona independentemente.
 * 
 * @version 2.0.1 - 2026-01-29
 */

(function() {
    'use strict';
    
    // Versão do script para debug de cache
    const SCRIPT_VERSION = '2.0.1';

    // Mensagem de estado vazio (reutilizável)
    const EMPTY_MESSAGE = `
        <div class="d-flex flex-column align-items-center justify-content-center py-10">
            <img src="/assets/media/illustrations/traco-fino/chasing-money.svg" class="mw-300px mb-5" alt="Nenhum resultado encontrado" />
            <div class="fs-3 fw-bold text-gray-900 mb-2">Nenhum resultado encontrado</div>
            <div class="fs-6 text-gray-500">Selecione outros filtros para refazer sua busca</div>
        </div>
    `;

    // Configuração de idioma do DataTables
    const DATATABLE_LANGUAGE = {
        sEmptyTable: EMPTY_MESSAGE,
        sZeroRecords: EMPTY_MESSAGE,
        sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
        sInfoEmpty: "Mostrando 0 até 0 de 0 registros",
        sInfoFiltered: "(Filtrados de _MAX_ registros)",
        sInfoPostFix: "",
        sInfoThousands: ".",
        sLengthMenu: "_MENU_ resultados por página",
        sSearch: "Pesquisar",
        oPaginate: {
            sNext: "<i class='fas fa-chevron-right'></i>",
            sPrevious: "<i class='fas fa-chevron-left'></i>",
            sFirst: "<i class='fas fa-chevron-double-left'></i>",
            sLast: "<i class='fas fa-chevron-double-right'></i>"
        },
        oAria: {
            sSortAscending: ": Ordenar colunas de forma ascendente",
            sSortDescending: ": Ordenar colunas de forma descendente"
        }
    };

    /**
     * Inicializa um pane de DataTable
     * @param {HTMLElement} paneEl - Elemento raiz do pane (.tenant-datatable-pane)
     */
    function initPane(paneEl) {
        console.log(`[TenantDataTablePane] Iniciando initPane para elemento:`, paneEl.id || paneEl.dataset.tableId);
        
        // Ler configuração dos data-* attributes
        const config = {
            paneId: paneEl.dataset.paneId,
            tableId: paneEl.dataset.tableId,
            filterId: paneEl.dataset.filterId || paneEl.dataset.tableId, // Fallback para tableId se não fornecido
            key: paneEl.dataset.key,
            tipo: paneEl.dataset.tipo,
            statsUrl: paneEl.dataset.statsUrl,
            dataUrl: paneEl.dataset.dataUrl,
            columns: JSON.parse(paneEl.dataset.columnsJson || '[]'),
            defaultOrder: JSON.parse(paneEl.dataset.defaultOrder || '[[1, "desc"]]'),
            pageLength: parseInt(paneEl.dataset.pageLength || '50', 10),
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        };
        
        console.log(`[TenantDataTablePane] Config do pane:`, {
            paneId: config.paneId,
            tableId: config.tableId,
            key: config.key,
            tipo: config.tipo,
            statsUrl: config.statsUrl,
            dataUrl: config.dataUrl,
            columnsCount: config.columns.length
        });

        // Estado interno do pane
        const state = {
            dataTable: null,
            currentStart: moment().startOf('month'),
            currentEnd: moment().endOf('month'),
            currentStatus: new URLSearchParams(window.location.search).get('status') || 'total'
        };

        /**
         * Atualiza as estatísticas das tabs
         */
        function updateStats() {
            if (!config.statsUrl) return;

            const params = new URLSearchParams({
                tipo: config.tipo,
                start_date: state.currentStart.format('YYYY-MM-DD'),
                end_date: state.currentEnd.format('YYYY-MM-DD')
            });

            // Se for extrato, adicionar flags
            if (config.key === 'extrato') {
                params.append('tab', 'extrato');
                params.append('is_extrato', 'true');
            }

            // Adicionar filtro de conta se houver seleção
            const accountSelect = paneEl.querySelector(`#account-filter-${config.filterId}`);
            if (accountSelect) {
                if (accountSelect.multiple) {
                    const selectedValues = Array.from(accountSelect.selectedOptions)
                        .map(option => option.value)
                        .filter(value => value !== '');
                    if (selectedValues.length > 0) {
                        selectedValues.forEach(value => {
                            params.append('entidade_id[]', value);
                        });
                    }
                } else if (accountSelect.value) {
                    params.append('entidade_id', accountSelect.value);
                }
            }

            fetch(config.statsUrl + '?' + params)
                .then(response => response.json())
                .then(data => {
                    console.log(`[Pane ${config.paneId}] Stats recebidas:`, data);
                    
                    // Determinar chaves de tabs baseado no tipo de pane
                    let tabKeys;
                    
                    if (config.key === 'extrato') {
                        // Tabs específicas para extrato
                        tabKeys = ['receitas_aberto', 'receitas_realizadas', 'despesas_aberto', 'despesas_realizadas', 'total'];
                    } else {
                        // Tabs padrão para contas a receber/pagar
                        tabKeys = ['vencidos', 'hoje', 'a_vencer'];
                        const receivedKey = config.tipo === 'entrada' ? 'recebidos' : 'pagos';
                        tabKeys.push(receivedKey, 'total');
                    }

                    // Atualizar os valores nas tabs dentro deste pane apenas
                    tabKeys.forEach(key => {
                        const tabElement = paneEl.querySelector(`[data-tab-key="${key}"]`);
                        if (tabElement) {
                            // Tentar .segmented-tab-count primeiro (novo componente), depois .fs-2 (fallback)
                            let valueElement = tabElement.querySelector('.segmented-tab-count');
                            if (!valueElement) {
                                valueElement = tabElement.querySelector('.fs-2');
                            }
                            
                            if (valueElement) {
                                const value = data[key] || '0,00';
                                valueElement.textContent = value;
                                console.log(`[Pane ${config.paneId}] Tab ${key} atualizada: ${value}`);
                                
                                // Se o valor for negativo, aplicar classe text-danger (vermelho)
                                if (value.startsWith('-')) {
                                    // Remover outras classes de cor e adicionar text-danger
                                    valueElement.classList.remove('text-primary', 'text-success', 'text-warning', 'text-info', 'text-secondary');
                                    valueElement.classList.add('text-danger');
                                }
                            } else {
                                console.warn(`[Pane ${config.paneId}] Elemento de valor não encontrado para tab ${key}`);
                            }
                        } else {
                            console.warn(`[Pane ${config.paneId}] Tab element não encontrado para key: ${key}`);
                        }
                    });
                })
                .catch(error => {
                    console.error(`[Pane ${config.paneId}] Erro ao atualizar estatísticas:`, error);
                });
        }

        /**
         * Atualiza contador de selecionados e estado do botão de ações em lote
         */
        function updateSelectionCount() {
            const selectedCountElement = paneEl.querySelector(`#selected-count-${config.tableId}`);
            const batchActionsBtn = paneEl.querySelector(`#batch-actions-btn-${config.tableId}`);

            // Contar checkboxes selecionados (exceto o checkbox "selecionar todos")
            const checkboxes = paneEl.querySelectorAll(`#${config.tableId} tbody .form-check-input.row-check[type="checkbox"]`);
            let selectedCount = 0;

            checkboxes.forEach(function(checkbox) {
                if (checkbox.checked) {
                    selectedCount++;
                }
            });

            // Atualizar contador
            if (selectedCountElement) {
                selectedCountElement.textContent = selectedCount + ' registro(s) selecionado(s)';
            }

            // Habilitar/desabilitar botão de ações em lote
            if (batchActionsBtn) {
                if (selectedCount > 0) {
                    batchActionsBtn.style.pointerEvents = '';
                    batchActionsBtn.style.opacity = '';
                    batchActionsBtn.style.cursor = '';
                    batchActionsBtn.classList.remove('disabled');
                } else {
                    batchActionsBtn.style.pointerEvents = 'none';
                    batchActionsBtn.style.opacity = '0.65';
                    batchActionsBtn.style.cursor = 'not-allowed';
                    batchActionsBtn.classList.add('disabled');
                }
            }
        }

        /**
         * Obtém IDs dos registros selecionados
         */
        function getSelectedIds() {
            const checkboxes = paneEl.querySelectorAll(`#${config.tableId} tbody .form-check-input.row-check[type="checkbox"]:checked`);
            const ids = [];
            checkboxes.forEach(function(checkbox) {
                const id = checkbox.value;
                if (id && id !== '1') {
                    ids.push(id);
                }
            });
            return ids;
        }

        /**
         * Exibe mensagem usando Flasher ou fallback
         */
        function showFlasherMessage(type, message) {
            if (typeof notify !== 'undefined' && typeof notify[type] === 'function') {
                notify[type](message);
            } else if (typeof flasher !== 'undefined' && typeof flasher[type] === 'function') {
                flasher[type](message);
            } else {
                console.warn(`[Pane ${config.paneId}] Flasher não disponível. Mensagem:`, type + ':', message);
                alert(message);
            }
        }

        /**
         * Inicializa/recarrega a DataTable
         */
        function initDataTable(status = state.currentStatus) {
            if (state.dataTable) {
                state.dataTable.destroy();
            }

            // Atualizar visibilidade da coluna Saldo
            toggleSaldoColumn(status);

            // Construir array de colunas do DataTables a partir de config.columns
            const dtColumns = config.columns.map((col, index) => {
                // Para colunas especiais, usar render customizado se necessário
                if (col.key === 'checkbox' || col.key === 'acoes') {
                    return {
                        data: index,
                        orderable: col.orderable !== false
                    };
                }
                return {
                    data: index,
                    orderable: col.orderable !== false
                };
            });

            // Reset visibility states (show skeleton, hide table)
            const skeleton = paneEl.querySelector(`#skeleton-${config.tableId}`);
            const tableWrapper = paneEl.querySelector(`#table-wrapper-${config.tableId}`);
            
            if (skeleton) skeleton.classList.remove('d-none');
            if (tableWrapper) tableWrapper.classList.add('d-none');

            // Verificar se DataTable está disponível
            if (!$.fn.DataTable) {
                console.error('[DataTable] jQuery DataTables não está carregado!');
                return;
            }

            state.dataTable = $(`#${config.tableId}`).DataTable({
                processing: true,
                serverSide: true,
                info: false,
                ajax: {
                    url: config.dataUrl,
                    data: function(d) {
                        d.tipo = config.tipo;
                        d.status = status;
                        d.start_date = state.currentStart.format('YYYY-MM-DD');
                        d.end_date = state.currentEnd.format('YYYY-MM-DD');
                        
                        // Debug: log do status sendo enviado
                        console.log('[DataTable AJAX] Enviando dados:', {
                            tipo: d.tipo,
                            status: d.status,
                            start_date: d.start_date,
                            end_date: d.end_date
                        });

                        // Detectar se é extrato
                        if (config.key === 'extrato') {
                            d.is_extrato = 'true';
                            d.tab = 'extrato';
                        }

                        // Adicionar filtros do componente tenant-datatable-filters (escopo ao pane)
                        const searchInput = paneEl.querySelector(`#search-${config.filterId}`);
                        if (searchInput && searchInput.value) {
                            d.search = { value: searchInput.value };
                        }

                        const accountSelect = paneEl.querySelector(`#account-filter-${config.filterId}`);
                        if (accountSelect) {
                            if (accountSelect.multiple) {
                                const selectedValues = Array.from(accountSelect.selectedOptions).map(option => option.value);
                                if (selectedValues.length > 0) {
                                    d.entidade_id = selectedValues;
                                }
                            } else if (accountSelect.value) {
                                d.entidade_id = accountSelect.value;
                            }
                        }

                        const situacaoSelect = paneEl.querySelector(`#situacao-filter-${config.filterId}`);
                        if (situacaoSelect && situacaoSelect.value) {
                            d.situacao = situacaoSelect.value;
                        }
                    }
                },
                columns: dtColumns,
                order: config.defaultOrder,
                pageLength: config.pageLength,
                language: DATATABLE_LANGUAGE,
                initComplete: function() {
                    // Hide skeleton, show table
                    if (skeleton) skeleton.classList.add('d-none');
                    if (tableWrapper) {
                        tableWrapper.classList.remove('d-none');
                        // Add fade in animation for smooth transition
                        tableWrapper.style.animation = 'fadeIn 0.5s';
                    }
                }
            });

            // Re-inicializar menus e tooltips após cada renderização da tabela
            state.dataTable.on('draw', function() {
                // Atualizar visibilidade da coluna Saldo após cada draw
                toggleSaldoColumn(state.currentStatus);

                // Inicializar menus do Metronic (escopo ao pane)
                if (typeof KTMenu !== 'undefined') {
                    const menuElements = paneEl.querySelectorAll('[data-kt-menu]');
                    menuElements.forEach(function(el) {
                        try {
                            KTMenu.createInstances();
                        } catch (e) {
                            console.warn(`[Pane ${config.paneId}] Erro ao inicializar menu:`, e);
                        }
                    });
                }

                // Inicializar tooltips do Bootstrap (escopo ao pane, dispose antes)
                if (typeof bootstrap !== 'undefined') {
                    const tooltipElements = paneEl.querySelectorAll('[data-bs-toggle="tooltip"]');
                    tooltipElements.forEach(function(el) {
                        const existingTooltip = bootstrap.Tooltip.getInstance(el);
                        if (existingTooltip) {
                            existingTooltip.dispose();
                        }
                        try {
                            new bootstrap.Tooltip(el);
                        } catch (e) {
                            console.warn(`[Pane ${config.paneId}] Erro ao inicializar tooltip:`, e);
                        }
                    });
                }

                // Resetar contador após draw
                updateSelectionCount();

                // Adicionar event listeners aos checkboxes (escopo ao pane)
                const checkboxes = paneEl.querySelectorAll(`#${config.tableId} tbody .form-check-input.row-check[type="checkbox"]`);
                const selectAllCheckbox = paneEl.querySelector(`#${config.tableId} thead .form-check-input[type="checkbox"]`);

                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        updateSelectionCount();
                    });
                });

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        setTimeout(function() {
                            updateSelectionCount();
                        }, 100);
                    });
                }
            });

            // Inicializar contador no carregamento inicial
            setTimeout(function() {
                updateSelectionCount();
            }, 500);
        }

        /**
         * Recarrega a tabela
         */
        function reloadTable() {
            if (state.dataTable) {
                state.dataTable.ajax.reload();
            }
        }

        /**
         * Atualiza estilos das tabs de status dentro do pane
         */
        function updateTabStyles(activeStatus) {
            const statusTabs = paneEl.querySelectorAll(`[data-status-tab]`);
            statusTabs.forEach(tab => {
                const tabStatus = tab.dataset.statusTab || 'total';
                const isActive = tabStatus === activeStatus;
                const activeColor = tab.dataset.activeColor || '#009ef7';

                if (isActive) {
                    tab.style.borderTopColor = activeColor;
                    tab.classList.add('active');
                } else {
                    tab.style.borderTopColor = 'transparent';
                    tab.classList.remove('active');
                }
            });
        }

        /**
         * Mostra/oculta a coluna "Saldo" no Extrato baseado na tab ativa
         * A coluna Saldo só deve aparecer na tab "Total do Período"
         */
        function toggleSaldoColumn(activeStatus) {
            // Só aplicar no Extrato
            if (config.key !== 'extrato') return;

            const table = paneEl.querySelector(`#${config.tableId}`);
            if (!table) return;

            // Encontrar o índice da coluna "Saldo (R$)"
            const headers = table.querySelectorAll('thead th');
            let saldoColumnIndex = -1;
            
            headers.forEach((th, index) => {
                if (th.textContent.trim().includes('Saldo')) {
                    saldoColumnIndex = index;
                }
            });

            if (saldoColumnIndex === -1) return;

            // Mostrar ou ocultar a coluna baseado no status
            const shouldShow = activeStatus === 'total';

            // Ocultar/mostrar header
            if (headers[saldoColumnIndex]) {
                headers[saldoColumnIndex].style.display = shouldShow ? '' : 'none';
            }

            // Ocultar/mostrar células do body
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells[saldoColumnIndex]) {
                    cells[saldoColumnIndex].style.display = shouldShow ? '' : 'none';
                }
            });
        }

        // Escutar mudanças no período do filtro (escopo ao pane)
        document.addEventListener('periodChanged', function(event) {
            // Verifica se o evento é para este pane (pode ser tableId ou filterId)
            const eventTableId = event.detail.tableId;
            const matches = eventTableId === config.filterId || 
                          eventTableId === config.tableId ||
                          config.filterId === eventTableId ||
                          config.tableId === eventTableId;
            
            console.log(`[Pane ${config.paneId}] Evento periodChanged recebido:`, {
                eventTableId: eventTableId,
                configFilterId: config.filterId,
                configTableId: config.tableId,
                matches: matches
            });
            
            if (matches) {
                console.log(`[Pane ${config.paneId}] Período alterado:`, event.detail);
                state.currentStart = event.detail.start;
                state.currentEnd = event.detail.end;
                updateStats();
                reloadTable();
            } else {
                console.log(`[Pane ${config.paneId}] Evento periodChanged ignorado (tableId não corresponde)`);
            }
        });

        // Escutar evento de pesquisa (escopo ao pane)
        document.addEventListener('searchTriggered', function(event) {
            // Verifica se o evento é para este pane (pode ser tableId ou filterId)
            const eventTableId = event.detail.tableId;
            const matches = eventTableId === config.filterId || 
                          eventTableId === config.tableId ||
                          config.filterId === eventTableId ||
                          config.tableId === eventTableId;
            
            if (matches) {
                console.log(`[Pane ${config.paneId}] Busca acionada:`, event.detail);
                reloadTable();
            }
        });

        // Escutar evento de aplicação de seleção (escopo ao pane)
        document.addEventListener('selectApplied', function(event) {
            // Verifica se o evento é para este pane (pode ser tableId ou filterId)
            const expectedSelectId = `account-filter-${config.filterId}`;
            const altSelectId = `account-filter-${config.tableId}`;
            const matches = event.detail.selectId === expectedSelectId || 
                          event.detail.selectId === altSelectId;
            
            if (matches) {
                updateStats();
                reloadTable();
            }
        });

        // Escutar mudanças nas tabs de status (escopo ao pane)
        const statusTabs = paneEl.querySelectorAll(`[data-status-tab]`);
        statusTabs.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const status = this.dataset.statusTab || 'total';

                // Atualizar URL sem recarregar
                const url = new URL(this.href || window.location.href);
                url.searchParams.set('status', status);
                window.history.pushState({ status: status }, '', url.toString());

                // Atualizar estilos visuais das tabs
                updateTabStyles(status);

                // Atualizar status e recarregar dados
                state.currentStatus = status;
                initDataTable(status);
                updateStats();
                
                // Atualizar visibilidade da coluna Saldo
                toggleSaldoColumn(status);
            });
        });

        // Suportar navegação do navegador (voltar/avançar)
        window.addEventListener('popstate', function(event) {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status') || 'total';
            state.currentStatus = status;

            updateTabStyles(status);

            initDataTable(status);
            updateStats();
            
            // Atualizar visibilidade da coluna Saldo
            toggleSaldoColumn(status);
        });

        // Handlers para ações em lote (escopo ao pane)
        paneEl.addEventListener('click', function(e) {
            const batchActionBtn = e.target.closest('[data-batch-action]');
            if (!batchActionBtn) return;

            const action = batchActionBtn.dataset.batchAction;
            const tableIdAttr = batchActionBtn.dataset.tableId;

            // Verificar se o botão pertence a este pane
            if (tableIdAttr && tableIdAttr !== config.tableId) return;

            const ids = getSelectedIds();
            if (ids.length === 0) {
                showFlasherMessage('warning', 'Nenhum registro selecionado.');
                return;
            }

            let route, confirmMessage, successMessage, errorMessage, bodyData = { ids: ids };

            switch (action) {
                case 'markAsPaid':
                    if (!confirm(`Deseja marcar ${ids.length} registro(s) como pago?`)) return;
                    route = batchActionBtn.dataset.markAsPaidRoute || '/banco/batch-mark-as-paid';
                    successMessage = 'Registros marcados como pagos com sucesso.';
                    errorMessage = 'Erro ao marcar registros como pagos.';
                    bodyData.data_pagamento = new Date().toISOString().split('T')[0];
                    break;

                case 'markAsOpen':
                    if (!confirm(`Deseja marcar ${ids.length} registro(s) como em aberto?`)) return;
                    route = batchActionBtn.dataset.markAsOpenRoute || '/banco/batch-mark-as-open';
                    successMessage = 'Registros marcados como em aberto com sucesso.';
                    errorMessage = 'Erro ao marcar registros como em aberto.';
                    break;

                case 'delete':
                    if (!confirm(`Deseja realmente excluir ${ids.length} registro(s)? Esta ação não pode ser desfeita.`)) return;
                    route = batchActionBtn.dataset.deleteRoute || '/banco/batch-delete';
                    successMessage = 'Registros excluídos com sucesso.';
                    errorMessage = 'Erro ao excluir registros.';
                    break;

                default:
                    console.warn(`[Pane ${config.paneId}] Ação desconhecida:`, action);
                    return;
            }

            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
                },
                body: JSON.stringify(bodyData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFlasherMessage('success', data.message || successMessage);
                    reloadTable();
                    updateStats();
                } else {
                    showFlasherMessage('error', data.message || errorMessage);
                }
            })
            .catch(error => {
                console.error(`[Pane ${config.paneId}] Erro:`, error);
                showFlasherMessage('error', 'Erro ao processar a solicitação.');
            });
        });

        // Handler para "Informar pagamento" individual (escopo ao pane)
        paneEl.addEventListener('click', function(e) {
            const informarBtn = e.target.closest('[data-action="informarPagamento"]');
            if (!informarBtn) return;

            const transacaoId = informarBtn.dataset.transacaoId;
            if (!transacaoId) return;

            if (!confirm('Deseja marcar esta transação como paga?')) return;

            const route = informarBtn.dataset.route || '/banco/mark-as-paid';

            fetch(route, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken
                },
                body: JSON.stringify({
                    id: transacaoId,
                    data_pagamento: new Date().toISOString().split('T')[0]
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showFlasherMessage('success', data.message || 'Transação marcada como paga com sucesso.');
                    reloadTable();
                    updateStats();
                } else {
                    showFlasherMessage('error', data.message || 'Erro ao marcar transação como paga.');
                }
            })
            .catch(error => {
                console.error(`[Pane ${config.paneId}] Erro:`, error);
                showFlasherMessage('error', 'Erro ao processar a solicitação.');
            });
        });

        // Inicializar quando o pane estiver visível
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (paneEl.classList.contains('show')) {
                        updateStats();
                        if (!state.dataTable) {
                            initDataTable();
                        }
                    }
                }
            });
        });

        observer.observe(paneEl, { attributes: true });

        // Inicializar imediatamente se já estiver ativo
        if (paneEl.classList.contains('show')) {
            updateStats();
            initDataTable();
            // Garantir visibilidade correta da coluna Saldo
            toggleSaldoColumn(state.currentStatus);
        }

        // Armazenar referência ao state no elemento para debug (opcional)
        paneEl._tenantPaneState = state;
        
        // ========================================
        // Event Listener: Atualização ao Salvar Transação
        // ========================================
        // Escutar evento global de criação/atualização de transações
        // Isso garante que STATS e TABELA atualizem juntas
        if (window.DominusEvents) {
            DominusEvents.on('transaction.created', function(data) {
                console.log(`[Pane ${config.paneId}] Transação criada/atualizada, recarregando stats e tabela...`, data);
                
                // 1. Atualizar estatísticas das tabs
                updateStats();
                
                // 2. Recarregar DataTable (se existir e estiver inicializado)
                if (state.dataTable) {
                    state.dataTable.ajax.reload(null, false); // false = manter na página atual
                }
            });
            
            console.log(`[Pane ${config.paneId}] Event listener 'transaction.created' registrado com sucesso.`);
        } else {
            console.warn(`[Pane ${config.paneId}] DominusEvents não disponível, auto-update desabilitado.`);
        }
    }

    // Inicializar todos os panes quando o DOM estiver pronto
    function initAllPanes() {
        console.log('[TenantDataTablePane] Buscando panes para inicializar...');
        
        // Suportar tanto tenant-datatable-pane quanto elementos com data-table-id/data-filter-id
        const oldPanes = document.querySelectorAll('.tenant-datatable-pane');
        console.log(`[TenantDataTablePane] Encontrados ${oldPanes.length} elementos com classe .tenant-datatable-pane`);
        
        // Buscar elementos com data-table-id ou data-filter-id (pode estar no div pai ou no segmented-shell)
        const elementsWithTableId = document.querySelectorAll('[data-table-id]');
        const elementsWithFilterId = document.querySelectorAll('[data-filter-id]');
        console.log(`[TenantDataTablePane] Encontrados ${elementsWithTableId.length} elementos com data-table-id`);
        console.log(`[TenantDataTablePane] Encontrados ${elementsWithFilterId.length} elementos com data-filter-id`);
        
        // Combinar todos os seletores
        const allElements = [...oldPanes, ...elementsWithTableId, ...elementsWithFilterId];
        
        // Remover duplicatas e filtrar apenas elementos que têm os atributos necessários
        const uniquePanes = Array.from(new Set(allElements)).filter(function(el) {
            // Deve ter data-table-id ou data-filter-id, e data-stats-url e data-data-url
            const hasRequiredAttrs = (el.dataset.tableId || el.dataset.filterId) && 
                                     el.dataset.statsUrl && 
                                     el.dataset.dataUrl;
            
            return hasRequiredAttrs;
        });
        
        console.log(`[TenantDataTablePane] Total de ${uniquePanes.length} panes válidos para inicializar`);
        
        uniquePanes.forEach(function(paneEl, index) {
            try {
                console.log(`[TenantDataTablePane] Inicializando pane ${index + 1}/${uniquePanes.length}:`, paneEl.id || paneEl.dataset.tableId);
                initPane(paneEl);
            } catch (error) {
                console.error(`[TenantDataTablePane] Erro ao inicializar pane ${index + 1}:`, paneEl, error);
            }
        });
    }

    // Função para aguardar dependências e inicializar
    function waitForDependenciesAndInit() {
        const maxAttempts = 50; // 5 segundos máximo (50 * 100ms)
        let attempts = 0;
        
        function checkAndInit() {
            attempts++;
            
            const hasJQuery = typeof $ !== 'undefined' || typeof jQuery !== 'undefined';
            const hasMoment = typeof moment !== 'undefined';
            const hasDataTables = hasJQuery && (typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined');
            
            if (hasJQuery && hasMoment && hasDataTables) {
                console.log('[TenantDataTablePane] Todas as dependências carregadas. Inicializando panes...');
                initAllPanes();
            } else if (attempts < maxAttempts) {
                setTimeout(checkAndInit, 100);
            } else {
                console.error('[TenantDataTablePane] Timeout aguardando dependências!', {
                    jQuery: hasJQuery,
                    moment: hasMoment,
                    dataTables: hasDataTables
                });
                // Tentar inicializar mesmo assim
                initAllPanes();
            }
        }
        
        checkAndInit();
    }

    // Aguardar DOM e dependências
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            waitForDependenciesAndInit();
        });
    } else {
        // DOM já está pronto
        waitForDependenciesAndInit();
    }

    // Exportar para uso global se necessário (opcional)
    window.TenantDataTablePane = {
        initPane: initPane,
        initAllPanes: initAllPanes
    };

})();

