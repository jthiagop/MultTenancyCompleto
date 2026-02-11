<!--begin::Card de Lançamentos Extraídos-->
<div class="card shadow-sm" id="extractedEntriesCard" style="display: none;">
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

    <!--begin::Card Body-->
    <div id="extractedEntriesList" class="card-body p-4">
        <!-- Skeleton Loading para extração -->
        <div id="extractedEntriesSkeleton" style="display: none;">
            @for($i = 0; $i < 3; $i++)
            <div class="card mb-4 border border-dashed border-gray-300">
                <div class="card-body p-0">
                    <div class="d-flex align-items-stretch">
                        <div class="w-4px bg-gray-300 rounded-start"></div>
                        <div class="p-4 ps-6 d-flex align-items-center">
                            <div class="skeleton-pulse rounded w-20px h-20px"></div>
                        </div>
                        <div class="flex-grow-1 p-4 d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="skeleton-pulse rounded w-200px h-20px mb-2"></div>
                                    <div class="skeleton-pulse rounded w-120px h-15px"></div>
                                </div>
                                <div class="skeleton-pulse rounded w-100px h-30px"></div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="skeleton-pulse rounded w-80px h-20px"></div>
                                <div class="skeleton-pulse rounded w-80px h-20px"></div>
                            </div>
                        </div>
                        <div class="p-4 d-flex align-items-center gap-2">
                            <div class="skeleton-pulse rounded w-90px h-30px"></div>
                            <div class="skeleton-pulse rounded w-30px h-30px"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
        <!-- Lançamentos serão inseridos aqui via JavaScript -->
    </div>
    <!--end::Card Body-->
</div>
<!--end::Card de Lançamentos Extraídos-->

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
            padding: 0.3rem 0.65rem !important;
            font-size: 0.8rem !important;
            border-radius: 0.3rem !important;
            line-height: 1.2 !important;
        }

        .valor-positivo {
            color: #50cd89 !important;
        }

        .valor-negativo {
            color: #f1416c !important;
        }

        /* Skeleton shimmer para extracted entries */
        .skeleton-pulse {
            background: linear-gradient(90deg, #e4e6ef 0%, #f0f0f3 50%, #e4e6ef 100%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s ease-in-out infinite;
        }

        @keyframes skeleton-shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
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
                const skeleton = document.getElementById('extractedEntriesSkeleton');

                if (!card || !list) return;

                card.style.display = 'block';

                // Mostrar skeleton enquanto carrega
                if (skeleton) {
                    skeleton.style.display = 'block';
                    // Esconder entries anteriores (se houver)
                    Array.from(list.children).forEach(child => {
                        if (child.id !== 'extractedEntriesSkeleton') child.style.display = 'none';
                    });
                }
                if (countBadge) countBadge.textContent = 'carregando...';

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
                        // Esconder skeleton
                        if (skeleton) skeleton.style.display = 'none';
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
                    if (skeleton) skeleton.style.display = 'none';
                    list.innerHTML = `
                        <div class="text-center py-6">
                            <i class="fa-solid fa-triangle-exclamation fs-3x text-danger mb-4 d-block"></i>
                            <h4 class="text-gray-700 fw-bold mb-2">Erro ao renderizar</h4>
                            <p class="text-gray-500 fs-6">${error.message}</p>
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
