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

    // Registra que já inicializou
    window.TenantFiltersRegistry[tableId] = true;
};
