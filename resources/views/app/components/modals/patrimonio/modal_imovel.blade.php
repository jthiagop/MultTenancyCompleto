<!--begin::Modal - New Imóvel-->
<div class="modal fade" id="kt_modal_new_imovel" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-fullscreen">
        <!--begin::Modal content-->
        <div class="modal-content rounded">
            <!--begin::Modal header-->
            <div class="modal-header btn btn-sm">
                <h3 class="modal-title" id="modal_imovel_title">Cadastro de Imóvel</h3>
                <button type="button" class="btn-close" aria-label="Close"></button>
            </div>
            <!--end::Modal header-->

            <!--begin::Modal body-->
            <div class="modal-body scroll-y px-10 px-lg-15 pb-15 bg-light pt-5">
                <!-- Begin::Form -->
                <form id="kt_modal_imovel_form" class="form" action="{{ route('bem.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <meta name="csrf-token" content="{{ csrf_token() }}">

                    <!-- Campo oculto para identificar o tipo -->
                    <input type="hidden" name="tipo" value="imovel">

                    <!--begin::Card-->
                    <div class="card mb-xl-10">
                        <div class="card-body px-10">
                            <!--begin::Form-->
                            <!--begin::Input group - Dados Gerais-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Descrição</span>
                                        <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                            title="Descrição do imóvel"></i>
                                    </label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Informe a descrição do imóvel"
                                        name="descricao" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-3 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Valor</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="text" class="form-control form-control-solid" placeholder="0,00"
                                            name="valor" />
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-3 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Data de Aquisição</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3"
                                                    d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z"
                                                    fill="currentColor" />
                                                <path
                                                    d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.6 10.8 8.4 10.9C8.2 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.1 12.4 6.9 12.4C6.7 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.5 10.1 7.9 10C8.3 9.9 8.6 9.8 9.1 9.8C9.5 9.8 9.8 9.9 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.1 16.3 6.1 16.1C6.1 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7 15.4 7.1 15.5C7.2 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.8 14.4 9.5 14.3 9.1 14.3C9 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.4 14.3 8.4 14.3C8.2 14.3 8 14.2 7.9 14.1C7.8 14 7.7 13.8 7.7 13.7C7.7 13.5 7.8 13.4 7.9 13.2C8 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.8 15.9 9.7C15.9 9.6 16.1 9.6 16.3 9.6C16.5 9.6 16.7 9.7 16.8 9.8C16.9 9.9 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                                    fill="currentColor" />
                                            </svg>
                                        </span>
                                        <input class="form-control form-control-solid ps-12" placeholder="DD/MM/AAAA"
                                            name="data_aquisicao" />
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group - Centro de Custo e Estado-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Centro de Custo</label>
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-dropdown-parent="#kt_modal_new_imovel" data-placeholder="Selecione o Centro de Custo"
                                        name="centro_custo" data-allow-clear="true">
                                        <option value="">Selecione...</option>
                                        @php
                                            $centrosAtivos = \App\Models\Financeiro\CostCenter::forActiveCompany()->get();
                                        @endphp
                                        @foreach ($centrosAtivos as $centro)
                                            <option value="{{ $centro->id }}">{{ $centro->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Estado do Bem</label>
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-dropdown-parent="#kt_modal_new_imovel" data-placeholder="Selecione o Estado"
                                        name="estado_bem" data-allow-clear="true">
                                        <option value="">Selecione...</option>
                                        <option value="Novo">Novo</option>
                                        <option value="Bom">Bom</option>
                                        <option value="Ruim">Ruim</option>
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span>Depreciar</span>
                                    </label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" name="depreciar" value="1" />
                                        <span class="form-check-label fw-semibold text-muted">Ativar depreciação</span>
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group - Documento e Fornecedor-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Número do Documento</label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="Número da nota fiscal ou escritura" name="numero_documento" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-8 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Fornecedor/Vendedor</label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="Nome do fornecedor ou vendedor" name="fornecedor" />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->

                    </div>
                    <!--end::Card-->

                    <!--begin::Card - Endereço e Localização-->
                    <div class="card mb-xl-10">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Endereço e Localização</span>
                            </h3>
                        </div>
                        <div class="card-body px-10">
                            <!--begin::Input group - Endereço-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">CEP</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="00000-000"
                                        name="cep" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Endereço</label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="Rua, Avenida, etc." name="endereco" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Bairro</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Bairro"
                                        name="bairro" />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->

                            <!--begin::Input group - Cidade e UF-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Cidade</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Cidade"
                                        name="cidade" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-2 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">UF</label>
                                    <select class="form-select form-select-solid" data-control="select2"
                                        data-dropdown-parent="#kt_modal_new_imovel" data-placeholder="UF" name="uf">
                                        <option value="">UF</option>
                                        <option value="AC">AC</option>
                                        <option value="AL">AL</option>
                                        <option value="AP">AP</option>
                                        <option value="AM">AM</option>
                                        <option value="BA">BA</option>
                                        <option value="CE">CE</option>
                                        <option value="DF">DF</option>
                                        <option value="ES">ES</option>
                                        <option value="GO">GO</option>
                                        <option value="MA">MA</option>
                                        <option value="MT">MT</option>
                                        <option value="MS">MS</option>
                                        <option value="MG">MG</option>
                                        <option value="PA">PA</option>
                                        <option value="PB">PB</option>
                                        <option value="PR">PR</option>
                                        <option value="PE">PE</option>
                                        <option value="PI">PI</option>
                                        <option value="RJ">RJ</option>
                                        <option value="RN">RN</option>
                                        <option value="RS">RS</option>
                                        <option value="RO">RO</option>
                                        <option value="RR">RR</option>
                                        <option value="SC">SC</option>
                                        <option value="SP">SP</option>
                                        <option value="SE">SE</option>
                                        <option value="TO">TO</option>
                                    </select>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Inscrição Municipal</label>
                                    <input type="text" class="form-control form-control-solid"
                                        placeholder="Inscrição municipal" name="inscricao_municipal" />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                        </div>
                    </div>
                    <!--end::Card - Endereço e Localização-->

                    <!--begin::Card - Dados de Cartório/Escritura-->
                    <div class="card mb-xl-10">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Dados de Cartório/Escritura</span>
                            </h3>
                        </div>
                        <div class="card-body px-10">
                            <!--begin::Input group - Dados de Cartório-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Matrícula</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Matrícula"
                                        name="matricula" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-4 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Cartório</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Cartório"
                                        name="cartorio" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-2 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Livro</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Livro"
                                        name="livro" />
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-2 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Folha</label>
                                    <input type="text" class="form-control form-control-solid" placeholder="Folha"
                                        name="folha" />
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                        </div>
                    </div>
                    <!--end::Card - Dados de Cartório/Escritura-->

                    <!--begin::Card - Áreas-->
                    <div class="card mb-xl-10">
                        <div class="card-header">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold fs-3 mb-1">Áreas</span>
                            </h3>
                        </div>
                        <div class="card-body px-10">
                            <!--begin::Input group - Áreas-->
                            <div class="row g-9 mb-4">
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Área Total (m²)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-solid" placeholder="0,00"
                                            name="area_total" />
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Área Privativa (m²)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-solid" placeholder="0,00"
                                            name="area_privativa" />
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                        </div>
                    </div>
                    <!--end::Card - Áreas-->

                            <!--begin::Input group - Áreas-->
                            <div class="row g-9 mb-4">
                                <div class="col-md-12">
                                    <h3 class="fs-5 fw-bold mb-5">Áreas</h3>
                                </div>
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Área Total (m²)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-solid" placeholder="0,00"
                                            name="area_total" />
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                                <!--end::Col-->
                                <!--begin::Col-->
                                <div class="col-md-6 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Área Privativa (m²)</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-solid" placeholder="0,00"
                                            name="area_privativa" />
                                        <span class="input-group-text">m²</span>
                                    </div>
                                </div>
                                <!--end::Col-->
                            </div>
                            <!--end::Input group-->
                        </div>
                    </div>
                    <!--end::Card-->
                </form>
                <!--end::Form-->
            </div>
            <!--end::Modal body-->

            <!--begin::Modal footer-->
            <div class="modal-footer btn btn-sm">
                <div class="text-center">
                    <button type="reset" id="kt_modal_imovel_cancel"
                        class="btn btn-sm btn-light me-3">Cancelar</button>
                    <!-- Split dropup button -->
                    <div class="btn-group dropup">
                        <!-- Botão principal -->
                        <button type="submit" id="kt_modal_imovel_submit" form="kt_modal_imovel_form" class="btn btn-sm btn-primary">
                            <span class="indicator-label">Salvar</span>
                            <span class="indicator-progress">Aguarde...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                        <!-- Botão de dropup -->
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <!-- Opções do dropup -->
                        <div class="dropdown-menu">
                            <a class="dropdown-item btn-sm" href="#" id="kt_modal_imovel_clone">Salvar e Clonar</a>
                            <a class="dropdown-item btn-sm" href="#" id="kt_modal_imovel_novo">Salvar e em Branco</a>
                        </div>
                    </div>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - New Imóvel-->

