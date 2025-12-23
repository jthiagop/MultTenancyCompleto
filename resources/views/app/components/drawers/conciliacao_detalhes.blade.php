<!--begin::Drawer - Detalhes da Conciliação-->
<div id="kt_drawer_conciliacao_detalhes" class="bg-body" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_conciliacao_button" data-kt-drawer-close="#kt_drawer_conciliacao_close"
    data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'md': '500px'}">

    <!--begin::Card-->
    <div class="card shadow-none rounded-0 w-100">
        <!--begin::Header-->
        <div class="card-header" id="kt_drawer_conciliacao_header">
            <h3 class="card-title fw-bold text-gray-800">Detalhes da Conciliação</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                    id="kt_drawer_conciliacao_close">
                    <i class="bi bi-x fs-2"></i>
                </button>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body position-relative" id="kt_drawer_conciliacao_body">
            <!--begin::Content-->
            <div id="kt_drawer_conciliacao_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
                data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_drawer_conciliacao_body"
                data-kt-scroll-dependencies="#kt_drawer_conciliacao_header, #kt_drawer_conciliacao_footer"
                data-kt-scroll-offset="5px">

                <!--begin::Conciliação Info-->
                <div class="mb-7">
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-60px symbol-circle me-3">
                            <span class="symbol-label bg-light-success">
                                <i class="bi bi-check2-circle fs-1"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                id="drawer_conciliacao_descricao">Carregando...</a>
                            <span class="text-muted fw-semibold d-block" id="drawer_conciliacao_id">#0000</span>
                        </div>
                    </div>
                </div>
                <!--end::Conciliação Info-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Status da Conciliação-->
                <div class="mb-7">
                    <h5 class="mb-4">Status da Conciliação</h5>
                    <div class="mb-3">
                        <span id="drawer_conciliacao_status_badge" class="badge badge-light-success fs-7 fw-bold">
                            CONCILIADO
                        </span>
                    </div>
                </div>
                <!--end::Status da Conciliação-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Detalhes Principais-->
                <div class="mb-7">
                    <h5 class="mb-4">Informações Principais</h5>

                    <div class="mb-3">
                        <span class="badge badge-light-primary fs-7 fw-bold" id="drawer_conciliacao_tipo_badge">
                            ENTRADA
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Valor:</div>
                        <div class="fw-bold text-gray-800 fs-3" id="drawer_conciliacao_valor">R$ 0,00</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Data do Extrato:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_data_extrato">--/--/----</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Data da Conciliação:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_data_conciliacao">--/--/----</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Lançamento Padrão:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_lancamento">-</div>
                    </div>
                </div>
                <!--end::Detalhes Principais-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Arquivo OFX-->
                <div class="mb-7">
                    <h5 class="mb-4">Arquivo OFX</h5>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Nome do Arquivo:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_arquivo_ofx">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Data de Importação:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_data_importacao">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Memo (OFX):</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_memo">-</div>
                    </div>
                </div>
                <!--end::Arquivo OFX-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Transação Vinculada-->
                <div class="mb-7">
                    <h5 class="mb-4">Transação Vinculada</h5>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">ID da Transação:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_transacao_id">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Entidade Financeira:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_entidade">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Centro de Custo:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_centro_custo">-</div>
                    </div>
                </div>
                <!--end::Transação Vinculada-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Histórico-->
                <div class="mb-7">
                    <h5 class="mb-4">Histórico Complementar</h5>
                    <div class="text-gray-800 fs-6" id="drawer_conciliacao_historico">
                        <span class="text-muted">Nenhum histórico complementar</span>
                    </div>
                </div>
                <!--end::Histórico-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Anexos-->
                <div class="mb-7">
                    <h5 class="mb-4">Anexos</h5>
                    <div id="drawer_conciliacao_anexos">
                        <span class="text-muted">Nenhum anexo</span>
                    </div>
                </div>
                <!--end::Anexos-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Auditoria-->
                <div class="mb-7">
                    <h5 class="mb-4">Informações de Auditoria</h5>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Conciliado por:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_criado_por">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Conciliado em:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_criado_em">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Atualizado por:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_atualizado_por">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Atualizado em:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_conciliacao_atualizado_em">-</div>
                    </div>
                </div>
                <!--end::Auditoria-->

            </div>
            <!--end::Content-->
        </div>
        <!--end::Body-->

        <!--begin::Footer-->
        <div class="card-footer py-5 text-center" id="kt_drawer_conciliacao_footer">
            <input type="hidden" id="drawer_conciliacao_id_hidden">

            <button type="button" class="btn btn-danger btn-sm me-2" id="btn_desfazer_conciliacao">
                <i class="bi bi-arrow-counterclockwise"></i> Desfazer Conciliação
            </button>

            <button type="button" class="btn btn-light-primary btn-sm" data-kt-drawer-dismiss="true">
                <i class="bi bi-x"></i> Fechar
            </button>
        </div>
        <!--end::Footer-->

    </div>
    <!--end::Card-->
</div>
<!--end::Drawer-->
