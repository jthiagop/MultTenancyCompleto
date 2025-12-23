"use strict";

// Class definition
var KTAppEcommerceReportShipping = function () {
    // Shared variables
    var table;
    var datatable;

    // Private functions
    var initDatatable = function () {
        // Obter URL da rota de dados (definida no Blade ou fallback)
        var dataUrl = typeof bancoTransacoesDataUrl !== 'undefined' 
            ? bancoTransacoesDataUrl 
            : '/banco/transacoes-data';
        
        // Init datatable com server-side processing
        datatable = $(table).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": dataUrl,
                "type": "GET",
                "data": function(d) {
                    // Adicionar filtros personalizados
                    var daterangepicker = $("#kt_ecommerce_report_shipping_daterangepicker");
                    if (daterangepicker.length && daterangepicker.data('daterangepicker')) {
                        var picker = daterangepicker.data('daterangepicker');
                        d.start_date = picker.startDate.format('YYYY-MM-DD');
                        d.end_date = picker.endDate.format('YYYY-MM-DD');
                    }
                    
                    // Filtro de tipo (status)
                    var statusFilter = $('[data-kt-ecommerce-order-filter="status"]');
                    if (statusFilter.length) {
                        d.tipo = statusFilter.val() || '';
                    }
                },
                "dataSrc": function(json) {
                    
                    // Verificar se há erro na resposta
                    if (json.error) {
                        console.error('[DataTables] Erro na resposta:', json.error);
                        return [];
                    }
                    
                    // Verificar se a estrutura está correta
                    if (!json || typeof json !== 'object') {
                        console.error('[DataTables] Resposta inválida - não é um objeto:', json);
                        return [];
                    }
                    
                    // Verificar se tem o campo data
                    if (!json.hasOwnProperty('data')) {
                        console.error('[DataTables] Resposta inválida - campo "data" não encontrado:', json);
                        return [];
                    }
                    
                    // Verificar se data é um array
                    if (!Array.isArray(json.data)) {
                        console.error('[DataTables] Resposta inválida - "data" não é um array:', typeof json.data, json.data);
                        return [];
                    }
                    
                    return json.data;
                },
                "error": function(xhr, error, thrown) {
                    console.error('[DataTables] Erro ao carregar dados:', error);
                    console.error('[DataTables] Status:', xhr.status);
                    console.error('[DataTables] Tipo de erro:', thrown);
                    console.error('[DataTables] Resposta:', xhr.responseText);
                    
                    // Tentar parsear a resposta para ver o erro
                    try {
                        var response = JSON.parse(xhr.responseText);
                        console.error('[DataTables] Erro parseado:', response);
                    } catch (e) {
                        console.error('[DataTables] Não foi possível parsear a resposta como JSON');
                    }
                    
                    // Forçar parar o indicador de carregamento
                    // Verificar se a API processing existe (depende da versão do DataTables)
                    if (datatable && typeof datatable.processing === 'function') {
                        datatable.processing(false);
                    } else {
                         // Fallback para jQuery se disponível
                         $('#kt_ecommerce_report_shipping_table_processing').hide();
                    }
                }
            },
            "columns": [
                { "data": 0, "name": "id", "orderable": true },
                { "data": 1, "name": "data_competencia", "orderable": true },
                { "data": 2, "name": "tipo_documento", "orderable": false },
                { "data": 3, "name": "comprovacao_fiscal", "orderable": false },
                { "data": 4, "name": "descricao", "orderable": false },
                { "data": 5, "name": "tipo", "orderable": true },
                { "data": 6, "name": "valor", "orderable": true },
                { "data": 7, "name": "origem", "orderable": false },
                { "data": 8, "name": "anexos", "orderable": false },
                { "data": 9, "name": "actions", "orderable": false }
            ],
            "order": [[0, 'desc']],
            "pageLength": 50,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
            "language": {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            },
            "drawCallback": function(settings) {
                // Reinicializar tooltips após cada desenho
                if (typeof KTApp !== 'undefined' && KTApp.initTooltips) {
                    KTApp.initTooltips();
                } else if (typeof $ !== 'undefined' && $.fn.tooltip) {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            }
        });
    }

    // Init daterangepicker
 // Inicializar o DateRangePicker
var initDaterangepicker = function () {
    // Configura a localidade para português do Brasil
    moment.locale('pt-br');

    // Configuração inicial do intervalo de datas
    var start = moment().subtract(29, "days");
    var end = moment();
    var input = $("#kt_ecommerce_report_shipping_daterangepicker");

    // Callback para exibir o intervalo selecionado
    function cb(start, end) {
        input.html(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));
        filterByDateRange(start, end);
    }

    // Inicializar o DateRangePicker com opções de intervalo
    input.daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
            "Hoje": [moment(), moment()],
            "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
            "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
            "Este Mês": [moment().startOf("month"), moment().endOf("month")],
            "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Todos": [moment().subtract(100, "years"), moment().add(1, "year")]
        },
        locale: {
            format: "DD/MM/YYYY", // Define o formato padrão para o picker
            applyLabel: "Aplicar",
            cancelLabel: "Cancelar",
            fromLabel: "De",
            toLabel: "Até",
            customRangeLabel: "Personalizado",
            weekLabel: "S",
            daysOfWeek: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"],
            monthNames: [
                "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"
            ],
            firstDay: 0 // Início da semana no domingo
        }
    }, cb);

    // Executa o callback inicial para definir o valor padrão
    cb(start, end);
};


    // Filter by date range
    var filterByDateRange = (start, end) => {
        // Com server-side processing, apenas recarregar os dados
        if (datatable) {
            datatable.ajax.reload();
        }
    }

    // Filtra pelo status
    var handleStatusFilter = () => {
        const filtroStatus = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
        $(filtroStatus).on('change', e => {
            // Com server-side processing, recarregar os dados
            if (datatable) {
                datatable.ajax.reload();
            }
        });
    };

    // Hook export buttons
    var exportButtons = () => {
        const documentTitle = 'Shipping Report';
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: documentTitle
                },
                {
                    extend: 'excelHtml5',
                    title: documentTitle
                },
                {
                    extend: 'csvHtml5',
                    title: documentTitle
                },
                {
                    extend: 'pdfHtml5',
                    extend: 'pdfHtml5',
                    title: documentTitle,
                text: 'Exportar para PDF',
                orientation: 'landscape', // ou 'portrait'
                pageSize: 'A4', // ou 'LETTER', 'LEGAL', etc.
                customize: function (doc) {
                    doc.content[1].table.widths = [
                        '5%', '10%', '20%', '30%', '5%','10%', '10%','5%', '0',
                    ];
                    doc.styles.tableHeader.fillColor = 'blue';
                    doc.styles.tableHeader.color = 'white';
                    doc.styles.tableHeader.alignment = 'center';
                    doc.styles.tableHeader.fontSize = 12;
                    doc.styles.title.fontSize = 14;
                    doc.styles.title.alignment = 'center';
                    doc.content[0].text = doc.content[0].text.toUpperCase();
                    doc.footer = function(page, pages) {
                        return {
                            columns: [
                                'Este é um rodapé personalizado',
                                {
                                    alignment: 'right',
                                    text: [
                                        { text: page.toString(), italics: true },
                                        ' de ',
                                        { text: pages.toString(), italics: true }
                                    ]
                                }
                            ],
                            margin: [10, 0]
                        };
                    };
                }
                }
            ]
        }).container().appendTo($('#kt_ecommerce_report_shipping_export'));

        // Hook dropdown menu click event to datatable export buttons
        const exportButtons = document.querySelectorAll('#kt_ecommerce_report_shipping_export_menu [data-kt-ecommerce-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();

                // Get clicked export value
                const exportValue = e.target.getAttribute('data-kt-ecommerce-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

                // Trigger click event on hidden datatable export buttons
                target.click();
            });
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search/
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-ecommerce-order-filter="search"]');
        if (filterSearch && datatable) {
            // Com server-side processing, o DataTables já gerencia a busca automaticamente
            // Mas podemos adicionar um debounce para melhorar a performance
            var searchTimeout;
            filterSearch.addEventListener('keyup', function (e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    datatable.search(e.target.value).draw();
                }, 500); // Aguarda 500ms após o usuário parar de digitar
            });
        }
    }

    // Public methods
    return {
        init: function () {
            table = document.querySelector('#kt_ecommerce_report_shipping_table');

            if (!table) {
                return;
            }

            initDatatable();
            initDaterangepicker();
            exportButtons();
            handleSearchDatatable();
            handleStatusFilter();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceReportShipping.init();

    // Inicializar gráfico combinado se estiver na aba overview
    if (document.getElementById('kt_charts_widget_combined')) {

        // Carregar ApexCharts se não estiver carregado
        if (typeof ApexCharts === 'undefined') {
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@latest';
            script.onload = function() {
                KTAppBancoChartCombined.init();
            };
            script.onerror = function() {
                console.error('Erro ao carregar ApexCharts');
            };
            document.head.appendChild(script);
        } else {
            KTAppBancoChartCombined.init();
        }
    } else {
    }
});
