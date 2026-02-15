<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório de Conciliação Bancária</title>

    {{-- CSS próprio pensado para impressão (Bootstrap removido para evitar timeout) --}}
    <style>
        /* Reset e básicos */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: .77rem; line-height: 1.5; color: #212529; }
        
        /* Utilitários Bootstrap essenciais */
        .container { width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; }
        .row { display: flex; flex-wrap: wrap; margin-right: -15px; margin-left: -15px; }
        .col, .col-auto { position: relative; width: 100%; padding-right: 15px; padding-left: 15px; }
        .col { flex-basis: 0; flex-grow: 1; max-width: 100%; }
        .col-auto { flex: 0 0 auto; width: auto; max-width: 100%; }
        .text-center { text-align: center !important; }
        .text-end { text-align: right !important; }
        .text-muted { color: #6c757d !important; }
        .fw-bold { font-weight: 700 !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .mt-3 { margin-top: 1rem !important; }
        .small { font-size: 0.875em; }
        
        /* Tabelas */
        table { width: 100%; border-collapse: collapse; }
        .table { width: 100%; margin-bottom: 1rem; color: #212529; }
        .table th, .table td { padding: 0.5rem; vertical-align: top; border-top: 1px solid #dee2e6; }
        .table thead th { vertical-align: bottom; border-bottom: 2px solid #dee2e6; }
        .table-sm th, .table-sm td { padding: 0.3rem; }
        
        /* Badges */
        .badge { display: inline-block; padding: 0.25em 0.4em; font-size: 75%; font-weight: 700; line-height: 1; text-align: center; white-space: nowrap; vertical-align: baseline; border-radius: 0.25rem; }
        
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

        .bg-success { background-color: #198754; color: white; }
        .bg-danger { background-color: #dc3545; color: white; }
        .bg-warning { background-color: #ffc107; color: #000; }
        .bg-info { background-color: #007bff; color: white; }
        .bg-light { background-color: #f8f9fa; color: #000; }
        
        /* Badges de status */
        .badge-ok { background-color: #198754; color: white; }
        .badge-pendente { background-color: #ffc107; color: #000; }
        .badge-ignorado { background-color: #6c757d; color: white; }
        
        /* Rodapé com informações completas */
        @page {
            @bottom-left {
                content: "Relatório Gerado pelo Sistema Dominus - www.dominus.eco.br";
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
    <p class="fw-bold mb-1 text-center">RELATÓRIO DE CONCILIAÇÃO BANCÁRIA</p>
    <p class="fw-bold mb-1">Período: {{ $dataInicial }} a {{ $dataFinal }}</p>
    @isset($entidade)
        <p class="mb-2"><strong>Entidade Financeira:</strong> {{ $entidade->nome }}</p>
    @endisset
    <p class="mb-3"><strong>Status:</strong> 
        @if($status === 'todos')
            Todos
        @elseif($status === 'ok')
            Conciliado (OK)
        @elseif($status === 'pendente')
            Pendente
        @elseif($status === 'parcial')
            Parcial
        @elseif($status === 'divergente')
            Divergente
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
                        @elseif($conciliacao->status_conciliacao === 'parcial')
                            <span class="badge badge-parcial">Parcial</span>
                        @elseif($conciliacao->status_conciliacao === 'divergente')
                            <span class="badge badge-divergente">Divergente</span>
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
