<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Prestação de Contas</title>

    {{-- Bootstrap 5 – CDN (Browsershot carrega normalmente) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        integrity="sha384-TwkQ…"
        crossorigin="anonymous">

    {{-- CSS próprio pensado para impressão --}}
    <style>
        @page { size: A4 landscape; margin: 8mm 8mm 15mm 8mm; }
        body   { font-size: .72rem; }
        .logo  { height: 60px; }
        .page-break { page-break-after: always; }
        /* zebra na tabela */
        table tbody tr:nth-child(odd) { background: #f8f9fa; }

        /* Titulo do relatorio */
        .report-title {
            background: linear-gradient(135deg, #4a90d9 0%, #667eea 100%);
            color: #fff;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .report-title h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
        .report-title .periodo { font-size: .85rem; opacity: .95; }

        /* Filtros aplicados */
        .filtros-box {
            border: 1px solid #dee2e6;
            border-left: 4px solid #4a90d9;
            border-radius: 0 6px 6px 0;
            padding: 10px 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }
        .filtros-box .filtro-item { display: inline-block; margin-right: 20px; font-size: .72rem; }
        .filtros-box .filtro-label { font-weight: 600; color: #495057; }

        /* Tabelas melhoradas */
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        .table> thead> tr> th { font-size: .7rem; padding: 6px 5px; white-space: nowrap; background-color: #495057; color: #fff; border: none; }
        .table> tbody> tr> td { font-size: .68rem; padding: 4px 5px; vertical-align: middle; }

        /* Destaque de valores */
        .text-entrada { color: #198754; font-weight: 600; }
        .text-saida { color: #dc3545; font-weight: 600; }
        .saldo-positivo { color: #198754; font-weight: 700; }
        .saldo-negativo { color: #dc3545; font-weight: 700; }

        /* Badge de origem/categoria */
        .origem-badge {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #4a90d9 100%);
            color: #fff;
            border-radius: 5px;
            padding: 6px 14px;
            font-size: .78rem;
            font-weight: 600;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
        }

        /* Resumo final */
        .resumo-container { display: flex; gap: 15px; margin-top: 20px; margin-bottom: 15px; }
        .resumo-box { flex: 1; border-radius: 8px; padding: 15px 20px; text-align: center; }
        .resumo-box.entradas { background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); border: 1px solid #28a745; }
        .resumo-box.saidas { background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); border: 1px solid #dc3545; }
        .resumo-box.saldo { background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%); border: 1px solid #007bff; }
        .resumo-box .label { font-size: .7rem; color: #495057; margin-bottom: 5px; text-transform: uppercase; }
        .resumo-box .value { font-size: 1.1rem; font-weight: 700; }
        .resumo-box.entradas .value { color: #155724; }
        .resumo-box.saidas .value { color: #721c24; }
        .resumo-box.saldo .value { color: #004085; }
        .subtotal-row { background-color: #e9ecef !important; }
        .subtotal-row td { font-weight: 600 !important; }
        .chart-container { margin-top: 20px; padding: 15px; border: 1px solid #dee2e6; border-radius: 8px; background: #fff; }
        .chart-title { font-size: .85rem; font-weight: 600; color: #495057; margin-bottom: 10px; text-align: center; }
        .footer-container { position: fixed; bottom: 0; left: 0; right: 0; padding: 5px 20px; font-size: .6rem; color: #999; border-top: 1px solid #ccc; background: #fff; }

        /* Estilo do cabeçalho similar à imagem */
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
    </style>
</head>

<body>
    {{-- Cabecalho padrao --}}
    <div class="header-container">
        <div class="header-content">
            {{-- Logo esquerdo --}}
            <div class="header-logo">
                @php
                    $avatar = $company->avatar ?? null;
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
                @if (file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo">
                @endif
            </div>

            {{-- Texto centralizado --}}
            <div class="header-text">
                <h4 style="margin: 0; padding: 0;">{{ strtoupper($company->name ?? '') }}</h4>
                <h5 style="margin: 5px 0; padding: 0; font-weight: normal;">
                    {{ strtoupper($company->razao_social ?? '') }}
                </h5>
                <small>CNPJ: {{ $company->cnpj ?? '' }}</small>
                <div style="font-size: 0.75rem; color: #333;">
                    @php
                        $addr = $company->addresses ?? null;
                    @endphp
                    @if ($addr)
                        {{ $addr->rua ?? '' }}
                        @if ($addr->numero ?? '')
                            , {{ $addr->numero }}
                        @endif
                        @if ($addr->bairro ?? '')
                            - {{ $addr->bairro }}
                        @endif
                        @if ($addr->cidade ?? '')
                            / {{ $addr->cidade }}
                        @endif
                        @if ($addr->uf ?? '')
                            - {{ $addr->uf }}
                        @endif
                        @if ($addr->cep ?? '')
                            - CEP: {{ $addr->cep }}
                        @endif
                    @endif
                </div>
                @if (($company->phone ?? null) || ($company->website ?? null) || ($company->email ?? null))
                    <small>
                        @if ($company->phone ?? null)
                            Fone: {{ $company->phone }}
                        @endif
                        @if ($company->website ?? null)
                            {{ $company->phone ?? null ? ' - ' : '' }}Site: {{ $company->website }}
                        @endif
                        @if ($company->email ?? null)
                            {{ ($company->phone ?? null) || ($company->website ?? null) ? ' - ' : '' }}E-mail: {{ $company->email }}
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

    {{-- Filtros --}}
    <div class="report-title">
        <h3>Prestação de Contas</h3>
        <span class="periodo">Período: {{ $dataInicial }} a {{ $dataFinal }}</span>
    </div>

    <div class="filtros-box">
        @isset($parceiroNome)
            <span class="filtro-item">
                <span class="filtro-label">Parceiro:</span> {{ $parceiroNome }}
            </span>
        @endisset
        @if(!empty($comprovacaoFiscal))
            <span class="filtro-item">
                <span class="filtro-label">Filtro:</span> Somente com comprovação fiscal
            </span>
        @endif
        @if(($tipoValor ?? 'previsto') === 'pago')
            <span class="filtro-item">
                <span class="filtro-label">Valores:</span> Efetivos (Pagos)
            </span>
        @else
            <span class="filtro-item">
                <span class="filtro-label">Valores:</span> Previstos
            </span>
        @endif
        @if (empty($parceiroNome) && empty($comprovacaoFiscal))
            <span class="filtro-item text-muted">Nenhum filtro adicional aplicado</span>
        @endif
    </div>

    {{-- Loop dos grupos --}}
    @foreach ($dados as $idx => $grupo)
        <div class="origem-badge">{{ $grupo['origem'] }}</div>

        <table class="table table-sm table-bordered table-striped align-middle mb-3">
            <thead>
                <tr class="text-center">
                    <th style="width: 80px;">Data</th>
                    <th style="width: 130px;">Entidade</th>
                    <th style="width: 150px;">Parceiro</th>
                    <th>Descrição</th>
                    <th style="width: 100px;" class="text-end">Entrada (R$)</th>
                    <th style="width: 100px;" class="text-end">Saída (R$)</th>
                    <th style="width: 100px;" class="text-end">Saldo</th>
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
                    <tr>
                        <td class="text-center">{{ $mov->data_competencia }}</td>
                        <td>{{ $mov->entidadeFinanceira->name ?? '-' }}</td>
                        <td>{{ $mov->parceiro->nome ?? '-' }}</td>
                        <td>
                            {{ $mov->descricao }}
                            @if ($mov->lancamentoPadrao)
                                <br><small class="text-muted">{{ $mov->lancamentoPadrao->description }}</small>
                            @endif
                        </td>
                        <td class="text-end {{ $entrada ? 'text-entrada' : '' }}">{{ $entrada ? number_format($entrada, 2, ',', '.') : '-' }}</td>
                        <td class="text-end {{ $saida ? 'text-saida' : '' }}">{{ $saida ? number_format($saida, 2, ',', '.') : '-' }}</td>
                        <td class="text-end {{ $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo' }}">{{ number_format($saldo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="subtotal-row">
                    <td colspan="4" class="text-end"><strong>Subtotal {{ $grupo['origem'] }}</strong></td>
                    <td class="text-end text-entrada">{{ number_format($grupo['totEntrada'], 2, ',', '.') }}</td>
                    <td class="text-end text-saida">{{ number_format($grupo['totSaida'],   2, ',', '.') }}</td>
                    <td class="text-end {{ $saldo >= 0 ? 'saldo-positivo' : 'saldo-negativo' }}">{{ number_format($saldo, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Page-break opcional se muitos registros --}}
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- Totais finais --}}
    @php
        $saldoFinal = $totalEntradas - $totalSaidas;
    @endphp
    <div class="resumo-container">
        <div class="resumo-box entradas">
            <div class="label">Total de Entradas</div>
            <div class="value">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</div>
        </div>
        <div class="resumo-box saidas">
            <div class="label">Total de Saídas</div>
            <div class="value">R$ {{ number_format($totalSaidas, 2, ',', '.') }}</div>
        </div>
        <div class="resumo-box saldo">
            <div class="label">Saldo Final</div>
            <div class="value">R$ {{ number_format($saldoFinal, 2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Gráfico (Chart.js) – Browsershot renderiza sem problemas --}}
    @if (count($dados) > 0)
        <div class="chart-container">
            <div class="chart-title">Comparativo de Entradas x Saídas por Categoria</div>
            <canvas id="chart" height="140"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        {
                            label: 'Entradas',
                            data: entradas,
                            backgroundColor: 'rgba(25, 135, 84, 0.8)',
                            borderColor: 'rgba(25, 135, 84, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Saídas',
                            data: saidas,
                            backgroundColor: 'rgba(220, 53, 69, 0.8)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, padding: 15 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Valores (R$)' },
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            }
                        }
                    }
                }
            });
        </script>
    @endif

    {{-- Footer --}}
    <div class="footer-container">
        <span>Relatório gerado em {{ now()->format('d/m/Y H:i:s') }} | Sistema Dominus</span>
    </div>
</body>
</html>
