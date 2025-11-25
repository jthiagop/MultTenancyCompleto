<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Transações Bancárias</title>

    {{-- CSS puro para impressão --}}
    <style>
        @page {
            size: A4 {{ (isset($orientacao) && $orientacao === 'horizontal') ? 'landscape' : 'portrait' }};
            margin: 8mm 8mm 25mm 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            padding-bottom: 25mm;
            background: #fff;
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
            width: auto;
            height: auto;
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
            letter-spacing: 0.5px;
        }

        .header-text .subtitle {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-top: 3px;
            line-height: 1.2;
        }

        .header-text small {
            display: block;
            font-size: 0.7rem;
            line-height: 1.5;
            margin-top: 3px;
        }

        /* Informações do Relatório */
        .report-info {
            margin-bottom: 20px;
        }

        .report-info h5 {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .report-info p {
            margin-bottom: 5px;
            font-size: 11px;
        }

        .report-info strong {
            font-weight: bold;
        }

        /* Seções por Origem */
        .section-title {
            color: #0066cc;
            font-weight: bold;
            font-size: 13px;
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .saldo-anterior {
            margin-bottom: 10px;
            font-size: 11px;
        }

        .saldo-anterior strong {
            font-weight: bold;
        }

        /* Tabelas */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }

        table thead th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            border: 1px solid #999;
            padding: 8px;
            font-size: 11px;
        }

        table tbody td {
            border: 1px solid #999;
            padding: 6px 8px;
            background-color: #fff;
            white-space: nowrap;
        }

        table tbody tr:nth-child(even) {
            background-color: #fff;
        }

        table tfoot td {
            background-color: #f0f0f0;
            font-weight: bold;
            border: 1px solid #999;
            padding: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-muted {
            color: #666;
            font-size: 10px;
        }

        /* Totais Gerais */
        .totais-gerais {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #999;
            border-radius: 4px;
        }

        .totais-gerais h5 {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .totais-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .totais-col {
            display: table-cell;
            width: 50%;
        }

        .totais-col-right {
            text-align: right;
        }

        /* Gráfico */
        .chart-container {
            margin-top: 20px;
        }

        #chart {
            max-height: 200px;
        }

        /* Rodapé fixo */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20mm;
            display: table;
            width: 100%;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #999;
            padding: 8px 10px;
            background-color: #fff;
        }

        .footer-left {
            display: table-cell;
            text-align: left;
            width: 33.33%;
            vertical-align: middle;
        }

        .footer-center {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            vertical-align: middle;
        }

        .footer-right {
            display: table-cell;
            text-align: right;
            width: 33.33%;
            vertical-align: middle;
        }

        /* Alertas */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #999;
            border-radius: 4px;
            background-color: #e7f3ff;
        }

        .alert p {
            margin: 0;
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
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}"
                         alt="Logo"
                         style="width: 100%; height: auto; max-height: 100px;">
                @endif
            </div>

            {{-- Texto centralizado --}}
            <div class="header-text">
                <h4>{{ strtoupper($company->name) }}</h4>
                <div class="subtitle">{{ strtoupper($company->name) }}</div>
                <small>CNPJ: {{ $company->cnpj ?? '' }}</small>
                <small>
                    @if($company->addresses->rua ?? '')
                        {{ $company->addresses->rua }}
                        @if($company->addresses->numero ?? '')
                            , {{ $company->addresses->numero }}
                        @endif
                        @if($company->addresses->bairro ?? '')
                            - {{ $company->addresses->bairro }}
                        @endif
                        / {{ $company->addresses->cidade ?? '' }}-{{ $company->addresses->uf ?? '' }}
                    @endif
                </small>
                <small>
                    Fone: {{ $company->phone ?? '' }}
                    @if($company->website ?? '')
                        - Site: {{ $company->website }}
                    @else
                        - Site: -
                    @endif
                    - E-mail: {{ $company->email ?? '' }}
                </small>
            </div>

            {{-- Logo direito --}}
            <div class="header-logo">
                @php
                    $logoPath = $company->avatar
                        ? storage_path('app/public/' . $company->avatar)
                        : public_path('assets/media/png/perfil.svg');
                @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}"
                         alt="Logo"
                         style="width: 100%; height: auto; max-height: 100px;">
                @endif
            </div>
        </div>
    </div>

    {{-- Informações do Relatório --}}
    <div class="report-info">
        <h5>Relatório de Transações Bancárias</h5>
        <p>
            <strong>Entidade(s) Financeira(s):</strong>
            @if(isset($entidades) && $entidades->count() > 0)
                {{ $entidades->pluck('nome')->implode(', ') }}
            @elseif(isset($entidade))
                {{ $entidade->nome }}
            @else
                Todas
            @endif
        </p>
        <p><strong>Período:</strong> {{ $dataInicial }} a {{ $dataFinal }}</p>
        @if($costCenter)
            <p><strong>Centro de Custo:</strong> {{ $costCenter->name }}</p>
        @endif
        @if($tipo !== 'ambos')
            <p><strong>Tipo:</strong> {{ ucfirst($tipo) }}</p>
        @endif
    </div>

    {{-- Loop dos grupos --}}
    @if(count($dados) > 0)
        @foreach ($dados as $idx => $grupo)
            @php
                // Saldo anterior já calculado no controller
                $saldoAnterior = $grupo['saldoAnterior'] ?? 0;
                // Calcular saldo acumulado no período
                $saldoAcumulado = $saldoAnterior;
            @endphp

            <div class="section-title">{{ $grupo['origem'] }}</div>

            <div class="saldo-anterior">
                <strong>Saldo Anterior:</strong> R$ {{ number_format($saldoAnterior, 2, ',', '.') }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Lançamento Padrão</th>
                        @if($costCenter)
                            <th>Centro de Custo</th>
                        @endif
                        <th class="text-end">Entrada (R$)</th>
                        <th class="text-end">Saída (R$)</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupo['items'] as $mov)
                        @php
                            $entrada = $mov->tipo === 'entrada' ? $mov->valor : 0;
                            $saida   = $mov->tipo === 'saida'   ? $mov->valor : 0;
                            $saldoAcumulado += $entrada - $saida;
                        @endphp
                        <tr>
                            <td class="text-center">{{ \Carbon\Carbon::parse($mov->data_competencia)->format('d/m/Y') }}</td>
                            <td>
                                <span class="text-muted">{{ $mov->lancamentoPadrao->description ?? '-' }}</span>
                            </td>
                            @if($costCenter)
                                <td>{{ $mov->costCenter->name ?? '-' }}</td>
                            @endif
                            <td class="text-end">{{ $entrada ? number_format($entrada, 2, ',', '.') : '' }}</td>
                            <td class="text-end">{{ $saida   ? number_format($saida,   2, ',', '.') : '' }}</td>
                            <td class="text-end">{{ number_format($saldoAcumulado, 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="{{ $costCenter ? '3' : '2' }}" class="text-end"><strong>Subtotal</strong></td>
                        <td class="text-end"><strong>{{ number_format($grupo['totEntrada'], 2, ',', '.') }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($grupo['totSaida'],   2, ',', '.') }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($saldoAcumulado, 2, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        @endforeach
    @else
        <div class="alert">
            <p>Nenhuma transação encontrada para os filtros selecionados.</p>
        </div>
    @endif

    {{-- Totais finais --}}
    @if(count($dados) > 0)
        <div class="totais-gerais">
            <h5>Totais gerais</h5>
            <div class="totais-row">
                <div class="totais-col"><strong>Total de Entradas:</strong></div>
                <div class="totais-col totais-col-right">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</div>
            </div>
            <div class="totais-row">
                <div class="totais-col"><strong>Total de Saídas:</strong></div>
                <div class="totais-col totais-col-right">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</div>
            </div>
            <div class="totais-row">
                <div class="totais-col"><strong>Saldo:</strong></div>
                <div class="totais-col totais-col-right"><strong>R$ {{ number_format($totalEntradas - $totalSaidas, 2, ',', '.') }}</strong></div>
            </div>
        </div>

        {{-- Gráfico (Chart.js) --}}
        <div class="chart-container">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <canvas id="chart" height="160"></canvas>

            <script>
                const ctx      = document.getElementById('chart').getContext('2d');
                const labels   = @json(array_column($dados, 'origem'));
                const entradas = @json(array_column($dados, 'totEntrada'));
                const saidas   = @json(array_column($dados, 'totSaida'));

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Entradas', data: entradas, backgroundColor: 'rgba(25,135,84,.7)' },
                            { label: 'Saídas',   data: saidas,   backgroundColor: 'rgba(220,53,69,.7)' }
                        ]
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' }},
                        scales:  { y: { beginAtZero: true, title: { display: true, text: 'R$' }}}
                    }
                });
            </script>
        </div>
    @endif

    {{-- Rodapé fixo em cada página --}}
    <div class="footer">
        <div class="footer-left">
            {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <div class="footer-center">
            Relatório gerado pelo Sistema Dominus
        </div>
        <div class="footer-right">
            Página <span class="page-number">1</span> / <span class="total-pages">1</span>
        </div>
    </div>

    <script>
        // Tentativa de contar páginas usando CSS e JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const pageNumbers = document.querySelectorAll('.page-number');
            const totalPages = document.querySelectorAll('.total-pages');

            // Se houver quebras de página (page-break), podemos tentar contar
            const pageBreaks = document.querySelectorAll('.page-break');
            const estimatedPages = pageBreaks.length + 1;

            if (estimatedPages > 1) {
                totalPages.forEach(el => el.textContent = estimatedPages);
            }
        });
    </script>
</body>
</html>
