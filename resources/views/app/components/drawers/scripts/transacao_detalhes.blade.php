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

                // Situação badge
                const situacaoBadgeEl = document.getElementById('drawer_transacao_situacao_badge');
                const sit = data.situacao || 'em_aberto';
                if (sit === 'pago' || sit === 'recebido') {
                    situacaoBadgeEl.textContent = sit === 'pago' ? 'Pago' : 'Recebido';
                    situacaoBadgeEl.className = 'badge badge-light-success fs-8 fw-bold';
                } else if (sit === 'agendado' || data.agendado) {
                    situacaoBadgeEl.textContent = 'Agendado';
                    situacaoBadgeEl.className = 'badge badge-light-info fs-8 fw-bold';
                } else {
                    situacaoBadgeEl.textContent = 'Em aberto';
                    situacaoBadgeEl.className = 'badge badge-light-warning fs-8 fw-bold';
                }

                // Valor com cor contextual
                const valorEl = document.getElementById('drawer_transacao_valor');
                const valorFormatadoPrincipal = `R$ ${parseFloat(data.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                valorEl.textContent = valorFormatadoPrincipal;
                valorEl.className = data.tipo === 'entrada' ? 'fw-bold fs-3 text-success' : 'fw-bold fs-3 text-danger';

                // Valor pago (se diferente do valor principal)
                const valorPagoRow = document.getElementById('drawer_transacao_valor_pago_row');
                if (data.valor_pago && parseFloat(data.valor_pago) > 0 && parseFloat(data.valor_pago) !== parseFloat(data.valor)) {
                    const valorPagoFormatado = `R$ ${parseFloat(data.valor_pago).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('drawer_transacao_valor_pago').textContent = valorPagoFormatado;
                    valorPagoRow.style.display = 'block';
                } else {
                    valorPagoRow.style.display = 'none';
                }

                // Data
                document.getElementById('drawer_transacao_data').textContent = data.data_competencia_formatada || '-';

                // Data de vencimento
                const vencimentoRow = document.getElementById('drawer_transacao_vencimento_row');
                if (data.data_vencimento_formatada) {
                    document.getElementById('drawer_transacao_vencimento').textContent = data.data_vencimento_formatada;
                    vencimentoRow.style.display = 'block';
                } else {
                    vencimentoRow.style.display = 'none';
                }

                // Data de pagamento
                const pagamentoRow = document.getElementById('drawer_transacao_pagamento_row');
                if (data.data_pagamento_formatada) {
                    document.getElementById('drawer_transacao_pagamento').textContent = data.data_pagamento_formatada;
                    pagamentoRow.style.display = 'block';
                } else {
                    pagamentoRow.style.display = 'none';
                }

                // Categoria (Lançamento Padrão)
                document.getElementById('drawer_transacao_lancamento').textContent = data.lancamento_padrao || '-';

                // Financeiro
                document.getElementById('drawer_transacao_entidade').textContent = data.entidade_financeira || '-';
                document.getElementById('drawer_transacao_origem').textContent = data.origem || '-';

                // Centro de custo
                const centroCustoRow = document.getElementById('drawer_transacao_centro_custo_row');
                if (data.centro_custo) {
                    document.getElementById('drawer_transacao_centro_custo').textContent = data.centro_custo;
                    centroCustoRow.style.display = 'block';
                } else {
                    centroCustoRow.style.display = 'none';
                }

                // Fornecedor/Cliente
                const fornecedorRow = document.getElementById('drawer_transacao_fornecedor_row');
                if (data.parceiro_nome) {
                    const labelEl = document.getElementById('drawer_transacao_fornecedor_label');
                    labelEl.textContent = data.tipo === 'entrada' ? 'Cliente:' : 'Fornecedor:';
                    document.getElementById('drawer_transacao_fornecedor').textContent = data.parceiro_nome;
                    fornecedorRow.style.display = 'block';
                } else {
                    fornecedorRow.style.display = 'none';
                }

                // Documento
                document.getElementById('drawer_transacao_tipo_doc').textContent = data.tipo_documento || '-';
                const numDocRow = document.getElementById('drawer_transacao_num_doc_row');
                if (data.numero_documento) {
                    document.getElementById('drawer_transacao_num_doc').textContent = data.numero_documento;
                    numDocRow.style.display = 'block';
                } else {
                    numDocRow.style.display = 'none';
                }
                document.getElementById('drawer_transacao_comprovacao').textContent = data.comprovacao_fiscal || '-';

                // Histórico complementar - ocultar seção se vazio
                const historicoEl = document.getElementById('drawer_transacao_historico');
                const historicoSection = document.getElementById('drawer_transacao_historico_section');
                const historicoSeparator = document.getElementById('drawer_historico_separator');
                if (data.historico_complementar) {
                    historicoEl.innerHTML = `<p class="mb-0">${data.historico_complementar}</p>`;
                    historicoSection.style.display = 'block';
                    historicoSeparator.style.display = 'block';
                } else {
                    historicoSection.style.display = 'none';
                    historicoSeparator.style.display = 'none';
                }

                // Anexos
                const anexosEl = document.getElementById('drawer_transacao_anexos');
                if (data.anexos && data.anexos.length > 0) {
                    let anexosHtml = '<div class="d-flex flex-column gap-3">';
                    data.anexos.forEach(anexo => {
                        const isLink = anexo.forma_anexo === 'link';
                        let iconClass = 'bi bi-file-earmark text-primary';
                        let bgClass = 'bg-light-primary';
                        if (isLink) {
                            iconClass = 'bi bi-link-45deg text-info';
                            bgClass = 'bg-light-info';
                        } else if (anexo.extensao) {
                            const ext = anexo.extensao.toLowerCase();
                            if (ext === 'pdf') { iconClass = 'bi bi-file-earmark-pdf text-danger'; bgClass = 'bg-light-danger'; }
                            else if (['jpg','jpeg','png','gif','webp'].includes(ext)) { iconClass = 'bi bi-file-earmark-image text-info'; bgClass = 'bg-light-info'; }
                            else if (['xls','xlsx','csv'].includes(ext)) { iconClass = 'bi bi-file-earmark-excel text-success'; bgClass = 'bg-light-success'; }
                            else if (['doc','docx'].includes(ext)) { iconClass = 'bi bi-file-earmark-word text-primary'; bgClass = 'bg-light-primary'; }
                            else if (ext === 'xml') { iconClass = 'bi bi-file-earmark-code text-warning'; bgClass = 'bg-light-warning'; }
                        }
                        let tamanhoStr = '';
                        if (anexo.tamanho) {
                            const kb = anexo.tamanho / 1024;
                            tamanhoStr = kb >= 1024 ? (kb / 1024).toFixed(1) + ' MB' : Math.round(kb) + ' KB';
                        }
                        const nomeExibicao = anexo.nome || (isLink ? 'Link externo' : 'Arquivo');
                        const tipoBadge = anexo.tipo_anexo ? `<span class="badge badge-light-primary fs-9">${anexo.tipo_anexo}</span>` : '';
                        const descHtml = anexo.descricao ? `<span class="text-muted fs-8 d-block">${anexo.descricao}</span>` : '';
                        const tamanhoHtml = tamanhoStr ? `<span class="text-muted fs-9">${tamanhoStr}</span>` : '';
                        anexosHtml += `
                            <a href="${anexo.url}" target="_blank" class="d-flex align-items-center p-3 rounded ${bgClass} text-hover-primary" style="text-decoration:none">
                                <div class="symbol symbol-35px symbol-circle me-3">
                                    <span class="symbol-label ${bgClass}"><i class="${iconClass} fs-3"></i></span>
                                </div>
                                <div class="flex-grow-1 me-2">
                                    <span class="text-gray-800 fw-semibold fs-7 d-block">${nomeExibicao}</span>
                                    ${descHtml}
                                    <div class="d-flex align-items-center gap-2 mt-1">${tipoBadge} ${tamanhoHtml}</div>
                                </div>
                                <i class="bi bi-download text-gray-500 fs-5"></i>
                            </a>
                        `;
                    });
                    anexosHtml += '</div>';
                    anexosEl.innerHTML = anexosHtml;
                    const countEl = document.getElementById('drawer_transacao_anexos_count');
                    if (countEl) countEl.textContent = '(' + data.anexos.length + ')';
                } else {
                    anexosEl.innerHTML = '<span class="text-muted">Nenhum anexo</span>';
                    const countEl = document.getElementById('drawer_transacao_anexos_count');
                    if (countEl) countEl.textContent = '';
                }

                // Auditoria
                document.getElementById('drawer_transacao_criado_por').textContent = data.created_by_name || '-';
                document.getElementById('drawer_transacao_criado_em').textContent = data.created_at_formatado || '-';

                const atualizadoRow = document.getElementById('drawer_transacao_atualizado_row');
                if (data.updated_by_name) {
                    document.getElementById('drawer_transacao_atualizado_por').textContent = data.updated_by_name;
                    document.getElementById('drawer_transacao_atualizado_em').textContent = data.updated_at_formatado || '-';
                    atualizadoRow.style.display = 'flex';
                } else {
                    atualizadoRow.style.display = 'none';
                }

                // Parcela Info (quando transação é filha)
                const parcelaInfoSection = document.getElementById('drawer_transacao_parcela_info_section');
                const parcelaInfoText = document.getElementById('drawer_transacao_parcela_info_text');
                if (data.parcela_info) {
                    parcelaInfoSection.style.display = 'block';
                    let infoHtml = 'Esta é a <strong>parcela ' + data.parcela_info.numero_parcela + '/' + data.parcela_info.total_parcelas + '</strong>';
                    if (data.parcela_info.parent_descricao) {
                        infoHtml += ' do lançamento <strong>"' + data.parcela_info.parent_descricao + '"</strong>';
                    }
                    parcelaInfoText.innerHTML = infoHtml;
                } else {
                    parcelaInfoSection.style.display = 'none';
                    parcelaInfoText.innerHTML = '';
                }

                // Parcelas
                const parcelasSection = document.getElementById('drawer_transacao_parcelas_section');
                const parcelasSeparator = document.getElementById('drawer_parcelas_separator');
                const parcelasEl = document.getElementById('drawer_transacao_parcelas');

                if (data.is_parcelado && data.parcelas && data.parcelas.length > 0) {
                    parcelasSection.style.display = 'block';
                    parcelasSeparator.style.display = 'block';

                    let parcelasHtml = '<div class="table-responsive"><table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3">';
                    parcelasHtml += '<thead><tr class="fw-bold text-muted fs-7">';
                    parcelasHtml += '<th class="min-w-30px">#</th>';
                    parcelasHtml += '<th class="min-w-80px">Vencimento</th>';
                    parcelasHtml += '<th class="min-w-70px text-end">Valor</th>';
                    parcelasHtml += '<th class="min-w-70px">Situação</th>';
                    parcelasHtml += '</tr></thead><tbody>';

                    data.parcelas.forEach(function(parcela) {
                        let situacaoBadge = '';
                        const sit = parcela.situacao || 'em_aberto';
                        if (sit === 'pago' || sit === 'recebido') {
                            situacaoBadge = '<span class="badge badge-light-success py-1 px-2 fs-8">Pago</span>';
                        } else if (sit === 'em_aberto') {
                            situacaoBadge = '<span class="badge badge-light-warning py-1 px-2 fs-8">Em aberto</span>';
                        } else {
                            situacaoBadge = '<span class="badge badge-light-secondary py-1 px-2 fs-8">' + sit.replace('_', ' ') + '</span>';
                        }

                        const valorFormatado = parseFloat(parcela.valor).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                        parcelasHtml += '<tr>';
                        parcelasHtml += '<td class="fw-bold">' + parcela.numero_parcela + '/' + parcela.total_parcelas + '</td>';
                        parcelasHtml += '<td>' + (parcela.data_vencimento || '-') + '</td>';
                        parcelasHtml += '<td class="text-end">R$ ' + valorFormatado + '</td>';
                        parcelasHtml += '<td>' + situacaoBadge + '</td>';
                        parcelasHtml += '</tr>';
                    });

                    parcelasHtml += '</tbody></table></div>';
                    parcelasEl.innerHTML = parcelasHtml;
                } else {
                    parcelasSection.style.display = 'none';
                    parcelasSeparator.style.display = 'none';
                    parcelasEl.innerHTML = '';
                }

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

    // Handler para os botões do footer
    document.addEventListener('DOMContentLoaded', function() {
        // Botão Editar - abre o drawer de edição
        const btnEditar = document.getElementById('btn_editar_transacao');
        if (btnEditar) {
            btnEditar.addEventListener('click', function() {
                const transacaoId = document.getElementById('drawer_transacao_id_hidden').value;
                if (!transacaoId) return;

                // Fechar o drawer de detalhes
                const drawer = KTDrawer.getInstance(document.getElementById('kt_drawer_transacao_detalhes'));
                if (drawer) {
                    drawer.hide();
                }

                // Abrir o drawer de edição
                if (typeof abrirDrawerEdicao === 'function') {
                    abrirDrawerEdicao(transacaoId);
                }
            });
        }

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

                        // Fazer requisição DELETE usando a URL correta
                        fetch(`/relatorios/transacoes-financeiras/${transacaoId}`, {
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

    // Função para inverter o tipo de uma transação (Receita ↔ Despesa)
    function inverterTipoTransacao(transacaoId) {
        Swal.fire({
            text: 'Deseja inverter o tipo desta transação (Receita ↔ Despesa)? As parcelas filhas também serão invertidas.',
            icon: 'warning',
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: 'Sim, inverter',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'btn btn-warning',
                cancelButton: 'btn btn-active-light'
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch('/banco/reverse-type', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: transacaoId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            text: data.message || 'Tipo invertido com sucesso.',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        }).then(function() {
                            reloadCurrentTable();
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Erro ao inverter tipo.',
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

