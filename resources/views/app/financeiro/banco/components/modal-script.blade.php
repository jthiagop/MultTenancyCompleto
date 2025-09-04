<!-- Script -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let dropArea = document.getElementById("drop-area");
        let fileInput = document.getElementById("fileInput");
        let fileNameDisplay = document.getElementById("fileName");
        let importButton = document.getElementById("importButton");

        // Evento ao selecionar um arquivo
        fileInput.addEventListener("change", function() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = "📂 " + fileInput.files[0].name;
                importButton.removeAttribute("disabled");
            }
        });

        // Eventos de arrastar e soltar
        dropArea.addEventListener("dragover", function(event) {
            event.preventDefault();
            dropArea.style.backgroundColor = "#f8f9fa";
        });

        dropArea.addEventListener("dragleave", function() {
            dropArea.style.backgroundColor = "white";
        });

        dropArea.addEventListener("drop", function(event) {
            event.preventDefault();
            dropArea.style.backgroundColor = "white";
            let files = event.dataTransfer.files;
            if (files.length > 0 && files[0].type === "application/x-ofx") {
                fileInput.files = files;
                fileNameDisplay.textContent = "📂 " + files[0].name;
                importButton.removeAttribute("disabled");
            } else {
                alert("Por favor, selecione um arquivo OFX válido.");
            }
        });
    });
</script>
