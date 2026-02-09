<script>
"use strict";

var ParceirosDataTable = (function () {
    // URLs geradas pelo Laravel
    const dataUrl = '{{ route("parceiros.data", [], false) }}';
    const storeUrl = '{{ route("parceiros.store", [], false) }}';
    const statsUrl = '{{ route("parceiros.stats", [], false) }}';
    const baseUrl = '{{ url("/financeiro/parceiros") }}';
    const activeTab = '{{ $activeTab }}';
    
    let dataTable = null;
    let tableId = null;

    // Detectar qual tabela está na página
    function getTableId() {
        const pane = document.querySelector('[data-table-id]');
        return pane ? pane.getAttribute('data-table-id') : null;
    }

    // Colunas por tab
    function getColumns() {
        const base = [
            {
                data: 'nome',
                render: function (data, type, row) {
                    let html = '<div class="d-flex flex-column">';
                    html += '<span class="fw-bold text-gray-800">' + escapeHtml(data || '') + '</span>';
                    if (row.nome_fantasia) {
                        html += '<span class="text-muted fs-7">' + escapeHtml(row.nome_fantasia) + '</span>';
                    }
                    html += '</div>';
                    return html;
                }
            }
        ];

        if (activeTab === 'todos' || activeTab === 'inativos') {
            base.push({
                data: 'tipo_label',
                render: function (data, type, row) {
                    let badgeClass = 'badge-light-primary';
                    if (row.tipo === 'fornecedor') badgeClass = 'badge-light-info';
                    else if (row.tipo === 'cliente') badgeClass = 'badge-light-success';
                    else if (row.tipo === 'ambos') badgeClass = 'badge-light-warning';
                    return '<span class="badge ' + badgeClass + ' fs-7">' + escapeHtml(data || '') + '</span>';
                }
            });
        }

        if (activeTab === 'fornecedores') {
            base.push({ data: 'nome_fantasia', defaultContent: '-' });
        }

        // Documento
        base.push({
            data: 'documento',
            render: function (data, type, row) {
                if (!data || data === '-') return '<span class="text-muted">-</span>';
                let label = row.tipo_documento || '';
                return '<span class="text-gray-700">' + escapeHtml(data) + '</span>';
            }
        });

        // Telefone
        base.push({ data: 'telefone', defaultContent: '<span class="text-muted">-</span>' });

        // Email
        base.push({
            data: 'email',
            render: function (data) {
                if (!data) return '<span class="text-muted">-</span>';
                return '<a href="mailto:' + escapeHtml(data) + '" class="text-gray-600">' + escapeHtml(data) + '</a>';
            }
        });

        // Cidade (não em inativos)
        if (activeTab !== 'inativos') {
            base.push({ data: 'cidade', defaultContent: '<span class="text-muted">-</span>' });
        }

        // Ações
        base.push({
            data: null,
            orderable: false,
            className: 'text-end',
            render: function (data, type, row) {
                let html = '<div class="d-flex justify-content-end gap-2">';
                
                // Botão Editar
                html += '<button class="btn btn-icon btn-sm btn-light-primary btn-edit-parceiro" ' +
                    'data-id="' + row.hash_id + '" ' +
                    'data-nome="' + escapeAttr(row.nome || '') + '" ' +
                    'data-nome-fantasia="' + escapeAttr(row.nome_fantasia || '') + '" ' +
                    'data-tipo="' + escapeAttr(row.tipo || '') + '" ' +
                    'data-cnpj="' + escapeAttr(row.documento && row.tipo_documento === 'CNPJ' ? row.documento : '') + '" ' +
                    'data-cpf="' + escapeAttr(row.documento && row.tipo_documento === 'CPF' ? row.documento : '') + '" ' +
                    'data-telefone="' + escapeAttr(row.telefone || '') + '" ' +
                    'data-email="' + escapeAttr(row.email || '') + '" ' +
                    'title="Editar">' +
                    '<i class="bi bi-pencil fs-6"></i></button>';

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

        return base;
    }

    // Inicializar DataTable
    function initDataTable() {
        tableId = getTableId();
        if (!tableId) return;

        const $table = $('#' + tableId);
        if (!$table.length) return;

        dataTable = $table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: dataUrl,
                type: 'GET',
                data: function (d) {
                    d.tab = activeTab;
                },
                error: function (xhr) {
                    console.error('Erro ao carregar parceiros:', xhr);
                }
            },
            columns: getColumns(),
            order: [[0, 'asc']],
            pageLength: 50,
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.0/i18n/pt-BR.json',
                emptyTable: 'Nenhum parceiro encontrado',
                loadingRecords: 'Carregando...',
                processing: '<div class="d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2"></span> Carregando...</div>'
            },
            drawCallback: function (settings) {
                const info = this.api().page.info();
                const $count = $('#count-' + tableId);
                if ($count.length) {
                    $count.text(info.recordsTotal + ' registro(s)');
                }
            }
        });

        // Busca via input
        $table.closest('.card').find('.parceiro-search').on('keyup', function () {
            dataTable.search(this.value).draw();
        });
    }

    // Carregar stats para as tabs
    function loadStats() {
        $.get(statsUrl, function (data) {
            // Atualizar badges das tabs (se existirem)
            if (data.todos !== undefined) {
                console.log('[Parceiros] Stats:', data);
            }
        });
    }

    // Abrir modal para edição
    function openEditModal(btn) {
        const $btn = $(btn);
        const modal = $('#modal_parceiro');
        
        modal.find('#modal_parceiro_title').text('Editar Parceiro');
        modal.find('#parceiro_id').val($btn.data('id'));
        modal.find('#parceiro_tipo').val($btn.data('tipo'));
        modal.find('#parceiro_nome').val($btn.data('nome'));
        modal.find('#parceiro_nome_fantasia').val($btn.data('nome-fantasia'));
        modal.find('#parceiro_cnpj').val($btn.data('cnpj'));
        modal.find('#parceiro_cpf').val($btn.data('cpf'));
        modal.find('#parceiro_telefone').val($btn.data('telefone'));
        modal.find('#parceiro_email').val($btn.data('email'));

        toggleDocFields($btn.data('tipo'));
        
        const bsModal = new bootstrap.Modal(modal[0]);
        bsModal.show();
    }

    // Toggle campos CNPJ/CPF baseado no tipo
    function toggleDocFields(tipo) {
        const $cnpj = $('#campo_cnpj');
        const $cpf = $('#campo_cpf');
        
        switch (tipo) {
            case 'fornecedor':
                $cnpj.show();
                $cpf.hide();
                break;
            case 'cliente':
                $cnpj.hide();
                $cpf.show();
                break;
            default: // ambos
                $cnpj.show();
                $cpf.show();
        }
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

        // Determinar URL e método
        let url = storeUrl;
        let method = 'POST';

        if (isEdit) {
            url = baseUrl + '/' + parceiroId;
            method = 'PUT';
        }

        // Indicator
        $btn.attr('data-kt-indicator', 'on');
        $btn.prop('disabled', true);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'Parceiro salvo com sucesso!',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Fechar modal e recarregar
                    const modal = bootstrap.Modal.getInstance($('#modal_parceiro')[0]);
                    if (modal) modal.hide();

                    resetForm();
                    if (dataTable) dataTable.ajax.reload(null, false);
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
                    const errors = xhr.responseJSON.errors;
                    msg = Object.values(errors).flat().join('<br>');
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
                : 'O parceiro será movido para a aba Inativos.',
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
        $('#modal_parceiro_title').text('Novo Parceiro');
        toggleDocFields('fornecedor');
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

        // Toggle campos CNPJ/CPF ao mudar tipo
        $('#parceiro_tipo').on('change', function () {
            toggleDocFields(this.value);
        });
        toggleDocFields('fornecedor');

        // Form submit
        $('#form_parceiro').on('submit', handleFormSubmit);

        // Reset ao fechar modal
        $('#modal_parceiro').on('hidden.bs.modal', function () {
            resetForm();
        });

        // Delegação de eventos na tabela
        $(document).on('click', '.btn-edit-parceiro', function () {
            openEditModal(this);
        });
        $(document).on('click', '.btn-toggle-parceiro', function () {
            handleToggle(this);
        });
        $(document).on('click', '.btn-delete-parceiro', function () {
            handleDelete(this);
        });
    }

    return { init: init };
})();

// Inicializar quando DOM pronto
document.addEventListener('DOMContentLoaded', function () {
    ParceirosDataTable.init();
});
</script>
