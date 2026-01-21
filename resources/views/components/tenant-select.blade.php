@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => 'Selecione',
    'required' => false,
    'value' => null,
    'class' => 'col-md-3',
    'options' => [],
    'hideSearch' => false,
    'dropdownParent' => null,
    'control' => 'select2',
    'allowClear' => false,
    'minimumResultsForSearch' => null,
    'labelSize' => 'fs-5',
    'tooltip' => null,
    'tooltipTitle' => null,
    'size' => 'md', // sm, md, lg
    'hidePlaceholder' => false, // Nova prop para esconder placeholder
])

<div class="{{ $class }} fv-row">
    @if($label)
        <x-tenant-label
            :for="$id ?? $name"
            :required="$required"
            :size="$labelSize"
            :tooltip="$tooltip ?? $tooltipTitle">
            {{ $label }}
        </x-tenant-label>
    @endif
    <div class="input-group">
        <select
            class="form-select form-select-{{ $size }}"
            data-control="{{ $control }}"
            @if($dropdownParent) data-dropdown-parent="{{ $dropdownParent }}" @endif
            data-placeholder="{{ $placeholder }}"
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            @if($hideSearch) data-hide-search="true" @endif
            @if($allowClear) data-allow-clear="true" @endif
            @if($minimumResultsForSearch !== null) data-minimum-results-for-search="{{ $minimumResultsForSearch }}" @endif
            @if($hidePlaceholder) data-hide-placeholder="true" @endif
            {!! $attributes->merge([]) !!}>
            @if(!$hidePlaceholder)
                @if(!$allowClear)
                    <option value="" disabled selected>{{ $placeholder }}</option>
                @else
                    <option value=""></option>
                @endif
            @endif
            {{ $slot }}
        </select>
    </div>
    <!-- Exibindo a mensagem de erro -->
    @error($name)
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>



