<!--begin::Área de Upload-->
<div class="card card-dashed document-upload-area" id="documentUploadArea">
    <div class="upload-zone" id="uploadZone" role="button" tabindex="0"
        aria-label="Clique ou arraste arquivos para importar documentos">
        {{-- Estado padrão --}}
        <div id="uploadZoneDefault" class="d-flex align-items-center gap-3">
            <i class="fa-solid fa-cloud-arrow-up fs-1 text-primary"></i>
            <span class="text-primary fw-bold">Clique aqui ou arraste arquivos para importar</span>
        </div>
        {{-- Estado dragover --}}
        <div id="uploadZoneDragover" class="d-flex align-items-center gap-3" style="display: none !important;">
            <i class="fa-solid fa-file-circle-plus fs-1 text-primary"></i>
            <span class="text-primary fw-bold">Solte os arquivos aqui</span>
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
        border: 2px dashed var(--bs-primary);
        border-radius: 0.475rem;
        padding: 1.5rem;
        text-align: center;
        background-color: var(--bs-body-bg);
        transition: all 0.2s ease;
        cursor: pointer;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        outline: none;
    }

    .upload-zone:hover,
    .upload-zone:focus-visible {
        background-color: var(--bs-gray-100);
    }

    .upload-zone:focus-visible {
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.3);
    }

    .upload-zone.dragover {
        border-color: var(--bs-primary);
        background-color: var(--bs-light-primary, #f1faff);
        transform: scale(1.01);
    }

    .upload-zone.dragover #uploadZoneDefault {
        display: none !important;
    }

    .upload-zone.dragover #uploadZoneDragover {
        display: flex !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const uploadZone = document.getElementById('uploadZone');
        const fileInput = document.getElementById('documentFileInput');

        if (!uploadZone || !fileInput) return;

        const ALLOWED_TYPES = ['application/pdf', 'image/png', 'image/jpeg', 'image/webp'];
        const MAX_FILE_SIZE = 10 * 1024 * 1024; // 10 MB
        let dragCounter = 0;

        // Clique na zona de upload
        uploadZone.addEventListener('click', (e) => {
            if (e.target.closest('button, input, .file-item')) return;
            fileInput.click();
        });

        // Acessibilidade: Enter/Space abre o seletor
        uploadZone.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
            }
        });

        // Drag and Drop (counter evita flickering no dragleave)
        uploadZone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            dragCounter++;
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        uploadZone.addEventListener('dragleave', () => {
            dragCounter--;
            if (dragCounter === 0) {
                uploadZone.classList.remove('dragover');
            }
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dragCounter = 0;
            uploadZone.classList.remove('dragover');

            const files = filterValidFiles(Array.from(e.dataTransfer.files));
            if (files.length > 0 && typeof window.handleFiles === 'function') {
                window.handleFiles(files);
            }
        });

        // Seleção de arquivos via input
        fileInput.addEventListener('change', (e) => {
            const files = Array.from(e.target.files);
            if (files.length > 0 && typeof window.handleFiles === 'function') {
                window.handleFiles(files);
            }
            e.target.value = '';
        });

        /**
         * Filtra arquivos inválidos no drop e notifica o usuário
         */
        function filterValidFiles(files) {
            const valid = [];
            const errors = [];

            files.forEach(file => {
                if (!ALLOWED_TYPES.includes(file.type)) {
                    errors.push(`"${file.name}" — tipo não permitido`);
                } else if (file.size > MAX_FILE_SIZE) {
                    errors.push(`"${file.name}" — excede 10 MB`);
                } else {
                    valid.push(file);
                }
            });

            if (errors.length > 0) {
                const swalFn = window.domusiaPendentesInstance?.showSwal || window.Swal?.fire;
                if (swalFn) {
                    swalFn({
                        icon: 'warning',
                        title: 'Arquivos ignorados',
                        html: errors.map(e => `<div class="text-start fs-7">${e}</div>`).join(''),
                    });
                }
            }

            return valid;
        }
    });
</script>
@endpush

