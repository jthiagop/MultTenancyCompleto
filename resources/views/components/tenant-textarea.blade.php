@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'required' => false,
    'value' => null,
    'class' => '',
    'rows' => 3,
    'maxlength' => null,
    'disabled' => false,
    'readonly' => false,
])

<div class="{{ $class }} fv-row">
    @if($label)
        <label class="fs-6 fw-semibold mb-2 {{ $required ? 'required' : '' }}" for="{{ $id ?? $name }}">
            {{ $label }}
        </label>
    @endif
    <textarea
        class="form-control"
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        @if($maxlength) maxlength="{{ $maxlength }}" @endif
        {!! $attributes->merge([]) !!}>{{ $value ?? old($name) }}</textarea>
    
    @error($name)
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
