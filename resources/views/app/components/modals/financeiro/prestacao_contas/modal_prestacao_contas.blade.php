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
                        <!--begin::Col - Período Inicial-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Período Inicial</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fa-solid fa-calendar-days fs-3"></i>
                                </span>
                                <input class="form-control ps-12" placeholder="Selecione uma data"
                                    name="data_inicial" id="data_inicial" />
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col - Período Final-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Período Final</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fa-solid fa-calendar-days fs-3"></i>
                                </span>
                                <input class="form-control ps-12" placeholder="Selecione uma data"
                                    name="data_final" id="data_final" />
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col - Modelo-->
                        <div class="col-md-4 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Modelo</label>
                            <div class="d-flex gap-5 mt-3">
                                <label class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="modelo" value="horizontal"
                                        id="modelo_horizontal" />
                                    <span class="form-check-label fw-semibold">
                                        Horizontal
                                    </span>
                                </label>
                                <label class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="modelo" value="vertical"
                                        id="modelo_vertical" />
                                    <span class="form-check-label fw-semibold">
                                        Vertical
                                    </span>
                                </label>
                            </div>
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Separator-->
                    <div class="separator separator-dashed my-8"></div>
                    <!--end::Separator-->

                    <!--begin::Filtros de Dados Section-->
                    <div class="mb-8">
                        <h5 class="text-gray-700 fw-bold mb-5">
                            <i class="bi bi-funnel me-1"></i> Filtros de Dados
                        </h5>

                        <!--begin::Tipo de Data-->
                        <div class="row g-9 mb-6">
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Filtrar por</label>
                                <div class="d-flex gap-5">
                                    <label class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="tipo_data"
                                            value="competencia" id="tipo_data_competencia" checked />
                                        <span class="form-check-label fw-semibold">
                                            Data de Competência
                                        </span>
                                    </label>
                                    <label class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="tipo_data"
                                            value="pagamento" id="tipo_data_pagamento" />
                                        <span class="form-check-label fw-semibold">
                                            Data de Pagamento
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!--end::Tipo de Data-->

                        <!--begin::Situação-->
                        <div class="row g-9 mb-6">
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Situação</label>
                                <select class="form-select" data-control="select2" data-hide-search="true"
                                    data-placeholder="Todas as situações" data-allow-clear="true"
                                    name="situacoes[]" id="situacoes" multiple="multiple">
                                    <option value="pago">Pago</option>
                                    <option value="recebido">Recebido</option>
                                    <option value="em_aberto">Em Aberto</option>
                                    <option value="pago_parcial">Pago Parcial</option>
                                    <option value="atrasado">Atrasado</option>
                                </select>
                                <div class="form-text text-muted">Deixe vazio para incluir todas</div>
                            </div>
                        </div>
                        <!--end::Situação-->

                        <!--begin::Categoria-->
                        <div class="row g-9 mb-6">
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Categoria Financeira</label>
                                <select class="form-select" data-control="select2"
                                    data-placeholder="Todas as categorias" data-allow-clear="true"
                                    name="categorias[]" id="categorias" multiple="multiple">
                                    <option value="">Carregando...</option>
                                </select>
                                <div class="form-text text-muted">Deixe vazio para incluir todas</div>
                            </div>
                        </div>
                        <!--end::Categoria-->

                        <!--begin::Parceiro-->
                        <div class="row g-9 mb-6">
                            <div class="col-md-12 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Parceiro / Fornecedor</label>
                                <select class="form-select" data-control="select2"
                                    data-placeholder="Todos os parceiros" data-allow-clear="true"
                                    name="parceiro_id" id="parceiro_id">
                                    <option value="">Carregando...</option>
                                </select>
                                <div class="form-text text-muted">Deixe vazio para incluir todos</div>
                            </div>
                        </div>
                        <!--end::Parceiro-->

                        <!--begin::Opções adicionais-->
                        <div class="row g-9 mb-2">
                            <!--begin::Comprovação Fiscal-->
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Comprovação Fiscal</label>
                                <div class="form-check form-check-custom mt-2">
                                    <input class="form-check-input" type="checkbox"
                                        name="comprovacao_fiscal" id="comprovacao_fiscal" value="1" />
                                    <label class="form-check-label fw-semibold" for="comprovacao_fiscal">
                                        Somente com comprovação fiscal
                                    </label>
                                </div>
                            </div>
                            <!--end::Comprovação Fiscal-->

                            <!--begin::Tipo de Valor-->
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Exibir valores</label>
                                <div class="d-flex gap-5 mt-2">
                                    <label class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="tipo_valor"
                                            value="previsto" id="tipo_valor_previsto" checked />
                                        <span class="form-check-label fw-semibold">
                                            Previstos
                                        </span>
                                    </label>
                                    <label class="form-check form-check-custom">
                                        <input class="form-check-input" type="radio" name="tipo_valor"
                                            value="pago" id="tipo_valor_pago" />
                                        <span class="form-check-label fw-semibold">
                                            Efetivos (Pagos)
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <!--end::Tipo de Valor-->
                        </div>
                        <!--end::Opções adicionais-->
                    </div>
                    <!--end::Filtros de Dados Section-->

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
                        <span class="indicator-label">
                            <i class="fa-regular fa-file-pdf me-1"></i> Gerar PDF
                        </span>
                        <span class="indicator-progress">Gerando PDF...
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
    <script src="/tenancy/assets/js/custom/utilities/modals/prestacao-contas.js"></script>
@endpush
