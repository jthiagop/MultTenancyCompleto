<script>
/**
 * Event Listeners para atualização de componentes financeiros
 *
 * Escuta eventos do DominusEvents e atualiza:
 * - DataTable de transações
 * - Tabs de resumo
 * - Gráfico de fluxo
 */
document.addEventListener('DOMContentLoaded', function() {
    // Função para aguardar DominusEvents estar disponível
    function initBancoListeners() {
        // Aguarda EventBus estar disponível
        if (!window.DominusEvents) {
            // Tenta novamente após 100ms
            setTimeout(initBancoListeners, 100);
            return;
        }


    /**
     * Listener principal: transaction.created
     * Atualiza todos os componentes quando uma transação é criada
     */
    DominusEvents.on('transaction.created', async (data) => {
        // 1. Atualiza DataTable
        reloadAllDataTables();

        // 2. Atualiza summary tabs via AJAX
        await refreshSummaryTabs();

        // 3. Atualiza gráfico de fluxo
        refreshFluxoChart();
    });

    /**
     * Listener: transaction.updated
     */
    DominusEvents.on('transaction.updated', async (data) => {
        reloadAllDataTables();
        await refreshSummaryTabs();
        refreshFluxoChart();
    });

    /**
     * Listener: transaction.deleted
     */
    DominusEvents.on('transaction.deleted', async (data) => {
        reloadAllDataTables();
        await refreshSummaryTabs();
        refreshFluxoChart();
    });

    /**
     * Recarrega todas as DataTables visíveis
     */
    function reloadAllDataTables() {

        // Método global se existir
        if (typeof window.reloadDataTable === 'function') {
            window.reloadDataTable();
            return;
        }

        // Lista de IDs conhecidos de tabelas na página de banco
        const tableIds = [
            'kt_contas_receber_table',
            'kt_contas_pagar_table',
            'kt_extrato_table',
            'kt_conciliacao_table'
        ];

        // Verificar se jQuery e DataTables estão disponíveis
        if (typeof $ === 'undefined' || typeof jQuery === 'undefined') {
            return;
        }

        // Tenta recarregar cada tabela
        if ($.fn && $.fn.DataTable) {
            tableIds.forEach(tableId => {
                const tableEl = document.getElementById(tableId);
                if (tableEl && $.fn.DataTable.isDataTable('#' + tableId)) {
                    try {
                        $('#' + tableId).DataTable().ajax.reload(null, false);
                    } catch (e) {
                        // erro silencioso
                    }
                }
            });

            // Fallback: tenta todas as tabelas visíveis
            try {
                const visibleTables = $.fn.DataTable.tables({ visible: true, api: true });
                if (visibleTables.length > 0) {
                    visibleTables.ajax.reload(null, false);
                }
            } catch (e) {
                // erro silencioso
            }
        }
    }

    /**
     * Atualiza tabs de resumo via AJAX
     */
    async function refreshSummaryTabs() {
        try {
            // Detecta qual tab está ativa
            const activeTab = getActiveTab();

            // Busca dados atualizados do servidor
            const summaryUrl = '{{ route("banco.summary") }}';
            const response = await fetch(summaryUrl + '?' + new URLSearchParams({
                start_date: getStartDate(),
                end_date: getEndDate(),
                tab: activeTab
            }));

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            // Atualiza valores nas tabs
            if (data.tabs && Array.isArray(data.tabs)) {
                data.tabs.forEach(tab => {
                    // Tenta múltiplos seletores para encontrar o elemento (incluindo segmented-tabs-toolbar)
                    const selectors = [
                        `[data-tab-key="${tab.key}"] .segmented-tab-count`,
                        `[data-tab-key="${tab.key}"] .fs-2`,
                        `[data-status-tab="${tab.key}"] .segmented-tab-count`,
                        `[data-status-tab="${tab.key}"] .fs-2`,
                        `a[data-tab-key="${tab.key}"] .fs-2.fw-bold`
                    ];

                    let tabEl = null;
                    for (const selector of selectors) {
                        tabEl = document.querySelector(selector);
                        if (tabEl) break;
                    }

                    if (tabEl) {
                        // Animação de fade
                        tabEl.style.transition = 'opacity 0.15s ease-in-out';
                        tabEl.style.opacity = 0;
                        setTimeout(() => {
                            tabEl.textContent = tab.value;
                            tabEl.style.opacity = 1;
                        }, 150);
                    }
                });
            }

            // Atualiza side-card se existir
            if (data.sideCard) {
                updateSideCard(data.sideCard);
            }

        } catch (error) {
            // erro silencioso
        }
    }

    /**
     * Detecta qual tab está ativa
     */
    function getActiveTab() {
        // Tenta encontrar a tab ativa via Bootstrap
        const activeTabEl = document.querySelector('.nav-link.active[data-bs-toggle="tab"]');
        if (activeTabEl) {
            const href = activeTabEl.getAttribute('href');
            if (href) {
                // Extrai o nome da tab do href (ex: #kt_tab_extrato -> extrato)
                const match = href.match(/#kt_tab_(\w+)/);
                if (match) {
                    return match[1];
                }
            }
        }

        // Fallback: verifica URL
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('tab') || 'contas_receber';
    }

    /**
     * Atualiza gráfico de fluxo de caixa
     */
    function refreshFluxoChart() {
        // Busca instância do ApexChart se existir
        const chartElement = document.getElementById('kt_card_widget_12_chart');
        if (!chartElement) return;

        // Se tiver função global de refresh
        if (typeof window.refreshFluxoBancoChart === 'function') {
            window.refreshFluxoBancoChart();
        }
    }

    /**
     * Atualiza side-card com saldos
     */
    function updateSideCard(data) {
        // Total Receitas
        const receitasEl = document.querySelector('[data-summary="total_receitas"]');
        if (receitasEl && data.total_receitas) {
            receitasEl.textContent = data.total_receitas;
        }

        // Total Despesas
        const despesasEl = document.querySelector('[data-summary="total_despesas"]');
        if (despesasEl && data.total_despesas) {
            despesasEl.textContent = data.total_despesas;
        }

        // Saldo
        const saldoEl = document.querySelector('[data-summary="saldo"]');
        if (saldoEl && data.saldo) {
            saldoEl.textContent = data.saldo;
        }
    }

    /**
     * Obtém data inicial do filtro (do datepicker ou padrão)
     */
    function getStartDate() {
        const input = document.querySelector('#kt_daterangepicker_period');
        if (input && input.value) {
            // Parse do formato "01/01/2026 - 31/01/2026"
            const parts = input.value.split(' - ');
            if (parts[0]) {
                const dateParts = parts[0].split('/');
                return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
            }
        }
        // Padrão: primeiro dia do mês atual
        const now = new Date();
        return `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-01`;
    }

    /**
     * Obtém data final do filtro
     */
    function getEndDate() {
        const input = document.querySelector('#kt_daterangepicker_period');
        if (input && input.value) {
            const parts = input.value.split(' - ');
            if (parts[1]) {
                const dateParts = parts[1].split('/');
                return `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}`;
            }
        }
        // Padrão: último dia do mês atual
        const now = new Date();
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        return `${lastDay.getFullYear()}-${String(lastDay.getMonth() + 1).padStart(2, '0')}-${String(lastDay.getDate()).padStart(2, '0')}`;
    }


    }

    // Inicia a função
    initBancoListeners();
});
</script>
