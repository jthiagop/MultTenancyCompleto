<x-tenant-app-layout>
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Content container-->
            <!--begin::Toolbar-->
            <x-toolbar :company="$company" />
            <!--end::Toolbar-->
            <div id="kt_app_content_container" class="app-container container-xxl">
                <!--begin::Input group-->
                <div id="kt_app_content" class="app-content flex-column-fluid">
                    <!--begin::Row-->
                    <div class="row mb-xxl-5">

                        <!--begin::Col - Financeiro-->
                        <div class="col-lg-4 hover-elevate-up parent-hover mb-5">
                            <input type="radio" class="btn-check" name="account_type" value="personal"
                                id="kt_create_account_form_account_type_personal" />
                            <a href="{{ route('caixa.index') }}"
                                class="btn btn-outline btn-outline-dashed btn-active-light-primary active p-4 d-flex align-items-center"
                                aria-label="Acessar módulo Financeiro">
                                <img class="me-5" width="75px" height="75px" src="/assets/media/png/financeiro.svg"
                                    alt="Ícone Financeiro">
                                <span class="d-block fw-semibold text-start">
                                    <span class="text-dark fw-bold d-block fs-4 mb-2">Financeiro</span>
                                    <span class="text-muted fw-semibold fs-6">Cadastros financeiros,
                                        movimentações</span>
                                </span>
                            </a>
                        </div>

                        <!--begin::Col - Patrimônio-->
                        <div class="col-lg-4 hover-elevate-up parent-hover mb-5">
                            <input type="radio" class="btn-check" name="account_type" value="donations"
                                id="kt_create_account_form_account_type_donations" />
                            <a href="{{ route('patrimonio.index') }}"
                                class="btn btn-outline btn-outline-dashed btn-active-light-primary active p-4 d-flex align-items-center"
                                aria-label="Acessar módulo Patrimônio">
                                <div class="d-flex align-items-center me-5">
                                    <img width="75px" height="75px" src="/assets/media/png/house3d.png"
                                        alt="Ícone Patrimônio">
                                </div>
                                <span class="d-block fw-semibold text-start">
                                    <span class="text-dark fw-bold fs-4 mb-2 d-block">Patrimônio</span>
                                    <span class="text-muted fw-semibold fs-6">Gestão patrimonial, foro e laudêmio</span>
                                </span>
                            </a>
                        </div>

                        <!--begin::Col - Contabilidade-->
                        <div class="col-lg-4 hover-elevate-up parent-hover mb-3">
                            <a href="{{ route('caixa.create') }}"
                                class="btn btn-outline btn-outline-dashed btn-outline-danger disabled active p-4 d-flex align-items-center">
                                <!--begin::Icon and Badge-->
                                <div class="d-flex align-items-center me-4">
                                    <img class="me-5" width="75px" height="75px"
                                        src="/assets/media/png/contabilidade.png" alt="Contabilidade">
                                </div>
                                <!--end::Icon and Badge-->
                                <!--begin::Info-->
                                <span class="d-block fw-semibold text-start">
                                    <span class="d-block fw-semibold text-start">
                                        <span class="text-dark fw-bold d-block fs-4 mb-2">Contabilidade</span>
                                        <span class="text-muted fw-semibold fs-6">Controle financeiro e gestão de
                                            ativos.</span>
                                    </span>
                                </span>
                                <!--end::Info-->
                                <!--begin::Lock Icon-->
                                <div class="d-flex justify-content-end ms-4 mb-15">
                                    <i class="fa-solid fa-lock" title="Módulo Bloqueado!" alt="Módulo Bloqueado!"></i>
                                </div>
                                <!--end::Lock Icon-->
                            </a>
                        </div>

                        <!--begin::Col - Dízimo e Doações-->
                        <div class="col-lg-4 hover-elevate-up parent-hover mb-5">
                            <input type="radio" class="btn-check" name="account_type" value="donations"
                                id="kt_create_account_form_account_type_donations" />
                            <a href="#"
                                class="btn btn-outline btn-outline-dashed btn-active-light-primary active p-4 d-flex align-items-center"
                                aria-label="Acessar módulo Dízimo e Doações">
                                <img class="me-5" width="75px" height="75px" src="/assets/media/png/dizimo.png"
                                    alt="Ícone Dízimo e Doações">
                                <span class="d-block fw-semibold text-start">
                                    <span class="text-dark fw-bold fs-4 mb-2 d-block">Dízimo e Doações</span>
                                    <span class="text-muted fw-semibold fs-6">Gestão de dízimos e doações</span>
                                </span>
                            </a>
                        </div>

                        <!--begin::Col - Cadastro de Fiéis-->
                        <div class="col-lg-4 hover-elevate-up parent-hover mb-5">
                            <input type="radio" class="btn-check" name="account_type" value="faithful"
                                id="kt_create_account_form_account_type_faithful" />
                            <a href="{{ route('fieis.index') }}"
                                class="btn btn-outline btn-outline-dashed btn-active-light-primary active p-4 d-flex align-items-center"
                                aria-label="Acessar módulo Cadastro de Fiéis">
                                <img class="me-5" width="75px" height="75px" src="/assets/media/png/fieis.png"
                                    alt="Ícone Cadastro de Fiéis">
                                <span class="d-block fw-semibold text-start">
                                    <span class="text-dark fw-bold fs-4 mb-2 d-block">Cadastro de Fiéis</span>
                                    <span class="text-muted fw-semibold fs-6">Gerenciamento de membros e
                                        contribuições</span>
                                </span>
                            </a>
                        </div>

                    </div>
                    <!--end::Row-->



                    <div class="separator separator-dotted separator-content my-5 d-flex align-items-center">
                        <span class="me-2">
                            <i class="fa-solid fa-clock-rotate-left fa-5x"></i>
                        </span>
                        <span class="h1 mb-0">Resumo </span>
                    </div>
                    <!--begin::Row-->
                    <div class="row g-6 g-xl-9 mt-xl-1">
                        <!--begin::Col-->
                        <div class="col-lg-5">
                            <!--begin::Summary-->
                            <div class="card card-flush h-lg-100">
                                <!--begin::Card header-->
                                <div class="card-header mt-6">
                                    <!--begin::Card title-->
                                    <div class="card-title flex-column">
                                        <h3 class="fw-bold mb-1">Resumo Financeiro</h3>
                                        <div class="fs-6 fw-semibold text-gray-400">Receitas e Despesas Totais</div>
                                    </div>
                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-light btn-sm">Ver Detalhes</a>
                                    </div>
                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body p-9 pt-5">
                                    <!--begin::Wrapper-->
                                    <div class="d-flex flex-wrap">
                                        <!--begin::Chart-->
                                        <div class="position-relative d-flex flex-center h-175px w-175px me-15 mb-7">
                                            <div
                                                class="position-absolute translate-middle start-50 top-50 d-flex flex-column flex-center">
                                                <span class="fs-0 fw-bold">R$237,00</span>
                                                <span class="fs-6 fw-semibold text-gray-400">Total Mensal</span>
                                            </div>
                                            <canvas id="project_overview_chart"></canvas>
                                        </div>
                                        <!--end::Chart-->
                                        <!--begin::Labels-->
                                        <div
                                            class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
                                            <!--begin::Label Receita-->
                                            <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                <div class="bullet bg-success me-3"></div>
                                                <div class="text-gray-400">Receitas</div>
                                                <div class="ms-auto fw-bold text-gray-700">R$ 45,00</div>
                                            </div>
                                            <!--end::Label Receita-->
                                            <!--begin::Label Despesa-->
                                            <div class="d-flex fs-6 fw-semibold align-items-center mb-3">
                                                <div class="bullet bg-danger me-3"></div>
                                                <div class="text-gray-400">Despesas</div>
                                                <div class="ms-auto fw-bold text-gray-700">R$ 192,00</div>
                                            </div>
                                            <!--end::Label Despesa-->
                                        </div>
                                        <!--end::Labels-->
                                    </div>
                                    <!--end::Wrapper-->
                                    <!--begin::Notice-->
                                    <div
                                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                                        <!--begin::Wrapper-->
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <!--begin::Content-->
                                            <div class="fw-semibold">
                                                <div class="fs-6 text-gray-700">
                                                    <a href="#" class="fw-bold me-1">Dica:</a> Convide
                                                    colaboradores para melhorar a análise de receitas e despesas.
                                                </div>
                                            </div>
                                            <!--end::Content-->
                                        </div>
                                        <!--end::Wrapper-->
                                    </div>
                                    <!--end::Notice-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Summary-->
                        </div>

                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-lg-7">
                            <!--begin::Graph-->
                            <div class="card card-flush h-lg-100">
                                <!--begin::Card header-->
                                <div class="card-header mt-6 text-center">
                                    <!--begin::Card title-->
                                    <div class="card-title flex-column text-center">
                                        <h3 class="fw-bold mb-1">Doações e Ofertas</h3>
                                        <!--begin::Labels-->
                                        <div class="fs-6 d-flex justify-content-center text-gray-400 fs-6 fw-semibold">
                                            <!--begin::Label-->
                                            <div class="d-flex align-items-center me-6">
                                                <span class="menu-bullet d-flex align-items-center me-2">
                                                    <span class="bullet bg-success"></span>
                                                </span>Doações
                                            </div>
                                            <!--end::Label-->
                                            <!--begin::Label-->
                                            <div class="d-flex align-items-center me-6">
                                                <span class="menu-bullet d-flex align-items-center me-2">
                                                    <span class="bullet bg-primary"></span>
                                                </span>Coletas
                                            </div>
                                            <!--end::Label-->
                                            <!--begin::Label-->
                                            <div class="d-flex align-items-center me-3">
                                                <span class="menu-bullet d-flex align-items-center me-2">
                                                    <span class="bullet bg-warning"></span>
                                                </span>Intenções
                                            </div>
                                            <!--end::Label-->
                                        </div>
                                        <!--end::Labels-->
                                    </div>

                                    <!--end::Card title-->
                                    <!--begin::Card toolbar-->
                                    <div class="card-toolbar">
                                        <!-- Seletor de Ano -->
                                        <select id="yearSelector" name="year" data-control="select2"
                                            data-hide-search="true"
                                            class="form-select form-select-solid form-select-sm fw-bold w-100px">
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

                                    <!--end::Card toolbar-->
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body pt-1 pb-0 px-5">
                                    <!--begin::Chart-->
                                    <div id="Dm_project_overview_graph" class="card-rounded-bottom"
                                        style="height: 300px"></div>
                                    <!--end::Chart-->
                                </div>
                                <!--end::Card body-->
                            </div>
                            <!--end::Graph-->
                        </div>
                        <!--end::Col-->
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Input group-->
            </div>
            <!--end::Content container-->
        </div>
    </div>

</x-tenant-app-layout>

<!--begin::Vendors Javascript(used for this page only)-->
<script src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
<!--end::Vendors Javascript-->

<!--begin::Custom Javascript(used for this page only)-->
<script src="{{ url('assets/js/custom/apps/projects/list/list.js') }}"></script>
<script src="{{ url('assets/js/widgets.bundle.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/chat/chat.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/upgrade-plan.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/create-campaign.js') }}"></script>
<script src="{{ url('assets/js/custom/utilities/modals/users-search.js') }}"></script>
<script src="{{ url('assets/js/custom/apps/dashboard/grafico_doacoes.js') }}"></script>



<!--end::Custom Javascript-->
<!--end::Javascript-->


<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'short', // Abreviação do dia da semana
                day: 'numeric', // Dia do mês
                month: 'short', // Mês abreviado
                year: 'numeric', // Ano com 4 dígitos
                hour: '2-digit', // Hora com 2 dígitos
                minute: '2-digit' // Minuto com 2 dígitos
            };

            // Gera a data e hora com letras maiúsculas
            const dateTimeString = now.toLocaleDateString('pt-BR', options).toUpperCase();

            document.getElementById('datetime').textContent = dateTimeString;
        }

        // Atualiza a cada minuto
        updateDateTime();
        setInterval(updateDateTime, 60000);
    });

    // Dados do gráfico de área
    const areaChartData = @json($areaChartData);

    document.addEventListener('DOMContentLoaded', function() {
        const yearSelector = document.getElementById('yearSelector');

        yearSelector.addEventListener('change', function() {
            const selectedYear = this.value;
            // Redireciona para a página com o parâmetro do ano na URL
            window.location.href = `?year=${selectedYear}`;
        });
    });
</script>
</div>
<!--end::Toolbar-->
