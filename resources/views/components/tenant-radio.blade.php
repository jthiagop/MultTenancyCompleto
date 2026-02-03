@props([
    'name',
    'id' => null,
    'label',
    'value',
    'checked' => false,
    'disabled' => false,
])

@php
    $inputId = $id ?? $name . '_' . \Str::slug($value);
    $isChecked = filter_var($checked, FILTER_VALIDATE_BOOLEAN);
@endphp

<div class="form-check form-check-custom form-check-solid">
    <input 
        class="form-check-input" 
        type="radio" 
        name="{{ $name }}" 
        value="{{ $value }}" 
        id="{{ $inputId }}"
        {{ $isChecked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
    />
    <label class="form-check-label" for="{{ $inputId }}">
        {{ $label }}
    </label>
</div>
