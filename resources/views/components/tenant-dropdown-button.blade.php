@props([
    'icon' => null,
    'iconImg' => null,
    'iconSize' => '18px',
    'text' => '',
    'class' => '',
    'variant' => 'primary',
    'size' => 'sm',
    'outline' => false,
    'placement' => 'bottom-end',
    'menuWidth' => 'w-200px',
    'heading' => null,
    'showChevron' => true,
])

@php
    // Classes base do botão
    $baseClasses = "btn btn-{$size}";

    // Classe de variante
    if ($outline) {
        $variantClass = "btn-outline-{$variant}";
    } else {
        $variantClass = "btn-{$variant}";
    }

    // Classes finais
    $buttonClasses = trim("{$baseClasses} {$variantClass} {$class}");
@endphp

<div>
    <button class="{{ $buttonClasses }}" 
            data-kt-menu-trigger="click"
            data-kt-menu-placement="{{ $placement }}">
        @if($iconImg)
            <img src="{{ $iconImg }}" alt="" class="me-1" style="width: {{ $iconSize }}; height: {{ $iconSize }}; vertical-align: middle;">
        @elseif($icon)
            <i class="{{ $icon }} fs-4"></i>
        @endif
        @if($text)
            {{ $text }}
        @endif
        @if($showChevron)
            <i class="fa-solid fa-chevron-down fs-7 ms-2"></i>
        @endif
    </button>

    <!--begin::Menu Dropdown-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold {{ $menuWidth }} py-3"
         data-kt-menu="true">
        
        @if($heading)
            <!--begin::Heading-->
            <div class="menu-item px-3">
                <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                    {{ $heading }}
                </div>
            </div>
            <!--end::Heading-->
        @endif

        {{ $slot }}
    </div>
    <!--end::Menu Dropdown-->
</div>
