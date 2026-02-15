<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Extrato Financeiro</title>

    {{-- Bootstrap 5 - CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous">

    {{-- CSS proprio pensado para impressao --}}
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 8mm 15mm 8mm;
        }

        body {
            font-size: .72rem;
        }

        .logo {
            height: 60px;
        }

        /* Cabecalho */
        .header-container {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 15px 0;
            margin-bottom: 15px;
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

        .table > thead > tr > th {
            font-size: .7rem;
            padding: 6px 5px;
            white-space: nowrap;
        }

        .table > tbody > tr > td {
            font-size: .68rem;
            padding: 4px 5px;
            vertical-align: middle;
        }

        .table > tfoot > tr > td,
        .table > tfoot > tr > th {
            font-size: .72rem;
            padding: 6px 5px;
        }

        /* Linha zebrada */
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        /* Destaque de valores */
        .text-entrada {
            color: #0d6efd;
        }

        .text-saida {
            color: #dc3545;
        }

        .saldo-positivo {
            color: #198754;
            font-weight: 600;
        }

        .saldo-negativo {
            color: #dc3545;
            font-weight: 600;
        }

        /* Resumo */
        .resumo-box {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
        }

        .resumo-box .label {
            font-size: .68rem;
            color: #6c757d;
            margin-bottom: 2px;
        }

        .resumo-box .value {
            font-size: .85rem;
            font-weight: 700;
        }

        /* Conta badge */
        .conta-badge {
            display: inline-block;
            background: #e8f0fe;
            border: 1px solid #4285f4;
            border-radius: 5px;
            padding: 4px 12px;
            font-size: .75rem;
            font-weight: 600;
            color: #1a73e8;
        }

        /* Footer fixo */
        .footer-container {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 5px 20px;
            font-size: .6rem;
            color: #999;
            border-top: 1px solid #ccc;
            background: #fff;
        }
    </style>
</head>

<body>
    {{-- ============ CABECALHO ============ --}}
    <div class="header-container">
        <div class="header-content">
            {{-- Logo esquerdo --}}
            <div class="header-logo">
                @php
                    $avatar = $avatarEmpresa ?? ($empresaRelatorio->avatar ?? null);
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
                <h4 style="margin: 0; padding: 0;">{{ strtoupper($nomeEmpresa ?? ($empresaRelatorio->name ?? '')) }}</h4>
                <h5 style="margin: 5px 0; padding: 0; font-weight: normal;">
                    {{ strtoupper($razaoSocial ?? ($empresaRelatorio->razao_social ?? '')) }}
                </h5>
                <small>CNPJ: {{ $cnpjEmpresa ?? ($empresaRelatorio->cnpj ?? '') }}</small>
                <div style="font-size: 0.75rem; color: #333;">
                    @php
                        $addr = $enderecoEmpresa ?? ($empresaRelatorio->addresses ?? null);
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
                @if (($empresaRelatorio->phone ?? null) || ($empresaRelatorio->website ?? null) || ($empresaRelatorio->email ?? null))
                    <small>
                        @if ($empresaRelatorio->phone ?? null)
                            Fone: {{ $empresaRelatorio->phone }}
                        @endif
                        @if ($empresaRelatorio->website ?? null)
                            {{ $empresaRelatorio->phone ?? null ? ' - ' : '' }}Site: {{ $empresaRelatorio->website }}
                        @endif
                        @if ($empresaRelatorio->email ?? null)
                            {{ ($empresaRelatorio->phone ?? null) || ($empresaRelatorio->website ?? null) ? ' - ' : '' }}E-mail: {{ $empresaRelatorio->email }}
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


    {{-- ============ TITULO DO RELATORIO ============ --}}
    <div class="text-center mb-3">
        <h5 class="mb-1" style="font-size: .95rem; font-weight: 700;">EXTRATO FINANCEIRO</h5>
        <div style="font-size: .75rem; color: #555;">
            Per&iacute;odo: <strong>{{ $dataInicial }}</strong> a <strong>{{ $dataFinal }}</strong>
        </div>
        <div class="mt-2">
            <span class="conta-badge">
                <i class="fa-solid fa-building-columns"></i>
                {{ $entidade->nome }}
                @if($entidade->tipo === 'banco' && $entidade->agencia)
                    &mdash; Ag: {{ $entidade->agencia }} / CC: {{ $entidade->conta ?? '-' }}
                @endif
            </span>
        </div>
    </div>

    {{-- ============ RESUMO ============ --}}
    <div class="row mb-3">
        <div class="col-3">
            <div class="resumo-box text-center">
                <div class="label">Saldo Anterior</div>
                <div class="value {{ $saldoAnterior >= 0 ? 'saldo-positivo' : 'saldo-negativo' }}">
                    R$ {{ number_format($saldoAnterior, 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="resumo-box text-center">
                <div class="label">Total Entradas</div>
                <div class="value text-entrada">
                    R$ {{ number_format($totalEntradas, 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="resumo-box text-center">
                <div class="label">Total Sa&iacute;das</div>
                <div class="value text-saida">
                    R$ {{ number_format($totalSaidas, 2, ',', '.') }}
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="resumo-box text-center">
                <div class="label">Saldo Final</div>
                <div class="value {{ $saldoFinal >= 0 ? 'saldo-positivo' : 'saldo-negativo' }}">
                    R$ {{ number_format($saldoFinal, 2, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- ============ TABELA DE MOVIMENTACOES ============ --}}
    <table class="table table-bordered table-striped mb-0">
        <thead class="table-dark">
            <tr>
                <th style="width: 75px;">Data</th>
                <th>Descri&ccedil;&atilde;o</th>
                <th>Categoria</th>
                <th>Parceiro</th>
                <th class="text-end" style="width: 100px;">Entrada</th>
                <th class="text-end" style="width: 100px;">Sa&iacute;da</th>
                <th class="text-end" style="width: 110px;">Saldo</th>
            </tr>
        </thead>
        <tbody>
            {{-- Linha de saldo anterior --}}
            <tr style="background-color: #e9ecef; font-weight: 600;">
                <td colspan="6" class="text-end">Saldo Anterior</td>
                <td class="text-end {{ $saldoAnterior >= 0 ? 'saldo-positivo' : 'saldo-negativo' }}">
                    R$ {{ number_format($saldoAnterior, 2, ',', '.') }}
                </td>
            </tr>

            @forelse ($movimentacoes as $mov)
                <tr>
                    <td>{{ $mov['data'] }}</td>
                    <td>{{ $mov['descricao'] }}</td>
                    <td>{{ $mov['categoria'] }}</td>
                    <td>{{ $mov['parceiro'] }}</td>
                    <td class="text-end text-entrada">
                        {{ $mov['entrada'] !== null ? 'R$ ' . number_format($mov['entrada'], 2, ',', '.') : '' }}
                    </td>
                    <td class="text-end text-saida">
                        {{ $mov['saida'] !== null ? 'R$ ' . number_format($mov['saida'], 2, ',', '.') : '' }}
                    </td>
                    <td class="text-end {{ $mov['saldo'] >= 0 ? 'saldo-positivo' : 'saldo-negativo' }}">
                        R$ {{ number_format($mov['saldo'], 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Nenhuma movimenta&ccedil;&atilde;o encontrada no per&iacute;odo selecionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="table-dark fw-bold">
                <td colspan="4" class="text-end">TOTAIS</td>
                <td class="text-end text-entrada" style="color: #90caf9 !important;">
                    R$ {{ number_format($totalEntradas, 2, ',', '.') }}
                </td>
                <td class="text-end" style="color: #ef9a9a !important;">
                    R$ {{ number_format($totalSaidas, 2, ',', '.') }}
                </td>
                <td class="text-end" style="color: {{ $saldoFinal >= 0 ? '#a5d6a7' : '#ef9a9a' }} !important; font-weight: 700;">
                    R$ {{ number_format($saldoFinal, 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- ============ NOTA DE FILTRO ============ --}}
    <div class="mt-2" style="font-size: .6rem; color: #999;">
        <em>* Transa&ccedil;&otilde;es desconsideradas, parceladas e agendadas n&atilde;o s&atilde;o exibidas neste extrato.</em>
    </div>

    {{-- ============ FOOTER FIXO ============ --}}
    <div class="footer-container d-flex justify-content-between">
        <span>Gerado em {{ now()->format('d/m/Y H:i') }}</span>
        <span>Dominus Sistema &copy; {{ date('Y') }}</span>
    </div>
</body>

</html>
