<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Conciliação Bancária</title>

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
        
        /* Badges de status */
        .badge-ok { background-color: #198754; color: white; }
        .badge-pendente { background-color: #ffc107; color: #000; }
        .badge-ignorado { background-color: #6c757d; color: white; }
        
        /* Rodapé com informações completas */
        @page {
            @bottom-left {
                content: "Relatório Gerado pelo Sistema Dominus - www.dominus.li";
                font-size: 0.65rem;
                color: #6c757d;
            }
            @bottom-right {
                content: "Página " counter(page) " de " counter(pages);
                font-size: 0.65rem;
                color: #6c757d;
            }
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
                <div class="subtitle">RELATÓRIO DE CONCILIAÇÃO BANCÁRIA</div>
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

    {{-- Filtros --}}
    <p class="fw-bold mb-1">Período: {{ $dataInicial }} a {{ $dataFinal }}</p>
    @isset($entidade)
        <p class="mb-2"><strong>Entidade Financeira:</strong> {{ $entidade->nome }}</p>
    @endisset
    <p class="mb-3"><strong>Status:</strong> 
        @if($status === 'todos')
            Todos
        @elseif($status === 'ok')
            Conciliado
        @elseif($status === 'pendente')
            Pendente
        @elseif($status === 'ignorado')
            Ignorado
        @endif
    </p>

    {{-- Tabela de Conciliações --}}
    <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
            <tr class="text-center">
                <th>Data</th>
                <th>Descrição</th>
                <th>Documento</th>
                <th class="text-end">Valor (R$)</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Transação Vinculada</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalConciliado = 0;
                $totalPendente = 0;
                $totalIgnorado = 0;
            @endphp
            
            @forelse($conciliacoes as $conciliacao)
                @php
                    $valor = $conciliacao->amount ?? 0;
                    if($conciliacao->status_conciliacao === 'ok') {
                        $totalConciliado += abs($valor);
                    } elseif($conciliacao->status_conciliacao === 'pendente') {
                        $totalPendente += abs($valor);
                    } elseif($conciliacao->status_conciliacao === 'ignorado') {
                        $totalIgnorado += abs($valor);
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('d/m/Y') }}</td>
                    <td>{{ $conciliacao->memo ?? '-' }}</td>
                    <td class="text-center">{{ $conciliacao->checknum ?? '-' }}</td>
                    <td class="text-end">{{ number_format(abs($valor), 2, ',', '.') }}</td>
                    <td class="text-center">
                        @if($valor >= 0)
                            <span class="badge bg-success">Entrada</span>
                        @else
                            <span class="badge bg-danger">Saída</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($conciliacao->status_conciliacao === 'ok')
                            <span class="badge badge-ok">Conciliado</span>
                        @elseif($conciliacao->status_conciliacao === 'pendente')
                            <span class="badge badge-pendente">Pendente</span>
                        @elseif($conciliacao->status_conciliacao === 'ignorado')
                            <span class="badge badge-ignorado">Ignorado</span>
                        @endif
                    </td>
                    <td>
                        @if($conciliacao->transacao)
                            {{ $conciliacao->transacao->descricao ?? 'Vinculada' }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Nenhuma conciliação encontrada para os filtros selecionados.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-light">
            <tr class="fw-semibold">
                <td colspan="3" class="text-end">Total de Registros:</td>
                <td class="text-end">{{ count($conciliacoes) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    {{-- Resumo por Status --}}
    <div class="mt-3 p-3 border border-dark-subtle rounded">
        <h5 class="mb-3">Resumo por Status</h5>
        <table class="table table-sm table-borderless mb-2">
            <tr>
                <td><strong>Conciliadas:</strong></td>
                <td class="text-end">R$ {{ number_format($totalConciliado, 2, ',', '.') }}</td>
                <td><strong>Pendentes:</strong></td>
                <td class="text-end">R$ {{ number_format($totalPendente, 2, ',', '.') }}</td>
                <td><strong>Ignoradas:</strong></td>
                <td class="text-end">R$ {{ number_format($totalIgnorado, 2, ',', '.') }}</td>
            </tr>
        </table>
        <hr>
        <div class="row">
            <div class="col-6">
                <strong>Total Geral:</strong>
            </div>
            <div class="col-6 text-end">
                <strong>R$ {{ number_format($totalConciliado + $totalPendente + $totalIgnorado, 2, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    {{-- Rodapé --}}
    <div class="mt-4 text-center">
        <small class="text-muted">
            Relatório gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
        </small>
    </div>
</body>
</html>
