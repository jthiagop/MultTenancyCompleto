<!--begin::Row-->
<div class="row g-5 g-xl-10 mb-xl-10">
    <!--begin::Col-->
    <div class="col-xl-12 mb-5 mb-xl-6">
        <!--begin::Chart widget combinado-->
        <div class="card card-flush overflow-hidden h-md-100">
            <!--begin::Header-->
            <div class="card-header py-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Movimentações Bancárias</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Entradas e Saídas por Período</span>
                </h3>
                <!--end::Title-->
                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <!--begin::Filtros-->
                    <div class="d-flex align-items-center gap-3">
                        <!-- Filtro de Mês -->
                        <select id="chart-month-filter" class="form-select form-select-sm w-150px">
                            <option value="1" {{ date('n') == 1 ? 'selected' : '' }}>Janeiro</option>
                            <option value="2" {{ date('n') == 2 ? 'selected' : '' }}>Fevereiro</option>
                            <option value="3" {{ date('n') == 3 ? 'selected' : '' }}>Março</option>
                            <option value="4" {{ date('n') == 4 ? 'selected' : '' }}>Abril</option>
                            <option value="5" {{ date('n') == 5 ? 'selected' : '' }}>Maio</option>
                            <option value="6" {{ date('n') == 6 ? 'selected' : '' }}>Junho</option>
                            <option value="7" {{ date('n') == 7 ? 'selected' : '' }}>Julho</option>
                            <option value="8" {{ date('n') == 8 ? 'selected' : '' }}>Agosto</option>
                            <option value="9" {{ date('n') == 9 ? 'selected' : '' }}>Setembro</option>
                            <option value="10" {{ date('n') == 10 ? 'selected' : '' }}>Outubro</option>
                            <option value="11" {{ date('n') == 11 ? 'selected' : '' }}>Novembro</option>
                            <option value="12" {{ date('n') == 12 ? 'selected' : '' }}>Dezembro</option>
                        </select>
                        
                        <!-- Filtro de Ano -->
                        <select id="chart-year-filter" class="form-select form-select-sm w-100px">
                            @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                                <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        
                        <!-- Filtro de Banco -->
                        <select id="chart-bank-filter" class="form-select form-select-sm w-200px">
                            <option value="">Todos os Bancos</option>
                            @foreach($entidadesBanco as $banco)
                                <option value="{{ $banco->id }}">{{ $banco->nome }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Botão Atualizar -->
                        <button id="refresh-chart" class="btn btn-sm btn-primary">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                    <!--end::Filtros-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Header-->
            <!--begin::Card body-->
            <div class="card-body d-flex justify-content-between flex-column pb-1 px-0">
                <!--begin::Info-->
                <div class="px-9 mb-5">
                    <div class="row">
                        <!-- Total Entradas -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-4 fw-semibold text-gray-400 align-self-start me-1">R$</span>
                                <span id="total-entradas" class="fs-2hx fw-bold text-success me-2 lh-1 ls-n2">0,00</span>
                            </div>
                            <span class="fs-6 fw-semibold text-gray-400">Total de Entradas</span>
                        </div>
                        
                        <!-- Total Saídas -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-4 fw-semibold text-gray-400 align-self-start me-1">R$</span>
                                <span id="total-saidas" class="fs-2hx fw-bold text-danger me-2 lh-1 ls-n2">0,00</span>
                            </div>
                            <span class="fs-6 fw-semibold text-gray-400">Total de Saídas</span>
                        </div>
                        
                        <!-- Saldo -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="fs-4 fw-semibold text-gray-400 align-self-start me-1">R$</span>
                                <span id="saldo-total" class="fs-2hx fw-bold text-primary me-2 lh-1 ls-n2">0,00</span>
                            </div>
                            <span class="fs-6 fw-semibold text-gray-400">Saldo do Período</span>
                        </div>
                    </div>
                </div>
                <!--end::Info-->
                <!--begin::Chart-->
                <div id="kt_charts_widget_combined" class="min-h-auto ps-4 pe-6" style="height: 400px"></div>
                <!--end::Chart-->
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Chart widget combinado-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
