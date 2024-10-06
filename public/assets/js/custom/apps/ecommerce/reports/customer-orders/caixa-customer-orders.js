"use strict";

// Definição da classe
var KTAppEcommerceReportCustomerOrders = function () {
    // Variáveis compartilhadas
    var tabela;
    var datatable;

    // Funções privadas
    var initDatatable = function () {
        // Definir ordem de dados da data
        const linhasTabela = tabela.querySelectorAll('tbody tr');

        linhasTabela.forEach(linha => {
            const colunasData = linha.querySelectorAll('td');
            const dataReal = moment(colunasData[1].innerHTML, "DD MMM YYYY, LT").format(); // seleciona a data da 4ª coluna na tabela
            colunasData[1].setAttribute('data-order', dataReal);
        });

        // Inicializa o datatable --- mais informações em: https://datatables.net/manual/
        datatable = $(tabela).DataTable({
            "info": false,
            'order': [0, 'desc'], // Ordena pela primeira coluna, ID
            'pageLength': 10, // Define o número de linhas por página
        });
    }

    // Inicializar o date range picker (seletor de intervalo de datas)
    var initDaterangepicker = () => {
        var inicio = moment().subtract(29, "days");
        var fim = moment();
        var input = $("#kt_ecommerce_report_customer_orders_daterangepicker");

        function cb(inicio, fim) {
            input.html(inicio.format("MMMM D, YYYY") + " - " + fim.format("MMMM D, YYYY"));
        }

        input.daterangepicker({
            startDate: inicio,
            endDate: fim,
            ranges: {
                "Hoje": [moment(), moment()],
                "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
                "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
            }
        }, cb);

        cb(inicio, fim);
    }

    // Manipular o dropdown do filtro de status
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

    // Manipular os botões de exportação
    var exportButtons = () => {
        const tituloDocumento = 'Relatório de Pedidos de Clientes';
        var botoes = new $.fn.dataTable.Buttons(tabela, {
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: tituloDocumento
                },
                {
                    extend: 'excelHtml5',
                    title: tituloDocumento
                },
                {
                    extend: 'csvHtml5',
                    title: tituloDocumento
                },
                {
                    extend: 'pdfHtml5',
                    title: tituloDocumento
                }
            ]
        }).container().appendTo($('#kt_ecommerce_report_customer_orders_export'));

        // Vincula o clique do menu dropdown aos botões de exportação do datatable
        const botoesExportacao = document.querySelectorAll('#kt_ecommerce_report_customer_orders_export_menu [data-kt-ecommerce-export]');
        botoesExportacao.forEach(botaoExportacao => {
            botaoExportacao.addEventListener('click', e => {
                e.preventDefault();

                // Pega o valor de exportação clicado
                const valorExportacao = e.target.getAttribute('data-kt-ecommerce-export');
                const alvo = document.querySelector('.dt-buttons .buttons-' + valorExportacao);

                // Aciona o evento de clique nos botões de exportação ocultos do datatable
                alvo.click();
            });
        });
    }

    // Pesquisar no Datatable --- documentação oficial: https://datatables.net/reference/api/search/
    var handleSearchDatatable = () => {
        const filtroPesquisa = document.querySelector('[data-kt-ecommerce-order-filter="search"]');
        filtroPesquisa.addEventListener('keyup', function (e) {
            datatable.search(e.target.value).draw();
        });
    }

    // Métodos públicos
    return {
        init: function () {
            tabela = document.querySelector('#kt_ecommerce_report_customer_orders_table');

            if (!tabela) {
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

// Ao carregar o documento
KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceReportCustomerOrders.init();
});
