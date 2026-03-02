@php
    $isPJ = $parceiro->tipo === 'pj';
    $documento = $parceiro->documento;
    $tipoDoc = $isPJ ? 'CNPJ' : 'CPF';
    $endereco = $parceiro->address;
    $enderecoCompleto = $endereco
        ? trim(collect([
            $endereco->rua,
            $endereco->numero ? 'nº ' . $endereco->numero : null,
            $endereco->bairro,
            $endereco->cidade,
            $endereco->uf,
        ])->filter()->implode(', '))
        : null;
@endphp

<x-tenant-app-layout :page-title="$parceiro->nome" :breadcrumbs="[
    ['label' => 'Financeiro', 'url' => route('banco.list')],
    ['label' => 'Parceiros', 'url' => route('parceiros.index')],
    ['label' => $parceiro->nome],
]">

    <!--begin::Content wrapper-->
    <div class="d-flex flex-column flex-column-fluid py-3 py-lg-6">

        <!--begin::Content-->
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-fluid">

                {{-- ========== HEADER: Info do Parceiro ========== --}}
                <div class="card mb-6">
                    <div class="card-body pt-6 pb-4">
                        <div class="d-flex flex-wrap flex-sm-nowrap align-items-center">
                            {{-- Avatar --}}
                            <div class="me-6 mb-3">
                                <div class="symbol symbol-70px symbol-circle">
                                    <span class="symbol-label bg-light-{{ $parceiro->natureza === 'fornecedor' ? 'primary' : ($parceiro->natureza === 'cliente' ? 'success' : 'info') }} fs-2 fw-bold text-{{ $parceiro->natureza === 'fornecedor' ? 'primary' : ($parceiro->natureza === 'cliente' ? 'success' : 'info') }}">
                                        {{ strtoupper(mb_substr($parceiro->nome, 0, 2)) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Dados principais --}}
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <div class="min-w-0">
                                        <h3 class="text-gray-900 fw-bold fs-2 text-truncate mb-1">{{ $parceiro->nome }}</h3>
                                        @if ($parceiro->nome_fantasia)
                                            <span class="text-gray-500 fs-6">{{ $parceiro->nome_fantasia }}</span>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2 flex-shrink-0 mt-2 mt-sm-0">
                                        <span class="badge {{ $parceiro->natureza_badge_class }} rounded-pill px-4 py-2 fs-7">
                                            {{ $parceiro->natureza_label }}
                                        </span>
                                        <span class="badge {{ $parceiro->active ? 'badge-light-success' : 'badge-light-danger' }} rounded-pill px-4 py-2 fs-7">
                                            {{ $parceiro->active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-4 fs-6 text-gray-600">
                                    @if ($documento)
                                        <span>
                                            <i class="bi bi-credit-card me-1 text-gray-500"></i>
                                            {{ $tipoDoc }}: <strong>{{ $documento }}</strong>
                                        </span>
                                    @endif
                                    @if ($parceiro->email)
                                        <span>
                                            <i class="bi bi-envelope me-1 text-gray-500"></i>
                                            {{ $parceiro->email }}
                                        </span>
                                    @endif
                                    @if ($parceiro->telefone)
                                        <span>
                                            <i class="bi bi-telephone me-1 text-gray-500"></i>
                                            {{ $parceiro->telefone }}
                                        </span>
                                    @endif
                                    @if ($enderecoCompleto)
                                        <span>
                                            <i class="bi bi-geo-alt me-1 text-gray-500"></i>
                                            {{ $enderecoCompleto }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== CARDS DE RESUMO ========== --}}
                <div class="row g-4 mb-6">
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card card-flush h-100 border-0 shadow-sm">
                            <div class="card-body py-4 px-4">
                                <div class="text-gray-500 fs-8 fw-semibold mb-1">Total Transações</div>
                                <div class="text-gray-900 fw-bold fs-3">{{ number_format($totalTransacoes, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card card-flush h-100 border-0 shadow-sm">
                            <div class="card-body py-4 px-4">
                                <div class="text-gray-500 fs-8 fw-semibold mb-1">Total Entradas</div>
                                <div class="text-success fw-bold fs-3">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card card-flush h-100 border-0 shadow-sm">
                            <div class="card-body py-4 px-4">
                                <div class="text-gray-500 fs-8 fw-semibold mb-1">Total Saídas</div>
                                <div class="text-danger fw-bold fs-3">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card card-flush h-100 border-0 shadow-sm">
                            <div class="card-body py-4 px-4">
                                <div class="text-gray-500 fs-8 fw-semibold mb-1">Em Aberto</div>
                                <div class="text-warning fw-bold fs-3">R$ {{ number_format($totalEmAberto, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card card-flush h-100 border-0 shadow-sm">
                            <div class="card-body py-4 px-4">
                                <div class="text-gray-500 fs-8 fw-semibold mb-1">Atrasado</div>
                                <div class="text-danger fw-bold fs-3">R$ {{ number_format($totalAtrasado, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 col-xl-2">
                        <div class="card card-flush h-100 border-0 shadow-sm">
                            <div class="card-body py-4 px-4">
                                <div class="text-gray-500 fs-8 fw-semibold mb-1">Pago / Recebido</div>
                                <div class="text-primary fw-bold fs-3">R$ {{ number_format($totalPago, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========== TABS + DATATABLE ========== --}}
                <div class="card card-flush">
                    <div class="card-header border-0 pt-6">
                        {{-- Tabs de navegação --}}
                        <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-semibold" id="parceiro_transacoes_tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab === 'todas' ? 'active' : '' }} text-gray-600"
                                   id="tab_todas" data-bs-toggle="tab" href="#pane_todas"
                                   role="tab" data-tab="todas">
                                    <i class="bi bi-list-ul me-2 fs-5"></i>Todas
                                    <span class="badge badge-light-dark rounded-pill ms-2 fs-8" id="count_todas">-</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab === 'a_receber' ? 'active' : '' }} text-gray-600"
                                   id="tab_a_receber" data-bs-toggle="tab" href="#pane_a_receber"
                                   role="tab" data-tab="a_receber">
                                    <i class="bi bi-arrow-down-circle me-2 fs-5 text-success"></i>A Receber
                                    <span class="badge badge-light-success rounded-pill ms-2 fs-8" id="count_a_receber">-</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link {{ $activeTab === 'a_pagar' ? 'active' : '' }} text-gray-600"
                                   id="tab_a_pagar" data-bs-toggle="tab" href="#pane_a_pagar"
                                   role="tab" data-tab="a_pagar">
                                    <i class="bi bi-arrow-up-circle me-2 fs-5 text-danger"></i>A Pagar
                                    <span class="badge badge-light-danger rounded-pill ms-2 fs-8" id="count_a_pagar">-</span>
                                </a>
                            </li>
                        </ul>

                        {{-- Filtros --}}
                        <div class="card-toolbar d-flex align-items-center gap-3">
                            {{-- Busca --}}
                            <div class="d-flex align-items-center position-relative">
                                <i class="bi bi-search fs-6 position-absolute ms-3 text-gray-500"></i>
                                <input type="text" class="form-control form-control-sm form-control-solid ps-9 w-200px"
                                       id="parceiro_transacoes_search" placeholder="Buscar transação..." autocomplete="off" />
                            </div>
                            {{-- Filtro situação --}}
                            <select class="form-select form-select-sm form-select-solid w-150px" id="parceiro_situacao_filter">
                                <option value="todos">Todas situações</option>
                                <option value="em_aberto">Em aberto</option>
                                <option value="atrasado">Atrasado</option>
                                <option value="pago">Pago</option>
                                <option value="recebido">Recebido</option>
                                <option value="previsto">Previsto</option>
                                <option value="parcial">Parcial</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="tab-content" id="parceiro_transacoes_content">
                            {{-- Pane: Todas --}}
                            <div class="tab-pane fade {{ $activeTab === 'todas' ? 'show active' : '' }}" id="pane_todas" role="tabpanel"></div>
                            {{-- Pane: A Receber --}}
                            <div class="tab-pane fade {{ $activeTab === 'a_receber' ? 'show active' : '' }}" id="pane_a_receber" role="tabpanel"></div>
                            {{-- Pane: A Pagar --}}
                            <div class="tab-pane fade {{ $activeTab === 'a_pagar' ? 'show active' : '' }}" id="pane_a_pagar" role="tabpanel"></div>
                        </div>

                        {{-- Tabela única compartilhada pelas tabs --}}
                        <div class="mt-4">
                            <table class="table align-middle table-striped table-row-dashed fs-6 gy-5"
                                   id="kt_parceiro_transacoes_table">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-4">
                                        <th class="min-w-100px">Data</th>
                                        <th class="min-w-200px">Descrição</th>
                                        <th class="min-w-80px">Tipo</th>
                                        <th class="min-w-120px">Valor</th>
                                        <th class="min-w-100px">Situação</th>
                                        <th class="min-w-100px">Vencimento</th>
                                        <th class="min-w-120px">Lançamento Padrão</th>
                                        <th class="min-w-120px">Conta</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ========== OBSERVAÇÕES (se houver) ========== --}}
                @if ($parceiro->observacoes)
                    <div class="card card-flush mt-6">
                        <div class="card-header">
                            <h3 class="card-title fs-5 fw-semibold text-gray-800">
                                <i class="bi bi-chat-left-text me-2 text-gray-500"></i>Observações
                            </h3>
                        </div>
                        <div class="card-body pt-2">
                            <p class="text-gray-700 mb-0">{{ $parceiro->observacoes }}</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        <!--end::Content-->
    </div>
    <!--end::Content wrapper-->

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="/tenancy/assets/plugins/custom/datatables/datatables.bundle.js"></script>

        @include('app.financeiro.parceiros.scripts.parceiro-show-datatable')
    @endpush

</x-tenant-app-layout>
