<script>
(function() {
    'use strict';

    const ParceiroShowDT = {
        dataUrl: "{{ route('parceiros.transacoes-data', $parceiro) }}",
        tableId: 'kt_parceiro_transacoes_table',
        dt: null,
        currentTab: '{{ $activeTab }}',

        escapeHtml(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML;
        },

        getColumns() {
            return [
                {
                    data: 'data_competencia',
                    name: 'data_competencia',
                    orderable: true,
                    render: function(data) {
                        return `<span class="text-gray-800 fw-semibold">${data || '-'}</span>`;
                    }
                },
                {
                    data: 'descricao',
                    name: 'descricao',
                    orderable: true,
                    render: function(data, type, row) {
                        let html = `<span class="text-gray-800 fw-semibold">${ParceiroShowDT.escapeHtml(data)}</span>`;
                        if (row.numero_documento) {
                            html += `<br><small class="text-muted">Doc: ${ParceiroShowDT.escapeHtml(row.numero_documento)}</small>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'tipo',
                    name: 'tipo',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<span class="badge ${row.tipo_badge} rounded-pill px-3 py-2 fs-8">${row.tipo_label}</span>`;
                    }
                },
                {
                    data: 'valor',
                    name: 'valor',
                    orderable: true,
                    className: 'text-end',
                    render: function(data, type, row) {
                        const color = row.tipo === 'entrada' ? 'text-success' : 'text-danger';
                        const prefix = row.tipo === 'entrada' ? '+' : '-';
                        let html = `<span class="${color} fw-bold">R$ ${prefix} ${data}</span>`;
                        if (row.valor_pago && row.valor_pago !== '0,00' && row.valor_pago !== data) {
                            html += `<br><small class="text-muted">Pago: R$ ${row.valor_pago}</small>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'situacao',
                    name: 'situacao',
                    orderable: true,
                    render: function(data, type, row) {
                        return `<span class="badge ${row.situacao_badge} rounded-pill px-3 py-2 fs-8">${row.situacao_label}</span>`;
                    }
                },
                {
                    data: 'data_vencimento',
                    name: 'data_vencimento',
                    orderable: true,
                    render: function(data) {
                        return `<span class="text-gray-600">${data || '-'}</span>`;
                    }
                },
                {
                    data: 'lancamento_padrao',
                    name: 'lancamento_padrao',
                    orderable: false,
                    render: function(data) {
                        return `<span class="text-gray-600 fs-7">${ParceiroShowDT.escapeHtml(data)}</span>`;
                    }
                },
                {
                    data: 'entidade',
                    name: 'entidade',
                    orderable: false,
                    render: function(data) {
                        return `<span class="text-gray-600 fs-7">${ParceiroShowDT.escapeHtml(data)}</span>`;
                    }
                },
            ];
        },

        initDataTable() {
            const self = this;

            if (self.dt) {
                self.dt.destroy();
                self.dt = null;
            }

            self.dt = $(`#${self.tableId}`).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: self.dataUrl,
                    type: 'GET',
                    data: function(d) {
                        d.tab = self.currentTab;
                        d.situacao = $('#parceiro_situacao_filter').val() || 'todos';
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX error:', error, thrown);
                    }
                },
                columns: self.getColumns(),
                order: [[0, 'desc']],
                pageLength: 50,
                lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Todos']],
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
                    processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Carregando...</span></div>',
                },
                dom: "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-4'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>",
                drawCallback: function() {
                    // Atualizar contadores nas tabs
                    self.updateTabCounts();
                }
            });
        },

        updateTabCounts() {
            const self = this;
            // Buscar contagens para cada tab
            const tabs = ['todas', 'a_receber', 'a_pagar'];
            tabs.forEach(function(tab) {
                $.ajax({
                    url: self.dataUrl,
                    type: 'GET',
                    data: { tab: tab, start: 0, length: 0, draw: 0 },
                    success: function(response) {
                        const count = response.recordsTotal || 0;
                        $(`#count_${tab}`).text(count);
                    }
                });
            });
        },

        bindEvents() {
            const self = this;

            // Troca de tab
            $('#parceiro_transacoes_tabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                self.currentTab = $(e.target).data('tab');
                if (self.dt) {
                    self.dt.ajax.reload();
                }
            });

            // Busca externa
            let searchTimer = null;
            $('#parceiro_transacoes_search').on('keyup', function() {
                clearTimeout(searchTimer);
                const val = this.value;
                searchTimer = setTimeout(function() {
                    if (self.dt) {
                        self.dt.search(val).draw();
                    }
                }, 400);
            });

            // Filtro de situação
            $('#parceiro_situacao_filter').on('change', function() {
                if (self.dt) {
                    self.dt.ajax.reload();
                }
            });
        },

        init() {
            this.initDataTable();
            this.bindEvents();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        ParceiroShowDT.init();
    });
})();
</script>
