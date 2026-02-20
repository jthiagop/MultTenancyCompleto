/**
 * TenantPeriodRegistry - Gerenciador global de seletores de período reutilizáveis
 * 
 * Emite o evento 'periodChanged' com { start, end, tableId } ao alterar o período.
 * Pode ser usado standalone ou dentro do tenant-datatable-filters.
 */
window.TenantPeriodRegistry = {};

/**
 * Inicializa um seletor de período para um ID específico
 * @param {string} id - Identificador do seletor (mesmo valor passado ao componente Blade)
 * @returns {object|null} API pública do seletor ({ getStart, getEnd, reset, updatePickerDates })
 */
window.initTenantPeriod = function(id) {
    if (window.TenantPeriodRegistry[id]) {
        return window.TenantPeriodRegistry[id];
    }

    if (typeof moment === 'undefined') {
        return null;
    }

    // Estado local
    let currentStart = moment().startOf('month');
    let currentEnd = moment().endOf('month');
    let pickerInitialized = false;
    let isAllPeriod = false;

    // Elementos
    const labelDisplay = document.getElementById(`period-display-${id}`);
    const inputPicker = document.getElementById(`kt_daterangepicker_${id}`);
    const prevBtn = document.getElementById(`prev-period-btn-${id}`);
    const nextBtn = document.getElementById(`next-period-btn-${id}`);
    const periodSelector = document.getElementById(`period-selector-${id}`);
    const allPeriodBtn = document.getElementById(`period-all-btn-${id}`);

    // Se não encontrou o elemento principal, não inicializa
    if (!periodSelector && !labelDisplay) {
        return null;
    }

    // --- Lógica de Período ---
    const updateDisplay = () => {
        if (!labelDisplay) return;

        if (isAllPeriod) {
            labelDisplay.textContent = 'Todo o período';
            return;
        }

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
            detail: {
                start: isAllPeriod ? null : currentStart.clone(),
                end: isAllPeriod ? null : currentEnd.clone(),
                tableId: id,
                isAllPeriod: isAllPeriod
            }
        }));
    };

    const updatePickerDates = () => {
        if (!pickerInitialized || typeof $ === 'undefined') return;
        const drp = $(inputPicker).data('daterangepicker');
        if (drp) {
            drp.setStartDate(currentStart);
            drp.setEndDate(currentEnd);
        }
    };

    const updateAllPeriodUI = () => {
        if (allPeriodBtn) {
            if (isAllPeriod) {
                allPeriodBtn.classList.add('active');
                if (prevBtn) prevBtn.disabled = true;
                if (nextBtn) nextBtn.disabled = true;
            } else {
                allPeriodBtn.classList.remove('active');
                if (prevBtn) prevBtn.disabled = false;
                if (nextBtn) nextBtn.disabled = false;
            }
        }
    };

    // Navegação Mês Anterior
    if (prevBtn) {
        prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (isAllPeriod) return;

            const duracao = currentEnd.diff(currentStart, 'days');
            currentStart = currentStart.clone().subtract(1, 'month').startOf('month');
            currentEnd = (duracao >= 27 && duracao <= 31)
                ? currentStart.clone().endOf('month')
                : currentStart.clone().add(duracao, 'days');

            updateDisplay();
            triggerChange();
            updatePickerDates();
        });
    }

    // Navegação Próximo Mês
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (isAllPeriod) return;

            const duracao = currentEnd.diff(currentStart, 'days');
            currentStart = currentStart.clone().add(1, 'month').startOf('month');
            currentEnd = (duracao >= 27 && duracao <= 31)
                ? currentStart.clone().endOf('month')
                : currentStart.clone().add(duracao, 'days');

            updateDisplay();
            triggerChange();
            updatePickerDates();
        });
    }

    // --- Lazy Loading do Daterangepicker ---
    const initPicker = () => {
        if (pickerInitialized) return;
        if (typeof $ === 'undefined' || !$.fn.daterangepicker || !inputPicker) return;

        $(inputPicker).daterangepicker({
            startDate: currentStart,
            endDate: currentEnd,
            autoApply: false,
            opens: 'left',
            drops: 'auto',
            ranges: {
                'Todo o período': [moment('2000-01-01'), moment()],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Hoje': [moment(), moment()]
            },
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                customRangeLabel: 'Personalizado',
                daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
                monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                             'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
                firstDay: 0
            }
        }, (start, end) => {
            // Detecta se selecionou "Todo o período" (range desde 2000)
            if (start.isSame(moment('2000-01-01'), 'day')) {
                isAllPeriod = true;
            } else {
                isAllPeriod = false;
            }
            currentStart = start;
            currentEnd = end;
            updateDisplay();
            updateAllPeriodUI();
            triggerChange();
        });

        pickerInitialized = true;
        $(inputPicker).data('daterangepicker').show();
    };

    // Botão "Todo o período"
    if (allPeriodBtn) {
        allPeriodBtn.addEventListener('click', (e) => {
            e.preventDefault();
            isAllPeriod = !isAllPeriod;

            if (!isAllPeriod) {
                // Volta para o mês atual
                currentStart = moment().startOf('month');
                currentEnd = moment().endOf('month');
                updatePickerDates();
            }

            updateDisplay();
            updateAllPeriodUI();
            triggerChange();
        });
    }

    // Clique no seletor → Lazy Load do picker
    if (periodSelector) {
        periodSelector.addEventListener('click', (e) => {
            e.preventDefault();
            // Se está no modo "todo período", desativa e volta para mês atual
            if (isAllPeriod) {
                isAllPeriod = false;
                currentStart = moment().startOf('month');
                currentEnd = moment().endOf('month');
                updateDisplay();
                updateAllPeriodUI();
                updatePickerDates();
                triggerChange();
                return;
            }
            if (!pickerInitialized) {
                initPicker();
            } else if (typeof $ !== 'undefined') {
                $(inputPicker).data('daterangepicker').toggle();
            }
        });
    }

    // Inicializa o display
    updateDisplay();
    updateAllPeriodUI();

    // API pública
    const api = {
        getStart: () => isAllPeriod ? null : currentStart.clone(),
        getEnd: () => isAllPeriod ? null : currentEnd.clone(),
        isAllPeriod: () => isAllPeriod,
        updatePickerDates,
        reset: () => {
            isAllPeriod = false;
            currentStart = moment().startOf('month');
            currentEnd = moment().endOf('month');
            updateDisplay();
            updateAllPeriodUI();
            updatePickerDates();
            triggerChange();
        },
        setAllPeriod: () => {
            isAllPeriod = true;
            updateDisplay();
            updateAllPeriodUI();
            triggerChange();
        }
    };

    // Registra
    window.TenantPeriodRegistry[id] = api;
    return api;
};
