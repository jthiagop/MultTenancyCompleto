<div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
    <!--begin:Form-->
    <form id="kt_modal_foro_form" class="form fv-plugins-bootstrap5 fv-plugins-framework"
        method="POST" action="{{ route('patrimonio.store') }}">
        @csrf
        <input type="hidden" id="method_field" name="_method" value="POST">
        <input type="hidden" id="address_id" name="id">
        <!--begin::Heading-->
        <div class="mb-13 text-center">
            <!--begin::Title-->
            <h1 class="mb-3">Registro de Localização do Patrimônio</h1>
            <!--end::Title-->
            <!--begin::Description-->
            <div class="text-muted fw-semibold fs-5">Para mais informações, consulte as
                <a href="#" class="fw-bold link-primary">Diretrizes do Projeto</a>.
            </div>
            <!--end::Description-->
        </div>
        <!--end::Heading-->
        <!--begin::Input group-->
        <div class="d-flex flex-column mb-8 fv-row">
            <!--begin::Label-->
            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                <span class="required">Descrição</span>
                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                    title="Especifique aqui os detalhes do patrimônios"></i>
            </label>
            <!--end::Label-->
            <input type="text" class="form-control form-control-solid"
                placeholder="Informações de patrimônio" id="descricao" name="descricao" />
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row g-9 mb-8">
            <!--begin::Col-->
            <div class="col-md-8 fv-row">
                <label class="fs-6 fw-semibold mb-2">
                    <span class="required">Patrimônio</span>
                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                        title="A que patrimônio pertence?"></i>
                </label>
                <select class="form-select form-select-solid" data-control="select2"
                    id="patrimonio" data-placeholder="Selecione o patrimônio"
                    name="patrimonio">
                    <option value="">Selecione o patrimônio...</option>
                    @foreach ($nameForos as $nameForo)
                        <option value="{{ $nameForo->name }}"
                            data-num-foro="{{ $nameForo->numForo }}"
                            data-ibge="{{ $nameForo->ibge }}">
                            {{ $nameForo->name }} - {{ $nameForo->numForo }}
                        </option>
                    @endforeach
                </select><br>

                <input type="hidden" id="numForo" readonly="true" type="text" name="numForo">
                <input type="hidden" id="numIbge" readonly="true" type="text" name="numIbge">

                <script>
                    document.getElementById('patrimonio').onchange = function() {
                        var sel = this;
                        var value = sel.value;
                        var numForo = sel.options[sel.selectedIndex].getAttribute('data-num-foro');
                        var numIbge = sel.options[sel.selectedIndex].getAttribute('data-ibge');

                        document.getElementById('numForo').value = numForo;
                        document.getElementById('numIbge').value = numIbge;
                    };
                </script>
            </div>


            <!-- Inputs ocultos para armazenar os valores adicionais -->
            <input type="hidden" id="selected-num-foro" name="selected-num-foro">
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <label class="required fs-6 fw-semibold mb-2">Data</label>
                <!--begin::Input-->
                <div class="position-relative d-flex align-items-center">
                    <!--begin::Icon-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
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
                                d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <!--end::Icon-->
                    <!--begin::Datepicker-->
                    <input class="form-control form-control-solid ps-12" placeholder="Secione a data"
                        name="data" />
                    <!--end::Datepicker-->
                </div>
                <!--end::Input-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->
        <!--begin::Input group-->
        <div class="row g-9 mb-7">
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <!--begin::Label-->
                <label class="fs-6 fw-semibold mb-2">Livro</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input class="form-control form-control-solid" placeholder="" name="livro" />
                <!--end::Input-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <!--begin::Label-->
                <label class="fs-6 fw-semibold mb-2">Folha</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input class="form-control form-control-solid" placeholder="" name="folha" />
                <!--end::Input-->
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-md-4 fv-row">
                <!--begin::Label-->
                <label class="fs-6 fw-semibold mb-2">Registro</label>
                <!--end::Label-->
                <!--begin::Input-->
                <input class="form-control form-control-solid" placeholder="" name="registro" />
                <!--end::Input-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Input group-->

        {{-- <!--begin::Input group-->
            <div class="d-flex flex-column mb-8 fv-row">
                <!--begin::Label-->
                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                    <span class="required">Tags</span>
                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                        title="Specify a target priorty"></i>
                </label>
                <!--end::Label-->
                <input class="form-control form-control-solid" value="Important, Urgent"
                    name="tags" />
            </div>
            <!--end::Input group--> --}}
        <!--begin::Titulo do Endereco-->

        <div class="separator separator-dashed my-10"></div>


        <div class="row">
            <div class="fw-bold fs-3 rotate collapsible mb-7" data-bs-toggle="collapse"
                href="#kt_modal_add_customer_billing_info" role="button" aria-expanded="false"
                aria-controls="kt_customer_view_details">Endereço
                <span class="ms-2 rotate-180 ">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                    <span class="svg-icon svg-icon-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </span>
            </div>
        </div>
        <!--end::Billing toggle-->
        <!--begin::Billing form-->
        <div class="row">
            <div id="kt_modal_add_customer_billing_info" class="collapse show">
                <!--begin::Input group-->
                <div class="row g-9 mb-7">
                    <!--begin::Col-->
                    <div class="col-md-4 fv-row">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-semibold mb-2">CEP</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input class="form-control form-control-solid" placeholder="" name="cep"
                            id="cep" />
                        <!--end::Input-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-md-8 fv-row">
                        <!--begin::Label-->
                        <label class="required fs-6 fw-semibold mb-2">Bairro</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input class="form-control form-control-solid" placeholder="" id="bairro"
                            name="bairro" />
                        <!--end::Input-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-7 fv-row">
                    <!--begin::Label-->
                    <label class="fs-6 fw-semibold mb-2">Rua</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" placeholder="nome da rua"
                        id="logradouro" name="logradouro" value="" />
                    <!--end::Input-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="row g-9 mb-7">
                    <!--begin::Col-->
                    <div class="col-md-6 fv-row">
                        <!--begin::Label-->
                        <label class=" fs-6 fw-semibold mb-2">Cidade</label>
                        <!--end::Label-->
                        <!--begin::Input-->
                        <input class="form-control form-control-solid" placeholder=""id="localidade"
                            name="localidade" value="Victoria" />
                        <!--end::Input-->
                    </div>
                    <!--end::Col-->
                    <!--begin::Col-->
                    <div class="col-md-6 fv-row">
                        <!--begin::Label-->
                        <label class="fs-6 fw-semibold mb-2">
                            <span class="">Estado</span>
                            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                title="Estado de origem"></i>
                        </label>
                        <!--end::Label-->
                        <!--begin::Select-->
                        <select id="uf" name="uf" class="form-select form-select-solid"
                            data-control="select2" data-hide-search="true"
                            data-placeholder="Escolha a cidade">
                            <option value="">Selecione a cidade</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amapá</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                        </select>
                        <!--end::Select-->
                    </div>
                    <!--end::Col-->
                </div>
                <!--end::Input group-->
                <!--begin::Input group-->
                <div class="d-flex flex-column mb-8">
                    <label class="fs-6 fw-semibold mb-2">[+] Informações</label>
                    <textarea class="form-control form-control-solid" id="complemento" name="complemento" maxlength="250"
                        rows="3" name="target_details" placeholder="Mais detalhes sobre o foro"></textarea>
                    <span class="fs-6 text-muted">Insira no máximo 250 caracteres</span>
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Billing form-->
            <div class="separator separator-dashed my-10"></div>

            <!--begin::Titulo do Endereco-->
            <div class="fw-bold fs-3 rotate collapsible mb-7" data-bs-toggle="collapse"
                href="#kt_modal_add_customer_dados_escritura" role="button" aria-expanded="false"
                aria-controls="kt_customer_view_dados_escritura">Dados da Escritura
                <span class="ms-2 rotate-180">
                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr072.svg-->
                    <span class="svg-icon svg-icon-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M11.4343 12.7344L7.25 8.55005C6.83579 8.13583 6.16421 8.13584 5.75 8.55005C5.33579 8.96426 5.33579 9.63583 5.75 10.05L11.2929 15.5929C11.6834 15.9835 12.3166 15.9835 12.7071 15.5929L18.25 10.05C18.6642 9.63584 18.6642 8.96426 18.25 8.55005C17.8358 8.13584 17.1642 8.13584 16.75 8.55005L12.5657 12.7344C12.2533 13.0468 11.7467 13.0468 11.4343 12.7344Z"
                                fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </span>
            </div>
        </div>
        <!--end::Billing toggle-->
        <!--begin::Billing form-->
        <div id="kt_modal_add_customer_dados_escritura" class="collapse show">
            <!--begin::Input group-->
            <div class="d-flex flex-column mb-8 fv-row">
                <!--begin::Label-->
                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                    <span class="">Outorgante</span>
                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                        title="Especifique aqui os detalhes do patrimônios"></i>
                </label>
                <!--end::Label-->
                <input type="text" class="form-control form-control-solid"
                    placeholder="Pessoa ou entidade que concede ou transfere(Vendedor)" id="outorgante" name="outorgante" />
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row g-9 mb-7">
                <!--begin::Col-->
                <div class="col-md-7 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">E-Mail</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" placeholder="" name="outorgante_email"
                    type="email" id="email" placeholder="seuemail@exemplo.com" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-5 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Telefone</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" type="text" placeholder="" id="telefone"
                        name="outorgante_telefone" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row g-9 mb-7">
                <!--begin::Col-->
                <div class="col-md-6 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Número da Matrícula</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" placeholder="" name="matricula"
                        id="matricula" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-6 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Data da Aquisição</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" type="date" placeholder="" id="aquisicao"
                        name="aquisicao" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->

            <div class="separator separator-dashed border-secondary my-10"></div>
            <!--begin::Input group-->
            <div class="d-flex flex-column mb-8 fv-row">
                <!--begin::Label-->
                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                    <span class="">Outorgado</span>
                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                        title="Especifique aqui os detalhes do patrimônios"></i>
                </label>
                <!--end::Label-->
                <input type="text" class="form-control form-control-solid"
                    placeholder="Pessoa ou entidade que recebe (comprador)" id="outorgado" name="outorgado" />
            </div>
                                        <!--begin::Input group-->
            <div class="row g-9 mb-7">
                <!--begin::Col-->
                <div class="col-md-7 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">E-Mail</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" placeholder="" name="outorgado_email"
                    type="email" id="email" placeholder="seuemail@exemplo.com" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-5 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Telefone</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" type="text" placeholder="" id="telefone"
                        name="outorgado_telefone" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="row g-9 mb-7">
                <!--begin::Col-->
                <div class="col-md-4 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Valor Aquisição</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <!--begin::Input-->
                <div class="position-relative d-flex align-items-center">
                    <!--begin::Icon-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                        R$
                    </span>
                    <!--end::Svg Icon-->
                    <!--end::Icon-->
                    <!--begin::Datepicker-->
                    <input class="form-control form-control-solid ps-12" placeholder="" name="valor"
                    id="valor" />
                    <!--end::Datepicker-->
                </div>
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-4 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Área Total</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" placeholder="" id="area_total"
                        name="area_total" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
                <!--begin::Col-->
                <div class="col-md-4 fv-row">
                    <!--begin::Label-->
                    <label class=" fs-6 fw-semibold mb-2">Área Privativa</label>
                    <!--end::Label-->
                    <!--begin::Input-->
                    <input class="form-control form-control-solid" placeholder="" id="area_privativa"
                        name="area_privativa" />
                    <!--end::Input-->
                </div>
                <!--end::Col-->
            </div>
            <!--end::Input group-->
            <!--begin::Input group-->
            <div class="d-flex flex-column mb-8">
                <label class="fs-6 fw-semibold mb-2">[+] Informações</label>
                <textarea class="form-control form-control-solid" id="informacoes" name="informacoes" maxlength="250"
                    rows="3" name="target_details" placeholder="Mais detalhes sobre o foro"></textarea>
                <span class="fs-6 text-muted">Insira no máximo 250 caracteres</span>
            </div>
            <!--end::Input group-->
        </div>
        <!--end::Billing form-->
        <!--begin::Actions-->
        <div class="text-center">
            <button type="reset" id="kt_modal_new_foro_cancel"
                class="btn btn-light me-3">Sair</button>
            <button type="submit" id="kt_modal_new_foro_submit" class="btn btn-primary">
                <span class="indicator-label">Salvar</span>
                <span class="indicator-progress">Por favor, espere...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
            </button>
        </div>
        <!--end::Actions-->
    </form>
    <!--end:Form-->
</div>
