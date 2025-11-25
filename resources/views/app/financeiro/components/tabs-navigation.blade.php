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
            data-kt-menu-placement="bottom-end" title="Adicionar novo lançamento" aria-label="Adicionar novo lançamento">
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

<!--begin::Filtros Avançados-->
<div class="card-body border-top">
    <div class="row g-4">
        <!--begin::Filtro de Vencimento-->
        <div class="col-md-3">
            <label class="form-label fw-semibold">Vencimento</label>
            <div class="input-group">
                <button class="btn btn-icon btn-light-primary" type="button" id="prevMonth">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <input type="text" class="form-control text-center" id="monthSelector"
                    value="{{ now()->format('F Y') }}" readonly>
                <button class="btn btn-icon btn-light-primary" type="button" id="nextMonth">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <button class="btn btn-icon btn-light-primary dropdown-toggle" type="button"
                    data-bs-toggle="dropdown">
                    <i class="fas fa-calendar"></i>
                </button>
                <ul class="dropdown-menu" id="monthDropdown">
                    <!-- Preenchido via JavaScript -->
                </ul>
            </div>
        </div>
        <!--end::Filtro de Vencimento-->

        <!--begin::Filtro de Status-->
        <div class="col-md-2">
            <label class="form-label fw-semibold">Status</label>
            <select class="form-select" id="statusFilter">
                <option value="">Todos os status</option>
                <option value="em aberto">Em Aberto</option>
                <option value="pendente">Pendente</option>
                <option value="pago">Pago</option>
                <option value="vencido">Vencido</option>
                <option value="cancelado">Cancelado</option>
            </select>
        </div>
        <!--end::Filtro de Status-->

        <!--begin::Filtro de Conta-->
        <div class="col-md-3">
            <label class="form-label fw-semibold">Conta</label>
            <select class="form-select" id="accountFilter">
                <option value="">Selecionar todas</option>
                <!-- Preenchido via JavaScript -->
            </select>
        </div>
        <!--end::Filtro de Conta-->

        <!--begin::Filtro de Fornecedor-->
        <div class="col-md-2">
            <label class="form-label fw-semibold">Fornecedor</label>
            <select class="form-select" id="supplierFilter">
                <option value="">Todos</option>
                <!-- Preenchido via JavaScript -->
            </select>
        </div>
        <!--end::Filtro de Fornecedor-->

        <!--begin::Mais Filtros-->
        <div class="col-md-2">
            <label class="form-label fw-semibold">&nbsp;</label>
            <div class="dropdown">
                <button class="btn btn-light-primary dropdown-toggle w-100" type="button"
                    data-bs-toggle="dropdown">
                    <i class="fas fa-filter me-2"></i>Mais filtros
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-filter="valor">
                        <i class="fas fa-dollar-sign me-2"></i>Por Valor
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-filter="data">
                        <i class="fas fa-calendar-alt me-2"></i>Por Data
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-filter="categoria">
                        <i class="fas fa-tags me-2"></i>Por Categoria
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-filter="limpar">
                        <i class="fas fa-times me-2"></i>Limpar Filtros
                    </a></li>
                </ul>
            </div>
        </div>
        <!--end::Mais Filtros-->
    </div>
</div>
<!--end::Filtros Avançados-->

<!--begin::Cards de Resumo-->
<div class="card-body border-top">
    <div class="row g-4">
        <!--begin::Vencidos-->
        <div class="col-md-2">
            <div class="card card-flush h-100 border border-danger border-dashed">
                <div class="card-body text-center p-4">
                    <div class="text-muted small mb-2">Vencidos (R$)</div>
                    <div class="fw-bold text-danger fs-3">{{ number_format($receitasVencidas ?? $despesasVencidas ?? 0, 2, ',', '.') }}</div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Requer atenção
                    </div>
                </div>
            </div>
        </div>
        <!--end::Vencidos-->

        <!--begin::Vencem Hoje-->
        <div class="col-md-2">
            <div class="card card-flush h-100 border border-warning border-dashed">
                <div class="card-body text-center p-4">
                    <div class="text-muted small mb-2">Vencem hoje (R$)</div>
                    <div class="fw-bold text-warning fs-3">{{ number_format($receitasVencemHoje ?? $despesasVencemHoje ?? 0, 2, ',', '.') }}</div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-clock me-1"></i>
                        Último dia
                    </div>
                </div>
            </div>
        </div>
        <!--end::Vencem Hoje-->

        <!--begin::A Vencer-->
        <div class="col-md-2">
            <div class="card card-flush h-100 border border-primary border-dashed">
                <div class="card-body text-center p-4">
                    <div class="text-muted small mb-2">A vencer (R$)</div>
                    <div class="fw-bold text-primary fs-3">{{ number_format($TotalreceitasAVencer ?? $despesasAVencer ?? 0, 2, ',', '.') }}</div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-calendar me-1"></i>
                        Próximos dias
                    </div>
                </div>
            </div>
        </div>
        <!--end::A Vencer-->

        <!--begin::Pagos-->
        <div class="col-md-2">
            <div class="card card-flush h-100 border border-success border-dashed">
                <div class="card-body text-center p-4">
                    <div class="text-muted small mb-2">Pagos (R$)</div>
                    <div class="fw-bold text-success fs-3">{{ number_format($receitasPagas ?? $despesasPagas ?? 0, 2, ',', '.') }}</div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-check-circle me-1"></i>
                        Concluído
                    </div>
                </div>
            </div>
        </div>
        <!--end::Pagos-->

        <!--begin::Total do Período-->
        <div class="col-md-4">
            <div class="card card-flush h-100 border border-info border-dashed">
                <div class="card-body text-center p-4">
                    <div class="text-muted small mb-2">
                        Total do período (R$)
                        <i class="fas fa-question-circle ms-1" data-bs-toggle="tooltip"
                            title="Total de movimentações do período selecionado"></i>
                    </div>
                    <div class="fw-bold text-info fs-3">{{ number_format($valorTotal ?? 0, 2, ',', '.') }}</div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-chart-line me-1"></i>
                        Período atual
                    </div>
                </div>
            </div>
        </div>
        <!--end::Total do Período-->
    </div>
</div>
<!--end::Cards de Resumo-->

<!--begin::Barra de Ações-->
<div class="card-body border-top">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="text-muted">
                <span id="selectedCount">0</span> registro(s) selecionado(s)
            </div>
            <div class="vr"></div>
            <div class="text-muted">
                Total: <span id="totalRecords">{{ count($receitasEmAberto ?? []) + count($despesasEmAberto ?? []) }}</span> registros
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" id="actionButton" disabled>
                <i class="fas fa-money-bill-wave me-2"></i>
                <span id="actionButtonText">Receber</span>
            </button>
            <div class="dropdown">
                <button class="btn btn-light-primary dropdown-toggle" type="button"
                    data-bs-toggle="dropdown" disabled id="batchActionsButton">
                    <i class="fas fa-tasks me-2"></i>
                    Ações em lote
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" data-action="marcar-pago">
                        <i class="fas fa-check me-2"></i>Marcar como Pago
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-action="exportar">
                        <i class="fas fa-download me-2"></i>Exportar
                    </a></li>
                    <li><a class="dropdown-item" href="#" data-action="imprimir">
                        <i class="fas fa-print me-2"></i>Imprimir
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#" data-action="excluir">
                        <i class="fas fa-trash me-2"></i>Excluir
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!--end::Barra de Ações-->
