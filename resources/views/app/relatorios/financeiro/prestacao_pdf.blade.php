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
    </style>
</head>

<body>
    {{-- Cabeçalho --}}
    <div class="text-center mb-2">
        <img class="logo mb-1"
             src="{{ public_path($company->avatar ?? 'assets/media/png/perfil.svg') }}"
             alt="Logo">
        <h4 class="mb-0">{{ $company->name }}</h4>
        <small>CNPJ: {{ $company->cnpj }}</small><br>
        <small>{{ $company->addresses->rua ?? '' }} –
               {{ $company->addresses->cidade ?? '' }}/{{ $company->addresses->uf ?? '' }}</small><br>
        <small>Fone: {{ $company->phone }} – E-mail: {{ $company->email }}</small>
        <hr>
    </div>

    {{-- Filtros --}}
    <p class="fw-bold mb-1">Período: {{ $dataInicial }} a {{ $dataFinal }}</p>
    @isset($costCenter)
        <p class="mb-2"><strong>Centro de custo:</strong> {{ $costCenter }}</p>
    @endisset

    {{-- Loop dos grupos --}}
    @foreach ($dados as $idx => $grupo)
        <h5 class="text-primary">{{ $grupo['origem'] }}</h5>

        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Data</th>
                    <th>Entidade</th>
                    <th>Descrição</th>
                    <th class="text-end">Entrada (R$)</th>
                    <th class="text-end">Saída (R$)</th>
                    <th class="text-end">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php $saldo = 0; @endphp
                @foreach ($grupo['items'] as $mov)
                    @php
                        $entrada = $mov->tipo === 'entrada' ? $mov->valor : 0;
                        $saida   = $mov->tipo === 'saida'   ? $mov->valor : 0;
                        $saldo  += $entrada - $saida;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $mov->data_competencia }}</td>
                        <td>{{ $mov->entidadeFinanceira->name }}</td>
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
                    <td colspan="3" class="text-end">Subtotal</td>
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
