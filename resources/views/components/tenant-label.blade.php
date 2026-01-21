@props([
    'for' => null,
    'required' => false,
    'size' => 'fs-6', // fs-5, fs-6, fs-7
    'class' => '',
    'tooltip' => null,
    'tooltipTitle' => null
])

<label 
    class="{{ $required ? 'required' : '' }} d-flex align-items-center {{ $size }} fw-semibold mb-2 {{ $class }}"
    @if($for) for="{{ $for }}" @endif
    {!! $attributes->merge([]) !!}>
    {{ $slot }}
    @if($tooltip || $tooltipTitle)
        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="{{ $tooltip ?? $tooltipTitle }}"></i>
    @endif
</label>

