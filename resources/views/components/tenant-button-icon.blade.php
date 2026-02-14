@props([
    'icon' => null,
    'iconImg' => null, // Caminho para imagem SVG/PNG como ícone
    'iconSize' => '18px', // Tamanho da imagem do ícone
    'text' => '',
    'class' => '',
    'type' => 'button',
    'dataBsToggle' => null,
    'dataBsTarget' => null,
    'onclick' => null,
    'href' => null,
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, light, dark
    'size' => 'sm', // sm, md, lg
    'outline' => false,
])

@php
    // Classes base do botão
    $baseClasses = "btn btn-{$size} btn-light-{$variant} me-2";

    // Classe de variante
    if ($outline) {
        $variantClass = "btn-outline-{$variant}";
    } else {
        $variantClass = "btn-{$variant}";
    }

    // Classes finais
    $buttonClasses = trim("{$baseClasses} {$variantClass} {$class}");

    // Atributos do botão
    $attributes = $attributes->merge([
        'class' => $buttonClasses,
        'type' => $type,
    ]);

    if ($dataBsToggle) {
        $attributes = $attributes->merge(['data-bs-toggle' => $dataBsToggle]);
    }

    if ($dataBsTarget) {
        $attributes = $attributes->merge(['data-bs-target' => $dataBsTarget]);
    }

    if ($onclick) {
        $attributes = $attributes->merge(['onclick' => $onclick]);
    }

    if ($href) {
        $attributes = $attributes->merge(['href' => $href]);
    }
@endphp

@if($href)
    <a {{ $attributes }}>
        @if($iconImg)
            <img src="{{ $iconImg }}" alt="" class="me-1" style="width: {{ $iconSize }}; height: {{ $iconSize }}; vertical-align: middle;">
        @elseif($icon)
            <i class="{{ $icon }} fs-3"></i>
        @endif
        @if($text)
            {{ $text }}
        @endif
        {{ $slot }}
    </a>
@else
    <button {{ $attributes }}>
        @if($iconImg)
            <img src="{{ $iconImg }}" alt="" class="me-1" style="width: {{ $iconSize }}; height: {{ $iconSize }}; vertical-align: middle;">
        @elseif($icon)
            <i class="{{ $icon }} fs-3"></i>
        @endif
        @if($text)
            {{ $text }}
        @endif
        {{ $slot }}
    </button>
@endif

