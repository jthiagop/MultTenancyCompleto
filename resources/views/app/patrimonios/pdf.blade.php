<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Bens Móveis</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header-container {
            width: 100%;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 100px;
            /* Largura fixa para a célula do logo */
            vertical-align: top;
        }

        .logo-cell img {
            width: 80px;
            /* Tamanho da imagem do logo */
            height: auto;
        }

        .details-cell {
            text-align: left;
            vertical-align: top;
        }

        .details-cell h1,
        .details-cell h2,
        .details-cell p {
            margin: 0;
            padding: 2px 0;
            font-weight: bold;
        }

        .main-title {
            font-size: 16px;
            letter-spacing: 4px;
            /* Efeito de letras espaçadas */
            margin-bottom: 4px;
        }

        .sub-title {
            font-size: 12px;
            margin-bottom: 4px;
        }

        .cnpj,
        .phone {
            font-size: 12px;
            font-weight: normal;
            /* CNPJ e Telefone sem negrito */
        }

        /* Estilos do relatório (tabela, etc) */
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 20px 0;
            background-color: #e0e0e0;
            padding: 8px;
        }

        table.report-table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        .report-table thead th {
            background-color: #f2f2f2;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <hr style="border: 0; border-top: 1px solid #000; margin-bottom: 15px;">

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    @if ($companyLogo)
                        <img src="{{ $companyLogo }}" alt="Logo">
                    @endif
                </td>
                <td class="details-cell">
                    <div>

                    </div>
                    <h1 class="main-title">{{ $company->name ?? 'ARQUIDIOCESE DE OLINDA E RECIFE' }}</h1>
                    <h2 class="sub-title">
                        {{ $company->details ?? 'ORATORIO PARTICULAR IGREJA DE NOSSA SENHORA DO LIVRAMENTO' }}</h2>
                    <p class="cnpj">{{ $company->cnpj ?? '00.000.000/0001-00' }}</p>

                    @if ($company->addresses)
                        <p>{{ optional($company->addresses)->rua }}, {{ optional($company->addresses)->numero }} -
                            {{ optional($company->addresses)->bairro }}</p>
                    @endif

                    <p class="phone">(81) 3241-5110</p>
                </td>
            </tr>
        </table>
    </div>
    <div class="report-title">
        Relatório de busca de bens móveis
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 18%;">RID</th>
                <th style="width: 25%;">Descrição</th>
                <th style="width: 20%;">Cidade</th>
                <th style="width: 27%;">Logradouro</th>
                <th style="width: 10%;">CEP</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($patrimonios as $foreiro)
                <tr>
                    <td>{{ $foreiro->codigo_rid }}</td>
                    <td>{{ $foreiro->descricao }}</td>
                    <td>{{ $foreiro->localidade }}</td>
                    <td>{{ $foreiro->logradouro }}</td>
                    <td>{{ $foreiro->cep }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Nenhum patrimônio encontrado para os filtros
                        selecionados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        <p>Total de Registros: {{ $totalRegistros }} | Gerado em: {{ date('d/m/Y H:i') }}</p>
    </div>
</body>

</html>
