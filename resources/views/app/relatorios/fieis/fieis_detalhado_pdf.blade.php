<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $tituloRelatorio }} - Detalhado</title>

    {{-- Bootstrap 5 – CDN --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        crossorigin="anonymous">

    {{-- CSS próprio pensado para impressão --}}
    <style>
        @page { size: A4 portrait; margin: 8mm 8mm 10mm 8mm; }
        body   { font-size: .70rem; }
        .page-break { page-break-after: always; }

        /* Estilo do cabeçalho similar à imagem */
        .header-container {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 12px 0;
            margin-bottom: 15px;
        }
        .header-content {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .header-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
            text-align: center;
        }
        .header-logo img {
            max-width: 80px;
            max-height: 80px;
            width: auto;
            height: auto;
        }
        .header-text {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            padding: 0 10px;
        }
        .header-text h4 {
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.3;
        }
        .header-text .subtitle {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.75rem;
            margin-top: 2px;
            line-height: 1.2;
        }
        .header-text small {
            display: block;
            font-size: 0.65rem;
            line-height: 1.4;
            margin-top: 2px;
        }

        /* Cartões de fiéis */
        .fiel-card {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 12px;
            margin-bottom: 12px;
            background-color: #f8f9fa;
            page-break-inside: avoid;
        }
        .fiel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .fiel-nome {
            font-weight: bold;
            font-size: 0.85rem;
            color: #333;
        }
        .fiel-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            font-size: 0.68rem;
        }
        .info-label {
            font-weight: 600;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .badge-dizimista {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.65rem;
            font-weight: bold;
        }
        .badge-nao-dizimista {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.65rem;
            font-weight: bold;
        }
        .titulo-relatorio {
            text-align: center;
            font-weight: bold;
            font-size: 1rem;
            margin: 12px 0;
            text-transform: uppercase;
        }
        .info-total {
            text-align: center;
            font-weight: bold;
            margin-top: 15px;
            padding: 8px;
            background-color: #e9ecef;
            border-radius: 5px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    {{-- Cabeçalho --}}
    <div class="header-container">
        <div class="header-content">
            <div class="header-logo">
                @php
                    $logoPath = $company->avatar
                        ? storage_path('app/public/' . $company->avatar)
                        : public_path('tenancy/assets/media/png/perfil.svg');
                @endphp
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo" style="width: 100%; height: auto; max-height: 80px;">
                @endif
            </div>

            <div class="header-text">
                <h4>{{ strtoupper($company->name) }}</h4>
                <div class="subtitle">{{ strtoupper($company->name) }}</div>
                <small>CNPJ: {{ $company->cnpj ?? '' }}</small>
                <small>
                    @if($company->addresses->rua ?? '')
                        {{ $company->addresses->rua }}
                        @if($company->addresses->numero ?? ''), {{ $company->addresses->numero }}@endif
                        @if($company->addresses->bairro ?? '') - {{ $company->addresses->bairro }}@endif
                        / {{ $company->addresses->cidade ?? '' }}-{{ $company->addresses->uf ?? '' }}
                    @endif
                </small>
                <small>
                    Fone: {{ $company->phone ?? '' }}
                    @if($company->website ?? '') - Site: {{ $company->website }}@else - Site: -@endif
                    - E-mail: {{ $company->email ?? '' }}
                </small>
            </div>

            <div class="header-logo">
                @if(file_exists($logoPath))
                    <img src="{{ $logoPath }}" alt="Logo" style="width: 100%; height: auto; max-height: 80px;">
                @endif
            </div>
        </div>
    </div>

    {{-- Título do Relatório --}}
    <div class="titulo-relatorio">
        {{ $tituloRelatorio }}
    </div>

    {{-- Cartões de Fiéis --}}
    @forelse($fieis as $index => $fiel)
        <div class="fiel-card">
            <div class="fiel-header">
                <div class="fiel-nome">{{ $index + 1 }}. {{ $fiel->nome_completo }}</div>
                <div>
                    @php
                        $isDizimista = $fiel->tithe && $fiel->tithe->dizimista == 1;
                    @endphp
                    @if($isDizimista)
                        <span class="badge-dizimista">Dizimista</span>
                    @else
                        <span class="badge-nao-dizimista">Não Dizimista</span>
                    @endif
                </div>
            </div>

            <div class="fiel-info">
                <div>
                    <span class="info-label">CPF:</span>
                    <span class="info-value">{{ $fiel->cpf ?? '-' }}</span>
                </div>
                <div>
                    <span class="info-label">RG:</span>
                    <span class="info-value">{{ $fiel->rg ?? '-' }}</span>
                </div>
                <div>
                    <span class="info-label">Data Nascimento:</span>
                    <span class="info-value">
                        @if($fiel->data_nascimento)
                            {{ \Carbon\Carbon::parse($fiel->data_nascimento)->format('d/m/Y') }}
                            ({{ \Carbon\Carbon::parse($fiel->data_nascimento)->age }} anos)
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div>
                    <span class="info-label">Sexo:</span>
                    <span class="info-value">{{ $fiel->sexo === 'M' ? 'Masculino' : ($fiel->sexo === 'F' ? 'Feminino' : '-') }}</span>
                </div>
                <div>
                    <span class="info-label">Telefone:</span>
                    <span class="info-value">
                        @php
                            $telefone = $fiel->contacts->where('tipo', 'telefone')->first();
                        @endphp
                        {{ $telefone ? $telefone->valor : '-' }}
                    </span>
                </div>
                <div>
                    <span class="info-label">E-mail:</span>
                    <span class="info-value">
                        @php
                            $email = $fiel->contacts->where('tipo', 'email')->first();
                        @endphp
                        {{ $email ? $email->valor : '-' }}
                    </span>
                </div>
                <div>
                    <span class="info-label">Profissão:</span>
                    <span class="info-value">{{ $fiel->complementaryData->profissao ?? '-' }}</span>
                </div>
                <div>
                    <span class="info-label">Estado Civil:</span>
                    <span class="info-value">{{ $fiel->complementaryData->estado_civil ?? '-' }}</span>
                </div>
                <div style="grid-column: span 2;">
                    <span class="info-label">Endereço:</span>
                    <span class="info-value">
                        @php
                            $endereco = $fiel->addresses->where('pivot.tipo', 'principal')->first();
                        @endphp
                        @if($endereco)
                            {{ $endereco->rua }}
                            @if($endereco->bairro), {{ $endereco->bairro }}@endif
                            @if($endereco->cidade) - {{ $endereco->cidade }}@endif
                            @if($endereco->uf)/{{ $endereco->uf }}@endif
                            @if($endereco->cep) - CEP: {{ $endereco->cep }}@endif
                        @else
                            -
                        @endif
                    </span>
                </div>
            </div>
        </div>

        @if(($index + 1) % 5 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <div style="text-align: center; padding: 40px; border: 1px solid #dee2e6; border-radius: 5px; background-color: #f8f9fa;">
            Nenhum fiel encontrado com os filtros selecionados.
        </div>
    @endforelse

    {{-- Total de registros --}}
    @if($fieis->count() > 0)
        <div class="info-total">
            Total de Fiéis: {{ $fieis->count() }}
        </div>
    @endif

    {{-- Rodapé com data de geração --}}
    <div style="margin-top: 15px; text-align: center; font-size: 0.60rem; color: #666;">
        Relatório gerado em {{ \Carbon\Carbon::now()->format('d/m/Y \à\s H:i:s') }}
    </div>
</body>
</html>
