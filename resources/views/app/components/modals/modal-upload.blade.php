<!-- Modal de Upload de Arquivos -->
<div class="modal fade" id="kt_modal_upload_arquivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!--begin::Form-->
            <form class="form" id="kt_modal_upload_form" action="{{ route('modulosAnexos.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf <!-- Token CSRF do Laravel -->

                <input type="hidden" name="anexavel_id" value="{{ $caixa->id }}"> <!-- ID do lançamento -->
                <input type="hidden" name="anexavel_type" value="App\Models\Financeiro\TransacaoFinanceira"> <!-- Tipo do modelo -->

                <!--begin::Modal header-->
                <div class="modal-header">
                    <h2 class="fw-bold">Upload de Arquivos Banco</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <!-- Ícone de fechar -->
                        </span>
                    </div>
                </div>
                <!--end::Modal header-->

                <!--begin::Modal body-->
                <div class="modal-body pt-10 pb-15 px-lg-17">
                    <!-- Área do Dropzone -->
                    <div class="form-group">

                            <!-- Pré-visualização (template) -->
                                <input type="file" name="files[]" id="photos" />

                        <span class="form-text fs-6 text-muted">Tamanho máximo do arquivo: 5MB por arquivo.</span>
                    </div>
                    <div class="d-flex justify-content-end my-7">
                        <!--begin::Button-->
                        <button type="submit" id="kt_ecommerce_add_product_submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> <span class="indicator-label">Salvar </span>
                        </button>
                        <!--end::Button-->
                    </div>
                </div>
                <!--end::Modal body-->
                <!-- Se tiver outros campos, coloque-os aqui, por exemplo:
                    <input type="text" name="titulo" placeholder="Título do arquivo" />
                    -->
            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
<script>
    $("#photos").kendoUpload({
        async: {
            removeUrl: "{{ url('/remove') }}",
            removeField: "path",
            withCredentials: false
        },
        multiple: true, // Permite a seleção de múltiplos arquivos
        validation: {
            allowedExtensions: ["jpg", "jpeg", "png", "pdf", "page"], // Extensões permitidas
            maxFileSize: 5242880, // Tamanho máximo do arquivo (5 MB)
            minFileSize: 1024 // Tamanho mínimo do arquivo (1 KB)
        },
        localization: {
            uploadSuccess: "Upload bem-sucedido!",
            uploadFail: "Falha no upload",
            invalidFileExtension: "Tipo de arquivo não permitido",
            invalidMaxFileSize: "O arquivo é muito grande",
            invalidMinFileSize: "O arquivo é muito pequeno",
            select: "Anexar Arquivos"

        }
    });
</script>
