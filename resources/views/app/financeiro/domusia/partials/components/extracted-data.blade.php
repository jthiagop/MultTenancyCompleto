<!--begin::Card de Lançamentos Extraídos-->
<div class="card shadow-sm " id="extractedEntriesCard" style="display: none;">
    <!--begin::Card Header-->
    <div class="card-header">
        <div class="card-title">
            <h3 class="fw-bold m-0">
                <i class="fa-solid fa-check-circle text-success me-2"></i>
                Lançamentos extraídos
            </h3>
        </div>
        <div class="card-toolbar">
            <span class="badge badge-light-success fs-7 fw-bold" id="extractedEntriesCount">0 itens</span>
        </div>
    </div>
    <!--end::Card Header-->
</div>
<!--end::Card de Lançamentos Extraídos-->

    <!--begin::Card Body-->
    <div id="extractedEntriesList">
        <!-- Lançamentos serão inseridos aqui via JavaScript -->
    </div>
<!--end::Card Body-->

@push('styles')
    <style>
        .extracted-entry-card {
            transition: all 0.2s ease;
        }

        .extracted-entry-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .extracted-entry-card .card-header {
            background-color: #F5F8FA;
            /* Gray light background */
            min-height: 50px;
        }

        .extracted-entry-card .card-title {
            color: #181C32;
            /* text-gray-900 */
            font-weight: 700 !important;
            /* fw-bold */
            font-size: 1.15rem;
        }

        .btn-xs {
            padding: 0.35rem 0.75rem !important;
            font-size: 0.85rem !important;
            border-radius: 0.35rem !important;
            line-height: 1.2 !important;
        }

        .btn-xs {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            border-radius: 0.25rem !important;
            line-height: 1.2 !important;
        }

        .valor-positivo {
            color: #50cd89 !important;
        }

        .valor-negativo {
            color: #f1416c !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Exibir lançamentos extraídos - Usando Blade renderizado no servidor
            window.displayExtractedEntries = async function(extractedData) {
                const card = document.getElementById('extractedEntriesCard');
                const list = document.getElementById('extractedEntriesList');
                const countBadge = document.getElementById('extractedEntriesCount');

                if (!card || !list) return;

                card.style.display = 'block';

                try {
                    const response = await fetch('{{ route("domusia.render-entries") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            extracted_data: extractedData
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        list.innerHTML = result.html;

                        // Update count badge
                        const itemCount = list.querySelectorAll('.extracted-entry-card').length;
                        if (countBadge) {
                            countBadge.textContent = `${itemCount} ${itemCount === 1 ? 'item' : 'itens'}`;
                        }
                    } else {
                        throw new Error(result.message || 'Erro ao renderizar itens');
                    }
                } catch (error) {
                    console.error('Erro ao renderizar itens extraídos:', error);
                    list.innerHTML = `
                        <div class="text-center py-4 text-danger">
                            <i class="fa-solid fa-exclamation-triangle fs-3x mb-4"></i>
                            <h4 class="text-gray-600 fw-bold mb-2">Erro ao renderizar</h4>
                            <p class="text-muted fs-6">${error.message}</p>
                        </div>
                    `;
                    if (countBadge) {
                        countBadge.textContent = '0 itens';
                    }
                }
            };

            // As funções createTransaction e createExpense agora são definidas
            // no drawer_domusia_despesa.blade.php via DomusiaDrawer controller.
            // Aqui mantemos apenas as funções que não migraram.

            window.searchEntry = function(index) {
                console.log('Buscar lançamento para item:', index);
                // Implementar lógica de busca
            };

            window.removeEntry = function(index) {
                console.log('Remover item:', index);
                const entryRow = document.querySelector(`[data-entry-index="${index}"]`);
                if (entryRow) {
                    entryRow.remove();

                    // Update count badge
                    const list = document.getElementById('extractedEntriesList');
                    const countBadge = document.getElementById('extractedEntriesCount');
                    if (list && countBadge) {
                        const itemCount = list.querySelectorAll('.extracted-entry-card').length;
                        countBadge.textContent = `${itemCount} ${itemCount === 1 ? 'item' : 'itens'}`;
                    }
                }
            };
        });
    </script>
@endpush
