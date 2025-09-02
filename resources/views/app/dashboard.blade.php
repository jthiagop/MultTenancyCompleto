<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <x-toolbar :company="$company" />
            <!--end::Toolbar-->
            <!--begin::DateTime-->
            <div class="text-center mb-5">
                <span id="datetime" class="fs-6 fw-semibold text-gray-600"></span>
            </div>
            <!--end::DateTime-->
            <!--begin::Content container-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Input group-->
                <div id="kt_app_content" class="app-content flex-column-xxl">
                    <!--begin::Graph-->
                    <div class="card card-flush h-lg-100">
                        <!--begin::Card header-->
                        <div class="card-header mt-6 text-center">
                            <div class="card-title flex-column text-center">
                                <h3 class="fw-bold mb-1">Módulos do Sistema</h3>
                            </div>

                            <!--begin::Row-->
                            <div class="row mb-5 mt-6">
                                <!--begin::Col - Financeiro-->
                                <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                    <a href="{{ route('caixa.index') }}"
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-4 d-flex align-items-center"
                                        aria-label="Acessar módulo Financeiro">
                                        <div class="icon-container me-5">
                                            <img loading="lazy" width="75px" height="75px"
                                                src="/assets/media/png/financeiro.svg" alt="Ícone Financeiro">
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-dark fw-bold d-block fs-4 mb-2">Financeiro</span>
                                            <span class="text-gray-600 fw-semibold fs-6">Cadastros financeiros,
                                                movimentações</span>
                                        </span>
                                    </a>
                                </div>
                                <!--end::Col - Financeiro-->

                                <!--begin::Col - Patrimônio-->
                                <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                    <a href="{{ route('patrimonio.index') }}"
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-4 d-flex align-items-center"
                                        aria-label="Acessar módulo Patrimônio">
                                        <div class="icon-container me-5">
                                            <img loading="lazy" width="75px" height="75px"
                                                src="/assets/media/png/house3d.png" alt="Ícone Patrimônio">
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-dark fw-bold d-block fs-4 mb-2">Patrimônio</span>
                                            <span class="text-gray-600 fw-semibold fs-6">Gestão patrimonial, foro e
                                                laudêmio</span>
                                        </span>
                                    </a>
                                </div>
                                <!--end::Col - Patrimônio-->

                                {{-- 
                                    Vamos assumir que a permissão para acessar a contabilidade 
                                    é a role 'admin'. Você pode mudar para qualquer outra role, 
                                    como 'contador', se preferir.
                                --}}
                                @if (auth()->user()->hasRole('admin'))
                                    <!--begin::Col - Contabilidade (DESBLOQUEADO)-->
                                    <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                        {{-- O link agora é funcional e aponta para a rota da contabilidade --}}
                                        <a href="{{ route('contabilidade.index') }}"
                                            class="btn btn-outline btn-outline-dashed btn-active-light-primary p-4 d-flex align-items-center"
                                            aria-label="Acessar Módulo Contabilidade">
                                            <div class="icon-container me-5">
                                                <img loading="lazy" width="75px" height="75px"
                                                    src="/assets/media/png/contabilidade.png" alt="Ícone Contabilidade">
                                            </div>
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-4 mb-2">Contabilidade</span>
                                                {{-- O texto agora indica que o módulo está acessível --}}
                                                <span class="text-gray-600 fw-semibold fs-6">Gerenciar plano de contas e
                                                    DE/PARA.</span>
                                            </span>
                                        </a>
                                    </div>
                                    <!--end::Col - Contabilidade-->
                                @else
                                    <!--begin::Col - Contabilidade (BLOQUEADO)-->
                                    <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                        {{-- O link está desabilitado e não leva a lugar nenhum --}}
                                        <a href="#"
                                            class="btn btn-outline btn-outline-dashed btn-outline-danger disabled p-4 d-flex align-items-center"
                                            aria-label="Módulo Contabilidade bloqueado" aria-disabled="true">
                                            <div class="icon-container me-5">
                                                <img loading="lazy" width="75px" height="75px"
                                                    src="/assets/media/png/contabilidade.png" alt="Ícone Contabilidade">
                                            </div>
                                            <span class="d-block fw-semibold text-start">
                                                <span class="text-dark fw-bold d-block fs-4 mb-2">Contabilidade</span>
                                                <span class="text-gray-600 fw-semibold fs-6">Módulo bloqueado</span>
                                            </span>
                                            <div class="d-flex justify-content-end ms-auto">
                                                {{-- O ícone é um cadeado fechado e vermelho --}}
                                                <i class="fa-solid fa-lock fs-2 text-danger"
                                                    aria-label="Módulo Contabilidade bloqueado"></i>
                                            </div>
                                        </a>
                                    </div>
                                    <!--end::Col - Contabilidade-->
                                @endif
                                <!--begin::Col - Dízimo e Doações-->
                                <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                    <a href="#"
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary disabled p-4 d-flex align-items-center"
                                        aria-label="Módulo Dízimo e Doações em desenvolvimento" aria-disabled="true">
                                        <div class="icon-container me-5">
                                            <img loading="lazy" width="75px" height="75px"
                                                src="/assets/media/png/dizimo.png" alt="Ícone Dízimo e Doações">
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-dark fw-bold d-block fs-4 mb-2">Dízimo e Doações</span>
                                            <span class="text-gray-600 fw-semibold fs-6">Em desenvolvimento</span>
                                        </span>
                                    </a>
                                </div>
                                <!--end::Col - Dízimo e Doações-->

                                <!--begin::Col - Cadastro de Fiéis-->
                                <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                    <a href="{{ route('fieis.index') }}"
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-4 d-flex align-items-center"
                                        aria-label="Acessar módulo Cadastro de Fiéis">
                                        <div class="icon-container me-5">
                                            <img loading="lazy" width="75px" height="75px"
                                                src="/assets/media/png/fieis.png" alt="Ícone Cadastro de Fiéis">
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-dark fw-bold d-block fs-4 mb-2">Cadastro de Fiéis</span>
                                            <span class="text-gray-600 fw-semibold fs-6">Gerenciamento de membros e
                                                contribuições</span>
                                        </span>
                                    </a>
                                </div>
                                <!--end::Col - Cadastro de Fiéis-->

                                <!--begin::Col - Cadastro de Sepulturas-->
                                <div class="col-12 col-sm-6 col-lg-4 hover-elevate-up parent-hover mb-5">
                                    <a href="{{ route('cemiterio.index') }}"
                                        class="btn btn-outline btn-outline-dashed btn-active-light-primary p-4 d-flex align-items-center"
                                        aria-label="Acessar módulo Cadastro de Sepulturas">
                                        <div class="icon-container me-5">
                                            <img loading="lazy" width="75px" height="75px"
                                                src="/assets/media/png/lapide2.png"
                                                alt="Ícone Cadastro de Sepulturas">
                                        </div>
                                        <span class="d-block fw-semibold text-start">
                                            <span class="text-dark fw-bold d-block fs-4 mb-2">Cadastro de
                                                Sepulturas</span>
                                            <span class="text-gray-600 fw-semibold fs-6">Gerenciamento de
                                                sepultamentos, manutenção e pagamentos</span>
                                        </span>
                                    </a>
                                </div>
                                <!--end::Col - Cadastro de Sepulturas-->
                            </div>
                            <!--end::Row-->
                        </div>
                        <!--end::Card header-->
                    </div>
                    <!--end::Graph-->

                    <!--begin::Separator-->
                    <div class="separator separator-dotted separator-content my-5 d-flex align-items-center">
                        <span class="me-2">
                            <i class="fa-solid fa-clock-rotate-left fa-3x"></i>
                        </span>
                        <span class="h1 mb-0">Resumo</span>
                    </div>
                    <!--end::Separator-->

                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9 mt-xl-1">
                        <!--begin::Col - Resumo Financeiro-->
                        <div class="col-12 col-lg-5">
                            <div class="card card-flush h-lg-100">
                                <!--begin::Card header-->
                                <div class="card-header mt-6">
                                    <div class="card-title flex-column">
                                        <h3 class="fw-bold mb-1">Resumo Financeiro</h3>
                                        <div class="fs-6 fw-semibold text-gray-600">Receitas e Despesas Totais</div>
                                    </div>
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-light btn-sm">Ver Detalhes</a>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body p-9 pt-5">
                                    <div class="d-flex flex-wrap">
                                        <div class="position-relative d-flex flex-center h-175px w-175px me-15 mb-7">
                                            <div
                                                class="position-absolute translate-middle start-50 top-50 d-flex flex-column flex-center">
                                                <span class="fs-0 fw-bold">R$237,00</span>
                                                <span class="fs-6 fw-semibold text-gray-600">Total Mensal</span>
                                            </div>
                                            <canvas id="project_overview_chart"
                                                aria-describedby="financial-chart-description"></canvas>
                                            <div id="financial-chart-description" class="sr-only">
                                                Gráfico circular mostrando o total mensal de receitas (R$45,00) e
                                                despesas (R$192,00).
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
                                            <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                <div class="bullet bg-success me-3"></div>
                                                <div class="text-gray-600">Receitas</div>
                                                <div class="ms-auto fw-bold text-gray-700">R$ 45,00</div>
                                            </div>
                                            <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                <div class="bullet bg-danger me-3"></div>
                                                <div class="text-gray-600">Despesas</div>
                                                <div class="ms-auto fw-bold text-gray-700">R$ 192,00</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="notice d-flex bg-light-primary rounded border-primary border-dashed p-6">
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">
                                                    <a href="#" data-bs-toggle="modal"
                                                        data-bs-target="#tipModal" class="fw-bold me-1">Dica:</a>
                                                    Convide colaboradores para melhorar a análise de receitas e
                                                    despesas.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                        <!--end::Col - Resumo Financeiro-->

                        <!--begin::Col - Doações e Ofertas-->
                        <div class="col-12 col-lg-7">
                            <div class="card card-flush h-lg-100">
                                <!--begin::Card header-->
                                <div class="card-header mt-6 text-center">
                                    <div class="card-title flex-column text-center">
                                        <h3 class="fw-bold mb-1">Doações e Ofertas</h3>
                                        <div class="fs-6 d-flex justify-content-center text-gray-600 fs-6 fw-semibold">
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
                                        <select id="yearSelector" name="year" data-control="select2"
                                            data-hide-search="true"
                                            class="form-select form-select-solid form-select-sm fw-bold w-100px">
                                            <option value="2025" {{ $selectedYear == 2025 ? 'selected' : '' }}>2025
                                            </option>
                                            <option value="2024" {{ $selectedYear == 2024 ? 'selected' : '' }}>2024
                                            </option>
                                            <option value="2022" {{ $selectedYear == 2022 ? 'selected' : '' }}>2022
                                            </option>
                                            <option value="2021" {{ $selectedYear == 2021 ? 'selected' : '' }}>2021
                                            </option>
                                            <option value="2020" {{ $selectedYear == 2020 ? 'selected' : '' }}>2020
                                            </option>
                                        </select>
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
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Content container-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->
</x-tenant-app-layout>



<!--begin::Vendors Javascript-->
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->
<script src="{{ url('assets/js/custom/apps/dashboard/grafico_doacoes.js') }}"></script>

<!--begin::Custom Javascript-->
<script src="{{ url('assets/js/widgets.bundle.js') }}"></script>
<!--end::Custom Javascript-->

<!--begin::Custom CSS-->
<style>
    .icon-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn:hover {
        background-color: #f0f0f0;
        transform: scale(1.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
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

    // Atualização dinâmica do gráfico via AJAX
    document.addEventListener('DOMContentLoaded', function() {
        const yearSelector = document.getElementById('yearSelector');
        yearSelector.addEventListener('change', function() {
            const selectedYear = this.value;
            // Tenta atualizar o gráfico via AJAX
            fetch(`/api/data?year=${selectedYear}`, {
                    method: 'GET'
                })
                .then(response => {
                    if (!response.ok) throw new Error('Erro na requisição');
                    return response.json();
                })
                .then(data => {
                    // Função fictícia para atualizar o gráfico (substitua pelo código real do gráfico)
                    updateChart(data);
                })
                .catch(error => {
                    // Fallback para redirecionamento completo
                    window.location.href = `?year=${selectedYear}`;
                });
        });

        // Função fictícia para atualizar o gráfico (substitua pela lógica real do gráfico)
        function updateChart(data) {
            console.log('Atualizando gráfico com dados:', data);
            // Exemplo: Atualize o canvas Dm_project_overview_graph com os novos dados
        }
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
