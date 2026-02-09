/**
 * Secretary Adapter
 * 
 * Adapter para o módulo de Secretaria (gestão de membros religiosos).
 * 
 * Características:
 * - NÃO usa filtros de data
 * - NÃO usa filtro de conta/entidade
 * - Tabs: todos, presbiteros, diaconos, irmaos, votos_simples
 * - Filtros via data-filter-json nas tab-panes
 * - Event listeners específicos para segmented-tabs-toolbar
 */

(function() {
    'use strict';

    class SecretaryAdapter extends DataTableAdapter {
        constructor() {
            super('secretary');
        }

        /**
         * Retorna as chaves das tabs da secretaria
         */
        getTabKeys(config) {
            return ['todos', 'presbiteros', 'diaconos', 'irmaos', 'votos_simples'];
        }

        /**
         * Constrói parâmetros para stats - sem datas, sem conta
         */
        buildStatsParams(config, state, paneEl) {
            const params = new URLSearchParams({
                tipo: config.tipo
            });

            // Secretaria não precisa de filtros de data nem conta
            // Os filtros são baseados nas tabs (role, profession)

            return params;
        }

        /**
         * Constrói dados AJAX baseado na tab ativa (via filterJson)
         */
        buildAjaxData(d, config, state, paneEl, status) {
            // Obter filtro da tab ativa
            const activePane = paneEl.querySelector('.tab-pane.active');
            if (activePane && activePane.dataset.filterJson) {
                try {
                    const filter = JSON.parse(activePane.dataset.filterJson);
                    if (filter) {
                        d.filter = JSON.stringify(filter);
                    }
                } catch (e) {
                    console.warn('[SecretaryAdapter] Erro ao parsear filterJson:', e);
                }
            }

            return d;
        }

        /**
         * Secretaria NÃO usa filtros de data
         */
        usesDateFilter() {
            return false;
        }

        /**
         * Secretaria NÃO usa filtro de conta
         */
        usesAccountFilter() {
            return false;
        }

        /**
         * Hook para quando tab do segmented-tabs-toolbar muda
         */
        onTabChanged(config, state, paneEl, event) {
            console.log(`[SecretaryAdapter] Tab mudou para: ${event?.target?.id || 'unknown'}`);
        }

        /**
         * Retorna listeners específicos para secretaria
         * Configura o reload ao trocar tabs do segmented-tabs-toolbar
         */
        getCustomEventListeners(config, state, paneEl, coreFunctions) {
            const listeners = [];

            // Listener para tabs do segmented-tabs-toolbar
            const segmentedTabs = paneEl.querySelectorAll('[data-bs-toggle="tab"]');
            segmentedTabs.forEach(tab => {
                listeners.push({
                    element: tab,
                    event: 'shown.bs.tab',
                    handler: (e) => {
                        console.log(`[SecretaryAdapter] Tab da secretaria mudou`);
                        
                        // Chamar hook
                        this.onTabChanged(config, state, paneEl, e);
                        
                        // Recarregar DataTable ao trocar de tab
                        if (state.dataTable) {
                            state.dataTable.ajax.reload();
                        }
                        
                        // Atualizar estatísticas
                        coreFunctions.updateStats();
                    }
                });
            });

            return listeners;
        }

        /**
         * Inicialização customizada da secretaria
         */
        onInit(config, state, paneEl, coreFunctions) {
            console.log(`[SecretaryAdapter] Inicializando adapter para secretaria`);
            
            // Registrar listeners customizados
            const listeners = this.getCustomEventListeners(config, state, paneEl, coreFunctions);
            listeners.forEach(({ element, event, handler }) => {
                element.addEventListener(event, handler);
            });
        }
    }

    // Registrar o adapter no registry global
    window.DataTableAdapters = window.DataTableAdapters || {};
    window.DataTableAdapters.secretary = new SecretaryAdapter();

})();
