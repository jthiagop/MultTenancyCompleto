/**
 * Tenant DataTable Pane Module - Entry Point (Refatorado)
 * 
 * Este arquivo agora funciona como orquestrador que:
 * 1. Carrega o Core (datatable-core.js)
 * 2. Carrega os Adapters (financeiro, extrato, secretary)
 * 3. Detecta os panes no DOM
 * 4. Instancia o Core com o Adapter apropriado para cada pane
 * 
 * A lógica específica de cada módulo foi movida para seus respectivos adapters.
 * O Core contém apenas código genérico reutilizável.
 * 
 * Estrutura:
 * - core/datatable-adapter.js  → Interface base para adapters
 * - core/datatable-core.js     → Motor genérico do DataTable
 * - adapters/financeiro-adapter.js → Contas a Receber/Pagar
 * - adapters/extrato-adapter.js    → Extrato Bancário
 * - adapters/secretary-adapter.js  → Secretaria (membros religiosos)
 */

(function() {
    'use strict';

    /**
     * Obtém o adapter apropriado para o módulo
     * @param {string} key - Chave do módulo (ex: 'secretary', 'extrato', 'contas_receber')
     * @returns {DataTableAdapter} Adapter do módulo ou default
     */
    function getAdapter(key) {
        // Aguardar registro dos adapters
        if (!window.DataTableAdapters) {
            return null;
        }

        const adapter = window.DataTableAdapters[key] || window.DataTableAdapters.default;
        
        if (!adapter) {
            return window.DataTableAdapters.default;
        }

        return adapter;
    }

    /**
     * Inicializa um pane de DataTable
     * @param {HTMLElement} paneEl - Elemento raiz do pane
     */
    function initPane(paneEl) {
        // Verificar atributos necessários
        const hasTableId = paneEl.dataset.tableId || paneEl.dataset.filterId;
        const hasStatsUrl = paneEl.dataset.statsUrl;
        const hasDataUrl = paneEl.dataset.dataUrl;
        
        if (!hasTableId || !hasStatsUrl || !hasDataUrl) {
            return;
        }

        // Verificar se o Core está disponível
        if (!window.DataTableCore) {
            return;
        }

        // Determinar o módulo e obter adapter
        const key = paneEl.dataset.key || 'default';
        const adapter = getAdapter(key);

        if (!adapter) {
            return;
        }

        // Criar instância do Core com o Adapter
        const core = new DataTableCore(paneEl, adapter);
        core.init();

        // Armazenar referência para debug/acesso externo
        paneEl._tenantPaneState = core.state;
    }

    /**
     * Inicializa todos os panes encontrados no DOM
     */
    function initAllPanes() {
        
        // Buscar elementos com os atributos necessários
        const oldPanes = document.querySelectorAll('.tenant-datatable-pane');
        const elementsWithTableId = document.querySelectorAll('[data-table-id]');
        const elementsWithFilterId = document.querySelectorAll('[data-filter-id]');
        
        
        // Combinar e remover duplicatas
        const allElements = [...oldPanes, ...elementsWithTableId, ...elementsWithFilterId];
        
        const uniquePanes = Array.from(new Set(allElements)).filter(el => {
            const hasRequiredAttrs = (el.dataset.tableId || el.dataset.filterId) && 
                                     el.dataset.statsUrl && 
                                     el.dataset.dataUrl;
            
            return hasRequiredAttrs;
        });
        
        
        uniquePanes.forEach((paneEl, index) => {
            try {
                initPane(paneEl);
            } catch (error) {
                console.error(`[TenantDataTablePane] Erro ao inicializar pane ${index + 1}:`, paneEl, error);
            }
        });
    }

    /**
     * Aguarda dependências e inicializa
     */
    function waitForDependenciesAndInit() {
        const maxAttempts = 50; // 5 segundos máximo
        let attempts = 0;
        
        function checkAndInit() {
            attempts++;
            
            const hasJQuery = typeof $ !== 'undefined' || typeof jQuery !== 'undefined';
            const hasMoment = typeof moment !== 'undefined';
            const hasDataTables = hasJQuery && (typeof $.fn !== 'undefined' && typeof $.fn.DataTable !== 'undefined');
            const hasCore = typeof window.DataTableCore !== 'undefined';
            const hasAdapters = typeof window.DataTableAdapters !== 'undefined' && 
                               window.DataTableAdapters.default !== undefined;
            

            
            if (hasJQuery && hasMoment && hasDataTables && hasCore && hasAdapters) {
                initAllPanes();
            } else if (attempts < maxAttempts) {
                setTimeout(checkAndInit, 100);
            } else {
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
        waitForDependenciesAndInit();
    }

    // Exportar para uso global
    window.TenantDataTablePane = {
        initPane: initPane,
        initAllPanes: initAllPanes,
        getAdapter: getAdapter
    };

})();
