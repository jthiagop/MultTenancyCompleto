{{--
REFACTORED: Conciliações Blade Template com Abas Filtradas via AJAX

✅ ARQUITETURA:
1. Filtragem server-side com amount_cents (sem erros de rounding)
2. AJAX para carregar tabs dinamicamente (sem reload de página)
3. Componente conciliacao-pane.blade.php renderização
4. Contadores dinâmicos baseados em tipo de transação

Benefícios:
- ✅ Sem reload de página ao trocar tabs
- ✅ Carrega apenas tab ativa (performance)
- ✅ amount_cents > 0 = Recebimento (entrada/credit)
- ✅ amount_cents < 0=Pagamento (saída/debit) 
- ✅ SEM truncamento ou rounding errors --}} 

@php

// ✅ Usa contadores server-side calculados no Controller
$tabs = [
    ['key' => 'all', 'label' => 'Todos', 'count' => $counts['all'] ?? 0],
    ['key' => 'received', 'label' => 'Recebimentos', 'count' => $counts['received'] ?? 0],
    ['key' => 'paid', 'label' => 'Pagamentos', 'count' => $counts['paid'] ?? 0],
];
@endphp <div class="card mt-5">
    <x-tenant.segmented-tabs-toolbar :tabs="$tabs" :active="$tab ?? 'all'" id="conciliacao">
        <x-slot:actionsLeft>
            <button class="btn btn-sm btn-light-primary">Conciliar</button>
            <button class="btn btn-sm btn-light-primary">Editar</button>
            <button class="btn btn-sm btn-light-danger">Ignorar</button>
        </x-slot>

        <x-slot:actionsRight>
            <a href="{{ route('entidades.historico', $entidade->id) }}"
                class="btn btn-sm btn-light-success {{ ($activeTab ?? '') === 'historico' ? 'active' : '' }}">
                <i class="bi bi-clock-history me-1"></i>
                Histórico
            </a>
        </x-slot>

        <x-slot:panes>
            <!-- ABA: TODOS -->
            <div class="tab-pane fade {{ ($tab ?? 'all') === 'all' ? 'show active' : '' }}" id="conciliacao-pane-all"
                role="tabpanel" aria-labelledby="conciliacao-tab-all" data-tab-key="all">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- ABA: RECEBIMENTOS -->
            <div class="tab-pane fade {{ ($tab ?? 'all') === 'received' ? 'show active' : '' }}"
                id="conciliacao-pane-received" role="tabpanel" aria-labelledby="conciliacao-tab-received"
                data-tab-key="received">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>

            <!-- ABA: PAGAMENTOS -->
            <div class="tab-pane fade {{ ($tab ?? 'all') === 'paid' ? 'show active' : '' }}" id="conciliacao-pane-paid"
                role="tabpanel" aria-labelledby="conciliacao-tab-paid" data-tab-key="paid">
                <div class="text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            </div>
        </x-slot:panes>
    </x-tenant.segmented-tabs-toolbar>

</div>

@push('scripts')
    {{-- Carregar o handler de formulários UMA VEZ só --}}
    <script src="{{ url('/app/financeiro/entidade/conciliacoes-form-handler.js') }}"></script>

    <script>
        /**
         * ✅ AJAX Tab Loading - TODAS as tabs via AJAX
         * Carrega tabs dinamicamente sem reload de página
         * Incluindo a tab inicial
         */
        document.addEventListener('DOMContentLoaded', function() {
            const entidadeId = {{ $entidade->id }};
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            const loadedTabs = new Set();
            const activeTab = '{{ $tab ?? 'all' }}';

            /**
             * Inicializa o botão "Carregar Mais"
             */
            function initLoadMoreButton(tabKey, targetPane) {
                const loadMoreBtn = targetPane.querySelector('.btn-load-more');
                if (!loadMoreBtn) {
                    return;
                }

                console.log('🔘 Botão "Carregar Mais" encontrado para tab:', tabKey);

                // Remove listeners antigos (clone e replace)
                const newBtn = loadMoreBtn.cloneNode(true);
                loadMoreBtn.parentNode.replaceChild(newBtn, loadMoreBtn);

                newBtn.addEventListener('click', function() {
                    const nextPage = parseInt(this.dataset.nextPage);
                    console.log('📄 Carregando página:', nextPage);

                    // Mostrar loading no botão
                    const originalHtml = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Carregando...';

                    // Fazer requisição para próxima página
                    const url =
                        `{{ route('entidades.conciliacoes-tab', ['id' => $entidade->id]) }}?tab=${tabKey}&page=${nextPage}`;

                    fetch(url, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Processar HTML
                                const tempDiv = document.createElement('div');
                                tempDiv.innerHTML = data.html;

                                // Remover scripts e headers
                                tempDiv.querySelectorAll('script').forEach(s => s.remove());
                                const header = tempDiv.querySelector(
                                '.row.gx-5.gx-xl-10'); // Header duplicado
                                if (header && !header.hasAttribute('data-conciliacao-id')) {
                                    header.remove();
                                }

                                // Remover container do botão atual
                                const loadMoreContainer = targetPane.querySelector(
                                    '#load-more-container');
                                if (loadMoreContainer) loadMoreContainer.remove();

                                // Encontrar container e adicionar itens
                                const cardBody = targetPane.querySelector('.card-body') || targetPane;
                                const newItems = tempDiv.querySelectorAll(
                                    '.row.gx-5.gx-xl-10[data-conciliacao-id]');

                                console.log('➕ Adicionando', newItems.length, 'novos itens');
                                newItems.forEach(item => cardBody.appendChild(item));

                                // Adicionar novo botão se houver
                                const newLoadMore = tempDiv.querySelector('#load-more-container');
                                if (newLoadMore) {
                                    cardBody.appendChild(newLoadMore);
                                    initLoadMoreButton(tabKey, targetPane);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ Erro ao carregar mais:', error);
                            this.disabled = false;
                            this.innerHTML = originalHtml;
                        });
                });
            }

            /**
             * Função para carregar uma tab via AJAX
             */
            function loadTab(tabKey) {
                // Pega o pane correto baseado no ID padrão
                const targetPane = document.getElementById(`conciliacao-pane-${tabKey}`);

                if (!targetPane) {
                    console.error('❌ Tab pane não encontrado para:', tabKey);
                    return;
                }

                // Se já carregou, ignora
                if (targetPane.getAttribute('data-loaded') === 'true') {
                    return;
                }

                console.log('⬇️ Iniciando carregamento da tab:', tabKey);

                // Marca visualmente como carregando se estiver vazio
                if (!targetPane.innerHTML.trim() || targetPane.querySelector('.spinner-border')) {
                    targetPane.innerHTML = `
                            <div class="text-center p-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        `;
                }

                // Fazer AJAX request
                fetch(`{{ route('entidades.conciliacoes-tab', ['id' => $entidade->id]) }}?tab=${tabKey}`, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na requisição');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Marca como carregado para evitar reload
                            targetPane.setAttribute('data-loaded', 'true');

                            // Processar HTML
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.html;
                            tempDiv.querySelectorAll('script').forEach(s => s.remove());

                            targetPane.innerHTML = '';
                            targetPane.insertAdjacentHTML('beforeend', tempDiv.innerHTML);

                            // ✅ Inicializar Select2 nos selects que foram injetados
                            if (typeof window.initializeSelect2 !== 'undefined') {
                                window.initializeSelect2(targetPane);
                            }

                            initLoadMoreButton(tabKey, targetPane);
                        } else {
                            targetPane.innerHTML = `<div class="alert alert-danger m-5">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        targetPane.innerHTML =
                            `<div class="alert alert-danger m-5">Erro ao carregar dados</div>`;
                    });
            }

            // 1. Carregar tab inicial
            loadTab(activeTab);

            // 2. Listener para cliques nas tabs
            tabButtons.forEach(btn => {
                btn.addEventListener('shown.bs.tab', function(e) {
                    // Extrai a chave da tab do ID do target (#conciliacao-pane-XXX -> XXX)
                    const targetId = this.getAttribute('data-bs-target');
                    const tabKey = targetId.replace('#conciliacao-pane-', '');

                    console.log('🖱️ Mudança de tab:', tabKey);
                    loadTab(tabKey);
                });
            });
        });
    </script>
@endpush

{{-- Include Modal de Filtro de Conciliações --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
