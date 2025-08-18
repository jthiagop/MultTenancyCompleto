<div class="card card-flush overflow-hidden h-md-100">
    <div class="card-header py-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-dark">Total de Despesas</span>
            <span class="text-gray-400 mt-1 fw-semibold fs-6">Despesas por período</span>
        </h3>
        <div class="card-toolbar">
            <div class="input-group w-250px me-3">
                 <input class="form-control form-control-sm form-control-solid" placeholder="Selecione um período" id="despesas_date_range_picker"/>
                 <button class="btn btn-sm btn-icon btn-light" id="despesas_date_range_picker_clear">
                    <i class="ki-duotone ki-cross fs-2"><span class="path1"></span><span class="path2"></span></i>
                </button>
            </div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-light-primary" data-range="7">7 Dias</button>
                <button type="button" class="btn btn-sm btn-light-primary active" data-range="30">30 Dias</button>
                <button type="button" class="btn btn-sm btn-light-primary" data-range="90">90 Dias</button>
            </div>
            </div>
        </div>
    <div class="card-body d-flex justify-content-between flex-column pb-1 px-0">
        <div class="px-9 mb-5">
            <div class="d-flex align-items-center mb-2">
                <span class="fs-4 fw-semibold text-gray-400 align-self-start me-1">R$</span>
                <span id="total_despesas_valor" class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2">--</span>
            </div>
            <span id="total_despesas_label" class="fs-6 fw-semibold text-gray-400">Total de despesas no período</span>
            </div>
        <div id="kt_despesas_chart" class="min-h-auto ps-4 pe-6" style="height: 300px"></div>
        </div>
    </div>

