/**
 * DataTable Adapter - Interface Base
 * 
 * Define o contrato que todos os adapters de módulo devem implementar.
 * Cada módulo (financeiro, extrato, secretary) terá seu próprio adapter
 * que estende esta classe base.
 */

(function() {
    'use strict';

    /**
     * Classe base para adapters de DataTable
     * @class DataTableAdapter
     */
    class DataTableAdapter {
        /**
         * @param {string} key - Identificador do módulo (ex: 'financeiro', 'extrato', 'secretary')
         */
        constructor(key) {
            this.key = key;
        }

        /**
         * Retorna as chaves das tabs de estatísticas do módulo
         * @param {Object} config - Configuração do pane
         * @returns {string[]} Array de chaves de tabs
         */
        getTabKeys(config) {
            return ['total'];
        }

        /**
         * Constrói os parâmetros para a requisição de estatísticas
         * @param {Object} config - Configuração do pane
         * @param {Object} state - Estado atual do pane
         * @param {HTMLElement} paneEl - Elemento do pane
         * @returns {URLSearchParams} Parâmetros para a URL
         */
        buildStatsParams(config, state, paneEl) {
            return new URLSearchParams({ tipo: config.tipo });
        }

        /**
         * Constrói os parâmetros adicionais para a requisição AJAX do DataTable
         * @param {Object} d - Objeto de dados do DataTable
         * @param {Object} config - Configuração do pane
         * @param {Object} state - Estado atual do pane
         * @param {HTMLElement} paneEl - Elemento do pane
         * @param {string} status - Status atual da tab
         * @returns {Object} Objeto d modificado
         */
        buildAjaxData(d, config, state, paneEl, status) {
            return d;
        }

        /**
         * Indica se o módulo usa filtros de data
         * @returns {boolean}
         */
        usesDateFilter() {
            return true;
        }

        /**
         * Indica se o módulo usa filtro de conta/entidade
         * @returns {boolean}
         */
        usesAccountFilter() {
            return true;
        }

        /**
         * Hook executado após cada draw da tabela
         * @param {Object} config - Configuração do pane
         * @param {Object} state - Estado atual do pane
         * @param {HTMLElement} paneEl - Elemento do pane
         */
        onTableDraw(config, state, paneEl) {
            // Override nos adapters específicos
        }

        /**
         * Hook executado quando uma tab é alterada
         * @param {Object} config - Configuração do pane
         * @param {Object} state - Estado atual do pane
         * @param {HTMLElement} paneEl - Elemento do pane
         * @param {Event} event - Evento de mudança de tab
         */
        onTabChanged(config, state, paneEl, event) {
            // Override nos adapters específicos
        }

        /**
         * Retorna listeners de eventos específicos do módulo
         * @param {Object} config - Configuração do pane
         * @param {Object} state - Estado atual do pane
         * @param {HTMLElement} paneEl - Elemento do pane
         * @param {Object} coreFunctions - Funções do core (updateStats, reloadTable, etc)
         * @returns {Array} Array de {selector, event, handler}
         */
        getCustomEventListeners(config, state, paneEl, coreFunctions) {
            return [];
        }

        /**
         * Inicialização customizada do módulo
         * @param {Object} config - Configuração do pane
         * @param {Object} state - Estado atual do pane
         * @param {HTMLElement} paneEl - Elemento do pane
         * @param {Object} coreFunctions - Funções do core
         */
        onInit(config, state, paneEl, coreFunctions) {
            // Override nos adapters específicos
        }
    }

    // Exportar para uso global
    window.DataTableAdapter = DataTableAdapter;

})();
