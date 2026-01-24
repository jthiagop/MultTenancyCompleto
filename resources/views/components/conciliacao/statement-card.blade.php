<!--begin::Col-->
<div class="col-xxl-5 mb-5 mb-xl-10">
    <!--begin::List widget 8-->
    <div class="card card-bordered h-lg-100">
        <div class="card card-bordered flex-row-fluid overflow-hidden border border-hover-primary">
            <div class="card-header rounded">
                <!-- Data e Dia da Semana na mesma linha -->
                <div class="d-flex align-items-center">
                    <span class="text-dark fw-bold text-hover-primary fs-5">
                        {{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('d/m/Y') }}
                    </span>
                    <div class="text-muted mx-3">
                        {{ strtoupper(\Carbon\Carbon::parse($conciliacao->dtposted)->translatedFormat('l')) }}
                    </div>
                </div>

                <!-- Lado Direito: Valor -->
                <div class="card-toolbar">
                    <span class="{{ $conciliacao->amount < 0 ? 'text-danger' : 'text-dark' }} fw-bold fs-5">
                        R$ {{ number_format($conciliacao->amount, 2, ',', '.') }}
                    </span>
                </div>
            </div>
            <!-- Descrição / Memo -->
            <div class="card-body sm:p-6 p-9 d-flex flex-column justify-content-between">
                <div class="d-flex flex-column">
                    <p class="text-gray-700 fw-semibold fs-6 mb-4">
                        {{ $conciliacao->memo }}
                        <!-- Número do Cheque (se houver) -->
                        @if ($conciliacao->checknum)
                            <span class="badge badge-light-success d-inline-flex align-items-center fs-8 fw-semibold">
                                {{ $conciliacao->checknum }}
                            </span>
                        @endif
                    </p>
                </div>
                <div>
                    <x-status-badge :status="$conciliacao->status_conciliacao" />
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-stack">
                    <div class="d-flex flex-column mw-200px">
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-gray-700 fs-6 fw-semibold me-2">
                                Importado via OFX
                            </span>
                        </div>
                    </div>
                    <!-- Botão "Ignorar" (se houver essa funcionalidade) -->
                    <form action="{{ route('conciliacao.ignorar', $conciliacao->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-secondary">
                            <span>🚫 Ignorar</span>
                        </button>
                        <i class="fas fa-exclamation-circle fs-7 ms-2" data-bs-toggle="tooltip"
                            title="Você ignora um lançamento quando: É uma tarifa bancária que não precisa conciliar. É um lançamento duplicado. É um erro do banco que será estornado. Não corresponde a nenhuma transação do sistema"></i>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::LIst widget 8-->
</div>
<!--end::Col-->
