{{-- Componente Blade para renderizar item de lançamento extraído --}}
<div class="card extracted-entry-card mb-4  hover-elevate-up" data-entry-index="{{ $index ?? 0 }}">
    <!--begin::Card Body-->
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-lg-row align-items-stretch position-relative">

            {{-- Indicador de Status (Barra Lateral) --}}
            <div class="position-absolute start-0 top-0 bottom-0 w-4px {{ $isReceitaItem ? 'bg-success' : 'bg-danger' }} rounded-start"></div>

            {{-- Checkbox Section --}}
            <div class="d-flex align-items-center justify-content-center p-4 ps-6 ">
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" value="" id="entry-checkbox-{{ $index }}">
                </div>
            </div>

            {{-- Main Content Section --}}
            <div class="flex-grow-1 p-4 d-flex flex-column justify-content-center gap-3">

                {{-- Top Row: Supplier & Value --}}
                <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap">
                    {{-- Supplier & Date --}}
                    <div>
                        <h4 class="text-gray-900 fw-bold mb-1 fs-5">{{ $fornecedor }}</h4>
                        <div class="text-muted fs-7 fw-semibold">
                            <i class="fa-regular fa-calendar me-1"></i> {{ $dataFormatada }}
                        </div>
                    </div>

                    {{-- Value Badge (Mobile/Desktop friendly) --}}
                    <div class="text-end">
                        <div class="badge badge-light-{{ $isReceitaItem ? 'success' : 'danger' }} fs-3 fw-bolder px-3 py-2">
                            {{ $isReceitaItem ? '+' : '-' }} R$ {{ number_format($valorItem, 2, ',', '.') }}
                        </div>
                    </div>
                </div>

                {{-- Bottom Row: Details --}}
                <div class="d-flex align-items-center gap-4 flex-wrap">
                    {{-- Categoria --}}
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-gray-500 fs-7 fw-semibold">Categoria:</span>
                        <span class="badge badge-light-primary fw-bold">{{ $categoria }}</span>
                    </div>

                    {{-- Pagamento --}}
                    @if($formaPagamento)
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-gray-500 fs-7 fw-semibold">Pagamento:</span>
                        <span class="badge badge-light-info fw-bold">{{ $formaPagamento }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Actions Section - Buttons on the same line --}}
            <div class="d-flex align-items-center  bg-light-white">
                <div class="d-flex flex-wrap flex-lg-nowrap align-items-center gap-2 p-4">

                    {{-- Primary Action --}}
                    <button type="button"
                            class="btn btn-sm btn-{{ $isReceitaItem ? 'success' : 'danger' }} fw-bold text-nowrap"
                            onclick="createTransaction({{ $index }}, {{ $isReceitaItem ? 'true' : 'false' }})">
                        <i class="fa-solid fa-plus fs-5 me-1"></i>
                        Criar {{ $isReceitaItem ? 'Receita' : 'Despesa' }}
                    </button>

                    {{-- Secondary Action --}}
                    <button type="button"
                            class="btn btn-sm btn-light-primary fw-semibold text-nowrap"
                            onclick="searchEntry({{ $index }})">
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Buscar
                    </button>

                    {{-- Delete Action --}}
                    <button type="button"
                            class="btn btn-sm btn-icon btn-light-danger"
                            onclick="removeEntry({{ $index }})"
                            data-bs-toggle="tooltip"
                            title="Remover item">
                        <i class="fa-solid fa-trash fs-5"></i>
                    </button>

                </div>
            </div>

        </div>
    </div>
    <!--end::Card Body-->
</div>
