<!--begin::Modal - Conciliação Bancária-->
<div class="modal fade" id="modal_conciliacao_bancaria" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <!--begin:Form-->
        <form id="kt_modal_conciliacao_bancaria_form" class="form" action="#">
            <!--begin::Modal content-->
            <div class="modal-content">
                <!--begin::Modal header-->
                <div class="modal-header" id="kt_modal_conciliacao_header">
                    <!--begin::Modal title-->
                    <h2>Filtrar Conciliações Bancárias</h2>
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
                    <!--begin::Input group - Período-->
                    <div class="row g-9 mb-8">
                        <!--begin::Col - Data Inicial-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Data Inicial</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </span>
                                <!--end::Icon-->
                                <!--begin::Datepicker-->
                                <input class="form-control form-control-solid ps-12" placeholder="Selecione uma data"
                                    name="data_inicial" id="conciliacao_data_inicial" />
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                        <!--begin::Col - Data Final-->
                        <div class="col-md-6 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Data Final</label>
                            <!--begin::Input-->
                            <div class="position-relative d-flex align-items-center">
                                <!--begin::Icon-->
                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                    <i class="fa-solid fa-calendar-days"></i>
                                </span>
                                <!--end::Icon-->
                                <!--begin::Datepicker-->
                                <input class="form-control form-control-solid ps-12" placeholder="Selecione uma data"
                                    name="data_final" id="conciliacao_data_final" />
                                <!--end::Datepicker-->
                            </div>
                            <!--end::Input-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group - Status-->
                    <div class="row g-9 mb-8">
                        <div class="col-md-12 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Status da Conciliação</label>
                            <select class="form-select form-select-solid" data-control="select2" 
                                data-hide-search="true" name="status_conciliacao" id="status_conciliacao">
                                <option value="">Selecione o status...</option>
                                <option value="todos">Todos</option>
                                <option value="ok">Conciliado (OK)</option>
                                <option value="pendente" selected>Pendente</option>
                                <option value="parcial">Parcial</option>
                                <option value="divergente">Divergente</option>
                                <option value="ignorado">Ignorado</option>
                            </select>
                            <!--begin::Help text-->
                            <div class="form-text">
                                <ul class="mb-0 ps-3">
                                    <li><strong>Conciliado (OK):</strong> Valores batem perfeitamente</li>
                                    <li><strong>Pendente:</strong> Lançamento ainda não conciliado</li>
                                    <li><strong>Parcial:</strong> Valor conciliado menor que o esperado</li>
                                    <li><strong>Divergente:</strong> Valor conciliado maior que o esperado</li>
                                    <li><strong>Ignorado:</strong> Lançamento marcado para ser ignorado</li>
                                    <li><strong>Todos:</strong> Exibe todos os lançamentos</li>
                                </ul>
                            </div>
                            <!--end::Help text-->
                        </div>
                    </div>
                    <!--end::Input group-->
                </div>
                <!--end::Modal body-->
                <!--begin::Modal footer-->
                <div class="modal-footer flex-center">
                    <!--begin::Button-->
                    <button type="reset" id="kt_modal_conciliacao_cancel"
                        class="btn btn-light me-3">Cancelar</button>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="kt_modal_conciliacao_submit" class="btn btn-primary">
                        <span class="indicator-label">Filtrar</span>
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
<!--end::Modal - Conciliação Bancária-->

@push('scripts')
    <script src="/assets/js/custom/utilities/modals/conciliacao-bancaria.js"></script>
@endpush
