<!--begin::Filtro de Documentos-->
@php
    $tiposDocumento = [
        'NF-e'          => 'NF-e',
        'NFC-e'         => 'NFC-e',
        'CUPOM'         => 'Cupom Fiscal',
        'BOLETO'        => 'Boleto',
        'RECIBO'        => 'Recibo',
        'FATURA_CARTAO' => 'Fatura de Cartão',
        'COMPROVANTE'   => 'Comprovante',
        'OUTRO'         => 'Outro',
    ];
@endphp
<div class="w-100">
    <label class="form-label fw-semibold fs-7 text-gray-600">
        <i class="fa-solid fa-filter fs-8 me-1"></i> Filtrar por tipo
    </label>
    <select class="form-select form-select-sm form-select-solid" id="documentTypeFilter">
        <option value="">Todos os tipos</option>
        @foreach($tiposDocumento as $valor => $label)
            <option value="{{ $valor }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
<!--end::Filtro de Documentos-->

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const documentTypeFilter = document.getElementById('documentTypeFilter');

        if (!documentTypeFilter) return;

        documentTypeFilter.addEventListener('change', function() {
            const selectedType = this.value;
            const instance = window.domusiaPendentesInstance;

            // Buscar documentos da instância ou do fallback global
            const allDocs = instance?.documentosCarregados
                || window.documentosCarregados
                || [];

            let filtered = allDocs;

            if (selectedType) {
                filtered = allDocs.filter(doc => doc.tipo_documento === selectedType);
            }

            // Renderizar documentos filtrados
            if (typeof window.renderPendingDocuments === 'function') {
                window.renderPendingDocuments(filtered);
            }

            // Atualizar miniaturas
            if (typeof window.renderThumbnails === 'function') {
                window.renderThumbnails(filtered);
            }
        });
    });
</script>
@endpush

