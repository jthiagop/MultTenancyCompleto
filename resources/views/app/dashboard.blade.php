<x-tenant-app-layout
    pageTitle="Dashboard">
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <x-toolbar :company="$company" />
            <!--end::Toolbar-->

            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-fluid">
                <!--begin::Input group-->
                <div id="kt_app_content" class="app-content">
                    <!--begin::DateTime-->
                    <div class="text-center">
                        <span id="datetime" class="fs-6 fw-semibold text-gray-400"></span>
                    </div>
                    <!--end::DateTime-->
                    <!--begin::Modules Grid-->
                    <style>
                        .module-card-bg {
                            background-image: url({{ global_asset('assets/media/images/2600x1600/bg-3.png') }});
                        }
                        .dark .module-card-bg {
                            background-image: url({{ global_asset('assets/media/images/2600x1600/bg-3-dark.png') }});
                        }
                    </style>
                    
                    <div class="row g-5 g-xl-8">
                        @forelse($modules ?? [] as $module)
                            <!--begin::Col - {{ $module->name }}-->
                            <div class="col-12 col-sm-6 col-lg-3">
                                <a href="{{ route($module->route_name) }}" 
                                   class="card card-flush h-100 bg-cover bg-no-repeat module-card-bg position-relative overflow-hidden hover-elevate-up"
                                   style="background-position: right top -1.7rem;"
                                   aria-label="Acessar módulo {{ $module->name }}">
                                    <!--begin::Card body-->
                                    <div class="card-body d-flex flex-column justify-content-between p-6">
                                        <!--begin::Icon-->
                                        <div class="mb-6">
                                            @if($module->icon_path)
                                                @php
                                                    // Tratar caminhos de storage vs caminhos públicos
                                                    if (str_starts_with($module->icon_path, '/assets')) {
                                                        $iconUrl = $module->icon_path;
                                                    } elseif (str_starts_with($module->icon_path, 'modules/icons') || !str_starts_with($module->icon_path, '/')) {
                                                        $iconUrl = route('file', ['path' => $module->icon_path]);
                                                    } else {
                                                        $iconUrl = $module->icon_path;
                                                    }
                                                @endphp
                                                <img loading="lazy" width="48" height="48"
                                                    src="{{ $iconUrl }}" alt="Ícone {{ $module->name }}"
                                                    class="module-icon">
                                            @elseif($module->icon_class)
                                                <i class="{{ $module->icon_class }} fs-2x text-primary module-icon"></i>
                                            @else
                                                <i class="fa-solid fa-cube fs-2x text-primary module-icon"></i>
                                            @endif
                                        </div>
                                        <!--end::Icon-->
                                        
                                        <!--begin::Info-->
                                        <div>
                                            <span class="text-gray-900 fw-bold d-block fs-3 mb-2">
                                                {{ $module->name }}
                                            </span>
                                            <span class="text-gray-600 fw-semibold fs-6">
                                                {{ $module->description }}
                                            </span>
                                        </div>
                                        <!--end::Info-->
                                    </div>
                                    <!--end::Card body-->
                                </a>
                            </div>
                            <!--end::Col - {{ $module->name }}-->
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    Nenhum módulo disponível no momento.
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <!--end::Modules Grid-->

                    @can('financeiro.show')
                        <!--begin::Row-->
                        <div class="row g-6 g-xl-9 mt-1">
                            <!--begin::Col - Resumo Financeiro-->
                            <div class="col-12 col-lg-5">
                                <!--begin::Col-->
                                    <!--begin::Chart widget 5-->
                                    <div class="card card-flush h-lg-100">
                                        <!--begin::Header-->
                                    <div class="card-header flex-nowrap pt-2">
                                            <!--begin::Title-->
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">Ranking de Missas</span>
                                            <span class="text-gray-400 pt-2 fw-semibold fs-6">Ranking de missas por dia da
                                                semana</span>
                                            </h3>
                                            <!--end::Title-->
                                            <!--begin::Toolbar-->
                                            <div class="card-toolbar">
                                            <!--begin::Daterangepicker-->
                                            <div class="btn btn-sm btn-light d-flex align-items-center px-4"
                                                id="missas-daterangepicker">
                                                <!--begin::Display range-->
                                                <i class="bi bi-calendar-date me-1 text-primary"></i>
                                                <div class="text-gray-400 fw-bold">Selecione um período</div>
                                                <!--end::Display range-->
                                            </div>
                                            <!--end::Daterangepicker-->
                                        </div>
                                            <!--end::Toolbar-->
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Body-->
                                        <div class="card-body pt-5 ps-6">
                                            <div id="kt_charts_widget_5" class="min-h-auto"></div>
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Chart widget 5-->
                                <!--end::Col-->
                            </div>
                            <!--end::Col - Resumo Financeiro-->

                            <!--begin::Col - Doações e Ofertas-->
                            <div class="col-12 col-lg-7">
                                <div class="card card-flush h-lg-100">
                                    <!--begin::Card header-->
                                    <div class="card-header mt-6 text-center">
                                        <div class="card-title flex-column text-center">
                                            <h3 class="fw-bold mb-1">Doações e Ofertas</h3>
                                            <div class="fs-6 d-flex justify-content-center text-gray-400 fs-6 fw-semibold">
                                                <div class="d-flex align-items-center me-6">
                                                    <span class="menu-bullet d-flex align-items-center me-2">
                                                        <span class="bullet bg-success"></span>
                                                    </span>Doações
                                                </div>
                                                <div class="d-flex align-items-center me-6">
                                                    <span class="menu-bullet d-flex align-items-center me-2">
                                                        <span class="bullet bg-primary"></span>
                                                    </span>Coletas
                                                </div>
                                                <div class="d-flex align-items-center me-3">
                                                    <span class="menu-bullet d-flex align-items-center me-2">
                                                        <span class="bullet bg-warning"></span>
                                                    </span>Intenções
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-toolbar">
                                            <!--begin::Daterangepicker-->
                                            <div data-kt-daterangepicker="true" data-kt-daterangepicker-opens="left"
                                                data-kt-daterangepicker-range="this year"
                                                class="btn btn-sm btn-light d-flex align-items-center px-4">
                                                <!--begin::Display range-->
                                                <i class="bi bi-calendar-date me-1 text-primary"></i>
                                                <div class="text-gray-400 fw-bold">Selecione um período</div>
                                                <!--end::Display range-->
                                            </div>
                                            <!--end::Daterangepicker-->
                                        </div>
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->
                                    <div class="card-body pt-1 pb-0 px-5">
                                        <div id="Dm_project_overview_graph" class="card-rounded-bottom"
                                            style="height: 300px" aria-describedby="donations-chart-description"></div>
                                        <div id="donations-chart-description" class="sr-only">
                                            Gráfico de área mostrando doações, coletas e intenções para o ano selecionado.
                                        </div>
                                    </div>
                                    <!--end::Card body-->
                                </div>
                            </div>
                            <!--end::Col - Doações e Ofertas-->
                        </div>
                        <!--end::Row-->
                    @endcan
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->
</x-tenant-app-layout>



<script src="/assets/js/custom/apps/dashboard/grafico_doacoes.js"></script>
<script src="/assets/js/custom/apps/dashboard/missas-chart.js"></script>



<!--begin::Custom CSS-->
<style>
    /* Module Cards */
    .module-card-bg {
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    
    .module-card-bg:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border-color: var(--bs-primary);
    }
    
    .module-icon {
        transition: transform 0.3s ease;
    }
    
    .module-card-bg:hover .module-icon {
        transform: scale(1.1);
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }
</style>
<!--end::Custom CSS-->

<!--begin::Custom Javascript-->
<script>
    // Atualização de data e hora
    document.addEventListener('DOMContentLoaded', function() {
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const dateTimeString = now.toLocaleDateString('pt-BR', options).toUpperCase();
            document.getElementById('datetime').textContent = dateTimeString;
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);
    });

    // Dados do gráfico de área
    const areaChartData = @json($areaChartData);

    // Inicialização do Daterangepicker para o gráfico
    document.addEventListener('DOMContentLoaded', function() {
        // Verificar se jQuery e daterangepicker estão disponíveis
        if (typeof jQuery === 'undefined' || typeof $.fn.daterangepicker === 'undefined') {
            console.error('jQuery ou daterangepicker não estão disponíveis');
            return;
        }

        var element = document.querySelector('[data-kt-daterangepicker="true"]');
        if (!element) {
            return;
        }

        // Verificar se já foi inicializado
        var isInitialized = element.getAttribute("data-kt-initialized");
        if (isInitialized === "1") {
            return;
        }

        var display = element.querySelector('.text-gray-400.fw-bold');
        var attrOpens = element.hasAttribute('data-kt-daterangepicker-opens') ?
            element.getAttribute('data-kt-daterangepicker-opens') :
            'left';
        var range = element.getAttribute('data-kt-daterangepicker-range');

        // Configurar datas iniciais baseadas no range
        var start = moment().startOf('year');
        var end = moment().endOf('year');

        if (range === "this year") {
            start = moment().startOf('year');
            end = moment().endOf('year');
        } else if (range === "this month") {
            start = moment().startOf('month');
            end = moment().endOf('month');
        }

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
                'Este Ano': [moment().startOf('year'), moment().endOf('year')],
                'Ano Passado': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1,
                    'year').endOf('year')],
                'Este Mês': [moment().startOf('month'), moment().endOf('month')],
                'Mês Passado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                    'month').endOf('month')],
                'Últimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Últimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Hoje': [moment(), moment()],
                'Ontem': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
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

            // Atualizar o gráfico via AJAX
            fetch(`/dashboard?start_date=${startDate}&end_date=${endDate}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        // Se não for JSON, fazer reload da página
                        window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data) {
                        // Atualizar os dados do gráfico
                        if (typeof areaChartData !== 'undefined' && typeof KTProjectOverview !==
                            'undefined') {
                            areaChartData = data.areaChartData;
                            // Recriar o gráfico com os novos dados
                            if (typeof KTProjectOverview !== 'undefined' && typeof KTProjectOverview
                                .initGraph === 'function') {
                                KTProjectOverview.initGraph();
                            }
                        }
                    }
                })
                .catch(error => {
                    // Fallback para redirecionamento completo
                    var startDate = picker.startDate.format('YYYY-MM-DD');
                    var endDate = picker.endDate.format('YYYY-MM-DD');
                    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
                });
        });

        cb(start, end);
        element.setAttribute("data-kt-initialized", "1");
    });
</script>
<!--end::Custom Javascript-->

<!--begin::Modal for Tip-->
<div class="modal fade" id="tipModal" tabindex="-1" aria-labelledby="tipModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tipModalLabel">Dica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Convide colaboradores para melhorar a análise de receitas e despesas. Adicione novos usuários na seção
                de gerenciamento de equipe.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal for Tip-->
