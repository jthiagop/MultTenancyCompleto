@props([
    'id' => 'daterange_' . uniqid(),
    'defaultRange' => 'month', // 'today', 'week', 'month', 'year', '12months'
    'size' => 'sm',
    'variant' => 'light',
    'icon' => 'bi-calendar3',
    'opens' => 'left',
])

@php
    $rangeKey = $defaultRange ?? 'month';
    $defaultLabels = [
        'today'    => 'Hoje',
        'week'     => \Carbon\Carbon::now()->startOfWeek()->translatedFormat('d M') . ' - ' . \Carbon\Carbon::now()->endOfWeek()->translatedFormat('d M'),
        'month'    => \Carbon\Carbon::now()->translatedFormat('F \d\e Y'),
        'year'     => \Carbon\Carbon::now()->format('Y'),
        '12months' => 'Últimos 12 meses',
    ];
    $label = $defaultLabels[$rangeKey] ?? $defaultLabels['month'];
@endphp

<div class="d-inline-flex align-items-center position-relative" id="wrapper_{{ $id }}">
    <button type="button"
            class="btn btn-{{ $size }} btn-{{ $variant }} d-flex align-items-center gap-2"
            id="btn_{{ $id }}">
        <i class="bi {{ $icon }} fs-7"></i>
        <span id="label_{{ $id }}">{{ $label }}</span>
        <i class="bi bi-chevron-down fs-9 text-gray-500"></i>
    </button>
    <input type="text"
           class="position-absolute opacity-0 top-0 start-0 w-100 h-100"
           id="input_{{ $id }}"
           readonly />
</div>

@push('scripts')
@once
<script>
/**
 * TenantDaterangeButton — inicializador global para botões de daterange
 */
window.initTenantDaterange = function(id, opts) {
    opts = opts || {};
    const btn = document.getElementById('btn_' + id);
    const input = document.getElementById('input_' + id);
    const label = document.getElementById('label_' + id);

    if (!btn || !input || !label) return;
    if (typeof moment === 'undefined' || typeof $ === 'undefined' || !$.fn.daterangepicker) {
        console.warn('[TenantDaterange] moment ou daterangepicker não disponível');
        return;
    }

    const defaultRange = opts.defaultRange || 'month';
    let startDate, endDate;

    switch (defaultRange) {
        case 'today':
            startDate = moment();
            endDate = moment();
            break;
        case 'week':
            startDate = moment().startOf('isoWeek');
            endDate = moment().endOf('isoWeek');
            break;
        case 'year':
            startDate = moment().startOf('year');
            endDate = moment().endOf('year');
            break;
        case '12months':
            startDate = moment().subtract(11, 'months').startOf('month');
            endDate = moment().endOf('month');
            break;
        case 'month':
        default:
            startDate = moment().startOf('month');
            endDate = moment().endOf('month');
            break;
    }

    const updateLabel = (s, e) => {
        // Mês completo
        if (s.date() === 1 && e.isSame(e.clone().endOf('month'), 'day') && s.month() === e.month() && s.year() === e.year()) {
            label.textContent = s.format('MMMM [de] YYYY');
        }
        // Ano completo
        else if (s.month() === 0 && s.date() === 1 && e.month() === 11 && e.date() === 31 && s.year() === e.year()) {
            label.textContent = s.format('YYYY');
        }
        // Hoje
        else if (s.isSame(e, 'day') && s.isSame(moment(), 'day')) {
            label.textContent = 'Hoje';
        }
        // Range genérico
        else {
            label.textContent = s.format('DD/MM/YYYY') + ' - ' + e.format('DD/MM/YYYY');
        }
    };

    $(input).daterangepicker({
        startDate: startDate,
        endDate: endDate,
        autoApply: false,
        opens: opts.opens || 'left',
        drops: 'auto',
        ranges: {
            'Hoje': [moment(), moment()],
            'Esta Semana': [moment().startOf('isoWeek'), moment().endOf('isoWeek')],
            'Este Mês': [moment().startOf('month'), moment().endOf('month')],
            'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            'Últimos 12 Meses': [moment().subtract(11, 'months').startOf('month'), moment().endOf('month')],
            'Este Ano': [moment().startOf('year'), moment().endOf('year')],
        },
        locale: {
            format: 'DD/MM/YYYY',
            applyLabel: 'Aplicar',
            cancelLabel: 'Cancelar',
            customRangeLabel: 'Personalizado',
            daysOfWeek: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
            monthNames: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                          'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
            firstDay: 0,
        }
    }, function(s, e) {
        updateLabel(s, e);
        document.dispatchEvent(new CustomEvent('daterangeChanged', {
            detail: { id: id, start: s.clone(), end: e.clone() }
        }));
    });

    // Abrir o picker ao clicar no botão
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        $(input).data('daterangepicker').toggle();
    });

    updateLabel(startDate, endDate);
};
</script>
@endonce

<script>
document.addEventListener('DOMContentLoaded', function() {
    window.initTenantDaterange('{{ $id }}', {
        defaultRange: '{{ $defaultRange }}',
        opens: '{{ $opens }}'
    });
});
</script>
@endpush
