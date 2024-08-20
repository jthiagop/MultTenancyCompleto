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
    var initDaterangepicker = () => {
        var start = moment().subtract(29, "days");
        var end = moment();
        var input = $("#kt_ecommerce_report_shipping_daterangepicker");

        function cb(start, end) {
            input.val(start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY"));
            filterByDateRange(start, end);
        }

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
            }
        }, cb);

        cb(start, end);
    }

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

    // Handle status filter dropdown
    var handleStatusFilter = () => {
        const filterStatus = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
        $(filterStatus).on('change', e => {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            datatable.column(4).search(value).draw();
        });
    }

// Hook export buttons
// Função para converter uma imagem em Base64
function getBase64Image(imgUrl, callback) {
    var img = new Image();
    img.setAttribute('crossOrigin', 'anonymous'); // Necessário para evitar problemas de CORS
    img.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        var dataURL = canvas.toDataURL("image/png"); // Converte a imagem para Base64
        callback(dataURL);
    };
    img.src = imgUrl;
}


getBase64Image(companyLogoUrl, function(base64Image) {
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
                        columns: [0, 1, 2, 3, 4, 6, 5],
                        alignment: 'center',
                    },
                    customize: function (doc) {
                        // Insere o logotipo, nome da diocese e outros detalhes no cabeçalho
                        doc.content.unshift({
                            columns: [
                                {
                                    image: base64Image, // Usa a imagem convertida em Base64
                                    width: 60
                                },
                                {
                                    stack: [
                                        {
                                            text: 'Proneb - Província Nossa Senhora da Penha do Brasil ',
                                            alignment: 'center',
                                            margin: [0, 0, 0, 5],
                                            fontSize: 14,
                                            bold: true
                                        },
                                        {
                                            text: companyName,
                                            alignment: 'center',
                                            fontSize: 12,
                                            bold: true
                                        },
                                        {
                                            text: 'CNPJ: ' + companyCnpj,
                                            alignment: 'center',
                                            fontSize: 10
                                        },
                                        {
                                            text: 'E-mail: paroquiafatimacaruaru@gmail.com / Home-page:',
                                            alignment: 'center',
                                            fontSize: 10
                                        },
                                        {
                                            text: 'Fone: 81 2161-2590',
                                            alignment: 'center',
                                            fontSize: 10
                                        }
                                    ],
                                    margin: [0, 0, 0, 10]
                                }
                            ],
                            margin: [0, 0, 0, 20] // Margem abaixo do cabeçalho
                        })

                        // Ajusta as larguras das colunas conforme necessário
                        if (doc.content[1] && doc.content[1].table) {
                            doc.content[1].table.widths = [
                                '5%', '10%', '20%', '35%', '5%', '5%', '20%'
                            ];
                        }

                        // Estilização do cabeçalho da tabela
                        doc.styles.tableHeader.fillColor = 'blue';
                        doc.styles.tableHeader.color = 'white';
                        doc.styles.tableHeader.alignment = 'center';
                        doc.styles.tableHeader.fontSize = 12;
                        doc.styles.title.fontSize = 14;
                        doc.styles.title.alignment = 'center';

                        if (doc.content[1] && doc.content[1].text) {
                            doc.content[1].text = doc.content[1].text.toUpperCase();
                        }

                        // Alinhamento de texto para cada célula da tabela
                        if (doc.content[2] && doc.content[2].table) {
                            doc.content[2].table.body.forEach(function(row, rowIndex) {
                                row.forEach(function(cell, cellIndex) {
                                    if (cellIndex === 0 || cellIndex === 1) {
                                        cell.alignment = 'left';
                                    } else if (cellIndex === 5) {
                                        cell.alignment = 'right';
                                    } else {
                                        cell.alignment = 'center';
                                    }
                                });
                            });

                            // Calcula a soma das entradas e saídas
                            var totalEntradas = 0;
                            var totalSaidas = 0;
                            doc.content[2].table.body.forEach(function(row, index) {
                                if (index > 0) { // Exclui a linha do cabeçalho da soma
                                    var valor = parseFloat(row[6].text.replace(/[^0-9.-]+/g,"")) || 0;
                                    if (row[4].text === 'entrada') {
                                        totalEntradas += valor;
                                    } else if (row[4].text === 'saida') {
                                        totalSaidas += valor;
                                    }
                                }
                            });

                            // Calcula o saldo
                            var saldo = totalEntradas - totalSaidas;

                            // Formata os totais no estilo brasileiro
                            var formattedTotalEntradas = totalEntradas.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            var formattedTotalSaidas = totalSaidas.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                            var formattedSaldo = saldo.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                            // Adiciona as somas formatadas e o saldo ao final do documento
                            doc.content.push({
                                text: 'Total Entradas: R$ ' + formattedTotalEntradas,
                                alignment: 'right',
                                margin: [0, 10, 20, 0],
                                fontSize: 12,
                                bold: true
                            });
                            doc.content.push({
                                text: 'Total Saídas: R$ ' + formattedTotalSaidas,
                                alignment: 'right',
                                margin: [0, 10, 20, 0],
                                fontSize: 12,
                                bold: true
                            });
                            doc.content.push({
                                text: 'Saldo: R$ ' + formattedSaldo,
                                alignment: 'right',
                                margin: [0, 10, 20, 0],
                                fontSize: 12,
                                bold: true
                            });
                        }

                        // Rodapé personalizado
                        doc.footer = function(page, pages) {
                            return {
                                columns: [
                                    'Proneb - Província Nossa Senhora da Penha do Brasil',
                                    {
                                        alignment: 'right',
                                        text: [
                                            { text: page.toString(), italics: true },
                                            ' de ',
                                            { text: pages.toString(), italics: true }
                                        ]
                                    }
                                ],
                                margin: [10, 0, 10, 0]
                            };
                        };
                    }
                }
            ]
        }).container().appendTo($('#kt_ecommerce_report_shipping_export'));

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

    // Execute a função exportButtons após a imagem ser carregada e convertida
    exportButtons();
});


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
});
