<!--begin::Área de Upload-->
<div class="document-upload-area" id="documentUploadArea">
    <div class="upload-zone" id="uploadZone">
        <div id="drop-area"
            class="border border-dashed border-primary rounded p-5 bg-light">
            <div class="row align-items-center">
                <div class="col-auto text-center">
                    <i class="fa-solid fa-cloud-arrow-up fs-1 text-primary"></i>
                </div>
                <div class="col">
                    <span class="text-primary fw-bold">Clique aqui ou arraste arquivos para
                        importar</span>
                </div>
            </div>
        </div>
        <input type="file" id="documentFileInput" accept=".pdf,.png,.jpg,.jpeg,.webp" multiple
            style="display: none;" />
    </div>
    <p class="text-center text-muted mt-3 mb-0 fs-7">
        Arquivos permitidos: PDF e imagens de até 10 MB.
    </p>
    <div class="separator separator-dashed my-3 mb-6"></div>
</div>
<!--end::Área de Upload-->

@push('styles')
<style>
    .document-upload-area {
        position: relative;
    }

    .upload-zone {
        border: 2px dashed #009ef7;
        border-radius: 0.475rem;
        padding: 1.5rem;
        text-align: center;
        background-color: #ffffff;
        transition: all 0.3s ease;
        cursor: pointer;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-zone:hover {
        background-color: #f8f9fa;
    }

    .upload-zone.dragover {
        border-color: #009ef7;
        background-color: #f1faff;
    }

    .upload-content {
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('documentFileInput');

        if (!uploadZone || !fileInput) return;

        // Clique na zona de upload
        uploadZone.addEventListener('click', (e) => {
            // Não abrir se clicar em botões, inputs ou elementos interativos
            if (e.target.closest('button') ||
                e.target.closest('input') ||
                e.target.closest('.file-item') ||
                e.target.tagName === 'BUTTON' ||
                e.target.tagName === 'INPUT') {
                return;
            }
            fileInput.click();
        });

        // Drag and Drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files);
            if (typeof window.handleFiles === 'function') {
                window.handleFiles(files);
            }
        });

        // Seleção de arquivos
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            if (files.length > 0) {
                if (typeof window.handleFiles === 'function') {
                    window.handleFiles(files);
                }
                // Limpar o input após processar para permitir selecionar o mesmo arquivo novamente
                e.target.value = '';
            }
        });
    });
</script>
@endpush

