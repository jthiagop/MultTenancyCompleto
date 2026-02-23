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

    // Dados pendentes para preenchimento do drawer (lido pelo handler de shown)
    var _pendingEditData = null;

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

        if (!form) return;

        initFormValidation();
        handleSubmit();
        handleCancel();

        // Registra handler do kt.drawer.shown UMA vez (via API do KTDrawer)
        if (drawerInstance) {
            drawerInstance.on('kt.drawer.shown', handleDrawerShown);
        }
    }

    // ─── Handler único para kt.drawer.shown ───
    function handleDrawerShown() {
        if (!_pendingEditData) return;
        var data = _pendingEditData;
        _pendingEditData = null;

        // Re-seta valores simples como safety-net (garante visibilidade após transição)
        if (data.tipo === 'banco') {
            var nomeBancoInput = document.getElementById('edit_nome_banco');
            if (nomeBancoInput) nomeBancoInput.value = data.nome;

            // Inicializa Select2 do banco (precisa DOM visível)
            reinitBancoSelect2();
            setTimeout(function () {
                if (data.bancoId && data.bancoId !== 'null' && data.bancoId !== '') {
                    $('#edit_banco-select').val(parseInt(data.bancoId)).trigger('change');
                }
            }, 50);

            // Natureza da conta
            if (data.accountType) {
                setTimeout(function () {
                    $('#edit_account_type').val(data.accountType).trigger('change');
                }, 50);
            }
        } else {
            var nomeInput = document.getElementById('edit_nome');
            if (nomeInput) nomeInput.value = data.nome;
        }

        // Conta contábil
        setTimeout(function () {
            if (data.contaContabilId) {
                $('#edit_conta_contabil_id').val(data.contaContabilId).trigger('change');
            } else {
                $('#edit_conta_contabil_id').val(null).trigger('change');
            }
        }, 50);
    }

    // ─── FormValidation ───
    function initFormValidation() {
        formValidator = FormValidation.formValidation(form, {
            fields: {
                nome: {
                    validators: {
                        callback: {
                            message: 'O nome é obrigatório',
                            callback: function (input) {
                                var tipo = document.getElementById('edit_tipo_hidden');
                                // Só valida nome para tipo caixa
                                if (tipo && tipo.value === 'caixa') {
                                    return input.value.trim() !== '';
                                }
                                return true; // Banco usa nome_banco
                            },
                        },
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

    // ─── Abrir Drawer ───
    function openDrawer(button) {
        if (!drawerElement || !drawerInstance || !form) return;

        // Reseta o form e validação antes de popular
        form.reset();
        if (formValidator) formValidator.resetForm();

        var id = button.getAttribute('data-entidade-id');
        var tipo = button.getAttribute('data-entidade-tipo');
        var nome = button.getAttribute('data-entidade-nome') || '';
        var bancoId = button.getAttribute('data-entidade-banco-id') || '';
        var agencia = button.getAttribute('data-entidade-agencia') || '';
        var conta = button.getAttribute('data-entidade-conta') || '';
        var accountType = button.getAttribute('data-entidade-account-type') || '';
        var descricao = button.getAttribute('data-entidade-descricao') || '';
        var contaContabilId = button.getAttribute('data-entidade-conta-contabil-id') || '';

        // Armazena dados para o handler de shown (Select2 precisa DOM visível)
        _pendingEditData = {
            tipo: tipo,
            nome: nome,
            bancoId: bancoId,
            accountType: accountType,
            contaContabilId: contaContabilId,
        };

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
        } else {
            if (nomeGroup) nomeGroup.classList.add('d-none');
            if (bancoGroup) bancoGroup.classList.remove('d-none');
            if (bancoDetailsGroup) bancoDetailsGroup.classList.remove('d-none');
        }

        // Preenche inputs simples com pequeno atraso para garantir que
        // qualquer handler assíncrono disparado por form.reset() já completou
        setTimeout(function () {
            if (tipo === 'caixa') {
                var nomeInput = document.getElementById('edit_nome');
                if (nomeInput) nomeInput.value = nome;
            } else {
                var nomeBancoInput = document.getElementById('edit_nome_banco');
                if (nomeBancoInput) nomeBancoInput.value = nome;
                var agenciaInput = document.getElementById('edit_agencia');
                var contaInput = document.getElementById('edit_conta');
                if (agenciaInput) agenciaInput.value = agencia;
                if (contaInput) contaInput.value = conta;
            }

            // Descrição
            var descricaoInput = document.getElementById('edit_descricao');
            if (descricaoInput) descricaoInput.value = descricao;

            // Abre drawer (o handler handleDrawerShown cuida do Select2)
            drawerInstance.show();
        }, 10);
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
