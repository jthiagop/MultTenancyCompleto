<div>
    <div class="card card-flush mt-5">
        <div class="card-body">
            <h5 class="card-title">Movimentações</h5>

            <!-- Container para o loader -->
            <div id="movimentacaoLoader" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Carregando movimentações...</p>
            </div>

            <!-- Container para o conteúdo (inicialmente vazio) -->
            <div id="movimentacaoContent" style="display: none;">
                <div id="movimentacaoEmpty" class="text-muted" style="display: none;">
                    <p>Nenhuma movimentação encontrada.</p>
                </div>

                <!-- Início do Accordion -->
                <div class="accordion" id="movimentacaoAccordion">
                    <!-- O conteúdo será inserido aqui via JavaScript -->
                </div>
                <!-- Fim do Accordion -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const entidadeId = {{ $entidade->id ?? 'null' }};

    if (!entidadeId) {
        document.getElementById('movimentacaoLoader').innerHTML =
            '<p class="text-danger">Erro: ID da entidade não encontrado.</p>';
        return;
    }

    // Função para carregar dados com filtros
    function carregarMovimentacoes() {
        const params = new URLSearchParams();
        if (window.periodoFiltro?.dataInicio) {
            params.append('data_inicio', window.periodoFiltro.dataInicio);
        }
        if (window.periodoFiltro?.dataFim) {
            params.append('data_fim', window.periodoFiltro.dataFim);
        }

        // Mostra o loader
        document.getElementById('movimentacaoLoader').style.display = 'block';
        document.getElementById('movimentacaoContent').style.display = 'none';
        document.getElementById('movimentacaoAccordion').innerHTML = '';

        // Faz a requisição AJAX para buscar os dados
        fetch(`{{ route('entidades.show.json', ':id') }}`.replace(':id', entidadeId) + (params.toString() ? '?' + params.toString() : ''), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erro ao carregar dados');
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.data) {
            renderMovimentacoes(data.data);
        } else {
            throw new Error(data.message || 'Erro ao processar dados');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('movimentacaoLoader').innerHTML =
            '<p class="text-danger">Erro ao carregar movimentações: ' + error.message + '</p>';
    });
    }

    // Carrega os dados inicialmente
    carregarMovimentacoes();

    // Escuta mudanças no período
    window.addEventListener('periodoAlterado', function() {
        carregarMovimentacoes();
    });

    function renderMovimentacoes(data) {
        const loader = document.getElementById('movimentacaoLoader');
        const content = document.getElementById('movimentacaoContent');
        const empty = document.getElementById('movimentacaoEmpty');
        const accordion = document.getElementById('movimentacaoAccordion');

        // Esconde o loader
        loader.style.display = 'none';
        content.style.display = 'block';

        // Verifica se há transações
        const transacoesPorDia = data.transacoesPorDia || {};

        if (Object.keys(transacoesPorDia).length === 0) {
            empty.style.display = 'block';
            return;
        }

        // Calcula saldo inicial (vem do accessor saldo_inicial_real via JSON appends)
        let saldoAtual = parseFloat(data.entidade.saldo_inicial_real || 0);

        // Converte transacoesPorDia para array e ordena as datas (mais antiga primeiro para cálculo de saldo)
        const diasArray = Object.keys(transacoesPorDia).sort((a, b) => {
            return new Date(a) - new Date(b);
        });

        // Primeiro, calcula os saldos de cada dia (do mais antigo para o mais recente)
        const saldosPorDia = {};
        diasArray.forEach(dia => {
            const listaTransacoes = Array.isArray(transacoesPorDia[dia]) ? transacoesPorDia[dia] : Object.values(transacoesPorDia[dia]);

            // Calcula saldo do dia (soma/subtrai as transações)
            listaTransacoes.forEach(transacao => {
                if (transacao.tipo === 'entrada') {
                    saldoAtual += parseFloat(transacao.valor || 0);
                } else {
                    saldoAtual -= parseFloat(transacao.valor || 0);
                }
            });
            saldosPorDia[dia] = saldoAtual;
        });

        // Ordena as datas (mais recente primeiro para exibição)
        const diasOrdenados = Object.keys(transacoesPorDia).sort((a, b) => {
            return new Date(b) - new Date(a);
        });

        // Para cada dia, renderiza o accordion item
        diasOrdenados.forEach((dia, index) => {
            const listaTransacoes = Array.isArray(transacoesPorDia[dia]) ? transacoesPorDia[dia] : Object.values(transacoesPorDia[dia]);
            const dataCarbon = new Date(dia + 'T00:00:00');

            // Calcula pendências do dia (transações não conciliadas)
            const qtdPendencias = listaTransacoes.filter(t =>
                t.status_conciliacao !== 'ok' && t.status_conciliacao !== 'ignorado'
            ).length;

            // Pega o saldo calculado para este dia
            const saldoDia = saldosPorDia[dia] || 0;

            // Formata a data
            const diaFormatado = formatarData(dataCarbon);
            const diaSemana = formatarDiaSemana(dataCarbon);

            // Cria o HTML do accordion item
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';
            accordionItem.innerHTML = `
                <h2 class="accordion-header" id="heading-${dia}">
                    <button class="accordion-button fs-4 fw-semibold collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#collapse-${dia}"
                        aria-expanded="false" aria-controls="collapse-${dia}">
                        ${diaFormatado} (${diaSemana})
                    </button>
                </h2>
                <div id="collapse-${dia}" class="accordion-collapse collapse"
                    aria-labelledby="heading-${dia}"
                    data-bs-parent="#movimentacaoAccordion">
                    <div class="accordion-body">
                        ${qtdPendencias > 0 ? `
                            <div class="alert alert-warning d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${qtdPendencias} conciliações pendentes neste dia.</strong>
                                    <br>
                                    Efetue as conciliações para acompanhar suas movimentações corretamente.
                                </div>
                                <div>
                                    <a href="#" class="btn btn-sm btn-light" onclick="expandirTodos()">Expandir tudo</a>
                                    <a href="#" class="btn btn-sm btn-light" onclick="recolherTodos()">Recolher tudo</a>
                                </div>
                            </div>
                        ` : `
                            <div class="alert alert-info d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Todos os lançamentos estão conciliados.</strong>
                                    <br>
                                    Nenhuma pendência encontrada para ${diaFormatado}.
                                </div>
                                <div>
                                    <a href="#" class="btn btn-sm btn-light" onclick="expandirTodos()">Expandir tudo</a>
                                    <a href="#" class="btn btn-sm btn-light" onclick="recolherTodos()">Recolher tudo</a>
                                </div>
                            </div>
                        `}
                        <table class="table table-bordered mb-3">
                            <thead>
                                <tr>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${listaTransacoes.map(transacao => `
                                    <tr>
                                        <td>${transacao.descricao || '-'}</td>
                                        <td>R$ ${formatarMoeda(transacao.valor || 0)}</td>
                                        <td>
                                            <span class="badge ${transacao.tipo === 'entrada' ? 'badge-success' : 'badge-danger'}">
                                                ${transacao.tipo ? transacao.tipo.charAt(0).toUpperCase() + transacao.tipo.slice(1) : '-'}
                                            </span>
                                        </td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                        <div class="text-end">
                            <small class="text-muted">
                                Saldo final do dia: <strong>R$ ${formatarMoeda(saldoDia)}</strong>
                            </small>
                        </div>
                    </div>
                </div>
            `;

            accordion.appendChild(accordionItem);
        });
    }

    function formatarData(data) {
        const dia = String(data.getDate()).padStart(2, '0');
        const mes = String(data.getMonth() + 1).padStart(2, '0');
        const ano = data.getFullYear();
        return `${dia}/${mes}/${ano}`;
    }

    function formatarDiaSemana(data) {
        const dias = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        return dias[data.getDay()];
    }

    function formatarMoeda(valor) {
        return parseFloat(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Funções para expandir/recolher todos
    window.expandirTodos = function() {
        const collapses = document.querySelectorAll('#movimentacaoAccordion .accordion-collapse');
        collapses.forEach(collapse => {
            const bsCollapse = new bootstrap.Collapse(collapse, {
                toggle: false
            });
            bsCollapse.show();
        });
    };

    window.recolherTodos = function() {
        const collapses = document.querySelectorAll('#movimentacaoAccordion .accordion-collapse');
        collapses.forEach(collapse => {
            const bsCollapse = new bootstrap.Collapse(collapse, {
                toggle: false
            });
            bsCollapse.hide();
        });
    };
});
</script>
