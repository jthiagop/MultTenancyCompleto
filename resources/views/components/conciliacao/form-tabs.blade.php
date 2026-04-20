@props([
    'conciliacao',
    'lps',
    'centrosAtivos',
    'entidade',
    'transacaoSugerida' => null,
])

{{-- Alpine.js para gerenciar abas e botões --}}
<div x-data="{
    tab: 'novo',
    showForm: false,
    showEdit: false
}" class="col-xxl-7 mb-5 mb-xl-10">
    <div class="card card-flush h-xl-100">
        {{-- SEÇÃO: Sugestão de Conciliação --}}
        @if ($transacaoSugerida)
            <div class="card-body">
                <div x-show="!showForm" class="card card-flush border border-hover-primary py-4 p-7 rounded flex-row-fluid overflow-hidden mb-3 h-xl-100">
                    <span style="background-color: #fff3cd; padding: 6px 12px; border-radius: 4px; border-left: 4px solid #ffc107; color: black;">
                        ⚠️ <strong>Encontramos um lançamento que parece corresponder:</strong>
                    </span>
                    <hr>

                    <div class="d-flex flex-stack pb-3">
                        <div class="d-flex">
                            <div class="">
                                <div class="d-flex align-items-center">
                                    <a href="#" class="text-dark fw-bold text-hover-primary fs-5 me-4">
                                        {{ \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('d/m/Y') }}
                                    </a>
                                </div>
                                <span class="text-muted fw-semibold mb-3">
                                    @if ($transacaoSugerida->tipo == 'entrada')
                                        <span style="color: green;">Receita</span>
                                    @elseif($transacaoSugerida->tipo == 'saida')
                                        <span class="text-danger">Despesa</span>
                                    @else
                                        <span>Tipo não identificado</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="text-end pb-3">
                                @if ($transacaoSugerida->tipo == 'entrada')
                                    <span class="fw-bold fs-5" style="color: green;">
                                        R$ {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}
                                    </span>
                                @elseif($transacaoSugerida->tipo == 'saida')
                                    <span class="fw-bold fs-5 text-danger">
                                        R$ {{ number_format($transacaoSugerida->valor, 2, ',', '.') }}
                                    </span>
                                @else
                                    <span class="fw-bold fs-5 text-muted">
                                        R$ {{ number_format($conciliacao->amount, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="p-0">
                        <p class="text-gray-700 fw-semibold fs-6 mb-4">
                            <strong>Descrição:</strong>
                            {{ $transacaoSugerida->descricao ?? 'Nenhuma descrição disponível' }}
                        </p>

                        @php
                            $matchScore = $transacaoSugerida->match_score ?? 0;
                            $matchClassificacao = \App\Services\ConciliacaoMatchingService::classificarScore($matchScore);
                        @endphp
                        <div class="d-flex flex-stack">
                            <div class="d-flex flex-column mw-250px">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="text-gray-700 fs-6 fw-semibold me-2">{{ $matchScore }}%</span>
                                    <span class="badge badge-light-{{ $matchClassificacao['cor'] }} fs-8">{{ $matchClassificacao['texto'] }}</span>
                                </div>
                                <div class="progress h-6px w-200px">
                                    <div class="progress-bar bg-{{ $matchClassificacao['cor'] }}" role="progressbar" style="width: {{ $matchScore }}%"
                                        aria-valuenow="{{ $matchScore }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="button" @click="showForm = !showForm; showEdit = false" class="btn btn-sm btn-primary">
                                    ✏️ Editar
                                </button>
                                <a href="#" class="btn btn-sm btn-warning">
                                    ⛓️‍💥 Desvincular
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Formulário de Edição --}}
                <div x-show="showForm" class="card card-flush py-4 flex-row-fluid overflow-hidden p-5 border">
                    <div class="alert alert-dismissible bg-light-warning border border-warning border-dashed d-flex flex-column flex-sm-row p-4 mb-10">
                        <div class="d-flex flex-column pe-3">
                            <span class="svg-icon svg-icon-2hx svg-icon-warning me-4 mb-2">
                                ⚠️
                                <span class="fs-6 fw-bold">Atenção:</span>
                            </span>
                            <span class="fs-6">Os dados do lançamento devem ser <strong>iguais</strong> aos da conciliação.</span>
                        </div>
                    </div>

                    <form id="edit-form-{{ $conciliacao->id }}" action="{{ route('conciliacao.conciliar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="bank_statement_id" value="{{ $conciliacao->id }}">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="required form-label fw-semibold">Data</label>
                                <input type="date" name="data_competencia" class="form-control"
                                    value="{{ \Carbon\Carbon::parse($transacaoSugerida->data_competencia)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="required form-label fw-semibold">Valor</label>
                                <input type="text" class="form-control" value="{{ number_format($transacaoSugerida->valor, 2, ',', '.') }}" readonly>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="required form-label fw-semibold">Código</label>
                                <input type="text" name="numero_documento" class="form-control"
                                    value="{{ $conciliacao->checknum }}">
                            </div>
                            <div class="col-md-8">
                                <label class="required form-label fw-semibold">Descrição</label>
                                <input type="text" name="descricao" class="form-control"
                                    value="{{ $transacaoSugerida->descricao ?? $conciliacao->memo }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-success">💾 Salvar</button>
                            <button type="button" @click="showForm = false" class="btn btn-sm btn-secondary">❌ Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            {{-- SEÇÃO: Sem Sugestão - Abas para criar novo lançamento ou transferência --}}
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <button @click="tab = 'novo'" :class="{ 'active': tab === 'novo' }" type="button"
                            class="nav-link" role="tab" aria-selected="true">
                            Novo lançamento
                        </button>
                    </li>
                    <li class="nav-item">
                        <button @click="tab = 'transf'" :class="{ 'active': tab === 'transf' }" type="button"
                            class="nav-link" role="tab" aria-selected="false">
                            Transferência
                        </button>
                    </li>
                    <li class="nav-item">
                        <button @click="tab = 'multiplo'" :class="{ 'active': tab === 'multiplo' }" type="button"
                            class="nav-link" role="tab" aria-selected="false">
                            Buscar/Criar vários
                        </button>
                    </li>
                </ul>

                <div class="card card-flush py-4 flex-row-fluid overflow-hidden tab-content p-5 border">
                    {{-- ABA: Novo Lançamento --}}
                    <div x-show="tab === 'novo'" role="tabpanel">
                        <x-conciliacao.novo-lancamento-form :conciliacao="$conciliacao" :lps="$lps"
                            :centrosAtivos="$centrosAtivos" :entidade="$entidade" />
                    </div>

                    {{-- ABA: Transferência --}}
                    <div x-show="tab === 'transf'" role="tabpanel">
                        <x-conciliacao.form-transferencia :conciliacao="$conciliacao" :lps="$lps"
                            :centrosAtivos="$centrosAtivos" :entidade="$entidade" />
                    </div>

                    {{-- ABA: Buscar/Criar Vários --}}
                    <div x-show="tab === 'multiplo'" role="tabpanel">
                        <p>Formulário para buscar/criar múltiplos lançamentos...</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
