{{-- 
    REFACTORED: Conciliações Blade Template
    
    ✅ SOLUÇÃO IMPLEMENTADA:
    1. JavaScript removido do @foreach (era executado N vezes)
    2. Formulários renderizados com Blade Components (sem renderFormFromJSON)
    3. Seletores relativos com data attributes (sem IDs desnecessários)
    4. Event Delegation centralizado em arquivo JS separado
    
    Benefícios:
    - Performance: ~N vezes mais rápido (sem loops de script)
    - Segurança: XSS mitigado com template engine
    - Maintainability: CSS classes centralizadas, sem strings em JS
    - SEO: HTML semântico, sem poluição de DOM
--}}

@php
    // Tab ativa (query string ou padrão 'all')
    $activeTab = request('tab', 'all');

    // Filtrar conciliações baseado na tab
    // Recebimentos: amount > 0 (positivo)
    // Pagamentos: amount < 0 (negativo)
    $conciliacoesTodas = $conciliacoesPendentes;
    $conciliacoesRecebimentos = $conciliacoesPendentes->filter(fn($c) => $c->amount > 0);
    $conciliacoesPagamentos = $conciliacoesPendentes->filter(fn($c) => $c->amount < 0);

    // Calcular counts reais
    $tabs = [
        ['key' => 'all', 'label' => 'Todos', 'count' => $conciliacoesTodas->count()],
        ['key' => 'received', 'label' => 'Recebimentos', 'count' => $conciliacoesRecebimentos->count()],
        ['key' => 'paid', 'label' => 'Pagamentos', 'count' => $conciliacoesPagamentos->count()],
    ];
@endphp

<div class="card mt-5">
    <x-tenant.segmented-tabs-toolbar :tabs="$tabs" :active="$activeTab" id="conciliacao">
        <x-slot:actionsLeft>
            <button class="btn btn-sm btn-primary">Conciliar</button>
            <button class="btn btn-sm btn-primary">Editar</button>
            <button class="btn btn-sm btn-danger">Ignorar</button>
        </x-slot>

        <x-slot:actionsRight>
            <button class="btn btn-sm btn-primary">Ordenar ↓</button>
        </x-slot>

        <x-slot:panes>
            <!-- Tab: Todos -->
            <div class="tab-pane fade {{ $activeTab === 'all' ? 'show active' : '' }}" 
                 id="conciliacao-pane-all" 
                 role="tabpanel"
                 aria-labelledby="conciliacao-tab-all">
                @include('app.financeiro.entidade.partials.conciliacoes-list', [
                    'conciliacoesPendentes' => $conciliacoesTodas,
                    'entidade' => $entidade,
                    'centrosAtivos' => $centrosAtivos,
                    'lps' => $lps,
                    'formasPagamento' => $formasPagamento,
                    'activeTab' => $activeTab
                ])
            </div>

            <!-- Tab: Recebimentos -->
            <div class="tab-pane fade {{ $activeTab === 'received' ? 'show active' : '' }}" 
                 id="conciliacao-pane-received" 
                 role="tabpanel"
                 aria-labelledby="conciliacao-tab-received">
                @include('app.financeiro.entidade.partials.conciliacoes-list', [
                    'conciliacoesPendentes' => $conciliacoesRecebimentos,
                    'entidade' => $entidade,
                    'centrosAtivos' => $centrosAtivos,
                    'lps' => $lps,
                    'formasPagamento' => $formasPagamento,
                    'activeTab' => $activeTab
                ])
            </div>

            <!-- Tab: Pagamentos -->
            <div class="tab-pane fade {{ $activeTab === 'paid' ? 'show active' : '' }}" 
                 id="conciliacao-pane-paid" 
                 role="tabpanel"
                 aria-labelledby="conciliacao-tab-paid">
                @include('app.financeiro.entidade.partials.conciliacoes-list', [
                    'conciliacoesPendentes' => $conciliacoesPagamentos,
                    'entidade' => $entidade,
                    'centrosAtivos' => $centrosAtivos,
                    'lps' => $lps,
                    'formasPagamento' => $formasPagamento,
                    'activeTab' => $activeTab
                ])
            </div>
        </x-slot>
    </x-tenant.segmented-tabs-toolbar>
</div>


@push('scripts')
    {{-- Carregar o handler de formulários UMA VEZ só --}}
    <script src="{{ url('/app/financeiro/entidade/conciliacoes-form-handler.js') }}"></script>
@endpush

{{-- Include Modal de Filtro de Conciliações --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
