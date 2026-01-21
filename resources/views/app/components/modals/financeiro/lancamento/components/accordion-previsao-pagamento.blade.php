<!--begin::Accordion - Previsão de Pagamento (apenas para 1x)-->
<div class="accordion mb-8" id="kt_accordion_previsao_pagamento" style="display: none;">
    <div class="accordion-item">
        <h2 class="accordion-header" id="kt_accordion_previsao_header">
            <button class="accordion-button fs-4 fw-semibold" type="button"
                data-bs-toggle="collapse" data-bs-target="#kt_accordion_previsao_body"
                aria-expanded="true" aria-controls="kt_accordion_previsao_body">
                Previsão de pagamento
            </button>
        </h2>
        <div id="kt_accordion_previsao_body" class="accordion-collapse collapse show"
            aria-labelledby="kt_accordion_previsao_header"
            data-bs-parent="#kt_accordion_previsao_pagamento">
            <div class="accordion-body">
                <!--begin::Row - Previsão de pagamento e Juros-->
                <div class="row g-9 mb-8">
                    <!--begin::Col - Previsão de pagamento-->
                    <x-tenant-date name="previsao_pagamento" id="previsao_pagamento"
                        label="Previsão de pagamento"
                        placeholder="Informe a data de previsão"
                        value="{{ old('previsao_pagamento', now()->format('d/m/Y')) }}"
                        class="col-md" required />
                    <!--end::Col-->

                    <!--begin::Col - Juros-->
                    <x-tenant-currency name="juros" id="juros" label="Juros"
                        placeholder="0,00" class="col-md" />
                    <!--end::Col-->

                    <!--begin::Col - Multa-->
                    <x-tenant-currency name="multa" id="multa" label="Multa"
                        placeholder="0,00" class="col-md" />
                    <!--end::Col-->

                    <!--begin::Col - Desconto-->
                    <x-tenant-currency name="desconto" id="desconto" label="Desconto"
                        placeholder="0,00" class="col-md" />
                    <!--end::Col-->

                    <!--begin::Col - Valor a Pagar-->
                    <x-tenant-currency name="valor_a_pagar" id="valor_a_pagar"
                        label="Valor a Pagar" placeholder="0,00" required
                        tooltip="Valor total a ser pago (calculado automaticamente: Valor + Juros + Multa - Desconto)"
                        class="col-md" readonly />
                    <!--end::Col-->
                </div>
                <!--end::Row-->
            </div>
        </div>
    </div>
</div>
<!--end::Accordion-->

