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
        @page { size: A4 landscape; margin: 8mm 8mm 10mm 8mm; }
        body   { font-size: .77rem; }
        .logo  { height: 60px; }
        .page-break { page-break-after: always; }
        /* zebra na tabela */
        table tbody tr:nth-child(odd) { background: #f8f9fa; }

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
    <p class="fw-bold mb-1">Período: {{ $dataInicial }} a {{ $dataFinal }}</p>
    @isset($parceiroNome)
        <p class="mb-1"><strong>Parceiro:</strong> {{ $parceiroNome }}</p>
    @endisset
    @if(!empty($comprovacaoFiscal))
        <p class="mb-1"><strong>Filtro:</strong> Somente com comprovação fiscal</p>
    @endif
    @if(($tipoValor ?? 'previsto') === 'pago')
        <p class="mb-1"><strong>Valores:</strong> Efetivos (Pagos)</p>
    @endif

    {{-- Loop dos grupos --}}
    @foreach ($dados as $idx => $grupo)
        <h5 class="text-primary">{{ $grupo['origem'] }}</h5>

        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Data</th>
                    <th>Entidade</th>
                    <th>Parceiro</th>
                    <th>Descrição</th>
                    <th class="text-end">Entrada (R$)</th>
                    <th class="text-end">Saída (R$)</th>
                    <th class="text-end">Saldo</th>
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
                        <td>{{ $mov->entidadeFinanceira->name }}</td>
                        <td>{{ $mov->parceiro->nome ?? '-' }}</td>
                        <td>
                            {{ $mov->descricao }}<br>
                            <small class="text-muted">{{ $mov->lancamentoPadrao->description }}</small>
                        </td>
                        <td class="text-end">{{ $entrada ? number_format($entrada, 2, ',', '.') : '' }}</td>
                        <td class="text-end">{{ $saida   ? number_format($saida,   2, ',', '.') : '' }}</td>
                        <td class="text-end">{{ number_format($saldo, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr class="fw-semibold">
                    <td colspan="4" class="text-end">Subtotal</td>
                    <td class="text-end">{{ number_format($grupo['totEntrada'], 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($grupo['totSaida'],   2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($saldo,             2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Page-break opcional se muitos registros --}}
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- Totais finais --}}
    <div class="mt-2 p-2 border border-dark-subtle rounded">
        <h5 class="mb-1">Totais gerais</h5>
        <div class="row">
            <div class="col-6"><strong>Total de Entradas:</strong></div>
            <div class="col-6 text-end">R$ {{ number_format($totalEntradas, 2, ',', '.') }}</div>
            <div class="col-6"><strong>Total de Saídas:</strong></div>
            <div class="col-6 text-end">R$ {{ number_format($totalSaidas,   2, ',', '.') }}</div>
        </div>
    </div>

    {{-- Gráfico (Chart.js) – Browsershot renderiza sem problemas --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <canvas id="chart" class="mt-3" height="160"></canvas>

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
                scales:  { y: { beginAtZero: true, title: { text: 'R$' }}}
            }
        });
    </script>
</body>
</html>
