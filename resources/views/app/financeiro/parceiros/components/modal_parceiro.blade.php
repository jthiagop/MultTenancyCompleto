<!--begin::Modal - Novo/Editar Parceiro-->
<div class="modal fade" id="modal_parceiro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modal_parceiro_title">Novo Cadastro</h3>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <form id="form_parceiro" autocomplete="off">
                <input type="hidden" name="parceiro_id" id="parceiro_id" value="">
                <input type="hidden" name="natureza" id="parceiro_natureza" value="fornecedor">
                <div class="modal-body py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" 
                         data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">

                        <!--begin::Tipo de Pessoa + Documento-->
                        <div class="row mb-7">
                            <!--begin::Col - Tipo de Pessoa-->
                            <div class="col-md-4 fv-row">
                                <x-tenant-select
                                    name="tipo"
                                    id="parceiro_tipo"
                                    label="Tipo de Pessoa"
                                    :required="true"
                                    class="w-100"
                                    :hideSearch="true"
                                    dropdownParent="#modal_parceiro"
                                    control="select2"
                                    value="pj">
                                    <option value="pj">Pessoa Jurídica</option>
                                    <option value="pf">Pessoa Física</option>
                                </x-tenant-select>
                            </div>
                            <!--end::Col-->

                            <!--begin::Col - CNPJ (Pessoa Jurídica)-->
                            <div class="col-md-8 fv-row" id="campo_cnpj">
                                <label class="fs-6 fw-semibold mb-2">CNPJ</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="cnpj" id="cnpj"
                                        placeholder="00.000.000/0000-00" />
                                    <button type="button" class="btn btn-light-primary" id="btn-consultar-cnpj">
                                        <i class="bi bi-search"></i> Consultar
                                    </button>
                                </div>
                            </div>
                            <!--end::Col-->

                            <!--begin::Col - CPF (Pessoa Física)-->
                            <div class="col-md-8 fv-row" id="campo_cpf" style="display: none;">
                                <label class="fs-6 fw-semibold mb-2">CPF</label>
                                <input type="text" class="form-control" name="cpf" id="cpf"
                                    placeholder="000.000.000-00" />
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Tipo de Pessoa + Documento-->

                        <!--begin::Nome-->
                        <div class="row mb-7">
                            <!--begin::Nome Fantasia (PJ)-->
                            <div class="col-md-12 fv-row" id="campo_nome_fantasia">
                                <x-tenant-label for="parceiro_nome_fantasia" :required="true">Nome Fantasia</x-tenant-label>
                                <x-tenant-input name="nome_fantasia" id="parceiro_nome_fantasia"
                                    placeholder="Nome Fantasia da empresa" class="" />
                            </div>
                            <!--end::Nome Fantasia-->
                            <!--begin::Nome Completo (PF)-->
                            <div class="col-md-12 fv-row" id="campo_nome" style="display: none;">
                                <x-tenant-label for="parceiro_nome" :required="true">Nome Completo</x-tenant-label>
                                <x-tenant-input name="nome" id="parceiro_nome"
                                    placeholder="Nome completo da pessoa" class="" />
                            </div>
                            <!--end::Nome Completo-->
                        </div>
                        <!--end::Nome-->

                        <!--begin::Contato-->
                        <div class="row mb-7">
                            <div class="col-md-4 fv-row">
                                <x-tenant-label for="parceiro_telefone">Telefone</x-tenant-label>
                                <x-tenant-input name="telefone" id="parceiro_telefone"
                                    placeholder="(00) 00000-0000" class="" />
                            </div>
                            <div class="col-md-8 fv-row">
                                <x-tenant-label for="parceiro_email">E-mail</x-tenant-label>
                                <x-tenant-input name="email" id="parceiro_email" type="email"
                                    placeholder="exemplo@email.com" class="" />
                            </div>
                        </div>
                        <!--end::Contato-->

                        <!--begin::Natureza (Checkboxes)-->
                        <div class="row mb-7">
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-3">Este parceiro é:</label>
                                <div class="d-flex gap-6">
                                    <label class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input class="form-check-input" type="checkbox" id="check_fornecedor" value="fornecedor" checked />
                                        <span class="form-check-label fw-semibold text-gray-700 fs-6">
                                            <i class="bi bi-building me-1 text-info"></i> Fornecedor
                                        </span>
                                    </label>
                                    <label class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input class="form-check-input" type="checkbox" id="check_cliente" value="cliente" />
                                        <span class="form-check-label fw-semibold text-gray-700 fs-6">
                                            <i class="bi bi-person-check me-1 text-success"></i> Cliente
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!--end::Natureza-->

                        <!--begin::Endereço (collapsible)-->
                        <div class="mb-7">
                            <a class="btn btn-sm btn-light-primary" data-bs-toggle="collapse" href="#collapse_endereco" role="button">
                                <i class="bi bi-geo-alt me-1"></i> Endereço (opcional)
                            </a>
                        </div>
                        <div class="collapse" id="collapse_endereco">
                            <div class="row mb-5">
                                <x-tenant-input
                                    name="cep"
                                    id="parceiro_cep"
                                    label="CEP"
                                    placeholder="00000-000"
                                    class="col-md-3"
                                    data-inputmask="'mask': '99999-999'" />

                                <x-tenant-input
                                    name="address1"
                                    id="parceiro_rua"
                                    label="Rua / Logradouro"
                                    class="col-md-7" />

                                <x-tenant-input
                                    name="numero"
                                    id="parceiro_numero"
                                    label="Nº"
                                    class="col-md-2" />
                            </div>
                            <div class="row mb-5">
                                <x-tenant-input
                                    name="bairro"
                                    id="parceiro_bairro"
                                    label="Bairro"
                                    class="col-md-4" />

                                <x-tenant-input
                                    name="city"
                                    id="parceiro_cidade"
                                    label="Cidade"
                                    class="col-md-5" />

                                <x-tenant-select
                                    name="country"
                                    id="parceiro_uf"
                                    label="UF"
                                    class="col-md-3"
                                    placeholder="UF"
                                    :hideSearch="false"
                                    dropdownParent="#modal_parceiro"
                                    control="select2">
                                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                        <option value="{{ $uf }}">{{ $uf }}</option>
                                    @endforeach
                                </x-tenant-select>
                            </div>
                        </div>
                        <!--end::Endereço-->

                        <!--begin::Observações-->
                        <div class="row mb-7">
                            <div class="col-md-12 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span>Observações</span>
                                </label>
                                <textarea class="form-control" name="observacoes" id="parceiro_observacoes" 
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
