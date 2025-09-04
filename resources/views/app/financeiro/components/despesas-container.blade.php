<!--begin::Despesas Container-->
<div class="tab-pane fade" id="containerDespesas" role="tabpanel" aria-labelledby="navDespesas">
    <div class="container py-4">
        <!--begin::Sub Tabs-->
        <ul class="nav nav-tabs nav-fill" id="myTabDespesas" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="despesasAberto-tab" data-bs-toggle="tab"
                    data-bs-target="#despesasAberto" type="button" role="tab"
                    aria-controls="despesasAberto" aria-selected="true">
                    <div class="text-muted small">Despesas em Aberto (R$)</div>
                    <div class="fw-bold text-danger fs-5">R$
                        {{ number_format($valorDespesaTotal, 2, ',', '.') }}</div>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="despesasRealizadas-tab" data-bs-toggle="tab"
                    data-bs-target="#despesasRealizadas" type="button" role="tab"
                    aria-controls="despesasRealizadas" aria-selected="false">
                    <div class="text-muted small">Despesas Realizadas (R$)</div>
                    <div class="fw-bold text-danger fs-5">R$
                        {{ number_format($valorDespesasRealizadas ?? 0, 2, ',', '.') }}</div>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="totalPeriodo-tab-despesas" data-bs-toggle="tab"
                    data-bs-target="#totalPeriodo" type="button" role="tab"
                    aria-controls="totalPeriodo" aria-selected="false">
                    <div class="text-muted small">Total do Período (R$)</div>
                    <div class="fw-bold text-success fs-5">R$
                        {{ number_format($valorTotal - $valorDespesaTotal, 2, ',', '.') }}</div>
                </button>
            </li>
        </ul>
        <!--end::Sub Tabs-->
        <!--begin::Sub Tab Content-->
        <div class="tab-content" id="myTabDespesasContent">
            <!--begin::Despesas em Aberto-->
            <div class="tab-pane fade show active" id="despesasAberto" role="tabpanel"
                aria-labelledby="despesasAberto-tab">
                <div class="p-3">
                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                        id="despesasAbertoTable" aria-labelledby="despesasAberto-tab">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-120px">Data de Vencimento</th>
                                <th class="min-w-150px">Descrição</th>
                                <th class="min-w-100px">Situação</th>
                                <th class="min-w-100px">Valor</th>
                                <th class="min-w-100px">Fornecedor</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @forelse($despesasEmAberto as $despesa)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($despesa->data_primeiro_vencimento)->format('d M Y') }}
                                    </td>
                                    <td>{{ $despesa->descricao }}</td>
                                    <td>
                                        <div class="badge badge-light-warning">
                                            {{ ucfirst($despesa->status_pagamento) }}</div>
                                    </td>
                                    <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                                    <td>{{ $despesa->fornecedor->nome ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhuma despesa em aberto encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Despesas em Aberto-->
            <!--begin::Despesas Realizadas-->
            <div class="tab-pane fade" id="despesasRealizadas" role="tabpanel"
                aria-labelledby="despesasRealizadas-tab">
                <div class="p-3">
                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                        id="despesasRealizadasTable" aria-labelledby="despesasRealizadas-tab">
                        <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-120px">Data de Vencimento</th>
                                <th class="min-w-150px">Descrição</th>
                                <th class="min-w-100px">Situação</th>
                                <th class="min-w-100px">Valor</th>
                                <th class="min-w-100px">Fornecedor</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @forelse($despesasRealizadas ?? [] as $despesa)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($despesa->data_primeiro_vencimento)->format('d M Y') }}
                                    </td>
                                    <td>{{ $despesa->descricao }}</td>
                                    <td>
                                        <div class="badge badge-light-success">
                                            {{ ucfirst($despesa->status_pagamento) }}</div>
                                    </td>
                                    <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>
                                    <td>{{ $despesa->fornecedor->nome ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhuma despesa realizada encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Despesas Realizadas-->
            <!--begin::Total do Período-->
            <div class="tab-pane fade" id="totalPeriodo" role="tabpanel"
                aria-labelledby="totalPeriodo-tab-despesas">
                <div class="p-3">
                    <h5>Total do Período (R$)</h5>
                    <p>Resumo financeiro do período selecionado.</p>
                    <!-- Adicione conteúdo dinâmico aqui, como um gráfico ou tabela de resumo -->
                </div>
            </div>
            <!--end::Total do Período-->
        </div>
        <!--end::Sub Tab Content-->
    </div>
</div>
<!--end::Despesas Container-->
