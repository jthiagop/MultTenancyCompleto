<!--begin::Col-->
<div class="col-lg-4 col-xxl-5 mb-5 mb-xl-10">
    <!--begin::Statement Card widget 8-->
    <div class="card card-bordered h-lg-100">
        <div class="card card-bordered flex-row-fluid overflow-hidden border border-hover-primary">
            <!-- Card Header: Data + Valor -->
            <div class="card-header rounded d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <!-- Checkbox para seleção -->
                    <div class="form-check form-check-custom">
                        <input class="form-check-input conciliacao-checkbox" 
                               type="checkbox" 
                               value="{{ $conciliacao->id }}"
                               data-amount-cents="{{ $conciliacao->amount_cents }}"
                               id="conciliacao_{{ $conciliacao->id }}" />
                    </div>
                    <div>
                        <span class="text-dark fw-bold text-hover-primary fs-5">
                            {{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('d/m/Y') }}
                        </span>
                        <div class="text-muted small">
                            {{ strtoupper(\Carbon\Carbon::parse($conciliacao->dtposted)->translatedFormat('l')) }}
                        </div>
                    </div>
                </div>
                <!-- Lado Direito: Valor em centavos formatado -->
                <div class="card-toolbar">
                    @php
                        $valorEmReais = ($conciliacao->amount_cents ?? 0) / 100;
                        $classe = $conciliacao->amount_cents < 0 ? 'text-danger' : 'text-success';
                    @endphp
                    <span class="{{ $classe }} fw-bold fs-5">
                        {{ $conciliacao->amount_cents < 0 ? '- ' : '' }}R$ {{ number_format(abs($valorEmReais), 2, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Card Body: Descrição e Status -->
            <div class="card-body sm:p-6 p-9 d-flex flex-column justify-content-between">
                <div class="d-flex flex-column">
                    <p class="text-gray-700 fw-semibold fs-6 mb-4">
                        {{ $conciliacao->memo ?? 'Sem descrição' }}
                        
                        @if ($conciliacao->checknum)
                            <span class="badge badge-light-info d-inline-block ms-2">
                                {{ $conciliacao->checknum }}
                            </span>
                        @endif
                    </p>
                </div>
                
                <div>
                    <x-status-badge :status="$conciliacao->status_conciliacao" />
                </div>
            </div>

            <!-- Card Footer: Info + Botão Ignorar -->
            <div class="card-footer">
                <div class="d-flex flex-stack justify-content-between">
                    <div class="d-flex flex-column">
                        <span class="text-gray-700 fs-7 fw-semibold">
                            <i class="bi bi-cloud-download me-1"></i>
                            Importado via OFX
                        </span>
                    </div>
                    
                    <!-- Botão "Ignorar" -->
                    <form action="{{ route('conciliacao.ignorar', $conciliacao->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="btn btn-sm btn-light-danger" 
                                data-bs-toggle="tooltip" 
                                data-bs-placement="top" 
                                title="Ignorar este lançamento">
                            <i class="fa-regular fa-circle-xmark me-1"></i>
                            Ignorar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Statement Card widget 8-->
</div>
<!--end::Col-->
