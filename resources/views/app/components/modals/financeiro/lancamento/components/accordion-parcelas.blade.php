<!--begin::Accordion - Parcelas (para 2x ou mais)-->
<div class="accordion mb-8" id="kt_accordion_parcelas" style="display: none;">
    <div class="accordion-item">
        <h2 class="accordion-header" id="kt_accordion_parcelas_header">
            <button class="accordion-button fs-4 fw-semibold" type="button"
                data-bs-toggle="collapse" data-bs-target="#kt_accordion_parcelas_body"
                aria-expanded="true" aria-controls="kt_accordion_parcelas_body">
                Parcelas
            </button>
        </h2>
        <div id="kt_accordion_parcelas_body" class="accordion-collapse collapse show"
            aria-labelledby="kt_accordion_parcelas_header"
            data-bs-parent="#kt_accordion_parcelas">
            <div class="accordion-body">
                <!--begin::Table-->
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5"
                        id="parcelas_table">
                        <!--begin::Table head-->
                        <thead>
                            <tr
                                class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-50px">#</th>
                                <th class="min-w-100px">Vencimento</th>
                                <th class="min-w-90px">Valor (R$)</th>
                                <th class="min-w-90px">Percentual %</th>
                                <th class="min-w-220px">Forma de pagamento</th>
                                <th class="min-w-220px">Conta para Pagamento</th>
                                <th class="min-w-200px">Descrição</th>
                                <th class="min-w-120px">Agendado</th>
                            </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody class="fw-semibold text-gray-600" id="parcelas_table_body">
                            <!-- Linhas serão geradas dinamicamente via JavaScript -->
                        </tbody>
                        <!--end::Table body-->
                    </table>
                </div>
                <!--end::Table-->
            </div>
        </div>
    </div>
</div>
<!--end::Accordion-->

