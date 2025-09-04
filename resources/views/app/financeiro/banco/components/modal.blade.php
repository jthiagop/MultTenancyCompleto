<!-- Modal -->
<div class="modal fade" id="modalConciliacao" tabindex="-1" aria-labelledby="modalConciliacaoLabel" aria-hidden="true">
    <!-- Modal -->
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Cabeçalho -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportarOFXLabel">Importe seu extrato em formato OFX</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="uploadForm" action="{{ route('upload.ofx') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Corpo -->
                <div class="modal-body">
                    <p><strong>Importe um arquivo OFX para sua conta.</strong></p>
                    <p>1. Acesse o site do seu banco e exporte seu extrato no formato OFX.</p>
                    <p>2. Após salvar o arquivo no seu computador, você poderá importá-lo para o sistema.</p>

                    <!-- Área de Upload -->
                    <div id="drop-area" class="border border-dashed rounded p-4 text-center">
                        <input type="file" id="fileInput" class="d-none" accept=".ofx" name="file" />
                        <label for="fileInput" class="btn btn-outline-primary">
                            📎 Escolha um arquivo
                        </label>
                        Ou arraste-o para este espaço
                        <p id="fileName" class="text-muted"></p>
                    </div>
                </div>

                <!-- Rodapé -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="importButton" disabled>Importar
                        Extrato</button>
                </div>
            </form>

        </div>
    </div>
</div>
