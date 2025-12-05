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
                console.log('Repeater dia show chamado');
                $(this).slideDown();

                // Init select2 on new repeated items
                initConditionsSelect2();

                // Atualizar opções após adicionar novo item
                setTimeout(() => {
                    updateSelectOptions();
                }, 150);

                // Inicializar repeater de horários aninhado
                setTimeout(() => {
                    $(this).find('.horarios-repeater').each(function() {
                        if (!$(this).data('repeater-initialized')) {
                            initFormRepeaterHorarios($(this));
                        }
                    });
                }, 100);
            },

            hide: function (deleteElement) {
                console.log('Repeater dia hide chamado');
                const deletedSelect = $(this).find('[data-kt-horarios-missas="dia_semana"]');

                $(this).slideUp(deleteElement);

                // Atualizar opções após remover um dia
                setTimeout(() => {
                    updateSelectOptions();
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

    // Public methods
    return {
        init: function () {
            console.log('KTHorariosMissas.init() chamado');

            // Verificar dependências
            console.log('jQuery disponível:', typeof $ !== 'undefined');
            console.log('jQuery.repeater disponível:', typeof $.fn.repeater !== 'undefined');
            console.log('tempusDominus disponível:', typeof tempusDominus !== 'undefined');

            // Init forms
            initFormRepeaterDias();
            initConditionsSelect2();
            initTempusDominusTimePickers();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    console.log('DOM carregado, inicializando KTHorariosMissas...');
    KTHorariosMissas.init();
});
