@props([
    'entidade',
    'isActive' => false,
])

@php
    $saldo = $entidade->saldo_atual ?? 0;
    $saldoNegativo = $saldo < 0;

    $accountTypeLabels = [
        'corrente'        => ['label' => 'Conta Corrente',  'badge' => 'badge-light-primary',  'icon' => 'bi-house-door-fill'],
        'poupanca'        => ['label' => 'Poupança',        'badge' => 'badge-light-success',  'icon' => 'bi-piggy-bank-fill'],
        'aplicacao'       => ['label' => 'Aplicação',       'badge' => 'badge-light-info',     'icon' => 'bi-graph-up-arrow'],
        'renda_fixa'      => ['label' => 'Renda Fixa',      'badge' => 'badge-light-warning',  'icon' => 'bi-lock-fill'],
        'tesouro_direto'  => ['label' => 'Tesouro Direto',  'badge' => 'badge-light-secondary','icon' => 'bi-shield-check'],
    ];

    $accountInfo = $accountTypeLabels[$entidade->account_type] ?? null;

    // Variação
    $temVariacao = isset($entidade->variacao_percentual) && $entidade->variacao_percentual != 0;
    $variacaoPositiva = $entidade->variacao_positiva ?? false;
    $variacaoValor = $entidade->variacao_valor ?? 0;
    $variacaoPercentual = $entidade->variacao_percentual ?? 0;
@endphp

<div class="carousel-item {{ $isActive ? 'active' : '' }}">
    <a href="{{ route('entidades.show', $entidade->id) }}" 
       class="d-block text-decoration-none"
       aria-label="Ver detalhes de {{ $entidade->tipo === 'banco' ? ($entidade->bank?->name ?? 'Banco') . ' - Ag ' . $entidade->agencia . ' Cc ' . $entidade->conta : $entidade->nome }}">

        <div class="d-flex align-items-start">
            {{-- Logo / Avatar --}}
            <div class="me-4 flex-shrink-0">
                <div class="symbol symbol-50px">
                    @if ($entidade->tipo === 'banco')
                        @if ($entidade->bank?->logo_url)
                            <img src="{{ $entidade->bank->logo_url }}"
                                 alt="{{ $entidade->bank->name }}" 
                                 class="rounded-2 p-2 object-fit-contain bg-light"
                                 loading="lazy"
                                 decoding="async" />
                        @else
                            <span class="symbol-label rounded-2 bg-light-primary">
                                <i class="bi bi-bank fs-2 text-primary" aria-hidden="true"></i>
                            </span>
                        @endif
                    @else
                        <span class="symbol-label rounded-2 overflow-hidden bg-light">
                            <img src="/tenancy/assets/media/svg/bancos/caixa.svg" 
                                 alt="Caixa"
                                 class="w-100 h-100 object-fit-contain p-1" 
                                 loading="lazy" 
                                 decoding="async" />
                        </span>
                    @endif
                </div>
            </div>

            {{-- Info + Saldo --}}
            <div class="flex-grow-1 min-w-0">
                {{-- Linha 1: Nome + Editar --}}
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center gap-2 min-w-0">
                        <span class="fw-bold text-gray-800 fs-6 text-truncate" data-entidade-nome="{{ $entidade->id }}">
                            @if ($entidade->tipo === 'banco')
                                {{ $entidade->bank?->name ?? $entidade->nome }}
                            @else
                                {{ $entidade->nome }}
                            @endif
                        </span>

                        @if (strtolower($entidade->status_conciliacao ?? '') === 'pendente')
                            <span data-bs-toggle="tooltip" 
                                  data-bs-placement="top"
                                  title="Conciliação pendente"
                                  aria-label="Status: Conciliação pendente">
                                <i class="bi bi-flag-fill text-warning fs-8" aria-hidden="true"></i>
                            </span>
                        @endif
                    </div>

                    <a href="javascript:void(0)"
                       class="btn-rename-entidade ms-2 flex-shrink-0 text-gray-500"
                       data-entidade-id="{{ $entidade->id }}"
                       data-entidade-nome="{{ $entidade->nome }}"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="Renomear"
                       aria-label="Renomear entidade">
                        <i class="fa-regular fa-pen-to-square fs-8" aria-hidden="true"></i>
                    </a>
                </div>

                {{-- Linha 2: Agência/Conta --}}
                <div class="text-gray-500 fs-8 mb-3">
                    @if ($entidade->tipo === 'banco')
                        Ag {{ $entidade->agencia }} • Cc {{ $entidade->conta }}
                    @else
                        {{ $entidade->nome }}
                    @endif
                </div>

                {{-- Linha 3: Badge tipo + Saldo --}}
                <div class="d-flex align-items-end justify-content-between">
                    {{-- Badge tipo de conta --}}
                    <div>
                        @if ($entidade->tipo === 'banco' && $accountInfo)
                            <span class="badge {{ $accountInfo['badge'] }} rounded-pill px-3 py-2 fs-9 fw-semibold">
                                <i class="bi {{ $accountInfo['icon'] }} me-1 fs-9" aria-hidden="true"></i>
                                {{ $accountInfo['label'] }}
                            </span>
                        @else
                            <span class="badge badge-light-success rounded-pill px-3 py-2 fs-9 fw-semibold">
                                <i class="bi bi-cash-stack me-1 fs-9" aria-hidden="true"></i>
                                Caixa
                            </span>
                        @endif
                    </div>

                    {{-- Saldo --}}
                    <div class="text-end" aria-label="Saldo atual">
                        <div class="d-flex align-items-baseline justify-content-end">
                            <span class="fw-semibold me-1 fs-7 {{ $saldoNegativo ? 'text-danger' : 'text-gray-400' }}" aria-hidden="true">R$</span>
                            <span class="fw-bold lh-1 fs-2x {{ $saldoNegativo ? 'text-danger' : 'text-gray-900' }}">
                                {{ $entidade->saldo_formatado ?? number_format($saldo, 2, ',', '.') }}
                            </span>
                        </div>

                        {{-- Variação --}}
                        @if ($temVariacao)
                            <div class="mt-2">
                                <span class="badge {{ $variacaoPositiva ? 'badge-light-success' : 'badge-light-danger' }} rounded-pill px-2 py-1 fs-9"
                                      aria-label="Variação de {{ number_format(abs($variacaoPercentual), 1, ',', '.') }}% {{ $variacaoPositiva ? 'positiva' : 'negativa' }}">
                                    <i class="bi {{ $variacaoPositiva ? 'bi-arrow-up' : 'bi-arrow-down' }} fs-9 me-1" aria-hidden="true"></i>
                                    R$ {{ number_format(abs($variacaoValor), 2, ',', '.') }}
                                    •
                                    {{ number_format(abs($variacaoPercentual), 1, ',', '.') }}%
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>
