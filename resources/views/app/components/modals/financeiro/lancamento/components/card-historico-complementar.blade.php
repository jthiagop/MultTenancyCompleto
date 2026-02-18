{{-- Card Histórico Complementar - Versão Simplificada --}}
{{-- 
    Parâmetros:
    - $idPrefix: Prefixo para IDs (ex: 'domusia_') - default: ''
    - $fieldName: Nome do campo - default: 'historico_complementar'
    - $maxLength: Máximo de caracteres - default: 500
    - $rows: Número de linhas do textarea - default: 3
    - $placeholder: Placeholder do campo - default: 'Observações adicionais sobre o lançamento...'
    - $cardClass: Classes adicionais do card - default: ''
    - $title: Título do card - default: 'Histórico Complementar'
--}}

@php
    $idPrefix = $idPrefix ?? '';
    $fieldName = $fieldName ?? 'historico_complementar';
    $maxLength = $maxLength ?? 500;
    $rows = $rows ?? 7;
    $placeholder = $placeholder ?? 'Observações adicionais sobre o lançamento...';
    $cardClass = $cardClass ?? '';
    $title = $title ?? 'Histórico Complementar';
    
    // ID do campo
    $fieldId = $idPrefix . 'historico';
@endphp

<div class="card border border-gray-300 mb-5 {{ $cardClass }}">
    <div class="card-header min-h-45px">
        <h3 class="card-title fs-6 fw-bold">{{ $title }}</h3>
    </div>
    <div class="card-body px-6 py-5">
        <textarea 
            class="form-control form-control-sm" 
            name="{{ $fieldName }}"
            id="{{ $fieldId }}" 
            maxlength="{{ $maxLength }}" 
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"></textarea>
        <span class="fs-8 text-muted mt-1 d-block">Máximo {{ $maxLength }} caracteres</span>
    </div>
</div>
