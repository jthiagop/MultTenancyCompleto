<link href="https://kendo.cdn.telerik.com/themes/8.0.1/default/default-main.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://kendo.cdn.telerik.com/2024.2.514/js/kendo.all.min.js"></script>

<x-tenant-app-layout>

    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <!--begin::Toolbar container-->
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <!--begin::Page title-->
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Resumo do Caixa</h1>
                        <!--end::Title-->
                        <!--begin::Breadcrumb-->
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('caixa.index') }}"
                                    class="text-muted text-hover-primary">Financeiro</a>
                            </li>
                            <!--end::Item-->

                            <!--begin::Item-->
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <!--end::Item-->
                            <!--begin::Item-->
                            <li class="breadcrumb-item text-muted">
                                <aspan class="text-muted text-hover-primary">Movimentações do Caixa</aspan>
                            </li>
                            <!--end::Item-->
                        </ul>
                        <!--end::Breadcrumb-->
                    </div>
                    <!--end::Page title-->
                </div>
                <!--end::Toolbar container-->
            </div>
            <!--end::Toolbar-->
            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <!--begin::Content container-->
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Navbar-->
                    <div class="card mb-6 mb-xl-9">
                        <div class="card-body pt-9 pb-0">
                            <!--begin::Details-->
                            <div class="d-flex flex-wrap flex-sm-nowrap mb-6">
                                <!--begin::Image-->
                                <span class="svg-icon svg-icon-5x me-5">
                                    <img width="150" height="150" src="/assets/media/png/Cash_Register-transformed.webp" alt="">
                            </span>
                                <!--end::Image-->
                                <!--begin::Wrapper-->
                                <div class="flex-grow-1">
                                    <!--begin::Head-->
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                        <!--begin::Details-->
                                        <div class="d-flex flex-column">
                                            <!--begin::Status-->
                                            <div class="d-flex align-items-center mb-1">
                                                <a href="#"
                                                    class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">Movimentações do caixa</a>
                                                <span class="badge badge-light-success me-auto">Ativado</span>
                                            </div>
                                            <!--end::Status-->
                                            <!--begin::Description-->
                                            <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-400">Todos os
                                                lançamentos relacionados ao Caixa</div>
                                            <!--end::Description-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Actions-->
                                        <div class="d-flex mb-4 align-items-center">
                                            <a href="{{ route('caixa.index') }}"
                                                class="btn btn-sm btn-bg-light btn-active-color-primary me-3">Financeiro</a>
                                            <a href="{{ route('caixa.list', ['tab' => 'lancamento']) }}"
                                                class="btn btn-sm btn-success me-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                                    <path
                                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                                                </svg>
                                                <span>Lançamento</span></a>
                                            <!--begin::Menu-->
                                            <div class="me-0">
                                                <button
                                                    class="btn btn-sm btn-icon btn-bg-light btn-active-color-primary"
                                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                    <i class="bi bi-three-dots fs-3"></i>
                                                </button>
                                                <!--begin::Menu 3-->
                                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                                    data-kt-menu="true">
                                                    <!--begin::Heading-->
                                                    <div class="menu-item px-3">
                                                        <div
                                                            class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">
                                                            Payments</div>
                                                    </div>
                                                    <!--end::Heading-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Create Invoice</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link flex-stack px-3">Create
                                                            Payment
                                                            <i class="fas fa-exclamation-circle ms-2 fs-7"
                                                                data-bs-toggle="tooltip"
                                                                title="Specify a target name for future usage and reference"></i></a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3">
                                                        <a href="#" class="menu-link px-3">Generate Bill</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                                        data-kt-menu-placement="right-end">
                                                        <a href="#" class="menu-link px-3">
                                                            <span class="menu-title">Subscription</span>
                                                            <span class="menu-arrow"></span>
                                                        </a>
                                                        <!--begin::Menu sub-->
                                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Plans</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#" class="menu-link px-3">Billing</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <a href="#"
                                                                    class="menu-link px-3">Statements</a>
                                                            </div>
                                                            <!--end::Menu item-->
                                                            <!--begin::Menu separator-->
                                                            <div class="separator my-2"></div>
                                                            <!--end::Menu separator-->
                                                            <!--begin::Menu item-->
                                                            <div class="menu-item px-3">
                                                                <div class="menu-content px-3">
                                                                    <!--begin::Switch-->
                                                                    <label
                                                                        class="form-check form-switch form-check-custom form-check-solid">
                                                                        <!--begin::Input-->
                                                                        <input class="form-check-input w-30px h-20px"
                                                                            type="checkbox" value="1"
                                                                            checked="checked" name="notifications" />
                                                                        <!--end::Input-->
                                                                        <!--end::Label-->
                                                                        <span
                                                                            class="form-check-label text-muted fs-6">Recuring</span>
                                                                        <!--end::Label-->
                                                                    </label>
                                                                    <!--end::Switch-->
                                                                </div>
                                                            </div>
                                                            <!--end::Menu item-->
                                                        </div>
                                                        <!--end::Menu sub-->
                                                    </div>
                                                    <!--end::Menu item-->
                                                    <!--begin::Menu item-->
                                                    <div class="menu-item px-3 my-1">
                                                        <a href="#" class="menu-link px-3">Settings</a>
                                                    </div>
                                                    <!--end::Menu item-->
                                                </div>
                                                <!--end::Menu 3-->
                                            </div>
                                            <!--end::Menu-->
                                        </div>
                                        <!--end::Actions-->
                                    </div>
                                    <!--end::Head-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-wrap justify-content-start">
                                        <!--begin::Stats-->
                                        <div class="d-flex flex-wrap">
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $total }}"
                                                        data-kt-countup-prefix="R$ ">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400">Saldo atua</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr065.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-danger me-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="11" y="18" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(-90 11 18)" fill="currentColor" />
                                                            <path
                                                                d="M11.4343 15.4343L7.25 11.25C6.83579 10.8358 6.16421 10.8358 5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75L11.2929 18.2929C11.6834 18.6834 12.3166 18.6834 12.7071 18.2929L18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25C17.8358 10.8358 17.1642 10.8358 16.75 11.25L12.5657 15.4343C12.2533 15.7467 11.7467 15.7467 11.4343 15.4343Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $valorSaidas }} "data-kt-countup-prefix="R$ ">
                                                        0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400">Saída</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                            <!--begin::Stat-->
                                            <div
                                                class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                                <!--begin::Number-->
                                                <div class="d-flex align-items-center">
                                                    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
                                                    <span class="svg-icon svg-icon-3 svg-icon-success me-2">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13"
                                                                height="2" rx="1"
                                                                transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path
                                                                d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    <!--end::Svg Icon-->
                                                    <div class="fs-4 fw-bold" data-kt-countup="true"
                                                        data-kt-countup-value="{{ $valorEntrada }}"
                                                        data-kt-countup-prefix="$">0</div>
                                                </div>
                                                <!--end::Number-->
                                                <!--begin::Label-->
                                                <div class="fw-semibold fs-6 text-gray-400"
                                                    data-kt-countup-prefix="R$ ">Entrada</div>
                                                <!--end::Label-->
                                            </div>
                                            <!--end::Stat-->
                                        </div>
                                        <!--end::Stats-->
                                        {{-- <!--begin::Users-->
                                        <div class="symbol-group symbol-hover mb-3">
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Alan Warden">
                                                <span
                                                    class="symbol-label bg-warning text-inverse-warning fw-bold">A</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michael Eberon">
                                                <img alt="Pic" src="/assets/media/avatars/300-11.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Michelle Swanston">
                                                <img alt="Pic" src="/assets/media/avatars/300-7.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Francis Mitcham">
                                                <img alt="Pic" src="/assets/media/avatars/300-20.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Susan Redwood">
                                                <span
                                                    class="symbol-label bg-primary text-inverse-primary fw-bold">S</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Melody Macy">
                                                <img alt="Pic" src="/assets/media/avatars/300-2.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Perry Matthew">
                                                <span class="symbol-label bg-info text-inverse-info fw-bold">P</span>
                                            </div>
                                            <!--end::User-->
                                            <!--begin::User-->
                                            <div class="symbol symbol-35px symbol-circle" data-bs-toggle="tooltip"
                                                title="Barry Walter">
                                                <img alt="Pic" src="/assets/media/avatars/300-12.jpg" />
                                            </div>
                                            <!--end::User-->
                                            <!--begin::All users-->
                                            <a href="#" class="symbol symbol-35px symbol-circle"
                                                data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                                <span class="symbol-label bg-dark text-inverse-dark fs-8 fw-bold"
                                                    data-bs-toggle="tooltip" data-bs-trigger="hover"
                                                    title="View more users">+42</span>
                                            </a>
                                            <!--end::All users-->
                                        </div>
                                        <!--end::Users--> --}}
                                    </div>
                                    <!--end::Info-->
                                </div>
                                <!--end::Wrapper-->
                            </div>
                            <!--end::Details-->
                            <div class="separator"></div>
                                    <!--begin::Nav-->
                                    <ul
                                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'overview' ? 'active' : '' }}"
                                                href="{{ route('caixa.list', ['tab' => 'overview']) }}">
                                                Gestão
                                            </a>
                                        </li>
                                        <!--end::Nav item-->
                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'lancamento' ? 'active' : '' }}"
                                                href="{{ route('caixa.list', ['tab' => 'lancamento']) }}">
                                                Lançamento
                                            </a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'registros' ? 'active' : '' }}"
                                                href="{{ route('caixa.list', ['tab' => 'registros']) }}">
                                                Registros
                                            </a>
                                        </li>
                                        <!--end::Nav item-->

                                        <!--begin::Nav item-->
                                        <li class="nav-item">
                                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'bancos' ? 'active' : '' }}"
                                                href="{{ route('caixa.list', ['tab' => 'bancos']) }}">
                                                Relatórios
                                            </a>
                                        </li>
                                        <!--end::Nav item-->
                                    </ul>
                                    <!--end::Nav-->
                        </div>
                    </div>
                    <!--end::Navbar-->
                    @includeIf("app.financeiro.caixa.tabs.{$activeTab}")

                </div>
                <!--end::Content container-->
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->

        @include('app.components.modals.financeiro.lancamento.modal_lacamento')

</x-tenant-app-layout>


<!--begin::Vendors Javascript(used for this page only)-->
<script src="/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!--end::Vendors Javascript-->
<!--begin::Custom Javascript(used for this page only)-->
<script src="/assets/js/custom/apps/caixa/shipping.js"></script>
<script src="/assets/js/custom/utilities/modals/financeiro/new-caixa.js"></script>

<script src="/assets/js/widgets.bundle.js"></script>
<script src="/assets/js/custom/apps/chat/chat.js"></script>
<script src="/assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="/assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="/assets/js/custom/utilities/modals/users-search.js"></script>
<!--end::Custom Javascript-->
<script src="/assets/js/custom/apps/user-management/users/list/table.js"></script>

<!--end::Javascript-->

<!-- jQuery -->
<!-- Bootstrap Bundle (includes Popper) -->

<!-- Custom Script -->
<script src="/assets/js/custom_script.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteLinks = document.querySelectorAll('.delete-link');

        deleteLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const id = this.getAttribute('data-id');
                const form = document.getElementById(`delete-form-${id}`);

                Swal.fire({
                    title: 'Você tem certeza?',
                    text: 'Esta ação não pode ser desfeita!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, exclua!',
                    cancelButtonText: 'Não, cancele',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>


@section('scripts')
<script>
    "use strict";

    // Função para inicializar e controlar nosso gráfico de despesas
    var DespesasChartWidget = function() {
        var chartElement = document.getElementById('kt_despesas_chart');
        var chart = null;

        // Função para buscar dados e atualizar o gráfico
        const updateChart = (params) => {
            // Mostra o overlay de loading
            const overlay = new KTOverlay(chartElement);
            overlay.show();

            // Monta a URL com os parâmetros
            const url = new URL("{{ route('charts.despesas.data') }}");
            url.search = new URLSearchParams(params).toString();

            // Faz a chamada AJAX
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    // Atualiza o valor total
                    document.getElementById('total_despesas_valor').innerText = data.total;

                    // Atualiza o gráfico com os novos dados
                    chart.updateOptions({
                        xaxis: {
                            categories: data.categories
                        },
                        series: data.series
                    });

                    overlay.hide(); // Esconde o loading
                })
                .catch(error => {
                    console.error('Erro ao buscar dados do gráfico:', error);
                    overlay.hide();
                });
        }

        // Função de inicialização
        const init = () => {
            if (!chartElement) {
                return;
            }

            // Opções base do ApexCharts (estilo do seu tema)
            var options = {
                series: [], // Começa vazio, será preenchido via AJAX
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                xaxis: {
                    categories: [], // Começa vazio
                    axisBorder: { show: false },
                    axisTicks: { show: false },
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
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.2,
                        stops: [15, 120, 100]
                    }
                },
                colors: [KTUtil.getCssVariableValue('--kt-primary')],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 2 },
                legend: { show: false },
                tooltip: {
                    x: {
                        format: 'dd MMM'
                    },
                    y: {
                        formatter: function (value) {
                            return "R$ " + value.toFixed(2).replace('.', ',');
                        }
                    }
                }
            };

            chart = new ApexCharts(chartElement, options);
            chart.render();

            // Busca os dados iniciais (padrão de 30 dias)
            updateChart({ range: 30 });
        }

        // Configuração dos botões e do seletor de data
        const setupEventListeners = () => {
            const rangeButtons = document.querySelectorAll('.card-toolbar .btn-group [data-range]');

            rangeButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Remove a classe 'active' de todos os botões e adiciona no clicado
                    rangeButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    // Limpa o seletor de data
                    $('#despesas_date_range_picker').val('');

                    // Chama a atualização com o novo range
                    const range = this.getAttribute('data-range');
                    updateChart({ range: range });
                });
            });

            // Inicializa o Date Range Picker
            const datePicker = $("#despesas_date_range_picker");
            datePicker.daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: "DD/MM/YYYY",
                    cancelLabel: 'Limpar',
                    applyLabel: 'Aplicar'
                }
            });

            datePicker.on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
                rangeButtons.forEach(btn => btn.classList.remove('active'));

                updateChart({
                    start_date: picker.startDate.format('YYYY-MM-DD'),
                    end_date: picker.endDate.format('YYYY-MM-DD')
                });
            });

            datePicker.on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // Botão para limpar o datepicker
            document.getElementById('despesas_date_range_picker_clear').addEventListener('click', function() {
                datePicker.val('');
                // Opcional: voltar para o filtro padrão de 30 dias
                document.querySelector('[data-range="30"]').click();
            });
        }

        // Retorna os métodos públicos
        return {
            init: function() {
                init();
                setupEventListeners();
            }
        };
    }();

    // Quando o documento estiver pronto, inicializa nosso widget
    KTUtil.onDOMContentLoaded(function() {
        DespesasChartWidget.init();
    });
</script>
@endsection
