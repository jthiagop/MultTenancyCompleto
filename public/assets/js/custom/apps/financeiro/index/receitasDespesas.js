"use strict";

// Classe principal
var KTAppEcommerceReportCustomerOrders = function () {
    // Variáveis compartilhadas
    var table;
    var datatable;

    // 1) Inicializa o DataTable
    var initDatatable = function () {
        // Opção extra: definir o atributo "data-order" para ordenação correta da data (opcional)
        const tableRows = table.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            const tds = row.querySelectorAll('td');

            // A Data de Vencimento está na 1ª coluna (td[0])
            // Ex: "09 Mar 2025"
            if (tds[0]) {
                // Converte a data para um formato de ordenação
                const realDate = moment(tds[0].innerHTML.trim(), "DD MMM YYYY").format("YYYY-MM-DD");
                tds[0].setAttribute('data-order', realDate);
            }
        });

        // Inicia o DataTable
        datatable = $(table).DataTable({
            info: false,
            order: [],
            pageLength: 10,
        });
    }

    // 2) Filtro personalizado por intervalo de datas no DataTables
    //    (com base na primeira coluna, que contém a Data de Vencimento)
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        // settings: configurações do DataTables
        // data: array com o conteúdo de cada coluna da linha (data[0], data[1], etc.)
        // dataIndex: índice da linha

        // Pega a data inicial e final do daterangepicker
        var daterangepicker = $('#kt_ecommerce_report_customer_orders_daterangepicker').data('daterangepicker');
        if (!daterangepicker) {
            return true; // se não existir ainda, não filtra
        }

        var min = daterangepicker.startDate.format('YYYY-MM-DD');
        var max = daterangepicker.endDate.format('YYYY-MM-DD');

        // Extrai a data da TABELA (1ª coluna = data[0]) e converte para YYYY-MM-DD
        var dateInTable = moment(data[0], 'DD MMM YYYY').format('YYYY-MM-DD');

        // Valida se a data da linha está dentro do intervalo [min, max]
        if (
            (min === null && max === null) ||
            (min === null && dateInTable <= max) ||
            (min <= dateInTable && max === null) ||
            (min <= dateInTable && dateInTable <= max)
        ) {
            return true;
        }
        return false;
    });

    // 3) Inicializa o DateRangePicker
    var initDaterangepicker = () => {
        // Define intervalo inicial: últimos 30 dias
        var start = moment().subtract(29, "days");
        var end = moment();
        var input = $("#kt_ecommerce_report_customer_orders_daterangepicker");

        // Função de callback que atualiza o input e faz o DataTable filtrar
        function cb(start, end) {
            // Exibe o intervalo escolhido no input
            input.val(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));

            // Depois de selecionar datas, forçamos o DataTable a refiltrar
            if (datatable) {
                datatable.draw();
            }
        }

        // Inicializa o daterangepicker
        input.daterangepicker({
            startDate: start,
            endDate: end,
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'De',
                toLabel: 'Até',
                customRangeLabel: 'Personalizado'
            },
            ranges: {
                "Hoje": [moment(), moment()],
                "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Últimos 7 dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 dias": [moment().subtract(29, "days"), moment()],
                "Este mês": [moment().startOf("month"), moment().endOf("month")],
                "Mês passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, cb);

        // Chamada inicial para exibir o intervalo padrão nos inputs
        cb(start, end);
    }

    // 4) Filtra a tabela por "Situação" (exemplo)
    var handleStatusFilter = () => {
        const filterStatus = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
        if (!filterStatus) return;

        $(filterStatus).on('change', e => {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            datatable.column(2).search(value).draw();
        });
    }

    // 5) Exportar a tabela (copiar, excel, csv, pdf)
    var exportButtons = () => {
        const documentTitle = 'Customer Orders Report';
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                { extend: 'copyHtml5',  title: documentTitle },
                { extend: 'excelHtml5', title: documentTitle },
                { extend: 'csvHtml5',   title: documentTitle },
                { extend: 'pdfHtml5',   title: documentTitle }
            ]
        }).container().appendTo($('#kt_ecommerce_report_customer_orders_export'));

        // Evento dos botões do dropdown de export
        const exportButtons = document.querySelectorAll('#kt_ecommerce_report_customer_orders_export_menu [data-kt-ecommerce-export]');
        exportButtons.forEach(exportButton => {
            exportButton.addEventListener('click', e => {
                e.preventDefault();
                // Identifica o valor de export (ex: 'excel', 'csv', etc.)
                const exportValue = e.target.getAttribute('data-kt-ecommerce-export');
                // Localiza o botão real do DataTables
                const target = document.querySelector('.dt-buttons .buttons-' + exportValue);
                // Dispara o clique no botão de export
                target.click();
            });
        });
    }

    // 6) Pesquisar na tabela
    var handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-ecommerce-order-filter="search"]');
        if (!filterSearch) return;

        filterSearch.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Métodos públicos
    return {
        init: function () {
            // Localiza a tabela no DOM
            table = document.querySelector('#kt_ecommerce_report_customer_orders_table');
            if (!table) {
                return;
            }

            // Inicia as funções privadas
            initDatatable();       // Inicializa DataTable
            initDaterangepicker(); // Inicializa Daterangepicker
            exportButtons();       // Configura botões de export
            handleSearchDatatable(); // Campo de busca textual
            handleStatusFilter();  // Filtro por situação
        }
    };
}();

// Executa ao carregar o documento
KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceReportCustomerOrders.init();
});
