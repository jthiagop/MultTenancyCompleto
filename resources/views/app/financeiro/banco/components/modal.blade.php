<!-- Modal -->
<div class="modal fade" id="modalConciliacao" tabindex="-1" aria-labelledby="modalConciliacaoLabel" aria-hidden="true">
    <!-- Modal -->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Cabeçalho -->
            <div class="modal-header border-0 pb-2">
                <h5 class="modal-title w-100 text-center" id="modalImportarOFXLabel">Importe seu extrato em formato OFX</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Fechar"></button>
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
                <div class="modal-footer border-0 pt-0">
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
            </form>
        </div>
    </div>
</div>
