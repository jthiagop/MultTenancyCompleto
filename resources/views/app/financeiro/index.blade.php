<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>

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
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Lançamentos Financeiros</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">Financeiro</li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                    <!--begin::Actions-->
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <!--begin::Filter menu-->
                        <div class="d-flex">
                            <select name="campaign-type" data-control="select2" data-hide-search="true"
                                class="form-select form-select-sm bg-body border-body w-175px">
                                <option value="Twitter" selected="selected">Select Campaign</option>
                                <option value="Twitter">Twitter Campaign</option>
                                <option value="Twitter">Facebook Campaign</option>
                                <option value="Twitter">Adword Campaign</option>
                                <option value="Twitter">Carbon Campaign</option>
                            </select>
                            <a href="#" class="btn btn-icon btn-sm btn-success flex-shrink-0 ms-4"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                <!--begin::Svg Icon | path: icons/duotune/arrows/arr075.svg-->
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                            rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor" />
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                            </a>
                        </div>
                        <!--end::Filter menu-->
                        <!--begin::Secondary button-->
                        <!--end::Secondary button-->
                        <!--begin::Primary button-->
                        <!--end::Primary button-->
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">

                    <!--begin::Referral program-->
                    <div class="card mb-5 mb-xl-10">
                        <!--begin::Body-->
                        <div class="card-body py-10">
                            <!--begin::Card title-->
                            <div class="card-title mb-10">
                                <h2>Modulos de Movimentação Financeira</h2>
                            </div>
                            <!--end::Card title-->
                            <!--begin::Row-->
                            <div class="fv-row">
                                <div class="row">

                                    <!--begin::Col-->
                                    <div class="col-6 col-sm-6 col-lg-6 hover-elevate-up ">
                                        <a href="{{ route('caixa.list', ['tab' => 'lancamento']) }}"
                                            class=" btn-outline btn-outline-dashed btn-active-light d-flex align-items-center">
                                            <!--begin::Option-->
                                            <!--begin::Notice-->
                                            <div
                                                class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                                <!--begin::Svg Icon | path: icons/duotune/communication/com005.svg-->
                                                <span class="svg-icon svg-icon-5x me-5">
                                                    <img width="50" height="50"
                                                        src="/assets/media/png/Cash_Register-transformed.webp"
                                                        alt="">
                                                </span>
                                                <!--end::Svg Icon-->
                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                    <!--begin::Content-->
                                                    <div class="mb-3 mb-md-0 fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">Lançamento de Caixa</h4>
                                                        <div class="text-muted fw-semibold fs-6">registre todas as
                                                            transações
                                                            em
                                                            espécie</div>
                                                    </div>
                                                    <!--end::Content-->
                                                    <!--begin::Action-->
                                                    <a href="{{ route('caixa.list') }}"
                                                        class="btn btn-primary px-6 align-self-center ">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="8" y="9" width="3" height="10"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect opacity="0.5" x="13" y="5" width="3"
                                                                    height="14" rx="1.5" fill="currentColor" />
                                                                <rect x="18" y="11" width="3" height="8"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect x="3" y="13" width="3" height="6"
                                                                    rx="1.5" fill="currentColor" />
                                                            </svg>
                                                        </span>

                                                        Movimentação </a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                        </a>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->
                                    <!--begin::Col-->
                                    <div class="col-6 col-sm-6  hover-elevate-up ">
                                        <a href="{{ route('banco.list', ['tab' => 'lancamento']) }}"
                                            class=" btn-outline btn-outline-dashed btn-active-light d-flex align-items-center">
                                            <!--begin::Option-->
                                            <!--begin::Notice-->
                                            <div
                                                class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                                <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
                                                <span class="svg-icon svg-icon-5x me-5">
                                                    <img width="50" height="50"
                                                        src="/assets/media/png/banco3.png" alt="">
                                                </span>

                                                <!--begin::Wrapper-->
                                                <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                                    <!--begin::Content-->
                                                    <div class="mb-3 mb-md-0 fw-semibold">
                                                        <h4 class="text-gray-900 fw-bold">Lançamentos Bancários</h4>
                                                        <div class="text-muted fw-semibold fs-6">Transações realizadas
                                                            com
                                                            contas bancárias</div>
                                                    </div>
                                                    <!--end::Content-->
                                                    <!--begin::Action-->
                                                    <a href="{{ route('banco.list') }}"
                                                        class="btn btn-primary px-6 align-self-center ">
                                                        <span class="svg-icon svg-icon-1">
                                                            <svg width="24" height="24" viewBox="0 0 24 24"
                                                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <rect x="8" y="9" width="3" height="10"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect opacity="0.5" x="13" y="5" width="3"
                                                                    height="14" rx="1.5"
                                                                    fill="currentColor" />
                                                                <rect x="18" y="11" width="3" height="8"
                                                                    rx="1.5" fill="currentColor" />
                                                                <rect x="3" y="13" width="3" height="6"
                                                                    rx="1.5" fill="currentColor" />
                                                            </svg>
                                                        </span>

                                                        Movimentação </a>
                                                    <!--end::Action-->
                                                </div>
                                                <!--end::Wrapper-->
                                            </div>
                                            <!--end::Notice-->
                                        </a>
                                        <!--end::Option-->
                                    </div>
                                    <!--end::Col-->

                                </div>
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Referral program-->
                    <!--begin::Input group-->


                    <!--begin:::Tab pane-->
                    <div class="tab-pane fade show active" id="kt_ecommerce_customer_overview" role="tabpanel">
                        <!--begin::Card-->
                        <div class="card pt-4 mb-6 mb-xl-9">
                            <!--begin::Card header-->
                            <!--begin::Products-->
                            <div class="card card-flush">
                                <!--begin::Card header-->
                                <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                    <!--begin::Card title-->
                                    <div class="card-title">
                                        <!--begin::Search-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Nav-->
                                            <ul class="nav nav-pills nav-pills-custom mb-3">
                                                <!--begin::Item-->
                                                <li class="nav-item mb-3 me-3 me-lg-6">
                                                    <!--begin::Link-->
                                                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-success flex-column overflow-hidden w-80px h-85px pt-5 pb-2 active"
                                                        id="navReceitas" data-bs-toggle="pill"
                                                        href="#containerReceitas">
                                                        <!--begin::Icon-->
                                                        <div class="nav-icon mb-3">
                                                            <i class="fonticon-drive fs-1 p-0"></i>
                                                        </div>
                                                        <!--end::Icon-->
                                                        <!--begin::Title-->
                                                        <span
                                                            class="nav-text text-gray-800 fw-bold fs-6 lh-1">Receitas</span>
                                                        <!--end::Title-->
                                                        <!--begin::Bullet-->
                                                        <span
                                                            class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-success"></span>
                                                        <!--end::Bullet-->
                                                    </a>
                                                    <!--end::Link-->
                                                </li>
                                                <!--end::Item-->
                                                <!--begin::Item-->
                                                <li class="nav-item mb-3 me-3 me-lg-6">
                                                    <!--begin::Link-->
                                                    <a class="nav-link btn btn-outline btn-flex btn-color-muted btn-active-color-danger flex-column overflow-hidden w-80px h-85px pt-5 pb-2"
                                                        id="navDespesas" data-bs-toggle="pill"
                                                        href="#containerDespesas">
                                                        <!--begin::Icon-->
                                                        <div class="nav-icon mb-3">
                                                            <i class="fonticon-bank fs-1 p-0"></i>
                                                        </div>
                                                        <!--end::Icon-->
                                                        <!--begin::Title-->
                                                        <span
                                                            class="nav-text text-gray-800 fw-bold fs-6 lh-1">Despesas</span>
                                                        <!--end::Title-->
                                                        <!--begin::Bullet-->
                                                        <span
                                                            class="bullet-custom position-absolute bottom-0 w-100 h-4px bg-danger"></span>
                                                        <!--end::Bullet-->
                                                    </a>
                                                    <!--end::Link-->
                                                </li>
                                                <!--end::Item-->

                                            </ul>
                                            <!--end::Nav-->
                                        </div>
                                        <!--end::Search-->
                                        <!--begin::Export buttons-->
                                        <div id="kt_ecommerce_report_customer_orders_export" class="d-none"></div>
                                        <!--end::Export buttons-->
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                        <!--begin::Daterangepicker-->
                                        <input class="form-control form-control-solid w-100 mw-250px"
                                            placeholder="Pick date range"
                                            id="kt_ecommerce_report_customer_orders_daterangepicker" />
                                        <!--end::Daterangepicker-->
                                        <!--begin::Filter-->
                                        <div class="d-flex align-items-center position-relative my-1">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546"
                                                        height="2" rx="1"
                                                        transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                                    <path
                                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                                        fill="currentColor" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            <input type="text" data-kt-ecommerce-order-filter="search"
                                                class="form-control form-control-solid w-250px ps-14"
                                                placeholder="Pesquisar..." />
                                        </div>
                                        <!--end::Filter-->
                                        <!--begin::Export dropdown-->
                                        <button type="button" class="btn btn-light-success"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
                                            <span class="svg-icon svg-icon-2">
                                                <svg width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <!-- Símbolo de "+" para "Novo" -->
                                                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                            Novo
                                        </button>
                                        <!--begin::Menu-->
                                        <div id="kt_ecommerce_report_customer_orders_export_menu"
                                            class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                            data-kt-menu="true">
                                            <!--begin::Menu item Receita-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                    data-bs-target="#Dm_modal_financeiro" data-tipo="receita">
                                                    <span role="img" class="me-2"
                                                        aria-label="Receita">💰</span>
                                                    Receita
                                                </a>
                                            </div>
                                            <!--end::Menu item-->

                                            <!--begin::Menu item Despesa-->
                                            <div class="menu-item px-3">
                                                <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                    data-bs-target="#Dm_modal_financeiro" data-tipo="despesa">
                                                    <span role="img" class="me-2"
                                                        aria-label="Despesa">💸</span>
                                                    Despesa
                                                </a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu-->

                                        <!--end::Export dropdown-->
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Receitas - Card body-->
                                <!-- Início: Containers para Receitas e Despesas -->
                                <div class="tab-content">
                                    <!--begin::Card body-->
                                    <!-- Container Receitas (exibido por padrão) -->
                                    <div class="tab-pane fade show active" id="containerReceitas">
                                        <div class="container py-4">
                                            <!-- Início: NAV das Abas (5 seções) -->
                                            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                                <!-- Aba: Receitas em aberto -->
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="receitasAberto-tab"
                                                        data-bs-toggle="tab" data-bs-target="#receitasAberto"
                                                        type="button" role="tab" aria-controls="receitasAberto"
                                                        aria-selected="true">
                                                        <div class="text-muted small">Receitas em aberto (R$)</div>
                                                        <div class="fw-bold text-danger fs-5">
                                                            R$ {{ number_format($valorTotal, 2, ',', '.') }}
                                                        </div>
                                                    </button>
                                                </li>

                                                <!-- Aba: Receitas realizadas -->
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="receitasRealizadas-tab"
                                                        data-bs-toggle="tab" data-bs-target="#receitasRealizadas"
                                                        type="button" role="tab"
                                                        aria-controls="receitasRealizadas" aria-selected="false">
                                                        <div class="text-muted small">Receitas a Vencerem (R$)</div>
                                                        <div class="fw-bold text-primary fs-5"> R$
                                                            {{ number_format($TotalreceitasAVencer, 2, ',', '.') }}
                                                        </div>
                                                    </button>
                                                </li>

                                                <!-- Aba: Despesas em aberto -->
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="despesasAberto-tab"
                                                        data-bs-toggle="tab" data-bs-target="#despesasAberto"
                                                        type="button" role="tab" aria-controls="despesasAberto"
                                                        aria-selected="false">
                                                        <div class="text-muted small">Despesas em aberto (R$)</div>
                                                        <div class="fw-bold text-danger fs-5">0,00</div>
                                                    </button>
                                                </li>

                                                <!-- Aba: Despesas realizadas -->
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="despesasRealizadas-tab"
                                                        data-bs-toggle="tab" data-bs-target="#despesasRealizadas"
                                                        type="button" role="tab"
                                                        aria-controls="despesasRealizadas" aria-selected="false">
                                                        <div class="text-muted small">Despesas realizadas (R$)</div>
                                                        <div class="fw-bold text-danger fs-5">0,00</div>
                                                    </button>
                                                </li>

                                                <!-- Aba: Total do período -->
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="totalPeriodo-tab"
                                                        data-bs-toggle="tab" data-bs-target="#totalPeriodo"
                                                        type="button" role="tab" aria-controls="totalPeriodo"
                                                        aria-selected="false">
                                                        <div class="text-muted small">Total do período (R$)</div>
                                                        <div class="fw-bold text-success fs-5">0,00</div>
                                                    </button>
                                                </li>
                                            </ul>
                                            <!-- Fim: NAV das Abas -->
                                            <!--begin:::Tab pane container-->
                                            <div class="tab-content" id="myTabContent">

                                                <!--begin:::Tab pane: Receitas em aberto-->
                                                <div class="tab-pane fade show active" id="receitasAberto"
                                                    role="tabpanel" aria-labelledby="receitasAberto-tab">
                                                    <div class="p-3">
                                                        <!--begin::Table-->
                                                        <!--begin::Table-->
                                                        <table class="table align-middle table-row-dashed fs-6 gy-5"
                                                            id="contas_financeiras_table">
                                                            <!--begin::Table head-->
                                                            <thead>
                                                                <tr
                                                                    class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                                    <th class="min-w-120px">Data de Vencimento</th>
                                                                    <th class="min-w-150px">Descrição</th>
                                                                    <th class="min-w-100px">Situação</th>
                                                                    <th class="min-w-100px">Valor</th>
                                                                    <th class="min-w-100px">Fornecedor</th>
                                                                </tr>
                                                            </thead>
                                                            <!--end::Table head-->
                                                            <!--begin::Table body-->
                                                            <tbody class="fw-semibold text-gray-600">
                                                                @forelse($receitasEmAberto as $receita)
                                                                    <tr>
                                                                        <!-- Exibe a data de vencimento formatada -->
                                                                        <td>{{ \Carbon\Carbon::parse($receita->data_primeiro_vencimento)->format('d M Y') }}
                                                                        </td>

                                                                        <!-- Exibe a descrição da receita -->
                                                                        <td>{{ $receita->descricao }}</td>

                                                                        <!-- Exibe o status com uma badge visual -->
                                                                        <td>
                                                                            <div class="badge badge-light-warning">
                                                                                {{ ucfirst($receita->status_pagamento) }}
                                                                            </div>
                                                                        </td>

                                                                        <!-- Exibe o valor formatado como moeda -->
                                                                        <td>R$
                                                                            {{ number_format($receita->valor, 2, ',', '.') }}
                                                                        </td>

                                                                        <!-- Exibe o fornecedor (caso tenha um relacionamento, substitua por $receita->fornecedor->nome) -->
                                                                        <td>{{ $receita->fornecedor_id }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5">Nenhuma receita em aberto
                                                                            encontrada.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <!--end::Table body-->
                                                        </table>
                                                        <!--end::Table-->

                                                        <!--end::Table-->
                                                        <!-- Aqui você pode inserir cards, tabelas ou qualquer componente -->
                                                    </div>
                                                </div>
                                                <!--end:::Tab pane: Receitas em aberto-->

                                                <!--begin:::Tab pane: Receitas realizadas-->
                                                <div class="tab-pane fade" id="receitasRealizadas" role="tabpanel"
                                                    aria-labelledby="receitasRealizadas-tab">
                                                    <div class="p-3">
                                                        <!--begin::Table-->
                                                        <table class="table align-middle table-row-dashed fs-6 gy-5"
                                                            id="contas_financeiras_table">
                                                            <!--begin::Table head-->
                                                            <thead>
                                                                <tr
                                                                    class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                                    <th class="min-w-120px">Data de Vencimento</th>
                                                                    <th class="min-w-150px">Descrição</th>
                                                                    <th class="min-w-100px">Situação</th>
                                                                    <th class="min-w-100px">Valor</th>
                                                                    <th class="min-w-100px">Fornecedor</th>
                                                                </tr>
                                                            </thead>
                                                            <!--end::Table head-->
                                                            <!--begin::Table body-->
                                                            <tbody class="fw-semibold text-gray-600">
                                                                @forelse($receitasAVencer as $receita)
                                                                    <tr>
                                                                        <!-- Exibe a data de vencimento formatada -->
                                                                        <td>{{ \Carbon\Carbon::parse($receita->data_primeiro_vencimento)->format('d M Y') }}
                                                                        </td>

                                                                        <!-- Exibe a descrição da receita -->
                                                                        <td>{{ $receita->descricao }}</td>

                                                                        <!-- Exibe o status com uma badge visual -->
                                                                        <td>
                                                                            <div class="badge badge-light-warning">
                                                                                {{ ucfirst($receita->status_pagamento) }}
                                                                            </div>
                                                                        </td>

                                                                        <!-- Exibe o valor formatado como moeda -->
                                                                        <td>R$
                                                                            {{ number_format($receita->valor, 2, ',', '.') }}
                                                                        </td>

                                                                        <!-- Exibe o fornecedor (caso tenha um relacionamento, substitua por $receita->fornecedor->nome) -->
                                                                        <td>{{ $receita->fornecedor_id }}</td>
                                                                    </tr>
                                                                @empty
                                                                    <tr>
                                                                        <td colspan="5">Nenhuma receita em aberto
                                                                            encontrada.</td>
                                                                    </tr>
                                                                @endforelse
                                                            </tbody>
                                                            <!--end::Table body-->
                                                        </table>
                                                        <!--end::Table-->
                                                    </div>
                                                </div>
                                                <!--end:::Tab pane: Receitas realizadas-->

                                                <!--begin:::Tab pane: Despesas em aberto-->
                                                <div class="tab-pane fade" id="despesasAberto" role="tabpanel"
                                                    aria-labelledby="despesasAberto-tab">
                                                    <div class="p-3">
                                                        <h5>Despesas em aberto (R$)</h5>
                                                        <p>Exemplo de conteúdo para “Despesas em aberto”.</p>
                                                    </div>
                                                </div>
                                                <!--end:::Tab pane: Despesas em aberto-->

                                                <!--begin:::Tab pane: Despesas realizadas-->
                                                <div class="tab-pane fade" id="despesasRealizadas" role="tabpanel"
                                                    aria-labelledby="despesasRealizadas-tab">
                                                    <div class="p-3">
                                                        <h5>Despesas realizadas (R$)</h5>
                                                        <p>Exemplo de conteúdo para “Despesas realizadas”.</p>
                                                    </div>
                                                </div>
                                                <!--end:::Tab pane: Despesas realizadas-->

                                                <!--begin:::Tab pane: Total do período-->
                                                <div class="tab-pane fade" id="totalPeriodo" role="tabpanel"
                                                    aria-labelledby="totalPeriodo-tab">
                                                    <div class="p-3">
                                                        <h5>Total do período (R$)</h5>
                                                        <p>Exemplo de conteúdo para “Total do período”.</p>
                                                    </div>
                                                </div>
                                                <!--end:::Tab pane: Total do período-->

                                            </div>
                                            <!--end:::Tab pane container-->
                                        </div>
                                        <!--end::Card body-->
                                    </div>
                                </div>
                                <!--end::Card body-->



                                <!--begin::Despesas - Card body-->
                                <!-- Container Despesas -->
                                <div class="tab-pane fade" id="containerDespesas">
                                    <!--begin::Card body-->
                                    <div class="container py-4">
                                        <!-- Início: NAV das Abas (5 seções) -->
                                        <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                            <!-- Aba: Receitas em aberto -->
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="receitasAberto-tab"
                                                    data-bs-toggle="tab" data-bs-target="#receitasAberto"
                                                    type="button" role="tab" aria-controls="receitasAberto"
                                                    aria-selected="true">
                                                    <div class="text-muted small">Depesas em aberto (R$)</div>
                                                    <div class="fw-bold text-danger fs-5">R$
                                                        {{ number_format($valorDespesaTotal, 2, ',', '.') }}</div>
                                                </button>
                                            </li>


                                            <!-- Aba: Despesas em aberto -->
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="despesasAberto-tab" data-bs-toggle="tab"
                                                    data-bs-target="#despesasAberto" type="button" role="tab"
                                                    aria-controls="despesasAberto" aria-selected="false">
                                                    <div class="text-muted small">Despesas em aberto (R$)</div>
                                                    <div class="fw-bold text-danger fs-5">0,00</div>
                                                </button>
                                            </li>

                                            <!-- Aba: Despesas realizadas -->
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="despesasRealizadas-tab"
                                                    data-bs-toggle="tab" data-bs-target="#despesasRealizadas"
                                                    type="button" role="tab" aria-controls="despesasRealizadas"
                                                    aria-selected="false">
                                                    <div class="text-muted small">Despesas realizadas (R$)</div>
                                                    <div class="fw-bold text-danger fs-5">0,00</div>
                                                </button>
                                            </li>

                                            <!-- Aba: Total do período -->
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="totalPeriodo-tab" data-bs-toggle="tab"
                                                    data-bs-target="#totalPeriodo" type="button" role="tab"
                                                    aria-controls="totalPeriodo" aria-selected="false">
                                                    <div class="text-muted small">Total do período (R$)</div>
                                                    <div class="fw-bold text-success fs-5">0,00</div>
                                                </button>
                                            </li>
                                        </ul>
                                        <!-- Fim: NAV das Abas -->
                                        <!--begin:::Tab pane container-->
                                        <div class="tab-content" id="myTabContent">

                                            <!--begin:::Tab pane: Receitas em aberto-->
                                            <div class="tab-pane fade show active" id="receitasAberto"
                                                role="tabpanel" aria-labelledby="receitasAberto-tab">
                                                <div class="p-3">
                                                                                                            <!--begin::Table-->
    <table class="table align-middle table-row-dashed fs-6 gy-5" id="contas_financeiras_table">
        <!--begin::Table head-->
        <thead>
            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                <th class="min-w-120px">Data de Vencimento</th>
                <th class="min-w-150px">Descrição</th>
                <th class="min-w-100px">Situação</th>
                <th class="min-w-100px">Valor</th>
                <th class="min-w-100px">Fornecedor</th>
            </tr>
        </thead>
        <!--end::Table head-->
        <!--begin::Table body-->
        <tbody class="fw-semibold text-gray-600">
            @forelse($despesasEmAberto as $despesa)
                <tr>
                    <!-- Exibe a data de vencimento formatada -->
                    <td>{{ \Carbon\Carbon::parse($despesa->data_primeiro_vencimento)->format('d M Y') }}</td>

                    <!-- Exibe a descrição da despesa -->
                    <td>{{ $despesa->descricao }}</td>

                    <!-- Exibe o status com uma badge visual -->
                    <td>
                        <div class="badge badge-light-warning">
                            {{ ucfirst($despesa->status_pagamento) }}
                        </div>
                    </td>

                    <!-- Exibe o valor formatado como moeda -->
                    <td>R$ {{ number_format($despesa->valor, 2, ',', '.') }}</td>

                    <!-- Exibe o fornecedor (caso tenha um relacionamento, substitua por $despesa->fornecedor->nome) -->
                    <td>{{ $despesa->fornecedor_id }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhuma despesa em aberto encontrada.</td>
                </tr>
            @endforelse
        </tbody>
        <!--end::Table body-->
    </table>
    <!--end::Table-->
                                                    <!-- Aqui você pode inserir cards, tabelas ou qualquer componente -->
                                                </div>
                                            </div>
                                            <!--end:::Tab pane: Receitas em aberto-->

                                            <!--begin:::Tab pane: Receitas realizadas-->
                                            <div class="tab-pane fade" id="receitasRealizadas" role="tabpanel"
                                                aria-labelledby="receitasRealizadas-tab">
                                                <div class="p-3">
                                                    <h5>Despesas realizadas (R$)</h5>
                                                    <p>Exemplo de conteúdo para “Receitas realizadas”.</p>
                                                </div>
                                            </div>
                                            <!--end:::Tab pane: Receitas realizadas-->

                                            <!--begin:::Tab pane: Despesas em aberto-->
                                            <div class="tab-pane fade" id="despesasAberto" role="tabpanel"
                                                aria-labelledby="despesasAberto-tab">
                                                <div class="p-3">
                                                    <h5>Despesas em aberto (R$)</h5>
                                                    <p>Exemplo de conteúdo para “Despesas em aberto”.</p>
                                                </div>
                                            </div>
                                            <!--end:::Tab pane: Despesas em aberto-->

                                            <!--begin:::Tab pane: Despesas realizadas-->
                                            <div class="tab-pane fade" id="despesasRealizadas" role="tabpanel"
                                                aria-labelledby="despesasRealizadas-tab">
                                                <div class="p-3">
                                                    <h5>Despesas realizadas (R$)</h5>
                                                    <p>Exemplo de conteúdo para “Despesas realizadas”.</p>
                                                </div>
                                            </div>
                                            <!--end:::Tab pane: Despesas realizadas-->

                                            <!--begin:::Tab pane: Total do período-->
                                            <div class="tab-pane fade" id="totalPeriodo" role="tabpanel"
                                                aria-labelledby="totalPeriodo-tab">
                                                <div class="p-3">
                                                    <h5>Total do período (R$)</h5>
                                                    <p>Exemplo de conteúdo para “Total do período”.</p>
                                                </div>
                                            </div>
                                            <!--end:::Tab pane: Total do período-->

                                        </div>
                                        <!--end:::Tab pane container-->
                                    </div>
                                    <!--end::Card body-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Products-->
                            <!--end::Card body-->
                        </div>
                        <!--end::Card-->
                    </div>
                    <!--end:::Tab pane-->

                    <!--end::Card-->

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
        <!--begin::Modal - Upgrade plan-->

        <!--end::Modal - Upgrade plan-->
        @include('app.components.modals.lancar-caixa')
        @include('app.components.modals.lancar-banco')

    </div>
    <!--end:::Main-->




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

<script>
    var lpsData = @json($lps);
</script>

<script></script>
