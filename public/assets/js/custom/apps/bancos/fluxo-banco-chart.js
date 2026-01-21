"use strict";

// Class definition
var KTBancoFluxoChart = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var loadChartData = function(startDate, endDate, callback) {
        var url = '/banco/fluxo-chart-data';
        var params = new URLSearchParams({
            start_date: startDate,
            end_date: endDate
        });

        fetch(url + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => response.json())
        .then(data => {
            if (callback) {
                callback(data);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar dados do gráfico:', error);
        });
    }

    var initChart = function(chart, chartData) {
        var element = document.getElementById("kt_charts_widget_combined");

        if (!element) {
            return;
        }

        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--bs-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');
        var basedangerColor = KTUtil.getCssVariableValue('--bs-danger');
        var lightdangerColor = KTUtil.getCssVariableValue('--bs-danger');
        var basesuccessColor = KTUtil.getCssVariableValue('--bs-success');
        var lightsuccessColor = KTUtil.getCssVariableValue('--bs-success');

        // Dados padrão se não houver dados do servidor
        var entradasData = chartData?.entradas || [];
        var saidasData = chartData?.saidas || [];
        var categorias = chartData?.categorias || [];

        // Calcular valores máximos e mínimos para o eixo Y
        var allValues = [...entradasData, ...saidasData].filter(v => v > 0);
        var maxValue = allValues.length > 0 ? Math.max(...allValues) : 100;
        var minValue = 0;
        var yAxisMax = maxValue > 0 ? Math.ceil(maxValue * 1.2) : 100;

        var options = {
            series: [{
                name: 'Saídas',
                data: saidasData
            }, {
                name: 'Entradas',
                data: entradasData
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0.2,
                    stops: [15, 120, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [basedangerColor, basesuccessColor] // Saídas (danger) primeiro, Entradas (success) depois
            },
            xaxis: {
                categories: categorias.length > 0 ? categorias : [],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                tickAmount: 6,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: [basedangerColor, basesuccessColor],
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                max: yAxisMax,
                min: minValue,
                tickAmount: 6,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                }
            },
            colors: [lightdangerColor, lightsuccessColor], // Saídas (danger) primeiro, Entradas (success) depois
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: [basedangerColor, basesuccessColor],
                strokeWidth: 3
            }
        };

        // Destruir gráfico existente se houver
        if (chart.self) {
            chart.self.destroy();
        }

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);
    }

    var updateChart = function(startDate, endDate) {
        loadChartData(startDate, endDate, function(data) {
            // Atualizar estatísticas
            if (data.totais) {
                var totalElement = document.querySelector('.fs-3x.fw-bold.text-gray-800');
                if (totalElement) {
                    var saldo = data.totais.saldo || 0;
                    totalElement.textContent = saldo.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                // Atualizar badge de variação
                var badgeElement = document.querySelector('.badge.badge-light-success');
                if (badgeElement && data.totais.entradas > 0) {
                    var percentual = ((data.totais.saldo / data.totais.entradas) * 100).toFixed(1);
                    badgeElement.innerHTML = '<i class="bi bi-graph-up-arrow me-2 text-success"></i>' + percentual + '%';
                }
            }

            // Atualizar gráfico
            initChart(chart, data);
        });
    }

    // Init daterangepicker
    var initDaterangepicker = function() {
        // Check if jQuery and daterangepicker are available
        if (typeof jQuery == 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
            return;
        }

        var element = document.querySelector('[data-kt-daterangepicker="true"]');

        if (!element || element.getAttribute("data-kt-initialized") === "1") {
            return;
        }

        var display = element.querySelector('.text-gray-600.fw-bold');
        var attrOpens = element.hasAttribute('data-kt-daterangepicker-opens')
            ? element.getAttribute('data-kt-daterangepicker-opens')
            : 'left';
        var range = element.getAttribute('data-kt-daterangepicker-range');

        var start = moment().subtract(29, 'days');
        var end = moment();

        var cb = function(start, end) {
            var current = moment();

            if (display) {
                if (current.isSame(start, "day") && current.isSame(end, "day")) {
                    display.innerHTML = start.format('D MMM YYYY');
                } else {
                    display.innerHTML = start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY');
                }
            }
        }

        if (range === "today") {
            start = moment();
            end = moment();
        }

        $(element).daterangepicker({
            startDate: start,
            endDate: end,
            opens: attrOpens,
            ranges: {
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
            }
        }, cb);

        // Callback quando o período é alterado
        $(element).on('apply.daterangepicker', function(ev, picker) {
            var startDate = picker.startDate.format('Y-MM-DD');
            var endDate = picker.endDate.format('Y-MM-DD');
            updateChart(startDate, endDate);
        });

        cb(start, end);
        element.setAttribute("data-kt-initialized", "1");

        // Carregar dados iniciais
        var startDate = start.format('Y-MM-DD');
        var endDate = end.format('Y-MM-DD');
        updateChart(startDate, endDate);
    }

    // Public methods
    return {
        init: function () {
            initDaterangepicker();

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {
                if (chart.rendered && chart.self) {
                    // Recarregar dados atuais
                    var daterangepickerElement = document.querySelector('[data-kt-daterangepicker="true"]');
                    if (daterangepickerElement && $(daterangepickerElement).data('daterangepicker')) {
                        var picker = $(daterangepickerElement).data('daterangepicker');
                        var startDate = picker.startDate.format('Y-MM-DD');
                        var endDate = picker.endDate.format('Y-MM-DD');
                        loadChartData(startDate, endDate, function(data) {
                            initChart(chart, data);
                        });
                    } else {
                        initChart(chart);
                    }
                }
            });
            
            // Expose refresh function globally
            window.refreshFluxoBancoChart = function() {
                var daterangepickerElement = document.querySelector('[data-kt-daterangepicker="true"]');
                if (daterangepickerElement && $(daterangepickerElement).data('daterangepicker')) {
                    var picker = $(daterangepickerElement).data('daterangepicker');
                    var startDate = picker.startDate.format('Y-MM-DD');
                    var endDate = picker.endDate.format('Y-MM-DD');
                    updateChart(startDate, endDate);
                } else {
                    // Fallback to defaults or current month if no picker
                    var start = moment().startOf('month').format('Y-MM-DD');
                    var end = moment().endOf('month').format('Y-MM-DD');
                    updateChart(start, end);
                }
            };
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTBancoFluxoChart;
}

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTBancoFluxoChart.init();
});

