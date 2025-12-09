<!-- Modal -->
<div class="modal fade" id="modalConciliacao" tabindex="-1" aria-labelledby="modalConciliacaoLabel" aria-hidden="true">
    <!-- Modal -->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Cabeçalho -->
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title w-100 text-center" id="modalImportarOFXLabel">Importe seu extrato em formato OFX
                </h5>
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
                </div>

                <!-- Rodapé -->
                <div class="modal-footer d-flex justify-content-between align-items-center">
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
                        <label class="form-check form-switch form-check-custom form-check-solid">
                            <!--begin::Input-->
                            <input class="form-check-input" name="usar_horarios_missa" type="checkbox" value="1"
                                id="switchHorariosMissas" />
                            <!--end::Input-->
                            <!--begin::Label-->
                            <span class="form-check-label fw-semibold text-muted" id="labelHorariosMissas">Não</span>
                            <!--end::Label-->
                        </label>
                        <!--end::Switch-->
                    </div>
                    <!--end::Wrapper-->
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
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const switchHorariosMissas = document.getElementById('switchHorariosMissas');
        const labelHorariosMissas = document.getElementById('labelHorariosMissas');
        const hasHorariosMissas = @json(isset($hasHorariosMissas) && $hasHorariosMissas);

        if (switchHorariosMissas && labelHorariosMissas) {
            // Função para atualizar o label
            function atualizarLabel() {
                if (switchHorariosMissas.checked) {
                    labelHorariosMissas.textContent = 'Sim';
                } else {
                    labelHorariosMissas.textContent = 'Não';
                }
            }

            // Função para exibir toast de aviso usando a função específica do toasts.js
            function exibirToastAviso() {
                if (typeof window.showHorariosMissasToast === 'function') {
                    window.showHorariosMissasToast({
                        cadastrarUrl: '{{ route("company.edit", ["tab" => "horario-missas"]) }}',
                        delay: 15000,
                        icon: 'bi bi-exclamation-triangle'
                    });
                } else {
                    console.warn('showHorariosMissasToast não está disponível. Certifique-se de que toasts.js está carregado.');
                }
            }

            // Atualiza o label inicialmente
            atualizarLabel();

            // Event listener para mudanças no checkbox
            switchHorariosMissas.addEventListener('change', function() {
                // Se não houver horários e o usuário tentar marcar, desmarcar e mostrar toast
                if (!hasHorariosMissas && this.checked) {
                    this.checked = false;
                    exibirToastAviso();
                }
                atualizarLabel();
            });

            // Previne que o checkbox seja marcado se não houver horários
            if (!hasHorariosMissas) {
                switchHorariosMissas.addEventListener('click', function(e) {
                    if (this.checked) {
                        e.preventDefault();
                        this.checked = false;
                        exibirToastAviso();
                    }
                    atualizarLabel();
                });
            }
        }
    });
</script>
