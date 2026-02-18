/**
 * TenantFiltersRegistry - Gerenciador global de filtros para datatables
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

    // Verifica se moment está disponível
    if (typeof moment === 'undefined') {
        return;
    }

    // Estado local
    let currentStart = moment().startOf('month');
    let currentEnd = moment().endOf('month');
    let pickerInitialized = false;

    // Elementos
    const labelDisplay = document.getElementById(`period-display-${tableId}`);
    const inputPicker = document.getElementById(`kt_daterangepicker_${tableId}`);
    const prevBtn = document.getElementById(`prev-period-btn-${tableId}`);
    const nextBtn = document.getElementById(`next-period-btn-${tableId}`);
    const periodSelector = document.getElementById(`period-selector-${tableId}`);
    
    // --- Lógica de Período ---
    const updateDisplay = () => {
        if (!labelDisplay) return;
        
        if (currentStart.format('YYYY-MM') === currentEnd.format('YYYY-MM') &&
            currentStart.date() === 1 &&
            currentEnd.isSame(currentEnd.clone().endOf('month'), 'day')) {
            labelDisplay.textContent = currentStart.format('MMMM [de] YYYY');
        } else {
            labelDisplay.textContent = `${currentStart.format('DD/MM/YYYY')} - ${currentEnd.format('DD/MM/YYYY')}`;
        }
    };

    const triggerChange = () => {
        document.dispatchEvent(new CustomEvent('periodChanged', {
            detail: { start: currentStart.clone(), end: currentEnd.clone(), tableId: tableId }
        }));
    };

    // Navegação Mês Anterior/Próximo
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Calcula a duração do período atual para manter
            const duracao = currentEnd.diff(currentStart, 'days');
            
            // Move para o mês anterior mantendo a duração
            currentStart = currentStart.clone().subtract(1, 'month').startOf('month');
            currentEnd = currentStart.clone().add(duracao, 'days');
            
            // Se a duração era de um mês completo, mantém o mês completo
            if (duracao >= 27 && duracao <= 31) {
                currentEnd = currentStart.clone().endOf('month');
            }
            
            updateDisplay();
            triggerChange();
            if(pickerInitialized) updatePickerDates();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            // Calcula a duração do período atual para manter
            const duracao = currentEnd.diff(currentStart, 'days');
            
            // Move para o próximo mês mantendo a duração
            currentStart = currentStart.clone().add(1, 'month').startOf('month');
            currentEnd = currentStart.clone().add(duracao, 'days');
            
            // Se a duração era de um mês completo, mantém o mês completo
            if (duracao >= 27 && duracao <= 31) {
                currentEnd = currentStart.clone().endOf('month');
            }
            
            updateDisplay();
            triggerChange();
            if(pickerInitialized) updatePickerDates();
        });
    }

    // --- Lazy Loading do Daterangepicker ---
    const initPicker = () => {
        if (pickerInitialized) return;
        if (typeof $ === 'undefined' || !$.fn.daterangepicker) {
            return;
        }
        
        $(inputPicker).daterangepicker({
            startDate: currentStart,
            endDate: currentEnd,
            autoApply: false,
            opens: 'left',
            drops: 'auto',
            ranges: {
                "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
                "Hoje": [moment(), moment()]
            },
            locale: {
                format: "DD/MM/YYYY",
                applyLabel: "Aplicar",
                cancelLabel: "Cancelar",
                customRangeLabel: "Personalizado",
                daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
                monthNames: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                firstDay: 0
            }
        }, (start, end) => {
            currentStart = start;
            currentEnd = end;
            updateDisplay();
            triggerChange();
        });

        pickerInitialized = true;
        // Abre imediatamente após inicializar
        $(inputPicker).data('daterangepicker').show();
    };
    
    const updatePickerDates = () => {
        const drp = $(inputPicker).data('daterangepicker');
        if (drp) {
            drp.setStartDate(currentStart);
            drp.setEndDate(currentEnd);
        }
    }

    // Listener de clique no seletor para Lazy Load
    if (periodSelector) {
        periodSelector.addEventListener('click', (e) => {
            e.preventDefault();
            if (!pickerInitialized) {
                initPicker();
            } else {
                $(inputPicker).data('daterangepicker').toggle();
            }
        });
    }

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

            // Volta para o mês atual
            currentStart = moment().startOf('month');
            currentEnd = moment().endOf('month');
            updateDisplay();
            if(pickerInitialized) updatePickerDates();
            
            triggerSearch();
            triggerChange();
        });
    }

    // Inicializa o display
    updateDisplay();

    // Registra que já inicializou
    window.TenantFiltersRegistry[tableId] = true;
};
