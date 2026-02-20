{{-- resources/views/app/financeiro/entidade/partials/historico.blade.php --}}
{{-- Aba de Histórico de Conciliações com Abas por Status --}}
@php
    // Calcular totais iniciais para passar para as tabs
    $counts = $counts ?? [];
    // Garante que todos os estados tenham valor para evitar undefined
    $counts['ok'] = $counts['ok'] ?? 0;
    $counts['ignorado'] = $counts['ignorado'] ?? 0;
    $counts['divergente'] = $counts['divergente'] ?? 0;

    // Calcula total sem incluir pendentes (que não serão exibidos)
    $totalTodos = $counts['ok'] + $counts['ignorado'] + $counts['divergente'];
    // Se 'all' vier do backend, usa ele, senão usa a soma
    $totalTodos = $counts['all'] ?? $totalTodos;

    $abasStatus = [
        ['key' => 'all', 'label' => 'Todos', 'count' => $totalTodos],
        ['key' => 'ok', 'label' => 'Conciliados', 'count' => $counts['ok']],
        ['key' => 'ignorado', 'label' => 'Ignorados', 'count' => $counts['ignorado']],
        ['key' => 'divergente', 'label' => 'Divergentes', 'count' => $counts['divergente']],
    ];

    $periodLabel = \Carbon\Carbon::now()->translatedFormat('F \d\e Y');
@endphp



<x-tenant.segmented-tabs-toolbar
    id="historico-status-tabs"
    :tabs="$abasStatus"
    :active="request('status', 'all')"
    class="mb-5"
    filterId="historico-conciliacoes"
    :periodLabel="$periodLabel"
    :showPeriodFilter="true"
    :showAllPeriod="true"
>
    @slot('panes')
        {{-- Loop para criar os containers de cada status --}}
        @foreach($abasStatus as $aba)
            @php
                // O componente segmented-tabs-toolbar gera o paneId como: $id . '-pane-' . $key
                // Como $id = 'historico-status-tabs', o paneId será: historico-status-tabs-pane-{key}
                // Precisamos usar o mesmo padrão aqui para que os botões encontrem os panes corretos
                $paneId = 'historico-status-tabs-pane-' . $aba['key'];
            @endphp
            <div class="tab-pane fade {{ $aba['key'] === (request('status', 'all')) ? 'show active' : '' }}"
                 id="{{ $paneId }}"
                 role="tabpanel"
                 data-status="{{ $aba['key'] }}">

                {{-- Container para o conteúdo --}}
                <div id="historico-container-{{ $aba['key'] }}"
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
                                    <div class="d-flex align-items-center position-relative">
                                        <i class="bi bi-search fs-3 position-absolute ms-4"></i>
                                        <input type="text"
                                            class="form-control btn btn-sm form-control-solid w-250px ps-12 busca-historico-input"
                                            id="busca-historico-{{ $aba['key'] }}"
                                            data-status="{{ $aba['key'] }}"
                                            placeholder="Buscar..." />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body py-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-row-bordered fs-6 gy-3"
                                       id="kt_historico_conciliacoes_table_{{ $aba['key'] }}">
                                    <thead>
                                        <tr class="fw-semibold fs-6 text-gray-800">
                                            <th class="min-w-100px">Data</th>
                                            <th class="min-w-200px">Histórico</th>
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
                                    <tfoot>
                                        <tr class="fw-bold fs-6">
                                            <th colspan="3" class="text-nowrap align-end">Total:</th>
                                            <th class="text-end fs-5" id="historico-total-{{ $aba['key'] }}">R$ 0,00</th>
                                        </tr>
                                    </tfoot>
                                </table>

                                {{-- Paginação para todas as tabs --}}
                                <div class="d-flex justify-content-between align-items-center flex-wrap pt-5 historico-pagination-wrapper" data-status="{{ $aba['key'] }}">
                                    <div class="d-flex align-items-center">
                                        <select id="items-per-page-{{ $aba['key'] }}" class="form-select form-select-sm w-75px items-per-page-select" data-status="{{ $aba['key'] }}">
                                            <option value="10" selected>10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <ul class="pagination historico-pagination-list" id="historico-pagination-{{ $aba['key'] }}" data-status="{{ $aba['key'] }}"></ul>
                                </div>
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
