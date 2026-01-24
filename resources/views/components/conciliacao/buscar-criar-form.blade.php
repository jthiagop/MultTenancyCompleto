@props([
    'conciliacao',
    'entidade' => null,
    'centrosAtivos' => [],
    'lps' => [],
    'formasPagamento' => [],
])

<div class="row g-3">
    <!-- Filtros de Busca -->
    <div class="col-12">
        <div class="alert alert-dismissible bg-light-info border border-info border-dashed d-flex flex-column flex-sm-row p-4 mb-5">
            <div class="d-flex flex-column">
                <span class="fs-6 fw-bold">ℹ️ Busca Avançada:</span>
                <span class="fs-6">Filtre lançamentos existentes para associar em lote ou crie vários novos de uma vez.</span>
            </div>
            <button type="button"
                class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto"
                data-bs-dismiss="alert"></button>
        </div>

        <!-- Filtros -->
        <div class="card card-flush mb-5">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-5">Filtros de Busca</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <form class="buscar-criar-filter-form" data-conciliacao-id="{{ $conciliacao->id }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Período (De)</label>
                            <input type="date" class="form-control" name="data_inicio" 
                                value="{{ old('data_inicio', now()->subMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Período (Até)</label>
                            <input type="date" class="form-control" name="data_fim" 
                                value="{{ old('data_fim', now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Centro de Custo</label>
                            <select class="form-select" name="cost_center_id">
                                <option value="">Todos</option>
                                @foreach($centrosAtivos as $centro)
                                    <option value="{{ $centro->id }}">{{ $centro->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tipo de Lançamento</label>
                            <select class="form-select" name="tipo">
                                <option value="">Todos</option>
                                <option value="entrada">Entrada</option>
                                <option value="saida">Saída</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <select class="form-select" name="situacao">
                                <option value="">Todos</option>
                                <option value="em_aberto">Em aberto</option>
                                <option value="pago">Pago/Recebido</option>
                                <option value="parcial">Parcial</option>
                                <option value="atrasado">Atrasado</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Descrição</label>
                            <input type="text" class="form-control" name="descricao" 
                                placeholder="Buscar por descrição...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Valor</label>
                            <input type="text" class="form-control" name="valor" 
                                placeholder="Valor aproximado...">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-5">
                        <button type="button" class="btn btn-sm btn-primary" data-action="buscar-lancamentos">
                            🔍 Buscar
                        </button>
                        <button type="reset" class="btn btn-sm btn-light">
                            🔄 Limpar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resultados da Busca -->
    <div class="col-12">
        <div id="busca-resultados-{{ $conciliacao->id }}" class="d-none">
            <div class="card card-flush">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-5">Resultados encontrados</span>
                        <span class="text-muted fw-semibold fs-7" id="resultado-count-{{ $conciliacao->id }}">0 lançamentos</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-striped table-row-dashed fs-6 gy-5" 
                            id="busca-resultados-table-{{ $conciliacao->id }}">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="w-10px pe-2">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                            <input class="form-check-input select-all-resultados" type="checkbox" 
                                                data-table-id="busca-resultados-table-{{ $conciliacao->id }}" />
                                        </div>
                                    </th>
                                    <th class="min-w-70px">Data</th>
                                    <th class="min-w-175px">Descrição</th>
                                    <th class="min-w-70px">Tipo</th>
                                    <th class="min-w-70px">Valor</th>
                                    <th class="min-w-70px">Status</th>
                                    <th class="text-end min-w-50px">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                <!-- Resultados serão carregados via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensagem quando não há resultados -->
        <div id="busca-vazia-{{ $conciliacao->id }}" class="text-center py-10">
            <div class="text-muted">
                <i class="bi bi-search fs-1 mb-3 d-block"></i>
                <span class="fs-6">Use os filtros acima e clique em "Buscar" para encontrar lançamentos</span>
            </div>
        </div>
    </div>

    <!-- Botões de Ação em Lote -->
    <div class="col-12 d-none" id="batch-actions-{{ $conciliacao->id }}">
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success" data-action="conciliar-lote">
                ✓ Conciliar selecionados
            </button>
            <button type="button" class="btn btn-sm btn-danger" data-action="desmarcar-lote">
                ✕ Desmarcar todos
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle batch actions visibility
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('resultado-checkbox') || 
            e.target.classList.contains('select-all-resultados')) {
            const conciliacaoId = '{{ $conciliacao->id }}';
            const table = document.getElementById('busca-resultados-table-' + conciliacaoId);
            const batchActions = document.getElementById('batch-actions-' + conciliacaoId);
            const checkedCount = table.querySelectorAll('.resultado-checkbox:checked').length;
            
            if (checkedCount > 0) {
                batchActions.classList.remove('d-none');
            } else {
                batchActions.classList.add('d-none');
            }
        }
    });

    // Select All Handler
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('select-all-resultados')) {
            const tableId = e.target.dataset.tableId;
            const table = document.getElementById(tableId);
            const isChecked = e.target.checked;
            
            table.querySelectorAll('.resultado-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });

            // Trigger change event to update batch actions
            table.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
});
</script>
@endpush
