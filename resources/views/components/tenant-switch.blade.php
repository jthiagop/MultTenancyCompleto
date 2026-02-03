@props([
    'name',
    'id' => null,
    'label' => null,
    'description' => null,
    'checked' => false,
    'value' => '1',
    'class' => '',
    'disabled' => false,
    'labelOn' => 'Sim',
    'labelOff' => 'Não',
    'showLabel' => true,
])

@php
    $isChecked = old($name, $checked) ? true : false;
@endphp

<div class="d-flex flex-stack {{ $class }}">
    <!--begin::Label-->
    <div class="me-5">
        @if($label)
            <label class="fs-6 fw-semibold" for="{{ $id ?? $name }}">{{ $label }}</label>
        @endif
        @if($description)
            <div class="fs-7 fw-semibold text-muted">{{ $description }}</div>
        @endif
    </div>
    <!--end::Label-->
    
    <!--begin::Switch-->
    <label class="form-check form-switch form-check-custom form-check-solid">
        <input 
            class="form-check-input" 
            type="checkbox" 
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            value="{{ $value }}"
            {{ $isChecked ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {!! $attributes->merge([]) !!} />
        @if($showLabel)
            <span class="form-check-label fw-semibold text-muted" data-on="{{ $labelOn }}" data-off="{{ $labelOff }}">
                {{ $isChecked ? $labelOn : $labelOff }}
            </span>
        @endif
    </label>
    <!--end::Switch-->
</div>

@error($name)
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Atualiza o label do switch quando o checkbox muda
    document.querySelectorAll('.form-switch .form-check-input').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const label = this.closest('.form-switch').querySelector('.form-check-label');
            if (label && label.dataset.on && label.dataset.off) {
                label.textContent = this.checked ? label.dataset.on : label.dataset.off;
            }
        });
    });
});
</script>
@endpush
@endonce
