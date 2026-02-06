<!--begin::Card - Parcelas Somente Leitura (exibido apenas na edição de transações parceladas)-->
<div class="card mb-xl-10" id="card_parcelas_readonly" style="display: none;">
    <div class="card-header">
        <div class="app-toolbar py-3 py-lg-6 d-flex justify-content-between align-items-center w-100">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap">
                <h3 class="card-title mb-0">
                    <i class="bi bi-signpost-split text-primary me-2 fs-3"></i>Parcelas
                </h3>
            </div>
            <div>
                <span class="badge badge-light-primary fs-7" id="card_parcelas_readonly_badge"></span>
            </div>
        </div>
    </div>
    <div class="card-body px-10 py-6">
        <!--begin::Info banner para parcela filha-->
        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5" id="card_parcela_filha_info" style="display: none;">
            <i class="bi bi-info-circle text-info fs-2 me-3"></i>
            <div class="d-flex flex-stack flex-grow-1">
                <div class="fw-semibold">
                    <div class="fs-6 text-gray-700" id="card_parcela_filha_info_text"></div>
                </div>
            </div>
        </div>
        <!--end::Info banner-->

        <!--begin::Table-->
        <div class="table-responsive" id="card_parcelas_readonly_table_wrapper">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">
                <thead>
                    <tr class="fw-bold text-muted fs-7">
                        <th class="min-w-40px">#</th>
                        <th class="min-w-90px">Vencimento</th>
                        <th class="min-w-80px text-end">Valor</th>
                        <th class="min-w-80px">Situação</th>
                        <th class="min-w-40px text-center">Ação</th>
                    </tr>
                </thead>
                <tbody id="card_parcelas_readonly_tbody">
                    <!-- Preenchido via JavaScript -->
                </tbody>
            </table>
        </div>
        <!--end::Table-->
    </div>
</div>
<!--end::Card - Parcelas Somente Leitura-->
