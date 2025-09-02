<!--begin::Col - Fluxo de Caixa Mensal-->
<div class="col-12 col-lg-7">
    <div class="card card-flush h-lg-100">
        <!--begin::Card header-->
        <div class="card-header mt-6">
            <div class="card-title flex-column text-center w-100">
                {{-- Título corrigido --}}
                <h3 class="fw-bold mb-1">Fluxo de Caixa Mensal</h3>
                {{-- Legenda corrigida --}}
                <div class="fs-6 d-flex justify-content-center text-gray-600 fs-6 fw-semibold">
                    <div class="d-flex align-items-center me-6">
                        <span class="menu-bullet d-flex align-items-center me-2"><span class="bullet bg-success"></span></span>Entradas
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="menu-bullet d-flex align-items-center me-2"><span class="bullet bg-danger"></span></span>Saídas
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <select id="yearSelector" name="year" data-control="select2" data-hide-search="true" class="form-select form-select-solid form-select-sm fw-bold w-100px">
                    {{-- Gera os anos dinamicamente --}}
                    @for ($year = now()->year; $year >= now()->year - 5; $year--)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <!--end::Card header-->
        <!--begin::Card body-->
        <div class="card-body pt-1 pb-0 px-5">
            <div id="Dm_project_overview_graph" class="card-rounded-bottom" style="height: 300px"></div>
        </div>
        <!--end::Card body-->
    </div>
</div>
<!--end::Col - Fluxo de Caixa Mensal-->

@section('scripts')
{{-- Garanta que seu layout principal tenha um @yield('scripts') --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Garante que as bibliotecas do tema estejam prontas
        var initializeChart = function() {
            if (typeof ApexCharts === 'undefined' || typeof KTUtil === 'undefined') {
                setTimeout(initializeChart, 100);
                return;
            }

            var element = document.getElementById('Dm_project_overview_graph');
            if (!element) {
                return;
            }

            // Pega os dados do PHP e converte para JSON
            var chartData = @json($areaChartData);

            var options = {
                series: chartData.series,
                chart: {
                    fontFamily: 'inherit',
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: ['40%'],
                        borderRadius: [6]
                    }
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    show: true,
                    width: 3,
                    // CORREÇÃO 1: Removido 'colors' daqui para que as linhas usem as cores da série
                },
                xaxis: {
                    categories: chartData.categories,
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: KTUtil.getCssVariableValue('--kt-gray-500'),
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                           return 'R$ ' + parseInt(value);
                        },
                        style: {
                            colors: KTUtil.getCssVariableValue('--kt-gray-500'),
                            fontSize: '12px'
                        }
                    }
                },
                // CORREÇÃO 2: Alterado para um preenchimento com gradiente de opacidade
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.1,
                        stops: [0, 90, 100]
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
                    },
                    y: {
                        formatter: function(val) {
                            return "R$ " + val.toFixed(2).replace('.', ',');
                        }
                    }
                },
                // CORREÇÃO 1: Cores definidas aqui para Entradas (success) e Saídas (danger)
                colors: [KTUtil.getCssVariableValue('--kt-success'), KTUtil.getCssVariableValue('--kt-danger')],
                grid: {
                    borderColor: KTUtil.getCssVariableValue('--kt-gray-200'),
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            };

            var chart = new ApexCharts(element, options);
            chart.render();

            // Lógica para o seletor de ano
            const yearSelector = document.getElementById('yearSelector');
            yearSelector.addEventListener('change', function() {
                const selectedYearValue = this.value;
                // Constrói a nova URL com o parâmetro do ano e recarrega a página
                window.location.href = "{{ route('dashboard') }}?year=" + selectedYearValue;
            });
        };

        initializeChart();
    });
</script>
@endsection
