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
            <div class="card mb-3 border border-dashed border-gray-300">
                <div class="card-body px-0 py-0">
                    <div class="d-flex align-items-center">
                        <div class="ps-5 pe-3 py-3">
                            <div class="skeleton-pulse rounded w-40px h-20px"></div>
                        </div>
                        <div class="flex-grow-1 py-3 px-3 d-flex flex-column gap-1">
                            <div class="d-flex gap-2">
                                <div class="skeleton-pulse rounded w-180px h-18px"></div>
                                <div class="skeleton-pulse rounded w-60px h-16px"></div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="skeleton-pulse rounded w-80px h-14px"></div>
                                <div class="skeleton-pulse rounded w-60px h-14px"></div>
                                <div class="skeleton-pulse rounded w-70px h-14px"></div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 pe-4 py-3">
                            <div class="skeleton-pulse rounded w-100px h-30px"></div>
                            <div class="skeleton-pulse rounded w-110px h-30px"></div>
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

        /* Toggle switch de tipo (entrada/saída) */
        .entry-type-toggle {
            width: 2.5rem !important;
            height: 1.25rem !important;
            cursor: pointer;
        }
        .entry-type-toggle:checked {
            background-color: #50cd89 !important;
            border-color: #50cd89 !important;
        }
        .entry-type-toggle:not(:checked) {
            background-color: #f1416c !important;
            border-color: #f1416c !important;
        }

        /* Truncar textos longos */
        .entry-ai-description {
            max-width: 500px;
        }
        .entry-fornecedor-name {
            max-width: 280px;
        }

        /* Transição suave ao trocar tipo */
        .entry-type-indicator,
        .entry-value-badge,
        .entry-type-label,
        .entry-create-btn {
            transition: all 0.25s ease;
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

            /**
             * Exibe lançamentos extraídos renderizados pelo servidor
             */
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
                        body: JSON.stringify({ extracted_data: extractedData })
                    });

                    const result = await response.json();

                    if (result.success) {
                        if (skeleton) skeleton.style.display = 'none';
                        list.innerHTML = result.html;
                        updateEntriesCount();
                        initTypeToggles();
                    } else {
                        throw new Error(result.message || 'Erro ao renderizar itens');
                    }
                } catch (error) {
                    if (skeleton) skeleton.style.display = 'none';
                    list.innerHTML = `
                        <div class="text-center py-6">
                            <i class="fa-solid fa-triangle-exclamation fs-3x text-danger mb-4 d-block"></i>
                            <h4 class="text-gray-700 fw-bold mb-2">Erro ao renderizar</h4>
                            <p class="text-gray-500 fs-6">${error.message}</p>
                        </div>
                    `;
                    if (countBadge) countBadge.textContent = '0 itens';
                }
            };

            /**
             * Atualiza o badge de contagem de itens
             */
            function updateEntriesCount() {
                const list = document.getElementById('extractedEntriesList');
                const countBadge = document.getElementById('extractedEntriesCount');
                if (!list || !countBadge) return;

                const itemCount = list.querySelectorAll('.extracted-entry-card').length;
                countBadge.textContent = `${itemCount} ${itemCount === 1 ? 'item' : 'itens'}`;
            }

            /**
             * Inicializa os toggles de tipo (entrada/saída) nos cards
             * Quando o usuário alterna, atualiza visualmente:
             * - Barra lateral (verde/vermelha)
             * - Badge de valor (+/-)
             * - Label do toggle (Entrada/Saída)
             * - Botão de criação (Receita/Despesa)
             * - data-entry-tipo no card
             */
            function initTypeToggles() {
                document.querySelectorAll('.entry-type-toggle').forEach(toggle => {
                    toggle.addEventListener('change', function() {
                        const card = this.closest('.extracted-entry-card');
                        if (!card) return;

                        const isEntrada = this.checked;
                        const tipo = isEntrada ? 'entrada' : 'saida';

                        // Atualiza data attribute
                        card.dataset.entryTipo = tipo;

                        // Barra lateral
                        const indicator = card.querySelector('.entry-type-indicator');
                        if (indicator) {
                            indicator.classList.remove('bg-success', 'bg-danger');
                            indicator.classList.add(isEntrada ? 'bg-success' : 'bg-danger');
                        }

                        // Label do toggle
                        const label = card.querySelector('.entry-type-label');
                        if (label) {
                            label.textContent = isEntrada ? 'Entrada' : 'Saída';
                            label.classList.remove('text-success', 'text-danger');
                            label.classList.add(isEntrada ? 'text-success' : 'text-danger');
                        }

                        // Tooltip do toggle
                        this.closest('[data-bs-toggle="tooltip"]')?.setAttribute(
                            'title', 
                            isEntrada ? 'Alternar: Receita → Despesa' : 'Alternar: Despesa → Receita'
                        );

                        // Badge de valor
                        const valueBadge = card.querySelector('.entry-value-badge');
                        if (valueBadge) {
                            valueBadge.classList.remove('badge-light-success', 'badge-light-danger');
                            valueBadge.classList.add(isEntrada ? 'badge-light-success' : 'badge-light-danger');
                        }
                        const valueSign = card.querySelector('.entry-value-sign');
                        if (valueSign) {
                            valueSign.textContent = isEntrada ? '+' : '-';
                        }

                        // Botão de criação
                        const createBtn = card.querySelector('.entry-create-btn');
                        if (createBtn) {
                            createBtn.classList.remove('btn-success', 'btn-danger');
                            createBtn.classList.add(isEntrada ? 'btn-success' : 'btn-danger');
                            createBtn.setAttribute('title', isEntrada ? 'Criar Receita' : 'Criar Despesa');
                        }
                        const createLabel = card.querySelector('.entry-create-label');
                        if (createLabel) {
                            createLabel.textContent = isEntrada ? 'Criar Receita' : 'Criar Despesa';
                        }
                    });
                });
            }
        });
    </script>
@endpush
