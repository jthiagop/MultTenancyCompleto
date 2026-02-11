{{-- Componente Blade para renderizar item de lançamento extraído --}}
@php
    $isSingleTransaction = $isSingleTransaction ?? false;
    $totalItens = $totalItens ?? 0;
    $tipoDocumento = $tipoDocumento ?? '';
@endphp
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
                            @if($isSingleTransaction && $totalItens > 0)
                                <span class="ms-2 badge badge-light-info fs-8">
                                    <i class="fa-solid fa-list fs-9 me-1"></i>
                                    {{ $totalItens }} {{ $totalItens === 1 ? 'item' : 'itens' }}
                                </span>
                            @endif
                        </div>
                        @if($isSingleTransaction && $tipoDocumento)
                            <span class="badge badge-light-primary fs-8 mt-1">{{ $tipoDocumento }}</span>
                        @endif
                    </div>

                    {{-- Value Badge (Mobile/Desktop friendly) --}}
                    <div class="text-end">
                        <div class="badge badge-light-{{ $isReceitaItem ? 'success' : 'danger' }} fs-3 fw-bolder px-3 py-2">
                            {{ $isReceitaItem ? '+' : '-' }} R$ {{ number_format($valorItem, 2, ',', '.') }}
                        </div>
                        @if($isSingleTransaction && $totalItens > 1)
                            <div class="text-muted fs-8 mt-1">Valor total da nota</div>
                        @endif
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

            {{-- Actions Section --}}
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center gap-2 p-4">

                    {{-- Primary Action --}}
                    <button type="button"
                            class="btn btn-sm btn-{{ $isReceitaItem ? 'success' : 'danger' }} fw-bold text-nowrap"
                            onclick="createTransaction({{ $index }}, {{ $isReceitaItem ? 'true' : 'false' }})"
                            data-bs-toggle="tooltip"
                            title="Criar {{ $isReceitaItem ? 'Receita' : 'Despesa' }}">
                        <i class="fa-solid fa-plus fs-5 me-1 d-none d-xl-inline"></i>
                        <i class="fa-solid fa-plus fs-5 d-xl-none"></i>
                        <span class="d-none d-xl-inline">Criar {{ $isReceitaItem ? 'Receita' : 'Despesa' }}</span>
                    </button>

                    {{-- Secondary Action --}}
                    <button type="button"
                            class="btn btn-sm btn-icon btn-light-primary d-xl-none"
                            onclick="searchEntry({{ $index }})"
                            data-bs-toggle="tooltip"
                            title="Buscar lançamento semelhante">
                        <i class="fa-solid fa-magnifying-glass fs-5"></i>
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-light-primary fw-semibold text-nowrap d-none d-xl-inline-flex"
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
                        <i class="fa-solid fa-trash-can fs-5"></i>
                    </button>

                </div>
            </div>

        </div>
    </div>
    <!--end::Card Body-->
</div>
