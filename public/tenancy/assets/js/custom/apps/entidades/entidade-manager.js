"use strict";

/**
 * EntidadeManager
 * Gerencia DataTables, Drawer de edição (AJAX + FormValidation) e exclusão de entidades financeiras.
 */
var EntidadeManager = (function () {
    // ─── Referências ───
    var dataTable;
    var drawerElement;
    var drawerInstance;
    var form;
    var formValidator;
    var submitBtn;
    var cancelBtn;
    var deleteBtn;

    // ─── Helpers ───
    function formatCurrency(value) {
        return parseFloat(value || 0).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL',
        });
    }

    // ─── DataTables ───
    function initDataTable() {
        var tableEl = document.getElementById('kt_entidades_table');
        if (!tableEl) return;

        dataTable = $(tableEl).DataTable({
            info: false,
            order: [[0, 'asc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            columnDefs: [
                // Coluna "Saldo Inicial" e "Saldo Atual" — ordena numericamente
                {
                    targets: [1, 3],
                    render: function (data) {
                        // Extrai número para ordenação correta
                        return data;
                    },
                },
                // Coluna "Ações" — não ordena
                {
                    targets: -1,
                    orderable: false,
                    searchable: false,
                },
            ],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Exibir _MENU_ registros',
                zeroRecords: 'Nenhuma entidade encontrada',
                paginate: {
                    first: 'Primeira',
                    last: 'Última',
                    next: '<i class="next"></i>',
                    previous: '<i class="previous"></i>',
                },
            },
        });

        // Filtro de busca customizado no header
        var searchInput = document.getElementById('kt_entidades_search');
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                dataTable.search(e.target.value).draw();
            });
        }
    }

    // ─── Select2 Helpers ───
    // Os selects usam <x-tenant-select> com data-control="select2",
    // então o Metronic já inicializa automaticamente.
    // Aqui só setamos valores e customizamos templates do banco.

    function reinitBancoSelect2() {
        var el = $('#edit_banco-select');
        if (el.length === 0) return;

        // Reinicializa com template customizado de logo do banco
        if (el.hasClass('select2-hidden-accessible')) {
            el.select2('destroy');
        }

        el.select2({
            placeholder: 'Selecione um banco',
            allowClear: true,
            dropdownParent: $(drawerElement),
            templateResult: formatBankOption,
            templateSelection: formatBankOption,
        });
    }

    function formatBankOption(state) {
        if (!state.id) return state.text;
        var iconUrl = $(state.element).attr('data-icon');
        if (!iconUrl) return state.text;
        return $(
            '<span class="d-flex align-items-center">' +
            '<img src="' + iconUrl + '" class="me-2" style="width:24px;height:24px;" />' +
            '<span>' + state.text + '</span>' +
            '</span>'
        );
    }

    // ─── Drawer de Edição ───
    function initDrawer() {
        drawerElement = document.getElementById('kt_drawer_edit_entidade');
        if (!drawerElement) return;

        // Garante instância do KTDrawer
        if (typeof KTDrawer !== 'undefined') {
            drawerInstance = KTDrawer.getInstance(drawerElement);
            if (!drawerInstance) {
                drawerInstance = new KTDrawer(drawerElement);
            }
        }

        form = document.getElementById('kt_drawer_edit_entidade_form');
        submitBtn = document.getElementById('kt_drawer_edit_entidade_submit');
        cancelBtn = document.getElementById('kt_drawer_edit_entidade_close_btn');
        deleteBtn = document.getElementById('kt_drawer_edit_entidade_delete');

        if (!form) return;

        initFormValidation();
        handleSubmit();
        handleCancel();
        handleDelete();
    }

    // ─── FormValidation ───
    function initFormValidation() {
        formValidator = FormValidation.formValidation(form, {
            fields: {
                nome: {
                    validators: {
                        notEmpty: { message: 'O nome é obrigatório' },
                        stringLength: { max: 100, message: 'Máximo 100 caracteres' },
                    },
                },
                bank_id: {
                    validators: {
                        callback: {
                            message: 'Selecione um banco',
                            callback: function (input) {
                                var tipo = document.getElementById('edit_tipo_hidden');
                                if (tipo && tipo.value === 'banco') {
                                    return input.value !== '' && input.value !== null;
                                }
                                return true; // Caixa não precisa
                            },
                        },
                    },
                },
                agencia: {
                    validators: {
                        callback: {
                            message: 'Informe a agência',
                            callback: function (input) {
                                var tipo = document.getElementById('edit_tipo_hidden');
                                if (tipo && tipo.value === 'banco') {
                                    return input.value.trim() !== '';
                                }
                                return true;
                            },
                        },
                    },
                },
                conta: {
                    validators: {
                        callback: {
                            message: 'Informe a conta',
                            callback: function (input) {
                                var tipo = document.getElementById('edit_tipo_hidden');
                                if (tipo && tipo.value === 'banco') {
                                    return input.value.trim() !== '';
                                }
                                return true;
                            },
                        },
                    },
                },
                account_type: {
                    validators: {
                        callback: {
                            message: 'Selecione a natureza da conta',
                            callback: function (input) {
                                var tipo = document.getElementById('edit_tipo_hidden');
                                if (tipo && tipo.value === 'banco') {
                                    return input.value !== '' && input.value !== null;
                                }
                                return true;
                            },
                        },
                    },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.mb-5',
                    eleInvalidClass: '',
                    eleValidClass: '',
                }),
            },
        });
    }

    // ─── Submit AJAX ───
    function handleSubmit() {
        if (!submitBtn) return;

        submitBtn.addEventListener('click', function (e) {
            e.preventDefault();

            if (!formValidator) return;

            formValidator.validate().then(function (status) {
                if (status !== 'Valid') return;

                submitBtn.setAttribute('data-kt-indicator', 'on');
                submitBtn.disabled = true;

                var formData = new FormData(form);
                var actionUrl = form.getAttribute('action');

                // Adiciona _method PUT
                formData.append('_method', 'PUT');

                fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, status: response.status, data: data };
                    });
                })
                .then(function (result) {
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;

                    if (result.ok && result.data.success) {
                        Swal.fire({
                            text: result.data.message,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: { confirmButton: 'btn btn-primary' },
                        }).then(function () {
                            // Atualiza a linha na tabela sem recarregar
                            if (result.data.data) {
                                updateTableRow(result.data.data);
                            }
                            if (drawerInstance) drawerInstance.hide();
                        });
                    } else {
                        // Trata erros de validação
                        var errorMsg = result.data.message || 'Erro ao atualizar a entidade.';
                        if (result.data.errors) {
                            var msgs = [];
                            Object.keys(result.data.errors).forEach(function (key) {
                                msgs.push(result.data.errors[key].join(', '));
                            });
                            errorMsg = msgs.join('<br>');
                        }
                        Swal.fire({
                            html: errorMsg,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: { confirmButton: 'btn btn-danger' },
                        });
                    }
                })
                .catch(function (error) {
                    console.error('Erro na requisição:', error);
                    submitBtn.removeAttribute('data-kt-indicator');
                    submitBtn.disabled = false;
                    Swal.fire({
                        text: 'Erro de comunicação com o servidor.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: { confirmButton: 'btn btn-danger' },
                    });
                });
            });
        });
    }

    // ─── Atualiza linha da tabela ───
    function updateTableRow(data) {
        if (!dataTable) {
            // Sem DataTables, recarrega página
            location.reload();
            return;
        }

        // Encontra a linha pelo data-entidade-id no botão de editar
        var rows = document.querySelectorAll('.btn-edit-entidade[data-entidade-id]');
        rows.forEach(function (btn) {
            if (btn.getAttribute('data-entidade-id') === String(data.id)) {
                var tr = btn.closest('tr');
                if (!tr) return;

                var cells = tr.querySelectorAll('td');
                if (cells.length >= 7) {
                    cells[0].textContent = data.nome;
                    // cells[1] = saldo inicial (não muda)
                    cells[2].textContent = data.updated_at;
                    // cells[3] = saldo atual — atualiza classes
                    var saldoAtual = parseFloat(data.saldo_atual || 0);
                    cells[3].className = 'text-end pe-0 ' + (saldoAtual >= 0 ? 'text-success' : 'text-danger');
                    cells[3].textContent = 'R$ ' + saldoAtual.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    // cells[4] = tipo (não muda)
                    // cells[5] = conta contábil
                    cells[5].innerHTML = data.conta_contabil
                        ? '<span class="text-gray-800">' + data.conta_contabil + '</span>'
                        : '<span class="text-muted">-</span>';
                    cells[6].textContent = data.descricao || '-';
                }

                // Atualiza data-attributes do botão
                btn.setAttribute('data-entidade-nome', data.nome);
                btn.setAttribute('data-entidade-descricao', data.descricao || '');
                btn.setAttribute('data-entidade-conta-contabil-id', data.conta_contabil_id || '');
            }
        });
    }

    // ─── Cancelar ───
    function handleCancel() {
        if (!cancelBtn) return;

        cancelBtn.addEventListener('click', function () {
            Swal.fire({
                text: 'Descartar alterações?',
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Sim, descartar',
                cancelButtonText: 'Não, continuar editando',
                customClass: {
                    confirmButton: 'btn btn-light me-3',
                    cancelButton: 'btn btn-primary',
                },
            }).then(function (result) {
                if (result.isConfirmed && drawerInstance) {
                    form.reset();
                    drawerInstance.hide();
                }
            });
        });

        // Botão X do header
        var closeX = document.getElementById('kt_drawer_edit_entidade_close');
        if (closeX) {
            closeX.addEventListener('click', function () {
                if (drawerInstance) {
                    form.reset();
                    drawerInstance.hide();
                }
            });
        }
    }

    // ─── Excluir ───
    function handleDelete() {
        if (!deleteBtn) return;

        deleteBtn.addEventListener('click', function () {
            var entidadeId = document.getElementById('edit_entidade_id').value;
            var entidadeNome = document.getElementById('edit_nome').value ||
                               document.getElementById('edit_tipo').value;
            var deleteUrl = deleteBtn.getAttribute('data-delete-url');

            if (!deleteUrl || !entidadeId) return;

            Swal.fire({
                html: '<p class="fs-5">Tem certeza que deseja excluir a entidade:<br><strong>' + entidadeNome + '</strong>?</p>' +
                      '<div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4 mt-4">' +
                      '<i class="ki-duotone ki-information-5 fs-2tx text-danger me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>' +
                      '<div class="text-start text-gray-700 fw-semibold">' +
                      '<p class="mb-1">Esta ação irá:</p>' +
                      '<ul class="mb-0"><li>Excluir todas as movimentações associadas</li>' +
                      '<li>Remover a entidade permanentemente</li></ul>' +
                      '<p class="text-danger fw-bold mt-2 mb-0">Esta ação não pode ser desfeita!</p>' +
                      '</div></div>',
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Sim, excluir',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-danger me-3',
                    cancelButton: 'btn btn-light',
                },
            }).then(function (result) {
                if (!result.isConfirmed) return;

                deleteBtn.setAttribute('data-kt-indicator', 'on');
                deleteBtn.disabled = true;

                fetch(deleteUrl, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    deleteBtn.removeAttribute('data-kt-indicator');
                    deleteBtn.disabled = false;

                    if (data.success) {
                        if (drawerInstance) drawerInstance.hide();
                        Swal.fire({
                            text: data.message,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: { confirmButton: 'btn btn-primary' },
                        }).then(function () {
                            // Remove a linha da tabela
                            removeTableRow(entidadeId);
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Erro ao excluir.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: { confirmButton: 'btn btn-danger' },
                        });
                    }
                })
                .catch(function () {
                    deleteBtn.removeAttribute('data-kt-indicator');
                    deleteBtn.disabled = false;
                    Swal.fire({
                        text: 'Erro de comunicação com o servidor.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: { confirmButton: 'btn btn-danger' },
                    });
                });
            });
        });
    }

    function removeTableRow(entidadeId) {
        var btn = document.querySelector('.btn-edit-entidade[data-entidade-id="' + entidadeId + '"]');
        if (btn) {
            var tr = btn.closest('tr');
            if (tr && dataTable) {
                dataTable.row(tr).remove().draw();
            } else if (tr) {
                tr.remove();
            }
        }
    }

    // ─── Abrir Drawer ───
    function openDrawer(button) {
        if (!drawerElement || !drawerInstance || !form) return;

        var id = button.getAttribute('data-entidade-id');
        var tipo = button.getAttribute('data-entidade-tipo');
        var nome = button.getAttribute('data-entidade-nome') || '';
        var bancoId = button.getAttribute('data-entidade-banco-id') || '';
        var agencia = button.getAttribute('data-entidade-agencia') || '';
        var conta = button.getAttribute('data-entidade-conta') || '';
        var accountType = button.getAttribute('data-entidade-account-type') || '';
        var descricao = button.getAttribute('data-entidade-descricao') || '';
        var contaContabilId = button.getAttribute('data-entidade-conta-contabil-id') || '';

        // Action URL
        var baseUrl = form.getAttribute('data-base-url');
        form.setAttribute('action', baseUrl + '/' + id);

        // Hidden fields
        document.getElementById('edit_entidade_id').value = id;
        document.getElementById('edit_tipo_hidden').value = tipo;

        // Tipo display
        var tipoDisplay = document.getElementById('edit_tipo');
        if (tipoDisplay) {
            tipoDisplay.value = tipo === 'banco' ? 'Banco' : 'Caixa';
        }

        // Alterna campos baseados no tipo
        var nomeGroup = document.getElementById('edit_nome-group');
        var bancoGroup = document.getElementById('edit_banco-group');
        var bancoDetailsGroup = document.getElementById('edit_banco-details-group');

        if (tipo === 'caixa') {
            if (nomeGroup) nomeGroup.classList.remove('d-none');
            if (bancoGroup) bancoGroup.classList.add('d-none');
            if (bancoDetailsGroup) bancoDetailsGroup.classList.add('d-none');
            document.getElementById('edit_nome').value = nome;
        } else {
            if (nomeGroup) nomeGroup.classList.add('d-none');
            if (bancoGroup) bancoGroup.classList.remove('d-none');
            if (bancoDetailsGroup) bancoDetailsGroup.classList.remove('d-none');
            document.getElementById('edit_agencia').value = agencia;
            document.getElementById('edit_conta').value = conta;
            // account_type é setado via Select2 trigger('change') no evento drawer.shown
        }

        // Descrição
        document.getElementById('edit_descricao').value = descricao;

        // Botão de excluir — configura URL
        if (deleteBtn) {
            deleteBtn.setAttribute('data-delete-url', baseUrl + '/' + id);
            deleteBtn.classList.remove('d-none');
        }

        // Abre drawer
        drawerInstance.show();

        // Inicializa Select2 após drawer visível
        drawerInstance.on('kt.drawer.shown', function () {
            if (tipo === 'banco') {
                // Reinicializa banco com template de logo
                reinitBancoSelect2();
                setTimeout(function () {
                    if (bancoId && bancoId !== 'null') {
                        $('#edit_banco-select').val(parseInt(bancoId)).trigger('change');
                    }
                }, 50);
            }

            // Conta contábil — já inicializado pelo Metronic, só seta valor
            setTimeout(function () {
                if (contaContabilId) {
                    $('#edit_conta_contabil_id').val(contaContabilId).trigger('change');
                }
            }, 50);

            // Natureza da conta — seta valor via Select2
            if (tipo === 'banco' && accountType) {
                setTimeout(function () {
                    $('#edit_account_type').val(accountType).trigger('change');
                }, 50);
            }
        });

        // Busca saldos via AJAX
        fetch(baseUrl + '/' + id + '/json', {
            headers: { 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success && data.data && data.data.entidade) {
                var ent = data.data.entidade;
                var saldoInicialEl = document.getElementById('edit_saldo_inicial');
                var saldoAtualEl = document.getElementById('edit_saldo_atual');
                if (saldoInicialEl) saldoInicialEl.value = formatCurrency(ent.saldo_inicial_real);
                if (saldoAtualEl) saldoAtualEl.value = formatCurrency(ent.saldo_atual);
            }
        })
        .catch(function (err) {
            console.error('Erro ao buscar saldos:', err);
        });

        // Reseta validação
        if (formValidator) formValidator.resetForm();
    }

    // ─── Bind botões de editar ───
    function bindEditButtons() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-edit-entidade');
            if (btn) {
                e.preventDefault();
                openDrawer(btn);
            }
        });
    }

    // ─── Init público ───
    return {
        init: function () {
            initDataTable();
            initDrawer();
            bindEditButtons();
        },
    };
})();

// Auto-init
document.addEventListener('DOMContentLoaded', function () {
    EntidadeManager.init();
});
