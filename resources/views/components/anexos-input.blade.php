@props([
    'name' => 'anexos',
    'anexosExistentes' => [],
    'uniqueId' => null,
    'maxFileSize' => 10, // MB
])

@php
    $uniqueId = $uniqueId ?? uniqid();
    $containerId = 'anexos-container-' . $uniqueId;
    
    // Tipos de anexo centralizados (única fonte de verdade)
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
    
    // Tamanho máximo em bytes
    $maxFileSizeBytes = $maxFileSize * 1024 * 1024;
@endphp

<div class="anexos-container" 
     data-name="{{ $name }}" 
     data-unique-id="{{ $uniqueId }}" 
     data-tipos-anexo="{{ json_encode($tiposAnexo) }}"
     data-extensoes-aceitas="{{ $extensoesAceitas }}"
     data-max-file-size="{{ $maxFileSizeBytes }}"
     data-max-file-size-mb="{{ $maxFileSize }}"
     id="{{ $containerId }}">
    <!-- Cabeçalho das colunas -->
    <div class="row g-3 mb-3 d-none d-md-flex">
        <div class="col-md-2">
            <label class="fs-6 fw-semibold text-muted">Forma do anexo</label>
        </div>
        <div class="col-md-3">
            <label class="fs-6 fw-semibold text-muted">Anexo</label>
        </div>
        <div class="col-md-2">
            <label class="fs-6 fw-semibold text-muted">Tipo de anexo</label>
        </div>
        <div class="col-md-4">
            <label class="fs-6 fw-semibold text-muted">Descrição</label>
        </div>
        <div class="col-md-1"></div>
    </div>

    <!-- Container para as linhas de anexos -->
    <div class="anexos-rows">
        @if(count($anexosExistentes) > 0)
            @foreach($anexosExistentes as $index => $anexo)
                @include('components.anexos-row', [
                    'index' => $index,
                    'name' => $name,
                    'anexo' => $anexo
                ])
            @endforeach
        @endif
    </div>

    <!-- Botão para adicionar novo anexo -->
    <div class="mt-4">
        <button type="button" class="btn btn-sm btn-light-primary btn-add-anexo">
            <i class="fas fa-plus fs-6 me-1"></i>
            Adicionar anexo
        </button>
    </div>
</div>

<script>
    (function() {
        const containerId = '{{ $containerId }}';
        let initAttempts = 0;
        const maxInitAttempts = 50; // Máximo 5 segundos (50 x 100ms)

        function initAnexosComponent() {
            let container = document.getElementById(containerId);

            // Fallback: busca por data attributes
            if (!container) {
                const containers = document.querySelectorAll('.anexos-container[data-name="{{ $name }}"][data-unique-id="{{ $uniqueId }}"]');
                container = containers.length > 0 ? containers[0] : null;
            }

            if (!container) {
                initAttempts++;
                if (initAttempts < maxInitAttempts) {
                    setTimeout(initAnexosComponent, 100);
                } else {
                    console.warn('[AnexosInput] Container não encontrado após ' + maxInitAttempts + ' tentativas:', containerId);
                }
                return;
            }

            // Evita inicialização duplicada
            if (container.dataset.initialized === 'true') return;
            container.dataset.initialized = 'true';

            // Configurações do container (centralizadas)
            const tiposAnexo = JSON.parse(container.dataset.tiposAnexo || '[]');
            const extensoesAceitas = container.dataset.extensoesAceitas || '.pdf,.jpg,.png';
            const maxFileSize = parseInt(container.dataset.maxFileSize) || 10485760; // 10MB
            const maxFileSizeMb = parseInt(container.dataset.maxFileSizeMb) || 10;
            const namePrefix = container.dataset.name || 'anexos';

            let rowIndex = {{ count($anexosExistentes) }};

            // Gera options HTML dos tipos de anexo
            function getTiposAnexoOptions() {
                return '<option value=""></option>' + tiposAnexo.map(tipo => 
                    `<option value="${tipo}">${tipo}</option>`
                ).join('');
            }

            // Cria HTML de uma nova linha de anexo
            function createAnexoRowHTML(index) {
                return `
                    <div class="anexo-row mb-4 p-4 border rounded bg-light">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-2">
                                <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Forma do anexo</label>
                                <select class="form-select form-select-sm forma-anexo-select"
                                        name="${namePrefix}[${index}][forma_anexo]"
                                        data-control="select2"
                                        data-hide-search="true"
                                        data-placeholder="Selecione">
                                    <option value="arquivo" selected>Arquivo</option>
                                    <option value="link">Link</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Anexo</label>
                                <div class="anexo-input-group">
                                    <div class="file-input-wrapper">
                                        <input type="file"
                                               class="form-control form-control-sm anexo-file-input"
                                               name="${namePrefix}[${index}][arquivo]"
                                               accept="${extensoesAceitas}"
                                               data-index="${index}">
                                        <div class="file-preview d-none">
                                            <div class="d-flex align-items-center bg-light-primary rounded px-3 ">
                                                <i class="fas fa-file-alt text-primary me-2 fs-7"></i>
                                                <span class="file-name text-gray-800 fs-7 me-2"></span>
                                                <span class="file-size text-muted fs-8 me-2"></span>
                                                <button type="button" class="btn btn-sm btn-icon btn-light-danger remove-file" title="Remover arquivo">
                                                    <i class="fas fa-times fs-8"></i>
                                                </button>
                                            </div>
                                            <div class="file-error text-danger fs-7 mt-1 d-none"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Tipo de anexo</label>
                                <select class="form-select form-select-sm"
                                        name="${namePrefix}[${index}][tipo_anexo]"
                                        data-control="select2"
                                        data-hide-search="true"
                                        data-placeholder="Selecione">
                                    ${getTiposAnexoOptions()}
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Descrição</label>
                                <input type="text"
                                       class="form-control form-control-sm"
                                       name="${namePrefix}[${index}][descricao]"
                                       placeholder="Descrição do anexo"
                                       value="">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-anexo" title="Remover anexo">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Adiciona nova linha
            function addAnexoRow() {
                const rowsContainer = container.querySelector('.anexos-rows');
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = createAnexoRowHTML(rowIndex);
                const newRow = tempDiv.firstElementChild;

                rowsContainer.appendChild(newRow);
                initRowSelects(newRow);

                const fileInput = newRow.querySelector('.anexo-file-input');
                if (fileInput) {
                    fileInput.addEventListener('change', handleFileSelect);
                }

                rowIndex++;
            }

            // Remove linha
            function removeAnexoRow(button) {
                const row = button.closest('.anexo-row');
                if (row) row.remove();
            }

            // Inicializa Select2 nos selects da linha (com fallback jQuery)
            function initRowSelects(rowElement) {
                if (!rowElement) return;

                const selects = rowElement.querySelectorAll('select[data-control="select2"]');
                selects.forEach(select => {
                    try {
                        // Tenta KTSelect2 (Metronic)
                        if (typeof KTSelect2 !== 'undefined') {
                            const instance = KTSelect2.getInstance(select);
                            if (instance) instance.destroy();
                            new KTSelect2(select);
                        } 
                        // Fallback: jQuery Select2
                        else if (typeof $ !== 'undefined' && $.fn.select2) {
                            $(select).select2({
                                minimumResultsForSearch: Infinity,
                                dropdownParent: $(select).closest('.modal, .drawer, body')
                            });
                        }
                    } catch (e) {
                        console.warn('[AnexosInput] Erro ao inicializar Select2:', e);
                    }
                });
            }

            // Alterna entre input de Arquivo e Link
            function toggleAnexoType(select) {
                const row = select.closest('.anexo-row');
                const anexoInputGroup = row.querySelector('.anexo-input-group');
                const formaAnexo = select.value;

                const indexMatch = select.name.match(/\[(\d+)\]/);
                const index = indexMatch ? indexMatch[1] : '0';

                if (formaAnexo === 'arquivo') {
                    anexoInputGroup.innerHTML = `
                        <div class="file-input-wrapper">
                            <input type="file"
                                   class="form-control form-control-sm anexo-file-input"
                                   name="${namePrefix}[${index}][arquivo]"
                                   accept="${extensoesAceitas}"
                                   data-index="${index}">
                            <div class="file-preview d-none border rounded bg-light-primary px-3 py-2">
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
                        </div>
                    `;

                    const fileInput = anexoInputGroup.querySelector('.anexo-file-input');
                    if (fileInput) {
                        fileInput.addEventListener('change', handleFileSelect);
                    }
                } else if (formaAnexo === 'link') {
                    anexoInputGroup.innerHTML = `
                        <input type="url"
                               class="form-control form-control-sm anexo-link-input"
                               name="${namePrefix}[${index}][link]"
                               placeholder="https://exemplo.com"
                               data-index="${index}">
                    `;
                }
            }

            // Lida com seleção de arquivo (com validação de tamanho)
            function handleFileSelect(event) {
                const fileInput = event.target;
                const file = fileInput.files[0];
                const row = fileInput.closest('.anexo-row');
                const preview = row.querySelector('.file-preview');
                const fileName = row.querySelector('.file-name');
                const fileSize = row.querySelector('.file-size');
                const fileError = row.querySelector('.file-error');

                // Limpa erros anteriores
                if (fileError) {
                    fileError.classList.add('d-none');
                    fileError.textContent = '';
                }

                if (file) {
                    // Valida tamanho do arquivo
                    if (file.size > maxFileSize) {
                        fileInput.value = '';
                        if (fileError) {
                            fileError.textContent = `Arquivo muito grande (${formatFileSize(file.size)}). Máximo permitido: ${maxFileSizeMb}MB`;
                            fileError.classList.remove('d-none');
                        } else {
                            alert(`Arquivo muito grande (${formatFileSize(file.size)}). Máximo permitido: ${maxFileSizeMb}MB`);
                        }
                        preview.classList.add('d-none');
                        fileInput.classList.remove('d-none');
                        return;
                    }

                    const fileSizeFormatted = formatFileSize(file.size);
                    // Trunca nome em 30 caracteres
                    const truncatedName = file.name.length > 40 
                        ? file.name.substring(0, 27) + '...' 
                        : file.name;
                    fileName.textContent = truncatedName;
                    fileName.title = file.name; // Tooltip com nome completo
                    fileSize.textContent = fileSizeFormatted;
                    
                    // Esconde o input file e mostra o preview compacto
                    fileInput.classList.add('d-none');
                    preview.classList.remove('d-none');
                }
            }

            // Formata tamanho do arquivo
            function formatFileSize(bytes) {
                if (bytes === 0) return '0 KB';
                if (bytes < 1024) return bytes + ' B';
                if (bytes < 1048576) return Math.round(bytes / 1024) + ' KB';
                return (bytes / 1048576).toFixed(2) + ' MB';
            }

            // Event listeners (delegação de eventos)
            if (!container.dataset.listenersAttached) {
                container.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-add-anexo')) {
                        e.preventDefault();
                        e.stopPropagation();
                        addAnexoRow();
                        return false;
                    }

                    if (e.target.closest('.btn-remove-anexo')) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeAnexoRow(e.target.closest('.btn-remove-anexo'));
                        return false;
                    }

                    if (e.target.closest('.remove-file')) {
                        e.preventDefault();
                        e.stopPropagation();
                        const row = e.target.closest('.anexo-row');
                        const fileInput = row.querySelector('.anexo-file-input');
                        const preview = row.querySelector('.file-preview');
                        const fileError = row.querySelector('.file-error');
                        if (fileInput) {
                            fileInput.value = '';
                            fileInput.classList.remove('d-none'); // Mostra o input novamente
                            preview.classList.add('d-none');
                        }
                        if (fileError) {
                            fileError.classList.add('d-none');
                            fileError.textContent = '';
                        }
                        return false;
                    }
                });

                container.addEventListener('change', function(e) {
                    if (e.target.matches('select[name*="[forma_anexo]"]')) {
                        toggleAnexoType(e.target);
                    }
                });

                container.dataset.listenersAttached = 'true';
            }

            // Inicializa selects e inputs existentes
            container.querySelectorAll('.anexo-row').forEach(row => {
                initRowSelects(row);

                const fileInput = row.querySelector('.anexo-file-input');
                if (fileInput) {
                    fileInput.addEventListener('change', handleFileSelect);
                }
            });
        }

        // Inicializa quando DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAnexosComponent);
        } else {
            initAnexosComponent();
        }

        // Reinicializa quando a tab for exibida (para modais)
        const tabPane = document.querySelector('#kt_tab_pane_2');
        if (tabPane) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (tabPane.classList.contains('show') && tabPane.classList.contains('active')) {
                            setTimeout(initAnexosComponent, 100);
                        }
                    }
                });
            });
            observer.observe(tabPane, { attributes: true });
        }

        // Expõe função globalmente para inicialização manual
        window['initAnexosComponent_{{ str_replace(['-', '[', ']'], ['_', '_', '_'], $name) }}_{{ $uniqueId }}'] = initAnexosComponent;
    })();
</script>


