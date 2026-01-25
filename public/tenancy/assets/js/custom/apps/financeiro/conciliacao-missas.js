"use strict";

// Class definition
var KTConciliacaoMissas = function () {
    // Shared variables
    var modal;
    var table;
    var selectedIds = [];

    // Private functions
    var initModal = function () {
        modal = document.querySelector('#kt_modal_conciliacao_missas');
        if (!modal) return;

        // Abrir modal quando o botão for clicado
        const btnHorariosMissas = document.querySelector('[data-kt-action="open-conciliacao-missas"]');
        if (btnHorariosMissas) {
            btnHorariosMissas.addEventListener('click', function (e) {
                // Verifica se o botão está desabilitado
                if (btnHorariosMissas.disabled || btnHorariosMissas.classList.contains('opacity-50')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                
                e.preventDefault();
                loadData();
                const modalInstance = new bootstrap.Modal(modal);
                modalInstance.show();
            });
        }
    };

    var loadData = function () {
        const tbody = document.querySelector('#kt_table_conciliacao_missas_body');
        if (!tbody) return;

        // Mostra loading
        tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></td></tr>';

        fetch('/conciliacao/candidatas', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderEstatisticas(data.estatisticas);
                renderTabela(data.transacoes);
            } else {
                Swal.fire({
                    text: data.message || 'Erro ao carregar dados',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Erro ao carregar dados:', error);
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10 text-danger">Erro ao carregar dados</td></tr>';
        });
    };

    var renderEstatisticas = function (estatisticas) {
        document.getElementById('stat-total-conciliadas').textContent = estatisticas.total_candidatas || estatisticas.total_conciliadas || 0;
        document.getElementById('stat-valor-total').textContent = 'R$ ' + (estatisticas.valor_total_candidatas || estatisticas.valor_total || '0,00');
        document.getElementById('stat-missas-envolvidas').textContent = estatisticas.missas_envolvidas || 0;
        document.getElementById('stat-ultima-atualizacao').textContent = estatisticas.ultima_atualizacao || 'N/A';
    };

    var renderTabela = function (transacoes) {
        const tbody = document.querySelector('#kt_table_conciliacao_missas_body');
        if (!tbody) return;

        if (!transacoes || transacoes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-10">Nenhuma transação candidata encontrada</td></tr>';
            return;
        }

        let html = '';
        transacoes.forEach(function (transacao) {
            const statusBadge = transacao.status === 'Sugerido como coleta de missa' 
                ? '<span class="badge badge-light-warning">Sugerido como coleta de missa</span>'
                : '<span class="badge badge-light-success">Conciliado</span>';
            
            html += `
                <tr>
                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input checkbox-transacao" type="checkbox" value="${transacao.id}" data-id="${transacao.id}" data-horario-missa-id="${transacao.horario_missa_id || ''}" />
                        </div>
                    </td>
                    <td>
                        <span class="text-gray-800">${transacao.data_hora || transacao.data}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 mb-1">${transacao.nome || transacao.memo || 'Coleta de missa'}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light-info">${transacao.origem || 'Pix'}</span>
                    </td>
                    <td>
                        <span class="text-gray-800">${transacao.missa_sugerida || transacao.missa || 'N/A'}</span>
                    </td>
                    <td>
                        ${statusBadge}
                    </td>
                    <td class="text-end">
                        <span class="text-gray-800 fw-bold">R$ ${transacao.valor}</span>
                    </td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-sm btn-success btn-confirmar" data-id="${transacao.id}" data-horario-missa-id="${transacao.horario_missa_id}">
                                <i class="bi bi-check"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-rejeitar" data-id="${transacao.id}">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        initCheckboxes();
        initAcoes();
    };

    var initCheckboxes = function () {
        // Select all checkbox
        const selectAll = document.getElementById('kt_select_all');
        const checkboxes = document.querySelectorAll('.checkbox-transacao');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                    updateSelectedIds(checkbox);
                });
            });
        }

        // Individual checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                updateSelectedIds(checkbox);
            });
        });
    };

    var updateSelectedIds = function (checkbox) {
        const id = parseInt(checkbox.value);
        if (checkbox.checked) {
            if (!selectedIds.includes(id)) {
                selectedIds.push(id);
            }
        } else {
            selectedIds = selectedIds.filter(selectedId => selectedId !== id);
        }
    };

    var processarConciliacao = function (bankStatementIds) {
        Swal.fire({
            title: 'Processando...',
            text: 'Aguarde enquanto processamos a conciliação',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/conciliacao/processar-missas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bank_statement_ids: bankStatementIds || []
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({
                    text: data.message || 'Conciliação processada com sucesso!',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                }).then(() => {
                    loadData(); // Recarrega os dados
                    selectedIds = []; // Limpa seleção
                });
            } else {
                Swal.fire({
                    text: data.message || 'Erro ao processar conciliação',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Erro ao processar conciliação:', error);
            Swal.fire({
                text: 'Erro ao processar conciliação',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'OK, entendi!',
                customClass: {
                    confirmButton: 'btn fw-bold btn-primary'
                }
            });
        });
    };

    var initProcessar = function () {
        // Botão processar todas
        const btnProcessarTodas = document.getElementById('kt_btn_processar_todas');
        if (btnProcessarTodas) {
            btnProcessarTodas.addEventListener('click', function () {
                Swal.fire({
                    title: 'Processar todas as transações?',
                    text: 'Isso irá processar todas as transações PIX não conciliadas. Deseja continuar?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, processar todas',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn fw-bold btn-success',
                        cancelButton: 'btn fw-bold btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Processa todas (array vazio = processa todas)
                        processarConciliacao([]);
                    }
                });
            });
        }

        // Botão processar selecionadas
        const btnProcessar = document.getElementById('kt_btn_processar_conciliacao');
        if (btnProcessar) {
            btnProcessar.addEventListener('click', function () {
                if (selectedIds.length === 0) {
                    Swal.fire({
                        text: 'Selecione pelo menos uma transação para processar',
                        icon: 'warning',
                        buttonsStyling: false,
                        confirmButtonText: 'OK, entendi!',
                        customClass: {
                            confirmButton: 'btn fw-bold btn-primary'
                        }
                    });
                    return;
                }
                processarConciliacao(selectedIds);
            });
        }

        // Botão atualizar
        const btnAtualizar = document.getElementById('kt_btn_atualizar');
        if (btnAtualizar) {
            btnAtualizar.addEventListener('click', function () {
                loadData();
            });
        }
    };

    var initAcoes = function () {
        // Botões confirmar
        document.querySelectorAll('.btn-confirmar').forEach(btn => {
            btn.addEventListener('click', function () {
                const bankStatementId = this.getAttribute('data-id');
                const horarioMissaId = this.getAttribute('data-horario-missa-id');
                
                if (!horarioMissaId) {
                    Swal.fire({
                        text: 'Horário de missa não encontrado',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'OK, entendi!',
                        customClass: {
                            confirmButton: 'btn fw-bold btn-primary'
                        }
                    });
                    return;
                }

                Swal.fire({
                    title: 'Confirmar como coleta de missa?',
                    text: 'Esta transação será marcada como coleta de missa e um lançamento financeiro será criado.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, confirmar',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn fw-bold btn-success',
                        cancelButton: 'btn fw-bold btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        confirmarTransacao(bankStatementId, horarioMissaId);
                    }
                });
            });
        });

        // Botões rejeitar
        document.querySelectorAll('.btn-rejeitar').forEach(btn => {
            btn.addEventListener('click', function () {
                const bankStatementId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Rejeitar transação?',
                    text: 'Esta transação não será mais sugerida como coleta de missa.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, rejeitar',
                    cancelButtonText: 'Cancelar',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn fw-bold btn-danger',
                        cancelButton: 'btn fw-bold btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        rejeitarTransacao(bankStatementId);
                    }
                });
            });
        });
    };

    var confirmarTransacao = function (bankStatementId, horarioMissaId) {
        Swal.fire({
            title: 'Processando...',
            text: 'Aguarde enquanto confirmamos a transação',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/conciliacao/confirmar-missa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bank_statement_id: bankStatementId,
                horario_missa_id: horarioMissaId
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({
                    text: data.message || 'Transação confirmada com sucesso!',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                }).then(() => {
                    loadData(); // Recarrega os dados
                });
            } else {
                Swal.fire({
                    text: data.message || 'Erro ao confirmar transação',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Erro ao confirmar transação:', error);
            Swal.fire({
                text: 'Erro ao confirmar transação',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'OK, entendi!',
                customClass: {
                    confirmButton: 'btn fw-bold btn-primary'
                }
            });
        });
    };

    var rejeitarTransacao = function (bankStatementId) {
        Swal.fire({
            title: 'Processando...',
            text: 'Aguarde enquanto rejeitamos a transação',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('/conciliacao/rejeitar-missa', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                bank_statement_id: bankStatementId
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                Swal.fire({
                    text: data.message || 'Transação rejeitada com sucesso!',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                }).then(() => {
                    loadData(); // Recarrega os dados
                });
            } else {
                Swal.fire({
                    text: data.message || 'Erro ao rejeitar transação',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK, entendi!',
                    customClass: {
                        confirmButton: 'btn fw-bold btn-primary'
                    }
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Erro ao rejeitar transação:', error);
            Swal.fire({
                text: 'Erro ao rejeitar transação',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'OK, entendi!',
                customClass: {
                    confirmButton: 'btn fw-bold btn-primary'
                }
            });
        });
    };

    var initSearch = function () {
        const searchInput = document.getElementById('kt_conciliacao_missas_search');
        if (!searchInput) return;

        searchInput.addEventListener('keyup', function (e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('#kt_table_conciliacao_missas_body tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    };

    // Public methods
    return {
        init: function () {
            initModal();
            initProcessar();
            initSearch();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTConciliacaoMissas.init();
});

