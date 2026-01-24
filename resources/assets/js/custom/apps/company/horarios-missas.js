"use strict";

// Class definition
var KTHorariosMissas = function () {
    // Private variables
    var timePickerInstances = [];

    // Private functions

    // Init form repeater principal (Dias) --- more info: https://github.com/DubFriend/jquery.repeater
    const initFormRepeaterDias = () => {
        console.log('Inicializando repeater de dias...');

        const repeaterElement = $('#kt_horarios_missas_dias_repeater');
        console.log('Elemento repeater dias encontrado:', repeaterElement.length);

        if (repeaterElement.length === 0) {
            console.error('Elemento #kt_horarios_missas_dias_repeater não encontrado!');
            return;
        }

        repeaterElement.repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            repeaters: [{
                selector: '.horarios-repeater',
                initEmpty: false,
                show: function () {
                    console.log('Repeater horário show chamado');
                    $(this).slideDown();

                    // Gerar ID único para o novo input-group de horário
                    const newInputGroup = $(this).find('.input-group[data-td-target-input="nearest"]');
                    if (newInputGroup.length > 0) {
                        const uniqueId = 'kt_td_picker_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        newInputGroup.attr('id', uniqueId);
                        newInputGroup.find('input').attr('data-td-target', '#' + uniqueId);
                        newInputGroup.find('.input-group-text').attr('data-td-target', '#' + uniqueId);
                        console.log('ID único gerado para horário:', uniqueId);
                    }

                    // Init tempusDominus no novo input
                    setTimeout(() => {
                        initTempusDominusTimePickers();
                    }, 100);
                },
                hide: function (deleteElement) {
                    console.log('Repeater horário hide chamado');
                    $(this).slideUp(deleteElement);
                }
            }],

            show: function () {
                // Verificar limite de 7 dias (backup caso o clique não seja interceptado)
                // Contar todos os itens de dias (incluindo o que está sendo adicionado)
                const diasList = $('#kt_horarios_missas_dias_repeater [data-repeater-list="dias"]');
                const totalItems = diasList.find('> [data-repeater-item]').length;

                console.log('Total de dias (incluindo o atual):', totalItems);

                // Se já temos 7 ou mais itens, bloquear (não deveria acontecer devido à interceptação do clique)
                if (totalItems > 7) {
                    Swal.fire({
                        text: 'Você já adicionou todos os 7 dias da semana. Não é possível adicionar mais dias.',
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'OK, entendi!',
                        customClass: {
                            confirmButton: 'btn fw-bold btn-primary'
                        }
                    });
                    // Remover o item extra que foi adicionado
                    $(this).remove();
                    return false;
                }

                console.log('Repeater dia show chamado');
                $(this).slideDown();

                // Init select2 on new repeated items
                initConditionsSelect2();

                // Atualizar opções após adicionar novo item
                setTimeout(() => {
                    updateSelectOptions();
                    updateAddDayButtonState(); // Atualizar estado do botão após adicionar
                }, 150);

                // O repeater de horários já está configurado no repeaters do repeater principal
                // Não precisa de inicialização adicional
            },

            hide: function (deleteElement) {
                console.log('Repeater dia hide chamado');
                const deletedSelect = $(this).find('[data-kt-horarios-missas="dia_semana"]');

                $(this).slideUp(deleteElement);

                // Atualizar opções após remover um dia
                setTimeout(() => {
                    updateSelectOptions();
                    updateAddDayButtonState(); // Atualizar estado do botão após remover
                }, 300);
            }
        });

        console.log('Repeater dias inicializado com sucesso');
    }

    // Obter todos os dias selecionados (exceto o select atual)
    const getSelectedDays = (excludeSelect = null) => {
        const selectedDays = [];
        const allSelects = document.querySelectorAll('[data-kt-horarios-missas="dia_semana"]');

        allSelects.forEach(select => {
            if (select !== excludeSelect) {
                const selectedValue = $(select).val();
                if (selectedValue && selectedValue !== '') {
                    selectedDays.push(selectedValue);
                }
            }
        });

        return selectedDays;
    }

    // Atualizar opções dos selects baseado nos dias já selecionados
    const updateSelectOptions = () => {
        const allSelects = document.querySelectorAll('[data-kt-horarios-missas="dia_semana"]');
        const selectedDays = getSelectedDays();

        allSelects.forEach(select => {
            const $select = $(select);
            const currentValue = $select.val();
            const allDays = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
            const isSelect2Initialized = $select.hasClass("select2-hidden-accessible");

            // Remover todas as opções exceto a vazia
            $select.find('option[value!=""]').remove();

            // Adicionar apenas os dias que não estão selecionados (ou o dia atual deste select)
            allDays.forEach(dia => {
                if (!selectedDays.includes(dia) || dia === currentValue) {
                    const optionText = {
                        'domingo': 'Domingo',
                        'segunda': 'Segunda',
                        'terca': 'Terça',
                        'quarta': 'Quarta',
                        'quinta': 'Quinta',
                        'sexta': 'Sexta',
                        'sabado': 'Sábado'
                    }[dia];

                    $select.append(new Option(optionText, dia, false, dia === currentValue));
                }
            });

            // Atualizar Select2 se já estiver inicializado
            if (isSelect2Initialized) {
                $select.trigger('change.select2');
            }
        });
    }

    // Init condition select2
    const initConditionsSelect2 = () => {
        console.log('Inicializando Select2...');

        // Init new repeating condition types
        const allConditionTypes = document.querySelectorAll('[data-kt-horarios-missas="dia_semana"]');
        console.log('Selects encontrados:', allConditionTypes.length);

        allConditionTypes.forEach(type => {
            if ($(type).hasClass("select2-hidden-accessible")) {
                console.log('Select já inicializado, pulando...');
                return;
            } else {
                $(type).select2({
                    minimumResultsForSearch: -1,
                    placeholder: "Selecione o dia"
                });

                // Adicionar event listener para mudanças
                $(type).on('change', function() {
                    updateSelectOptions();
                });

                console.log('Select2 inicializado');
            }
        });

        // Atualizar opções inicialmente
        updateSelectOptions();
    }

    // Atualizar estado do botão "Adicionar Dia" baseado no número de dias
    const updateAddDayButtonState = () => {
        // Contar apenas os itens de dias (não os itens de horários aninhados)
        const diasList = $('#kt_horarios_missas_dias_repeater [data-repeater-list="dias"]');
        const currentItems = diasList.find('> [data-repeater-item]').length;
        const addButton = $('.btn-add-dia');

        console.log('Atualizando estado do botão. Dias atuais:', currentItems);

        if (currentItems >= 7) {
            addButton.prop('disabled', true);
            addButton.addClass('disabled');
            addButton.attr('data-bs-toggle', 'tooltip');
            addButton.attr('title', 'Limite de 7 dias atingido (todos os dias da semana)');
        } else {
            addButton.prop('disabled', false);
            addButton.removeClass('disabled');
            addButton.attr('title', 'Adicionar novo dia');
        }

        // Reinicializar tooltip se necessário
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipInstance = bootstrap.Tooltip.getInstance(addButton[0]);
            if (tooltipInstance) {
                tooltipInstance.dispose();
            }
            new bootstrap.Tooltip(addButton[0]);
        }
    }

    // Init tempusDominus time pickers
    const initTempusDominusTimePickers = () => {
        console.log('Inicializando tempusDominus...');

        const allTimeInputs = document.querySelectorAll('.input-group[data-td-target-input="nearest"]');
        console.log('Input groups encontrados:', allTimeInputs.length);

        allTimeInputs.forEach(inputGroup => {
            // Verificar se já foi inicializado
            if (inputGroup.getAttribute('data-td-initialized') === '1') {
                console.log('Input group já inicializado, pulando...');
                return;
            }

            const pickerId = inputGroup.getAttribute('id');
            console.log('Picker ID:', pickerId);

            // Se não tem ID, pular (deve ter ID no Blade)
            if (!pickerId) {
                console.warn('Input group sem ID encontrado, pulando inicialização');
                return;
            }

            try {
                // Verificar se tempusDominus está disponível
                if (typeof tempusDominus === 'undefined') {
                    console.warn('tempusDominus não está disponível');
                    return;
                }

                const picker = new tempusDominus.TempusDominus(inputGroup, {
                    display: {
                        viewMode: "clock",
                        components: {
                            decades: false,
                            year: false,
                            month: false,
                            date: false,
                            hours: true,
                            minutes: true,
                            seconds: false
                        }
                    },
                    localization: {
                        locale: 'pt-BR',
                        format: 'HH:mm'
                    }
                });

                console.log('TempusDominus inicializado para:', pickerId);

                // Armazenar instância
                timePickerInstances.push({
                    id: pickerId,
                    instance: picker,
                    element: inputGroup
                });

                // Marcar como inicializado
                inputGroup.setAttribute('data-td-initialized', '1');
            } catch (error) {
                console.error('Erro ao inicializar tempusDominus:', error);
            }
        });
    }

    // Handle form submission
    var initFormSubmission = function () {
        const form = document.getElementById('kt_horarios_missas_form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            // Verificar se há dia da semana selecionado sem horário
            const dias = document.querySelectorAll('[data-repeater-item]');
            let hasDiaSemHorario = false;

            dias.forEach(diaItem => {
                const diaSelect = diaItem.querySelector('[data-kt-horarios-missas="dia_semana"]');
                    const horarios = diaItem.querySelectorAll('.horario-input');

                // Se há um dia selecionado, verificar se tem pelo menos um horário válido
                if (diaSelect && diaSelect.value && diaSelect.value !== '') {
                    let hasHorarioValido = false;

                        horarios.forEach(horarioInput => {
                            if (horarioInput.value && horarioInput.value.trim() !== '') {
                                hasHorarioValido = true;
                            }
                        });

                    // Se o dia está selecionado mas não tem horário válido, marcar como erro
                    if (!hasHorarioValido) {
                        hasDiaSemHorario = true;
                    }
                }
            });

            // Só mostrar erro se houver dia selecionado sem horário
            // Permitir excluir todos os horários (nenhum item de dia)
            if (hasDiaSemHorario) {
                e.preventDefault();
                Swal.fire({
                    text: 'Por favor, adicione pelo menos um horário para o(s) dia(s) da semana selecionado(s).',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                });
                return false;
            }

            // Garantir que o campo dias[] seja enviado mesmo quando o repeater está vazio
            // O jQuery repeater não envia o array quando está vazio, então precisamos garantir isso
            const repeaterList = form.querySelector('[data-repeater-list="dias"]');
            const repeaterItems = repeaterList ? repeaterList.querySelectorAll('[data-repeater-item]') : [];

            // Se não há itens no repeater, remover o campo hidden vazio e garantir que o array seja enviado
            const diasEmptyIndicator = form.querySelector('#dias_empty_indicator');
            if (repeaterItems.length === 0) {
                // Remover o campo hidden vazio se existir
                if (diasEmptyIndicator) {
                    diasEmptyIndicator.remove();
                }
                // Adicionar um campo hidden para garantir que o array vazio seja enviado
                // Isso garante que o controller saiba que todos os horários foram removidos
                const emptyDiasInput = document.createElement('input');
                emptyDiasInput.type = 'hidden';
                emptyDiasInput.name = 'dias';
                emptyDiasInput.value = '';
                form.appendChild(emptyDiasInput);
            } else {
                // Se há itens, remover o indicador vazio
                if (diasEmptyIndicator) {
                    diasEmptyIndicator.remove();
                }
            }

            // Converter intervalo de H:i para minutos antes de enviar
            const intervaloInput = document.getElementById('intervalo_padrao');
            if (intervaloInput && intervaloInput.value) {
                const intervaloTime = intervaloInput.value;
                const match = intervaloTime.match(/(\d{1,2}):(\d{2})/);
                if (match) {
                    const horas = parseInt(match[1]) || 0;
                    const minutos = parseInt(match[2]) || 0;
                    const intervaloMinutos = (horas * 60) + minutos;

                    // Criar campo hidden com o valor em minutos
                    let hiddenInput = form.querySelector('input[name="intervalo_minutos"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'intervalo_minutos';
                        form.appendChild(hiddenInput);
                    }
                    hiddenInput.value = intervaloMinutos;
                }
            }
        });
    };

    // Public methods
    return {
        init: function () {
            console.log('KTHorariosMissas.init() chamado');

            // Verificar dependências
            console.log('jQuery disponível:', typeof $ !== 'undefined');
            console.log('jQuery.repeater disponível:', typeof $.fn.repeater !== 'undefined');
            console.log('tempusDominus disponível:', typeof tempusDominus !== 'undefined');

            // Interceptar clique no botão "Adicionar Dia" para verificar limite antes de adicionar
            const addDayButton = $('.btn-add-dia');
            if (addDayButton.length > 0) {
                addDayButton.on('click', function(e) {
                    const diasList = $('#kt_horarios_missas_dias_repeater [data-repeater-list="dias"]');
                    const currentItems = diasList.find('> [data-repeater-item]').length;

                    console.log('Tentando adicionar dia. Total atual:', currentItems);

                    if (currentItems >= 7) {
                        e.preventDefault();
                        e.stopPropagation();
                        Swal.fire({
                            text: 'Você já adicionou todos os 7 dias da semana. Não é possível adicionar mais dias.',
                            icon: 'warning',
                            buttonsStyling: false,
                            confirmButtonText: 'OK, entendi!',
                            customClass: {
                                confirmButton: 'btn fw-bold btn-primary'
                            }
                        });
                        return false;
                    }
                });
            }

            // Init forms
            initFormRepeaterDias();
            initConditionsSelect2();
            initTempusDominusTimePickers();
            initFormSubmission();

            // Atualizar estado inicial do botão
            setTimeout(() => {
                updateAddDayButtonState();
            }, 200);
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    console.log('DOM carregado, inicializando KTHorariosMissas...');
    KTHorariosMissas.init();
});
