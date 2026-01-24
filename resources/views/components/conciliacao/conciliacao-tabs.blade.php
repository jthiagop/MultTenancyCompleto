@props([
    'conciliacao',
    'transacaoSugerida' => null,
    'centrosAtivos' => [],
    'lps' => [],
    'formasPagamento' => [],
    'entidade' => null,
])

<!-- Formulários Tabulados -->
<div class="col-xxl-6 mb-5 mb-xl-10">
    <div class="card card-flush h-xl-100">
        <div class="card card-flush flex-row-fluid overflow-hidden h-xl-100">
            <!-- Abas -->
            <ul class="nav nav-tabs" data-conciliacao-id="{{ $conciliacao->id }}"
                role="tablist">
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
                <div class="tab-pane fade show active"
                    id="novo-lancamento-{{ $conciliacao->id }}-pane" role="tabpanel">
                    <div
                        class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row p-5 mb-10">
                        <div class="d-flex flex-column">
                            <span class="fs-6 fw-bold">Lançamento não encontrado
                                automaticamente:</span>
                            <span class="fs-6">Crie um novo ao alimentar o formulário e
                                clicando no botão conciliar.</span>
                        </div>
                        <button type="button"
                            class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto"
                            data-bs-dismiss="alert"></button>
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
                <div class="tab-pane fade"
                    id="transferencia-{{ $conciliacao->id }}-pane" role="tabpanel">
                    <x-conciliacao.transferencia-form 
                        :conciliacao="$conciliacao"
                        :entidade="$entidade" 
                        :centrosAtivos="$centrosAtivos"
                        :lps="$lps" />
                </div>

                <!-- Buscar/Criar Vários -->
                <div class="tab-pane fade" id="buscar-criar-{{ $conciliacao->id }}-pane"
                    role="tabpanel">
                    <x-conciliacao.buscar-criar-form 
                        :conciliacao="$conciliacao"
                        :entidade="$entidade"
                        :centrosAtivos="$centrosAtivos"
                        :lps="$lps"
                        :formasPagamento="$formasPagamento" />
                </div>
            </div>
        </div>
    </div>
</div>
