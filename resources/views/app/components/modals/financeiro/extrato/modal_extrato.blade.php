<!--begin::Modal - Extrato Financeiro-->
<div class="modal fade" id="modal_extrato" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-top mw-650px">
        <!--begin:Form-->
        <form id="kt_modal_extrato_form" class="form" action="#">
            <!--begin::Modal content-->
            <div class="modal-content border border-active active">
                <!--begin::Modal header-->
                <div class="modal-header btn btn-sm" id="kt_modal_extrato_header">
                    <!--begin::Modal title-->
                    <h3>Extrato Financeiro</h3>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <span class="svg-icon svg-icon-1">
                            <i class="fa-solid fa-xmark fs-3"></i>
                        </span>
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body py-10 px-lg-17">
                    <!--begin::Scroll-->
                    <div class="scroll-y me-n7 pe-7" id="kt_modal_extrato_scroll" data-kt-scroll="true"
                        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_extrato_header"
                        data-kt-scroll-wrappers="#kt_modal_extrato_scroll" data-kt-scroll-offset="300px">

                        <!--begin::Input group - Tipo de Conta-->
                        <div class="row g-9 mb-8">
                            <div class="col-md-12 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Tipo de Conta</label>
                                <div class="d-flex gap-5">
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="tipo_conta" value="banco" checked />
                                        <span class="form-check-label fw-semibold text-gray-700">Banco</span>
                                    </label>
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="tipo_conta" value="caixa" />
                                        <span class="form-check-label fw-semibold text-gray-700">Caixa</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Conta Financeira-->
                        <div class="row g-9 mb-8">
                            <div class="col-md-12 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Conta Financeira</label>
                                <select name="entidade_id" id="extrato_entidade_id" class="form-select form-select-solid"
                                    data-control="select2" data-placeholder="Selecione a conta"
                                    data-dropdown-parent="#modal_extrato">
                                    <option value="">Selecione a conta</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group - Período-->
                        <div class="row g-9 mb-8">
                            <!--begin::Col - Período Inicial-->
                            <x-tenant-date name="data_inicial" id="extrato_data_inicial" label="Período Inicial"
                                placeholder="Selecione uma data"
                                class="col-md-6"
                                required />
                            <!--end::Col-->
                            <!--begin::Col - Período Final-->
                            <x-tenant-date name="data_final" id="extrato_data_final" label="Período Final"
                                placeholder="Selecione uma data"
                                class="col-md-6"
                                required />
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Notice-->
                        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                            <!--begin::Icon-->
                            <i class="fa-solid fa-circle-info fs-2tx text-primary me-4"></i>
                            <!--end::Icon-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-grow-1">
                                <!--begin::Content-->
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Informações do Extrato</h4>
                                    <div class="fs-6 text-gray-700">
                                        O extrato financeiro apresentará todas as movimentações da conta selecionada no período, com saldo progressivo (similar a um extrato bancário).
                                    </div>
                                </div>
                                <!--end::Content-->
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Notice-->
                    </div>
                    <!--end::Scroll-->
                </div>
                <!--end::Modal body-->
                <!--begin::Modal footer-->
                <div class="modal-footer flex-center btn btn-sm">
                    <!--begin::Button-->
                    <button type="reset" id="kt_modal_extrato_cancel" class="btn btn-sm btn-light me-3">
                        <i class="fa-solid fa-xmark fs-5"></i>
                        Cancelar</button>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_modal_extrato_submit" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-file-lines fs-5"></i>
                        <span class="indicator-label">Gerar Extrato</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
                <!--end::Modal footer-->
            </div>
            <!--end::Modal content-->
        </form>
        <!--end:Form-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Extrato Financeiro-->
