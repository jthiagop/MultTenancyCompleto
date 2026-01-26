{{-- resources/views/app/financeiro/entidade/partials/historico.blade.php --}}
{{-- Aba de Histórico de Conciliações com Abas por Status --}}
@php
    // Calcular totais iniciais para passar para as tabs
    $counts = $counts ?? []; 
    // Garante que todos os estados tenham valor para evitar undefined
    $counts['ok'] = $counts['ok'] ?? 0;
    $counts['pendente'] = $counts['pendente'] ?? 0;
    $counts['ignorado'] = $counts['ignorado'] ?? 0;
    $counts['divergente'] = $counts['divergente'] ?? 0;
    
    $totalTodos = $counts['ok'] + $counts['pendente'] + $counts['ignorado'] + $counts['divergente'];
    // Se 'all' vier do backend, usa ele, senão usa a soma
    $totalTodos = $counts['all'] ?? $totalTodos;

    $abasStatus = [
        ['key' => 'all', 'label' => 'Todos', 'count' => $totalTodos],
        ['key' => 'ok', 'label' => 'Conciliados', 'count' => $counts['ok']],
        ['key' => 'pendente', 'label' => 'Pendentes', 'count' => $counts['pendente']],
        ['key' => 'ignorado', 'label' => 'Ignorados', 'count' => $counts['ignorado']],
        ['key' => 'divergente', 'label' => 'Divergentes', 'count' => $counts['divergente']],
    ];
@endphp

<x-tenant.segmented-tabs-toolbar
    id="conciliacao-status"
    :tabs="$abasStatus"
    active="all"
    class="mb-5"
>
    @slot('panes')
        {{-- Loop para criar os containers de cada status --}}
        @foreach($abasStatus as $aba)
            <div class="tab-pane fade {{ $aba['key'] === 'all' ? 'show active' : '' }}" 
                 id="conciliacao-status-pane-{{ $aba['key'] }}" 
                 role="tabpanel"
                 data-status="{{ $aba['key'] }}">
                
                {{-- Container para o conteúdo --}}
                {{-- Nota: Para a tab 'all', mantemos o ID antigo para compatibilidade inicial do JS --}}
                <div id="{{ $aba['key'] === 'all' ? 'conciliacoes-historico' : 'conciliacoes-historico-' . $aba['key'] }}"
                     data-status="{{ $aba['key'] }}"
                     data-entidade-id='@json($entidade?->id)'
                     data-url-historico="{{ $entidade?->id ? route('entidades.historico-conciliacoes', $entidade->id) : '' }}"
                     data-url-detalhes="{{ route('conciliacao.detalhes', ':id') }}"
                     data-url-desfazer="{{ route('conciliacao.desfazer', ':id') }}"
                     class="mt-4">
                    
                    <div class="card card-flush">
                        <div class="card-header border-0 pt-5 separator">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Histórico de Conciliações</span>
                                @if($aba['key'] !== 'all')
                                    <span class="text-muted mt-1 fw-semibold fs-7 status-label-descricao">
                                        Filtro: {{ $aba['label'] }}
                                    </span>
                                @endif
                            </h3>
                            <div class="card-toolbar">
                                <div class="d-flex align-items-center gap-2">
                                    {{-- Campo de busca (apenas no All inicialmente para não duplicar IDs complexos) --}}
                                    @if($aba['key'] === 'all')
                                    <div class="d-flex align-items-center position-relative">
                                        <i class="bi bi-search fs-3 position-absolute ms-4"></i>
                                        <input type="text" id="busca-historico"
                                            class="form-control btn btn-sm form-control-solid w-250px ps-12"
                                            placeholder="Buscar..." />
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-row-bordered fs-6 gy-3" 
                                       id="kt_historico_conciliacoes_table_{{ $aba['key'] }}">
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
                                    {{-- IDs diferenciados para cada corpo de tabela --}}
                                    <tbody class="text-gray-600 fw-semibold" 
                                           id="{{ $aba['key'] === 'all' ? 'historico-conciliacoes-body' : 'historico-conciliacoes-body-' . $aba['key'] }}">
                                        {{-- Spinner inicial --}}
                                        <tr>
                                            <td colspan="7" class="text-center py-10">
                                                <div class="d-flex flex-column align-items-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Carregando...</span>
                                                    </div>
                                                    <span class="text-muted mt-3">Carregando histórico...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                
                                {{-- Paginação apenas no ALL por enquanto --}}
                                @if($aba['key'] === 'all')
                                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5">
                                    <div class="d-flex align-items-center">
                                        <select id="items-per-page" class="form-select form-select-sm w-75px">
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <ul class="pagination" id="historico-pagination"></ul>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endslot
</x-tenant.segmented-tabs-toolbar>

{{-- Drawer de detalhes --}}
@include('app.components.drawers.conciliacao_detalhes')

@once
    @push('scripts')
        {{-- Passa os contadores iniciais para o JS --}}
        <script>
            window.initialConciliacaoCounts = @json($counts);
        </script>
        @vite('resources/js/pages/conciliacoes/historico.js')
    @endpush
@endonce
