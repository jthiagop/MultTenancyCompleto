<!--begin::Filtro de Documentos-->
    <label class="form-label fw-semibold">Filtrar por tipo de documento</label>
    <select class="form-select form-select-solid" id="documentTypeFilter">
        <option value="">Todos os tipos</option>
        <option value="NF-e">NF-e</option>
        <option value="NFC-e">NFC-e</option>
        <option value="BOLETO">Boleto</option>
        <option value="RECIBO">Recibo</option>
        <option value="OUTRO">Outro</option>
    </select>
<!--end::Filtro de Documentos-->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const documentTypeFilter = document.getElementById('documentTypeFilter');

        if (!documentTypeFilter) return;

        documentTypeFilter.addEventListener('change', function() {
            const selectedType = this.value;

            // Filtrar documentos carregados
            if (typeof window.documentosCarregados !== 'undefined') {
                let filtered = window.documentosCarregados;

                if (selectedType) {
                    filtered = window.documentosCarregados.filter(doc => {
                        return doc.tipo_documento === selectedType;
                    });
                }

                // Renderizar documentos filtrados
                if (typeof window.renderPendingDocuments === 'function') {
                    window.renderPendingDocuments(filtered);
                }

                // Atualizar miniaturas
                if (typeof window.renderThumbnails === 'function') {
                    window.renderThumbnails(filtered);
                }
            }
        });
    });
</script>
@endpush

