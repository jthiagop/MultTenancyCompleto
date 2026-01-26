{{-- resources/views/components/tenant/historico-conciliacoes-tabs.blade.php --}}
{{-- 
    Componente wrapper para exibir histórico de conciliações com abas por status
    
    Uso:
    <x-tenant.historico-conciliacoes-tabs :entidade="$entidade" :counts="$counts">
        @include('app.financeiro.entidade.partials.historico')
    </x-tenant.historico-conciliacoes-tabs>
--}}

@props([
    'entidade' => null,
    'counts' => [],
    'dadosIniciais' => [], // Dados da tab 'ok' para carregamento inicial
])

@php
    // Tabs de status de conciliação com cores automáticas
    $statusTabs = [
        ['key' => 'all', 'label' => 'Todos', 'count' => ($counts['ok'] ?? 0) + ($counts['pendente'] ?? 0) + ($counts['ignorado'] ?? 0) + ($counts['divergente'] ?? 0)],
        ['key' => 'ok', 'label' => 'Conciliados', 'count' => $counts['ok'] ?? 0],
        ['key' => 'pendente', 'label' => 'Pendentes', 'count' => $counts['pendente'] ?? 0],
        ['key' => 'ignorado', 'label' => 'Ignorados', 'count' => $counts['ignorado'] ?? 0],
        ['key' => 'divergente', 'label' => 'Divergentes', 'count' => $counts['divergente'] ?? 0],
    ];
@endphp

<x-tenant.segmented-tabs-toolbar
    id="conciliacao-status"
    :tabs="$statusTabs"
    active="all"
>
    @slot('panes')
        <!-- ABA: TODOS -->
        <div class="tab-pane fade show active" id="conciliacao-status-pane-all" role="tabpanel"
            aria-labelledby="conciliacao-status-tab-all" data-status="all">
            <div id="conciliacoes-status-all" data-entidade-id="{{ $entidade?->id }}" data-status="all">
                {{ $slot }}
            </div>
        </div>

        <!-- ABA: CONCILIADOS (OK) -->
        <div class="tab-pane fade" id="conciliacao-status-pane-ok" role="tabpanel"
            aria-labelledby="conciliacao-status-tab-ok" data-status="ok">
            <div id="conciliacoes-status-ok" data-entidade-id="{{ $entidade?->id }}" data-status="ok"
                class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Carregando histórico de conciliados...</p>
            </div>
        </div>

        <!-- ABA: PENDENTES -->
        <div class="tab-pane fade" id="conciliacao-status-pane-pendente" role="tabpanel"
            aria-labelledby="conciliacao-status-tab-pendente" data-status="pendente">
            <div id="conciliacoes-status-pendente" data-entidade-id="{{ $entidade?->id }}" data-status="pendente"
                class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Carregando histórico de pendentes...</p>
            </div>
        </div>

        <!-- ABA: IGNORADOS -->
        <div class="tab-pane fade" id="conciliacao-status-pane-ignorado" role="tabpanel"
            aria-labelledby="conciliacao-status-tab-ignorado" data-status="ignorado">
            <div id="conciliacoes-status-ignorado" data-entidade-id="{{ $entidade?->id }}" data-status="ignorado"
                class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Carregando histórico de ignorados...</p>
            </div>
        </div>

        <!-- ABA: DIVERGENTES -->
        <div class="tab-pane fade" id="conciliacao-status-pane-divergente" role="tabpanel"
            aria-labelledby="conciliacao-status-tab-divergente" data-status="divergente">
            <div id="conciliacoes-status-divergente" data-entidade-id="{{ $entidade?->id }}" data-status="divergente"
                class="p-5 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Carregando histórico de divergentes...</p>
            </div>
        </div>
    @endslot
</x-tenant.segmented-tabs-toolbar>

@push('scripts')
    <script>
        /**
         * 📑 Sistema de Abas de Status do Histórico de Conciliações
         * Carrega dinamicamente o conteúdo de cada tab via AJAX
         */
        document.addEventListener('DOMContentLoaded', function() {
            const shell = document.querySelector('[id^="conciliacao-status-pane"]')?.closest('[id="conciliacao-status"]');
            if (!shell) return;

            const entidadeId = document.querySelector('[data-entidade-id]')?.getAttribute('data-entidade-id');
            const statusTabs = ['all', 'ok', 'pendente', 'ignorado', 'divergente'];
            const loadedTabs = new Set(['all']); // Tab 'all' já carrega com conteúdo

            // URL base para requisições AJAX
            const baseUrl = `{{ route('entidades.historico-conciliacoes', ':id') }}`.replace(':id', entidadeId);

            /**
             * Carrega o histórico de um status específico via AJAX
             */
            function loadStatusTab(status) {
                if (loadedTabs.has(status)) {
                    console.log(`⏭️ Tab "${status}" já carregada, pulando...`);
                    return;
                }

                const container = document.querySelector(`#conciliacoes-status-${status}`);
                if (!container) {
                    console.error(`❌ Container não encontrado para status: ${status}`);
                    return;
                }

                console.log(`📑 Carregando tab de status: ${status}`);

                // Construir URL com parâmetro de status
                const url = new URL(baseUrl, window.location.origin);
                url.searchParams.append('status', status);

                console.log(`🌐 Fazendo requisição para: ${url.toString()}`);

                fetch(url.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log(`📊 Response status: ${response.status}`);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(`✅ Dados recebidos para "${status}":`, {
                        success: data.success,
                        htmlLength: data.html?.length || 0,
                        total: data.meta?.total || 0,
                        counts: data.counts
                    });

                    if (data.success) {
                        // Renderiza a tabela dentro de um card
                        const html = `
                            <div class="card card-flush">
                                <div class="card-body py-4">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-row-bordered fs-6 gy-3">
                                            <thead>
                                                <tr class="fw-semibold fs-6 text-gray-800">
                                                    <th class="min-w-100px">Data Conciliação</th>
                                                    <th class="min-w-200px">Descrição</th>
                                                    <th class="min-w-50px">Tipo</th>
                                                    <th class="min-w-100px text-end">Valor</th>
                                                    <th class="min-w-100px">Status</th>
                                                    <th class="min-w-100px">Usuário</th>
                                                    <th class="min-w-100px text-end">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody class="text-gray-600 fw-semibold">
                                                ${data.html || '<tr><td colspan="7" class="text-center text-muted">Nenhum registro encontrado</td></tr>'}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        container.innerHTML = html;
                        loadedTabs.add(status);
                        
                        // ✅ Atualizar contadores se recebidos
                        if (data.counts) {
                            console.log(`🔄 Atualizando contadores:`, data.counts);
                            window.atualizarContagemStatusTabs(data.counts);
                        }
                        
                        console.log(`✅ Tab "${status}" carregada com sucesso`);

                        // Re-inicializar listeners para os botões de detalhes
                        initializeDetailButtons();
                    } else {
                        console.error(`❌ Requisição falhou: ${data.message}`);
                        container.innerHTML = `<div class="alert alert-danger m-5">${data.message || 'Erro ao carregar dados'}</div>`;
                    }
                })
                .catch(error => {
                    console.error(`❌ Erro ao carregar tab ${status}:`, error);
                    container.innerHTML = `
                        <div class="alert alert-danger m-5">
                            <strong>Erro ao carregar histórico</strong>
                            <p class="mt-2 mb-0"><small>${error.message}</small></p>
                        </div>
                    `;
                });
            }

            /**
             * Inicializa listeners para botões de detalhes
             */
            function initializeDetailButtons() {
                document.querySelectorAll('button[data-id]').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const bankStatementId = this.getAttribute('data-id');
                        if (bankStatementId) {
                            console.log(`🔍 Abrindo detalhes do banco #${bankStatementId}`);
                            // Aqui você pode chamar a função de carregamento de detalhes
                            // if (typeof window.carregarDetalhesConciliacao === 'function') {
                            //     window.carregarDetalhesConciliacao(bankStatementId);
                            // }
                        }
                    });
                });
            }

            /**
             * Listener para mudança de tabs
             */
            statusTabs.forEach(status => {
                const tabButton = document.querySelector(`#conciliacao-status-tab-${status}`);
                if (tabButton) {
                    tabButton.addEventListener('shown.bs.tab', function() {
                        console.log(`🔄 Tab "${status}" ativada!`);
                        console.log(`   loadedTabs.has('${status}') = ${loadedTabs.has(status)}`);
                        console.log(`   baseUrl = ${baseUrl}`);
                        console.log(`   entidadeId = ${entidadeId}`);
                        loadStatusTab(status);
                    });
                }
            });

            /**
             * Atualizar contadores via funções globais (quando conciliar/desfazer)
             */
            window.atualizarContagemStatusTabs = function(newCounts) {
                console.log('📊 Atualizando contadores das abas de status:', newCounts);
                
                // ✅ Calcular total "all" como soma dos 4 status
                const allCount = (newCounts.ok || 0) + (newCounts.pendente || 0) + (newCounts.ignorado || 0) + (newCounts.divergente || 0);
                const countsToUpdate = {
                    ...newCounts,
                    all: allCount
                };
                
                statusTabs.forEach(status => {
                    const tabButton = document.querySelector(`#conciliacao-status-tab-${status}`);
                    if (!tabButton) {
                        console.warn(`⚠️ Botão da tab não encontrado: #conciliacao-status-tab-${status}`);
                        return;
                    }

                    const countElement = tabButton.querySelector('.segmented-tab-count');
                    if (countElement && countsToUpdate[status] !== undefined) {
                        const count = countsToUpdate[status];
                        
                        console.log(`  🎯 ${status}: ${count}`);
                        
                        // Anima a atualização
                        countElement.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
                        countElement.style.transform = 'scale(1.15) rotate(5deg)';
                        
                        setTimeout(() => {
                            countElement.textContent = count;
                            countElement.style.transform = 'scale(1) rotate(0deg)';
                        }, 150);
                    } else if (!countElement) {
                        console.warn(`⚠️ Elemento de contagem não encontrado na tab: ${status}`);
                    }
                });
                
                console.log(`✅ Contadores atualizados:`, countsToUpdate);
            };

            // Inicializar botões de detalhes para tab 'ok' que carrega imediatamente
            setTimeout(() => {
                initializeDetailButtons();
            }, 100);

            console.log('✅ Sistema de abas de status inicializado');
        });
    </script>
@endpush
