<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Prestação de Contas</title>

    {{-- Bootstrap 5 – CDN (Browsershot carrega normalmente) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous">

    {{-- Chart.js para gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- CSS próprio pensado para impressão --}}
    <style>
        @page { size: A4 landscape; margin: 8mm 8mm 18mm 8mm; }
        body   { font-size: .77rem; }
        .logo  { height: 60px; }
        .page-break { page-break-after: always; }

        /* Cabeçalho */
        .header-container {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 15px 0;
            margin-bottom: 20px;
        }
        .header-content {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .header-logo {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: center;
        }
        .header-logo img {
            max-width: 100px;
            max-height: 100px;
        }
        .header-text {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 0 15px;
        }
        .header-text h4 {
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            font-size: 1rem;
            line-height: 1.3;
        }
        .header-text .subtitle {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-top: 3px;
        }
        .header-text small {
            display: block;
            font-size: 0.65rem;
            line-height: 1.5;
            margin-top: 3px;
        }

        /* Tabelas */
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }

        /* zebra na tabela */
        table tbody tr:nth-child(odd) { background: #f8f9fa; }

        /* Cores das colunas */
        .header-entrada {
            background-color: #28a745 !important;
            color: white !important;
            font-weight: bold;
        }
        .header-saida {
            background-color: #eb1228 !important;
            color: white !important;
            font-weight: bold;
        }
        .total-entrada {
            background-color: #28a745 !important;
            color: white;
            font-weight: bold;
        }
        .total-saida {
            background-color: #eb1228 !important;
            color: white;
            font-weight: bold;
        }

        /* Gráficos */
        .chart-container {
            position: relative;
            height: 250px;
            margin: 20px 0;
        }

        /* Resultado badge */
        .resultado-badge {
            display: inline-block;
            padding: 6px 18px;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: bold;
        }
        .resultado-deficit {
            background-color: #fde8ea;
            color: #dc3545;
            border: 1px solid #f5c6cb;
        }
        .resultado-superavit {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Rodapé fixo */
        .footer-container {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 4px 8mm;
            border-top: 1px solid #dee2e6;
            font-size: 0.6rem;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
            background: #fff;
        }

        /* Filtros badge */
        .filter-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.7rem;
            background-color: #e9ecef;
            color: #495057;
            margin-right: 4px;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>
    {{-- Cabeçalho padronizado --}}
    <div class="header-container">
        <div class="header-content">
            {{-- Logo esquerdo --}}
            <div class="header-logo">
                @php
                    $avatar = $avatarEmpresa ?? ($empresaRelatorio->avatar ?? ($company->avatar ?? null));
                    $logoPath = null;

                    if ($avatar) {
                        $paths = [storage_path('app/public/' . $avatar), storage_path($avatar)];

                        foreach ($paths as $path) {
                            if (file_exists($path)) {
                                $logoPath = $path;
                                break;
                            }
                        }
                    }

                    if (!$logoPath || !file_exists($logoPath)) {
                        $logoPath = public_path('tenancy/assets/media/png/perfil.svg');
                    }
                @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </div>

            {{-- Texto centralizado --}}
            <div class="header-text">
                <h4 style="margin: 0; padding: 0;">{{ strtoupper($nomeEmpresa ?? ($empresaRelatorio->name ?? ($company->name ?? ''))) }}</h4>
                <h5 style="margin: 5px 0; padding: 0; font-weight: normal;">
                    {{ strtoupper($razaoSocial ?? ($empresaRelatorio->razao_social ?? ($company->razao_social ?? ''))) }}
                </h5>
                <small>CNPJ: {{ $cnpjEmpresa ?? ($empresaRelatorio->cnpj ?? ($company->cnpj ?? '')) }}</small>
                <div style="font-size: 0.75rem; color: #333;">
                    @php
                        $addr = $enderecoEmpresa ?? ($empresaRelatorio->addresses ?? ($company->addresses ?? null));
                    @endphp
                    @if($addr)
                        {{ $addr->rua ?? '' }}
                        @if($addr->numero ?? '')
                            , {{ $addr->numero }}
                        @endif
                        @if($addr->bairro ?? '')
                            - {{ $addr->bairro }}
                        @endif
                        @if($addr->cidade ?? '')
                            / {{ $addr->cidade }}
                        @endif
                        @if($addr->uf ?? '')
                            - {{ $addr->uf }}
                        @endif
                        @if($addr->cep ?? '')
                            - CEP: {{ $addr->cep }}
                        @endif
                    @endif
                </div>
                @php
                    $emp = $empresaRelatorio ?? $company ?? null;
                @endphp
                @if($emp && (($emp->phone ?? null) || ($emp->website ?? null) || ($emp->email ?? null)))
                    <small>
                        @if($emp->phone ?? null)
                            Fone: {{ $emp->phone }}
                        @endif
                        @if($emp->website ?? null)
                            {{ $emp->phone ?? null ? ' - ' : '' }}Site: {{ $emp->website }}
                        @endif
                        @if($emp->email ?? null)
                            {{ ($emp->phone ?? null) || ($emp->website ?? null) ? ' - ' : '' }}E-mail: {{ $emp->email }}
                        @endif
                    </small>
                @endif
            </div>

            {{-- Logo direito --}}
            <div class="header-logo">
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </div>
        </div>
    </div>

    {{-- Título e Período --}}
    <p class="fw-bold mb-2 text-center" style="font-size: 0.85rem;">
        PRESTAÇÃO DE CONTAS &mdash; PERÍODO: {{ $dataInicial }} a {{ $dataFinal }}
    </p>

    {{-- Filtros aplicados --}}
    <div class="text-center mb-3">
        @isset($parceiroNome)
            <span class="filter-badge"><strong>Parceiro:</strong> {{ $parceiroNome }}</span>
        @endisset
        @if(!empty($comprovacaoFiscal))
            <span class="filter-badge"><strong>Filtro:</strong> Somente com comprovação fiscal</span>
        @endif
        @if(($tipoValor ?? 'previsto') === 'pago')
            <span class="filter-badge"><strong>Valores:</strong> Efetivos (Pagos)</span>
        @else
            <span class="filter-badge"><strong>Valores:</strong> Previstos</span>
        @endif
        <span class="filter-badge text-muted">
            <em>Excluídas: desconsideradas, parceladas e agendadas</em>
        </span>
    </div>

    {{-- Loop dos grupos --}}
    @foreach ($dados as $idx => $grupo)
        <div class="mb-3">
            <h6 class="text-primary fw-bold mb-1" style="font-size: 0.85rem; border-bottom: 2px solid #0d6efd; padding-bottom: 4px;">
                <i class="bi bi-folder2-open"></i> {{ $grupo['origem'] }}
            </h6>

            <table class="table table-sm table-bordered align-middle mb-2">
                <thead class="table-light">
                    <tr class="text-center" style="font-size: 0.7rem;">
                        <th style="width: 8%">Data</th>
                        <th style="width: 14%">Entidade</th>
                        <th style="width: 14%">Parceiro</th>
                        <th style="width: 28%">Descrição</th>
                        <th style="width: 12%" class="text-end header-entrada">Entrada (R$)</th>
                        <th style="width: 12%" class="text-end header-saida">Saída (R$)</th>
                        <th style="width: 12%" class="text-end">Saldo (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $saldo = 0;
                        $campoValor = ($tipoValor ?? 'previsto') === 'pago' ? 'valor_pago' : 'valor';
                    @endphp
                    @foreach ($grupo['items'] as $mov)
                        @php
                            $valorMov = $mov->{$campoValor} ?? $mov->valor;
                            $entrada  = $mov->tipo === 'entrada' ? $valorMov : 0;
                            $saida    = $mov->tipo === 'saida'   ? $valorMov : 0;
                            $saldo   += $entrada - $saida;
                        @endphp
                        <tr style="font-size: 0.7rem;">
                            <td class="text-center text-nowrap">{{ \Carbon\Carbon::parse($mov->data_competencia)->format('d/m/Y') }}</td>
                            <td>{{ $mov->entidadeFinanceira->name ?? '-' }}</td>
                            <td>{{ $mov->parceiro->nome ?? '-' }}</td>
                            <td>
                                {{ $mov->descricao }}
                                @if($mov->lancamentoPadrao?->description)
                                    <br><small class="text-muted">{{ $mov->lancamentoPadrao->description }}</small>
                                @endif
                            </td>
                            <td class="text-end {{ $entrada ? 'text-success fw-semibold' : '' }}">
                                {{ $entrada ? number_format($entrada, 2, ',', '.') : '' }}
                            </td>
                            <td class="text-end {{ $saida ? 'text-danger fw-semibold' : '' }}">
                                {{ $saida ? number_format($saida, 2, ',', '.') : '' }}
                            </td>
                            <td class="text-end fw-semibold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($saldo, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold" style="font-size: 0.75rem;">
                        <td colspan="4" class="text-end">Subtotal</td>
                        <td class="text-end total-entrada">{{ number_format($grupo['totEntrada'], 2, ',', '.') }}</td>
                        <td class="text-end total-saida">{{ number_format($grupo['totSaida'], 2, ',', '.') }}</td>
                        <td class="text-end {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($saldo, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Page-break opcional se muitos registros --}}
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- Totais finais --}}
    @php
        $resultado = $totalEntradas - $totalSaidas;
    @endphp
    <div class="mt-3 p-3 border border-dark-subtle rounded" style="background-color: #f8f9fa;">
        <h5 class="mb-3 text-center fw-bold">Resumo Geral</h5>
        <div class="row">
            <div class="col-4 text-center">
                <div class="p-2 rounded" style="background-color: #d4edda;">
                    <small class="d-block text-muted">Total de Entradas</small>
                    <strong class="text-success" style="font-size: 1.1rem;">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="p-2 rounded" style="background-color: #fde8ea;">
                    <small class="d-block text-muted">Total de Saídas</small>
                    <strong class="text-danger" style="font-size: 1.1rem;">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-4 text-center">
                <div class="p-2 rounded resultado-badge {{ $resultado >= 0 ? 'resultado-superavit' : 'resultado-deficit' }}" style="padding: 8px;">
                    <small class="d-block" style="opacity: 0.8;">{{ $resultado >= 0 ? 'Superávit' : 'Déficit' }}</small>
                    <strong style="font-size: 1.1rem;">R$ {{ number_format(abs($resultado), 2, ',', '.') }}</strong>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico (Chart.js) --}}
    @if(count($dados) > 0)
        <div class="page-break"></div>
        <div class="mt-3">
            <h6 class="text-center fw-bold mb-2">Entradas x Saídas por Origem</h6>
            <canvas id="chart" height="200"></canvas>
        </div>

        <script>
            var ctx = document.getElementById('chart').getContext('2d');
            var labels = @json(array_column($dados, 'origem'));
            var entradas = @json(array_column($dados, 'totEntrada'));
            var saidas = @json(array_column($dados, 'totSaida'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Entradas',
                            data: entradas,
                            backgroundColor: 'rgba(40, 167, 69, 0.8)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Saídas',
                            data: saidas,
                            backgroundColor: 'rgba(235, 18, 40, 0.8)',
                            borderColor: 'rgba(235, 18, 40, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var value = context.parsed.y || 0;
                                    return context.dataset.label + ': R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'R$' },
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 0});
                                }
                            }
                        }
                    }
                }
            });
        </script>

        {{-- Gráfico Pizza - Distribuição geral --}}
        <div class="mt-4">
            <h6 class="text-center fw-bold mb-2">Distribuição Geral</h6>
            <div class="d-flex justify-content-center">
                <canvas id="chartPie" width="300" height="300" style="max-width: 300px;"></canvas>
            </div>
        </div>

        <script>
            var ctxPie = document.getElementById('chartPie').getContext('2d');
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Entradas', 'Saídas'],
                    datasets: [{
                        data: [{{ $totalEntradas }}, {{ $totalSaidas }}],
                        backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(235, 18, 40, 0.8)'],
                        borderColor: ['rgba(40, 167, 69, 1)', 'rgba(235, 18, 40, 1)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var value = context.parsed || 0;
                                    var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                    var pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return context.label + ': R$ ' + value.toLocaleString('pt-BR', {minimumFractionDigits: 2}) + ' (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endif

    {{-- Rodapé fixo --}}
    <div class="footer-container">
        <span>Prestação de Contas &mdash; Gerado em {{ now()->format('d/m/Y H:i') }}</span>
        <span>{{ $nomeEmpresa ?? ($empresaRelatorio->name ?? ($company->name ?? '')) }}</span>
    </div>
</body>
</html>
