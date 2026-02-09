@props([
    'entidade',
    'isActive' => false,
    'index' => 0,
])

<div class="carousel-item {{ $isActive ? 'active' : '' }}">
    <a href="{{ route('entidades.show', $entidade->id) }}" 
       class="d-block text-decoration-none"
       aria-label="Ver detalhes de {{ $entidade->tipo === 'banco' ? 'Agência ' . $entidade->agencia . ' Conta ' . $entidade->conta : $entidade->nome }}">

        <div class="d-flex align-items-center">
            {{-- Avatar --}}
            <div class="symbol symbol-70px " role="img" aria-label="{{ $entidade->tipo === 'banco' ? 'Logo do banco' : 'Ícone de caixa' }}">
                @if ($entidade->tipo === 'banco')
                    @if ($entidade->bank?->logo_path)
                        <img src="{{ $entidade->bank->logo_path }}"
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
                    <div >
                        <div class="fw-bold text-gray-800">
                            @if ($entidade->tipo === 'banco')
                                Ag {{ $entidade->agencia }} • Cc {{ $entidade->conta }}
                                <span class="badge badge-light-info ms-2" aria-label="Tipo: Banco">Banco</span>
                            @else
                                {{ $entidade->nome }}
                                <span class="badge badge-light-success ms-2" aria-label="Tipo: Caixa">Caixa</span>
                            @endif

                            @if (strtolower($entidade->status_conciliacao ?? '') === 'pendente')
                                <span class="badge badge-light-warning ms-2"
                                      data-bs-toggle="tooltip" 
                                      data-bs-placement="top"
                                      title="Conciliação pendente"
                                      aria-label="Status: Conciliação pendente">
                                    <i class="bi bi-flag-fill" aria-hidden="true"></i>
                                </span>
                            @endif
                        </div>

                        <div class="text-gray-500 fs-8">
                            Clique para ver detalhes
                        </div>
                    </div>

                    {{-- Balance --}}
                    <div class="text-end mb-2" aria-label="Saldo atual">
                        <div class="d-flex align-items-baseline justify-content-end">
                            <span class="fs-6 fw-semibold text-gray-400 me-1" aria-hidden="true">R$</span>
                            <span class="fs-2 fw-bold text-gray-900 lh-1">
                                {{ $entidade->saldo_formatado ?? number_format($entidade->saldo_atual ?? 0, 2, ',', '.') }}
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
