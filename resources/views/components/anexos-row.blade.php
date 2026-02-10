@props([
    'index',
    'name',
    'anexo' => null
])

@php
    $formaAnexo = $anexo ? ($anexo->forma_anexo ?? 'arquivo') : 'arquivo';
    $isArquivo = $formaAnexo === 'arquivo';
    $tipoAnexo = $anexo ? ($anexo->tipo_anexo ?? '') : '';
    $descricao = $anexo ? ($anexo->descricao ?? '') : '';
    $linkUrl = $anexo && !$isArquivo ? ($anexo->link ?? '') : '';
    $nomeArquivo = $anexo && $isArquivo ? ($anexo->nome_arquivo ?? '') : '';
    $tamanhoArquivo = $anexo && $isArquivo ? ($anexo->tamanho_arquivo ?? 0) : 0;
    
    // Tipos de anexo centralizados (mesma lista do anexos-input.blade.php)
    $tiposAnexo = [
        'Boleto',
        'Nota Fiscal',
        'NF-e (XML)',
        'Fatura',
        'Recibo',
        'Comprovante',
        'Contrato',
        'DARF',
        'Guia',
        'Planilha',
        'Outros',
    ];
    
    // Extensões aceitas (financeiro-friendly)
    $extensoesAceitas = '.jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.csv,.xml,.txt,.odt,.ods';
@endphp

<div class="anexo-row mb-4 p-4 border rounded bg-light">
    <div class="row g-3 align-items-end">
        <!-- Forma do anexo -->
        <div class="col-md-2">
            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Forma do anexo</label>
            <select class="form-select form-select-sm "
                    name="{{ $name }}[{{ $index }}][forma_anexo]"
                    data-control="select2"
                    data-hide-search="true"
                    data-placeholder="Selecione">
                <option value="arquivo" {{ $formaAnexo === 'arquivo' ? 'selected' : '' }}>Arquivo</option>
                <option value="link" {{ $formaAnexo === 'link' ? 'selected' : '' }}>Link</option>
            </select>
        </div>

        <!-- Anexo (Arquivo ou Link) -->
        <div class="col-md-3">
            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Anexo</label>
            <div class="anexo-input-group">
                @if($formaAnexo === 'arquivo')
                    <div class="file-input-wrapper">
                        @php
                            // Trunca nome em 30 caracteres
                            $nomeExibicao = $nomeArquivo && strlen($nomeArquivo) > 30 
                                ? substr($nomeArquivo, 0, 27) . '...' 
                                : $nomeArquivo;
                        @endphp
                        @if($nomeArquivo)
                            {{-- Arquivo existente: mostra preview inline, input oculto --}}
                            <input type="file"
                                   class="form-control form-control-sm anexo-file-input d-none"
                                   name="{{ $name }}[{{ $index }}][arquivo]"
                                   accept="{{ $extensoesAceitas }}"
                                   data-index="{{ $index }}">
                            <div class="file-preview">
                                <div class="d-flex align-items-center bg-light-primary rounded px-3 py-2">
                                    <i class="fas fa-file-alt text-primary me-2 fs-7"></i>
                                    <span class="file-name text-gray-800 fs-7 me-2" title="{{ $nomeArquivo }}">{{ $nomeExibicao }}</span>
                                    @if($tamanhoArquivo)
                                        <span class="file-size text-muted fs-8 me-2">{{ number_format($tamanhoArquivo / 1024, 0) }} KB</span>
                                    @endif
                                    <button type="button" class="btn btn-xs btn-icon btn-light-danger remove-file" title="Remover arquivo">
                                        <i class="fas fa-times fs-8"></i>
                                    </button>
                                </div>
                                <div class="file-error text-danger fs-7 mt-1 d-none"></div>
                            </div>
                        @else
                            {{-- Novo arquivo: mostra input, preview oculto --}}
                            <input type="file"
                                   class="form-control form-control-sm anexo-file-input"
                                   name="{{ $name }}[{{ $index }}][arquivo]"
                                   accept="{{ $extensoesAceitas }}"
                                   data-index="{{ $index }}">
                            <div class="file-preview d-none">
                                <div class="d-flex align-items-center bg-light-primary rounded px-3 py-2">
                                    <i class="fas fa-file-alt text-primary me-2 fs-7"></i>
                                    <span class="file-name text-gray-800 fs-7 me-2"></span>
                                    <span class="file-size text-muted fs-8 me-2"></span>
                                    <button type="button" class="btn btn-xs btn-icon btn-light-danger remove-file" title="Remover arquivo">
                                        <i class="fas fa-times fs-8"></i>
                                    </button>
                                </div>
                                <div class="file-error text-danger fs-7 mt-1 d-none"></div>
                            </div>
                        @endif
                    </div>
                @else
                    <input type="url"
                           class="form-control form-control-sm anexo-link-input"
                           name="{{ $name }}[{{ $index }}][link]"
                           placeholder="https://exemplo.com"
                           value="{{ $linkUrl }}"
                           data-index="{{ $index }}">
                @endif
            </div>
        </div>

        <!-- Tipo de anexo -->
        <div class="col-md-2">
            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Tipo de anexo</label>
            <select class="form-select form-select-sm"
                    name="{{ $name }}[{{ $index }}][tipo_anexo]"
                    data-control="select2"
                    data-hide-search="true"
                    data-placeholder="Selecione">
                <option value=""></option>
                @foreach($tiposAnexo as $tipo)
                    <option value="{{ $tipo }}" {{ $tipoAnexo === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>

        <!-- Descrição -->
        <div class="col-md-4">
            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Descrição</label>
            <input type="text"
                   class="form-control form-control-sm"
                   name="{{ $name }}[{{ $index }}][descricao]"
                   placeholder="Descrição do anexo"
                   value="{{ $descricao }}">
        </div>

        <!-- Botão remover -->
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-anexo" title="Remover anexo">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

