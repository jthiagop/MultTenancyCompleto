<!--begin::Modal - Conciliação de Missas-->
<div class="modal fade" id="kt_modal_conciliacao_missas" tabindex="-1" aria-hidden="true">
    <!--begin::Modal dialog-->
    <div class="modal-dialog modal-dialog-centered mw-900px">
        <!--begin::Modal content-->
        <div class="modal-content">
            <!--begin::Modal header-->
            <div class="modal-header" id="kt_modal_conciliacao_missas_header">
                <!--begin::Modal title-->
                <h2 class="fw-bold">Conciliação de Missas</h2>
                <!--end::Modal title-->
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                    <i class="bi bi-x fs-2"></i>
                </div>
                <!--end::Close-->
            </div>
            <!--end::Modal header-->
            <!--begin::Modal body-->
            <div class="modal-body scroll-y ">
                <!--begin::Estatísticas-->
                <div class="row g-5 ">
                    <div class="col-md-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center ">
                                        <span class="badge badge-light-primary me-2">Total</span>
                                    </div>
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1" id="stat-total-conciliadas">0</span>
                                    <span class="fs-7 fw-semibold text-gray-500">Transações Conciliadas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center ">
                                        <span class="badge badge-light-success me-2">Valor</span>
                                    </div>
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1" id="stat-valor-total">R$ 0,00</span>
                                    <span class="fs-7 fw-semibold text-gray-500">Valor Total Conciliado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center ">
                                        <span class="badge badge-light-info me-2">Missas</span>
                                    </div>
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1" id="stat-missas-envolvidas">0</span>
                                    <span class="fs-7 fw-semibold text-gray-500 text-center">Missas Envolvidas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-flush h-md-100">
                            <div class="card-body">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center ">
                                        <span class="badge badge-light-warning me-2">Atualização</span>
                                    </div>
                                    <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1" id="stat-ultima-atualizacao">N/A</span>
                                    <span class="fs-7 fw-semibold text-gray-500">Última Atualização</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Estatísticas-->

                <!--begin::Tabela-->
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="kt_conciliacao_missas_search" class="form-control form-control-solid w-250px ps-13" placeholder="Buscar lançamento..." />
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex justify-content-end gap-2" data-kt-user-table-toolbar="base">
                                <button type="button" class="btn btn-sm btn-success" id="kt_btn_processar_todas">
                                    <i class="bi bi-arrow-repeat fs-3"></i>
                                    Processar Todas
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="kt_btn_processar_conciliacao">
                                    <i class="bi bi-check fs-3"></i>
                                    Processar Selecionadas
                                </button>
                                <button type="button" class="btn btn-sm icon-btn btn-light" id="kt_btn_atualizar">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_conciliacao_missas">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="w-10px pe-2">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                                <input class="form-check-input" type="checkbox" id="kt_select_all" />
                                            </div>
                                        </th>
                                        <th class="min-w-150px">Data/Hora</th>
                                        <th class="min-w-200px">Nome do Lançamento</th>
                                        <th class="min-w-120px">Origem</th>
                                        <th class="min-w-150px">Missa Sugerida</th>
                                        <th class="min-w-100px">Status</th>
                                        <th class="min-w-100px text-end">Valor</th>
                                        <th class="min-w-100px text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold" id="kt_table_conciliacao_missas_body">
                                    <tr>
                                        <td colspan="3" class="text-center py-10">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Carregando...</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end::Tabela-->
            </div>
            <!--end::Modal body-->
            <!--begin::Modal footer-->
            <div class="modal-footer flex-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Fechar</button>
            </div>
            <!--end::Modal footer-->
        </div>
        <!--end::Modal content-->
    </div>
    <!--end::Modal dialog-->
</div>
<!--end::Modal - Conciliação de Missas-->

