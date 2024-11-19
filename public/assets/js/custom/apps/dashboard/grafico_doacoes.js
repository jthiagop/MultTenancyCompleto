"use strict";

// Class definition
var KTProjectOverview = function () {
    // Colors
    const colors = {
        primary: KTUtil.getCssVariableValue('--bs-primary'),
        lightPrimary: KTUtil.getCssVariableValue('--bs-primary-light'),
        success: KTUtil.getCssVariableValue('--bs-success'),
        lightSuccess: KTUtil.getCssVariableValue('--bs-success-light'),
        warning: KTUtil.getCssVariableValue('--bs-warning'),
        lightWarning: KTUtil.getCssVariableValue('--bs-warning-light'),
        danger: KTUtil.getCssVariableValue('--bs-danger'),
        lightDanger: KTUtil.getCssVariableValue('--bs-danger-light'),
        gray200: KTUtil.getCssVariableValue('--bs-gray-400'),
        gray500: KTUtil.getCssVariableValue('--bs-gray-500'),
    };

    // Private functions
    var initChart = function () {
        var element = document.getElementById("project_overview_chart");

        if (!element) return;  // Return early if element does not exist

        var config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [30, 45, 25],
                    backgroundColor: [colors.danger, colors.success, colors.gray200]
                }],
                labels: ['Active', 'Completed', 'Yet to start']
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        intersect: false,
                        backgroundColor: colors.success,
                        titleFontColor: '#ffffff',
                        padding: { x: 10, y: 10 },
                        displayColors: false,
                        cornerRadius: 4
                    }
                }
            }
        };

        var ctx = element.getContext('2d');
        var myDoughnut = new Chart(ctx, config);
    }



       var initGraph = function () {
        var element = document.getElementById("Dm_project_overview_graph");
        if (!element) return;

        var height = parseInt(KTUtil.css(element, 'height'));

        var options = {
            series: areaChartData.series,
            chart: {
                type: 'area',
                height: height,
                toolbar: { show: true }
            },
            legend: { show: false },
            dataLabels: { enabled: false },
            fill: { type: 'solid', opacity: 1 },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [colors.primary, colors.success, colors.warning]
            },
            xaxis: {
                categories: areaChartData.categories,
                labels: {
                    style: {
                        colors: colors.gray500,
                        fontSize: '12px'
                    }
                },
                tooltip: {
                    enabled: true,
                    style: { fontSize: '12px' }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: colors.gray500,
                        fontSize: '12px'
                    },
                    formatter: function (val) {
                        return "R$ " + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
            },
            tooltip: {
                style: { fontSize: '12px' },
                y: {
                    formatter: function (val) {
                        return "R$ " + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    }
                }
            },
            colors: [colors.lightPrimary, colors.lightSuccess, colors.lightWarning],
            grid: {
                borderColor: colors.gray200,
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            },
            markers: {
                colors: [colors.lightPrimary, colors.lightSuccess, colors.lightWarning],
                strokeColor: [colors.primary, colors.success, colors.warning],
                strokeWidth: 3
            }
        };

        var chart = new ApexCharts(element, options);
        chart.render();
    }

    // Public methods
    return {
        init: function () {
            initChart();
            initGraph();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function() {
    KTProjectOverview.init();
});
