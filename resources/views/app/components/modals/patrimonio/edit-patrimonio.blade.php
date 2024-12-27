<div class="modal fade" id="kt_modal_new_address" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            <form id="kt_modal_new_address_form" class="form fv-plugins-bootstrap5 fv-plugins-framework"
            method="POST" action="{{ route('patrimonio.update', $patrimonio->id) }}">
            @csrf
            @method('PUT')
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2>Atualizar Patrimônio: {{ $patrimonio->codigo_rid }} </h2>
                    <!--end::Modal title-->
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                    rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--end::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body py-10 px-lg-17">
                    <!--begin::Scroll-->
                    <div class="scroll-y me-n7 pe-7" id="kt_modal_new_address_scroll" data-kt-scroll="true"
                        data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto"
                        data-kt-scroll-dependencies="#kt_modal_new_address_header"
                        data-kt-scroll-wrappers="#kt_modal_new_address_scroll" data-kt-scroll-offset="300px">
                        <!--begin::Notice-->
                        <!--begin::Notice-->
                        <div
                            class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                            <!--begin::Icon-->
                            <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                            <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.3" x="2" y="2" width="20" height="20"
                                        rx="10" fill="currentColor" />
                                    <rect x="11" y="14" width="7" height="2" rx="1"
                                        transform="rotate(-90 11 14)" fill="currentColor" />
                                    <rect x="11" y="17" width="2" height="2" rx="1"
                                        transform="rotate(-90 11 17)" fill="currentColor" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <!--end::Icon-->
                            <!--begin::Wrapper-->
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">Atenção</h4>
                                    <div class="fs-6 text-gray-700">
                                        Atualizar o endereço pode alterar o Código RID, pois ele depende diretamente
                                        do CEP.
                                    </div>
                                </div>
                            </div>
                            <!--end::Wrapper-->
                        </div>
                        <!--end::Notice-->
                        <!--end::Notice-->
                        <div class="row g-9 mb-5">
                            <div class="col-md-9 fv-row">
                                <div class="d-flex flex-column mb-5 fv-row">
                                    <!--begin::Label-->
                                    <label class="required fs-5 fw-semibold mb-2">Descrição</label>
                                    <!--end::Label-->
                                    <!--begin::Input-->
                                    <input class="form-control form-control-solid" placeholder=""
                                        name="descricao" value="{{ $patrimonio->descricao }}" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <!--begin::Col-->
                            <div class="col-md-3 fv-row">
                                <!--begin::Label-->
                                <label class="fs-5 fw-semibold mb-2">CEP</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="" id="cep"
                                    name="cep" value="{{ $patrimonio->cep }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--begin::Input group-->
                        <div class="row g-9 mb-5">
                            <!--begin::Col-->
                            <div class="col-md-8 fv-row">
                                <!--begin::Label-->
                                <label class="fs-5 fw-semibold mb-2">Rua</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="" id="logradouro"
                                    name="logradouro" value="{{ $patrimonio->logradouro }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="required fs-5 fw-semibold mb-2">Bairro</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" placeholder=""
                                    id="bairro" name="bairro" value="{{ $patrimonio->bairro }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row mb-5">

                            <!--begin::Col-->
                            <div class="col-md-6 fv-row">
                                <!--end::Label-->
                                <label class="required fs-5 fw-semibold mb-2">Cidade</label>
                                <!--end::Label-->
                                <!--end::Input-->
                                <input type="text" class="form-control form-control-solid" placeholder=""
                                    id="localidade" name="localidade"
                                    value="{{ $patrimonio->localidade }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <div class="col-md-6 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-5 fw-semibold mb-2">
                                    <span class="required">Estado</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Your payment statements may very based on selected country"></i>
                                </label>
                                <!--end::Label-->
                                <select id="uf" name="uf" data-control="select2"
                                    data-dropdown-parent="#kt_modal_new_address"
                                    data-placeholder="Selecione o Estado..."
                                    class="form-select form-select-solid">

                                    <!-- Opcional: exibir uma opção neutra ou descritiva -->
                                    <option value="">Selecione o estado</option>

                                    <option value="AC" {{ $patrimonio->uf == 'AC' ? 'selected' : '' }}>Acre
                                    </option>
                                    <option value="AL" {{ $patrimonio->uf == 'AL' ? 'selected' : '' }}>
                                        Alagoas</option>
                                    <option value="AP" {{ $patrimonio->uf == 'AP' ? 'selected' : '' }}>Amapá
                                    </option>
                                    <option value="AM" {{ $patrimonio->uf == 'AM' ? 'selected' : '' }}>
                                        Amazonas</option>
                                    <option value="BA" {{ $patrimonio->uf == 'BA' ? 'selected' : '' }}>Bahia
                                    </option>
                                    <option value="CE" {{ $patrimonio->uf == 'CE' ? 'selected' : '' }}>Ceará
                                    </option>
                                    <option value="DF" {{ $patrimonio->uf == 'DF' ? 'selected' : '' }}>
                                        Distrito Federal</option>
                                    <option value="ES" {{ $patrimonio->uf == 'ES' ? 'selected' : '' }}>
                                        Espírito Santo</option>
                                    <option value="GO" {{ $patrimonio->uf == 'GO' ? 'selected' : '' }}>Goiás
                                    </option>
                                    <option value="MA" {{ $patrimonio->uf == 'MA' ? 'selected' : '' }}>
                                        Maranhão</option>
                                    <option value="MT" {{ $patrimonio->uf == 'MT' ? 'selected' : '' }}>Mato
                                        Grosso</option>
                                    <option value="MS" {{ $patrimonio->uf == 'MS' ? 'selected' : '' }}>Mato
                                        Grosso do Sul</option>
                                    <option value="MG" {{ $patrimonio->uf == 'MG' ? 'selected' : '' }}>Minas
                                        Gerais</option>
                                    <option value="PA" {{ $patrimonio->uf == 'PA' ? 'selected' : '' }}>Pará
                                    </option>
                                    <option value="PB" {{ $patrimonio->uf == 'PB' ? 'selected' : '' }}>
                                        Paraíba</option>
                                    <option value="PR" {{ $patrimonio->uf == 'PR' ? 'selected' : '' }}>Paraná
                                    </option>
                                    <option value="PE" {{ $patrimonio->uf == 'PE' ? 'selected' : '' }}>
                                        Pernambuco</option>
                                    <option value="PI" {{ $patrimonio->uf == 'PI' ? 'selected' : '' }}>Piauí
                                    </option>
                                    <option value="RJ" {{ $patrimonio->uf == 'RJ' ? 'selected' : '' }}>Rio de
                                        Janeiro</option>
                                    <option value="RN" {{ $patrimonio->uf == 'RN' ? 'selected' : '' }}>Rio
                                        Grande do Norte</option>
                                    <option value="RS" {{ $patrimonio->uf == 'RS' ? 'selected' : '' }}>Rio
                                        Grande do Sul</option>
                                    <option value="RO" {{ $patrimonio->uf == 'RO' ? 'selected' : '' }}>
                                        Rondônia</option>
                                    <option value="RR" {{ $patrimonio->uf == 'RR' ? 'selected' : '' }}>
                                        Roraima</option>
                                    <option value="SC" {{ $patrimonio->uf == 'SC' ? 'selected' : '' }}>Santa
                                        Catarina</option>
                                    <option value="SP" {{ $patrimonio->uf == 'SP' ? 'selected' : '' }}>São
                                        Paulo</option>
                                    <option value="SE" {{ $patrimonio->uf == 'SE' ? 'selected' : '' }}>
                                        Sergipe</option>
                                    <option value="TO" {{ $patrimonio->uf == 'TO' ? 'selected' : '' }}>
                                        Tocantins</option>

                                </select>

                            </div>
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-5 fv-row">
                            <!--begin::Label-->
                            <label class=" fs-5 fw-semibold mb-2">Complemento</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <textarea class="form-control form-control-solid" placeholder="" id="complemento" name="complemento">{{ $patrimonio->complemento }} </textarea>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="row g-9 mb-5">
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="fs-5 fw-semibold mb-2">Livro</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="" id="logradouro"
                                    name="livro" value="{{ $patrimonio->livro }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class=" fs-5 fw-semibold mb-2">Folha</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" placeholder=""
                                    id="bairro" name="folha" value="{{ $patrimonio->folha }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class=" fs-5 fw-semibold mb-2">Registro</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input type="text" class="form-control form-control-solid" placeholder=""
                                    id="bairro" name="registro" value="{{ $patrimonio->registro }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->
                    </div>
                    <!--end::Scroll-->
                </div>
                <!--end::Modal body-->
                <!--begin::Modal footer-->
                <div class="modal-footer flex-center">
                    <!-- Botão "Sair" (fecha o modal) -->
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                        <i class="bi bi-box-arrow-right"></i> <!-- Ícone (Bootstrap Icons) -->
                        <span>Sair</span>
                    </button>

                    <!-- Botão "Atualizar" (envia o form) -->
                    <button type="submit" id="kt_modal_new_address_submit" class="btn btn-primary">
                        <i class="bi bi-arrow-repeat"></i> <!-- Ícone (Bootstrap Icons) -->
                        <span>Atualizar</span>
                    </button>
                </div>
                <!--end::Modal footer-->

            </form>
            <!--end::Form-->
        </div>
    </div>
</div>
