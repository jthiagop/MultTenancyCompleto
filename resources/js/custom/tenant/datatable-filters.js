/**
 * TenantFiltersRegistry - Gerenciador global de filtros para datatables
 * Delega a lógica de período ao initTenantPeriod (period-selector.js)
 */
window.TenantFiltersRegistry = {};

/**
 * Inicializa os filtros para uma tabela específica
 * @param {string} tableId 
 */
window.initTenantFilters = function(tableId) {
    if (window.TenantFiltersRegistry[tableId]) {
        return; // Evita re-inicializar
    }

    const wrapper = document.getElementById(`filters-wrapper-${tableId}`);
    if (!wrapper) {
        return;
    }

    // Inicializa o seletor de período via módulo reutilizável
    const periodApi = typeof window.initTenantPeriod === 'function'
        ? window.initTenantPeriod(tableId)
        : null;

    // --- Badge de filtros ativos + Limpar tudo ---
    const activeGroup = document.getElementById(`active-filters-${tableId}`);
    const activeBadge = document.getElementById(`active-filters-badge-${tableId}`);
    const clearAllBtn = document.getElementById(`clear-all-filters-${tableId}`);

    function countActiveFilters() {
        let count = 0;

        // Busca com texto
        const searchEl = document.getElementById(`search-${tableId}`);
        if (searchEl && searchEl.value.trim() !== '') count++;

        // Selects (multi ou single)
        wrapper.querySelectorAll('select').forEach(sel => {
            if (sel.multiple) {
                const vals = Array.from(sel.selectedOptions).filter(o => o.value !== '');
                if (vals.length > 0) count++;
            } else if (sel.value && sel.value !== '') {
                count++;
            }
        });

        // Período diferente do mês atual
        if (periodApi && !periodApi.isAllPeriod()) {
            const start = periodApi.getStart();
            const end = periodApi.getEnd();
            if (start && end) {
                const now = moment();
                const isDefault = start.isSame(now.clone().startOf('month'), 'day')
                               && end.isSame(now.clone().endOf('month'), 'day');
                if (!isDefault) count++;
            }
        }
        if (periodApi && periodApi.isAllPeriod()) count++;

        return count;
    }

    function updateActiveFiltersUI() {
        const count = countActiveFilters();
        if (activeGroup && activeBadge) {
            if (count > 0) {
                activeBadge.textContent = count + (count === 1 ? ' filtro ativo' : ' filtros ativos');
                activeGroup.classList.remove('d-none');
                activeGroup.classList.add('d-flex');
            } else {
                activeGroup.classList.add('d-none');
                activeGroup.classList.remove('d-flex');
            }
        }
    }

    // Atualiza badge em qualquer mudança
    document.addEventListener('searchTriggered', (e) => {
        if (e.detail && e.detail.tableId === tableId) {
            setTimeout(updateActiveFiltersUI, 50);
        }
    });
    document.addEventListener('periodChanged', (e) => {
        if (!e.detail || e.detail.tableId === tableId || e.detail.selectorId === tableId) {
            setTimeout(updateActiveFiltersUI, 50);
        }
    });
    document.addEventListener('selectApplied', () => {
        setTimeout(updateActiveFiltersUI, 50);
    });

    // --- Busca e Outros Filtros ---
    const searchInput = document.getElementById(`search-${tableId}`);
    const searchButton = document.getElementById(`search-button-search-${tableId}`);

    const triggerSearch = () => {
        document.dispatchEvent(new CustomEvent('searchTriggered', {
            detail: {
                searchValue: searchInput ? searchInput.value : '',
                tableId: tableId
            }
        }));
    };

    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                triggerSearch();
            }
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', triggerSearch);
        searchButton.style.cursor = 'pointer';
    }

    // Listener delegado para filtros (Vanilla JS, sem jQuery)
    wrapper.addEventListener('change', (e) => {
        if (e.target && e.target.matches('select')) {
            triggerSearch();
        }
    });

    // --- Limpar Filtros ---
    const btnClearPath = `clear-filters-btn-${tableId}`;
    const btnClear = document.getElementById(btnClearPath);
    if (btnClear) {
        btnClear.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Reseta inputs de texto
            wrapper.querySelectorAll('input[type="text"]').forEach(input => {
                if (input.id !== `kt_daterangepicker_${tableId}`) {
                    input.value = "";
                }
            });

            // Reseta selects (incluindo Select2)
            wrapper.querySelectorAll('select').forEach(sel => {
                // Multi-select: desmarcar todas as opções
                if (sel.multiple) {
                    Array.from(sel.options).forEach(opt => opt.selected = false);
                } else {
                    sel.value = "";
                }
                // Atualizar Select2 se inicializado, senão evento nativo
                if (sel.classList.contains('select2-hidden-accessible') && typeof $ !== 'undefined') {
                    $(sel).val(null).trigger('change.select2');
                } else {
                    sel.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            // Reseta o período via API do módulo reutilizável
            if (periodApi) {
                periodApi.reset();
            }
            
            triggerSearch();
        });
    }

    // --- Limpar tudo (botão global) ---
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', (e) => {
            e.preventDefault();

            // Reseta busca
            if (searchInput) searchInput.value = '';

            // Reseta selects
            wrapper.querySelectorAll('select').forEach(sel => {
                if (sel.multiple) {
                    Array.from(sel.options).forEach(opt => opt.selected = false);
                } else {
                    sel.value = '';
                }
                if (sel.classList.contains('select2-hidden-accessible') && typeof $ !== 'undefined') {
                    $(sel).val(null).trigger('change');
                } else {
                    sel.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            // Reseta período
            if (periodApi) periodApi.reset();

            triggerSearch();
            updateActiveFiltersUI();
        });
    }

    // Estado inicial
    setTimeout(updateActiveFiltersUI, 200);

    // Registra que já inicializou
    window.TenantFiltersRegistry[tableId] = true;
};
