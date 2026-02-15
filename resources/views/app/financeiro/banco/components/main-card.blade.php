<div class="row no-gutters">
    <div class="12 col-sm-12 col-md-12 ">
        <div class="card mb-1 mb-xl-9">
            <div class="card-header hover-scroll-x overflow-x-auto">
                <div class="d-flex align-items-center justify-content-between flex-nowrap w-100"
                    style="min-width: max-content;">
                    <!--begin::Nav-->
                    <ul
                        class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold flex-nowrap">
                        <!--begin::Nav item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'contas_receber' ? 'active' : '' }}"
                                href="{{ route('banco.list', ['tab' => 'contas_receber', 'status' => 'total']) }}">
                                Receitas
                            </a>
                        </li>
                        <!--end::Nav item-->
                        <!--begin::Nav item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'contas_pagar' ? 'active' : '' }}"
                                href="{{ route('banco.list', ['tab' => 'contas_pagar', 'status' => 'total']) }}">
                                Despesas
                            </a>
                        </li>
                        <!--end::Nav item-->
                        <!--begin::Nav item-->
                        <li class="nav-item">
                            <a class="nav-link text-active-primary py-5 me-6 {{ $activeTab === 'extrato' ? 'active' : '' }}"
                                href="{{ route('banco.list', ['tab' => 'extrato']) }}">
                                Extrato
                            </a>
                        </li>
                        <!--end::Nav item-->
                    </ul>
                    <!--end::Nav-->
                    <div class="card-toolbar flex-nowrap ms-2">
                        <!--begin::Actions-->
                        <div class="d-flex align-items-center gap-2">
                            <!--begin::Novo Lançamento Drawer Button-->
                            <x-tenant-dropdown-button icon="bi bi-plus-circle" text="Novo Lançamento" variant="primary"
                                size="sm" heading="O que deseja criar?">
                                <x-tenant-dropdown-item icon="fa-regular fa-circle-up" iconClass="text-success"
                                    onclick="abrirDrawerLancamento('receita', 'Banco'); return false;"
                                    ariaLabel="Adicionar nova receita">
                                    Nova Receita
                                </x-tenant-dropdown-item>
                                <x-tenant-dropdown-item icon="fa-regular fa-circle-down" iconClass="text-danger"
                                    onclick="abrirDrawerLancamento('despesa', 'Banco'); return false;"
                                    ariaLabel="Adicionar nova despesa">
                                    Nova Despesa
                                </x-tenant-dropdown-item>
                            </x-tenant-dropdown-button>
                            <!--end::Novo Lançamento Drawer Button-->

                            <!--begin::Conciliação Bancária Button-->
                            <x-tenant-button-icon icon="bi bi-link-45deg" text="Conciliação Bancária"
                                dataBsToggle="modal" dataBsTarget="#modalConciliacao" variant="primary"
                                size="sm" />
                            <!--end::Conciliação Bancária Button-->

                            <!--begin::Domus Ia-->
                            <x-tenant-button-icon icon="bi bi-stars" text="Dominus IA"
                                href="{{ route('domusia.index') }}" variant="light-info" size="sm" />
                            <!--end::Domus Ia-->

                            <!--begin::Menu Relatórios-->
                            <x-tenant-relatorios-menu />
                            <!--end::Menu Relatórios-->

                            <!--begin::More Options Button-->
                            <div>
                                <button class="btn btn-sm btn-icon btn-light btn-active-color-primary"
                                    data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                    <i class="bi bi-three-dots fs-4"></i>
                                </button>
                                <!--begin::Menu 3-->
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3"
                                    data-kt-menu="true">
                                    <!--begin::Heading-->
                                    <div class="menu-item px-3">
                                        <div class="menu-content text-muted pb-2 px-3 fs-7 text-uppercase">Menu BB
                                        </div>
                                    </div>
                                    <!--end::Heading-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('bank-statements.index') }}"
                                            class="menu-link px-3">Extrato</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('domusia.index') }}" class="menu-link flex-stack px-3">Domus
                                            IA
                                            <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                                title="Faça o lançamento de receitas e despesas com a ajuda da IA"></i></a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <a href="{{ route('company.edit', ['tab' => 'horario-missas']) }}"
                                            class="menu-link px-3">Horário de Missa</a>
                                    </div>
                                    <!--end::Menu item-->
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3" data-kt-menu-trigger="hover"
                                        data-kt-menu-placement="right-end">
                                        <a href="#" class="menu-link px-3">
                                            <span class="menu-title">Configurações</span>
                                            <span class="menu-arrow"></span>
                                        </a>
                                        <!--begin::Menu sub-->
                                        <div class="menu-sub menu-sub-dropdown w-175px py-4">
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3 ">
                                                <a
                                                    class="menu-link px-3 {{ Route::currentRouteName() == 'entidades.index' ? 'active' : '' }}"href="{{ route('entidades.index') }}">
                                                    Criar Entidade Financeira</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3">
                                                <a class="menu-link px-3 {{ Route::currentRouteName() == 'lancamentoPadrao.index' ? 'active' : '' }}"
                                                    href="{{ route('lancamentoPadrao.index') }}">Categorias
                                                    Financeiras</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3 ">
                                                <a
                                                    class="menu-link px-3 {{ Route::currentRouteName() == 'costCenter.index' ? 'active' : '' }}"href="{{ route('costCenter.index') }}">Centro
                                                    de Custos</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3 ">
                                                <a
                                                    class="menu-link px-3 {{ Route::currentRouteName() == 'formas-pagamento.index' ? 'active' : '' }}"href="{{ route('formas-pagamento.index') }}">Formas
                                                    de Pagamento</a>
                                            </div>
                                            <!--end::Menu item-->
                                            <!--begin::Menu item-->
                                            <div class="menu-item px-3 ">
                                                <a class="menu-link px-3 {{ Route::currentRouteName() == 'entidades.index' ? 'active' : '' }}"
                                                    href="{{ route('entidades.index') }}">Entidades Financeiras</a>
                                            </div>
                                            <!--end::Menu item-->
                                        </div>
                                        <!--end::Menu sub-->
                                    </div>
                                    <!--end::Menu item-->
                                    <div class="separator separator-dotted "></div>
                                    <!--begin::Menu item-->
                                    <div class="menu-item px-3">
                                        <form action="{{ route('financeiro.recalcular-saldos') }}" method="POST"
                                            style="display: inline;">
                                            @csrf
                                            <button type="submit"
                                                class="menu-link flex-stack px-3 w-100 border-0 bg-transparent text-start"
                                                onclick="return confirm('⚠️ Deseja sincronizar todos os saldos com as movimentações?');">
                                                <span>Recalcular Saldos</span>
                                                <i class="fas fa-sync-alt ms-2 fs-7" data-bs-toggle="tooltip"
                                                    title="Sincroniza cache de saldos com movimentações (Admin only)"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <!--end::Menu item-->
                                </div>
                                <!--end::Menu Dropdown-->
                            </div>
                            <!--end::More Options Button-->
                        </div>
                        <!--end::Actions-->
                    </div>
                </div>
            </div>
            <div class="card-body pt-9 pb-0">
                <!--begin::Details-->
                <div class="d-flex flex-wrap flex-sm-nowrap mb-1">
                    <!--begin::Wrapper-->
                    <div class="flex-grow-1">
                        <!--begin::Header-->
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h3 class="fw-bold text-gray-800 fs-6 mb-0">Fluxo de Receitas e Despesas -
                                {{ $anoSelecionado }}</h3>
                            <!--begin::Daterange Picker-->
                            <x-tenant-daterange-button id="fluxo_periodo" defaultRange="year" variant="light"
                                opens="left" />
                            <!--end::Daterange Picker-->
                        </div>
                        <!--end::Header-->
                        <!--begin::Chart-->
                        <div id="kt_card_widget_12_chart" class="min-h-auto" style="height: 160px"
                            data-entradas="{{ json_encode($dadosFluxoCaixaAnual['entradas'] ?? []) }}"
                            data-saidas="{{ json_encode($dadosFluxoCaixaAnual['saidas'] ?? []) }}">
                        </div>
                        <!--end::Chart-->
                        <!--begin::Legend-->
                        <div class="d-flex align-items-center justify-content-center gap-5 mt-2"
                            id="kt_card_widget_12_legend">
                            <div class="d-flex align-items-center">
                                <i class="fa-regular fa-circle-up text-success fs-7 me-1"></i>
                                <span class="text-gray-600 fs-8 fw-semibold">Entradas</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fa-regular fa-circle-down text-danger fs-7 me-1"></i>
                                <span class="text-gray-600 fs-8 fw-semibold">Saídas</span>
                            </div>
                        </div>
                        <!--end::Legend-->
                    </div>
                    <!--end::Wrapper-->
                    @include('app.financeiro.banco.components.side-card')

                </div>
                <!--end::Details-->
            </div>
        </div>
    </div>
</div>

<!--begin::Script - Atualizar gráfico ao mudar período-->
@push('scripts')
    <script>
        document.addEventListener('daterangeChanged', function(e) {
            if (e.detail.id !== 'fluxo_periodo') return;

            var startDate = e.detail.start.format('YYYY-MM-DD');
            var endDate = e.detail.end.format('YYYY-MM-DD');

            // Atualizar título do gráfico
            var titleEl = document.querySelector('#kt_card_widget_12_chart').closest('.flex-grow-1').querySelector(
                'h3');
            if (titleEl) {
                var startLabel = e.detail.start.format('DD/MM/YYYY');
                var endLabel = e.detail.end.format('DD/MM/YYYY');

                // Verificar se é ano inteiro (ex: 01/01/2025 - 31/12/2025)
                if (e.detail.start.month() === 0 && e.detail.start.date() === 1 &&
                    e.detail.end.month() === 11 && e.detail.end.date() === 31 &&
                    e.detail.start.year() === e.detail.end.year()) {
                    titleEl.textContent = 'Fluxo de Receitas e Despesas - ' + e.detail.start.year();
                } else {
                    titleEl.textContent = 'Fluxo de Receitas e Despesas - ' + startLabel + ' a ' + endLabel;
                }
            }

            // Buscar dados do backend
            var url = (typeof bancoFluxoChartDataUrl !== 'undefined' ? bancoFluxoChartDataUrl :
                '/banco/fluxo-chart-data');
            var params = new URLSearchParams({
                start_date: startDate,
                end_date: endDate,
                group_by: 'auto'
            });

            fetch(url + '?' + params.toString(), {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data && data.entradas && data.saidas && data.categorias) {
                        KTCardWidget12.update(data.entradas, data.saidas, data.categorias);
                    }
                })
                .catch(function(error) {
                    console.error('Erro ao atualizar gráfico de fluxo:', error);
                });
        });
    </script>
@endpush
<!--end::Script-->

<!--begin::Modal - Conciliação Bancária (Importar OFX)-->
@include('app.financeiro.banco.components.modal')
<!--end::Modal - Conciliação Bancária-->
