<!-- Navbar -->
<div class="card card-flush p mb-0">
    <!--begin::Card header-->
    <div class="card-header align-items-stretch py-5">
        <div class="d-flex flex-column flex-lg-row flex-stack gap-5 w-100">
            <!--begin::Card title-->
            <div class="card-title flex-column flex-lg-row align-items-start align-items-lg-center gap-3 gap-lg-5">
                <!--begin::Select Banco-->
                <div class="d-flex align-items-center" style="min-width: 250px;">
                    <x-tenant-select name="entidade_id" id="banco-select" 
                        placeholder="Selecione um banco" :hideSearch="true"
                        class="w-250px">
                        @if (isset($entidadesBancos) && $entidadesBancos->isNotEmpty())
                            @foreach ($entidadesBancos as $entidadeBanco)
                                <option value="{{ $entidadeBanco->id }}"
                                    data-kt-select2-icon="{{ $entidadeBanco->bank->logo_path ?? asset('tenancy/assets/media/svg/bancos/default.svg') }}"
                                    data-nome="{{ $entidadeBanco->nome }}" data-origem="Banco"
                                    {{ isset($entidade) && $entidade->id == $entidadeBanco->id ? 'selected' : '' }}>
                                    {{ $entidadeBanco->agencia }} - {{ $entidadeBanco->conta }}
                                </option>
                            @endforeach
                        @endif
                        @if (isset($entidadesCaixa) && $entidadesCaixa->isNotEmpty())
                            @foreach ($entidadesCaixa as $entidadeCaixa)
                                <option value="{{ $entidadeCaixa->id }}"
                                    data-kt-select2-icon="{{ url('/tenancy/assets/media/svg/bancos/caixa.svg') }}"
                                    data-nome="{{ $entidadeCaixa->nome }}" data-origem="Caixa"
                                    {{ isset($entidade) && $entidade->id == $entidadeCaixa->id ? 'selected' : '' }}>
                                    {{ $entidadeCaixa->nome }}
                                </option>
                            @endforeach
                        @endif
                    </x-tenant-select>
                </div>
                <!--end::Select Banco-->
            </div>
            <!--end::Card title-->

            <!--begin::Card toolbar-->
            <div class="card-toolbar flex-column flex-lg-row align-items-end align-items-lg-center gap-4">
                <!--begin::Daterangepicker-->
                <div class="d-flex align-items-center gap-2">
                    <!-- Botão Mês Anterior -->
                    <button type="button" id="btn-mes-anterior" class="btn btn-icon btn-sm btn-light-primary"
                        title="Mês Anterior">
                        <i class="bi bi-chevron-left fs-2"></i>
                    </button>

                    <!-- Input do Daterangepicker -->
                    <input class="form-control form-control-solid" style="width: 250px;"
                        placeholder="Selecionar período" id="Periodo" />

                    <!-- Botão Próximo Mês -->
                    <button type="button" id="btn-mes-proximo" class="btn btn-icon btn-sm btn-light-primary"
                        title="Próximo Mês">
                        <i class="bi bi-chevron-right fs-2"></i>
                    </button>
                </div>
                <!--end::Daterangepicker-->

                <!--begin::Informações Financeiras-->
                <div class="d-flex flex-column align-items-end gap-2">
                    <div class="text-muted fs-7 d-flex align-items-center gap-2">
                        <span>Saldo atual na Dominus:</span>
                        <span id="saldo-atual" class="fw-bold text-dark fs-6">-</span>
                    </div>
                    <div class="text-muted fs-7 d-flex align-items-center gap-2">
                        <span>Valor pendente de conciliação:</span>
                        <span id="valor-pendente" class="fw-bold text-warning fs-6">-</span>
                        <i class="bi bi-info-circle text-primary fs-7" data-bs-toggle="tooltip"
                            title="Valor total dos lançamentos pendentes de conciliação"></i>
                    </div>
                </div>
                <!--end::Informações Financeiras-->
            </div>
            <!--end::Card toolbar-->

        </div>

        <!--begin::Informações de Data-->
        <div class="d-flex flex-column gap-2">
            <div class="text-muted fs-7">
                <span>Data da última atualização: </span>
                <span id="data-ultima-atualizacao" class="fw-semibold text-dark">-</span>
            </div>
        </div>
        <!--end::Informações de Data-->
    </div>
    <!--end::Card header-->

    <!--begin::Separator-->
    <div class="separator separator-dashed"></div>
    <!--end::Separator-->

    <!--begin::Nav Tabs-->
    <div class="card-header  pt-0 pb-0">
        <div class="d-flex justify-content-between align-items-center w-100">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-1x border-transparent fs-5 fw-bold">
                <!-- Aba de Conciliações Pendentes -->
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-4 {{ ($activeTab ?? 'conciliacoes') === 'conciliacoes' ? 'active' : '' }}"
                        href="{{ route('entidades.show', $entidade->id) }}">
                        <i class="bi bi-link-45deg fs-3 me-2"></i>
                        Conciliações Pendentes
                    </a>
                </li>
                <!-- Aba de Histórico -->
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-4 {{ ($activeTab ?? 'historico') === 'historico' ? 'active' : '' }}"
                        href="{{ route('entidades.historico', $entidade->id) }}">
                        <i class="bi bi-clock-history fs-4 me-2" aria-hidden="true"></i>
                        Histórico
                    </a>
                </li>
                <!-- Aba de Movimentação -->
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-4 {{ ($activeTab ?? '') === 'movimentacoes' ? 'active' : '' }}"
                        href="{{ route('entidades.movimentacoes', $entidade->id) }}">
                        Movimentações
                    </a>
                </li>
                <!-- Aba de Informações -->
                <li class="nav-item">
                    <a class="nav-link text-active-primary me-4 {{ ($activeTab ?? '') === 'informacoes' ? 'active' : '' }}"
                        href="{{ route('entidades.informacoes', $entidade->id) }}">
                        Informações
                    </a>
                </li>
            </ul>
            <!--begin::Actions-->
            <div class="d-flex align-items-end gap-2 ms-auto">
                <!--begin::Horários de Missas Button-->
                <button class="btn btn-sm btn-primary" data-kt-action="open-conciliacao-missas"
                    id="btnHorariosMissasHeader">
                    <i class="bi bi-alarm me-2"></i>
                    Horários de Missas
                </button>
                <!--end::Horários de Missas Button-->
            </div>
            <!--end::Actions-->
        </div>
    </div>
    <!--end::Nav Tabs-->
</div>
{{-- Scripts necessários --}}
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/locale/pt-br.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">
{{-- Toast Script --}}
<script src="/tenancy/assets/js/toasts.js"></script>

<script>
    // Variável global para armazenar o período selecionado
    window.periodoFiltro = {
        dataInicio: null,
        dataFim: null
    };

    // Função global para recarregar dados das abas
    window.recarregarDadosAbas = function () {
        // Dispara evento customizado para que as abas recarreguem
        window.dispatchEvent(new CustomEvent('periodoAlterado', {
            detail: {
                dataInicio: window.periodoFiltro.dataInicio,
                dataFim: window.periodoFiltro.dataFim
            }
        }));
    };

    document.addEventListener('DOMContentLoaded', function () {
        const entidadeId = {{ $entidade->id ?? 'null' }};

        if (!entidadeId) {
            return;
        }

        // Handler para o botão de Histórico
        const btnHistorico = document.getElementById('btnHistoricoConciliações');
        const linkHistorico = document.getElementById('link-historico-hidden');
        if (btnHistorico && linkHistorico) {
            btnHistorico.addEventListener('click', function (e) {
                e.preventDefault();
                linkHistorico.click();
            });
        }

        // Função global para carregar o total de conciliações pendentes (independente do filtro de data)
        window.carregarTotalPendentes = function () {
            fetch(`{{ route('entidades.total-pendentes', ':id') }}`.replace(':id', entidadeId), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const total = data.total || 0;

                        const badge = document.getElementById('badge-conciliacoes');
                        if (badge) {
                            if (total > 0) {
                                badge.textContent = total;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar total de pendentes:', error);
                });
        }

        // Carrega o total de pendentes ao carregar a página
        carregarTotalPendentes();

        // Recarrega o total quando a aba de conciliações for ativada
        document.querySelector('a[href="#kt_tab_pane_conciliacoes"]')?.addEventListener('shown.bs.tab', function () {
            carregarTotalPendentes();
        });

        // Configura a localidade para português do Brasil
        moment.locale('pt-br');

        // Configuração inicial do intervalo de datas (últimos 30 dias)
        var start = moment().subtract(29, "days");
        var end = moment();
        var input = $("#Periodo");
        var daterangepickerInstance = null;

        // Callback para exibir o intervalo selecionado e atualizar filtro
        function cb(start, end) {
            input.val(start.format("DD/MM/YYYY") + " - " + end.format("DD/MM/YYYY"));

            // Atualiza o filtro global
            window.periodoFiltro.dataInicio = start.format('YYYY-MM-DD');
            window.periodoFiltro.dataFim = end.format('YYYY-MM-DD');

            // Recarrega os dados das abas
            window.recarregarDadosAbas();
        }

        // Função para navegar para o mês anterior
        function navegarMesAnterior() {
            if (daterangepickerInstance) {
                var currentStart = daterangepickerInstance.startDate.clone();
                var currentEnd = daterangepickerInstance.endDate.clone();

                // Calcula a duração do período atual
                var duracao = currentEnd.diff(currentStart, 'days');

                // Move para o mês anterior mantendo a duração
                var newStart = currentStart.clone().subtract(1, 'month');
                var newEnd = newStart.clone().add(duracao, 'days');

                // Atualiza o daterangepicker
                daterangepickerInstance.setStartDate(newStart);
                daterangepickerInstance.setEndDate(newEnd);
                cb(newStart, newEnd);
            }
        }

        // Função para navegar para o próximo mês
        function navegarMesProximo() {
            if (daterangepickerInstance) {
                var currentStart = daterangepickerInstance.startDate.clone();
                var currentEnd = daterangepickerInstance.endDate.clone();

                // Calcula a duração do período atual
                var duracao = currentEnd.diff(currentStart, 'days');

                // Move para o próximo mês mantendo a duração
                var newStart = currentStart.clone().add(1, 'month');
                var newEnd = newStart.clone().add(duracao, 'days');

                // Atualiza o daterangepicker
                daterangepickerInstance.setStartDate(newStart);
                daterangepickerInstance.setEndDate(newEnd);
                cb(newStart, newEnd);
            }
        }

        // Inicializar o DateRangePicker
        input.daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                "Hoje": [moment(), moment()],
                "Ontem": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "Últimos 7 Dias": [moment().subtract(6, "days"), moment()],
                "Últimos 30 Dias": [moment().subtract(29, "days"), moment()],
                "Este Mês": [moment().startOf("month"), moment().endOf("month")],
                "Mês Passado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1,
                    "month").endOf("month")]
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
            },
            opens: 'left'
        }, cb);

        // Armazena a instância do daterangepicker
        daterangepickerInstance = input.data('daterangepicker');

        // Executa o callback inicial
        cb(start, end);

        // Adiciona event listeners aos botões de navegação
        $('#btn-mes-anterior').on('click', function () {
            navegarMesAnterior();
        });

        $('#btn-mes-proximo').on('click', function () {
            navegarMesProximo();
        });

        // Função para formatar data
        function formatarData(data) {
            if (!data) return '-';
            const date = new Date(data);
            const dia = String(date.getDate()).padStart(2, '0');
            const mes = String(date.getMonth() + 1).padStart(2, '0');
            const ano = date.getFullYear();
            const horas = String(date.getHours()).padStart(2, '0');
            const minutos = String(date.getMinutes()).padStart(2, '0');
            return `${dia}/${mes}/${ano} às ${horas}h${minutos}`;
        }

        // Função global para formatar moeda
        window.formatarMoeda = function (valor) {
            if (valor === null || valor === undefined) return 'R$ 0,00';
            return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Função global para carregar o contador de conciliações pendentes e informações adicionais
        window.carregarInformacoes = function () {
            const params = new URLSearchParams();
            if (window.periodoFiltro.dataInicio) {
                params.append('data_inicio', window.periodoFiltro.dataInicio);
            }
            if (window.periodoFiltro.dataFim) {
                params.append('data_fim', window.periodoFiltro.dataFim);
            }

            fetch(`{{ route('entidades.show.json', ':id') }}`.replace(':id', entidadeId) + (params.toString() ?
                '?' + params.toString() : ''), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || ''
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data) {
                        // Badge de conciliações pendentes é atualizado pela função carregarTotalPendentes()
                        // que busca o total geral independente do filtro de data

                        // Atualiza informações adicionais
                        const infoAdicional = data.data.informacoesAdicionais || {};

                        // Data da última atualização
                        const dataUltimaAtualizacao = document.getElementById('data-ultima-atualizacao');
                        if (dataUltimaAtualizacao) {
                            dataUltimaAtualizacao.textContent = formatarData(infoAdicional
                                .data_ultima_atualizacao);
                        }

                        // Data do último lançamento importado
                        const dataUltimoLancamento = document.getElementById('data-ultimo-lancamento');
                        if (dataUltimoLancamento) {
                            const dataLancamento = infoAdicional.data_ultimo_lancamento;
                            if (dataLancamento) {
                                const date = new Date(dataLancamento);
                                const dia = String(date.getDate()).padStart(2, '0');
                                const mes = String(date.getMonth() + 1).padStart(2, '0');
                                const ano = date.getFullYear();
                                dataUltimoLancamento.textContent = `${dia}/${mes}/${ano}`;
                            } else {
                                dataUltimoLancamento.textContent = '-';
                            }
                        }

                        // Saldo atual
                        const saldoAtual = document.getElementById('saldo-atual');
                        if (saldoAtual) {
                            saldoAtual.textContent = formatarMoeda(infoAdicional.saldo_atual);
                        }

                        // Valor pendente de conciliação
                        const valorPendente = document.getElementById('valor-pendente');
                        if (valorPendente) {
                            valorPendente.textContent = formatarMoeda(infoAdicional
                                .valor_pendente_conciliacao);
                        }
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar informações:', error);
                });
        }

        // Carrega as informações inicialmente
        carregarInformacoes();

        // Escuta mudanças no período para atualizar as informações
        window.addEventListener('periodoAlterado', function () {
            carregarInformacoes();
        });

        // Event listener para mudança de banco/entidade selecionada
        const bancoSelect = $('#banco-select');

        // Inicializa o Select2 se ainda não foi inicializado
        if (bancoSelect.length && !bancoSelect.hasClass('select2-hidden-accessible')) {

            const optionLog = [];
            bancoSelect.find('option').each(function() {
                optionLog.push({
                    value: this.value,
                    text: this.text,
                    icon: this.getAttribute('data-kt-select2-icon'),
                    origem: this.getAttribute('data-origem')
                });
            });
            console.table(optionLog);

            const optionFormatBanco = function(item) {
                if (!item.id) {
                    return item.text;
                }

                const imgUrl = item.element?.getAttribute('data-kt-select2-icon');
                if (!imgUrl) {
                    return item.text;
                }

                const span = document.createElement('span');
                span.innerHTML = `<img src="${imgUrl}" class="rounded-circle h-20px me-2" alt="logo"/>${item.text}`;
                return $(span);
            };

            bancoSelect.select2({
                placeholder: "Selecione um banco",
                allowClear: false,
                minimumResultsForSearch: 0,
                templateSelection: optionFormatBanco,
                templateResult: optionFormatBanco,
                width: '100%'
            });

        }

        // Controle do botão Horários de Missas
        const btnHorariosMissas = document.getElementById('btnHorariosMissas');
        const hasHorariosMissas = @json(isset($hasHorariosMissas) && $hasHorariosMissas);

        if (btnHorariosMissas) {
            // Previne a abertura do modal se não houver horários
            btnHorariosMissas.addEventListener('click', function (e) {
                if (!hasHorariosMissas) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Exibe toast de aviso usando a função específica do toasts.js
                    if (typeof window.showHorariosMissasToast === 'function') {
                        window.showHorariosMissasToast({
                            cadastrarUrl: '{{ route('company.edit', ['tab' => 'horario-missas']) }}',
                            delay: 8000,
                            icon: 'ki-duotone ki-information-5'
                        });
                    } else {
                        // Fallback se showHorariosMissasToast não estiver disponível
                        console.warn(
                            'showHorariosMissasToast não está disponível. Certifique-se de que toasts.js está carregado.'
                        );
                    }
                    return false;
                }
            });

            // Adiciona estilo visual quando desabilitado
            if (!hasHorariosMissas && btnHorariosMissas) {
                btnHorariosMissas.classList.add('opacity-50');
                btnHorariosMissas.style.cursor = 'not-allowed';
            }
        }

        // Quando o usuário selecionar uma entidade financeira
        bancoSelect.on('change', function () {
            const entidadeId = $(this).val();

            if (entidadeId) {
                // Redireciona para a página da entidade selecionada
                // A rota resource 'entidades' gera a rota 'entidades.show'
                // Usa a URL base atual e substitui apenas o ID
                const currentPath = window.location.pathname;
                const pathParts = currentPath.split('/').filter(part => part !== '');
                // Remove o último segmento (ID atual) e adiciona o novo ID
                pathParts[pathParts.length - 1] = entidadeId;
                const newUrl = '/' + pathParts.join('/');
                window.location.href = newUrl;
            }
        });
    });
</script>