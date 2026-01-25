"use strict";

// Class definition
var KTMissasChart = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var loadChartData = function(startDate, endDate, retryCount) {
        retryCount = retryCount || 0;
        var maxRetries = 2;
        
        var url = '/dashboard/missas-chart-data';
        var params = new URLSearchParams();

        if (startDate) {
            params.append('start_date', startDate);
        }
        if (endDate) {
            params.append('end_date', endDate);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        return fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Verificar se a resposta é válida
            if (!response.ok) {
                throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            
            // Verificar Content-Type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error('Resposta inválida. Content-Type: ' + contentType + ', Body: ' + text.substring(0, 100));
                });
            }
            
            return response.json().catch(err => {
                throw new Error('Erro ao decodificar JSON: ' + err.message);
            });
        })
        .then(data => {
            // Validar estrutura dos dados
            if (!data) {
                throw new Error('Dados vazios recebidos do servidor');
            }
            
            if (data.error) {
                throw new Error('Erro do servidor: ' + data.error);
            }
            
            if (!data.success) {
                throw new Error('Requisição não bem-sucedida');
            }
            
            if (!Array.isArray(data.data) || !Array.isArray(data.categories)) {
                throw new Error('Formato de dados inválido: data ou categories não são arrays');
            }

            return {
                data: data.data,
                categories: data.categories
            };
        })
        .catch(error => {
            console.error('[KTMissasChart] Erro ao carregar dados (tentativa ' + (retryCount + 1) + '):', error.message);
            
            // Tentar novamente se houver tentativas restantes
            if (retryCount < maxRetries) {
                console.log('[KTMissasChart] Tentando novamente em 2 segundos...');
                return new Promise(resolve => setTimeout(resolve, 2000))
                    .then(() => loadChartData(startDate, endDate, retryCount + 1));
            }
            
            // Se esgotou as tentativas, lançar o erro
            throw error;
        });
    };

    var initChart = function(chartData) {
        // Verificar se ApexCharts está disponível
        if (typeof ApexCharts === 'undefined') {
            return;
        }

        var element = document.getElementById("kt_charts_widget_5");

        if (!element) {
            return;
        }

        var borderColor = KTUtil.getCssVariableValue('--bs-border-dashed-color');

        var options = {
            series: [{
                data: chartData.data || [],
                show: false
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    distributed: true,
                    barHeight: 23
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            colors: ['#3E97FF', '#F1416C', '#50CD89', '#FFC700', '#7239EA', '#50CDCD', '#3F4254'],
            xaxis: {
                categories: chartData.categories || [],
                labels: {
                    formatter: function (val) {
                        return "R$ " + parseFloat(val).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    },
                    style: {
                        colors: KTUtil.getCssVariableValue('--bs-gray-400'),
                        fontSize: '14px',
                        fontWeight: '600',
                        align: 'left'
                    }
                },
                axisBorder: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: KTUtil.getCssVariableValue('--bs-gray-800'),
                        fontSize: '14px',
                        fontWeight: '600'
                    },
                    offsetY: 2,
                    align: 'left'
                }
            },
            grid: {
                borderColor: borderColor,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: false
                    }
                },
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "R$ " + parseFloat(val).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            }
        };

        // Destruir gráfico anterior se existir
        if (chart.self && chart.rendered) {
            chart.self.destroy();
        }

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);
    };

    var updateChart = function(startDate, endDate) {
        loadChartData(startDate, endDate)
            .then(function(chartData) {
                try {
                    initChart(chartData);
                } catch (error) {
                    console.error('[KTMissasChart] Erro ao renderizar gráfico:', error);
                }
            })
            .catch(function(error) {
                console.error('[KTMissasChart] Erro ao atualizar gráfico de missas:', error.message || error);
                // Mostrar dados vazios em caso de erro
                try {
                    initChart({
                        data: [0, 0, 0, 0, 0, 0, 0],
                        categories: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']
                    });
                } catch (e) {
                    console.error('[KTMissasChart] Erro ao renderizar gráfico com dados vazios:', e);
                }
            });
    };

    var initDaterangepicker = function() {
        // Verificar se jQuery e daterangepicker estão disponíveis
        if (typeof jQuery === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
            return null;
        }

        if (typeof moment === 'undefined') {
            return null;
        }

        var element = document.getElementById('missas-daterangepicker');
        if (!element) {
            return null;
        }

        // Verificar se já foi inicializado
        var isInitialized = element.getAttribute("data-kt-initialized");
        if (isInitialized === "1") {
            return;
        }

        var display = element.querySelector('.text-gray-600.fw-bold');
        // Configuração padrão do daterangepicker
        var attrOpens = 'left';
        var range = 'this week'; // Range padrão: Esta Semana

        // Configurar datas iniciais baseadas no range padrão
        var start = moment().startOf('week');
        var end = moment().endOf('week');

        var cb = function(start, end) {
            if (display) {
                if (start.isSame(end, "day")) {
                    display.innerHTML = start.format('D MMM YYYY');
                } else {
                    display.innerHTML = start.format('D MMM YYYY') + ' - ' + end.format('D MMM YYYY');
                }
            }
        };

        $(element).daterangepicker({
            startDate: start,
            endDate: end,
            opens: attrOpens,
            ranges: {
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Esta Semana': [moment().startOf('week'), moment().endOf('week')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')],
                'Este Ano': [moment().startOf('year'), moment().endOf('year')]
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
            var startDate = picker.startDate.format('YYYY-MM-DD');
            var endDate = picker.endDate.format('YYYY-MM-DD');

            // Atualizar o gráfico de missas
            updateChart(startDate, endDate);
        });

        cb(start, end);
        element.setAttribute("data-kt-initialized", "1");

        // Retornar as datas iniciais para inicializar o gráfico
        return {
            start: start.format('YYYY-MM-DD'),
            end: end.format('YYYY-MM-DD')
        };
    };

    // Public methods
    return {
        init: function () {
            // Verificar se os elementos necessários existem
            var chartElement = document.getElementById("kt_charts_widget_5");
            var daterangepickerElement = document.getElementById('missas-daterangepicker');

            if (!chartElement) {
                return;
            }

            if (!daterangepickerElement) {
                return;
            }

            // Verificar dependências
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            if (typeof KTUtil === 'undefined') {
                return;
            }

            // Inicializar daterangepicker e obter datas iniciais
            var dates = initDaterangepicker();

            // Se o daterangepicker não foi inicializado, usar datas padrão
            if (!dates) {
                console.warn('[KTMissasChart] Daterangepicker não inicializado, usando datas padrão');
                if (typeof moment !== 'undefined') {
                    var start = moment().startOf('year');
                    var end = moment().endOf('year');
                    dates = {
                        start: start.format('YYYY-MM-DD'),
                        end: end.format('YYYY-MM-DD')
                    };
                } else {
                    // Fallback sem moment.js - usar data atual
                    var today = new Date();
                    var startOfYear = new Date(today.getFullYear(), 0, 1);
                    var endOfYear = new Date(today.getFullYear(), 11, 31);
                    dates = {
                        start: startOfYear.toISOString().split('T')[0],
                        end: endOfYear.toISOString().split('T')[0]
                    };
                }
            }

            // Carregar dados iniciais do gráfico
            if (dates && dates.start && dates.end) {
                updateChart(dates.start, dates.end);
            } else {
                console.error('[KTMissasChart] Datas inválidas para inicialização');
            }

            // Update chart on theme mode change
            if (typeof KTThemeMode !== 'undefined') {
                KTThemeMode.on("kt.thememode.change", function() {
                    if (chart.rendered) {
                        var currentData = {
                            data: chart.self.w.config.series[0].data,
                            categories: chart.self.w.config.xaxis.categories
                        };
                        chart.self.destroy();
                        chart.rendered = false;
                        initChart(currentData);
                    }
                });
            }
        },
        update: function(startDate, endDate) {
            updateChart(startDate, endDate);
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTMissasChart;
}

// Função para inicializar com retry caso dependências não estejam prontas
var initializeWithRetry = function(retryCount) {
    retryCount = retryCount || 0;
    var maxRetries = 10;
    var retryDelay = 200;

    // Verificar se todas as dependências estão disponíveis
    if (typeof ApexCharts === 'undefined' || typeof KTUtil === 'undefined' || typeof jQuery === 'undefined' || typeof moment === 'undefined') {
        if (retryCount < maxRetries) {
            setTimeout(function() {
                initializeWithRetry(retryCount + 1);
            }, retryDelay);
            return;
        } else {
        }
    }

    // Verificar se os elementos existem
    var chartElement = document.getElementById("kt_charts_widget_5");
    var daterangepickerElement = document.getElementById('missas-daterangepicker');

    if (!chartElement || !daterangepickerElement) {
        if (retryCount < maxRetries) {
            setTimeout(function() {
                initializeWithRetry(retryCount + 1);
            }, retryDelay);
            return;
        } else {
            return;
        }
    }

    // Todas as dependências estão prontas, inicializar
    KTMissasChart.init();
};

// On document ready
if (typeof KTUtil !== 'undefined' && typeof KTUtil.onDOMContentLoaded === 'function') {
    KTUtil.onDOMContentLoaded(function() {
        initializeWithRetry(0);
    });
} else {
    // Fallback caso KTUtil não esteja disponível
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializeWithRetry(0);
        });
    } else {
        // DOM já está carregado
        initializeWithRetry(0);
    }
}

