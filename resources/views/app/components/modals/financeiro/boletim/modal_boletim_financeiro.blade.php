<!--begin::Modal - Boletim Financeiro-->
<div class="modal fade" id="modal_boletim_financeiro" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-top mw-650px "> 
        <!--begin:Form-->
        <form id="kt_modal_boletim_financeiro_form" class="form" action="#">
            <!--begin::Modal content-->
            <div class="modal-content border border-active active">
                <!--begin::Modal header-->
                <div class="modal-header btn btn-sm" id="kt_modal_boletim_header">
                    <!--begin::Modal title-->
                    <h3>Boletim Financeiro</h3>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                            <i class="fa-solid fa-xmark fs-3"></i>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body py-10 px-lg-17">
                    <!--begin::Scroll-->
                    <div class="scroll-y me-n7 pe-7" id="kt_modal_boletim_scroll" data-kt-scroll="true"
                        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_boletim_header"
                        data-kt-scroll-wrappers="#kt_modal_boletim_scroll" data-kt-scroll-offset="300px">
                        
                        <!--begin::Input group-->
                        <div class="row g-9 mb-8">
                            <!--begin::Col - Período Inicial-->
                            <x-tenant-date name="data_inicial" id="boletim_data_inicial" label="Período Inicial"
                                placeholder="Selecione uma data"
                                class="col-md-6"
                                required />
                            <!--end::Col-->
                            <!--begin::Col - Período Final-->
                            <x-tenant-date name="data_final" id="boletim_data_final" label="Período Final"
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
                                    <h4 class="text-gray-900 fw-bold">Informações do Boletim</h4>
                                    <div class="fs-6 text-gray-700">
                                        O boletim financeiro apresentará um resumo completo das movimentações financeiras no período selecionado, incluindo entradas, saídas e saldo.
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
                    <button type="reset" id="kt_modal_boletim_cancel" class="btn btn-sm btn-light me-3">
                        <i class="fa-solid fa-xmark fs-5"></i>
                        Cancelar</button>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_modal_boletim_submit" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-file-lines fs-5"></i>

                        <span class="indicator-label">Gerar Boletim</span>
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
<!--end::Modal - Boletim Financeiro-->
