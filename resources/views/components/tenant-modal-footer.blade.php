@props([
    'cancelText' => 'Cancelar',
    'cancelIcon' => 'fa-solid fa-times',
    'submitText' => 'Salvar',
    'submitIcon' => 'fa-solid fa-check',
    'submitId' => null,
    'submitClass' => 'btn-primary',
    'loadingText' => 'Aguarde...',
    'size' => 'sm',
    'showCancel' => true,
])

@php
    $btnSize = $size ? 'btn-' . $size : '';
@endphp

<!--begin::Modal footer-->
<div class="modal-footer d-flex justify-content-between align-items-center border-top">
    {{-- Lado esquerdo: Cancelar --}}
    <div class="d-flex align-items-center">
        @if($showCancel)
            <button type="button" data-bs-dismiss="modal" class="btn {{ $btnSize }} btn-light">
                @if($cancelIcon)
                    <i class="{{ $cancelIcon }} me-1"></i>
                @endif
                {{ $cancelText }}
            </button>
        @endif
    </div>

    {{-- Lado direito: Ação principal + extras --}}
    <div class="d-flex align-items-center gap-2">
        {{-- Slot para botões extras à esquerda do principal --}}
        {{ $slot }}

        <button type="submit" class="btn {{ $btnSize }} {{ $submitClass }}" @if($submitId) id="{{ $submitId }}" @endif>
            <span class="indicator-label">
                @if($submitIcon)
                    <i class="{{ $submitIcon }} me-2"></i>
                @endif
                {{ $submitText }}
            </span>
            <span class="indicator-progress">
                {{ $loadingText }} <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </div>
</div>
<!--end::Modal footer-->
