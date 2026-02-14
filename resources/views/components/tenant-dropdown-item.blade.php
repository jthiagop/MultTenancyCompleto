@props([
    'icon' => null,
    'iconClass' => '',
    'href' => '#',
    'onclick' => null,
    'ariaLabel' => null,
])

<div class="menu-item px-3">
    <a href="{{ $href }}" 
       class="menu-link px-3"
       @if($onclick) onclick="{{ $onclick }}" @endif
       @if($ariaLabel) aria-label="{{ $ariaLabel }}" @endif>
        @if($icon)
            <span class="me-2"><i class="{{ $icon }} {{ $iconClass }}"></i></span>
        @endif
        {{ $slot }}
    </a>
</div>
