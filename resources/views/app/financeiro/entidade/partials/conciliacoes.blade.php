{{--
REFACTORED: Conciliações Blade Template com Abas Filtradas

✅ ARQUITETURA:
1. Componente conciliacao-pane.blade.php responsável pela renderização
2. Três abas com filtros automáticos por tipo
3. Event Delegation centralizado em arquivo JS separado
4. Replicação eficiente usando o mesmo componente

Benefícios:
- DRY: Reutilização de componente em 3 abas
- Performance: Filtros no frontend (dados já carregados)
- Maintainability: Alterações em um único arquivo
- Escalabilidade: Fácil adicionar novas abas
--}}

@php
    $tabs = [
        ['key' => 'all', 'label' => 'Todos', 'count' => $conciliacoesPendentes->count() ?? 0],
        ['key' => 'received', 'label' => 'Recebimentos', 'count' => $conciliacoesPendentes?->filter(fn($c) => $c->trntype === 'credit')->count() ?? 0],
        ['key' => 'paid', 'label' => 'Pagamentos', 'count' => $conciliacoesPendentes?->filter(fn($c) => $c->trntype === 'debit')->count() ?? 0],
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
@endpush

{{-- Include Modal de Filtro de Conciliações --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
