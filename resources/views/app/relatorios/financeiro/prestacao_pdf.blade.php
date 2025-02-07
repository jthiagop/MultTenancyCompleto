<head>
    <meta charset="UTF-8">
    <title>Relatório de Prestação de Contas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Estilos básicos para tabela e layout */
        body {
            font-family: Arial, sans-serif;
            margin: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            font-size: 12px;
            padding: 5px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }

        .header p {
            margin: 3px 0;
            font-size: 13px;
        }

        .subtotal,
        .saldo {
            text-align: right;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }

        .total {
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <hr />
        {{-- @if (!empty($company->avatar))
            <img class="logo" src="{{ public_path('storage/' . $company->avatar) }}" alt="Logo">
        @else
            <img class="logo" src="/assets/media/png/perfil.svg" alt="Logo">
        @endif --}}
        <h2>{{ $company->name }}</h2>
        <p>CNPJ: {{ $company->cnpj }}</p>
        <p>{{ $company->addresses->rua ?? '' }}, {{ $company->addresses->bairro ?? '' }} -
            {{ $company->addresses->cidade ?? '' }}/{{ $company->addresses->uf ?? '' }}</p>
        <p>Fone: {{ $company->addresses->bairro ?? '' }} - E-mail: {{ $company->email }}</p>
        <hr>
    </div>
    @if ($costCenter)
        <p>Centro de Custo: {{ $costCenter }}</p>
    @endif

    <!-- Título do relatório e período -->
    <div class="mb-4 text-center">
        <h2 class="h5">RELATÓRIO DE PRESTAÇÃO DE CONTAS</h2>
        <p class="m-0"><strong>Período:</strong> {{ $dataInicial }} - {{ $dataFinal }}</p>
    </div>

    <!-- Centro de Custo, se houver -->
    @if ($costCenter)
        <p><strong>Centro de Custo:</strong> {{ $costCenter }}</p>
    @endif

    <!-- Loop das categorias / tipos de documentos -->
    @foreach ($dados as $categoria)
        <h3 class="h6 mb-3">
            {{-- Conta Contábil / Tipo Documento: {{ $categoria['movimentacoes'] }} --}}
        </h3>

        <!-- Tabela de movimentações -->
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>DATA</th>
                    <th>ENTIDADE</th>
                    <th>DESCRIÇÃO</th>
                    <th class="text-right">ENTRADA</th>
                    <th class="text-right">SAÍDA</th>
                    <th class="text-right">SALDO</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumEntrada = 0;
                    $sumSaida = 0;
                    $saldoAcumulado = 0;
                @endphp

                @foreach ($categoria['movimentacoes'] as $mov)
                    @php
                        // Se for entrada, soma no sumEntrada; se for saída, soma no sumSaida.
                        $entrada = 0;
                        $saida = 0;

                        if ($mov->tipo === 'entrada') {
                            $entrada = $mov->valor;
                            $sumEntrada += $entrada;
                        } elseif ($mov->tipo === 'saida') {
                            $saida = $mov->valor;
                            $sumSaida += $saida;
                        }

                        // Atualiza saldo acumulado
                        $saldoAcumulado = $saldoAcumulado + ($entrada - $saida);
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($mov->data_competencia)->format('d/m/Y') }}</td>
                        <td>{{ $mov->entidadeFinanceira->name }}</td>
                        <td>{{ $mov->descricao }} <br> <b> {{ $mov->lancamentoPadrao->description }}</b></td>
                        <td class="text-right">
                            {{ $entrada > 0 ? number_format($entrada, 2, ',', '.') : '' }}
                        </td>
                        <td class="text-right">
                            {{ $saida > 0 ? number_format($saida, 2, ',', '.') : '' }}
                        </td>
                        <td class="text-right">
                            {{ number_format($saldoAcumulado, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <!-- Linha do rodapé em cinza claro, exibindo totais -->
                <tr style="background-color: #f2f2f2;">
                    <th scope="row" colspan="3">Total</th>
                    <td class="text-right">
                        {{ number_format($sumEntrada, 2, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($sumSaida, 2, ',', '.') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($saldoAcumulado, 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>


        <!-- Totais da categoria -->
        <p>
            <strong>Total Entradas:</strong>
            R$ {{ number_format($categoria['total_entrada'], 2, ',', '.') }}
        </p>
        <p>
            <strong>Total Saídas:</strong>
            R$ {{ number_format($categoria['total_saida'], 2, ',', '.') }}
        </p>

        <hr>
    @endforeach
    </div>

    <h2>Relatório de Entradas e Saídas por Origem</h2>

    @foreach ($dados as $grupo)
        <h3>Origem: {{ $grupo['origem'] }}</h3>

        <p>
            Entradas: R$ {{ number_format($grupo['total_entrada'], 2, ',', '.') }}<br>
            Saídas: R$ {{ number_format($grupo['total_saida'], 2, ',', '.') }}<br>
        </p>

        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($grupo['movimentacoes'] as $mov)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($mov->data_competencia)->format('d/m/Y') }}</td>
                        <td>{{ $mov->tipo == 'entrada' ? 'Entrada' : 'Saída' }}</td>
                        <td>{{ $mov->descricao }}</td>
                        <td>R$ {{ number_format($mov->valor, 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    @php
        // Arrays para os gráficos
        $labels = []; // ex.: ['Banco', 'Caixa']
        $entradas = []; // ex.: [15000, 2500]
        $saidas = []; // ex.: [500, 300]

        foreach ($dados as $grupo) {
            $labels[] = $grupo['origem'];
            $entradas[] = $grupo['total_entrada'];
            $saidas[] = $grupo['total_saida'];
        }
    @endphp


    <hr>
    <h3>Total Geral</h3>
    <p>
        Total de Entradas (Banco + Caixa): R$ {{ number_format($totalGeralEntrada, 2, ',', '.') }} <br>
        Total de Saídas (Banco + Caixa): R$ {{ number_format($totalGeralSaida, 2, ',', '.') }}
    </p>

    <!-- Canvas onde o Chart.js vai desenhar o gráfico -->
    <canvas id="origensChart" width="400" height="200"></canvas>
</body>


<script>
    // Pega o elemento canvas
    const ctx = document.getElementById('origensChart').getContext('2d');

    // Dados vindos do Blade (transformando em JSON para uso no JS)
    const labels = @json($labels);
    const entradas = @json($entradas);
    const saidas = @json($saidas);

    // Monta a configuração do gráfico
    const data = {
        labels: labels,
        datasets: [{
                label: 'Entradas',
                data: entradas,
                backgroundColor: 'rgba(0, 128, 0, 0.7)', // Verde translúcido
                borderColor: 'rgba(0, 128, 0, 1)', // Borda verde
                borderWidth: 1
            },
            {
                label: 'Saídas',
                data: saidas,
                backgroundColor: 'rgba(255, 0, 0, 0.7)', // Vermelho translúcido
                borderColor: 'rgba(255, 0, 0, 1)', // Borda vermelha
                borderWidth: 1
            }
        ]
    };

    // Cria o gráfico de barras
    const origensChart = new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            // Exemplo de configurações extras
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Valores (R$)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Origem'
                    }
                }
            }
        }
    });
</script>

</html>
