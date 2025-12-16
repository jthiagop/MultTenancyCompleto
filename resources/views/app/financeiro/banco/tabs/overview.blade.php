<!--begin::Row-->
<div class="row g-5 g-xl-10 mb-xl-10">
    <!--begin::Col-->
    <div class="col-xl-12 mb-5 mb-xl-6">
        <!--begin::Chart widget 36-->
        <div class="card card-flush overflow-hidden h-xl-100">
            <!--begin::Header-->
            <div class="card-header pt-5">
                <!--begin::Title-->
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-dark">Gráfico de Fluxo de Banco</span>
                    <span class="text-gray-400 mt-1 fw-semibold fs-6">Fluxo de entradas e saídas financeiras</span>
                </h3>
                <!--end::Title-->

                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <!--begin::Group By Select-->
                    <div class="me-3">
                        <select id="group-by-select" class="form-select form-select-sm form-select-solid w-150px"
                            data-control="select2" data-hide-search="true" data-placeholder="Agrupar por">
                            <option value="auto">Automático</option>
                            <option value="day">Diário</option>
                            <option value="week">Semanal</option>
                            <option value="month">Mensal</option>
                        </select>
                    </div>
                    <!--end::Group By Select-->
                    <!--begin::Daterangepicker(defined in src/js/layout/app.js)-->
                    <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left"
                        data-kt-daterangepicker-range="this month"
                        class="btn btn-sm btn-light d-flex align-items-center px-4">
                        <!--begin::Display range-->
                        <i class="bi bi-calendar-date me-1 text-primary"></i>
                        <div class="text-gray-600 fw-bold">Selecione um período</div>
                        <!--end::Display range-->
                        <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Daterangepicker-->
                </div>
                <!--end::Toolbar-->
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body">
                <!--begin::Statistics-->
                <div class="d-flex align-items-center mb-2">
                    <!--begin::Statistics-->
                    <div class="d-flex align-items-center">
                        <span class="fs-1 fw-semibold text-gray-400 me-1 mt-n1">R$</span>
                        <span class="fs-3x fw-bold text-gray-800 me-2 lh-1 ls-n2" id="saldo-periodo">0,00</span>
                        <span class="badge badge-light-success fs-base" id="percentual-saldo" style="display: none;">
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                            <i class="bi bi-graph-up-arrow me-2 text-success"></i>
                            <!--end::Svg Icon-->
                            <span id="percentual-texto">0%</span>
                        </span>
                    </div>
                    <!--end::Statistics-->
                    <!--begin::Description-->
                    <span class="fs-6 fw-semibold text-gray-400">Saldo do período selecionado</span>
                    <!--end::Description-->
                </div>
                <!--end::Statistics-->
            </div>
            <!--begin::Card body-->
            <div class="card-body d-flex align-items-end p-0">
                <!--begin::Chart-->
                <div id="kt_charts_widget_overview" class="min-h-auto w-100 ps-4 pe-6" style="height: 300px"></div>
                <!--end::Chart-->
            </div>
            <!--end::Card body-->
            <!--begin::Load More Button-->
            <div class="card-footer text-center" id="chart-load-more-container" style="display: none;">
                <button type="button" id="chart-load-more-btn" class="btn btn-sm btn-light-primary">
                    <span class="indicator-label">Carregar Mais Dados</span>
                    <span class="indicator-progress">Carregando...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
                <div class="text-muted fs-7 mt-2" id="chart-data-info"></div>
            </div>
            <!--end::Load More Button-->
        </div>
        <!--end::Chart widget 36-->
    </div>
    <!--end::Col-->
</div>
<!--end::Row-->
