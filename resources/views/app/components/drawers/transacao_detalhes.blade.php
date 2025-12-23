<!--begin::Drawer - Detalhes da Transação-->
<div id="kt_drawer_transacao_detalhes" class="bg-body" data-kt-drawer="true" data-kt-drawer-activate="true"
    data-kt-drawer-toggle="#kt_drawer_transacao_button" data-kt-drawer-close="#kt_drawer_transacao_close"
    data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'300px', 'md': '500px'}">

    <!--begin::Card-->
    <div class="card shadow-none rounded-0 w-100">
        <!--begin::Header-->
        <div class="card-header" id="kt_drawer_transacao_header">
            <h3 class="card-title fw-bold text-gray-800">Detalhes da Transação</h3>
            <div class="card-toolbar">
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary"
                    id="kt_drawer_transacao_close">
                    <i class="bi bi-x fs-2"></i>
                </button>
            </div>
        </div>
        <!--end::Header-->

        <!--begin::Body-->
        <div class="card-body position-relative" id="kt_drawer_transacao_body">
            <!--begin::Content-->
            <div id="kt_drawer_transacao_scroll" class="position-relative scroll-y me-n5 pe-5" data-kt-scroll="true"
                data-kt-scroll-height="auto" data-kt-scroll-wrappers="#kt_drawer_transacao_body"
                data-kt-scroll-dependencies="#kt_drawer_transacao_header, #kt_drawer_transacao_footer"
                data-kt-scroll-offset="5px">

                <!--begin::Transação Info-->
                <div class="mb-7">
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-60px symbol-circle me-3">
                            <span class="symbol-label bg-light-primary">
                                <i class="bi bi-currency-exchange fs-1"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold"
                                id="drawer_transacao_descricao">Carregando...</a>
                            <span class="text-muted fw-semibold d-block" id="drawer_transacao_id">#0000</span>
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
                        <span class="badge badge-light-primary fs-7 fw-bold" id="drawer_transacao_tipo_badge">
                            ENTRADA
                        </span>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Valor:</div>
                        <div class="fw-bold text-gray-800 fs-3" id="drawer_transacao_valor">R$ 0,00</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Data de Competência:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_data">--/--/----</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Lançamento Padrão:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_lancamento">-</div>
                    </div>
                </div>
                <!--end::Detalhes Principais-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Detalhes do Documento-->
                <div class="mb-7">
                    <h5 class="mb-4">Detalhes do Documento</h5>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Tipo de Documento:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_tipo_doc">-</div>
                    </div>

                    <div class="mb-3">
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
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Detalhes Financeiros-->
                <div class="mb-7">
                    <h5 class="mb-4">Detalhes Financeiros</h5>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Origem:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_origem">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Entidade Financeira:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_entidade">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Centro de Custo:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_centro_custo">-</div>
                    </div>
                </div>
                <!--end::Detalhes Financeiros-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Histórico-->
                <div class="mb-7">
                    <h5 class="mb-4">Histórico Complementar</h5>
                    <div class="text-gray-800 fs-6" id="drawer_transacao_historico">
                        <span class="text-muted">Nenhum histórico complementar</span>
                    </div>
                </div>
                <!--end::Histórico-->

                <!--begin::Separator-->
                <div class="separator separator-dashed mb-7"></div>
                <!--end::Separator-->

                <!--begin::Anexos-->
                <div class="mb-7" id="drawer_transacao_anexos_section">
                    <h5 class="mb-4">Anexos</h5>
                    <div id="drawer_transacao_anexos">
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
                        <div class="fw-semibold text-gray-600 fs-7">Criado por:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_criado_por">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Criado em:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_criado_em">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Atualizado por:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_atualizado_por">-</div>
                    </div>

                    <div class="mb-3">
                        <div class="fw-semibold text-gray-600 fs-7">Atualizado em:</div>
                        <div class="fw-bold text-gray-800 fs-6" id="drawer_transacao_atualizado_em">-</div>
                    </div>
                </div>
                <!--end::Auditoria-->

            </div>
            <!--end::Content-->
        </div>
        <!--end::Body-->

        <!--begin::Footer-->
        <div class="card-footer py-5 text-center" id="kt_drawer_transacao_footer">
            <input type="hidden" id="drawer_transacao_id_hidden">

            <!-- Botão Gerar Recibo (exibido quando NÃO existe recibo) -->
            <button type="button" class="btn btn-warning btn-sm me-2" id="btn_gerar_recibo" style="display: none;">
                <i class="bi bi-receipt"></i> Gerar Recibo
            </button>

            <!-- Botão Editar Recibo (exibido quando JÁ existe recibo) -->
            <button type="button" class="btn btn-primary btn-sm me-2" id="btn_editar_recibo" style="display: none;">
                <i class="bi bi-pencil-square "></i> Editar Recibo
            </button>

            <!-- Botão Excluir Transação -->
            <button type="button" class="btn btn-danger btn-sm me-2" id="btn_excluir_transacao">
                <i class="bi bi-trash"></i> Excluir
            </button>
        </div>
        <!--end::Footer-->

    </div>
    <!--end::Card-->
</div>
<!--end::Drawer-->

<!--begin::JavaScript-->
<script>
    // Função para abrir o drawer com os detalhes da transação
    function abrirDrawerTransacao(transacaoId) {
        // Buscar dados da transação via AJAX
        fetch(`/financeiro/transacao/${transacaoId}/detalhes`)
            .then(response => response.json())
            .then(data => {
                // Preencher os dados no drawer
                document.getElementById('drawer_transacao_id_hidden').value = data.id;
                document.getElementById('drawer_transacao_id').textContent = `#${data.id}`;
                document.getElementById('drawer_transacao_descricao').textContent = data.descricao || 'Sem descrição';

                // Configurar botões de recibo
                const btnGerarRecibo = document.getElementById('btn_gerar_recibo');
                const btnEditarRecibo = document.getElementById('btn_editar_recibo');

                if (data.recibo) {
                    // Já existe recibo - mostrar botão de editar
                    btnGerarRecibo.style.display = 'none';
                    btnEditarRecibo.style.display = 'inline-block';

                    btnEditarRecibo.onclick = function() {
                        abrirModalReciboAjax(data, true); // true = modo edição
                    };
                } else {
                    // Não existe recibo - mostrar botão de gerar
                    btnGerarRecibo.style.display = 'inline-block';
                    btnEditarRecibo.style.display = 'none';

                    btnGerarRecibo.onclick = function() {
                        abrirModalReciboAjax(data, false); // false = modo criação
                    };
                }

                // Tipo e badge
                const tipoBadge = document.getElementById('drawer_transacao_tipo_badge');
                if (data.tipo === 'entrada') {
                    tipoBadge.textContent = 'ENTRADA';
                    tipoBadge.className = 'badge badge-light-success fs-7 fw-bold';
                } else {
                    tipoBadge.textContent = 'SAÍDA';
                    tipoBadge.className = 'badge badge-light-danger fs-7 fw-bold';
                }

                // Valor
                document.getElementById('drawer_transacao_valor').textContent =
                    `R$ ${parseFloat(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;

                // Data
                document.getElementById('drawer_transacao_data').textContent = data.data_competencia_formatada || '-';

                // Lançamento Padrão
                document.getElementById('drawer_transacao_lancamento').textContent = data.lancamento_padrao || '-';

                // Documento
                document.getElementById('drawer_transacao_tipo_doc').textContent = data.tipo_documento || '-';
                document.getElementById('drawer_transacao_num_doc').textContent = data.numero_documento || '-';
                document.getElementById('drawer_transacao_comprovacao').textContent = data.comprovacao_fiscal || '-';

                // Financeiro
                document.getElementById('drawer_transacao_origem').textContent = data.origem || '-';
                document.getElementById('drawer_transacao_entidade').textContent = data.entidade_financeira || '-';
                document.getElementById('drawer_transacao_centro_custo').textContent = data.centro_custo || '-';

                // Histórico
                const historicoEl = document.getElementById('drawer_transacao_historico');
                if (data.historico_complementar) {
                    historicoEl.innerHTML = `<p class="mb-0">${data.historico_complementar}</p>`;
                } else {
                    historicoEl.innerHTML = '<span class="text-muted">Nenhum histórico complementar</span>';
                }

                // Anexos
                const anexosEl = document.getElementById('drawer_transacao_anexos');
                if (data.anexos && data.anexos.length > 0) {
                    let anexosHtml = '<div class="d-flex flex-column gap-2">';
                    data.anexos.forEach(anexo => {
                        anexosHtml += `
                            <a href="${anexo.url}" target="_blank" class="d-flex align-items-center text-gray-800 text-hover-primary">
                                <i class="ki-duotone ki-file fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>
                                ${anexo.nome}
                            </a>
                        `;
                    });
                    anexosHtml += '</div>';
                    anexosEl.innerHTML = anexosHtml;
                } else {
                    anexosEl.innerHTML = '<span class="text-muted">Nenhum anexo</span>';
                }

                // Auditoria
                document.getElementById('drawer_transacao_criado_por').textContent = data.created_by_name || '-';
                document.getElementById('drawer_transacao_criado_em').textContent = data.created_at_formatado || '-';
                document.getElementById('drawer_transacao_atualizado_por').textContent = data.updated_by_name || '-';
                document.getElementById('drawer_transacao_atualizado_em').textContent = data.updated_at_formatado || '-';

                // Abrir o drawer
                const drawer = KTDrawer.getInstance(document.getElementById('kt_drawer_transacao_detalhes'));
                if (drawer) {
                    drawer.show();
                }
            })
            .catch(error => {
                console.error('Erro ao carregar detalhes da transação:', error);
                Swal.fire({
                    text: "Erro ao carregar os detalhes da transação.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
            });
    }

    // Handler para o botão de excluir transação
    document.addEventListener('DOMContentLoaded', function() {
        const btnExcluir = document.getElementById('btn_excluir_transacao');
        if (btnExcluir) {
            btnExcluir.addEventListener('click', function() {
                const transacaoId = document.getElementById('drawer_transacao_id_hidden').value;
                
                if (!transacaoId) {
                    Swal.fire({
                        text: "ID da transação não encontrado.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                    return;
                }
                
                Swal.fire({
                    title: 'Excluir Transação?',
                    html: `
                        <p>Esta ação irá:</p>
                        <ul class="text-start">
                            <li>Deletar a transação financeira</li>
                            <li>Deletar a movimentação relacionada</li>
                            <li>Reverter o saldo da entidade</li>
                            <li>Desfazer conciliação (se aplicável)</li>
                        </ul>
                        <p class="text-danger fw-bold mt-3">Esta ação não pode ser desfeita!</p>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mostrar loading
                        Swal.fire({
                            title: 'Processando...',
                            text: 'Excluindo transação',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Fazer requisição DELETE
                        fetch(`/financeiro/transacoes-financeiras/${transacaoId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Excluída!',
                                    text: data.message || 'Transação excluída com sucesso.',
                                    icon: 'success',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                }).then(() => {
                                    // Fechar o drawer
                                    const drawer = KTDrawer.getInstance(document.getElementById('kt_drawer_transacao_detalhes'));
                                    if (drawer) {
                                        drawer.hide();
                                    }
                                    
                                    // Recarregar a página para atualizar a lista
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Erro!',
                                    text: data.message || 'Erro ao excluir transação.',
                                    icon: 'error',
                                    buttonsStyling: false,
                                    confirmButtonText: 'Ok',
                                    customClass: {
                                        confirmButton: 'btn btn-primary'
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao excluir transação:', error);
                            Swal.fire({
                                title: 'Erro!',
                                text: 'Erro ao excluir transação. Tente novamente.',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        });
                    }
                });
            });
        }
    });
</script>
<!--end::JavaScript-->
