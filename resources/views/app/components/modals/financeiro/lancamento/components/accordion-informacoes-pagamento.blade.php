<!--begin::Accordion - Informações do Pagamento (quando Pago está marcado)-->
<div class="accordion mb-8" id="kt_accordion_informacoes_pagamento"
    style="display: none;">
    <div class="accordion-item">
        <h2 class="accordion-header" id="kt_accordion_informacoes_pagamento_header">
            <button class="accordion-button fs-4 fw-semibold" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#kt_accordion_informacoes_pagamento_body"
                aria-expanded="true"
                aria-controls="kt_accordion_informacoes_pagamento_body">
                Informações do pagamento
            </button>
        </h2>
        <div id="kt_accordion_informacoes_pagamento_body"
            class="accordion-collapse collapse show"
            aria-labelledby="kt_accordion_informacoes_pagamento_header"
            data-bs-parent="#kt_accordion_informacoes_pagamento">
            <div class="accordion-body">
                <!--begin::Row - Campos de pagamento-->
                <div class="row g-9 mb-8">
                    <!--begin::Col - Data do pagamento-->
                    <x-tenant-date name="data_pagamento" id="data_pagamento"
                        label="Data do pagamento"
                        placeholder="Informe a data do pagamento"
                        value="{{ old('data_pagamento', now()->format('d/m/Y')) }}"
                        class="col-md" required />
                    <!--end::Col-->

                    <!--begin::Col - Valor pago-->
                    <x-tenant-currency name="valor_pago" id="valor_pago"
                        label="Valor pago" placeholder="0,00" class="col-md" required />
                    <!--end::Col-->

                    <!--begin::Col - Juros-->
                    <x-tenant-currency name="juros_pagamento" id="juros_pagamento"
                        label="Juros" placeholder="0,00" class="col-md" />
                    <!--end::Col-->

                    <!--begin::Col - Multa-->
                    <x-tenant-currency name="multa_pagamento" id="multa_pagamento"
                        label="Multa" placeholder="0,00" class="col-md" />
                    <!--end::Col-->

                    <!--begin::Col - Desconto-->
                    <x-tenant-currency name="desconto_pagamento" id="desconto_pagamento"
                        label="Desconto" placeholder="0,00" class="col-md" />
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Row - Resumo-->
                <div class="row g-9 mb-8">
                    <!--begin::Col - Total a pagar-->
                    <div class="col-md-6" id="total_pagar_container"
                        style="display: none;">
                        <div class="fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Total a pagar</span>
                            </label>
                            <div class="fs-3 fw-bold text-success"
                                id="total_pagar_display">R$ 0,00</div>
                        </div>
                    </div>
                    <!--end::Col-->

                    <!--begin::Col - Valor em Aberto-->
                    <div class="col-md-6" id="valor_aberto_container"
                        style="display: none;">
                        <div class="fv-row">
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                <span>Valor em Aberto</span>
                            </label>
                            <div class="fs-3 fw-bold text-primary"
                                id="valor_aberto_display">R$ 0,00</div>
                        </div>
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Row-->

                <!--begin::Accordion - Resumo da baixa-->
                <div class="accordion mb-8" id="kt_accordion_resumo_baixa">
                    <div class="accordion-item">
                        <h2 class="accordion-header"
                            id="kt_accordion_resumo_baixa_header">
                            <button class="accordion-button fs-4 fw-semibold collapsed"
                                type="button" data-bs-toggle="collapse"
                                data-bs-target="#kt_accordion_resumo_baixa_body"
                                aria-expanded="false"
                                aria-controls="kt_accordion_resumo_baixa_body">
                                Resumo da baixa
                            </button>
                        </h2>
                        <div id="kt_accordion_resumo_baixa_body"
                            class="accordion-collapse collapse"
                            aria-labelledby="kt_accordion_resumo_baixa_header"
                            data-bs-parent="#kt_accordion_resumo_baixa">
                            <div class="accordion-body">
                                <!--begin::Table-->
                                <div class="table-responsive">
                                    <table
                                        class="table align-middle table-row-dashed fs-6 gy-5"
                                        id="resumo_baixa_table">
                                        <!--begin::Table head-->
                                        <thead>
                                            <tr
                                                class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-100px">Data</th>
                                                <th class="min-w-150px">Forma de pagamento
                                                </th>
                                                <th class="min-w-150px">Conta</th>
                                                <th class="min-w-100px text-end">Valor R$
                                                </th>
                                                <th class="min-w-120px text-end">
                                                    Juros/Multa R$</th>
                                                <th class="min-w-120px text-end">
                                                    Desconto/Tarifas R$</th>
                                                <th class="min-w-100px">Situação</th>
                                            </tr>
                                        </thead>
                                        <!--end::Table head-->
                                        <!--begin::Table body-->
                                        <tbody class="text-gray-600 fw-semibold"
                                            id="resumo_baixa_tbody">
                                            <!-- As linhas serão geradas dinamicamente via JavaScript -->
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
            </div>
        </div>
    </div>
</div>
<!--end::Accordion-->

