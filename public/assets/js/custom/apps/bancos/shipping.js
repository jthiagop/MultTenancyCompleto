"use strict";

// Class definition
var KTAppEcommerceReportShipping = function () {
    // Shared variables
    var table;
    var datatable;

    // Private functions
    var initDatatable = function () {
        // Set date data order
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            const realDate = moment(dateRow[1].innerText, "DD-MM-YYYY").format(); // select date from 2nd column in table
            dateRow[1].setAttribute('data-order', realDate);
        });

        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [0, 'desc'],
            'pageLength': 10,
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
            "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
            "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
            "Este Mês": [moment().startOf("month"), moment().endOf("month")],
            "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
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
        $.fn.dataTable.ext.search.push((settings, data, dataIndex) => {
            var min = start.format("YYYY-MM-DD");
            var max = end.format("YYYY-MM-DD");
            var date = moment(data[1], "DD-MM-YYYY").format("YYYY-MM-DD"); // use data from the 2nd column

            if (
                (min === "" && max === "") ||
                (min === "" && date <= max) ||
                (min <= date && max === "") ||
                (min <= date && date <= max)
            ) {
                return true;
            }
            return false;
        });
        datatable.draw();
        $.fn.dataTable.ext.search.pop();
    }

    // Filtra pelo status
    var handleStatusFilter = () => {
        const filtroStatus = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
        $(filtroStatus).on('change', e => {
            let valor = e.target.value;
            if (valor === 'all') {
                valor = ''; // Limpa o filtro para mostrar todos os resultados
            }
            datatable.column(5).search(valor).draw(); // A coluna "Tipo" é a 5ª na tabela, então o índice é 4
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
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
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
        console.log('Elemento do gráfico encontrado, inicializando...');

        // Carregar ApexCharts se não estiver carregado
        if (typeof ApexCharts === 'undefined') {
            console.log('ApexCharts não encontrado, carregando...');
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/apexcharts@latest';
            script.onload = function() {
                console.log('ApexCharts carregado, inicializando gráfico...');
                KTAppBancoChartCombined.init();
            };
            script.onerror = function() {
                console.error('Erro ao carregar ApexCharts');
            };
            document.head.appendChild(script);
        } else {
            console.log('ApexCharts já carregado, inicializando gráfico...');
            KTAppBancoChartCombined.init();
        }
    } else {
        console.log('Elemento do gráfico não encontrado');
    }
});
