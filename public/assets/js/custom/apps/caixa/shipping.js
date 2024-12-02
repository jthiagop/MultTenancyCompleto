"use strict";

// Definição da classe
var KTAppEcommerceReportShipping = function () {
    // Variáveis compartilhadas
    var table;
    var datatable;

    // Funções privadas
    var initDatatable = function () {
        // Ajusta a ordem da data nos dados
        const tableRows = table.querySelectorAll('tbody tr');

        tableRows.forEach(row => {
            const dateRow = row.querySelectorAll('td');
            // Usa o formato correto da data DD-MM-YYYY
            const realDate = moment(dateRow[1].textContent, "DD-MM-YYYY").format('YYYY-MM-DD');
            dateRow[1].setAttribute('data-order', realDate);
        });

        // Inicializa o datatable
        datatable = $(table).DataTable({
            "info": false,
            'order': [[0, 'desc']],
            'pageLength': 10,
        });
    }

    // // Inicializa o date range picker
    // var initDaterangepicker = () => {
    //     var start = moment().subtract(29, "days");
    //     var end = moment();
    //     var input = $("#kt_ecommerce_report_shipping_daterangepicker");

    //     function cb(start, end) {
    //         input.val(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
    //         filterByDateRange(start, end);
    //     }

    //     input.daterangepicker({
    //         startDate: start,
    //         endDate: end,
    //         ranges: {
    //             "Hoje": [moment(), moment()],
    //             "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
    //             "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
    //             "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
    //             "Este Mês": [moment().startOf("month"), moment().endOf("month")],
    //             "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
    //         }
    //     }, cb);

    //     cb(start, end);
    // }
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


    // Filtra pelo intervalo de datas
    var filterByDateRange = (start, end) => {
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            var min = start.format("YYYY-MM-DD");
            var max = end.format("YYYY-MM-DD");
            var date = moment(data[1], "DD-MM-YYYY").format("YYYY-MM-DD"); // Usa os dados da 2ª coluna

            if (
                (!min && !max) ||
                (!min && date <= max) ||
                (min <= date && !max) ||
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
            datatable.column(4).search(valor).draw(); // A coluna "Tipo" é a 5ª na tabela, então o índice é 4
        });
    };

    // Função para manipular os botões de exportação
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
                    text: 'Exportar para PDF',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6], // Colunas a serem exportadas
                    }
                }
            ]
        }).container().appendTo($('#kt_ecommerce_report_shipping_export'));

        // Conecta os botões de exportação
        const exportMenuButtons = document.querySelectorAll('#kt_ecommerce_report_shipping_export_menu [data-kt-ecommerce-export]');
        exportMenuButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();
                const exportValue = e.target.getAttribute('data-kt-ecommerce-export');
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);
                if (target) target.click();
            });
        });
    };

    // Pesquisa no DataTable
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-ecommerce-order-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Métodos públicos
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

// Quando o documento estiver pronto
KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceReportShipping.init();
});
