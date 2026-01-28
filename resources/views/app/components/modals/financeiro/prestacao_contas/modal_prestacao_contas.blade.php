<!--begin::Modal - Prestação de Contas-->
<div class="modal fade" id="modal_prestacao_contas" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-top mw-800px">
        <!--begin:Form-->
        <form id="kt_modal_prestacao_contas_form" class="form" action="#">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_new_address_header">
                    <!--begin::Modal title-->
                    <h2>Prestação de Contas</h2>
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
                    <!--begin::Input group-->
                    <div class="row g-9 mb-8">
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Visão</label>
                            <select class="form-select" data-control="select2" data-hide-search="true"
                                data-placeholder="Selecione a visão" name="visao" id="visao">
                                <option value="1" selected>Convento</option>
                                <option value="2">Centro de Custo</option>
                            </select>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Período Inicial</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Datepicker-->
                                <input class="form-control ps-12" placeholder="Selecione uma data"
                                    name="data_inicial" id="data_inicial" />
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Período Final</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <!--begin::Svg Icon | path: icons/duotune/general/gen014.svg-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </span>
                                <!--end::Svg Icon-->
                                <!--end::Icon-->
                                <!--begin::Datepicker-->
                                <input class="form-control ps-12" placeholder="Selecione uma data"
                                    name="data_final" id="data_final" />
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Visão Details Row-->
                    <div class="row g-9 mb-8" id="visao_details_row">
                        <!--begin::Left Column - Organismo/Centro de Custo-->
                        <div class="col-md-6 fv-row">
                            <!--begin::Convento Display (shown when visao = 1)-->
                            <div id="convento_display">
                                <label class="fs-6 fw-semibold mb-2">Organismo</label>
                                <input type="text" class="form-control" id="company_name_input"
                                    readonly />
                            </div>
                            <!--end::Convento Display-->

                            <!--begin::Centro de Custo Select (shown when visao = 2)-->
                            <div id="centro_custo_display" style="display: none;">
                                <label class="required fs-6 fw-semibold mb-2">Centro de Custo</label>
                                <select class="form-select" data-control="select2"
                                    data-hide-search="true" data-placeholder="Selecione o centro de custo"
                                    name="cost_center_id" id="cost_center_id">
                                    <option value="">Carregando...</option>
                                </select>
                            </div>
                            <!--end::Centro de Custo Select-->
                        </div>
                        <!--end::Left Column-->

                        <!--begin::Right Column - Modelo-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Modelo</label>
                            <div class="d-flex gap-5">
                                <!--begin::Radio Horizontal-->
                                <label class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="modelo" value="horizontal"
                                        id="modelo_horizontal" />
                                    <span class="form-check-label fw-semibold">
                                        Horizontal
                                    </span>
                                </label>
                                <!--end::Radio Horizontal-->

                                <!--begin::Radio Vertical-->
                                <label class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="modelo" value="vertical"
                                        id="modelo_vertical" />
                                    <span class="form-check-label fw-semibold">
                                        Vertical
                                    </span>
                                </label>
                                <!--end::Radio Vertical-->
                            </div>
                        </div>
                        <!--end::Right Column-->
                    </div>
                    <!--end::Visão Details Row-->

                    <!--begin::Separator-->
                    <div class="separator separator-dashed my-8"></div>
                    <!--end::Separator-->

                    <!--begin::Financial Accounts Section-->
                    <div class="card-body">
                        <!--begin::Section Title-->
                        <h5 class="text-gray-700 fw-bold mb-5">Tipos de seleção de contas financeiras</h5>
                        <!--end::Section Title-->

                        <!--begin::Checkbox to enable selection-->
                        <div class="form-check form-check-custom mb-5">
                            <input class="form-check-input" type="checkbox" name="filtrar_contas"
                                id="filtrar_contas" />
                            <label class="form-check-label fw-semibold" for="filtrar_contas">
                                Filtrar por caixa/banco
                            </label>
                        </div>
                        <!--end::Checkbox-->

                        <!--begin::Options (disabled by default)-->
                        <div id="tipo_conta_options" style="display: none;">
                            <div class="row g-5">
                                <!--begin::Radio buttons column-->
                                <div class="col-md-3">
                                    <div class="d-flex gap-4">
                                        <!--begin::Radio Caixa-->
                                        <label class="form-check form-check-custom">
                                            <input class="form-check-input" type="radio" name="tipo_conta"
                                                value="caixa" id="tipo_conta_caixa" disabled />
                                            <span class="form-check-label text-gray-600 fw-semibold">
                                                Caixa
                                            </span>
                                        </label>
                                        <!--end::Radio Caixa-->

                                        <!--begin::Radio Banco-->
                                        <label class="form-check form-check-custom">
                                            <input class="form-check-input" type="radio" name="tipo_conta"
                                                value="banco" id="tipo_conta_banco" disabled />
                                            <span class="form-check-label text-gray-600 fw-semibold">
                                                Banco
                                            </span>
                                        </label>
                                        <!--end::Radio Banco-->
                                    </div>
                                </div>
                                <!--end::Radio buttons column-->


                                <!--begin::Banco/Caixa select field-->
                                <div class="col-md-9">
                                    <select class="form-select" data-control="select2"
                                        data-placeholder="Selecione..." name="conta_id" id="conta_id" disabled>
                                        <option value="">Carregando...</option>
                                    </select>
                                </div>
                                <!--end::Banco/Caixa select field-->
                            </div>
                        </div>
                        <!--end::Options-->
                    </div>
                    <!--end::Financial Accounts Section-->
                </div>
                <!--end::Modal body-->
                <!--begin::Modal footer-->
                <div class="modal-footer flex-center">
                    <!--begin::Button-->
                    <button type="reset" id="kt_modal_prestacao_contas_cancel"
                        class="btn btn-light me-3">Cancelar</button>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_modal_prestacao_contas_submit" class="btn btn-primary">
                        <span class="indicator-label">Enviar</span>
                        <span class="indicator-progress">Aguarde...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                    <!--end::Button-->
                </div>
                <!--end::Modal footer-->
            </div>
        </form>
        <!--end:Form-->
    </div>
</div>
<!--end::Modal - Prestação de Contas-->

@push('scripts')
    <script src="/assets/js/custom/utilities/modals/prestacao-contas.js"></script>
@endpush
