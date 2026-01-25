"use strict";

// Class definition
var KTFieisCharts = function () {
    // Define colors - with fallback values
    var primaryColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue('--kt-primary')) || '#3E97FF';
    var dangerColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue('--kt-danger')) || '#F1416C';
    var successColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue('--kt-success')) || '#50CD89';
    var warningColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue('--kt-warning')) || '#FFC700';
    var infoColor = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue('--kt-info')) || '#009EF7';

    // Define fonts
    var fontFamily = (typeof KTUtil !== 'undefined' && KTUtil.getCssVariableValue('--bs-font-sans-serif')) || 'Inter';

    // Chart colors array - expanded with more colors
    var chartColors = [
        primaryColor,
        dangerColor,
        successColor,
        warningColor,
        infoColor,
        '#7239EA', // Purple
        '#009EF7', // Blue
        '#F7A600', // Orange
        '#50CD89', // Green
        '#F1416C', // Pink
        '#A78BFA', // Light Purple
        '#60A5FA', // Light Blue
        '#34D399', // Light Green
        '#FBBF24', // Light Yellow
        '#FB7185'  // Light Pink
    ];

    // Helper function to get colors for dataset
    var getColorsForDataset = function(count) {
        var colors = [];
        for (var i = 0; i < count; i++) {
            colors.push(chartColors[i % chartColors.length]);
        }
        return colors;
    };

    // Init Faixa Etária Chart
    var initFaixaEtariaChart = function (data) {
        var canvas = document.getElementById('kt_chart_faixa_etaria');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        var labels = data.labels || [];
        var values = data.values || [];
        var colors = getColorsForDataset(labels.length);

        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Fiéis por Faixa Etária',
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: fontFamily
                            },
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    };

    // Init Estado Civil Chart
    var initEstadoCivilChart = function (data) {
        var canvas = document.getElementById('kt_chart_estado_civil');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        var labels = data.labels || [];
        var values = data.values || [];
        var colors = getColorsForDataset(labels.length);

        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Por Estado Civil',
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: fontFamily
                            },
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    };

    // Init Profissão Chart
    var initProfissaoChart = function (data) {
        var canvas = document.getElementById('kt_chart_profissao');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        var labels = data.labels || [];
        var values = data.values || [];
        var colors = getColorsForDataset(labels.length);

        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Por Profissão',
                    data: values,
                    backgroundColor: colors,
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: fontFamily
                            },
                            usePointStyle: true,
                            padding: 15
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    };

    // Load chart data
    var loadChartData = function () {
        var chartsDataUrl = window.fieisRoutes?.chartsData || '/fieis/charts/data';

        fetch(chartsDataUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(result => {
            if (result.success && result.data) {
                // Initialize charts with data
                initFaixaEtariaChart(result.data.faixas_etarias);
                initEstadoCivilChart(result.data.estados_civis);
                initProfissaoChart(result.data.profissoes);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar dados dos gráficos:', error);
        });
    };

    // Public methods
    return {
        init: function () {
            loadChartData();
        }
    };
}();

// On document ready
if (typeof KTUtil !== 'undefined') {
    KTUtil.onDOMContentLoaded(function () {
        // Wait for Chart.js to be loaded
        if (typeof Chart !== 'undefined') {
            KTFieisCharts.init();
        } else {
            // Retry after a short delay if Chart.js is not loaded yet
            setTimeout(function() {
                if (typeof Chart !== 'undefined') {
                    KTFieisCharts.init();
                } else {
                    console.error('Chart.js não foi carregado');
                }
            }, 500);
        }
    });
} else {
    // Fallback if KTUtil is not available
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Chart !== 'undefined') {
            KTFieisCharts.init();
        } else {
            setTimeout(function() {
                if (typeof Chart !== 'undefined') {
                    KTFieisCharts.init();
                } else {
                    console.error('Chart.js não foi carregado');
                }
            }, 500);
        }
    });
}
