<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Boletim Financeiro</title>

    {{-- Bootstrap 5 – CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous">

    {{-- Chart.js para gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- CSS próprio pensado para impressão --}}
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 8mm 10mm 8mm;
        }

        body {
            font-size: .75rem;
        }

        .logo {
            height: 60px;
        }

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
        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-footer-group;
        }

        /* Cores das colunas */
        .col-entrada {
            background-color: #009c24 !important;
        }

        .col-saida {
            background-color: #c02431 !important;
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

        /* Cabeçalhos das tabelas */
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

        /* Gráficos */
        .chart-container {
            position: relative;
            height: 250px;
            margin: 20px 0;
        }

        /* Rodapé */
        @page {
            @bottom-left {
                content: "Gerado em: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}";
                font-size: 0.65rem;
                color: #6c757d;
            }

            @bottom-center {
                content: "Sistema Dominus - www.dominus.li";
                font-size: 0.65rem;
                color: #6c757d;
            }

            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 0.65rem;
                color: #6c757d;
            }
        }
    </style>
</head>

<body>
    {{-- Cabeçalho --}}
    <div class="header-container">
        <div class="header-content">
            {{-- Logo esquerdo --}}
            <div class="header-logo">
                @php
                    $logoPath = $company->avatar
                        ? storage_path('app/public/' . $company->avatar)
                        : public_path('assets/media/png/perfil.svg');
                @endphp
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </div>

            {{-- Texto centralizado --}}
            <div class="header-text">
                <h4>{{ strtoupper($company->name) }}</h4>
                <h4>{{ strtoupper($company->razao_social) }}</h4>

                <div class="subtitle">BOLETIM FINANCEIRO</div>
                <small>CNPJ: {{ $company->cnpj ?? '' }}</small>
                <small>
                    @if ($company->addresses->rua ?? '')
                        {{ $company->addresses->rua }}
                        @if ($company->addresses->numero ?? '')
                            , {{ $company->addresses->numero }}
                        @endif
                        @if ($company->addresses->bairro ?? '')
                            - {{ $company->addresses->bairro }}
                        @endif
                        / {{ $company->addresses->cidade ?? '' }}-{{ $company->addresses->uf ?? '' }}
                    @endif
                </small>
                @if ($company->phone || $company->website || $company->email)
                    <small>
                        @if ($company->phone)
                            Fone: {{ $company->phone }}
                        @endif
                        @if ($company->website)
                            {{ $company->phone ? ' - ' : '' }}Site: {{ $company->website }}
                        @endif
                        @if ($company->email)
                            {{ $company->phone || $company->website ? ' - ' : '' }}E-mail: {{ $company->email }}
                        @endif
                    </small>
                @endif
            </div>

            {{-- Logo direito --}}
            <div class="header-logo">
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </div>
        </div>
    </div>

    {{-- Período --}}
    <p class="fw-bold mb-3 text-center">PERÍODO: {{ $dataInicial }} a {{ $dataFinal }}</p>

    {{-- 1. PRESTAÇÃO DE CONTAS --}}
    <h5 class="text-center fw-bold mb-3">PRESTAÇÃO DE CONTAS</h5>

    <div class="row">
        {{-- Coluna ENTRADAS --}}
        <div class="col-6">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="text-center ">
                        <th class="col-entrada text-white" colspan="3">ENTRADAS</th>
                    </tr>
                    <tr>
                        <th style="width: 15%">CÓD</th>
                        <th style="width: 60%">DESCRIÇÃO</th>
                        <th style="width: 25%" class="text-end">VALOR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lancamentosEntradas as $lancamento)
                        <tr>
                            <td class="text-center">{{ $lancamento['codigo'] }}</td>
                            <td>{{ $lancamento['descricao'] }}</td>
                            <td class="text-end">{{ number_format($lancamento['valor'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Nenhuma entrada no período</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-entrada">
                        <td colspan="2" class="text-end">TOTAL DAS ENTRADAS</td>
                        <td class="text-end">{{ number_format($totalEntradas, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Coluna SAÍDAS --}}
        <div class="col-6">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr class="text-center header-saida">
                        <th class="col-saida text-white" colspan="3">SAÍDAS</th>
                    </tr>
                    <tr>
                        <th style="width: 15%">CÓD</th>
                        <th style="width: 60%">DESCRIÇÃO</th>
                        <th style="width: 25%" class="text-end">VALOR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lancamentosSaidas as $lancamento)
                        <tr>
                            <td class="text-center">{{ $lancamento['codigo'] }}</td>
                            <td>{{ $lancamento['descricao'] }}</td>
                            <td class="text-end">{{ number_format($lancamento['valor'], 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Nenhuma saída no período</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="total-saida">
                        <td colspan="2" class="text-end">TOTAL DAS SAÍDAS</td>
                        <td class="text-end">{{ number_format($totalSaidas, 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 2. RESULTADO DAS CONTAS DE MOVIMENTO FINANCEIRO --}}
    <h5 class="text-center fw-bold mb-3 mt-4">RESULTADO DAS CONTAS DE MOVIMENTO FINANCEIRO</h5>

    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr class="text-center">
                <th>CONTA</th>
                <th>SALDO ANTERIOR</th>
                <th class="col-entrada text-white">ENTRADA</th>
                <th class="col-saida text-white">SAÍDA</th>
                <th>SALDO ATUAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse($contasMovimento as $conta)
                <tr>
                    <td>{{ $conta['conta'] }}</td>
                    <td class="text-end">{{ number_format($conta['saldo_anterior'], 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($conta['entrada'], 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($conta['saida'], 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($conta['saldo_atual'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Nenhuma conta financeira cadastrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 3. RESUMO GERAL --}}
    <h5 class="text-center fw-bold mb-3 mt-4">RESUMO GERAL</h5>

    <div class="row">
        <div class="col-6">
            <h6 class="text-center">Resultado do período</h6>
            <div class="chart-container">
                <canvas id="chartResultado"></canvas>
            </div>
        </div>
        <div class="col-6">
            <h6 class="text-center">Evolução do saldo</h6>
            <div class="chart-container">
                <canvas id="chartEvolucao"></canvas>
            </div>
        </div>
    </div>

    {{-- 4. TOTAIS FINAIS --}}
    <table class="table table-bordered mt-4">
        <thead class="table-light">
            <tr class="text-center fw-bold">
                <th>SALDO ANTERIOR</th>
                <th>ENTRADAS</th>
                <th>SAÍDAS</th>
                <th>SALDO ATUAL</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center">
                <td>{{ number_format($saldoAnteriorTotal, 2, ',', '.') }}</td>
                <td>{{ number_format($totalEntradas, 2, ',', '.') }}</td>
                <td>{{ number_format($totalSaidas, 2, ',', '.') }}</td>
                <td>{{ number_format($saldoAtualTotal, 2, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if ($deficit < 0)
        <h4 class="text-center text-danger fw-bold mt-3">(-) DÉFICIT {{ number_format(abs($deficit), 2, ',', '.') }}
        </h4>
    @else
        <h4 class="text-center text-success fw-bold mt-3">(+) SUPERÁVIT {{ number_format($deficit, 2, ',', '.') }}</h4>
    @endif


    {{-- JavaScript para gráficos --}}
    <script>
        // Gráfico 1: Resultado do Período
        const ctxResultado = document.getElementById('chartResultado').getContext('2d');
        new Chart(ctxResultado, {
            type: 'bar',
            data: {
                labels: ['Entradas', 'Saídas'],
                datasets: [{
                    data: [{{ $totalEntradas }}, {{ $totalSaidas }}],
                    backgroundColor: ['#28a745', '#dc3545'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico 2: Evolução do Saldo
        const ctxEvolucao = document.getElementById('chartEvolucao').getContext('2d');
        new Chart(ctxEvolucao, {
            type: 'bar',
            data: {
                labels: ['Saldo anterior', 'Saldo atual'],
                datasets: [{
                    data: [{{ $saldoAnteriorTotal }}, {{ $saldoAtualTotal }}],
                    backgroundColor: ['#17a2b8', '#007bff'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>
