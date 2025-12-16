<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $tituloRelatorio }}</title>

    {{-- Bootstrap 5 – CDN (Browsershot carrega normalmente) --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
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

        /* Estilo da tabela */
        .table-custom {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .table-custom thead th {
            background-color: #f0f0f0;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 0.75rem;
        }
        .table-custom tbody td {
            border: 1px solid #dee2e6;
            padding: 6px 8px;
            font-size: 0.7rem;
        }
        .titulo-relatorio {
            text-align: center;
            font-weight: bold;
            font-size: 1.1rem;
            margin: 15px 0;
            text-transform: uppercase;
        }
        .info-total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
            font-size: 0.85rem;
        }
        .badge-sim {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.65rem;
        }
        .badge-nao {
            background-color: #6c757d;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.65rem;
        }
    </style>
</head>

<body>
    {{-- Cabeçalho estilo imagem --}}
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
                <div class="subtitle">{{ strtoupper($company->razao_social) }}</div>
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

    {{-- Título do Relatório --}}
    <div class="titulo-relatorio">
        {{ $tituloRelatorio }}
    </div>

    {{-- Tabela de Fiéis --}}
    <table class="table-custom">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Nome</th>
                <th style="width: 10%;">CPF</th>
                <th style="width: 10%;">RG</th>
                <th style="width: 10%;">Data Nasc.</th>
                <th style="width: 8%;">Idade</th>
                <th style="width: 8%;">Sexo</th>
                <th style="width: 12%;">Telefone</th>
                <th style="width: 8%;">Dizimista</th>
            </tr>
        </thead>
        <tbody>
            @forelse($fieis as $index => $fiel)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $fiel->nome_completo }}</td>
                    <td>{{ $fiel->cpf ?? '-' }}</td>
                    <td>{{ $fiel->rg ?? '-' }}</td>
                    <td>
                        @if($fiel->data_nascimento)
                            {{ \Carbon\Carbon::parse($fiel->data_nascimento)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($fiel->data_nascimento)
                            {{ \Carbon\Carbon::parse($fiel->data_nascimento)->age }} anos
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $fiel->sexo === 'M' ? 'Masculino' : ($fiel->sexo === 'F' ? 'Feminino' : '-') }}</td>
                    <td>
                        @php
                            $telefone = $fiel->contacts->where('tipo', 'telefone')->first();
                        @endphp
                        {{ $telefone ? $telefone->valor : '-' }}
                    </td>
                    <td style="text-align: center;">
                        @php
                            $isDizimista = $fiel->tithe && $fiel->tithe->dizimista == 1;
                        @endphp
                        @if($isDizimista)
                            <span class="badge-sim">Sim</span>
                        @else
                            <span class="badge-nao">Não</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px;">
                        Nenhum fiel encontrado com os filtros selecionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Total de registros --}}
    @if($fieis->count() > 0)
        <div class="info-total">
            Total de Fiéis: {{ $fieis->count() }}
        </div>
    @endif

    {{-- Rodapé com data de geração --}}
    <div style="margin-top: 20px; text-align: center; font-size: 0.65rem; color: #666;">
        Relatório gerado em {{ \Carbon\Carbon::now()->format('d/m/Y \à\s H:i:s') }}
    </div>
</body>
</html>
