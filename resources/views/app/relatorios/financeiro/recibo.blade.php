<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Recibo Nº {{ $recibo->id ?? 'XXXX' }}</title>
    <style>
        /* Reset básico */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        /* Cabeçalho */
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .header .org-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .org-info {
            font-size: 12px;
            color: #777;
        }

        /* Título do Recibo */
        .title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }

        /* Informações do Recibo (número, data, valor) */
        .recibo-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .recibo-info div {
            flex: 1;
            margin-right: 10px;
        }

        .recibo-info div:last-child {
            margin-right: 0;
        }

        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .value {
            font-size: 14px;
            color: #444;
        }

        /* Corpo do Recibo */
        .body {
            line-height: 1.5;
            margin-bottom: 20px;
        }

        /* Assinatura e Data Local */
        .signature-section {
            margin-top: 50px;
            text-align: center;
        }

        .signature-line {
            margin: 40px auto 10px auto;
            width: 50%;
            border-bottom: 1px solid #000;
        }

        .signature-label {
            font-size: 14px;
            margin-top: 5px;
        }

        /* Rodapé (informações adicionais) */
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <!-- Se tiver um logotipo, inclua aqui -->

            <div class="org-name">
                {{ $company->name }}
            </div>
            <div class="org-info">
                CNPJ: {{ $company->cnpj }}<br>
                {{ $company->addresses->rua }}, {{ $company->addresses->numero }} - {{ $company->addresses->bairro }},
                {{ $company->addresses->cidade }}/{{ $company->addresses->uf }} - CEP:
                {{ $company->addresses->cep }}<br>
                Telefone: (81) 3046-5061 | E-mail: curia@diocesedecaruaru.org
            </div>
        </div>

        <!-- Título -->
        <div class="title">
            RECIBO
        </div>

        <!-- Informações do Recibo -->
        <div class="recibo-info">
            <div>
                <span class="label">Recibo Nº</span>
                <span class="value">{{ $recibo->id ?? '_____' }}</span>
            </div>
            <div>
                <span class="label">Data de Emissão</span>
                <span class="value">
                    {{ \Carbon\Carbon::parse($recibo->data_emissao ?? now())->format('d/m/Y') }}
                </span>
            </div>
            <div>
                <span class="label">Valor (R$)</span>
                <span class="value">
                    {{ number_format($recibo->valor ?? 0, 2, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Corpo do Recibo -->
        <!-- Corpo do Recibo -->
        <div class="body">
            @php
            // Define a frase de acordo com o tipo de transação
            $tipoRecibo = isset($recibo->tipo_transacao) && strtolower($recibo->tipo_transacao) === 'pagamento'
                ? 'Pagamos a'
                : 'Recebemos de';
        @endphp

            {{ $tipoRecibo }} <strong>{{ $recibo->nome ?? 'Nome da Pessoa/Entidade' }}</strong>,
            portador do CPF/CNPJ <strong>{{ $recibo->cpf_cnpj ?? '___' }}</strong>,
            a importância de <strong>R$ {{ number_format($recibo->valor ?? 0, 2, ',', '.') }}</strong>
            (<em>{{ \NumberFormatter::create('pt_BR', \NumberFormatter::SPELLOUT)->format($recibo->valor ?? 0) }}</em>
            reais),
            referente a <strong>{{ $recibo->referente ?? 'motivo/descrição' }}</strong>.<br><br>

            @if (!empty($recibo->address))
                Endereço: <strong>
                    {{ $recibo->address->rua ?? '' }},
                    Nº {{ $recibo->address->numero ?? 's/n' }},
                    {{ $recibo->address->bairro ?? '' }} -
                    {{ $recibo->address->cidade ?? '' }}/{{ $recibo->address->uf ?? '' }},
                    CEP: {{ $recibo->address->cep ?? '' }}
                </strong><br><br>
            @endif

            Caruaru, {{ \Carbon\Carbon::now()->format('d') }}
            de {{ \Carbon\Carbon::now()->locale('pt_BR')->translatedFormat('F') }}
            de {{ \Carbon\Carbon::now()->format('Y') }}.
        </div>


        <!-- Assinatura -->
        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-label">Assinatura do Responsável</div>
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <p>
                Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i') }}.
                Este recibo é válido como comprovante de pagamento.
            </p>
        </div>
    </div>

</body>

</html>
