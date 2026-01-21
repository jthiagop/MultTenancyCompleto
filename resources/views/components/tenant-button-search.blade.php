@props([
    'id' => null,
    'placeholder' => 'Pesquisar',
    'label' => null,
    'tableId' => null,
])

@php
    $inputId = $id ?? 'search-' . ($tableId ?? 'default');
    $buttonId = 'search-button-' . $inputId;
@endphp

<!--begin::Busca-->
<div style="min-width: 250px; max-width: 350px;">
    @if($label)
        <label class="form-label mb-1 fw-bold">{{ $label }}</label>
    @endif
    <!--begin::Input group-->
    <div class="input-group w-100">
        <input type="text"
            class="form-control form-control-sm "
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            aria-label="Pesquisar"
            aria-describedby="{{ $buttonId }}" />
        <span class="input-group-text btn btn-light btn-sm btn-icon btn-light-primary" id="{{ $buttonId }}">
            <i class="bi bi-search fs-4"></i>
        </span>
    </div>
    <!--end::Input group-->
</div>
<!--end::Busca-->

