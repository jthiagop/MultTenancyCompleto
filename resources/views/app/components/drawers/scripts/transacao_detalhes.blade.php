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

    // Função para informar pagamento (abre confirmação e marca como pago)
    function informarPagamento(transacaoId) {
        Swal.fire({
            title: 'Informar Pagamento',
            text: 'Deseja marcar esta transação como paga/recebida?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, confirmar',
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success me-3',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/banco/mark-as-paid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: transacaoId,
                        data_pagamento: new Date().toISOString().split('T')[0]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            text: data.message || 'Pagamento registrado com sucesso!',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        }).then(() => {
                            reloadCurrentTable();
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Erro ao registrar pagamento.',
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
                    console.error('Erro:', error);
                    Swal.fire({
                        text: 'Erro ao processar a solicitação.',
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
    }

    // Função para definir transação como paga/recebida
    function definirComoPago(transacaoId) {
        Swal.fire({
            title: 'Confirmar',
            text: 'Deseja marcar esta transação como paga/recebida?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, confirmar',
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success me-3',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/banco/mark-as-paid', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: transacaoId,
                        data_pagamento: new Date().toISOString().split('T')[0]
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            text: data.message || 'Transação marcada com sucesso!',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        }).then(() => {
                            // Recarregar a tabela atual
                            if (typeof reloadCurrentTable === 'function') {
                                reloadCurrentTable();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Erro ao marcar transação.',
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
                    console.error('Erro:', error);
                    Swal.fire({
                        text: 'Erro ao processar a solicitação.',
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
    }

    // Função para definir transação como em aberto
    function definirComoAberto(transacaoId) {
        Swal.fire({
            title: 'Confirmar',
            text: 'Deseja marcar esta transação como em aberto?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sim, confirmar',
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-warning me-3',
                cancelButton: 'btn btn-secondary'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('/banco/mark-as-open', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: transacaoId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            text: data.message || 'Transação marcada como em aberto!',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        }).then(() => {
                            // Recarregar a tabela atual
                            if (typeof reloadCurrentTable === 'function') {
                                reloadCurrentTable();
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Erro ao marcar transação.',
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
                    console.error('Erro:', error);
                    Swal.fire({
                        text: 'Erro ao processar a solicitação.',
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
    }

    // Função auxiliar para recarregar a tabela atual
    function reloadCurrentTable() {
        // Tenta encontrar a aba ativa e recarregar sua DataTable
        const activePane = document.querySelector('.tab-pane.active.show');
        if (activePane) {
            const tableId = activePane.dataset.tableId;
            if (tableId) {
                const table = document.getElementById(tableId);
                if (table && $.fn.DataTable.isDataTable(table)) {
                    $(table).DataTable().ajax.reload(null, false);
                    return;
                }
            }
        }
        // Fallback: recarregar a página
        location.reload();
    }
</script>

