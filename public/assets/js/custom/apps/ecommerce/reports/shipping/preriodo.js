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
            const realDate = moment(dateRow[0].innerHTML, "MMM DD, YYYY").format(); // select date from 4th column in table
            dateRow[0].setAttribute('data-order', realDate);
        });


        // Init datatable --- more info on datatables: https://datatables.net/manual/
        datatable = $(table).DataTable({
            "info": false,
            'order': [],
            'pageLength': 10,
        });
    }

    // Detecta o tema atual
    var getTheme = function() {
        return document.documentElement.getAttribute("data-bs-theme") ||
               localStorage.getItem("data-bs-theme") ||
               "light";
    }

    // Aplica classes do tema dark ao daterangepicker
    var applyDarkTheme = function() {
        var isDark = getTheme() === "dark";
        if (isDark) {
            $('.daterangepicker').addClass('dark-theme');
            $('.daterangepicker').css({
                'background-color': '#1e1e2d',
                'color': '#92929f',
                'border-color': '#2b2b40'
            });
            $('.daterangepicker .calendar-table').css({
                'background-color': '#1e1e2d',
                'color': '#92929f'
            });
            $('.daterangepicker .calendar-table th').css({
                'color': '#92929f'
            });
            $('.daterangepicker .calendar-table td').css({
                'color': '#92929f'
            });
            $('.daterangepicker .calendar-table td.available:hover').css({
                'background-color': '#2b2b40',
                'color': '#fff'
            });
            $('.daterangepicker .calendar-table td.active').css({
                'background-color': '#009ef7',
                'color': '#fff'
            });
            $('.daterangepicker .ranges li').css({
                'color': '#92929f'
            });
            $('.daterangepicker .ranges li.active').css({
                'background-color': '#009ef7',
                'color': '#fff'
            });
        } else {
            $('.daterangepicker').removeClass('dark-theme');
            $('.daterangepicker').css({
                'background-color': '',
                'color': '',
                'border-color': ''
            });
        }
    }

    // Init daterangepicker
    var initDaterangepicker = () => {
        var start = moment().subtract(29, "days");
        var end = moment();
        var input = $("#Periodo");
        var inputContainer = input.parent();

        // Cria container para o input com setas (apenas se ainda não existir)
        if (!inputContainer.find('.daterange-navigation').length && !inputContainer.find('#prev-month').length) {
            // Adiciona botão anterior antes do input
            var prevButton = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-icon btn-light-primary me-2',
                id: 'prev-month',
                title: 'Mês Anterior',
                html: '<i class="bi bi-chevron-left fs-2"></i>'
            });

            // Adiciona botão próximo depois do input
            var nextButton = $('<button>', {
                type: 'button',
                class: 'btn btn-sm btn-icon btn-light-primary ms-2',
                id: 'next-month',
                title: 'Próximo Mês',
                html: '<i class="bi bi-chevron-right fs-2"></i>'
            });

            // Adiciona os botões ao redor do input
            input.before(prevButton);
            input.after(nextButton);
        }

        var daterangepickerInstance;

        function cb(start, end) {
            input.val(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));

            // Aplica tema dark após o picker ser criado
            setTimeout(function() {
                applyDarkTheme();
            }, 100);
        }

        // Configura o daterangepicker
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
                format: "DD/MM/YYYY",
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
                firstDay: 0
            },
            opens: 'left'
        }, cb);

        // Armazena a instância do daterangepicker após inicialização
        setTimeout(function() {
            daterangepickerInstance = input.data('daterangepicker');

            // Navegação para mês anterior
            $('#prev-month').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (daterangepickerInstance) {
                    var currentStart = daterangepickerInstance.startDate.clone();
                    var currentEnd = daterangepickerInstance.endDate.clone();

                    // Calcula a diferença em dias
                    var diffDays = currentEnd.diff(currentStart, 'days');

                    // Move para o mês anterior
                    var newStart = currentStart.clone().subtract(1, 'month').startOf('month');
                    var newEnd = newStart.clone().add(diffDays, 'days');

                    // Se a data final ultrapassar o fim do mês, ajusta
                    if (newEnd.month() !== newStart.month()) {
                        newEnd = newStart.clone().endOf('month');
                    }

                    daterangepickerInstance.setStartDate(newStart);
                    daterangepickerInstance.setEndDate(newEnd);
                    cb(newStart, newEnd);

                    // Aplica tema dark
                    applyDarkTheme();
                }
            });

            // Navegação para próximo mês
            $('#next-month').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (daterangepickerInstance) {
                    var currentStart = daterangepickerInstance.startDate.clone();
                    var currentEnd = daterangepickerInstance.endDate.clone();

                    // Calcula a diferença em dias
                    var diffDays = currentEnd.diff(currentStart, 'days');

                    // Move para o próximo mês
                    var newStart = currentStart.clone().add(1, 'month').startOf('month');
                    var newEnd = newStart.clone().add(diffDays, 'days');

                    // Se a data final ultrapassar o fim do mês, ajusta
                    if (newEnd.month() !== newStart.month()) {
                        newEnd = newStart.clone().endOf('month');
                    }

                    daterangepickerInstance.setStartDate(newStart);
                    daterangepickerInstance.setEndDate(newEnd);
                    cb(newStart, newEnd);

                    // Aplica tema dark
                    applyDarkTheme();
                }
            });
        }, 100);

        // Executa callback inicial
        cb(start, end);

        // Observa mudanças no tema
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'data-bs-theme') {
                    applyDarkTheme();
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-bs-theme']
        });

        // Aplica tema quando o picker é aberto
        input.on('show.daterangepicker', function() {
            setTimeout(function() {
                applyDarkTheme();
            }, 10);
        });
    }

    // Handle status filter dropdown
    var handleStatusFilter = () => {
        const filterStatus = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
        $(filterStatus).on('change', e => {
            let value = e.target.value;
            if (value === 'all') {
                value = '';
            }
            datatable.column(5).search(value).draw();
        });
    }

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
                    title: documentTitle
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


    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
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
