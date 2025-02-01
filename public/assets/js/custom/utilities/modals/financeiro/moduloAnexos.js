// Aguarda o DOM carregar antes de tentar inicializar
$(document).ready(function() {
    $("#s").kendoUpload({
        async: {
            removeUrl: "/remove",  // ou "{{ url('/remove') }}"
            removeField: "path",
            withCredentials: false
        },
        multiple: true,
        validation: {
            allowedExtensions: ["jpg", "jpeg", "png", "pdf", "page"],
            maxFileSize: 5242880, // 5 MB
            minFileSize: 1024     // 1 KB
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
});

