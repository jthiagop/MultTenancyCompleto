<!--begin::Receitas Container-->
<div class="tab-pane fade show active" id="containerReceitas" role="tabpanel"
    aria-labelledby="navReceitas">
    <div class="container py-4">
        <!--begin::Sub Tabs-->
        <ul class="nav nav-tabs nav-fill" id="myTabReceitas" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="receitasAberto-tab" data-bs-toggle="tab"
                    data-bs-target="#receitasAberto" type="button" role="tab"
                    aria-controls="receitasAberto" aria-selected="true">
                    <div class="text-muted small">Receitas em Aberto (R$)</div>
                    <div class="fw-bold text-danger fs-5">R$
                        {{ number_format($valorTotal, 2, ',', '.') }}</div>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="receitasRealizadas-tab" data-bs-toggle="tab"
                    data-bs-target="#receitasRealizadas" type="button" role="tab"
                    aria-controls="receitasRealizadas" aria-selected="false">
                    <div class="text-muted small">Receitas a Vencer (R$)</div>
                    <div class="fw-bold text-primary fs-5">R$
                        {{ number_format($TotalreceitasAVencer, 2, ',', '.') }}</div>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="totalPeriodo-tab" data-bs-toggle="tab"
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
        <div class="tab-content" id="myTabReceitasContent">
            <!--begin::Receitas em Aberto-->
            <div class="tab-pane fade show active" id="receitasAberto" role="tabpanel"
                aria-labelledby="receitasAberto-tab">
                <div class="p-3">
                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                        id="receitasAbertoTable" aria-labelledby="receitasAberto-tab">
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
                            @forelse($receitasEmAberto as $receita)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($receita->data_primeiro_vencimento)->format('d M Y') }}
                                    </td>
                                    <td>{{ $receita->descricao }}</td>
                                    <td>
                                        <div class="badge badge-light-warning">
                                            {{ ucfirst($receita->status_pagamento) }}</div>
                                    </td>
                                    <td>R$ {{ number_format($receita->valor, 2, ',', '.') }}</td>
                                    <td>{{ $receita->fornecedor->nome ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhuma receita em aberto encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Receitas em Aberto-->
            <!--begin::Receitas Realizadas-->
            <div class="tab-pane fade" id="receitasRealizadas" role="tabpanel"
                aria-labelledby="receitasRealizadas-tab">
                <div class="p-3">
                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                        id="receitasRealizadasTable" aria-labelledby="receitasRealizadas-tab">
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
                            @forelse($receitasAVencer as $receita)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($receita->data_primeiro_vencimento)->format('d M Y') }}
                                    </td>
                                    <td>{{ $receita->descricao }}</td>
                                    <td>
                                        <div class="badge badge-light-warning">
                                            {{ ucfirst($receita->status_pagamento) }}</div>
                                    </td>
                                    <td>R$ {{ number_format($receita->valor, 2, ',', '.') }}</td>
                                    <td>{{ $receita->fornecedor->nome ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">Nenhuma receita a vencer encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <!--end::Receitas Realizadas-->
            <!--begin::Total do Período-->
            <div class="tab-pane fade" id="totalPeriodo" role="tabpanel"
                aria-labelledby="totalPeriodo-tab">
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
<!--end::Receitas Container-->
