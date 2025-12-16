<!--begin::Modal - Edição em Massa de Lançamentos Padrões-->
<div class="modal fade" id="kt_modal_lancamento_padrao_bulk" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header justify-content-between mb-10">
                <h3 class="modal-title fw-bold">Editar em Massa</h3>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <i class="bi bi-x-lg fs-3"></i>
                    </span>
                </div>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin::Nav Tabs-->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-10">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-4 active" data-bs-toggle="tab" href="#kt_tab_baixar_modelo">
                            Baixar modelo
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary me-4" data-bs-toggle="tab" href="#kt_tab_enviar_arquivo">
                            Enviar arquivo
                        </a>
                    </li>
                </ul>
                <!--end::Nav Tabs-->

                <!--begin::Tab Content-->
                <div class="tab-content">
                    <!--begin::Tab Pane - Baixar Modelo-->
                    <div class="tab-pane fade show active" id="kt_tab_baixar_modelo" role="tabpanel">
                        <div class="d-flex flex-column">
                            <h4 class="fw-bold mb-5">Formulário Básico</h4>
                            <p class="text-gray-700 fs-6 mb-8">
                                O formulário básico contém os campos obrigatórios para editar seus lançamentos padrões.
                                O formulário pode ser usado para qualquer categoria.
                            </p>
                            <div class="d-flex justify-content-start">
                                <a href="{{ route('lancamentoPadrao.download-template') }}"
                                   class="btn btn-primary"
                                   id="btn_download_template">
                                    <i class="bi bi-download me-2"></i>
                                    Baixar
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end::Tab Pane - Baixar Modelo-->

                    <!--begin::Tab Pane - Enviar Arquivo-->
                    <div class="tab-pane fade" id="kt_tab_enviar_arquivo" role="tabpanel">
                        <div class="d-flex flex-column">
                            <p class="text-gray-700 fs-6 mb-8">
                                Envie o modelo completo e você pode verificar os novos lançamentos atualizados na lista
                                quando o envio for completado.
                            </p>

                            <!--begin::Upload Area-->
                            <div class="border border-dashed border-gray-300 rounded p-10 mb-10 text-center"
                                 id="upload_area">
                                <i class="bi bi-cloud-upload fs-1 text-gray-400 mb-5"></i>
                                <p class="text-gray-700 fs-6 mb-2">
                                    Selecione o arquivo ou insira seus arquivos do Excel aqui
                                </p>
                                <p class="text-gray-500 fs-7 mb-5">
                                    Tamanho max.: 10.0 MB apenas xlsx
                                </p>
                                <button type="button" class="btn btn-primary" id="btn_select_file">
                                    Selecionar arquivo
                                </button>
                                <input type="file"
                                       id="file_input"
                                       accept=".xlsx"
                                       style="display: none;">
                            </div>
                            <!--end::Upload Area-->

                            <!--begin::File Info (hidden by default)-->
                            <div id="file_info" class="d-none mb-10">
                                <div class="alert alert-info d-flex align-items-center p-5">
                                    <i class="bi bi-file-earmark-spreadsheet fs-2x text-primary me-4"></i>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold" id="file_name"></span>
                                        <span class="text-muted fs-7" id="file_size"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-icon btn-light ms-auto" id="btn_remove_file">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </div>
                            <!--end::File Info-->

                            <!--begin::Registros Section-->
                            <div class="separator separator-dashed my-10"></div>
                            <h4 class="fw-bold mb-5">Registros</h4>
                            <p class="text-gray-500 fs-7 mb-5">
                                Os arquivos serão mantidos apenas nos últimos 30 dias.
                            </p>

                            <!--begin::Table-->
                            <div class="table-responsive">
                                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                                    <thead>
                                        <tr class="fw-bold text-muted">
                                            <th class="min-w-100px">Data</th>
                                            <th class="min-w-200px">Nome do arquivo</th>
                                            <th class="min-w-100px">Produtos</th>
                                            <th class="min-w-100px">Status</th>
                                            <th class="min-w-100px text-end">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="registros_table_body">
                                        <tr>
                                            <td colspan="5" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <i class="bi bi-box-arrow-up fs-1 text-gray-400 mb-3"></i>
                                                    <span class="text-gray-500 fs-6">Ainda não há histórico de upload</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!--end::Table-->
                        </div>
                    </div>
                    <!--end::Tab Pane - Enviar Arquivo-->
                </div>
                <!--end::Tab Content-->
            </div>
            <!--end::Modal body-->

            <!--begin::Modal footer-->
            <div class="modal-footer flex-center">
                <button type="button" data-bs-dismiss="modal" class="btn btn-sm btn-light me-3">
                    <i class="bi bi-x-lg me-2"></i> Fechar
                </button>
                <button type="button" id="btn_upload_file" class="btn btn-sm btn-primary d-none">
                    <span class="indicator-label">
                        <i class="bi bi-upload me-2"></i> Enviar arquivo
                    </span>
                    <span class="indicator-progress">Enviando...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Edição em Massa de Lançamentos Padrões-->

<!--begin::Script para funcionalidade do modal-->
<script>
(function() {
    'use strict';

    function initBulkModal() {
        const modal = document.getElementById('kt_modal_lancamento_padrao_bulk');
        const btnSelectFile = document.getElementById('btn_select_file');
        const fileInput = document.getElementById('file_input');
        const uploadArea = document.getElementById('upload_area');
        const fileInfo = document.getElementById('file_info');
        const btnRemoveFile = document.getElementById('btn_remove_file');
        const fileName = document.getElementById('file_name');
        const fileSize = document.getElementById('file_size');

        if (!modal || !btnSelectFile || !fileInput) {
            return;
        }

        // Abrir seletor de arquivo ao clicar no botão
        btnSelectFile.addEventListener('click', function() {
            fileInput.click();
        });

        // Abrir seletor de arquivo ao clicar na área de upload
        uploadArea.addEventListener('click', function(e) {
            if (e.target === uploadArea || e.target.closest('#upload_area')) {
                fileInput.click();
            }
        });

        // Prevenir comportamento padrão de drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('border-primary');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('border-primary');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('border-primary');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        // Validar e exibir informações do arquivo selecionado
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFileSelect(file);
            }
        });

        function handleFileSelect(file) {
            // Validar tipo de arquivo
            if (!file.name.endsWith('.xlsx')) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        text: 'Por favor, selecione apenas arquivos .xlsx',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, entendi!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                    alert('Por favor, selecione apenas arquivos .xlsx');
                }
                fileInput.value = '';
                return;
            }

            // Validar tamanho (10MB)
            const maxSize = 10 * 1024 * 1024; // 10MB em bytes
            if (file.size > maxSize) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        text: 'O arquivo excede o tamanho máximo de 10MB',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, entendi!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                    alert('O arquivo excede o tamanho máximo de 10MB');
                }
                fileInput.value = '';
                return;
            }

            // Exibir informações do arquivo
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.classList.remove('d-none');
            uploadArea.classList.add('d-none');

            // Mostrar botão de envio
            const btnUpload = document.getElementById('btn_upload_file');
            if (btnUpload) {
                btnUpload.classList.remove('d-none');
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Remover arquivo selecionado
        btnRemoveFile.addEventListener('click', function() {
            fileInput.value = '';
            fileInfo.classList.add('d-none');
            uploadArea.classList.remove('d-none');

            // Ocultar botão de envio
            const btnUpload = document.getElementById('btn_upload_file');
            if (btnUpload) {
                btnUpload.classList.add('d-none');
            }
        });

        // Enviar arquivo
        const btnUpload = document.getElementById('btn_upload_file');
        if (btnUpload) {
            btnUpload.addEventListener('click', function() {
                const file = fileInput.files[0];
                if (!file) {
                    return;
                }

                // Validar novamente
                if (!file.name.endsWith('.xlsx')) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: 'Por favor, selecione apenas arquivos .xlsx',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, entendi!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                    return;
                }

                const maxSize = 10 * 1024 * 1024;
                if (file.size > maxSize) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: 'O arquivo excede o tamanho máximo de 10MB',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, entendi!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                    return;
                }

                // Preparar FormData
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                // Ativar indicador de loading
                btnUpload.setAttribute('data-kt-indicator', 'on');
                btnUpload.disabled = true;

                // Enviar via AJAX
                fetch('{{ route("lancamentoPadrao.upload-template") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    btnUpload.removeAttribute('data-kt-indicator');
                    btnUpload.disabled = false;

                    if (data.success) {
                        // Sucesso
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: data.message || 'Arquivo enviado com sucesso!',
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, entendi!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            }).then(function() {
                                // Atualizar tabela de registros
                                if (data.registro) {
                                    addRegistroToTable(data.registro);
                                }

                                // Limpar arquivo
                                fileInput.value = '';
                                fileInfo.classList.add('d-none');
                                uploadArea.classList.remove('d-none');
                                btnUpload.classList.add('d-none');

                                // Recarregar página para atualizar a tabela principal
                                if (typeof window.location !== 'undefined') {
                                    window.location.reload();
                                }
                            });
                        } else {
                            alert(data.message || 'Arquivo enviado com sucesso!');
                            window.location.reload();
                        }
                    } else {
                        // Erro
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                text: data.message || 'Erro ao enviar arquivo. Por favor, tente novamente.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, entendi!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        } else {
                            alert(data.message || 'Erro ao enviar arquivo.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao enviar arquivo:', error);
                    btnUpload.removeAttribute('data-kt-indicator');
                    btnUpload.disabled = false;

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: 'Erro ao enviar arquivo. Por favor, tente novamente.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, entendi!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    } else {
                        alert('Erro ao enviar arquivo. Por favor, tente novamente.');
                    }
                });
            });
        }

        function addRegistroToTable(registro) {
            const tbody = document.getElementById('registros_table_body');
            if (!tbody) return;

            // Remove a mensagem de "sem histórico"
            const emptyRow = tbody.querySelector('tr td[colspan]');
            if (emptyRow) {
                emptyRow.closest('tr').remove();
            }

            // Cria nova linha
            const row = document.createElement('tr');
            const statusBadge = registro.status === 'processado'
                ? '<span class="badge badge-light-success">Processado</span>'
                : registro.status === 'processando'
                ? '<span class="badge badge-light-warning">Processando</span>'
                : '<span class="badge badge-light-danger">Erro</span>';

            row.innerHTML = `
                <td>${registro.data}</td>
                <td>${registro.nome_arquivo}</td>
                <td>${registro.produtos || 0}</td>
                <td>${statusBadge}</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-light" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </td>
            `;

            tbody.insertBefore(row, tbody.firstChild);
        }

        // Resetar modal ao fechar
        modal.addEventListener('hidden.bs.modal', function() {
            fileInput.value = '';
            fileInfo.classList.add('d-none');
            uploadArea.classList.remove('d-none');

            // Ocultar botão de envio
            const btnUpload = document.getElementById('btn_upload_file');
            if (btnUpload) {
                btnUpload.classList.add('d-none');
            }

            // Resetar para a primeira aba
            const firstTab = modal.querySelector('[href="#kt_tab_baixar_modelo"]');
            if (firstTab) {
                firstTab.click();
            }
        });
    }

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBulkModal);
    } else {
        initBulkModal();
    }
})();
</script>
<!--end::Script para funcionalidade do modal-->

