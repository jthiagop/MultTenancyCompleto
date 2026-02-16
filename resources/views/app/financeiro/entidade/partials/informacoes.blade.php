<div>
    <div class="card mt-5">
        <div class="card-body">
            <!-- Container para o loader -->
            <div id="informacoesLoader" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p class="text-muted mt-3">Carregando informações...</p>
            </div>

            <!-- Container para o conteúdo (inicialmente vazio) -->
            <div id="informacoesContent" style="display: none;">
                <!-- Informações básicas do banco -->
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <h4 class="mb-4">Dados Bancários</h4>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <strong>Banco:</strong>
                                <span id="info-nome" class="ms-2">-</span>
                            </div>
                            <div>
                                <strong>Agência:</strong>
                                <span id="info-agencia" class="ms-2">-</span>
                            </div>
                            <div>
                                <strong>Conta:</strong>
                                <span id="info-conta" class="ms-2">-</span>
                            </div>
                            <div>
                                <strong>Saldo Inicial:</strong>
                                <span id="info-saldo-inicial" class="ms-2 text-primary fw-bold">-</span>
                            </div>
                            <div>
                                <strong>Saldo Atual:</strong>
                                <span id="info-saldo-atual" class="ms-2 text-success fw-bold">-</span>
                            </div>
                            <div>
                                <strong>Tipo:</strong>
                                <span id="info-tipo" class="ms-2">
                                    <span class="badge badge-light-primary">-</span>
                                </span>
                            </div>
                            @if(isset($entidade->bank) && $entidade->bank)
                            <div>
                                <strong>Instituição:</strong>
                                <span id="info-instituicao" class="ms-2">-</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <h4 class="mb-4">Estatísticas</h4>
                        <div class="d-flex flex-column gap-3">
                            <div>
                                <strong>Total de Transações:</strong>
                                <span id="info-total-transacoes" class="ms-2">-</span>
                            </div>
                            <div>
                                <strong>Transações Conciliadas:</strong>
                                <span id="info-conciliadas" class="ms-2 text-success">-</span>
                            </div>
                            <div>
                                <strong>Transações Pendentes:</strong>
                                <span id="info-pendentes" class="ms-2 text-warning">-</span>
                            </div>
                            <div>
                                <strong>Percentual Conciliado:</strong>
                                <span id="info-percentual" class="ms-2">
                                    <span class="badge badge-light-success">-</span>
                                </span>
                            </div>
                            <div>
                                <strong>Conciliações Pendentes:</strong>
                                <span id="info-conciliacoes-pendentes" class="ms-2 text-warning fw-bold">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($entidade->descricao) && $entidade->descricao)
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">Descrição</h4>
                        <p id="info-descricao" class="text-muted">-</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const entidadeId = {{ $entidade->id ?? 'null' }};

    if (!entidadeId) {
        document.getElementById('informacoesLoader').innerHTML =
            '<p class="text-danger">Erro: ID da entidade não encontrado.</p>';
        return;
    }

    // Função para carregar dados com filtros
    function carregarInformacoes() {
        const params = new URLSearchParams();
        if (window.periodoFiltro?.dataInicio) {
            params.append('data_inicio', window.periodoFiltro.dataInicio);
        }
        if (window.periodoFiltro?.dataFim) {
            params.append('data_fim', window.periodoFiltro.dataFim);
        }

        // Mostra o loader
        document.getElementById('informacoesLoader').style.display = 'block';
        document.getElementById('informacoesContent').style.display = 'none';

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
            renderInformacoes(data.data);
        } else {
            throw new Error(data.message || 'Erro ao processar dados');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('informacoesLoader').innerHTML =
            '<p class="text-danger">Erro ao carregar informações: ' + error.message + '</p>';
    });
    }

    // Carrega os dados inicialmente
    carregarInformacoes();

    // Escuta mudanças no período
    window.addEventListener('periodoAlterado', function() {
        carregarInformacoes();
    });

    function renderInformacoes(data) {
        const loader = document.getElementById('informacoesLoader');
        const content = document.getElementById('informacoesContent');

        // Esconde o loader
        loader.style.display = 'none';
        content.style.display = 'block';

        const entidade = data.entidade || {};
        const estatisticas = data.estatisticas || {};

        // Preenche dados bancários
        document.getElementById('info-nome').textContent = entidade.nome || '-';
        document.getElementById('info-agencia').textContent = entidade.agencia || '-';
        document.getElementById('info-conta').textContent = entidade.conta || '-';
        document.getElementById('info-saldo-inicial').textContent = formatarMoeda(entidade.saldo_inicial_real || 0);
        document.getElementById('info-saldo-atual').textContent = formatarMoeda(entidade.saldo_atual || 0);

        // Tipo
        const tipoElement = document.getElementById('info-tipo');
        const tipo = entidade.tipo || '-';
        if (tipo !== '-') {
            tipoElement.innerHTML = `<span class="badge ${tipo === 'banco' ? 'badge-light-primary' : 'badge-light-info'}">${tipo.charAt(0).toUpperCase() + tipo.slice(1)}</span>`;
        } else {
            tipoElement.innerHTML = '<span class="badge badge-light">-</span>';
        }

        // Instituição bancária (se houver)
        if (entidade.bank) {
            document.getElementById('info-instituicao').textContent = entidade.bank.name || '-';
        }

        // Estatísticas
        document.getElementById('info-total-transacoes').textContent = estatisticas.total_transacoes || 0;
        document.getElementById('info-conciliadas').textContent = estatisticas.total_conciliadas || 0;
        document.getElementById('info-pendentes').textContent = estatisticas.total_pendentes || 0;

        // Percentual conciliado
        const percentual = data.percentualConciliado || 0;
        const percentualElement = document.getElementById('info-percentual');
        const badgeClass = percentual >= 80 ? 'badge-light-success' : percentual >= 50 ? 'badge-light-warning' : 'badge-light-danger';
        percentualElement.innerHTML = `<span class="badge ${badgeClass}">${percentual}%</span>`;

        // Conciliações pendentes
        const conciliacoesPendentes = data.conciliacoesPendentes?.data?.length || data.conciliacoesPendentes?.length || 0;
        document.getElementById('info-conciliacoes-pendentes').textContent = conciliacoesPendentes;

        // Descrição (se houver)
        if (entidade.descricao) {
            document.getElementById('info-descricao').textContent = entidade.descricao;
        }
    }

    function formatarMoeda(valor) {
        return 'R$ ' + parseFloat(valor).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
});
</script>
