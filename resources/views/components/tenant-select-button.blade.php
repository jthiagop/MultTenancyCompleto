@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => 'Selecione',
    'required' => false,
    'value' => null,
    'class' => '',
    'options' => [],
    'optionsHtml' => '',
    'labelSize' => 'fs-7',
    'multiple' => true,
])

@php
    $selectId = $id ?? $name;
    $selectName = $multiple ? $name . '[]' : $name;
@endphp

<div class="{{ $class }}">
    @if($label)
        <label class="form-label mb-1 fw-bold {{ $labelSize }}" for="{{ $selectId }}">{{ $label }}</label>
    @endif

    <!--begin::Select com botão-->
    <select
        class="form-select form-select-sm"
        name="{{ $selectName }}"
        data-dropdown-css-class="w-400px"
        id="{{ $selectId }}"
        data-placeholder="{{ $placeholder }}"
        @if($multiple) multiple @endif
        {!! $attributes->merge([]) !!}>
        {{-- Opção vazia para placeholder quando múltiplo --}}
        @if($multiple)
            <option value="" disabled style="display:none;">{{ $placeholder }}</option>
        @else
            <option value="">{{ $placeholder }}</option>
        @endif
        @if(!empty($optionsHtml))
            {!! $optionsHtml !!}
        @elseif(!empty($options))
            @foreach($options as $option)
                @if(is_array($option))
                    <option value="{{ $option['value'] ?? $option['id'] ?? '' }}"
                        {{ ($value && (is_array($value) ? in_array($option['value'] ?? $option['id'], $value) : $value == ($option['value'] ?? $option['id']))) ? 'selected' : '' }}>
                        {{ $option['label'] ?? $option['nome'] ?? $option['name'] ?? '' }}
                    </option>
                @else
                    <option value="{{ $option->id }}"
                        {{ ($value && (is_array($value) ? in_array($option->id, $value) : $value == $option->id)) ? 'selected' : '' }}>
                        {{ $option->nome ?? $option->name ?? '' }}
                    </option>
                @endif
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    <!--end::Select com botão-->

    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
(function() {
    var selectId = '{{ $selectId }}';
    var selectElement = document.getElementById(selectId);

    if (!selectElement) {
        return;
    }

    function initSelect2WithCheckboxes() {
        // Verificar se já foi inicializado pelo nosso script
        if (selectElement.getAttribute('data-kt-initialized') === '1') {
            return;
        }

        // Verificar se jQuery e Select2 estão disponíveis
        if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
            setTimeout(initSelect2WithCheckboxes, 100);
            return;
        }

        var $select = $(selectElement);

        // Destruir qualquer instância Select2 existente
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        // Configurar Select2 com template customizado
        var select2Options = {
            placeholder: '{{ $placeholder }}',
            allowClear: true,
            closeOnSelect: false,
            dropdownParent: $select.closest('.card-body, .modal-body, body'),
            minimumResultsForSearch: 0, // Sempre mostrar campo de busca
            // Prevenir seleção automática ao clicar
            matcher: function(params, data) {
                // Se não há termo de busca, mostrar todos
                if ($.trim(params.term) === '') {
                    return data;
                }
                // Caso contrário, usar busca padrão
                if (typeof data.text === 'undefined') {
                    return null;
                }
                if (data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
                    return data;
                }
                return null;
            },
            templateResult: function(data) {
                if (!data.id) {
                    return data.text;
                }
                var $element = $(data.element);
                var isSelected = $element.prop('selected');
                var $wrapper = $('<div class="d-flex align-items-center py-2 px-2 select2-result-item-wrapper" style="cursor: pointer;" data-value="' + data.id + '"></div>');
                var $checkbox = $('<div class="form-check form-check-custom form-check-solid me-2">' +
                                    '<input class="form-check-input select2-result-checkbox" type="checkbox" ' +
                                    'value="' + data.id + '" ' + (isSelected ? 'checked' : '') + '>' +
                                  '</div>');
                var $text = $('<span class="flex-grow-1">' + data.text + '</span>');

                $wrapper.append($checkbox);
                $wrapper.append($text);

                return $wrapper;
            },
            templateSelection: function(data, container) {
                if (!data.id) {
                    return data.text;
                }

                // Se for múltiplo, mostrar contador ou texto
                if ('{{ $multiple }}') {
                    var selectedValues = $select.val();
                    var selectedCount = selectedValues && selectedValues.length ? selectedValues.length : 0;

                    if (selectedCount === 0) {
                        return '{{ $placeholder }}';
                    }

                    // Se todos selecionados (exceto o placeholder/vazio)
                    var totalOptions = $select.find('option[value!=""]').length;
                    if (selectedCount === totalOptions && totalOptions > 0) {
                        return 'Todos selecionados (' + selectedCount + ')';
                    }

                    // Se apenas 1 selecionado, mostra o nome
                    if (selectedCount === 1) {
                         return data.text;
                    }

                    return selectedCount + ' selecionado(s)';
                }
                return data.text;
            }
        };

        // Inicializar Select2
        try {
            if (typeof KTSelect2 !== 'undefined') {
                new KTSelect2(selectElement, select2Options);
            } else {
                $select.select2(select2Options);
            }

            // Função para esconder itens duplicados no display de seleção
            function hideDuplicateSelections() {
                var $container = $select.next('.select2-container');
                var $choices = $container.find('.select2-selection__choice');

                // Se houver mais de 1 choice, esconder todos exceto o primeiro
                if ($choices.length > 1) {
                    $choices.slice(1).hide();
                }
            }

            // Aplicar após cada mudança
            $select.on('change', function() {
                setTimeout(hideDuplicateSelections, 10);
            });

            // Aplicar após abrir/fechar
            $select.on('select2:close', function() {
                setTimeout(hideDuplicateSelections, 10);
            });

            // Prevenir seleção automática ao clicar nos resultados
            $select.on('select2:selecting', function(e) {
                // Se o clique foi em um resultado customizado, prevenir seleção automática
                var $target = $(e.params.args.originalEvent.target);
                if ($target.closest('.select2-result-item-wrapper').length > 0) {
                    e.params.args.originalEvent.preventDefault();
                    e.params.args.originalEvent.stopPropagation();
                }
            });

            // Customizar o dropdown quando aberto
            $select.on('select2:open', function() {
                setTimeout(function() {
                    var $dropdown = $('.select2-container--open .select2-dropdown');
                    if ($dropdown.length === 0) {
                        return;
                    }

                    // Verificar se já foi customizado
                    if ($dropdown.find('.select2-custom-controls').length > 0) {
                        return;
                    }

                    // Encontrar o campo de busca
                    var $search = $dropdown.find('.select2-search');
                    var $results = $dropdown.find('.select2-results');

                    // Inject "Select All" após o campo de busca
                    var $selectAllContainer = $('<div class="select2-custom-controls p-3 border-bottom">' +
                        '<div class="form-check form-check-custom form-check-solid">' +
                            '<input class="form-check-input" type="checkbox" id="select-all-' + selectId + '" />' +
                            '<label class="form-check-label fw-bold" for="select-all-' + selectId + '">Selecionar todas</label>' +
                        '</div>' +
                    '</div>');

                    // Inject "Apply" button no final
                    var $applyContainer = $('<div class="select2-custom-controls p-3 border-top text-center">' +
                        '<button type="button" class="btn btn-primary btn-sm w-100" id="apply-' + selectId + '">Aplicar</button>' +
                    '</div>');

                    // Inserir "Selecionar todas" após o campo de busca
                    if ($search.length > 0) {
                        $search.after($selectAllContainer);
                    } else {
                        $dropdown.prepend($selectAllContainer);
                    }

                    // Inserir botão "Aplicar" após os resultados
                    $dropdown.append($applyContainer);

                    // Atualizar estado do checkbox "Selecionar todas"
                    function updateSelectAllState() {
                        var totalOptions = $select.find('option[value!=""]').length;
                        var selectedOptions = $select.val() ? $select.val().length : 0;
                        var $selectAll = $('#select-all-' + selectId);
                        if ($selectAll.length) {
                            $selectAll.prop('checked', totalOptions > 0 && totalOptions === selectedOptions);
                        }
                    }

                    updateSelectAllState();

                    // Handle "Select All" click
                    $dropdown.on('change', '#select-all-' + selectId, function() {
                        var isChecked = $(this).is(':checked');
                        var allOptionValues = [];
                        $select.find('option[value!=""]').each(function() {
                            allOptionValues.push($(this).val());
                        });

                        if (isChecked) {
                            $select.val(allOptionValues).trigger('change');
                        } else {
                            $select.val(null).trigger('change');
                        }

                        // Atualizar checkboxes visuais nos resultados
                        setTimeout(function() {
                            $dropdown.find('.select2-result-checkbox').each(function() {
                                var checkboxValue = $(this).val();
                                $(this).prop('checked', isChecked && allOptionValues.includes(checkboxValue));
                            });
                        }, 100);
                    });

                    // Handle clique na linha inteira ou no checkbox
                    $dropdown.on('click', '.select2-result-item-wrapper', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Clique na linha - alternar seleção via checkbox
                        var $wrapper = $(this);
                        var checkboxValue = $wrapper.data('value');
                        var $checkbox = $wrapper.find('.select2-result-checkbox');
                        var isChecked = $checkbox.is(':checked');

                        // Alternar checkbox
                        $checkbox.prop('checked', !isChecked);

                        // Atualizar seleção no select
                        var currentValues = $select.val() || [];
                        if (!isChecked) {
                            if (!currentValues.includes(checkboxValue)) {
                                currentValues.push(checkboxValue);
                            }
                        } else {
                            currentValues = currentValues.filter(function(v) { return v !== checkboxValue; });
                        }

                        $select.val(currentValues).trigger('change');
                        updateSelectAllState();
                    });

                    // Handle checkboxes nos resultados
                    $dropdown.on('change', '.select2-result-checkbox', function(e) {
                        e.stopPropagation();
                        var checkboxValue = $(this).val();
                        var isChecked = $(this).is(':checked');
                        var currentValues = $select.val() || [];

                        if (isChecked) {
                            if (!currentValues.includes(checkboxValue)) {
                                currentValues.push(checkboxValue);
                            }
                        } else {
                            currentValues = currentValues.filter(function(v) { return v !== checkboxValue; });
                        }

                        $select.val(currentValues).trigger('change');
                        updateSelectAllState();
                    });

                    // Handle "Apply" click
                    $dropdown.on('click', '#apply-' + selectId, function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $select.select2('close');

                        // Trigger custom applied event
                        var event = new CustomEvent('selectApplied', {
                            detail: {
                                selectId: selectId,
                                selectedValues: $select.val() || []
                            }
                        });
                        document.dispatchEvent(event);
                    });
                }, 50);
            });

            // Atualizar estado quando seleção mudar
            $select.on('change', function() {
                var $selectAll = $('#select-all-' + selectId);
                if ($selectAll.length) {
                    var totalOptions = $select.find('option[value!=""]').length;
                    var selectedOptions = $select.val() ? $select.val().length : 0;
                    $selectAll.prop('checked', totalOptions > 0 && totalOptions === selectedOptions);
                }

                // Atualizar checkboxes visuais nos resultados
                var selectedValues = $select.val() || [];
                setTimeout(function() {
                    $('.select2-container--open .select2-result-checkbox').each(function() {
                        var checkboxValue = $(this).val();
                        $(this).prop('checked', selectedValues.includes(checkboxValue));
                    });
                }, 50);
            });

            // Atualizar checkboxes quando os resultados são renderizados
            $select.on('select2:open', function() {
                setTimeout(function() {
                    var selectedValues = $select.val() || [];
                    $('.select2-container--open .select2-result-checkbox').each(function() {
                        var checkboxValue = $(this).val();
                        var $option = $select.find('option[value="' + checkboxValue + '"]');
                        var isSelected = $option.length > 0 && $option.prop('selected');
                        $(this).prop('checked', isSelected);
                    });
                }, 100);
            });

            // Marcar como inicializado
            selectElement.setAttribute('data-kt-initialized', '1');
        } catch (error) {
            console.error('Erro ao inicializar Select2 com checkboxes para ' + selectId + ':', error);
        }
    }

    // Aguardar o DOM estar pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSelect2WithCheckboxes);
    } else {
        // Dar tempo para outros scripts carregarem
        setTimeout(initSelect2WithCheckboxes, 100);
    }
})();
</script>
@endpush

