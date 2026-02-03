/**
 * Financeiro Adapter
 * 
 * Adapter para módulos de Contas a Receber e Contas a Pagar.
 * Este é o adapter padrão (default) usado quando nenhum outro é especificado.
 * 
 * Características:
 * - Usa filtros de data (período)
 * - Usa filtro de conta/entidade
 * - Tabs: vencidos, hoje, a_vencer, recebidos/pagos, total
 */

(function() {
    'use strict';

    class FinanceiroAdapter extends DataTableAdapter {
        constructor() {
            super('financeiro');
        }

        /**
         * Retorna as chaves das tabs baseado no tipo (entrada/saida)
         */
        getTabKeys(config) {
            const tabKeys = ['vencidos', 'hoje', 'a_vencer'];
            // Para entrada = recebidos, para saída = pagos
            const receivedKey = config.tipo === 'entrada' ? 'recebidos' : 'pagos';
            tabKeys.push(receivedKey, 'total');
            return tabKeys;
        }

        /**
         * Constrói parâmetros para stats incluindo período e conta
         */
        buildStatsParams(config, state, paneEl) {
            const params = new URLSearchParams({
                tipo: config.tipo
            });

            // Adicionar filtros de data
            params.append('start_date', state.currentStart.format('YYYY-MM-DD'));
            params.append('end_date', state.currentEnd.format('YYYY-MM-DD'));

            // Adicionar filtro de conta se houver seleção
            this._appendAccountFilter(params, config, paneEl);

            return params;
        }

        /**
         * Constrói dados AJAX incluindo todos os filtros
         */
        buildAjaxData(d, config, state, paneEl, status) {
            d.tipo = config.tipo;
            d.status = status;
            d.start_date = state.currentStart.format('YYYY-MM-DD');
            d.end_date = state.currentEnd.format('YYYY-MM-DD');

            // Filtro de busca
            const searchInput = paneEl.querySelector(`#search-${config.filterId}`);
            if (searchInput && searchInput.value) {
                d.search = { value: searchInput.value };
            }

            // Filtro de conta/entidade
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

            // Filtro de situação
            const situacaoSelect = paneEl.querySelector(`#situacao-filter-${config.filterId}`);
            if (situacaoSelect && situacaoSelect.value) {
                d.situacao = situacaoSelect.value;
            }

            return d;
        }

        /**
         * Usa filtros de data
         */
        usesDateFilter() {
            return true;
        }

        /**
         * Usa filtro de conta
         */
        usesAccountFilter() {
            return true;
        }

        /**
         * Helper para adicionar filtro de conta aos params
         * @private
         */
        _appendAccountFilter(params, config, paneEl) {
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
        }
    }

    // Registrar o adapter no registry global
    window.DataTableAdapters = window.DataTableAdapters || {};
    window.DataTableAdapters.financeiro = new FinanceiroAdapter();
    window.DataTableAdapters.default = window.DataTableAdapters.financeiro;
    
    // Alias para contas_receber e contas_pagar
    window.DataTableAdapters.contas_receber = window.DataTableAdapters.financeiro;
    window.DataTableAdapters.contas_pagar = window.DataTableAdapters.financeiro;

})();
