@props([
    'entidade',
    'isActive' => false,
])

@php
    $saldo = $entidade->saldo_atual ?? 0;
    $saldoNegativo = $saldo < 0;

    $accountTypeLabels = [
        'corrente'        => ['label' => 'Conta Corrente',  'badge' => 'badge-light-info',      'icon' => 'bi-wallet2'],
        'poupanca'        => ['label' => 'Poupança',        'badge' => 'badge-light-success',   'icon' => 'bi-piggy-bank'],
        'aplicacao'       => ['label' => 'Aplicação',       'badge' => 'badge-light-primary',   'icon' => 'bi-graph-up-arrow'],
        'renda_fixa'      => ['label' => 'Renda Fixa',      'badge' => 'badge-light-warning',   'icon' => 'bi-lock'],
        'tesouro_direto'  => ['label' => 'Tesouro Direto',  'badge' => 'badge-light-secondary', 'icon' => 'bi-shield-check'],
    ];

    $accountInfo = $accountTypeLabels[$entidade->account_type] ?? null;
@endphp

<div class="carousel-item {{ $isActive ? 'active' : '' }}">
    <a href="{{ route('entidades.show', $entidade->id) }}" 
       class="d-block text-decoration-none"
       aria-label="Ver detalhes de {{ $entidade->tipo === 'banco' ? ($entidade->bank?->name ?? 'Banco') . ' - Ag ' . $entidade->agencia . ' Cc ' . $entidade->conta : $entidade->nome }}">

        <div class="d-flex align-items-center">
            {{-- Avatar --}}
            <div class="symbol symbol-70px" role="img" aria-label="{{ $entidade->tipo === 'banco' ? 'Logo do banco' : 'Ícone de caixa' }}">
                @if ($entidade->tipo === 'banco')
                    @if ($entidade->bank?->logo_url)
                        <img src="{{ $entidade->bank->logo_url }}"
                             alt="{{ $entidade->bank->name }}" 
                             class="p-2 object-fit-contain"
                             loading="lazy"
                             decoding="async" />
                    @else
                        <span class="symbol-label bg-light-primary">
                            <i class="bi bi-bank fs-2x text-primary" aria-hidden="true"></i>
                        </span>
                    @endif
                @else
                    <span class="symbol-label overflow-hidden">
                        <img src="/tenancy/assets/media/svg/bancos/caixa.svg" 
                             alt="Caixa"
                             class="w-100 h-100 object-fit-contain" 
                             loading="lazy" 
                             decoding="async" />
                    </span>
                @endif
            </div>

            {{-- Info --}}
            <div class="card-body flex-grow-1">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        {{-- Linha 1: Nome da entidade + botão editar --}}
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-bold text-gray-800 fs-7" data-entidade-nome="{{ $entidade->id }}">
                                {{ $entidade->nome }}
                            </span>
                            <a href="javascript:void(0)"
                                    class="btn-rename-entidade text-primary"
                                    data-entidade-id="{{ $entidade->id }}"
                                    data-entidade-nome="{{ $entidade->nome }}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Renomear"
                                    aria-label="Renomear entidade">
                                <i class="fa-regular fa-pen-to-square " aria-hidden="true"></i>
                            </a>

                            @if (strtolower($entidade->status_conciliacao ?? '') === 'pendente')
                                <span class=""
                                      data-bs-toggle="tooltip" 
                                      data-bs-placement="top"
                                      title="Conciliação pendente"
                                      aria-label="Status: Conciliação pendente">
                                    <i class="bi bi-flag-fill" aria-hidden="true"></i>
                                </span>
                            @endif
                        </div>

                        {{-- Linha 2: Agência/Conta (banco) ou tipo (caixa) + badge account_type --}}
                        <div class="text-gray-500 fs-8 d-flex align-items-center gap-1 mt-1">
                            @if ($entidade->tipo === 'banco')
                                Ag {{ $entidade->agencia }} • Cc {{ $entidade->conta }}
                                @if ($accountInfo)
                                    <span class="badge {{ $accountInfo['badge'] }} fs-9 py-1 px-2">{{ $accountInfo['label'] }}</span>
                                @endif
                            @else
                                <span class="badge badge-light-success fs-9 py-1 px-2">Caixa</span>
                            @endif
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="text-end" aria-label="Saldo atual">
                        <div class="d-flex align-items-baseline justify-content-end">
                            <span class="fs-6 fw-semibold {{ $saldoNegativo ? 'text-danger' : 'text-gray-400' }} me-1" aria-hidden="true">R$</span>
                            <span class="fs-2 fw-bold {{ $saldoNegativo ? 'text-danger' : 'text-gray-900' }} lh-1">
                                {{ $entidade->saldo_formatado ?? number_format($saldo, 2, ',', '.') }}
                            </span>
                        </div>

                        @if (isset($entidade->variacao_percentual))
                            <span class="badge {{ $entidade->variacao_positiva ? 'badge-light-success' : 'badge-light-danger' }} mt-2"
                                  aria-label="Variação de {{ abs($entidade->variacao_percentual) }}% {{ $entidade->variacao_positiva ? 'positiva' : 'negativa' }}">
                                <i class="bi {{ $entidade->variacao_positiva ? 'bi-arrow-up' : 'bi-arrow-down' }}" aria-hidden="true"></i>
                                {{ number_format(abs($entidade->variacao_percentual), 1, ',', '.') }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>
