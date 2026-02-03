/**
 * Extrato Adapter
 * 
 * Adapter para o módulo de Extrato Bancário.
 * 
 * Características:
 * - Usa filtros de data (período)
 * - Usa filtro de conta/entidade
 * - Tabs: receitas_aberto, receitas_realizadas, despesas_aberto, despesas_realizadas, total
 * - Coluna "Saldo" só aparece na tab "total"
 * - Adiciona flags is_extrato e tab aos requests
 */

(function() {
    'use strict';

    class ExtratoAdapter extends DataTableAdapter {
        constructor() {
            super('extrato');
        }

        /**
         * Retorna as chaves das tabs do extrato
         */
        getTabKeys(config) {
            return ['receitas_aberto', 'receitas_realizadas', 'despesas_aberto', 'despesas_realizadas', 'total'];
        }

        /**
         * Constrói parâmetros para stats incluindo flags de extrato
         */
        buildStatsParams(config, state, paneEl) {
            const params = new URLSearchParams({
                tipo: config.tipo
            });

            // Adicionar filtros de data
            params.append('start_date', state.currentStart.format('YYYY-MM-DD'));
            params.append('end_date', state.currentEnd.format('YYYY-MM-DD'));

            // Flags específicas do extrato
            params.append('tab', 'extrato');
            params.append('is_extrato', 'true');

            // Adicionar filtro de conta se houver seleção
            this._appendAccountFilter(params, config, paneEl);

            return params;
        }

        /**
         * Constrói dados AJAX incluindo flags de extrato
         */
        buildAjaxData(d, config, state, paneEl, status) {
            d.tipo = config.tipo;
            d.status = status;
            d.start_date = state.currentStart.format('YYYY-MM-DD');
            d.end_date = state.currentEnd.format('YYYY-MM-DD');

            // Flags específicas do extrato
            d.is_extrato = 'true';
            d.tab = 'extrato';

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
         * Hook executado após cada draw - controla visibilidade da coluna Saldo
         */
        onTableDraw(config, state, paneEl) {
            this.toggleSaldoColumn(config, state, paneEl);
        }

        /**
         * Mostra/oculta a coluna "Saldo" baseado na tab ativa
         * A coluna Saldo só deve aparecer na tab "Total do Período"
         */
        toggleSaldoColumn(config, state, paneEl) {
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
            const shouldShow = state.currentStatus === 'total';

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
    window.DataTableAdapters.extrato = new ExtratoAdapter();

})();
