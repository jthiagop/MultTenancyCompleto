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
							<!--begin::Content-->
							<div id="kt_app_content" class="app-content flex-column-fluid">
								<!--begin::Content container-->
								<div id="kt_app_content_container" class="app-container container-xxl">
									<!--begin::Products-->
									<div class="card card-flush">
										<!--begin::Card header-->
										<div class="card-header align-items-center py-5 gap-2 gap-md-5">
											<!--begin::Card title-->
											<div class="card-title">
												<!--begin::Search-->
												<div class="d-flex align-items-center position-relative my-1">
													<!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
													<span class="svg-icon svg-icon-1 position-absolute ms-4">
														<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
															<rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
															<path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
														</svg>
													</span>
													<!--end::Svg Icon-->
													<input type="text" data-kt-ecommerce-order-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search Report" />
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
												<input class="form-control form-control-solid w-100 mw-250px" placeholder="Pick date range" id="kt_ecommerce_report_customer_orders_daterangepicker" />
												<!--end::Daterangepicker-->
												<!--begin::Filter-->
												<div class="w-150px">
													<!--begin::Select2-->
													<select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-order-filter="status">
														<option></option>
														<option value="all">All</option>
														<option value="active">Active</option>
														<option value="locked">Locked</option>
														<option value="disabled">Disabled</option>
														<option value="banned">Banned</option>
													</select>
													<!--end::Select2-->
												</div>
												<!--end::Filter-->
												<!--begin::Export dropdown-->
												<button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr078.svg-->
												<span class="svg-icon svg-icon-2">
													<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
														<rect opacity="0.3" x="12.75" y="4.25" width="12" height="2" rx="1" transform="rotate(90 12.75 4.25)" fill="currentColor" />
														<path d="M12.0573 6.11875L13.5203 7.87435C13.9121 8.34457 14.6232 8.37683 15.056 7.94401C15.4457 7.5543 15.4641 6.92836 15.0979 6.51643L12.4974 3.59084C12.0996 3.14332 11.4004 3.14332 11.0026 3.59084L8.40206 6.51643C8.0359 6.92836 8.0543 7.5543 8.44401 7.94401C8.87683 8.37683 9.58785 8.34458 9.9797 7.87435L11.4427 6.11875C11.6026 5.92684 11.8974 5.92684 12.0573 6.11875Z" fill="currentColor" />
														<path opacity="0.3" d="M18.75 8.25H17.75C17.1977 8.25 16.75 8.69772 16.75 9.25C16.75 9.80228 17.1977 10.25 17.75 10.25C18.3023 10.25 18.75 10.6977 18.75 11.25V18.25C18.75 18.8023 18.3023 19.25 17.75 19.25H5.75C5.19772 19.25 4.75 18.8023 4.75 18.25V11.25C4.75 10.6977 5.19771 10.25 5.75 10.25C6.30229 10.25 6.75 9.80228 6.75 9.25C6.75 8.69772 6.30229 8.25 5.75 8.25H4.75C3.64543 8.25 2.75 9.14543 2.75 10.25V19.25C2.75 20.3546 3.64543 21.25 4.75 21.25H18.75C19.8546 21.25 20.75 20.3546 20.75 19.25V10.25C20.75 9.14543 19.8546 8.25 18.75 8.25Z" fill="currentColor" />
													</svg>
												</span>
												<!--end::Svg Icon-->Export Report</button>
												<!--begin::Menu-->
												<div id="kt_ecommerce_report_customer_orders_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
													<!--begin::Menu item-->
													<div class="menu-item px-3">
														<a href="#" class="menu-link px-3" data-kt-ecommerce-export="copy">Copy to clipboard</a>
													</div>
													<!--end::Menu item-->
													<!--begin::Menu item-->
													<div class="menu-item px-3">
														<a href="#" class="menu-link px-3" data-kt-ecommerce-export="excel">Export as Excel</a>
													</div>
													<!--end::Menu item-->
													<!--begin::Menu item-->
													<div class="menu-item px-3">
														<a href="#" class="menu-link px-3" data-kt-ecommerce-export="csv">Export as CSV</a>
													</div>
													<!--end::Menu item-->
													<!--begin::Menu item-->
													<div class="menu-item px-3">
														<a href="#" class="menu-link px-3" data-kt-ecommerce-export="pdf">Export as PDF</a>
													</div>
													<!--end::Menu item-->
												</div>
												<!--end::Menu-->
												<!--end::Export dropdown-->
											</div>
											<!--end::Card toolbar-->
										</div>
										<!--end::Card header-->
										<!--begin::Card body-->
										<div class="card-body pt-0">
											<!--begin::Table-->
											<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_report_customer_orders_table">
												<!--begin::Table head-->
												<thead>
													<!--begin::Table row-->
													<tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
														<th class="min-w-100px">Customer Name</th>
														<th class="min-w-100px">Email</th>
														<th class="min-w-100px">Status</th>
														<th class="min-w-100px">Date Joined</th>
														<th class="text-end min-w-75px">No. Orders</th>
														<th class="text-end min-w-75px">No. Products</th>
														<th class="text-end min-w-100px">Total</th>
													</tr>
													<!--end::Table row-->
												</thead>
												<!--end::Table head-->
												<!--begin::Table body-->
												<tbody class="fw-semibold text-gray-600">
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Emma Smith</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">smith@kpmg.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Jul 2023, 6:05 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">47</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">62</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2548.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Melody Macy</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">melody@altbox.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>15 Apr 2023, 11:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">90</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">103</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2557.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Max Smith</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">max@kt.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>15 Apr 2023, 9:23 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">89</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">103</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$5002.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Sean Bean</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">sean@dellito.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>24 Jun 2023, 6:43 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">97</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">105</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4956.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Brian Cox</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">brian@exchange.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>24 Jun 2023, 10:10 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">92</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">97</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3749.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Mikaela Collins</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">mik@pex.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 11:05 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">78</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">85</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1277.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Francis Mitcham</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">f.mit@kpmg.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>22 Sep 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">99</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">104</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$789.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Olivia Wild</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">olivia@corpmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Nov 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">23</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">34</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4419.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Neil Owen</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">owen.neil@gmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>22 Sep 2023, 11:05 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">35</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">44</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$297.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Dan Wilson</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">dam@consilting.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>22 Sep 2023, 2:40 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">12</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">25</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2189.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Emma Bold</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">emma@intenso.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>21 Feb 2023, 5:20 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">54</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">62</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3337.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Ana Crown</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">ana.cf@limtel.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>15 Apr 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">100</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">108</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2413.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Robert Doe</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">robert@benko.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-danger">Banned</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Jun 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">34</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">44</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4787.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">John Miller</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">miller@mapple.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Oct 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">7</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">21</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1389.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Lucy Kunic</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">lucy.m@fentech.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>21 Feb 2023, 2:40 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">9</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">22</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$378.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Ethan Wilder</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">ethan@loop.com.au</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-info">Disabled</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Nov 2023, 5:30 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">68</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">80</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2984.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Sean Bean</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">sean@dellito.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Jul 2023, 5:30 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">62</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">70</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1071.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Emma Smith</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">smith@kpmg.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Jul 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">20</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">35</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1559.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Melody Macy</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">melody@altbox.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>21 Feb 2023, 5:20 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">14</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">24</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4751.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Max Smith</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">max@kt.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Mar 2023, 11:05 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">81</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">95</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2607.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Sean Bean</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">sean@dellito.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>05 May 2023, 6:05 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">45</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">57</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2352.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Brian Cox</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">brian@exchange.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-info">Disabled</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>22 Sep 2023, 5:20 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">93</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">102</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1143.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Mikaela Collins</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">mik@pex.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Jul 2023, 8:43 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">49</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">55</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2636.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Francis Mitcham</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">f.mit@kpmg.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-info">Disabled</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>24 Jun 2023, 11:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">12</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">21</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$141.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Olivia Wild</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">olivia@corpmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>05 May 2023, 9:23 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">74</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">85</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3505.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Neil Owen</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">owen.neil@gmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Jun 2023, 5:30 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">85</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">94</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$273.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Dan Wilson</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">dam@consilting.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>21 Feb 2023, 6:05 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">78</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">84</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2220.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Emma Bold</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">emma@intenso.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-warning">Locked</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>19 Aug 2023, 9:23 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">55</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">67</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4436.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Ana Crown</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">ana.cf@limtel.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-danger">Banned</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Nov 2023, 5:20 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">9</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">24</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2342.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Robert Doe</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">robert@benko.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 11:05 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">99</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">111</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3616.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">John Miller</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">miller@mapple.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 6:43 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">13</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">26</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$124.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Lucy Kunic</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">lucy.m@fentech.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 11:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">23</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">38</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1972.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Ethan Wilder</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">ethan@loop.com.au</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>15 Apr 2023, 10:10 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">72</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">84</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3852.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Neil Owen</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">owen.neil@gmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Jul 2023, 6:43 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">97</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">106</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$472.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Emma Smith</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">smith@kpmg.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>19 Aug 2023, 8:43 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">36</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">49</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3640.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Melody Macy</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">melody@altbox.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 5:20 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">77</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">84</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1827.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Max Smith</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">max@kt.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-info">Disabled</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>21 Feb 2023, 2:40 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">79</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">88</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$1294.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Sean Bean</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">sean@dellito.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>25 Jul 2023, 6:43 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">61</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">70</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2192.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Brian Cox</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">brian@exchange.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 10:10 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">89</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">96</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$617.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Mikaela Collins</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">mik@pex.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Mar 2023, 2:40 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">1</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">11</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3044.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Francis Mitcham</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">f.mit@kpmg.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>19 Aug 2023, 5:20 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">71</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">77</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3545.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Olivia Wild</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">olivia@corpmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>22 Sep 2023, 10:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">34</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">43</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$393.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Neil Owen</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">owen.neil@gmail.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Nov 2023, 5:30 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">51</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">66</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$913.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Dan Wilson</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">dam@consilting.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Dec 2023, 11:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">42</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">49</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4476.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Emma Bold</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">emma@intenso.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>20 Jun 2023, 10:10 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">49</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">54</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4873.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Ana Crown</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">ana.cf@limtel.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>24 Jun 2023, 6:43 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">48</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">59</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$434.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Robert Doe</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">robert@benko.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-warning">Locked</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>15 Apr 2023, 11:30 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">62</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">73</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3998.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">John Miller</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">miller@mapple.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>21 Feb 2023, 2:40 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">6</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">21</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$3892.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Lucy Kunic</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">lucy.m@fentech.com</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>10 Nov 2023, 6:43 am</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">36</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">49</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$4137.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
													<!--begin::Table row-->
													<tr>
														<!--begin::Customer name=-->
														<td>
															<a href="../../demo1/dist/apps/ecommerce/customers/details.html" class="text-dark text-hover-primary">Ethan Wilder</a>
														</td>
														<!--end::Customer name=-->
														<!--begin::Email=-->
														<td>
															<a href="#" class="text-dark text-hover-primary">ethan@loop.com.au</a>
														</td>
														<!--end::Email=-->
														<!--begin::Status=-->
														<td>
															<div class="badge badge-light-success">Active</div>
														</td>
														<!--begin::Status=-->
														<!--begin::Status=-->
														<td>05 May 2023, 5:30 pm</td>
														<!--begin::Status=-->
														<!--begin::No orders=-->
														<td class="text-end pe-0">89</td>
														<!--end::No orders=-->
														<!--begin::No products=-->
														<td class="text-end pe-0">97</td>
														<!--end::No products=-->
														<!--begin::Total=-->
														<td class="text-end">$2955.00</td>
														<!--end::Total=-->
													</tr>
													<!--end::Table row-->
												</tbody>
												<!--end::Table body-->
											</table>
											<!--end::Table-->
										</div>
										<!--end::Card body-->
									</div>
									<!--end::Products-->
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

<script>
    var lpsData = @json($lps);
</script>

<script></script>
