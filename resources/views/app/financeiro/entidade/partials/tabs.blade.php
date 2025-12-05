<!-- Navbar -->
<div class="card card-flush">
    <!--begin::Card header-->
    <div class="card-header align-items-stretch py-5">
        <div class="d-flex flex-column flex-lg-row flex-stack gap-5 w-100">
            <!--begin::Card title-->
            <div class="card-title flex-column flex-lg-row align-items-start align-items-lg-center gap-3 gap-lg-5">
                <!--begin::Select Banco-->
                <div class="d-flex align-items-center">
                    <select id="banco-select" name="entidade_id" class="form-select form-select-solid"
                        data-control="select2" data-placeholder="Selecione um banco" style="min-width: 220px;">
                        <option></option>
                        @isset($entidadesBancos)
                            @foreach ($entidadesBancos as $entidadeBanco)
                                <option value="{{ $entidadeBanco->id }}"
                                    {{ isset($entidade) && $entidade->id == $entidadeBanco->id ? 'selected' : '' }}>
                                    {{ $entidadeBanco->nome }}
                                </option>
                            @endforeach
                        @endisset
                    </select>
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
    <div class="card-body pt-0">
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <!-- Aba de Conciliações Pendentes -->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary me-4 active" data-bs-toggle="tab"
                    href="#kt_tab_pane_conciliacoes">
                    Conciliações Pendentes
                    <span id="badge-conciliacoes" class="badge badge-danger ms-2" style="display: none;">0</span>
                </a>
            </li>
            <!-- Aba de Movimentação -->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary me-4" data-bs-toggle="tab" href="#kt_tab_pane_movimentacao">
                    Movimentações
                </a>
            </li>
            <!-- Aba de Informações -->
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary me-4" data-bs-toggle="tab" href="#kt_tab_pane_informacao">
                    Informações
                </a>
            </li>
        </ul>
    </div>
    <!--end::Nav Tabs-->
</div>

{{-- Scripts necessários --}}
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/locale/pt-br.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3.1.0/daterangepicker.css">

<script>
    // Variável global para armazenar o período selecionado
    window.periodoFiltro = {
        dataInicio: null,
        dataFim: null
    };

    // Função global para recarregar dados das abas
    window.recarregarDadosAbas = function() {
        // Dispara evento customizado para que as abas recarreguem
        window.dispatchEvent(new CustomEvent('periodoAlterado', {
            detail: {
                dataInicio: window.periodoFiltro.dataInicio,
                dataFim: window.periodoFiltro.dataFim
            }
        }));
    };

    document.addEventListener('DOMContentLoaded', function() {
        const entidadeId = {{ $entidade->id ?? 'null' }};

        if (!entidadeId) {
            return;
        }

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
        $('#btn-mes-anterior').on('click', function() {
            navegarMesAnterior();
        });

        $('#btn-mes-proximo').on('click', function() {
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

        // Função para formatar moeda
        function formatarMoeda(valor) {
            if (valor === null || valor === undefined) return 'R$ 0,00';
            return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Carrega o contador de conciliações pendentes e informações adicionais
        function carregarInformacoes() {
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
                        // Atualiza contador de conciliações
                        const conciliacoesPendentes = data.data.conciliacoesPendentes;
                        const total = conciliacoesPendentes?.data?.length || conciliacoesPendentes
                            ?.length || 0;

                        const badge = document.getElementById('badge-conciliacoes');
                        if (badge) {
                            if (total > 0) {
                                badge.textContent = total;
                                badge.style.display = 'inline-block';
                            } else {
                                badge.style.display = 'none';
                            }
                        }

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
        window.addEventListener('periodoAlterado', function() {
            carregarInformacoes();
        });

        // Event listener para mudança de banco/entidade selecionada
        const bancoSelect = $('#banco-select');

        // Inicializa o Select2 se ainda não foi inicializado
        if (bancoSelect.length && !bancoSelect.hasClass('select2-hidden-accessible')) {
            bancoSelect.select2({
                placeholder: "Selecione um banco",
                allowClear: false,
                minimumResultsForSearch: 0
            });
        }

        // Quando o usuário selecionar uma entidade financeira
        bancoSelect.on('change', function() {
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
