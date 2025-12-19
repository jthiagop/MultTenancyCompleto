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
            font-size: 12px;
            color: #333;
            /* CORREÇÃO: Fundo cinza removido */
            background-color: #fff;
            padding: 20px;
        }

        .recibo-wrapper {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .separador {
            border-top: 2px dashed #999;
            margin: 20px 0;
            text-align: center;
            position: relative;
        }

        .separador::before {
            content: '✂';
            /* Símbolo de tesoura */
            position: absolute;
            top: -11px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 0 10px;
            font-size: 16px;
        }

        .via-identificador {
            text-align: right;
            font-style: italic;
            font-size: 10px;
            color: #888;
            margin-bottom: 10px;
        }

        .container {
            padding: 20px;
            border: 1px solid #ccc;
        }

        .header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header-left,
        .header-right {
            flex: 1;
        }

        .header-center {
            flex: 4;
            text-align: center;
        }

        .header-left img {
            max-height: 70px;
            max-width: 140px;
        }

        .header .org-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header .org-razaosocial {
            font-size: 12px;
            margin-bottom: 5px;
        }

        .header .org-info {
            font-size: 11px;
            color: #777;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }

        .recibo-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .recibo-info div {
            flex: 1;
            text-align: center;
        }

        .label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 11px;
        }

        .value {
            font-size: 12px;
            color: #000;
        }

        .body {
            line-height: 1.5;
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 3px solid #007bff;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
        }

        .signature-line {
            margin: 30px auto 10px auto;
            width: 50%;
            border-bottom: 1px solid #000;
        }

        .signature-label {
            font-size: 12px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div>
        <!-- #################### INÍCIO DA 1ª VIA #################### -->
        <div class="container">
            <div class="via-identificador">1ª Via - Emitente</div>
            <div class="header">
                <div class="header-left">
                    @if ($companyLogo)
                        <img src="{{ $companyLogo }}" alt="Logo">
                    @endif
                </div>
                <div class="header-center">
                    <div class="org-name">{{ $company->name }}</div>
                    <div class="org-razaosocial">{{ $company->razao_social }}</div>
                    <div class="org-info">
                        CNPJ: {{ $company->cnpj }}<br>
                        @if ($company->addresses)
                            {{ $company->addresses->rua }}, {{ $company->addresses->numero }} -
                            {{ $company->addresses->bairro }},
                            {{ $company->addresses->cidade }}/{{ $company->addresses->uf }}<br>
                        @endif
                        E-mail: {{ $company->email }}
                    </div>
                </div>
                <div class="header-right">&nbsp;</div>
            </div>
            <div class="title">RECIBO</div>
            <div class="recibo-info">
                <div>
                    <span class="label">Recibo Nº</span>
                    <span class="value">{{ $recibo->id ?? '_____' }}</span>
                </div>
                <div>
                    <span class="label">Data de Emissão</span>
                    <span
                        class="value">{{ \Carbon\Carbon::parse($recibo->data_emissao ?? now())->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="label">Valor (R$)</span>
                    <span class="value">{{ number_format($recibo->valor ?? 0, 2, ',', '.') }}</span>
                </div>
            </div>
            <div class="body">
                @php
                    $tipoRecibo =
                        isset($recibo->tipo_transacao) && strtolower($recibo->tipo_transacao) === 'pagamento'
                            ? 'Pagamos a'
                            : 'Recebemos de';
                @endphp
                {{ $tipoRecibo }} <strong>{{ $recibo->nome ?? 'Nome da Pessoa/Entidade' }}</strong>,
                portador do CPF/CNPJ <strong>{{ $recibo->cpf_cnpj ?? '___' }}</strong>,
                a importância de <strong>R$ {{ number_format($recibo->valor ?? 0, 2, ',', '.') }}</strong>
                (<em>{{ \NumberFormatter::create('pt_BR', \NumberFormatter::SPELLOUT)->format($recibo->valor ?? 0) }}
                    reais</em>),
                referente a <strong>{{ $recibo->referente ?? 'motivo/descrição' }}</strong>.
            </div>

            <!-- CORREÇÃO: Assinatura e Rodapé adicionados -->
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Assinatura do Responsável</div>
            </div>
            <div class="footer">
                <p>
                    Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i') }}.
                    Este recibo é válido como comprovante de pagamento.
                </p>
            </div>
        </div>
        <!-- #################### FIM DA 1ª VIA #################### -->

        <div class="separador"></div>

        <!-- #################### INÍCIO DA 2ª VIA (CÓPIA) #################### -->
        <div class="container">
            <div class="via-identificador">2ª Via - Cliente/Pagador</div>
            <div class="header">
                <div class="header-left">
                    @if ($companyLogo)
                        <img src="{{ $companyLogo }}" alt="Logo">
                    @endif
                </div>
                <div class="header-center">
                    <div class="org-name">{{ $company->name }}</div>
                    <div class="org-razaosocial">{{ $company->razao_social }}</div>

                    <div class="org-info">
                        CNPJ: {{ $company->cnpj }}<br>
                        @if ($company->addresses)
                            {{ $company->addresses->rua }}, {{ $company->addresses->numero }} -
                            {{ $company->addresses->bairro }},
                            {{ $company->addresses->cidade }}/{{ $company->addresses->uf }}<br>
                        @endif
                        E-mail: {{ $company->email }}
                    </div>
                </div>
                <div class="header-right">&nbsp;</div>
            </div>
            <div class="title">RECIBO</div>
            <div class="recibo-info">
                <div>
                    <span class="label">Recibo Nº</span>
                    <span class="value">{{ $recibo->id ?? '_____' }}</span>
                </div>
                <div>
                    <span class="label">Data de Emissão</span>
                    <span
                        class="value">{{ \Carbon\Carbon::parse($recibo->data_emissao ?? now())->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="label">Valor (R$)</span>
                    <span class="value">{{ number_format($recibo->valor ?? 0, 2, ',', '.') }}</span>
                </div>
            </div>
            <div class="body">
                @php
                    $tipoRecibo =
                        isset($recibo->tipo_transacao) && strtolower($recibo->tipo_transacao) === 'pagamento'
                            ? 'Pagamos a'
                            : 'Recebemos de';
                @endphp
                {{ $tipoRecibo }} <strong>{{ $recibo->nome ?? 'Nome da Pessoa/Entidade' }}</strong>,
                portador do CPF/CNPJ <strong>{{ $recibo->cpf_cnpj ?? '___' }}</strong>,
                a importância de <strong>R$ {{ number_format($recibo->valor ?? 0, 2, ',', '.') }}</strong>
                (<em>{{ \NumberFormatter::create('pt_BR', \NumberFormatter::SPELLOUT)->format($recibo->valor ?? 0) }}
                    reais</em>),
                referente a <strong>{{ $recibo->referente ?? 'motivo/descrição' }}</strong>.
            </div>

            <!-- CORREÇÃO: Assinatura e Rodapé adicionados -->
            <div class="signature-section">
                <div class="signature-line"></div>
                <div class="signature-label">Assinatura do Responsável</div>
            </div>
            <div class="footer">
                <p>
                    Documento gerado eletronicamente em {{ now()->format('d/m/Y H:i') }}.
                    Este recibo é válido como comprovante de pagamento.
                </p>
            </div>
        </div>
        <!-- #################### FIM DA 2ª VIA #################### -->
    </div>

</body>

</html>
