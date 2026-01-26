@props([
    'title' => '',
    'accordionId' => 'kt_accordion_default',
    'headerId' => 'header_1',
    'bodyId' => 'body_1',
    'expanded' => false,
])

<div class="accordion-item">
    <h2 class="accordion-header" id="{{ $headerId }}">
        <button class="accordion-button fs-4 fw-semibold {{ !$expanded ? 'collapsed' : '' }}" type="button"
            data-bs-toggle="collapse" data-bs-target="#{{ $bodyId }}"
            aria-expanded="{{ $expanded ? 'true' : 'false' }}" aria-controls="{{ $bodyId }}">
            {{ $title }}

        </button>
    </h2>
    <div id="{{ $bodyId }}" class="accordion-collapse collapse {{ $expanded ? 'show' : '' }}"
        aria-labelledby="{{ $headerId }}" data-bs-parent="#{{ $accordionId }}">
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>
