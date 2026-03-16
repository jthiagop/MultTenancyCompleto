<x-tenant-drawer
    drawerId="kt_drawer_transacao_detalhes"
    title="Detalhes da Transação"
    width="{default:'300px', 'md': '500px'}"
    toggleButtonId="kt_drawer_transacao_button"
    closeButtonId="kt_drawer_transacao_close">

    <!--begin::Transação Info-->
    <div class="mb-7">
        <div class="d-flex align-items-center mb-5">
            <div class="symbol symbol-60px symbol-circle me-3" id="drawer_transacao_icon_wrapper">
                <span class="symbol-label bg-light-primary" id="drawer_transacao_icon">
                    <i class="bi bi-currency-exchange fs-1"></i>
                </span>
            </div>
            <div class="flex-grow-1">
                <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold"
                    id="drawer_transacao_descricao">Carregando...</a>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="text-muted fw-semibold" id="drawer_transacao_id">#0000</span>
                    <span class="badge fs-8 fw-bold" id="drawer_transacao_situacao_badge"></span>
                </div>
            </div>
        </div>
    </div>
    <!--end::Transação Info-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7"></div>
    <!--end::Separator-->

    <!--begin::Detalhes Principais-->
    <div class="mb-7">
        <h5 class="mb-4">Informações Principais</h5>

        <div class="mb-3">
            <span class="badge fs-7 fw-bold" id="drawer_transacao_tipo_badge">
                ENTRADA
            </span>
        </div>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Valor:</div>
            <div class="fw-bold fs-3" id="drawer_transacao_valor">R$ 0,00</div>
        </div>

        <div class="mb-3" id="drawer_transacao_valor_pago_row" style="display: none;">
            <div class="fw-semibold text-gray-600 fs-7">Valor Pago:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_valor_pago">-</div>
        </div>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Data de Competência:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_data">--/--/----</div>
        </div>

        <div class="mb-3" id="drawer_transacao_vencimento_row">
            <div class="fw-semibold text-gray-600 fs-7">Data de Vencimento:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_vencimento">-</div>
        </div>

        <div class="mb-3" id="drawer_transacao_pagamento_row" style="display: none;">
            <div class="fw-semibold text-gray-600 fs-7">Data de Pagamento:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_pagamento">-</div>
        </div>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Categoria:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_lancamento">-</div>
        </div>
    </div>
    <!--end::Detalhes Principais-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7"></div>
    <!--end::Separator-->

    <!--begin::Detalhes Financeiros-->
    <div class="mb-7">
        <h5 class="mb-4">Detalhes Financeiros</h5>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Entidade Financeira:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_entidade">-</div>
        </div>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Origem:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_origem">-</div>
        </div>

        <div class="mb-3" id="drawer_transacao_centro_custo_row">
            <div class="fw-semibold text-gray-600 fs-7">Centro de Custo:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_centro_custo">-</div>
        </div>

        <div class="mb-3" id="drawer_transacao_fornecedor_row" style="display: none;">
            <div class="fw-semibold text-gray-600 fs-7" id="drawer_transacao_fornecedor_label">Fornecedor/Cliente:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_fornecedor">-</div>
        </div>
    </div>
    <!--end::Detalhes Financeiros-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7" id="drawer_doc_separator"></div>
    <!--end::Separator-->

    <!--begin::Detalhes do Documento-->
    <div class="mb-7" id="drawer_transacao_doc_section">
        <h5 class="mb-4">Detalhes do Documento</h5>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Forma de Pagamento:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_tipo_doc">-</div>
        </div>

        <div class="mb-3" id="drawer_transacao_num_doc_row" style="display: none;">
            <div class="fw-semibold text-gray-600 fs-7">Número do Documento:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_num_doc">-</div>
        </div>

        <div class="mb-3">
            <div class="fw-semibold text-gray-600 fs-7">Comprovação Fiscal:</div>
            <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_comprovacao">-</div>
        </div>
    </div>
    <!--end::Detalhes do Documento-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7" id="drawer_historico_separator" style="display: none;"></div>
    <!--end::Separator-->

    <!--begin::Histórico-->
    <div class="mb-7" id="drawer_transacao_historico_section" style="display: none;">
        <h5 class="mb-4"><i class="bi bi-chat-left-text text-gray-500 me-2"></i>Histórico Complementar</h5>
        <div class="text-gray-800 fs-6 bg-light rounded p-3" id="drawer_transacao_historico">
            <span class="text-muted">Nenhum histórico complementar</span>
        </div>
    </div>
    <!--end::Histórico-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7" id="drawer_anexos_separator"></div>
    <!--end::Separator-->

    <!--begin::Anexos-->
    <div class="mb-7" id="drawer_transacao_anexos_section">
        <h5 class="mb-4"><i class="bi bi-paperclip text-gray-500 me-2"></i>Anexos <span class="text-muted fs-7 fw-normal" id="drawer_transacao_anexos_count"></span></h5>
        <div id="drawer_transacao_anexos">
            <span class="text-muted">Nenhum anexo</span>
        </div>
    </div>
    <!--end::Anexos-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7"></div>
    <!--end::Separator-->

    <!--begin::Parcela Info (exibido quando a transação é uma parcela filha)-->
    <div class="mb-5" id="drawer_transacao_parcela_info_section" style="display: none;">
        <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-4">
            <i class="bi bi-signpost-split text-primary fs-2 me-3"></i>
            <div class="d-flex flex-stack flex-grow-1">
                <div class="fw-semibold">
                    <div class="fs-6 text-gray-700" id="drawer_transacao_parcela_info_text">
                        <!-- Preenchido via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Parcela Info-->

    <!--begin::Parcelas (exibido apenas para transações parceladas)-->
    <div class="mb-7" id="drawer_transacao_parcelas_section" style="display: none;">
        <h5 class="mb-4">
            <i class="bi bi-signpost-split text-primary me-2"></i>Parcelas
        </h5>
        <div id="drawer_transacao_parcelas">
            <!-- Preenchido via JavaScript -->
        </div>
    </div>
    <!--end::Parcelas-->

    <!--begin::Separator-->
    <div class="separator separator-dashed mb-7" id="drawer_parcelas_separator" style="display: none;"></div>
    <!--end::Separator-->

    <!--begin::Auditoria-->
    <div class="mb-7">
        <h5 class="mb-4"><i class="bi bi-clock-history text-gray-500 me-2"></i>Auditoria</h5>

        <div class="d-flex justify-content-between mb-3">
            <div>
                <div class="fw-semibold text-gray-600 fs-8">Criado por</div>
                <div class="fw-bold text-gray-800 fs-7" id="drawer_transacao_criado_por">-</div>
            </div>
            <div class="text-end">
                <div class="fw-semibold text-gray-600 fs-8">Em</div>
                <div class="fw-bold text-gray-800 fs-7" id="drawer_transacao_criado_em">-</div>
            </div>
        </div>

        <div class="d-flex justify-content-between" id="drawer_transacao_atualizado_row" style="display: none;">
            <div>
                <div class="fw-semibold text-gray-600 fs-8">Atualizado por</div>
                <div class="fw-bold text-gray-800 fs-7" id="drawer_transacao_atualizado_por">-</div>
            </div>
            <div class="text-end">
                <div class="fw-semibold text-gray-600 fs-8">Em</div>
                <div class="fw-bold text-gray-800 fs-7" id="drawer_transacao_atualizado_em">-</div>
            </div>
        </div>
    </div>
    <!--end::Auditoria-->

    <x-slot name="footer">
        <input type="hidden" id="drawer_transacao_id_hidden">

        <!-- Botão Editar Transação -->
        <button type="button" class="btn btn-light-primary btn-sm me-2" id="btn_editar_transacao">
            <i class="bi bi-pencil-square"></i> Editar
        </button>

        <!-- Botão Gerar Recibo (exibido quando NÃO existe recibo) -->
        <button type="button" class="btn btn-primary btn-sm me-2" id="btn_gerar_recibo" style="display: none;">
            <i class="bi bi-receipt"></i> Gerar Recibo
        </button>

        <!-- Botão Editar Recibo (exibido quando JÁ existe recibo) -->
        <button type="button" class="btn btn-primary btn-sm me-2" id="btn_editar_recibo" style="display: none;">
            <i class="bi bi-pencil-square"></i> Editar Recibo
        </button>

        <!-- Botão Excluir Transação -->
        <button type="button" class="btn btn-danger btn-sm me-2" id="btn_excluir_transacao">
            <i class="bi bi-trash"></i> Excluir
        </button>
    </x-slot>

</x-tenant-drawer>

@push('scripts')
    @include('app.components.drawers.scripts.transacao_detalhes')
@endpush
