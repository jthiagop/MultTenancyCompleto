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
                        <input type="file"
                               class="form-control form-control-sm anexo-file-input"
                               name="{{ $name }}[{{ $index }}][arquivo]"
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                               data-index="{{ $index }}">
                        @if($nomeArquivo)
                            <div class="file-preview mt-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-paperclip text-primary me-2"></i>
                                    <span class="file-name text-gray-700">{{ $nomeArquivo }}</span>
                                    @if($tamanhoArquivo > 0)
                                        <span class="file-size text-muted ms-2">
                                            ({{ number_format($tamanhoArquivo / 1024, 0) }}Kb)
                                        </span>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2 remove-file">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="file-preview d-none mt-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-paperclip text-primary me-2"></i>
                                    <span class="file-name text-gray-700"></span>
                                    <span class="file-size text-muted ms-2"></span>
                                    <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-2 remove-file">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
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
                <option value="Boleto" {{ $tipoAnexo === 'Boleto' ? 'selected' : '' }}>Boleto</option>
                <option value="Nota Fiscal" {{ $tipoAnexo === 'Nota Fiscal' ? 'selected' : '' }}>Nota Fiscal</option>
                <option value="Fatura" {{ $tipoAnexo === 'Fatura' ? 'selected' : '' }}>Fatura</option>
                <option value="Recibo" {{ $tipoAnexo === 'Recibo' ? 'selected' : '' }}>Recibo</option>
                <option value="Comprovante" {{ $tipoAnexo === 'Comprovante' ? 'selected' : '' }}>Comprovante</option>
                <option value="Contrato" {{ $tipoAnexo === 'Contrato' ? 'selected' : '' }}>Contrato</option>
                <option value="Outros" {{ $tipoAnexo === 'Outros' ? 'selected' : '' }}>Outros</option>
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

