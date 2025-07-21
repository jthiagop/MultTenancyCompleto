<!--begin::Modal - Emitir Ptam-->
<div class="modal fade" id="kt_modal_emitir_ptam" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header pb-0 border-0 justify-content-end">
                <!--begin::Close-->
                <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </div>
                <!--end::Close-->
            </div>
            <!--begin::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <!--begin:Form-->
                <form id="kt_modal_emitir_ptam_confirm_form" class="form" action="" method="POST">
                    @csrf
                    <!--begin::Heading-->
                    <div class="mb-13 text-center">
                        <h1 class="mb-3">Confirmar Geração de PTAM</h1>
                        <div class="text-muted fw-semibold fs-5">Revise os detalhes abaixo antes de gerar o PTAM.</div>
                    </div>
                    <!--end::Heading-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Tipo de PTAM</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" name="ptam_type_display" id="ptam_type_display" readonly />
                        <input type="hidden" name="ptam_type" id="ptam_type_confirm" />
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row" id="dominio_direto_percentage_confirm" style="display: none;">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span>Percentual (Domínio Direto)</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" name="custom_percentage_display" id="custom_percentage_display" readonly />
                        <input type="hidden" name="custom_percentage" id="custom_percentage_confirm" />
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span>Valor do Imóvel</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" value="" readonly />
                        <input type="hidden" name="valor_imovel" value="{{ $escrituraAtual->valor ?? 0 }}">
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span>Valor Calculado</span>
                        </label>
                        <input type="text" class="form-control form-control-solid" name="valor_calculado_display" id="valor_calculado_display" readonly />
                        <input type="hidden" name="valor_calculado" id="valor_calculado_confirm" />
                    </div>
                    <!--end::Input group-->
                    <!--begin::Input group-->
                    <div class="d-flex flex-column mb-8 fv-row">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Observações</span>
                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="Adicione observações para o PTAM, se necessário"></i>
                        </label>
                        <textarea class="form-control form-control-solid" rows="3" name="observacoes" placeholder="Digite observações adicionais"></textarea>
                    </div>
                    <!--end::Input group-->
                    <!--begin::Hidden inputs-->
                    <input type="hidden" name="patrimonio_id" value="{{ $patrimonio->id }}">
                    <!--end::Hidden inputs-->
                    <!--begin::Actions-->
                    <div class="text-center">
                        <button type="reset" id="kt_modal_emitir_ptam_cancel" class="btn btn-light me-3">Cancelar</button>
                        <button type="submit" id="kt_modal_emitir_ptam_submit" class="btn btn-primary">
                            <span class="indicator-label">Gerar PTAM</span>
                            <span class="indicator-progress">Por favor, aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
                <!--end:Form-->
            </div>
            <!--end::Modal body-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Emitir Ptam-->
