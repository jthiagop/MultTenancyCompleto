"use strict";

// Class definition
var KTAppBancoChartCombined = function () {
    // Shared variables
    var chart;
    var chartElement;

    // Private functions
    var initChart = function () {
        // Verificar se já foi inicializado
        if (chart) {
            console.log('Gráfico já inicializado, pulando...');
            return;
        }

        chartElement = document.getElementById('kt_charts_widget_combined');

        if (!chartElement) {
            console.log('Elemento kt_charts_widget_combined não encontrado');
            return;
        }

        console.log('Inicializando gráfico...');

        // Configuração inicial do gráfico
        var options = {
            series: [{
                name: 'Entradas',
                type: 'line',
                data: []
            }, {
                name: 'Saídas',
                type: 'line',
                data: []
            }],
            chart: {
                height: 400,
                type: 'line',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                }
            },
            colors: ['#00C851', '#FF4444'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: [3, 3],
                curve: 'smooth'
            },
            xaxis: {
                categories: [],
                title: {
                    text: 'Dias do Mês'
                }
            },
            yaxis: [{
                title: {
                    text: 'Valor (R$)'
                },
                labels: {
                    formatter: function (value) {
                        return 'R$ ' + value.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            }],
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (value, { seriesIndex }) {
                        if (seriesIndex === 0 || seriesIndex === 1) {
                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        } else {
                            return 'R$ ' + value.toLocaleString('pt-BR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            grid: {
                borderColor: '#f1f1f1'
            }
        };

        chart = new ApexCharts(chartElement, options);
        chart.render();

        console.log('Gráfico criado e renderizado');

        // Carregar dados iniciais
        loadChartData();
    };

    var loadChartData = function () {
        var mes = document.getElementById('chart-month-filter').value;
        var ano = document.getElementById('chart-year-filter').value;
        var bancoId = document.getElementById('chart-bank-filter').value;

        console.log('Carregando dados do gráfico:', { mes, ano, bancoId });

        // Mostrar loading (ApexCharts não tem showLoading, vamos usar uma abordagem diferente)
        console.log('Iniciando carregamento de dados...');

        // Fazer requisição AJAX
        var url = '/banco/chart-data?' + new URLSearchParams({
            mes: mes,
            ano: ano,
            entidade_id: bancoId
        });

        console.log('Fazendo requisição para:', url);

        fetch(url, {
            method: 'GET',
            credentials: 'include', // Incluir cookies de autenticação
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            console.log('Resposta recebida:', response.status, response.statusText);
            if (!response.ok) {
                console.error('Erro na resposta:', response.status, response.statusText);
                throw new Error('Erro na resposta: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dados recebidos:', data);
            if (data.error) {
                console.error('Erro ao carregar dados:', data.error);
                return;
            }

            // Atualizar gráfico
            updateChart(data);

            // Atualizar totais
            updateTotals(data.totais);
        })
        .catch(error => {
            console.error('Erro na requisição:', error);
        })
        .finally(() => {
            console.log('Carregamento de dados finalizado');
        });
    };

    var updateChart = function (data) {
        if (!chart) {
            console.log('Gráfico não inicializado');
            return;
        }

        console.log('Atualizando gráfico com dados:', data);

        var categories = data.dados.map(item => item.data);
        var entradas = data.dados.map(item => item.entradas);
        var saidas = data.dados.map(item => item.saidas);

        console.log('Categorias:', categories);
        console.log('Entradas:', entradas);
        console.log('Saídas:', saidas);

        chart.updateOptions({
            series: [{
                name: 'Entradas',
                data: entradas
            }, {
                name: 'Saídas',
                data: saidas
            }],
            xaxis: {
                categories: categories
            }
        });

        console.log('Gráfico atualizado');
    };

    var updateTotals = function (totais) {
        // Atualizar total de entradas
        document.getElementById('total-entradas').textContent =
            totais.entradas.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // Atualizar total de saídas
        document.getElementById('total-saidas').textContent =
            totais.saidas.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // Atualizar saldo total
        var saldoElement = document.getElementById('saldo-total');
        saldoElement.textContent =
            totais.saldo.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        // Aplicar cor baseada no saldo
        saldoElement.className = 'fs-2hx fw-bold me-2 lh-1 ls-n2 ' +
            (totais.saldo >= 0 ? 'text-success' : 'text-danger');
    };

    var initEventListeners = function () {
        // Event listener para o botão de atualizar
        var refreshBtn = document.getElementById('refresh-chart');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function () {
                loadChartData();
            });
        }

        // Event listeners para os filtros
        var monthFilter = document.getElementById('chart-month-filter');
        var yearFilter = document.getElementById('chart-year-filter');
        var bankFilter = document.getElementById('chart-bank-filter');

        if (monthFilter) {
            monthFilter.addEventListener('change', loadChartData);
        }

        if (yearFilter) {
            yearFilter.addEventListener('change', loadChartData);
        }

        if (bankFilter) {
            bankFilter.addEventListener('change', loadChartData);
        }
    };

    // Public methods
    return {
        init: function () {
            initChart();
            initEventListeners();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTAppBancoChartCombined.init();
});
