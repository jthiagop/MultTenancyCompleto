<!-- Modal de Upload de Arquivos -->
<div class="modal fade" id="kt_modal_upload_arquivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!--begin::Form-->
            <form class="form" id="kt_modal_upload_form" action="{{ route('anexos.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf <!-- Meta Tag para CSRF -->
                <meta name="csrf-token" content="{{ csrf_token() }}">
                <input type="hidden" name="caixa_id" id="caixa_id" value="{{ $caixa->id }}">
                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="fw-bold">Upload de Arquivos</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </div>
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body pt-10 pb-15 px-lg-17">
                    <!-- Dropzone HTML -->
                    <div class="form-group">
                        <div class="dropzone dropzone-queue mb-2" id="kt_modal_upload_dropzone">
                            <div class="dropzone-panel mb-4">
                                <a class="dropzone-select btn btn-sm btn-primary me-2">Anexar
                                    Arquivos</a>
                                <a class="dropzone-upload btn btn-sm btn-light-primary me-2">Upload</a>
                                <a class="dropzone-remove-all btn btn-sm btn-light-primary">Remover</a>
                            </div>

                            <!-- File Previews -->
                            <div class="dropzone-items wm-200px">
                                <div class="dropzone-item p-5" style="display:none">
                                    <div class="dropzone-file">
                                        <div class="dropzone-filename text-dark" title="some_image_file_name.jpg">
                                            <span data-dz-name="">some_image_file_name.jpg</span>
                                            <strong>(<span data-dz-size="">340kb</span>)</strong>
                                        </div>
                                        <div class="dropzone-error mt-0" data-dz-errormessage=""></div>
                                    </div>
                                    <div class="dropzone-progress">
                                        <div class="progress bg-light-primary">
                                            <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0"
                                                aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                        </div>
                                    </div>
                                    <div class="dropzone-toolbar">
                                        <span class="dropzone-start">
                                            <i class="bi bi-play-fill fs-3"></i>
                                        </span>
                                        <span class="dropzone-cancel" data-dz-remove="" style="display: none;">
                                            <i class="bi bi-x fs-3"></i>
                                        </span>
                                        <span class="dropzone-delete" data-dz-remove="">
                                            <i class="bi bi-x fs-1"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="form-text fs-6 text-muted">Tamanho máximo do arquivo:
                            1MB por arquivo.</span>
                    </div>
                </div>
                <!--end::Modal body-->
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
