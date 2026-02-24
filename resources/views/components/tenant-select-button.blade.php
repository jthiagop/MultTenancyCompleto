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
        style="visibility: hidden; height: 0; overflow: hidden;"
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

{{-- Estilos inline (alguns layouts não carregam @stack('styles')) --}}
<style>
    /* ══════════════════════════════════════════════════
       Tenant Select Button — Multi-select com checkboxes
       ══════════════════════════════════════════════════ */

    /* ── Container de seleção (campo fechado) ── */
    .tenant-sb .select2-selection--multiple {
        min-height: 34px !important;
        border: 1px solid var(--bs-gray-300, #dbdfe9) !important;
        border-radius: 0.475rem;
        cursor: pointer;
        display: flex !important;
        align-items: center;
    }
    .tenant-sb .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        align-items: center;
        flex-wrap: nowrap !important;
        overflow: hidden;
        white-space: nowrap;
        padding: 2px 28px 2px 10px !important;
    }
    /* Esconde as tags/chips nativos do Select2 — usamos nosso texto resumo */
    .tenant-sb .select2-selection__choice,
    .tenant-sb .select2-selection__choice__remove {
        display: none !important;
    }
    .tenant-sb .select2-search--inline {
        width: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
    }
    /* Texto resumo (placeholder / "3 selecionados") */
    .tenant-sb-text {
        color: var(--bs-gray-600, #78829d);
        font-size: 0.925rem;
        list-style: none;
        line-height: 28px;
    }
    /* Seta dropdown */
    .tenant-sb .select2-selection--multiple::after {
        content: '';
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        border: 4px solid transparent;
        border-top-color: var(--bs-gray-500, #99a1b7);
    }

    /* ── Dropdown (janela aberta) ── */
    .tenant-sb-dropdown {
        border: 1px solid var(--bs-gray-300, #dbdfe9) !important;
        border-radius: 0.475rem !important;
        box-shadow: 0 3px 12px rgba(0,0,0,.08) !important;
        overflow: hidden;
    }
    /* Remove check (✓) nativo do Select2 nos itens selecionados */
    .tenant-sb-dropdown .select2-results__option--selected,
    .tenant-sb-dropdown .select2-results__option[aria-selected="true"] {
        background-color: transparent !important;
        color: inherit !important;
    }
    .tenant-sb-dropdown .select2-results__option--selected::before,
    .tenant-sb-dropdown .select2-results__option[aria-selected="true"]::before {
        display: none !important;
    }
    /* Hover nos itens */
    .tenant-sb-dropdown .select2-results__option--highlighted[aria-selected] {
        background-color: var(--bs-light-primary, #f1faff) !important;
        color: inherit !important;
    }
    /* Padding dos itens */
    .tenant-sb-dropdown .select2-results__option {
        padding: 6px 12px !important;
    }
    /* Scrollbar mais refinada */
    .tenant-sb-dropdown .select2-results__options {
        max-height: 260px;
        overflow-y: auto;
    }

    /* ── Checkbox customizado dentro dos itens ── */
    .tenant-sb-check {
        width: 18px;
        height: 18px;
        min-width: 18px;
        border: 2px solid var(--bs-gray-300, #dbdfe9);
        border-radius: 4px;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s ease;
    }
    .tenant-sb-check.checked {
        background-color: var(--bs-primary, #3e97ff);
        border-color: var(--bs-primary, #3e97ff);
    }
    .tenant-sb-check.checked::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        margin-top: -1px;
    }

    /* ── Header e Footer dentro do dropdown ── */
    .tenant-sb-header,
    .tenant-sb-footer {
        background: #fff;
        z-index: 10;
    }
</style>

@push('scripts')
<script>
/**
 * Tenant Select Button — multi-select com checkboxes, "Selecionar todas" e "Aplicar".
 * Usa div customizada como checkbox visual (evita conflitos com form-check do Metronic).
 */
(function() {
    var selectId = '{{ $selectId }}';
    var el = document.getElementById(selectId);
    if (!el) return;

    function init() {
        if (el.getAttribute('data-kt-initialized') === '1') return;
        if (typeof $ === 'undefined' || typeof $.fn.select2 === 'undefined') {
            return setTimeout(init, 100);
        }

        var $select = $(el);

        // Destruir instância anterior (Metronic auto-inits)
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        var extraDropdownCls = $select.attr('data-dropdown-css-class') || '';

        // ── Inicializar Select2 ──
        $select.select2({
            placeholder: '{{ $placeholder }}',
            allowClear: false,
            closeOnSelect: false,
            dropdownParent: $select.closest('.card-body, .modal-body, body'),
            minimumResultsForSearch: 0,
            dropdownCssClass: (extraDropdownCls + ' tenant-sb-dropdown').trim(),
            templateResult: function(data) {
                if (!data.id) return data.text;
                var isSelected = $(data.element).prop('selected');
                var $row = $(
                    '<div class="d-flex align-items-center" style="cursor:pointer; gap:10px;">' +
                        '<div class="tenant-sb-check' + (isSelected ? ' checked' : '') + '"></div>' +
                        '<span>' + data.text + '</span>' +
                    '</div>'
                );
                return $row;
            }
        });

        var $container = $select.next('.select2-container');
        $container.addClass('tenant-sb');

        // ── Helpers ──
        function getSummary() {
            var vals = $select.val() || [];
            var n = vals.length;
            var total = $select.find('option[value!=""]').length;
            if (n === 0) return '{{ $placeholder }}';
            if (n === total && total > 0) return 'Todos selecionados (' + n + ')';
            if (n === 1) {
                var txt = $select.find('option:selected').first().text();
                return txt ? txt.trim() : '1 selecionado';
            }
            return n + ' selecionado(s)';
        }

        function updateSummary() {
            var $ul = $container.find('.select2-selection__rendered');
            var $txt = $ul.find('.tenant-sb-text');
            var text = getSummary();
            if ($txt.length) {
                $txt.text(text);
            } else {
                $ul.prepend('<li class="tenant-sb-text">' + text + '</li>');
            }
        }

        function syncCheckboxes() {
            var selected = ($select.val() || []).map(String);
            var total = $select.find('option[value!=""]').length;

            // Sincroniza checkboxes visuais nos resultados
            var inst = $select.data('select2');
            if (!inst || !inst.$dropdown) return;
            var $dd = inst.$dropdown;

            $dd.find('.select2-results__option[role="option"]').each(function() {
                var d = $(this).data('data');
                if (d && d.id) {
                    var isSelected = selected.indexOf(String(d.id)) > -1;
                    var $cb = $(this).find('.tenant-sb-check');
                    $cb.toggleClass('checked', isSelected);
                }
            });

            // Sincroniza "Selecionar todas"
            var $allCb = $dd.find('.tenant-sb-selectall-check');
            if ($allCb.length) {
                $allCb.toggleClass('checked', total > 0 && selected.length === total);
            }
        }

        // ── Eventos ──
        $select.on('change.tenantSB', function() {
            updateSummary();
            setTimeout(syncCheckboxes, 10);
        });

        $select.on('select2:open.tenantSB', function() {
            setTimeout(function() {
                var inst = $select.data('select2');
                if (!inst || !inst.$dropdown) return;
                var $dd = inst.$dropdown;

                // Injeta header e footer apenas uma vez
                if ($dd.find('.tenant-sb-header').length) {
                    syncCheckboxes();
                    return;
                }

                var selected = ($select.val() || []).map(String);
                var total = $select.find('option[value!=""]').length;
                var allChecked = total > 0 && selected.length === total;

                // ── Header: "Selecionar todas" ──
                var $header = $(
                    '<div class="tenant-sb-header px-3 py-2 border-bottom d-flex align-items-center" style="cursor:pointer; gap:10px;">' +
                        '<div class="tenant-sb-check tenant-sb-selectall-check' + (allChecked ? ' checked' : '') + '"></div>' +
                        '<span class="fw-bold text-gray-700">Selecionar todas</span>' +
                    '</div>'
                );

                // ── Footer: botão "Aplicar" ──
                var $footer = $(
                    '<div class="tenant-sb-footer px-3 py-2 border-top text-center">' +
                        '<button type="button" class="btn btn-sm btn-primary w-100">Aplicar</button>' +
                    '</div>'
                );

                // Insere DENTRO do .select2-dropdown (dentro da janela)
                var $resultsWrapper = $dd.find('.select2-results');
                $resultsWrapper.before($header);
                $resultsWrapper.after($footer);

                // ── Clique em "Selecionar todas" ──
                $header.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var $cb = $(this).find('.tenant-sb-selectall-check');
                    var wasChecked = $cb.hasClass('checked');

                    if (wasChecked) {
                        $select.val(null).trigger('change');
                    } else {
                        var vals = [];
                        $select.find('option[value!=""]').each(function() { vals.push($(this).val()); });
                        $select.val(vals).trigger('change');
                    }
                });

                // ── Clique em "Aplicar" ──
                $footer.on('click', 'button', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $select.select2('close');
                    document.dispatchEvent(new CustomEvent('selectApplied', {
                        detail: { selectId: selectId, selectedValues: $select.val() || [] }
                    }));
                });

                syncCheckboxes();
            }, 20);
        });

        // ── Estado inicial ──
        updateSummary();

        // ── Marca inicializado e desfaz FOUC ──
        el.setAttribute('data-kt-initialized', '1');
        el.style.visibility = '';
        el.style.height = '';
        el.style.overflow = '';
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        setTimeout(init, 100);
    }
})();
</script>
@endpush

