<!--begin::Modal - Novo/Editar Parceiro-->
<div class="modal fade" id="modal_parceiro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_parceiro_title">Novo Parceiro</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_parceiro" autocomplete="off">
                <input type="hidden" name="parceiro_id" id="parceiro_id" value="">
                <div class="modal-body py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" 
                         data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                        
                        <!--begin::Tipo-->
                        <div class="row mb-7">
                            <div class="col-md-12">
                                <label class="required fw-semibold fs-6 mb-2">Tipo</label>
                                <select class="form-select form-select-solid" name="tipo" id="parceiro_tipo" required>
                                    <option value="fornecedor">Fornecedor (PJ)</option>
                                    <option value="cliente">Cliente (PF)</option>
                                    <option value="ambos">Ambos</option>
                                </select>
                            </div>
                        </div>
                        <!--end::Tipo-->

                        <!--begin::Nome / Razão Social-->
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <label class="required fw-semibold fs-6 mb-2">Nome / Razão Social</label>
                                <input type="text" class="form-control form-control-solid" name="nome" id="parceiro_nome" 
                                       placeholder="Nome ou Razão Social" required />
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Nome Fantasia</label>
                                <input type="text" class="form-control form-control-solid" name="nome_fantasia" id="parceiro_nome_fantasia" 
                                       placeholder="Nome Fantasia" />
                            </div>
                        </div>
                        <!--end::Nome-->

                        <!--begin::Documentos-->
                        <div class="row mb-7">
                            <div class="col-md-6" id="campo_cnpj">
                                <label class="fw-semibold fs-6 mb-2">CNPJ</label>
                                <input type="text" class="form-control form-control-solid" name="cnpj" id="parceiro_cnpj" 
                                       placeholder="00.000.000/0000-00" data-inputmask="'mask': '99.999.999/9999-99'" />
                            </div>
                            <div class="col-md-6" id="campo_cpf">
                                <label class="fw-semibold fs-6 mb-2">CPF</label>
                                <input type="text" class="form-control form-control-solid" name="cpf" id="parceiro_cpf" 
                                       placeholder="000.000.000-00" data-inputmask="'mask': '999.999.999-99'" />
                            </div>
                        </div>
                        <!--end::Documentos-->

                        <!--begin::Contato-->
                        <div class="row mb-7">
                            <div class="col-md-6">
                                <label class="fw-semibold fs-6 mb-2">Telefone</label>
                                <input type="text" class="form-control form-control-solid" name="telefone" id="parceiro_telefone" 
                                       placeholder="(00) 00000-0000" />
                            </div>
                            <div class="col-md-6">
                                <label class="fw-semibold fs-6 mb-2">E-mail</label>
                                <input type="email" class="form-control form-control-solid" name="email" id="parceiro_email" 
                                       placeholder="email@exemplo.com" />
                            </div>
                        </div>
                        <!--end::Contato-->

                        <!--begin::Endereço (collapsible)-->
                        <div class="mb-7">
                            <a class="btn btn-sm btn-light-primary" data-bs-toggle="collapse" href="#collapse_endereco" role="button">
                                <i class="bi bi-geo-alt me-1"></i> Endereço (opcional)
                            </a>
                        </div>
                        <div class="collapse" id="collapse_endereco">
                            <div class="row mb-5">
                                <div class="col-md-3">
                                    <label class="fw-semibold fs-6 mb-2">CEP</label>
                                    <input type="text" class="form-control form-control-solid" name="cep" id="parceiro_cep" 
                                           placeholder="00000-000" data-inputmask="'mask': '99999-999'" />
                                </div>
                                <div class="col-md-7">
                                    <label class="fw-semibold fs-6 mb-2">Rua / Logradouro</label>
                                    <input type="text" class="form-control form-control-solid" name="address1" id="parceiro_rua" />
                                </div>
                                <div class="col-md-2">
                                    <label class="fw-semibold fs-6 mb-2">Nº</label>
                                    <input type="text" class="form-control form-control-solid" name="numero" id="parceiro_numero" />
                                </div>
                            </div>
                            <div class="row mb-5">
                                <div class="col-md-4">
                                    <label class="fw-semibold fs-6 mb-2">Bairro</label>
                                    <input type="text" class="form-control form-control-solid" name="bairro" id="parceiro_bairro" />
                                </div>
                                <div class="col-md-5">
                                    <label class="fw-semibold fs-6 mb-2">Cidade</label>
                                    <input type="text" class="form-control form-control-solid" name="city" id="parceiro_cidade" />
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-semibold fs-6 mb-2">UF</label>
                                    <select class="form-select form-select-solid" name="country" id="parceiro_uf">
                                        <option value="">Selecione</option>
                                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                            <option value="{{ $uf }}">{{ $uf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!--end::Endereço-->

                        <!--begin::Observações-->
                        <div class="row mb-7">
                            <div class="col-md-12">
                                <label class="fw-semibold fs-6 mb-2">Observações</label>
                                <textarea class="form-control form-control-solid" name="observacoes" id="parceiro_observacoes" 
                                          rows="3" placeholder="Observações sobre o parceiro..."></textarea>
                            </div>
                        </div>
                        <!--end::Observações-->
                    </div>
                </div>

                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btn_salvar_parceiro">
                        <span class="indicator-label">Salvar</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Modal-->
