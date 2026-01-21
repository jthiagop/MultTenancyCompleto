@props([
    'name',
    'id' => null,
    'label' => null,
    'value' => '1',
    'checked' => false,
    'required' => false,
    'tooltip' => null,
    'tooltipTitle' => null,
    'class' => '',
    'hiddenValue' => '0', // Valor a ser enviado quando desmarcado
    'dynamicTooltipField' => null, // Campo que deve ser usado para atualizar o tooltip dinamicamente (ex: 'vencimento')
    'dynamicTooltipPrefix' => null, // Texto antes da data no tooltip dinâmico
    'dynamicTooltipSuffix' => null, // Texto depois da data no tooltip dinâmico
])

<!-- Input Hidden para garantir o envio do valor quando desmarcado -->
<input type="hidden" name="{{ $name }}" value="{{ $hiddenValue }}" id="{{ ($id ?? $name) }}_hidden">

<div class="form-check form-check-custom form-check-solid {{ $class }}">
    <input 
        class="form-check-input" 
        type="checkbox" 
        name="{{ $name }}" 
        id="{{ $id ?? $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $required ? 'required' : '' }}
        {!! $attributes->merge([]) !!} />
    <label class="form-check-label" for="{{ $id ?? $name }}">
        {{ $label ?? $slot }}
        @if($tooltip || $tooltipTitle)
            <i class="fas fa-exclamation-circle ms-2 fs-7" 
               data-bs-toggle="tooltip" 
               title="{{ $tooltip ?? $tooltipTitle }}"
               @if($dynamicTooltipField) data-dynamic-tooltip-field="{{ $dynamicTooltipField }}" @endif
               @if($dynamicTooltipPrefix) data-dynamic-tooltip-prefix="{{ $dynamicTooltipPrefix }}" @endif
               @if($dynamicTooltipSuffix) data-dynamic-tooltip-suffix="{{ $dynamicTooltipSuffix }}" @endif></i>
        @endif
    </label>
</div>

<script>
    (function() {
        var checkboxId = '{{ $id ?? $name }}';
        var checkboxElement = $('#{{ $id ?? $name }}');
        var tooltipIcon = checkboxElement.closest('.form-check').find('i[data-bs-toggle="tooltip"]');
        var dynamicTooltipField = tooltipIcon.attr('data-dynamic-tooltip-field');
        var dynamicTooltipPrefix = tooltipIcon.attr('data-dynamic-tooltip-prefix') || '';
        var dynamicTooltipSuffix = tooltipIcon.attr('data-dynamic-tooltip-suffix') || '';
        var tooltipBase = tooltipIcon.attr('title') || '';

        // Remove o hidden quando o checkbox é marcado
        $(document).ready(function() {
            checkboxElement.on('change', function() {
                var hiddenInput = $('#{{ ($id ?? $name) }}_hidden');
                if ($(this).is(':checked')) {
                    hiddenInput.remove();
                } else {
                    // Recria o hidden se não existir
                    if (hiddenInput.length === 0) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: '{{ $name }}',
                            value: '{{ $hiddenValue }}',
                            id: '{{ ($id ?? $name) }}_hidden'
                        }).insertBefore($(this).closest('.form-check'));
                    }
                }
            });
        });

        // Função para atualizar o tooltip dinamicamente se houver campo associado
        function atualizarTooltipDinamico() {
            if (!dynamicTooltipField || !tooltipIcon.length) {
                return;
            }

            var campoValor = $('#' + dynamicTooltipField).val() || '';
            var textoAtualizado;

            if (campoValor) {
                // Monta o texto com o valor do campo
                textoAtualizado = dynamicTooltipPrefix + campoValor + dynamicTooltipSuffix;
            } else {
                // Usa o texto base sem o valor
                textoAtualizado = tooltipBase;
            }

            // Atualiza o atributo title
            tooltipIcon.attr('data-bs-original-title', textoAtualizado);
            tooltipIcon.attr('title', textoAtualizado);

            // Reinicializa o tooltip do Bootstrap se já foi inicializado
            if (tooltipIcon.data('bs.tooltip')) {
                tooltipIcon.tooltip('dispose');
            }

            // Reinicializa o tooltip
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                new bootstrap.Tooltip(tooltipIcon[0]);
            } else if (typeof $ !== 'undefined' && $.fn.tooltip) {
                tooltipIcon.tooltip();
            }
        }

        // Se houver campo dinâmico, adiciona listeners
        if (dynamicTooltipField) {
            // Atualiza quando o campo associado muda
            $(document).on('change', '#' + dynamicTooltipField, function() {
                setTimeout(function() {
                    atualizarTooltipDinamico();
                }, 100);
            });

            // Tenta adicionar listener ao flatpickr se disponível
            function adicionarListenerFlatpickr() {
                var campoInput = document.getElementById(dynamicTooltipField);
                if (campoInput && campoInput._flatpickr && !campoInput._flatpickr._tooltipListenerAdded) {
                    campoInput._flatpickr.config.onChange.push(function(selectedDates, dateStr, instance) {
                        setTimeout(function() {
                            atualizarTooltipDinamico();
                        }, 100);
                    });
                    campoInput._flatpickr._tooltipListenerAdded = true;
                }
            }

            // Atualiza quando o documento está pronto
            $(document).ready(function() {
                setTimeout(function() {
                    atualizarTooltipDinamico();
                    adicionarListenerFlatpickr();
                }, 500);
            });

            // Atualiza quando modais são abertos (para elementos dinâmicos)
            $(document).on('shown.bs.modal', function() {
                setTimeout(function() {
                    atualizarTooltipDinamico();
                    adicionarListenerFlatpickr();
                }, 500);
            });
        }
    })();
</script>

