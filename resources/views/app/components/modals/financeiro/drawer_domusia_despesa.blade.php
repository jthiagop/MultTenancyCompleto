{{-- Drawer para criar lançamento de despesa a partir de documento Domusia --}}
<div id="domusia_expense_drawer" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="domusia-expense"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#domusia_expense_drawer_toggle" data-kt-drawer-close="#kt_drawer_example_advanced_close">
    {{-- Drawer header --}}
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header btn btn-sm ms-3">
                <h3 class="modal-title" id="modal_financeiro_title">Nova Despesa</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" cancelar="true"
                    aria-label="Close"></button>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body p-0 bg-light">
                    <div class="row g-5 g-xl-10 mb-5">
                        <!--begin::Col-->
                        <div class="col-xl-6">
                            <!--begin::List widget 1-->
                            <div class="card card-flush h-md-100 mb-5 mb-lg-10">
                                {{-- Coluna 1: Visualizador de Documento --}}
                                <div class="col-md-6 border-end">
                                    <div class="d-flex flex-column h-100">
                                        @include( 'app.financeiro.domusia.partials.components.document-viewer', [ 'showCard' => true, 'showControls' => true, 'viewerHeight' => '100%', 'viewerMaxHeight' => '100%' ] )
                                    </div>
                                </div>
                            </div>
                            <!--end::LIst widget 1-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-xl-6">
                            <!--begin::List widget 2-->
                            <div class="card card-flush h-md-100 mb-5 mb-lg-10">

                                {{-- Coluna 2: Formulário --}}
                                <div class="col-md-6 overflow-y-auto" style="max-height: calc(100vh - 150px);">
                                    <div class="p-6">
                                        <h5 class="mb-4 fw-bold text-gray-800">
                                            <i class="ki-outline ki-plus-square fs-2 me-2"></i>
                                            Dados do Lançamento
                                        </h5>
                                        <p class="text-muted">Formulário virá aqui na próxima etapa</p>
                                    </div>
                                </div>
                            </div>
                            <!--end::List widget 2-->
                        </div>
                        <!--end::Col-->
                    </div>
            </div>
            <!--end::Modal body-->

            <!--begin::Modal footer-->
            <div class="modal-footer btn btn-sm">
                <div class="text-center">
                    <button type="reset" data-kt-drawer-dismiss="true" cancelar="true"
                        class="btn btn-sm btn-light me-3">Cancelar</button>
                    <!-- Split dropup button -->
                    <div class="btn-group dropup">
                        <!-- Botão principal -->
                        <button type="submit" data-kt-drawer-dismiss="true" id="Dm_modal_financeiro_submit"
                            class="btn btn-sm btn-primary">
                            <span class="indicator-label">Enviar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>

                        </button>
                        <!-- Botão de dropup -->
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <!-- Opções do dropup -->
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-sm" href="#" id="Dm_modal_financeiro_clone">Salvar e
                                Clonar</a>
                            <a class="dropdown-item btn-sm" href="#" id="Dm_modal_financeiro_novo">Salvar e
                                Limpar</a>
                        </div>
                    </div>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>

<script>
    // Função para carregar documento no visualizador do drawer
    window.loadDocumentInDrawer = function(docData) {
        if (!docData) {
            console.warn('Nenhum documento fornecido para o drawer');
            // Mostrar empty state
            const pdfViewer = document.getElementById('drawer_pdfViewer');
            const imageViewer = document.getElementById('drawer_imageViewer');
            const emptyState = document.getElementById('drawer_emptyState');
            const documentViewer = document.getElementById('drawer_documentViewer');

            if (pdfViewer) pdfViewer.style.display = 'none';
            if (imageViewer) imageViewer.style.display = 'none';
            if (documentViewer) documentViewer.style.display = 'none';
            if (emptyState) {
                emptyState.style.display = 'block';
            }
            return;
        }

        const pdfViewer = document.getElementById('drawer_pdfViewer');
        const imageViewer = document.getElementById('drawer_imageViewer');
        const emptyState = document.getElementById('drawer_emptyState');
        const documentViewer = document.getElementById('drawer_documentViewer');

        // Esconder todos os visualizadores primeiro
        if (pdfViewer) pdfViewer.style.display = 'none';
        if (imageViewer) imageViewer.style.display = 'none';
        if (emptyState) emptyState.style.display = 'none';

        // Mostrar container do visualizador
        if (documentViewer) {
            documentViewer.style.display = 'flex';
        }

        // Verificar tipo de documento
        const isPdf = docData.mime_type === 'application/pdf';

        if (isPdf) {
            // Exibir PDF
            if (pdfViewer && docData.file_url) {
                const pdfUrl = docData.file_url + '#toolbar=1&navpanes=1&scrollbar=1';
                pdfViewer.src = pdfUrl;
                pdfViewer.style.display = 'block';
                console.log('PDF carregado no drawer:', pdfUrl);
            }
        } else {
            // Exibir Imagem
            if (imageViewer && docData.file_url) {
                const imageUrl = docData.file_url;
                imageViewer.src = imageUrl;
                imageViewer.style.display = 'block';
                console.log('Imagem carregada no drawer:', imageUrl);
            }
        }
    }
</script>
