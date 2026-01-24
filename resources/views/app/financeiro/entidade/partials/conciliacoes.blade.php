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
    $tabs = [
        ['key' => 'all', 'label' => 'Todos', 'count' => 68],
        ['key' => 'received', 'label' => 'Recebimentos', 'count' => 56],
        ['key' => 'paid', 'label' => 'Pagamentos', 'count' => 12],
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
            <div class="tab-pane fade show active" id="conciliacao-pane-all" role="tabpanel"
                aria-labelledby="conciliacao-tab-all">Todos os Registros de Conciliação</div>
            <div class="tab-pane fade" id="conciliacao-pane-received" role="tabpanel"
                aria-labelledby="conciliacao-tab-received">Todos os Registros de Recebimento da conciliação</div>
            <div class="tab-pane fade" id="conciliacao-pane-paid" role="tabpanel"
                aria-labelledby="conciliacao-tab-paid">Todos os Registros de Pagamento da conciliação</div>
        </x-slot>
    </x-tenant.segmented-tabs-toolbar>

    <div class="card-body">
        <!-- Header com logo do banco e Dominus -->
        <x-tenant.reconciliation-header :entidade="$entidade" />

        @if ($conciliacoesPendentes->isEmpty())
            <p class="text-muted">Nenhuma conciliação pendente encontrada.</p>
        @else
            <!-- Exemplo de exibição de transações -->
            @foreach ($conciliacoesPendentes as $conciliacao)
                @php
                    $sugestoes = $conciliacao->possiveisTransacoes ?? collect();
                    $transacaoSugerida = $sugestoes->first();
                @endphp

                <!--begin::Row-->
                <div class="row gx-5 gx-xl-10" data-conciliacao-id="{{ $conciliacao->id }}">
                    
                    <!-- Statement Card (Left: col-xxl-4) -->
                    <x-conciliacao.statement-card :conciliacao="$conciliacao" />

                    @if ($sugestoes->count() > 0)
                        <!-- Botão Conciliar (Center: col-xxl-2) -->
                        <div class="col-xxl-1 mb-5 mb-xl-10">
                            <div class="card card-flush h-xl-100">
                                <div class="card-body d-flex align-items-center justify-content-center h-100">
                                    <button class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                        type="button" 
                                        data-action="conciliar"
                                        data-conciliacao-id="{{ $conciliacao->id }}">
                                        <i class="bi bi-link-45deg fs-1 me-2"></i>
                                        <span>Conciliar 2</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sugestão de Conciliação (Right: col-xxl-6) -->
                        <div class="col-xxl-7 mb-5 mb-xl-10">
                            <div class="card card-flush h-xl-100">
                                <!-- View: Sugestão encontrada -->
                                <div class="suggestion-view" data-conciliacao-id="{{ $conciliacao->id }}">
                                    <div class="card card-flush border border-hover-primary py-4 p-7 rounded flex-row-fluid overflow-hidden mb-3 h-xl-100">
                                        <span style="background-color: #fff3cd; padding: 6px 12px; border-radius: 4px; border-left: 4px solid #ffc107; color: black;">
                                            ⚠️ <strong>Encontramos um lançamento que parece corresponder:</strong>
                                        </span>
                                        <hr>

                                        <div class="d-flex flex-stack pb-3">
                                            <div class="d-flex">
                                                <div>
                                                    <div class="d-flex align-items-center">
                                                        <span class="text-dark fw-bold text-hover-primary fs-5 me-4">
                                                            {{ $transacaoSugerida ? \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('d/m/Y') : 'N/A' }}
                                                        </span>
                                                    </div>
                                                    <span class="text-muted fw-semibold mb-3">
                                                        @if ($transacaoSugerida && $transacaoSugerida->tipo == 'entrada')
                                                            <span style="color: green;">Receita</span>
                                                        @elseif($transacaoSugerida && $transacaoSugerida->tipo == 'saida')
                                                            <span class="text-danger">Despesa</span>
                                                        @else
                                                            <span>Tipo não identificado</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex">
                                                <div class="text-end pb-3">
                                                    @if ($transacaoSugerida && $transacaoSugerida->tipo == 'entrada')
                                                        <span class="fw-bold fs-5" style="color: green;">R$ {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                    @elseif($transacaoSugerida && $transacaoSugerida->tipo == 'saida')
                                                        <span class="fw-bold fs-5 text-danger">R$ {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}</span>
                                                    @else
                                                        <span class="fw-bold fs-5 text-muted">R$ {{ number_format($conciliacao->amount, 2, ',', '.') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-0">
                                            <p class="text-gray-700 fw-semibold fs-6 mb-4">
                                                <strong>Descrição:</strong>
                                                {{ $transacaoSugerida->descricao ?? 'Nenhuma descrição disponível' }}
                                            </p>

                                            <div class="d-flex flex-stack">
                                                <div class="d-flex flex-column mw-200px">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="text-gray-700 fs-6 fw-semibold me-2">{{ $percentualConciliado }}%</span>
                                                        <span class="text-muted fs-8">Conciliação Bancária</span>
                                                    </div>
                                                    <div class="progress h-6px w-200px">
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: {{ $percentualConciliado }}%"
                                                            aria-valuenow="{{ $percentualConciliado }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-primary" data-action="toggle-edit" data-conciliacao-id="{{ $conciliacao->id }}">✏️ Editar</button>
                                                    <button type="button" class="btn btn-sm btn-warning">⛓️‍💥 Desvincular</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Form: Editar Sugestão -->
                                <div class="suggestion-edit d-none" data-conciliacao-id="{{ $conciliacao->id }}">
                                    <div class="card card-flush py-4 flex-row-fluid overflow-hidden p-5 border">
                                        <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-4 mb-10">
                                            <div class="d-flex flex-column pe-3">
                                                <span class="fs-6 fw-bold">⚠️ Atenção:</span>
                                                <span class="fs-6">Os dados do lançamento devem ser <strong>iguais</strong> aos da conciliação.</span>
                                            </div>
                                        </div>

                                        <form class="edit-suggestion-form row" data-conciliacao-id="{{ $conciliacao->id }}" method="POST"
                                            action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}">
                                            @csrf

                                            <input type="hidden" name="bank_statement_id" value="{{ $conciliacao->id }}">
                                            @if ($transacaoSugerida)
                                                <input type="hidden" name="transacao_financeira_id" value="{{ $transacaoSugerida->id }}">
                                                <input type="hidden" name="valor_conciliado" value="{{ $transacaoSugerida->valor }}">
                                            @endif

                                            <div class="col-md-6 mb-3">
                                                <label class="required form-label fw-semibold">Data</label>
                                                <input type="date" class="form-control" name="data_competencia"
                                                    value="{{ old('data_competencia', $transacaoSugerida ? \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="required form-label fw-semibold">Valor</label>
                                                <input type="text" class="form-control" name="valor"
                                                    value="{{ old('valor', $transacaoSugerida ? number_format($transacaoSugerida->valor, 2, ',', '.') : number_format($conciliacao->amount, 2, ',', '.')) }}">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="required form-label fw-semibold">Código</label>
                                                <input type="text" class="form-control" name="numero_documento"
                                                    value="{{ old('numero_documento', $conciliacao->checknum) }}">
                                            </div>

                                            <div class="col-md-8 mb-3">
                                                <label class="required form-label fw-semibold">Descrição</label>
                                                <input type="text" class="form-control" name="descricao"
                                                    value="{{ old('descricao', $transacaoSugerida->descricao ?? $conciliacao->memo) }}">
                                            </div>

                                            <div class="col-12 d-flex gap-2">
                                                <button type="submit" class="btn btn-sm btn-success">💾 Salvar</button>
                                                <button type="button" class="btn btn-sm btn-secondary" data-action="toggle-edit" data-conciliacao-id="{{ $conciliacao->id }}">❌ Cancelar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else
                        <!-- Sem Sugestão: Botão de Conciliar + Formulários -->
                        <div class="col-xxl-1 mb-5 mb-xl-10">
                            <div class="card card-flush h-xl-100">
                                <div class="card-body d-flex align-items-center justify-content-center h-100">
                                    <button class="btn btn-lg btn-primary px-5 py-2 d-flex align-items-center"
                                        type="button"
                                        data-action="conciliar-novo-lancamento"
                                        data-conciliacao-id="{{ $conciliacao->id }}">
                                        <i class="bi bi-link-45deg fs-1"></i>
                                        <span>Conciliar</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Formulários Tabulados -->
                        <div class="col-xxl-6 mb-5 mb-xl-10">
                            <div class="card card-flush h-xl-100">
                                <div class="card card-flush flex-row-fluid overflow-hidden h-xl-100">
                                    <!-- Abas -->
                                    <ul class="nav nav-tabs" data-conciliacao-id="{{ $conciliacao->id }}" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" data-bs-toggle="tab"
                                                data-bs-target="#novo-lancamento-{{ $conciliacao->id }}-pane"
                                                type="button" role="tab" aria-selected="true">
                                                Novo lançamento
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab"
                                                data-bs-target="#transferencia-{{ $conciliacao->id }}-pane"
                                                type="button" role="tab" aria-selected="false">
                                                Transferência
                                            </button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" data-bs-toggle="tab"
                                                data-bs-target="#buscar-criar-{{ $conciliacao->id }}-pane"
                                                type="button" role="tab" aria-selected="false">
                                                Buscar/Criar vários
                                            </button>
                                        </li>
                                    </ul>

                                    <!-- Conteúdo das Abas -->
                                    <div class="tab-content py-4 px-5 border">
                                        <!-- Novo Lançamento -->
                                        <div class="tab-pane fade show active" id="novo-lancamento-{{ $conciliacao->id }}-pane" role="tabpanel">
                                            <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-5 mb-10">
                                                <div class="d-flex flex-column">
                                                    <span class="fs-6 fw-bold">Lançamento não encontrado automaticamente:</span>
                                                    <span class="fs-6">Crie um novo ao alimentar o formulário e clicando no botão conciliar.</span>
                                                </div>
                                                <button type="button" class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto" data-bs-dismiss="alert"></button>
                                            </div>

                                            <x-conciliacao.novo-lancamento-form 
                                                :conciliacao="$conciliacao"
                                                :transacaoSugerida="$transacaoSugerida"
                                                :centrosAtivos="$centrosAtivos"
                                                :lps="$lps"
                                                :formasPagamento="$formasPagamento"
                                                :entidade="$entidade" />
                                        </div>

                                        <!-- Transferência -->
                                        <div class="tab-pane fade" id="transferencia-{{ $conciliacao->id }}-pane" role="tabpanel">
                                            <x-conciliacao.transferencia-form 
                                                :conciliacao="$conciliacao"
                                                :entidade="$entidade"
                                                :centrosAtivos="$centrosAtivos"
                                                :lps="$lps" />
                                        </div>

                                        <!-- Buscar/Criar Vários -->
                                        <div class="tab-pane fade" id="buscar-criar-{{ $conciliacao->id }}-pane" role="tabpanel">
                                            <p>Formulário para buscar/criar múltiplos lançamentos...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="separator separator-dashed border-muted my-5"></div>
                </div>
                <!--end::Row-->
            @endforeach

            <!-- Botões de paginação -->
            {{ $conciliacoesPendentes->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>

@push('scripts')
    {{-- Carregar o handler de formulários UMA VEZ só --}}
    <script src="{{ url('/app/financeiro/entidade/conciliacoes-form-handler.js') }}"></script>
@endpush

@push('styles')
    {{-- Estilos para validação de formulário --}}
    <link rel="stylesheet" href="{{ url('/app/financeiro/entidade/form-validation.css') }}">
@endpush

{{-- Include Modal de Filtro de Conciliações --}}
@include('app.components.modals.financeiro.conciliacao.modal_conciliacao_bancaria')
