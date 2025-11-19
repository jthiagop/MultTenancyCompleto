// Aguarda o DOM carregar antes de tentar inicializar
$(document).ready(function() {
    // Verifica se o elemento existe e se o Kendo UI está carregado
    var uploadElement = $("#s");
    if (uploadElement.length > 0 && typeof kendo !== 'undefined' && typeof kendo.ui !== 'undefined') {
        uploadElement.kendoUpload({
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
        });
    } else {
        console.warn('Kendo Upload: Elemento #s não encontrado ou Kendo UI não está carregado');
    }
});

