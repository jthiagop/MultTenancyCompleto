<!--begin::Card-->
<div class="card mb-xl-10 ">
    <div class="card-header">
        <div id="kt_app_toolbar"
            class="app-toolbar py-3 py-lg-6 d-flex justify-content-between align-items-center w-100">
            <!--begin::Coluna Esquerda - Título-->
            <div class="page-title d-flex flex-column justify-content-center flex-wrap">
                <h3 class="card-title mb-0">Condição de pagamento</h3>
            </div>
            <!--end::Coluna Esquerda-->

            <!--begin::Coluna Direita - Checkbox-->
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <!--begin::Label-->
                <div class="me-5">
                    <label class="fs-5 fw-semibold">Existe comprovação fiscal?</label>
                    <div class="fs-7 fw-semibold text-muted">Documentos que comprovam
                        transações financeiras</div>
                </div>
                <!--end::Label-->
                <!-- Input Hidden para garantir o envio de "0" quando desmarcado -->
                <input type="hidden" name="comprovacao_fiscal" value="0">
                <!--begin::Switch-->
                <label class="form-check form-switch form-check-custom form-check-solid">
                    <!-- Checkbox para enviar 1 quando marcado -->
                    <input class="form-check-input" type="checkbox" name="comprovacao_fiscal"
                        value="1" id="comprovacao_fiscal_checkbox" />
                    <span class="form-check-label fw-semibold text-muted">Possui Nota</span>
                </label>
                <!--end::Switch-->
            </div>
            <!--end::Coluna Direita-->
        </div>
    </div>
    <div class="card-body px-10">

        <!--begin::Input group-->
        <div class="d-flex flex-stack w-lg-50 g-9 mb-5">
            <!--begin::Linha: Checkbox e Select de Recorrência-->
            <div class="d-flex align-items-center gap-5 w-100">
                <div class="form-check form-switch form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" role="switch" name="repetir_lancamento" value="1"
                        id="flexSwitchDefault" />
                    <label class="form-check-label" for="flexSwitchDefault">
                        Repetir lançamento?
                    </label>
                </div>

                <!--begin::Configurações de Repetição (inicialmente oculto)-->
                <div class="flex-grow-1" id="configuracao-recorrencia-wrapper"
                    style="display: none;">
                    <x-tenant-select name="configuracao_recorrencia" id="configuracao_recorrencia"
                        placeholder="Selecione uma configuração" :allowClear="true"
                        :minimumResultsForSearch="0" dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6"
                        class="mb-0">
                        <option value="">Nenhuma configuração selecionada</option>
                    </x-tenant-select>
                </div>
                <!--end::Configurações de Repetição-->
            </div>
            <!--end::Linha: Checkbox e Select de Recorrência-->
        </div>
        <!--end::Input group-->

        <!--begin::Input group - Parcelamento e Vencimento-->
        <div class="row g-9 mb-8 align-items-end">
            <!--begin::Col - Parcelamento-->
            <div id="parcelamento_wrapper" class="col-md-2">
                <x-tenant-select name="parcelamento" id="parcelamento" label="Parcelamento"
                    placeholder="Selecione" required :allowClear="false"
                    :minimumResultsForSearch="0" dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6"
                    class="w-100">
                    <option value="avista"
                        {{ old('parcelamento', 'avista') == 'avista' ? 'selected' : '' }}>À Vista
                    </option>
                    @for ($i = 1; $i <= 100; $i++)
                        <option value="{{ $i }}x"
                            {{ old('parcelamento') == $i . 'x' ? 'selected' : '' }}>
                            {{ $i }}x</option>
                    @endfor
                </x-tenant-select>
            </div>
            <!--end::Col-->

            <!--begin::Col - Dia de Cobrança (Recorrência)-->
            <div id="dia_cobranca_wrapper" class="col-md-2" style="display: none;">
                <x-tenant-select name="dia_cobranca" id="dia_cobranca" label="Dia de Cobrança"
                    :allowClear="false"
                    :hidePlaceholder="true"
                    :minimumResultsForSearch="0" dropdown-parent="{{ $dropdownParent ?? '#Dm_modal_financeiro' }}" labelSize="fs-6"
                    class="w-100">
                    @for ($i = 1; $i <= 30; $i++)
                        <option value="{{ $i }}">{{ $i }}º dia do mês</option>
                    @endfor
                    <option value="ultimo">Último dia do mês</option>
                </x-tenant-select>
            </div>
            <!--end::Col-->

            <!--begin::Col - Vencimento-->
            <x-tenant-date name="vencimento" id="vencimento" label="Vencimento"
                placeholder="Informe a data de vencimento"
                class="col-md-2"
                required />
            <!--end::Col-->

            <!--begin::Col - Checkboxes por Tipo (Entrada vs Saída)-->
            <div class="col-md-6 d-flex align-items-end gap-5 pb-2">
                
                <!--begin::Wrapper Checkboxes Entrada (Receita) - Apenas Recebido-->
                <div id="checkboxes-entrada-wrapper" style="display: none;">
                    <!--begin::Checkbox Recebido (só aparece se parcelamento for À vista ou 1x)-->
                    <div id="checkbox-recebido-wrapper" style="display: none;">
                        <x-tenant-checkbox name="recebido" id="recebido_checkbox" label="Recebido"
                            tooltipTitle="Marcar como já recebido"
                            dynamicTooltipField="vencimento"
                            dynamicTooltipPrefix="Marcar como já recebido em "
                            dynamicTooltipSuffix="." />
                    </div>
                    <!--end::Checkbox Recebido-->
                </div>
                <!--end::Wrapper Checkboxes Entrada-->
                
                <!--begin::Wrapper Checkboxes Saída (Despesa) - Pago e Agendado-->
                <div id="checkboxes-saida-wrapper" style="display: none;">
                    <!--begin::Checkbox Pago (só aparece se parcelamento for À vista ou 1x)-->
                    <div id="checkbox-pago-wrapper" style="display: none;">
                        <x-tenant-checkbox name="pago" id="pago_checkbox" label="Pago"
                            tooltipTitle="Marcar como pago" />
                    </div>
                    <!--end::Checkbox Pago-->

                    <!--begin::Checkbox Agendado-->
                    <div id="checkbox-agendado-wrapper">
                        <x-tenant-checkbox name="agendado" id="agendado_checkbox" label="Agendado"
                            tooltipTitle="O pagamento será agendado para a data do campo Vencimento, mas não será marcado como pago automaticamente. Ele será marcado como pago apenas quando você fizer isso manualmente."
                            dynamicTooltipField="vencimento"
                            dynamicTooltipPrefix="O pagamento será agendado para a data do campo Vencimento ("
                            dynamicTooltipSuffix="), mas não será marcado como pago automaticamente. Ele será marcado como pago apenas quando você fizer isso manualmente." />
                    </div>
                    <!--end::Checkbox Agendado-->
                </div>
                <!--end::Wrapper Checkboxes Saída-->
                
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group - Parcelamento e Vencimento-->

        @include('app.components.modals.financeiro.lancamento.components.accordion-previsao-pagamento')

        @include('app.components.modals.financeiro.lancamento.components.accordion-informacoes-pagamento')

        @include('app.components.modals.financeiro.lancamento.components.accordion-parcelas')

    </div>
</div>
<!--end::Card-->

