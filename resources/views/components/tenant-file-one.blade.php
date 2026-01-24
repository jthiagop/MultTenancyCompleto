@props([
    'name',
    'id' => null,
    'label' => null,
    'accept' => '*/*',
    'value' => null,
    'required' => false
])

@php
    $id = $id ?? $name . '_' . uniqid();
@endphp

<div class="tenant-file-one-component" data-id="{{ $id }}">
    @if($label)
        <label class="form-label fs-6 fw-bold text-gray-700 mb-2 {{ $required ? 'required' : '' }}">{{ $label }}</label>
    @endif
    
    <!-- Input oculto -->
    <input type="file" name="{{ $name }}" id="{{ $id }}" class="d-none tenant-file-input" accept="{{ $accept }}" @if($required) required @endif>

    <!-- Estado 1: Vazio / Seleção -->
    <!-- Estilo: Borda tracejada, fundo claro, centralizado -->
    <div class="drop-zone tenant-file-dropzone border-dotted border-secondary  rounded p-6 text-center cursor-pointer hover-elevate-up" 
         id="drop-zone-{{ $id }}"
         data-input-id="{{ $id }}">
        <div class="d-flex flex-row align-items-center justify-content-flex gap-3">
             <!-- Botão Visual -->
             <button type="button" class="btn btn-sm btn-light-primary pointer-events-none ">Escolha um arquivo</button>
             <span class="text-gray-500 fw-semibold fs-7">Ou arraste-o para este espaço</span>
        </div>
    </div>

    <!-- Estado 2: Arquivo Selecionado -->
    <!-- Estilo: Fundo azulado claro, informações do arquivo, botão substituir e excluir -->
    <div class="file-info bg-light-primary border border-primary border-dotted rounded  d-flex align-items-center justify-content-between d-none" 
         id="file-info-{{ $id }}">
        
        <div class="d-flex align-items-center">
            <i class="fas fa-paperclip text-primary fs-2 me-3"></i>
            <div class="d-flex flex-column">
                <span class="fw-bold text-gray-800 fs-6" id="filename-{{ $id }}"></span>
                <span class="fw-semibold text-gray-500 fs-8" id="filesize-{{ $id }}"></span>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-light-primary" onclick="document.getElementById('{{ $id }}').click()">
                Substituir
            </button>
            <button type="button" class="btn btn-icon btn-sm btn-light-danger tenant-file-remove" data-input-id="{{ $id }}">
                <i class="fas fa-trash fs-5"></i>
            </button>
        </div>
    </div>
</div>
