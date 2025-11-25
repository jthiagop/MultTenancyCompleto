<div class="d-flex align-items-center">
    @php
        $href = $getHref();
        $displayName = $getDisplayName();
        $iconClass = $getIconClass();
    @endphp
    @if($href && $href !== '#')
        <a href="{{ $href }}"
           target="_blank"
           class="text-gray-800 text-hover-primary">
            <i class="bi {{ $iconClass }} fs-2 text-primary me-4"></i>
            {{ $displayName }}
        </a>
    @else
        <span class="text-gray-800">
            <i class="bi {{ $iconClass }} fs-2 text-primary me-4"></i>
            {{ $displayName }}
        </span>
    @endif
</div>
