<script>
"use strict";

var ParceirosDataTable = (function () {
    const dataUrl = '{{ route("parceiros.data", [], false) }}';
    const storeUrl = '{{ route("parceiros.store", [], false) }}';
    const statsUrl = '{{ route("parceiros.stats", [], false) }}';
    const baseUrl = '{{ url("/financeiro/parceiros") }}';
    const activeTab = '{{ $activeTab }}';

    let dataTable = null;
    let tableId = null;
    let currentStatus = 'todos';

    // Detectar tabela via data-attribute
    function getTableId() {
        const pane = document.querySelector('[data-table-id]');
        return pane ? pane.getAttribute('data-table-id') : null;
    }

    // Mapeamento de colunas por tab (checkbox + colunas + ações)
    function getColumns() {
        const cols = [];

        // Checkbox
        cols.push({
            data: null,
            orderable: false,
            className: 'w-10px pe-2',
            render: function (data, type, row) {
                return '<div class="form-check form-check-sm form-check-custom form-check-solid">' +
                    '<input class="form-check-input" type="checkbox" value="' + row.hash_id + '" />' +
                    '</div>';
            }
        });

        // Nome (sempre)
        cols.push({
            data: 'nome',
            render: function (data, type, row) {
                let html = '<div class="d-flex flex-column">';
                html += '<span class="fw-bold text-gray-800">' + escapeHtml(data || '') + '</span>';
                if (row.nome_fantasia && activeTab !== 'fornecedores') {
                    html += '<span class="text-muted fs-7">' + escapeHtml(row.nome_fantasia) + '</span>';
                }
                html += '</div>';
                return html;
            }
        });

        // Natureza (todos e inativos)
        if (activeTab === 'todos' || activeTab === 'inativos') {
            cols.push({
                data: 'natureza_label',
                render: function (data, type, row) {
                    var badgeClass = row.natureza_badge_class || 'badge-light-primary';
                    return '<span class="badge ' + badgeClass + ' fs-7">' + escapeHtml(data || '') + '</span>';
                }
            });
        }

        // Nome Fantasia (fornecedores)
        if (activeTab === 'fornecedores') {
            cols.push({ data: 'nome_fantasia', defaultContent: '<span class="text-muted">-</span>' });
        }

        // Documento
        cols.push({
            data: 'documento',
            render: function (data) {
                if (!data || data === '-') return '<span class="text-muted">-</span>';
                return '<span class="text-gray-700">' + escapeHtml(data) + '</span>';
            }
        });

        // Telefone
        cols.push({ data: 'telefone', defaultContent: '<span class="text-muted">-</span>' });

        // Email
        cols.push({
            data: 'email',
            render: function (data) {
                if (!data) return '<span class="text-muted">-</span>';
                return '<a href="mailto:' + escapeHtml(data) + '" class="text-gray-600">' + escapeHtml(data) + '</a>';
            }
        });

        // Cidade (exceto inativos)
        if (activeTab !== 'inativos') {
            cols.push({ data: 'cidade', defaultContent: '<span class="text-muted">-</span>' });
        }

        // Ações
        cols.push({
            data: null,
            orderable: false,
            className: 'text-end',
            render: function (data, type, row) {
                let html = '<div class="d-flex justify-content-end gap-2">';

                // Editar
                html += '<button class="btn btn-icon btn-sm btn-light-primary btn-edit-parceiro" ' +
                    'data-id="' + row.hash_id + '" ' +
                    'data-nome="' + escapeAttr(row.nome || '') + '" ' +
                    'data-nome-fantasia="' + escapeAttr(row.nome_fantasia || '') + '" ' +
                    'data-tipo="' + escapeAttr(row.tipo || '') + '" ' +
                    'data-natureza="' + escapeAttr(row.natureza || '') + '" ' +
                    'data-cnpj="' + escapeAttr(row.documento && row.tipo_documento === 'CNPJ' ? row.documento : '') + '" ' +
                    'data-cpf="' + escapeAttr(row.documento && row.tipo_documento === 'CPF' ? row.documento : '') + '" ' +
                    'data-telefone="' + escapeAttr(row.telefone || '') + '" ' +
                    'data-email="' + escapeAttr(row.email || '') + '" ' +
                    'title="Editar"><i class="bi bi-pencil fs-6"></i></button>';

                // Toggle ativar/desativar
                if (row.active) {
                    html += '<button class="btn btn-icon btn-sm btn-light-danger btn-toggle-parceiro" ' +
                        'data-id="' + row.hash_id + '" data-action="desativar" title="Desativar">' +
                        '<i class="bi bi-toggle-on fs-5"></i></button>';
                } else {
                    html += '<button class="btn btn-icon btn-sm btn-light-success btn-toggle-parceiro" ' +
                        'data-id="' + row.hash_id + '" data-action="ativar" title="Ativar">' +
                        '<i class="bi bi-toggle-off fs-5"></i></button>';
                }

                // Excluir
                html += '<button class="btn btn-icon btn-sm btn-light-danger btn-delete-parceiro" ' +
                    'data-id="' + row.hash_id + '" data-nome="' + escapeAttr(row.nome || '') + '" title="Excluir">' +
                    '<i class="bi bi-trash fs-6"></i></button>';

                html += '</div>';
                return html;
            }
        });

        return cols;
    }

    // Inicializar DataTable com skeleton/wrapper pattern
    function initDataTable() {
        tableId = getTableId();
        if (!tableId) return;

        const $table = $('#' + tableId);
        if (!$table.length) return;

        const $skeleton = $('#skeleton-' + tableId);
        const $wrapper = $('#table-wrapper-' + tableId);

        dataTable = $table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataUrl,
                type: 'GET',
                data: function (d) {
                    d.tab = activeTab;
                    d.status = currentStatus;
                },
                dataSrc: function (json) {
                    // Esconder skeleton e mostrar tabela no primeiro load
                    if ($skeleton.length) $skeleton.addClass('d-none');
                    if ($wrapper.length) $wrapper.removeClass('d-none');
                    return json.data;
                },
                error: function (xhr) {
                    console.error('[Parceiros] Erro ao carregar dados:', xhr);
                    if ($skeleton.length) $skeleton.addClass('d-none');
                    if ($wrapper.length) $wrapper.removeClass('d-none');
                }
            },
            columns: getColumns(),
            order: [[1, 'asc']],
            pageLength: 50,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.0/i18n/pt-BR.json',
                emptyTable: 'Nenhum parceiro encontrado',
                loadingRecords: 'Carregando...',
                processing: '<div class="d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2"></span> Carregando...</div>'
            },
            drawCallback: function (settings) {
                const info = this.api().page.info();
                const $count = $('#selected-count-' + tableId);
                if ($count.length) {
                    $count.text(info.recordsTotal + ' registro(s)');
                }
                // Atualizar contagem de seleção
                updateSelectedCount();
            }
        });

        // Busca ao digitar (se houver campo de busca externo)
        $(document).on('keyup', '.parceiro-search', function () {
            if (dataTable) dataTable.search(this.value).draw();
        });

        // Checkbox select all
        $table.find('thead [data-kt-check]').on('change', function () {
            updateSelectedCount();
        });
        $table.on('change', 'tbody .form-check-input', function () {
            updateSelectedCount();
        });
    }

    // Atualizar contagem de selecionados
    function updateSelectedCount() {
        if (!tableId) return;
        const checked = $('#' + tableId + ' tbody .form-check-input:checked').length;
        const $count = $('#selected-count-' + tableId);
        if ($count.length) {
            if (checked > 0) {
                $count.text(checked + ' registro(s) selecionado(s)');
            } else {
                const info = dataTable ? dataTable.page.info() : null;
                $count.text((info ? info.recordsTotal : 0) + ' registro(s)');
            }
        }
    }

    // Carregar e atualizar stats nos segmented tabs
    function loadStats() {
        $.get(statsUrl, { tab: activeTab }, function (data) {
            if (!data) return;

            // Atualizar os valores nos segmented-tab-count
            const shell = document.querySelector('.segmented-shell');
            if (!shell) return;

            const countElements = shell.querySelectorAll('.segmented-tab-count');
            const tabButtons = shell.querySelectorAll('[data-tab-key]');

            tabButtons.forEach(function (btn) {
                const key = btn.getAttribute('data-tab-key');
                const countEl = btn.querySelector('.segmented-tab-count');
                if (countEl && data[key] !== undefined) {
                    countEl.textContent = data[key];
                }
            });

            console.log('[Parceiros] Stats carregados:', data);
        }).fail(function (xhr) {
            console.error('[Parceiros] Erro ao carregar stats:', xhr);
        });
    }

    // Abrir modal para edição
    function openEditModal(btn) {
        const $btn = $(btn);
        const modal = $('#modal_parceiro');
        const tipo = $btn.data('tipo');
        const natureza = $btn.data('natureza');

        modal.find('#modal_parceiro_title').text('Editar Cadastro');
        modal.find('#parceiro_id').val($btn.data('id'));

        // Select2: definir tipo de pessoa
        modal.find('#parceiro_tipo').val(tipo).trigger('change');

        // Checkboxes de natureza
        $('#check_fornecedor').prop('checked', natureza === 'fornecedor' || natureza === 'ambos');
        $('#check_cliente').prop('checked', natureza === 'cliente' || natureza === 'ambos');
        updateNaturezaHidden();

        modal.find('#parceiro_nome').val($btn.data('nome'));
        modal.find('#parceiro_nome_fantasia').val($btn.data('nome-fantasia'));
        modal.find('#parceiro_cnpj').val($btn.data('cnpj'));
        modal.find('#parceiro_cpf').val($btn.data('cpf'));
        modal.find('#parceiro_telefone').val($btn.data('telefone'));
        modal.find('#parceiro_email').val($btn.data('email'));

        toggleDocFields(tipo);

        const bsModal = new bootstrap.Modal(modal[0]);
        bsModal.show();
    }

    // Toggle campos baseado no tipo de pessoa (PJ/PF)
    function toggleDocFields(tipo) {
        const $cnpj = $('#campo_cnpj');
        const $cpf = $('#campo_cpf');
        const $nomeFantasia = $('#campo_nome_fantasia');
        const $nome = $('#campo_nome');

        if (tipo === 'pf') {
            // Pessoa Física: CPF + Nome Completo
            $cnpj.hide();
            $cpf.show();
            $nomeFantasia.hide();
            $nome.show();
        } else {
            // Pessoa Jurídica: CNPJ + Nome Fantasia
            $cnpj.show();
            $cpf.hide();
            $nomeFantasia.show();
            $nome.hide();
        }
    }

    // Atualiza o hidden de natureza a partir dos checkboxes
    function updateNaturezaHidden() {
        const isFornecedor = $('#check_fornecedor').is(':checked');
        const isCliente = $('#check_cliente').is(':checked');

        let natureza = 'fornecedor';
        if (isFornecedor && isCliente) {
            natureza = 'ambos';
        } else if (isCliente) {
            natureza = 'cliente';
        } else if (isFornecedor) {
            natureza = 'fornecedor';
        }
        $('#parceiro_natureza').val(natureza);
    }

    // Submit formulário (create/update)
    function handleFormSubmit(e) {
        e.preventDefault();

        const $form = $('#form_parceiro');
        const $btn = $('#btn_salvar_parceiro');
        const parceiroId = $('#parceiro_id').val();
        const isEdit = !!parceiroId;

        const formData = {};
        $form.serializeArray().forEach(function (item) {
            if (item.name !== 'parceiro_id') {
                formData[item.name] = item.value;
            }
        });

        // Enviar estado dos checkboxes de natureza
        formData['is_fornecedor'] = $('#check_fornecedor').is(':checked') ? '1' : '';
        formData['is_cliente'] = $('#check_cliente').is(':checked') ? '1' : '';
        // Atualizar hidden de natureza
        updateNaturezaHidden();

        let url = storeUrl;
        let method = 'POST';

        if (isEdit) {
            url = baseUrl + '/' + parceiroId;
            method = 'PUT';
        }

        $btn.attr('data-kt-indicator', 'on');
        $btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'Parceiro salvo com sucesso!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    const modal = bootstrap.Modal.getInstance($('#modal_parceiro')[0]);
                    if (modal) modal.hide();

                    resetForm();
                    if (dataTable) dataTable.ajax.reload(null, false);
                    loadStats();
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: res.message || 'Erro ao salvar.' });
                }
            },
            error: function (xhr) {
                let msg = 'Erro ao salvar parceiro.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                Swal.fire({ icon: 'error', title: 'Erro', html: msg });
            },
            complete: function () {
                $btn.removeAttr('data-kt-indicator');
                $btn.prop('disabled', false);
            }
        });
    }

    // Toggle ativar/desativar
    function handleToggle(btn) {
        const $btn = $(btn);
        const id = $btn.data('id');
        const action = $btn.data('action');

        Swal.fire({
            title: action === 'ativar' ? 'Ativar parceiro?' : 'Desativar parceiro?',
            text: action === 'ativar'
                ? 'O parceiro voltará a aparecer nas listagens ativas.'
                : 'O parceiro será movido para a aba Desativados.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: action === 'ativar' ? 'Sim, ativar' : 'Sim, desativar',
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: action === 'ativar' ? 'btn btn-success me-2' : 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl + '/' + id + '/toggle-active',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                            if (dataTable) dataTable.ajax.reload(null, false);
                            loadStats();
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Erro ao alterar status.' });
                    }
                });
            }
        });
    }

    // Excluir parceiro
    function handleDelete(btn) {
        const $btn = $(btn);
        const id = $btn.data('id');
        const nome = $btn.data('nome');

        Swal.fire({
            title: 'Excluir parceiro?',
            html: 'Deseja excluir <strong>' + escapeHtml(nome) + '</strong>?<br><span class="text-muted fs-7">Esta ação pode ser desfeita.</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger me-2',
                cancelButton: 'btn btn-secondary'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: baseUrl + '/' + id,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function (res) {
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: res.message, timer: 1500, showConfirmButton: false });
                            if (dataTable) dataTable.ajax.reload(null, false);
                            loadStats();
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Erro ao excluir parceiro.' });
                    }
                });
            }
        });
    }

    // Resetar formulário
    function resetForm() {
        $('#form_parceiro')[0].reset();
        $('#parceiro_id').val('');
        $('#modal_parceiro_title').text('Novo Cadastro');
        // Resetar Select2 tipo de pessoa
        $('#parceiro_tipo').val('pj').trigger('change');
        $('#parceiro_uf').val('').trigger('change');
        // Resetar checkboxes de natureza
        $('#check_fornecedor').prop('checked', true);
        $('#check_cliente').prop('checked', false);
        updateNaturezaHidden();
        toggleDocFields('pj');
    }

    // Escape helpers
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function escapeAttr(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    // Init
    function init() {
        initDataTable();
        loadStats();

        // Toggle campos ao mudar tipo de pessoa (Select2)
        $('#parceiro_tipo').on('change select2:select', function () {
            toggleDocFields($(this).val());
        });
        toggleDocFields('pj');

        // Atualizar hidden de natureza ao clicar nos checkboxes
        $('#check_fornecedor, #check_cliente').on('change', function () {
            updateNaturezaHidden();
        });

        // Form submit
        $('#form_parceiro').on('submit', handleFormSubmit);

        // Reset ao fechar modal
        $('#modal_parceiro').on('hidden.bs.modal', function () {
            resetForm();
        });

        // Listener de clique nas stats tabs (segmented-tabs-toolbar)
        $(document).on('click', '[data-status-tab]', function (e) {
            e.preventDefault();
            const status = $(this).data('status-tab') || 'todos';
            currentStatus = status;

            // Visual: ativar tab clicada
            $('[data-status-tab]').removeClass('active');
            $(this).addClass('active');

            // Recarregar tabela com novo filtro
            if (dataTable) {
                dataTable.ajax.reload(null, true);
            }
        });

        // Delegação de eventos na tabela
        $(document).on('click', '.btn-edit-parceiro', function () { openEditModal(this); });
        $(document).on('click', '.btn-toggle-parceiro', function () { handleToggle(this); });
        $(document).on('click', '.btn-delete-parceiro', function () { handleDelete(this); });
    }

    return { init: init };
})();

document.addEventListener('DOMContentLoaded', function () {
    ParceirosDataTable.init();
});
</script>
