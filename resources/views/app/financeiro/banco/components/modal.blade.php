<!-- Modal -->
<div class="modal  fade" id="modalConciliacao" tabindex="-1" aria-labelledby="modalConciliacaoLabel" aria-hidden="true">
    <!-- Modal -->
    <div class="modal-dialog modal-lg modal-dialog-top ">
        <div class="modal-content border border-active active">
            <!-- Cabeçalho -->
            <div class="modal-header border-1 ">
                <h2 class="modal-title w-100 text-center" id="modalImportarOFXLabel">Importe seu extrato em formato OFX
                </h2>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal"
                    aria-label="Fechar"></button>
            </div>

            <form id="uploadForm" action="{{ route('upload.ofx') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Corpo -->
                <div class="modal-body">
                    <!-- Descrição -->
                    <div class="text-center mb-4">
                        <div class="text-start d-inline-block">
                            <ol class="mb-0">
                                <li>Acesse o site do seu banco e exporte seu extrato no formato OFX.</li>
                                <li>Após salvar o arquivo no seu computador, você poderá importá-lo para o sistema.</li>
                            </ol>
                        </div>
                    </div>

                    <!-- Área de Upload -->
                    <div id="drop-area" class="border border-dashed border-primary rounded p-5 text-center bg-light">
                        <input type="file" id="fileInput" class="d-none" accept=".ofx" name="file" />
                        <div class="mb-3">
                            <i class="bi bi-cloud-upload fs-1 text-primary"></i>
                        </div>
                        <label for="fileInput" class="btn btn-sm btn-primary mb-2">
                            <i class="bi bi-paperclip me-2"></i>Escolha um arquivo clicando aqui
                        </label>
                        <p class="text-muted mb-2">ou arraste-o para este espaço e solte aqui para importar</p>
                        <p id="fileName" class="text-success fw-bold mt-2"></p>
                    </div>

                    <div class="separator my-5"></div>

                    <!--begin::Wrapper - Switch à esquerda -->
                    <div class="d-flex flex-stack text-start">
                        <!--begin::Label-->
                        <div class="me-5">
                            <!--begin::Label-->
                            <label class="fs-5 fw-semibold">Utilizar Horários de Missa?</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <div class="fs-7 fw-semibold text-muted">Irei fazer o lançamento de acordo com o horário de
                                missa.</div>
                            <!--end::Input-->

                        </div>
                        <!--end::Label-->
                        <!--begin::Switch-->
                        <label class="form-check form-switch form-check-custom ">
                            <!--begin::Input-->
                            <input class="form-check-input " name="usar_horarios_missa" type="checkbox" value="1"
                                id="switchHorariosMissas" />
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span class="form-check-label fw-semibold text-muted" id="labelHorariosMissas">Não</span>
                            <!--end::Label-->
                        </label>
                        <!--end::Switch-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                                <!--begin::Alert - Horários de Missa não cadastrados (oculto por padrão)-->
                <div id="alertHorariosMissas"
                    class="alert alert-dismissible bg-light-warning border border-warning d-flex flex-column flex-sm-row p-5 mb-4 d-none mx-4">
                    <!--begin::Icon-->
                    <span class="fs-2hx text-warning me-4 mb-5 mb-sm-0" >⚠️</span>
                    <!--end::Icon-->

                    <!--begin::Wrapper-->
                    <div class="d-flex flex-column pe-0 pe-sm-10">
                        <!--begin::Title-->
                        <h4 class="fw-semibold">Atenção</h4>
                        <!--end::Title-->

                        <!--begin::Content-->
                        <span>Não existem horários de missa cadastrados.
                            <a href="{{ route('company.edit', ['tab' => 'horario-missas']) }}"
                                class="text-primary fw-bold">Cadastrar Horários de Missa?</a>
                        </span>
                        <!--end::Content-->
                    </div>
                    <!--end::Wrapper-->

                    <!--begin::Close-->
                    <button type="button"
                        class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                        data-bs-dismiss="alert"
                        onclick="document.getElementById('alertHorariosMissas').classList.add('d-none')">
                        <i class="bi bi-x-circle fs-1 text-warning"></i>
                    </button>
                    <!--end::Close-->
                </div>
                <!--end::Alert-->

                <!-- Rodapé -->
                <div class="modal-footer items-end ">

                    <!--begin::Botões à direita -->
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary" id="importButton" disabled>
                            <span class="indicator-label">
                                <i class="bi bi-upload me-2"></i>Importar Extrato
                            </span>
                            <span class="indicator-progress d-none">
                                Por favor, aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                    <!--end::Botões -->
                </div>
                <!--end::Rodapé-->


            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const dropArea = document.getElementById('drop-area');
        const fileNameDisplay = document.getElementById('fileName');
        const importButton = document.getElementById('importButton');
        const uploadForm = document.getElementById('uploadForm');

        // ==================== VALIDAÇÃO DE ARQUIVO ====================

        const ALLOWED_EXTENSION = '.ofx';
        const ALLOWED_MIMETYPES = ['application/x-ofx', 'text/x-ofx', 'application/vnd.intu.qbo'];

        function validarArquivo(file) {
            // Validar extensão
            if (!file.name.toLowerCase().endsWith(ALLOWED_EXTENSION)) {
                return {
                    valido: false,
                    erro: `❌ Extensão inválida! Use apenas arquivos ${ALLOWED_EXTENSION}`
                };
            }

            // Validar MIME type (verificação adicional)
            if (!ALLOWED_MIMETYPES.includes(file.type) && file.type !== '') {
                console.warn(`MIME type: ${file.type}`);
            }

            return {
                valido: true
            };
        }

        function exibirMensagem(mensagem, tipo = 'success') {
            if (!mensagem) {
                fileNameDisplay.textContent = '';
                return;
            }

            fileNameDisplay.textContent = mensagem;
            fileNameDisplay.classList.remove('text-success', 'text-danger');
            fileNameDisplay.classList.add(`text-${tipo}`);
        }

        // ==================== GERENCIAR ARQUIVO SELECIONADO ====================

        function processoArquivo(files) {
            if (!files || files.length === 0) {
                importButton.disabled = true;
                exibirMensagem('');
                return;
            }

            const arquivo = files[0];
            const validacao = validarArquivo(arquivo);

            if (!validacao.valido) {
                importButton.disabled = true;
                exibirMensagem(validacao.erro, 'danger');
                fileInput.value = ''; // Limpa o input
                return;
            }

            // Arquivo válido
            importButton.disabled = false;
            exibirMensagem(`✅ ${arquivo.name} (${(arquivo.size / 1024).toFixed(2)} KB)`);
        }

        // Event listener para mudanças no input file
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                processoArquivo(this.files);
            });
        }

        // ==================== DRAG AND DROP ====================

        if (dropArea && fileInput) {
            // Previne comportamento padrão
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });

            // Destaca a área ao passar o arquivo
            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.add('border-success', 'bg-success');
                    dropArea.style.opacity = '0.7';
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, () => {
                    dropArea.classList.remove('border-success', 'bg-success');
                    dropArea.style.opacity = '1';
                });
            });

            // Manipula o drop do arquivo
            dropArea.addEventListener('drop', (e) => {
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    const event = new Event('change', {
                        bubbles: true
                    });
                    fileInput.dispatchEvent(event);
                }
            });
        }

        // ==================== VALIDAÇÃO NA SUBMISSÃO ====================

        if (uploadForm) {
            uploadForm.addEventListener('submit', function(e) {
                if (!fileInput.files || fileInput.files.length === 0) {
                    e.preventDefault();
                    exibirMensagem('⚠️ Por favor, selecione um arquivo OFX!', 'danger');
                }
            });
        }

        // ==================== GERENCIAMENTO DO CHECKBOX HORÁRIOS ====================

        const switchHorariosMissas = document.getElementById('switchHorariosMissas');
        const labelHorariosMissas = document.getElementById('labelHorariosMissas');
        const hasHorariosMissas = @json(isset($hasHorariosMissas) && $hasHorariosMissas);

        if (switchHorariosMissas && labelHorariosMissas) {
            function atualizarLabel() {
                labelHorariosMissas.textContent = switchHorariosMissas.checked ? 'Sim' : 'Não';
            }

            function exibirAlertHorariosMissas() {
                const alertElement = document.getElementById('alertHorariosMissas');
                if (alertElement) {
                    alertElement.classList.remove('d-none');
                }
            }

            function ocultarAlertHorariosMissas() {
                const alertElement = document.getElementById('alertHorariosMissas');
                if (alertElement) {
                    alertElement.classList.add('d-none');
                }
            }

            atualizarLabel();

            switchHorariosMissas.addEventListener('change', function() {
                if (!hasHorariosMissas && this.checked) {
                    this.checked = false;
                    exibirAlertHorariosMissas();
                } else if (!this.checked) {
                    ocultarAlertHorariosMissas();
                }
                atualizarLabel();
            });

            if (!hasHorariosMissas) {
                switchHorariosMissas.addEventListener('click', function(e) {
                    if (this.checked) {
                        e.preventDefault();
                        this.checked = false;
                        exibirAlertHorariosMissas();
                    } else {
                        ocultarAlertHorariosMissas();
                    }
                    atualizarLabel();
                });
            }
        }
    });
</script>
