{{--
REFACTORED: Conciliações Blade Template com Abas Filtradas Server-Side

✅ ARQUITETURA:
1. Filtragem server-side com amount_cents (sem erros de rounding)
2. Query params: ?tab=all|received|paid
3. Componente conciliacao-pane.blade.php renderização
4. Contadores dinâmicos baseados em tipo de transação

Benefícios:
- ✅ Filtragem precisa usando centavos (integers)
- ✅ amount_cents > 0 = Recebimento (entrada/credit)
- ✅ amount_cents < 0 = Pagamento (saída/debit)
- ✅ Paginação mantém query string
- ✅ SEM truncamento ou rounding errors
--}}

@php
    // ✅ Usa contadores server-side calculados no Controller
    $tabs = [
        ['key' => 'all', 'label' => 'Todos', 'count' => $counts['all'] ?? 0],
        ['key' => 'received', 'label' => 'Recebimentos', 'count' => $counts['received'] ?? 0],
        ['key' => 'paid', 'label' => 'Pagamentos', 'count' => $counts['paid'] ?? 0],
    ];
@endphp

<div class="card mt-5">
    <x-tenant.segmented-tabs-toolbar :tabs="$tabs" active="all" id="conciliacao">
        <x-slot:actionsLeft>
            <button class="btn btn-sm btn-primary">Conciliar</button>
            <button class="btn btn-sm btn-primary">Editar</button>
            <button class="btn btn-sm btn-danger">Ignorar</button>
            </x-slot>

            <x-slot:actionsRight>
                <button class="btn btn-sm btn-primary">Ordenar ↓</button>
                </x-slot>

                <x-slot:panes>
                    <!-- ABA: TODOS -->
                    <div class="tab-pane fade show active" id="conciliacao-pane-all" role="tabpanel"
                        aria-labelledby="conciliacao-tab-all">
                        <x-conciliacao.conciliacao-pane 
                            :entidade="$entidade"
                            :conciliacoesPendentes="$conciliacoesPendentes"
                            :tipo="null"
                            :centrosAtivos="$centrosAtivos"
                            :lps="$lps"
                            :formasPagamento="$formasPagamento"
                        />
                    </div>

                    <!-- ABA: RECEBIMENTOS -->
                    <div class="tab-pane fade" id="conciliacao-pane-received" role="tabpanel"
                        aria-labelledby="conciliacao-tab-received">
                        <x-conciliacao.conciliacao-pane 
                            :entidade="$entidade"
                            :conciliacoesPendentes="$conciliacoesPendentes"
                            :tipo="'entrada'"
                            :centrosAtivos="$centrosAtivos"
                            :lps="$lps"
                            :formasPagamento="$formasPagamento"
                        />
                    </div>

                    <!-- ABA: PAGAMENTOS -->
                    <div class="tab-pane fade" id="conciliacao-pane-paid" role="tabpanel"
                        aria-labelledby="conciliacao-tab-paid">
                        <x-conciliacao.conciliacao-pane 
                            :entidade="$entidade"
                            :conciliacoesPendentes="$conciliacoesPendentes"
                            :tipo="'saida'"
                            :centrosAtivos="$centrosAtivos"
                            :lps="$lps"
                            :formasPagamento="$formasPagamento"
                        />
                    </div>
                </x-slot:panes>
    </x-tenant.segmented-tabs-toolbar>

</div>

@push('scripts')
    {{-- Carregar o handler de formulários UMA VEZ só --}}
    <script src="{{ url('/app/financeiro/entidade/conciliacoes-form-handler.js') }}"></script>
    
    <script>
        /**
         * ✅ Handler para mudar de tab
         * Transforma cliques em query params: ?tab=received
         */
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
            
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const targetId = this.getAttribute('data-bs-target');
                    const tabKey = targetId?.replace('#conciliacao-pane-', '');
                    
                    if (tabKey) {
                        // Adiciona query param ?tab=received|paid|all
                        const url = new URL(window.location);
                        url.searchParams.set('tab', tabKey);
                        window.history.replaceState({}, '', url);
                    }
                });
            });
        });
    </script>
@endpush

{{-- Include Modal de Filtro de Conciliações --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
