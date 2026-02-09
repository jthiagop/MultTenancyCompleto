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
                <div class="modal-body py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" 
                         data-kt-scroll-max-height="auto" data-kt-scroll-offset="300px">
                        
                        <!--begin::Natureza + Tipo de Pessoa-->
                        <div class="row mb-7">
                            <x-tenant-select
                                name="natureza"
                                id="parceiro_natureza"
                                label="Natureza"
                                :required="true"
                                class="col-md-6"
                                :hideSearch="true"
                                dropdownParent="#modal_parceiro"
                                control="select2"
                                value="fornecedor">
                                @foreach(\App\Enums\NaturezaParceiro::options() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </x-tenant-select>

                            <x-tenant-select
                                name="tipo"
                                id="parceiro_tipo"
                                label="Tipo de Pessoa"
                                :required="true"
                                class="col-md-6"
                                :hideSearch="true"
                                dropdownParent="#modal_parceiro"
                                control="select2"
                                value="pj">
                                <option value="pj">Pessoa Jurídica (PJ)</option>
                                <option value="pf">Pessoa Física (PF)</option>
                                <option value="ambos">Ambos (PJ e PF)</option>
                            </x-tenant-select>
                        </div>
                        <!--end::Natureza + Tipo de Pessoa-->

                        <!--begin::Nome / Razão Social + Nome Fantasia-->
                        <div class="row mb-7">
                            <x-tenant-input
                                name="nome"
                                id="parceiro_nome"
                                label="Razão Social"
                                placeholder="Razão Social da empresa"
                                :required="true"
                                class="col-md-6" />

                            <div class="col-md-6" id="campo_nome_fantasia">
                                <x-tenant-input
                                    name="nome_fantasia"
                                    id="parceiro_nome_fantasia"
                                    label="Nome Fantasia"
                                    placeholder="Nome Fantasia"
                                    class="w-100" />
                            </div>
                        </div>
                        <!--end::Nome-->

                        <!--begin::Documentos-->
                        <div class="row mb-7">
                            <div class="col-md-6" id="campo_cnpj">
                                <x-tenant-input
                                    name="cnpj"
                                    id="parceiro_cnpj"
                                    label="CNPJ"
                                    placeholder="00.000.000/0000-00"
                                    class="w-100"
                                    data-inputmask="'mask': '99.999.999/9999-99'" />
                            </div>
                            <div class="col-md-6" id="campo_cpf" style="display: none;">
                                <x-tenant-input
                                    name="cpf"
                                    id="parceiro_cpf"
                                    label="CPF"
                                    placeholder="000.000.000-00"
                                    class="w-100"
                                    data-inputmask="'mask': '999.999.999-99'" />
                            </div>
                        </div>
                        <!--end::Documentos-->

                        <!--begin::Contato-->
                        <div class="row mb-7">
                            <x-tenant-input
                                name="telefone"
                                id="parceiro_telefone"
                                label="Telefone"
                                placeholder="(00) 00000-0000"
                                class="col-md-6" />

                            <x-tenant-input
                                name="email"
                                id="parceiro_email"
                                label="E-mail"
                                type="email"
                                placeholder="email@exemplo.com"
                                class="col-md-6" />
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
