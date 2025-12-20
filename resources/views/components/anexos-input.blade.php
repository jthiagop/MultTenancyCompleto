@props([
    'name' => 'anexos',
    'anexosExistentes' => [],
    'uniqueId' => null
])

@php
    $uniqueId = $uniqueId ?? uniqid();
    $containerId = 'anexos-container-' . $uniqueId;
@endphp

<div class="anexos-container" data-name="{{ $name }}" data-unique-id="{{ $uniqueId }}" id="{{ $containerId }}">
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
        // Aguarda o DOM estar pronto ou executa imediatamente se já estiver
        function initAnexosComponent() {
            // Busca o container específico pelo ID único ou pelo data-name
            const containerId = '{{ $containerId }}';
            let container = document.getElementById(containerId);

            // Se não encontrou pelo ID, tenta pelo data-name (fallback)
            if (!container) {
                const containers = document.querySelectorAll('.anexos-container[data-name="{{ $name }}"][data-unique-id="{{ $uniqueId }}"]');
                container = containers.length > 0 ? containers[0] : null;
            }

            if (!container) {
                // Se o container ainda não existe, tenta novamente após um delay
                setTimeout(initAnexosComponent, 100);
                return;
            }

            // Evita inicialização duplicada
            if (container.dataset.initialized === 'true') return;
            container.dataset.initialized = 'true';

            let rowIndex = {{ count($anexosExistentes) }};

            // Função para criar HTML de uma nova linha
            function createAnexoRowHTML(index) {
                return `
                    <div class="anexo-row mb-4 p-4 border rounded bg-light">
                    <div class="row g-3 align-items-end">
                        <!-- Forma do anexo -->
                        <div class="col-md-2">
                            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Forma do anexo</label>
                            <select class="form-select form-select-sm forma-anexo-select"
                                    name="{{ $name }}[${index}][forma_anexo]"
                                    data-control="select2"
                                    data-hide-search="true"
                                    data-placeholder="Selecione">
                                <option value="arquivo" selected>Arquivo</option>
                                <option value="link">Link</option>
                            </select>
                        </div>

                        <!-- Anexo (Arquivo ou Link) -->
                        <div class="col-md-3">
                            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Anexo</label>
                            <div class="anexo-input-group">
                                <div class="file-input-wrapper">
                                    <input type="file"
                                           class="form-control form-control-sm anexo-file-input"
                                           name="{{ $name }}[${index}][arquivo]"
                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                           data-index="${index}">
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
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de anexo -->
                        <div class="col-md-2">
                            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Tipo de anexo</label>
                            <select class="form-select form-select-sm"
                                    name="{{ $name }}[${index}][tipo_anexo]"
                                    data-control="select2"
                                    data-hide-search="true"
                                    data-placeholder="Selecione">
                                <option value=""></option>
                                <option value="Boleto">Boleto</option>
                                <option value="Nota Fiscal">Nota Fiscal</option>
                                <option value="Fatura">Fatura</option>
                                <option value="Recibo">Recibo</option>
                                <option value="Comprovante">Comprovante</option>
                                <option value="Contrato">Contrato</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>

                        <!-- Descrição -->
                        <div class="col-md-4">
                            <label class="d-md-none fs-7 fw-semibold text-muted mb-2">Descrição</label>
                            <input type="text"
                                   class="form-control form-control-sm"
                                   name="{{ $name }}[${index}][descricao]"
                                   placeholder="Descrição do anexo"
                                   value="">
                        </div>

                        <!-- Botão remover -->
                        <div class="col-md-1">
                            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-anexo" title="Remover anexo">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

            // Função para adicionar nova linha
            function addAnexoRow() {
                const rowsContainer = container.querySelector('.anexos-rows');
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = createAnexoRowHTML(rowIndex);
                const newRow = tempDiv.firstElementChild;

                rowsContainer.appendChild(newRow);

                // Inicializa os selects da nova linha
                initRowSelects(newRow);

                // Adiciona evento ao input de arquivo
                const fileInput = newRow.querySelector('.anexo-file-input');
                if (fileInput) {
                    fileInput.addEventListener('change', handleFileSelect);
                }

                rowIndex++;
            }

            // Função para remover linha
            function removeAnexoRow(button) {
                const row = button.closest('.anexo-row');
                if (row) {
                    row.remove();
                }
            }

            // Função para inicializar selects de uma linha
            function initRowSelects(rowElement) {
                if (!rowElement) return;

                // Inicializa Select2 para os selects da linha
                const selects = rowElement.querySelectorAll('select[data-control="select2"]');
                selects.forEach(select => {
                    if (typeof KTSelect2 !== 'undefined') {
                        KTSelect2.getInstance(select)?.destroy();
                        new KTSelect2(select);
                    }
                });
            }

            // Função para alternar entre Arquivo e Link
            function toggleAnexoType(select) {
                const row = select.closest('.anexo-row');
                const anexoInputGroup = row.querySelector('.anexo-input-group');
                const formaAnexo = select.value;

                // Extrai o índice do nome do select
                const indexMatch = select.name.match(/\[(\d+)\]/);
                const index = indexMatch ? indexMatch[1] : '0';

                if (formaAnexo === 'arquivo') {
                    // Mostra input de arquivo
                    anexoInputGroup.innerHTML = `
                        <div class="file-input-wrapper">
                            <input type="file"
                                   class="form-control form-control-sm anexo-file-input"
                                   name="{{ $name }}[${index}][arquivo]"
                                   accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"
                                   data-index="${index}">
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
                        </div>
                    `;

                    // Adiciona evento ao input de arquivo
                    const fileInput = anexoInputGroup.querySelector('.anexo-file-input');
                    if (fileInput) {
                        fileInput.addEventListener('change', handleFileSelect);
                    }
                } else if (formaAnexo === 'link') {
                    // Mostra input de link
                    anexoInputGroup.innerHTML = `
                        <input type="url"
                               class="form-control form-control-sm anexo-link-input"
                               name="{{ $name }}[${index}][link]"
                               placeholder="https://exemplo.com"
                               data-index="${index}">
                    `;
                }
            }

            // Função para lidar com seleção de arquivo
            function handleFileSelect(event) {
                const fileInput = event.target;
                const file = fileInput.files[0];
                const row = fileInput.closest('.anexo-row');
                const preview = row.querySelector('.file-preview');
                const fileName = row.querySelector('.file-name');
                const fileSize = row.querySelector('.file-size');

                if (file) {
                    const fileSizeFormatted = formatFileSize(file.size);
                    fileName.textContent = file.name;
                    fileSize.textContent = `(${fileSizeFormatted})`;
                    preview.classList.remove('d-none');
                }
            }

            // Função para formatar tamanho do arquivo
            function formatFileSize(bytes) {
                if (bytes === 0) return '0Kb';
                const kb = Math.round(bytes / 1024);
                return kb + 'Kb';
            }

            // Event listeners - verifica se já foram adicionados
            if (!container.dataset.listenersAttached) {
                container.addEventListener('click', function(e) {
                    // Botão adicionar anexo
                    if (e.target.closest('.btn-add-anexo')) {
                        e.preventDefault();
                        e.stopPropagation();
                        addAnexoRow();
                        return false;
                    }

                    // Botão remover linha
                    if (e.target.closest('.btn-remove-anexo')) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeAnexoRow(e.target.closest('.btn-remove-anexo'));
                        return false;
                    }

                    // Botão remover arquivo
                    if (e.target.closest('.remove-file')) {
                        e.preventDefault();
                        e.stopPropagation();
                        const row = e.target.closest('.anexo-row');
                        const fileInput = row.querySelector('.anexo-file-input');
                        const preview = row.querySelector('.file-preview');
                        if (fileInput) {
                            fileInput.value = '';
                            preview.classList.add('d-none');
                        }
                        return false;
                    }
                });

                // Event listener para mudança de forma do anexo
                container.addEventListener('change', function(e) {
                    if (e.target.matches('select[name*="[forma_anexo]"]')) {
                        toggleAnexoType(e.target);
                    }
                });

                // Marca que os listeners foram adicionados
                container.dataset.listenersAttached = 'true';
            }

            // Inicializa selects existentes
            container.querySelectorAll('.anexo-row').forEach(row => {
                initRowSelects(row);

                // Adiciona eventos aos inputs de arquivo existentes
                const fileInput = row.querySelector('.anexo-file-input');
                if (fileInput) {
                    fileInput.addEventListener('change', handleFileSelect);
                }
            });
        }

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAnexosComponent);
        } else {
            initAnexosComponent();
        }

        // Também inicializa quando a tab for exibida (para modais)
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

        // Expõe a função de inicialização globalmente com ID único para ser chamada quando o container for exibido
        const functionName = 'initAnexosComponent_{{ str_replace(['-', '[', ']'], ['_', '_', '_'], $name) }}_{{ $uniqueId }}';
        window[functionName] = initAnexosComponent;
    })();
</script>


