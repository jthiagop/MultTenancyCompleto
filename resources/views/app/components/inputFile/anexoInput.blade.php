<div class="col-md-12 ">
    <input type="file" name="files[]" id="photos" />
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
</div>
