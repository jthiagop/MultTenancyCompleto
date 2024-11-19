<!-- Modal de Upload de Arquivos -->
<div class="modal fade" id="kt_modal_upload_arquivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <form id="kt_modal_upload_form" method="POST" enctype="multipart/form-data">
                @csrf
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" id="caixa_id" value="{{ $caixa->id }}">

                <div class="modal-header">
                    <h2 class="fw-bold">Upload de Arquivos</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <i class="bi bi-x"></i>
                        </span>
                    </div>
                </div>

                <div class="modal-body pt-10 pb-15 px-lg-17">
                    <div class="dropzone dropzone-queue mb-2" id="kt_modal_upload_dropzone">
                        <div class="dropzone-panel mb-4">
                            <a class="dropzone-select btn btn-sm btn-primary me-2">Selecionar Arquivos</a>
                            <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Enviar Todos</a>
                            <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remover Todos</a>
                        </div>
                        <div class="dropzone-items wm-200px">
                            <div class="dropzone-item p-5" style="display:none">
                                <div class="dropzone-file">
                                    <span data-dz-name></span> (<span data-dz-size></span>)
                                </div>
                                <div class="dropzone-progress">
                                    <div class="progress bg-light-primary">
                                        <div class="progress-bar bg-primary" role="progressbar" data-dz-uploadprogress></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="form-text fs-6 text-muted">Tamanho máximo por arquivo é de 2MB.</span>
                </div>
            </form>
        </div>
    </div>
</div>
