@props([
    'label',
    'value' => null,
    'valueId' => null,
    'href' => null,
    'badge' => false,
    'badgeVariant' => null,
    'currency' => false,
    'currencyVariant' => null,
    'editable' => false,
    'field' => null,
    'class' => ''
])

@php
    $displayValue = $value;
    if ($value && !$currency && !$badge && strlen($value) > 30) {
        $displayValue = substr($value, 0, 30) . '...';
    }
@endphp

<div class="{{ $class }}" style="flex: 0 0 calc(25% - 1rem); min-width: 200px; max-width: calc(30% - 1rem);">
    <div class="fs-6 fw-bold text-gray-900 mb-2">{{ $label }}</div>
    <div class="fs-6 text-gray-600">
        @if($currency && $value)
            <span class="fs-4 fw-bold @if($currencyVariant === 'entrada') text-success @elseif($currencyVariant === 'saida') text-danger @endif">
                R$ {{ number_format($value, 2, ',', '.') }}
            </span>
        @elseif($badge && $value)
            @if($badgeVariant === 'entrada')
                <span class="badge badge-light-success">{{ $value }}</span>
            @elseif($badgeVariant === 'saida')
                <span class="badge badge-light-danger">{{ $value }}</span>
            @else
                <span class="badge badge-light-secondary">{{ $value }}</span>
            @endif
        @elseif($href && $value)
            <a href="{{ $href }}" class="text-primary text-hover-primary" title="{{ $value }}">{{ $displayValue }}</a>
            @if($editable)
                <i class="bi bi-pencil-fill text-primary ms-1 fs-7"></i>
            @endif
        @elseif(isset($slot) && $slot->isNotEmpty())
            {{ $slot }}
            @if($editable)
                <i class="bi bi-pencil-fill text-primary ms-1 fs-7"></i>
            @endif
        @else
            <span title="{{ $value }}">{{ $displayValue }}</span>
            @if($editable && $field)
                <i class="fa-regular fa-pen-to-square text-primary ms-2 fs-6 cursor-pointer"
                   style="cursor: pointer;"
                   data-bs-toggle="modal"
                   data-bs-target="#modal_edit_field"
                   data-field="{{ $field }}"
                   data-value="{{ $valueId ?? $value }}"
                   title="Editar {{ $label }}"></i>
            @endif
        @endif
    </div>
</div>

