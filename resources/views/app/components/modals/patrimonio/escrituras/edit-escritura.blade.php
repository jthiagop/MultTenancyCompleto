<div class="modal fade" id="DM_modal_edit_escritura" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Form-->
            <form id="kt_modal_new_address_form" class="form fv-plugins-bootstrap5 fv-plugins-framework"
                method="POST" action="{{ route('escritura.update', $patrimonio->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <!--begin::Modal title-->
                    <h2>Atualizar Escritura: {{ $patrimonio->codigo_rid }} </h2>
                    <!--end::Modal title-->
                    <input type="hidden" name="patrimonio_id" value="{{ $patrimonio->id }}">

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
                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-8 fv-row">
                            <!--begin::Label-->
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2" for="outorgante">
                                <span>Outorgante</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Especifique aqui os detalhes do patrimônios"></i>
                            </label>
                            <!--end::Label-->
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Pessoa ou entidade que concede ou transfere (Vendedor)"
                                id="outorgante" name="outorgante"
                                value="{{ old('outorgante', optional($patrimonio->escrituras->last())->outorgante ?? '') }}" />
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row g-9 mb-7">
                            <!--begin::Col-->
                            <div class="col-md-7 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="outorgante_email">E-Mail</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="nome@exemplo.com.br"
                                    name="outorgante_email" type="email" id="outorgante_email"
                                    value="{{ old('outorgante_email', optional($patrimonio->escrituras->last())->outorgante_email ?? '') }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-5 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="outorgante_telefone">Telefone</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" type="text"
                                    placeholder="(00) 0.0000-0000" id="outorgante_telefone"
                                    name="outorgante_telefone"
                                    value="{{ old('outorgante_telefone', optional($patrimonio->escrituras->last())->outorgante_telefone ?? '') }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row g-9 mb-7">
                            <!--begin::Col-->
                            <div class="col-md-8 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="matricula">Número da Matrícula</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="Número da Matrícula"
                                    value="{{ old('matricula', optional($patrimonio->escrituras->last())->matricula ?? '') }}"
                                    name="matricula" id="matricula" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="aquisicao">Data da Aquisição</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" type="date"
                                    placeholder="Selecione a data" id="aquisicao"
                                    value="{{ old('aquisicao', optional($patrimonio->escrituras->last())->aquisicao ?? '') }}"
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
                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2" for="outorgado">
                                <span>Outorgado</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Especifique aqui os detalhes do patrimônios"></i>
                            </label>
                            <!--end::Label-->
                            <input type="text" class="form-control form-control-solid"
                                placeholder="Pessoa ou entidade que recebe (Comprador)" id="outorgado"
                                name="outorgado"
                                value="{{ old('outorgado', optional($patrimonio->escrituras->last())->outorgado ?? '') }}" />
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="row g-9 mb-7">
                            <!--begin::Col-->
                            <div class="col-md-7 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="outorgado_email">E-Mail</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" name="outorgado_email"
                                    type="email" id="outorgado_email" placeholder="nome@exemplo.com.br"
                                    value="{{ old('outorgado_email', optional($patrimonio->escrituras->last())->outorgado_email ?? '') }}" />
                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-5 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="outorgado_telefone">Telefone</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" type="text"
                                    placeholder="(00) 0.0000-0000" id="outorgado_telefone"
                                    name="outorgado_telefone"
                                    value="{{ old('outorgado_telefone', optional($patrimonio->escrituras->last())->outorgado_telefone ?? '') }}" />
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
                                <label class="fs-6 fw-semibold mb-2" for="valor">Valor Aquisição</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <div class="position-relative d-flex align-items-center">
                                    <!--begin::Icon-->
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        R$
                                    </span>
                                    <!--end::Icon-->
                                    <!--begin::Input-->
                                    <input class="form-control form-control-solid ps-12" placeholder="0,00"
                                    name="valor" id="valor"
                                    value="{{ old('valor', optional($patrimonio->escrituras->last())->valor ? number_format(optional($patrimonio->escrituras->last())->valor, 2, ',', '.') : 'Sem Informações') }}" />
                                    <!--end::Input-->
                                </div>
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="area_total">Área Total</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="Área Total"
                                    id="area_total" name="area_total"
                                    value="{{ old('valor', optional($patrimonio->escrituras->last())->area_total ? number_format($patrimonio->escrituras->last()->area_total, 2, ',', '.') : 'Sem Informações') }}" />


                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                            <!--begin::Col-->
                            <div class="col-md-4 fv-row">
                                <!--begin::Label-->
                                <label class="fs-6 fw-semibold mb-2" for="area_privativa">Área Privativa</label>
                                <!--end::Label-->
                                <!--begin::Input-->
                                <input class="form-control form-control-solid" placeholder="Área Privativa"
                                    id="area_privativa" name="area_privativa"
                                    value="{{ old('valor', optional($patrimonio->escrituras->last())->area_privativa ? number_format($patrimonio->escrituras->last()->area_total, 2, ',', '.') : 'Sem Informações') }}" />

                                <!--end::Input-->
                            </div>
                            <!--end::Col-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-8">
                            <label class="fs-6 fw-semibold mb-2">[+] Informações</label>
                            <textarea class="form-control form-control-solid" id="informacoes" name="informacoes" maxlength="250"
                                rows="3" name="informacoes" placeholder="Mais detalhes sobre o foro">{{ old('informacoes', optional($patrimonio->informacoes)->informacoes ?? 'Sem escritura') }}</textarea>
                            <span class="fs-6 text-muted">Insira no máximo 250 caracteres</span>
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
