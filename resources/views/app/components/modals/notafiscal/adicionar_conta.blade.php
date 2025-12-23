<!--begin::Modal - Adicionar Conta da Nota Fiscal-->
<div class="modal fade" id="kt_modal_adicionar_conta_notafiscal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header">
                <h2 class="fw-bold">Adicionar Conta da Nota Fiscal</h2>
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                <form id="kt_modal_adicionar_conta_form" class="form" enctype="multipart/form-data" action="{{ route('notafiscal.conta.store') }}" method="POST" onsubmit="return false;">
                    @csrf

                    <!--begin::CNPJ-->
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">CNPJ</label>
                        <input type="text"
                               name="cnpj"
                               id="cnpj_input"
                               class="form-control form-control-solid mb-3 mb-lg-0"
                               value="{{ $cnpjMatriz ?? '' }}"
                               readonly/>
                        <input type="hidden" name="cnpj_raw" value="{{ $cnpjMatrizRaw ?? '' }}" />
                        <div class="form-text">CNPJ da empresa matriz (não editável).</div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <span role="alert" id="cnpj_error" class="text-danger d-none"></span>
                            </div>
                        </div>
                    </div>
                    <!--end::CNPJ-->

                    <!--begin::Certificado A1-->
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Certificado A1</label>
                        <input type="file"
                               name="certificado_a1"
                               id="certificado_a1_input"
                               class="form-control form-control-solid mb-3 mb-lg-0"
                               accept=".pfx,.p12" />
                        <div id="certificado_file_name" class="form-text text-success fw-semibold mt-2 d-none">
                            <i class="ki-duotone ki-check-circle fs-2 text-success me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span id="certificado_file_name_text"></span>
                        </div>
                        <div class="form-text">Selecione o arquivo do certificado digital A1 (.pfx ou .p12).</div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <span role="alert" id="certificado_a1_error" class="text-danger d-none"></span>
                            </div>
                        </div>
                    </div>
                    <!--end::Certificado A1-->

                    <!--begin::Senha A1-->
                    <div class="fv-row mb-7">
                        <label class="required fw-semibold fs-6 mb-2">Senha A1</label>
                        <div class="position-relative">
                            <input type="password"
                                   name="senha_a1"
                                   id="senha_a1_input"
                                   class="form-control form-control-solid mb-3 mb-lg-0"
                                   placeholder="Digite a senha do certificado" />
                            <span class="btn btn-sm btn-icon position-absolute end-0 top-50 translate-middle-y me-2"
                                  id="toggle_senha_a1"
                                  style="cursor: pointer;">
                                <i class="ki-duotone ki-eye fs-2" id="icon_toggle_senha">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                        </div>
                        <div class="form-text">Informe a senha do certificado digital A1.</div>
                        <div class="fv-plugins-message-container">
                            <div class="fv-help-block">
                                <span role="alert" id="senha_a1_error" class="text-danger d-none"></span>
                            </div>
                        </div>
                    </div>
                    <!--end::Senha A1-->

                    <!--begin::Actions-->
                    <div class="text-center pt-5">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="kt_modal_adicionar_conta_submit">
                            <span class="indicator-label">Continuar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
            </div>
            <!--end::Modal body-->
        </div>
    </div>
</div>
<!--end::Modal - Adicionar Conta da Nota Fiscal-->

