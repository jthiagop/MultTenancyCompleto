<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>

<x-tenant-app-layout>
    {{-- *** Modal de Receitas e Despesas *** --}}
    @include('app.components.modals.financeiro.recitasDespesas.Dm_modal_financeiro')
    {{-- *** Fim Modal de Receitas e Despesas *** --}}
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Lançamentos Financeiros
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1"
                            aria-label="Navegação do site">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted" aria-current="page">Financeiro</li>
                        </ul>
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-3">
                        <div id="kt_financeiro_new_menu"
                            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                            data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                    data-bs-target="#modalLancarCaixa" aria-label="Adicionar lançamento de caixa">
                                    <span class="me-2">💰</span> Lançar Caixa
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                    data-bs-target="#modalLancarBanco" aria-label="Adicionar lançamento bancário">
                                    <span class="me-2">🏦</span> Lançar Banco
                                </a>
                            </div>
                        </div>
                    </div>
                    <!--end::Actions-->
                </div>
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Financial Modules-->
                    <div class="card mb-5 mb-xl-10">
                        <div class="card-body py-10">
                            <div class="card-title mb-10">
                                <h2>Módulos de Movimentação Financeira</h2>
                            </div>
                            <div class="row">
                                <!--begin::Caixa Card-->
                                <div class="col-12 col-md-6 hover-elevate-up">
                                    <a href="{{ route('caixa.list', ['tab' => 'lancamento']) }}"
                                        class="text-decoration-none" aria-label="Acessar lançamentos de caixa">
                                        <div
                                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                            <span class="svg-icon svg-icon-5x me-5">
                                                <img width="50" height="50"
                                                    src="/assets/media/png/Cash_Register-transformed.webp"
                                                    alt="Ícone de lançamento de caixa" />
                                            </span>
                                            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                <div class="mb-3 mb-md-0 fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Lançamento de Caixa</h4>
                                                    <div class="text-muted fw-semibold fs-6">Gerencie entradas e saídas
                                                        em dinheiro com controle total.</div>
                                                    <span class="badge badge-success mt-2">{{ $caixaPendentes ?? 0 }}
                                                        lançamentos pendentes</span>
                                                </div>
                                                <span class="btn btn-primary px-6 align-self-center" role="button">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    Acessar
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!--end::Caixa Card-->
                                <!--begin::Banco Card-->
                                <div class="col-12 col-md-6 hover-elevate-up">
                                    <a href="{{ route('banco.list', ['tab' => 'lancamento']) }}"
                                        class="text-decoration-none" aria-label="Acessar lançamentos bancários">
                                        <div
                                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                            <span class="svg-icon svg-icon-5x me-5">
                                                <img width="50" height="50" src="/assets/media/png/banco3.png"
                                                    alt="Ícone de lançamentos bancários" />
                                            </span>
                                            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                <div class="mb-3 mb-md-0 fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">Lançamentos Bancários</h4>
                                                    <div class="text-muted fw-semibold fs-6">Controle transações de
                                                        contas bancárias com relatórios detalhados.</div>
                                                    <span class="badge badge-success mt-2">{{ $bancoPendentes ?? 0 }}
                                                        lançamentos pendentes</span>
                                                </div>
                                                <span class="btn btn-primary px-6 align-self-center" role="button">
                                                    <svg width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5 12H19M19 12L13 6M19 12L13 18" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                    Acessar
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <!--end::Banco Card-->
                            </div>
                        </div>
                    </div>
                    <!--end::Financial Modules-->
                </div>
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Modals-->
        @include('app.components.modals.lancar-caixa')
        @include('app.components.modals.lancar-banco')
        <!--end::Modals-->
    </div>
    <!--end::Main-->
    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <!--begin::Content container-->
        <div id="kt_app_content_container" class="app-container container-xxl">
            <!--begin::Financial Overview-->
            <div class="card card-flush">
                <!--begin::Card header-->
                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                    <!--begin::Card title-->
                    <div class="card-title">
                        <!--begin::Tabs-->
                        <ul class="nav nav-pills nav-pills-custom mb-3">
                            <!--begin::Receitas Tab-->
                            <li class="nav-item mb-3 me-3 me-lg-6">
                                <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-success flex-column overflow-hidden w-80px h-85px pt-5 pb-2 active"
                                    id="navReceitas" data-bs-toggle="pill" href="#containerReceitas"
                                    aria-label="Exibir Receitas">
                                    <div class="nav-icon mb-3">
                                        <i class="bi bi-arrow-up-circle fs-1" aria-hidden="true"></i>
                                    </div>
                                    <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">Receitas</span>
                                    <span
                                        class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-success"></span>
                                </a>
                            </li>
                            <!--end::Receitas Tab-->
                            <!--begin::Despesas Tab-->
                            <li class="nav-item mb-3 me-3 me-lg-6">
                                <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                                    id="navDespesas" data-bs-toggle="pill" href="#containerDespesas"
                                    aria-label="Exibir Despesas">
                                    <div class="nav-icon mb-3">
                                        <i class="bi bi-arrow-down-circle fs-1" aria-hidden="true"></i>
                                    </div>
                                    <span class="nav-text text-gray-800 fw-bold fs-6 lh-1">Despesas</span>
                                    <span
                                        class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                                </a>
                            </li>
                            <!--end::Despesas Tab-->
                        </ul>
                        <!--end::Tabs-->
                    </div>
                    <!--end::Card title-->
                    <!--begin::Card toolbar-->
                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                        <!--begin::Search-->
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                        rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                    <path
                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                            <input type="text" data-kt-ecommerce-order-filter="search"
                                class="form-control form-control-solid w-250px ps-14"
                                placeholder="Pesquisar por descrição..." aria-label="Pesquisar por descrição" />
                        </div>
                        <!--end::Search-->
                        <!--begin::Daterangepicker-->
                        <input class="form-control form-control-solid w-100 mw-250px" placeholder="Selecionar período"
                            id="kt_ecommerce_report_customer_orders_daterangepicker"
                            aria-label="Selecionar período de datas" />
                        <!--end::Daterangepicker-->
                        <!--begin::New Button-->
                        <button type="button" class="btn btn-light-success" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end" aria-label="Adicionar novo lançamento">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            Novo
                        </button>
                        <!--begin::Menu-->
                        <div id="kt_ecommerce_report_customer_orders_export_menu"
                            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                            data-kt-menu="true">
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                    data-bs-target="#Dm_modal_financeiro" data-tipo="receita"
                                    aria-label="Adicionar nova receita">
                                    <span class="me-2">💰</span> Receita
                                </a>
                            </div>
                            <div class="menu-item px-3">
                                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                    data-bs-target="#Dm_modal_financeiro" data-tipo="despesa"
                                    aria-label="Adicionar nova despesa">
                                    <span class="me-2">💸</span> Despesa
                                </a>
                            </div>
                        </div>
                        <!--end::Menu-->
                        <!--end::New Button-->
                    </div>
                    <!--end::Card toolbar-->
                </div>
                <!--end::Card header-->
                <!--begin::Tab Content-->
                <div class="tab-content">
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
                </div>
                <!--end::Tab Content-->
            </div>
            <!--end::Financial Overview-->
        </div>
        <!--end::Content container-->
    </div>
    <!--end::Content-->
</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<link href="/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
<script src="/assets/js/scripts.bundle.js"></script>

<!--end::Custom Javascript-->
<script src="/assets/js/custom/apps/financeiro/index/receitasDespesas.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/Dm_modal_financeiro.js"></script>


<!--end::Javascript-->
