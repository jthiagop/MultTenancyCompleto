/**
 * DataTable Core
 * 
 * Motor genérico do DataTable Pane.
 * Contém toda a lógica reutilizável entre módulos:
 * - Inicialização do DataTable
 * - Skeleton/loading states
 * - Seleção em massa (checkboxes)
 * - Ações em lote (batch actions)
 * - Event listeners genéricos
 * - Integração com Metronic (menus, tooltips)
 * - Sistema de eventos (DominusEvents)
 * 
 * Delega para adapters as regras específicas de cada módulo.
 */

(function() {
    'use strict';

    // ============================================
    // CONSTANTES GLOBAIS
    // ============================================

    /**
     * Mensagem de estado vazio (reutilizável)
     */
    const EMPTY_MESSAGE = `
        <div class="d-flex flex-column align-items-center justify-content-center py-10">
            <img src="/assets/media/illustrations/traco-fino/chasing-money.svg" class="mw-300px mb-5" alt="Nenhum resultado encontrado" />
            <div class="fs-3 fw-bold text-gray-900 mb-2">Nenhum resultado encontrado</div>
            <div class="fs-6 text-gray-500">Selecione outros filtros para refazer sua busca</div>
        </div>
    `;

    /**
     * Configuração de idioma do DataTables (PT-BR)
     */
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

    // ============================================
    // CLASSE PRINCIPAL: DataTableCore
    // ============================================

    class DataTableCore {
        /**
         * @param {HTMLElement} paneEl - Elemento raiz do pane
         * @param {DataTableAdapter} adapter - Adapter específico do módulo
         */
        constructor(paneEl, adapter) {
            this.paneEl = paneEl;
            this.adapter = adapter;
            this.config = this._readConfig();
            this.state = this._initState();
            
            // Bind das funções para manter contexto
            this.updateStats = this.updateStats.bind(this);
            this.reloadTable = this.reloadTable.bind(this);
            this.initDataTable = this.initDataTable.bind(this);
        }

        /**
         * Lê configuração dos data-* attributes do elemento
         * @private
         */
        _readConfig() {
            const el = this.paneEl;
            return {
                paneId: el.dataset.paneId || el.id || 'pane-' + Math.random().toString(36).substr(2, 9),
                tableId: el.dataset.tableId || el.dataset.filterId,
                filterId: el.dataset.filterId || el.dataset.tableId,
                key: el.dataset.key || 'default',
                tipo: el.dataset.tipo || 'all',
                statsUrl: el.dataset.statsUrl,
                dataUrl: el.dataset.dataUrl,
                columns: JSON.parse(el.dataset.columnsJson || '[]'),
                defaultOrder: JSON.parse(el.dataset.defaultOrder || '[[1, "desc"]]'),
                pageLength: parseInt(el.dataset.pageLength || '50', 10),
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            };
        }

        /**
         * Inicializa o estado interno do pane
         * @private
         */
        _initState() {
            return {
                dataTable: null,
                currentStart: moment().startOf('month'),
                currentEnd: moment().endOf('month'),
                currentStatus: new URLSearchParams(window.location.search).get('status') || 'total'
            };
        }

        /**
         * Retorna funções do core para uso pelos adapters
         */
        getCoreFunctions() {
            return {
                updateStats: this.updateStats,
                reloadTable: this.reloadTable,
                initDataTable: this.initDataTable,
                showFlasherMessage: this.showFlasherMessage.bind(this),
                updateSelectionCount: this.updateSelectionCount.bind(this),
                getSelectedIds: this.getSelectedIds.bind(this)
            };
        }

        // ============================================
        // ESTATÍSTICAS (STATS)
        // ============================================

        /**
         * Atualiza as estatísticas das tabs
         */
        updateStats() {
            if (!this.config.statsUrl) {
                console.warn(`[Core ${this.config.paneId}] statsUrl não configurado, pulando updateStats`);
                return;
            }
            
            console.log(`[Core ${this.config.paneId}] Atualizando estatísticas...`);

            // Delegar construção dos params ao adapter
            const params = this.adapter.buildStatsParams(this.config, this.state, this.paneEl);

            const statsUrlFull = this.config.statsUrl + '?' + params.toString();
            console.log(`[Core ${this.config.paneId}] Fazendo requisição para: ${statsUrlFull}`);
            
            fetch(statsUrlFull)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`[Core ${this.config.paneId}] Dados recebidos das stats:`, data);
                    
                    // Obter chaves de tabs do adapter
                    const tabKeys = this.adapter.getTabKeys(this.config);

                    // Atualizar os valores nas tabs
                    tabKeys.forEach(key => {
                        const tabElement = this.paneEl.querySelector(`[data-tab-key="${key}"]`);
                        if (tabElement) {
                            let valueElement = tabElement.querySelector('.segmented-tab-count');
                            if (!valueElement) {
                                valueElement = tabElement.querySelector('.fs-2');
                            }
                            
                            if (valueElement) {
                                const value = data[key] || '0';
                                valueElement.textContent = value;
                                console.log(`[Core ${this.config.paneId}] Atualizado tab ${key}: ${value}`);
                                
                                // Aplicar classe text-danger para valores negativos
                                if (value.toString().startsWith('-')) {
                                    valueElement.classList.remove('text-primary', 'text-success', 'text-warning', 'text-info', 'text-secondary');
                                    valueElement.classList.add('text-danger');
                                }
                            } else {
                                console.warn(`[Core ${this.config.paneId}] Elemento de valor não encontrado para tab ${key}`);
                            }
                        } else {
                            console.warn(`[Core ${this.config.paneId}] Tab element não encontrado para key: ${key}`);
                        }
                    });
                })
                .catch(error => {
                    console.error(`[Core ${this.config.paneId}] Erro ao atualizar estatísticas:`, error);
                });
        }

        // ============================================
        // SELEÇÃO (CHECKBOXES)
        // ============================================

        /**
         * Atualiza contador de selecionados e estado do botão de ações em lote
         */
        updateSelectionCount() {
            const selectedCountElement = this.paneEl.querySelector(`#selected-count-${this.config.tableId}`);
            const batchActionsBtn = this.paneEl.querySelector(`#batch-actions-btn-${this.config.tableId}`);

            const checkboxes = this.paneEl.querySelectorAll(`#${this.config.tableId} tbody .form-check-input.row-check[type="checkbox"]`);
            let selectedCount = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) selectedCount++;
            });

            if (selectedCountElement) {
                selectedCountElement.textContent = selectedCount + ' registro(s) selecionado(s)';
            }

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
        getSelectedIds() {
            const checkboxes = this.paneEl.querySelectorAll(`#${this.config.tableId} tbody .form-check-input.row-check[type="checkbox"]:checked`);
            const ids = [];
            checkboxes.forEach(checkbox => {
                const id = checkbox.value;
                if (id && id !== '1') {
                    ids.push(id);
                }
            });
            return ids;
        }

        // ============================================
        // NOTIFICAÇÕES
        // ============================================

        /**
         * Exibe mensagem usando Flasher ou fallback
         */
        showFlasherMessage(type, message) {
            if (typeof notify !== 'undefined' && typeof notify[type] === 'function') {
                notify[type](message);
            } else if (typeof flasher !== 'undefined' && typeof flasher[type] === 'function') {
                flasher[type](message);
            } else {
                console.warn(`[Core ${this.config.paneId}] Flasher não disponível. Mensagem:`, type + ':', message);
                alert(message);
            }
        }

        // ============================================
        // DATATABLE
        // ============================================

        /**
         * Inicializa/recarrega a DataTable
         */
        initDataTable(status = this.state.currentStatus) {
            if (this.state.dataTable) {
                this.state.dataTable.destroy();
            }

            // Hook do adapter antes de inicializar
            this.adapter.onTableDraw(this.config, this.state, this.paneEl);

            // Construir colunas - usar 'key' como 'data' para mapear dados do servidor
            const dtColumns = this.config.columns.map((col, index) => ({
                data: col.key || index, // Usar key se disponível, senão índice
                orderable: col.orderable !== false
            }));

            // Reset visibility states
            const skeleton = this.paneEl.querySelector(`#skeleton-${this.config.tableId}`);
            const tableWrapper = this.paneEl.querySelector(`#table-wrapper-${this.config.tableId}`);
            
            if (skeleton) skeleton.classList.remove('d-none');
            if (tableWrapper) tableWrapper.classList.add('d-none');

            // Verificar dependências
            if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
                console.error(`[Core ${this.config.paneId}] jQuery não está disponível! Aguardando...`);
                setTimeout(() => {
                    if (typeof $ !== 'undefined') {
                        this.initDataTable(status);
                    }
                }, 500);
                return;
            }

            if (!$.fn.DataTable) {
                console.error(`[Core ${this.config.paneId}] jQuery DataTables não está carregado!`);
                return;
            }

            console.log(`[Core ${this.config.paneId}] Inicializando DataTable para tabela: #${this.config.tableId}`);
            
            const tableElement = document.getElementById(this.config.tableId);
            if (!tableElement) {
                console.error(`[Core ${this.config.paneId}] Tabela #${this.config.tableId} não encontrada no DOM!`);
                return;
            }

            const self = this;

            this.state.dataTable = $(`#${this.config.tableId}`).DataTable({
                processing: true,
                serverSide: true,
                info: false,
                ajax: {
                    url: this.config.dataUrl,
                    data: (d) => {
                        // Delegar construção dos dados ao adapter
                        this.adapter.buildAjaxData(d, this.config, this.state, this.paneEl, status);
                    }
                },
                columns: dtColumns,
                order: this.config.defaultOrder,
                pageLength: this.config.pageLength,
                language: DATATABLE_LANGUAGE,
                initComplete: function() {
                    if (skeleton) skeleton.classList.add('d-none');
                    if (tableWrapper) {
                        tableWrapper.classList.remove('d-none');
                        tableWrapper.style.animation = 'fadeIn 0.5s';
                    }
                }
            });

            // Handler de draw
            this.state.dataTable.on('draw', () => {
                // Hook do adapter após draw
                this.adapter.onTableDraw(this.config, this.state, this.paneEl);

                // Inicializar menus do Metronic
                if (typeof KTMenu !== 'undefined') {
                    this.paneEl.querySelectorAll('[data-kt-menu]').forEach(el => {
                        try {
                            KTMenu.createInstances();
                        } catch (e) {
                            console.warn(`[Core ${this.config.paneId}] Erro ao inicializar menu:`, e);
                        }
                    });
                }

                // Inicializar tooltips do Bootstrap
                if (typeof bootstrap !== 'undefined') {
                    this.paneEl.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                        const existingTooltip = bootstrap.Tooltip.getInstance(el);
                        if (existingTooltip) existingTooltip.dispose();
                        try {
                            new bootstrap.Tooltip(el);
                        } catch (e) {
                            console.warn(`[Core ${this.config.paneId}] Erro ao inicializar tooltip:`, e);
                        }
                    });
                }

                // Atualizar contador de seleção
                this.updateSelectionCount();

                // Event listeners para checkboxes
                this.paneEl.querySelectorAll(`#${this.config.tableId} tbody .form-check-input.row-check[type="checkbox"]`)
                    .forEach(checkbox => {
                        checkbox.addEventListener('change', () => this.updateSelectionCount());
                    });

                const selectAllCheckbox = this.paneEl.querySelector(`#${this.config.tableId} thead .form-check-input[type="checkbox"]`);
                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', () => {
                        setTimeout(() => this.updateSelectionCount(), 100);
                    });
                }
            });

            // Inicializar contador
            setTimeout(() => this.updateSelectionCount(), 500);
        }

        /**
         * Recarrega a tabela
         */
        reloadTable() {
            if (this.state.dataTable) {
                this.state.dataTable.ajax.reload();
            }
        }

        // ============================================
        // TABS DE STATUS
        // ============================================

        /**
         * Atualiza estilos das tabs de status
         */
        updateTabStyles(activeStatus) {
            const statusTabs = this.paneEl.querySelectorAll(`[data-status-tab]`);
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

        // ============================================
        // EVENT LISTENERS
        // ============================================

        /**
         * Configura todos os event listeners
         */
        setupEventListeners() {
            // Evento de mudança de período
            document.addEventListener('periodChanged', (event) => {
                const eventTableId = event.detail.tableId;
                const matches = eventTableId === this.config.filterId || 
                              eventTableId === this.config.tableId;
                
                if (matches && this.adapter.usesDateFilter()) {
                    console.log(`[Core ${this.config.paneId}] Período alterado:`, event.detail);
                    this.state.currentStart = event.detail.start;
                    this.state.currentEnd = event.detail.end;
                    this.updateStats();
                    this.reloadTable();
                }
            });

            // Evento de pesquisa
            document.addEventListener('searchTriggered', (event) => {
                const eventTableId = event.detail.tableId;
                const matches = eventTableId === this.config.filterId || 
                              eventTableId === this.config.tableId;
                
                if (matches) {
                    console.log(`[Core ${this.config.paneId}] Busca acionada:`, event.detail);
                    this.reloadTable();
                }
            });

            // Evento de seleção de conta
            document.addEventListener('selectApplied', (event) => {
                const expectedSelectId = `account-filter-${this.config.filterId}`;
                const altSelectId = `account-filter-${this.config.tableId}`;
                const matches = event.detail.selectId === expectedSelectId || 
                              event.detail.selectId === altSelectId;
                
                if (matches && this.adapter.usesAccountFilter()) {
                    this.updateStats();
                    this.reloadTable();
                }
            });

            // Tabs de status
            this.paneEl.querySelectorAll(`[data-status-tab]`).forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();

                    const status = link.dataset.statusTab || 'total';

                    const url = new URL(link.href || window.location.href);
                    url.searchParams.set('status', status);
                    window.history.pushState({ status: status }, '', url.toString());

                    this.updateTabStyles(status);
                    this.state.currentStatus = status;
                    this.initDataTable(status);
                    this.updateStats();
                });
            });

            // Popstate (navegação do browser)
            window.addEventListener('popstate', (event) => {
                const urlParams = new URLSearchParams(window.location.search);
                const status = urlParams.get('status') || 'total';
                this.state.currentStatus = status;

                this.updateTabStyles(status);
                this.initDataTable(status);
                this.updateStats();
            });

            // Batch actions
            this._setupBatchActions();

            // Informar pagamento individual
            this._setupInformarPagamento();

            // Evento global de transação criada
            if (window.DominusEvents) {
                DominusEvents.on('transaction.created', (data) => {
                    console.log(`[Core ${this.config.paneId}] Transação criada/atualizada, recarregando...`, data);
                    this.updateStats();
                    if (this.state.dataTable) {
                        this.state.dataTable.ajax.reload(null, false);
                    }
                });
                console.log(`[Core ${this.config.paneId}] Event listener 'transaction.created' registrado.`);
            }
        }

        /**
         * Configura handlers de ações em lote
         * @private
         */
        _setupBatchActions() {
            this.paneEl.addEventListener('click', (e) => {
                const batchActionBtn = e.target.closest('[data-batch-action]');
                if (!batchActionBtn) return;

                const action = batchActionBtn.dataset.batchAction;
                const tableIdAttr = batchActionBtn.dataset.tableId;

                if (tableIdAttr && tableIdAttr !== this.config.tableId) return;

                const ids = this.getSelectedIds();
                if (ids.length === 0) {
                    this.showFlasherMessage('warning', 'Nenhum registro selecionado.');
                    return;
                }

                let route, successMessage, errorMessage, bodyData = { ids: ids };

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
                        console.warn(`[Core ${this.config.paneId}] Ação desconhecida:`, action);
                        return;
                }

                fetch(route, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.config.csrfToken
                    },
                    body: JSON.stringify(bodyData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showFlasherMessage('success', data.message || successMessage);
                        this.reloadTable();
                        this.updateStats();
                    } else {
                        this.showFlasherMessage('error', data.message || errorMessage);
                    }
                })
                .catch(error => {
                    console.error(`[Core ${this.config.paneId}] Erro:`, error);
                    this.showFlasherMessage('error', 'Erro ao processar a solicitação.');
                });
            });
        }

        /**
         * Configura handler de informar pagamento individual
         * @private
         */
        _setupInformarPagamento() {
            this.paneEl.addEventListener('click', (e) => {
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
                        'X-CSRF-TOKEN': this.config.csrfToken
                    },
                    body: JSON.stringify({
                        id: transacaoId,
                        data_pagamento: new Date().toISOString().split('T')[0]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.showFlasherMessage('success', data.message || 'Transação marcada como paga com sucesso.');
                        this.reloadTable();
                        this.updateStats();
                    } else {
                        this.showFlasherMessage('error', data.message || 'Erro ao marcar transação como paga.');
                    }
                })
                .catch(error => {
                    console.error(`[Core ${this.config.paneId}] Erro:`, error);
                    this.showFlasherMessage('error', 'Erro ao processar a solicitação.');
                });
            });
        }

        // ============================================
        // INICIALIZAÇÃO
        // ============================================

        /**
         * Inicializa o pane
         */
        init() {
            console.log(`[Core ${this.config.paneId}] Inicializando com adapter: ${this.adapter.key}`);

            // Configurar event listeners
            this.setupEventListeners();

            // Hook de inicialização do adapter
            this.adapter.onInit(this.config, this.state, this.paneEl, this.getCoreFunctions());

            // Observer para visibilidade
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (this.paneEl.classList.contains('show') || this.paneEl.classList.contains('active')) {
                            console.log(`[Core ${this.config.paneId}] Pane ficou visível, inicializando...`);
                            this.updateStats();
                            if (!this.state.dataTable) {
                                this.initDataTable();
                            }
                        }
                    }
                });
            });

            observer.observe(this.paneEl, { attributes: true });

            // Inicializar se já estiver ativo
            if (this.paneEl.classList.contains('show') || this.paneEl.classList.contains('active')) {
                console.log(`[Core ${this.config.paneId}] Pane já está ativo, inicializando imediatamente...`);
                setTimeout(() => {
                    this.updateStats();
                    this.initDataTable();
                }, 100);
            } else {
                console.log(`[Core ${this.config.paneId}] Pane não está ativo ainda (classes: ${this.paneEl.className})`);
            }

            // Observer para tabs internas
            this.paneEl.querySelectorAll('[data-bs-toggle="tab"]').forEach((button) => {
                button.addEventListener('shown.bs.tab', (event) => {
                    setTimeout(() => {
                        this.updateStats();
                        if (!this.state.dataTable) {
                            this.initDataTable();
                        } else {
                            this.reloadTable();
                        }
                    }, 100);
                });
            });

            // Armazenar referência para debug
            this.paneEl._tenantPaneCore = this;
        }
    }

    // Exportar para uso global
    window.DataTableCore = DataTableCore;
    window.DATATABLE_LANGUAGE = DATATABLE_LANGUAGE;
    window.DATATABLE_EMPTY_MESSAGE = EMPTY_MESSAGE;

})();
