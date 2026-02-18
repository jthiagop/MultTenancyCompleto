{{-- Componente Blade para renderizar item de lançamento extraído --}}
@php
    $isSingleTransaction = $isSingleTransaction ?? false;
    $totalItens = $totalItens ?? 0;
    $tipoDocumento = $tipoDocumento ?? '';
    $entryIndex = $index ?? 0;
    $tipoDb = $isReceitaItem ? 'entrada' : 'saida';

    // Dados adicionais disponíveis no extractedData
    $cnpj = $extractedData['estabelecimento']['cnpj'] ?? null;
    $cnpjFormatado = $cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj) : null;
    $descricaoDetalhada = $extractedData['classificacao']['descricao_detalhada'] ?? null;

    // Parcelamento
    $isParcelado = $extractedData['parcelamento']['is_parcelado'] ?? false;
    $parcelaAtual = $extractedData['parcelamento']['parcela_atual'] ?? 1;
    $totalParcelas = $extractedData['parcelamento']['total_parcelas'] ?? 1;

    // Financeiro extra
    $juros = floatval($extractedData['financeiro']['juros'] ?? 0);
    $multa = floatval($extractedData['financeiro']['multa'] ?? 0);
    $desconto = floatval($extractedData['financeiro']['desconto'] ?? 0);
    $numeroDocumento = $extractedData['financeiro']['numero_documento'] ?? null;
    $numeroNf = $extractedData['nfe_info']['numero_nf'] ?? null;
@endphp
<div class="card extracted-entry-card mb-3 hover-elevate-up border border-dashed" 
     data-entry-index="{{ $entryIndex }}" 
     data-entry-tipo="{{ $tipoDb }}"
     data-entry-valor="{{ $valorItem }}">
    <div class="card-body px-0 py-0">
        <div class="d-flex align-items-center position-relative">

            {{-- Barra Lateral --}}
            <div class="position-absolute start-0 top-0 bottom-0 w-4px rounded-start entry-type-indicator {{ $isReceitaItem ? 'bg-success' : 'bg-danger' }}"></div>

            {{-- Toggle --}}
            <div class="ps-5 pe-3 py-3 flex-shrink-0">
                <div class="form-check form-switch form-check-custom form-check-solid"
                     data-bs-toggle="tooltip"
                     title="Alternar: {{ $isReceitaItem ? 'Receita → Despesa' : 'Despesa → Receita' }}">
                    <input class="form-check-input entry-type-toggle" 
                           type="checkbox" 
                           id="entry-type-{{ $entryIndex }}"
                           data-entry-index="{{ $entryIndex }}"
                           {{ $isReceitaItem ? 'checked' : '' }} />
                    <label class="form-check-label fw-semibold fs-9 ms-1 entry-type-label text-nowrap {{ $isReceitaItem ? 'text-success' : 'text-danger' }}" 
                           for="entry-type-{{ $entryIndex }}">
                        {{ $isReceitaItem ? 'Entrada' : 'Saída' }}
                    </label>
                </div>
            </div>

            <div class="vr opacity-25 my-2"></div>

            {{-- Conteúdo principal --}}
            <div class="flex-grow-1 py-3 px-3 d-flex flex-column gap-1 overflow-hidden">
                {{-- Linha 1: Fornecedor + badges --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-bold text-gray-900 fs-6 text-truncate entry-fornecedor-name">{{ $fornecedor }}</span>
                    @if($cnpjFormatado)
                        <span class="text-muted fs-9">{{ $cnpjFormatado }}</span>
                    @endif
                    @if($tipoDocumento)
                        <span class="badge badge-light-primary fs-9 py-1">{{ $tipoDocumento }}@if($numeroNf) Nº {{ $numeroNf }}@elseif($numeroDocumento) {{ $numeroDocumento }}@endif</span>
                    @endif
                    @if($isSingleTransaction && $totalItens > 1)
                        <span class="badge badge-light-info fs-9 py-1">{{ $totalItens }} itens</span>
                    @endif
                    @if($isParcelado && $totalParcelas > 1)
                        <span class="badge badge-light-warning fs-9 py-1">{{ $parcelaAtual }}/{{ $totalParcelas }}x</span>
                    @endif
                </div>

                {{-- Linha 2: Data + Pagamento + Categoria + Encargos --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="text-muted fs-8">
                        <i class="fa-regular fa-calendar fs-9 me-1"></i>{{ $dataFormatada }}
                    </span>
                    @if($formaPagamento)
                        <span class="badge badge-light-info fs-9 py-1">{{ $formaPagamento }}</span>
                    @endif
                    <span class="badge badge-light-primary fs-9 py-1">{{ $categoria }}</span>
                    @if($desconto > 0)
                        <span class="fs-9 text-success fw-semibold">-R$ {{ number_format($desconto, 2, ',', '.') }}</span>
                    @endif
                    @if($juros > 0)
                        <span class="fs-9 text-warning fw-semibold">+R$ {{ number_format($juros, 2, ',', '.') }} juros</span>
                    @endif
                    @if($multa > 0)
                        <span class="fs-9 text-danger fw-semibold">+R$ {{ number_format($multa, 2, ',', '.') }} multa</span>
                    @endif
                </div>

                {{-- Linha 3: Descrição IA (opcional, compacta) --}}
                @if($descricaoDetalhada && $descricaoDetalhada !== $fornecedor)
                    <span class="text-gray-500 fs-9 text-truncate d-block entry-ai-description">
                        <i class="fa-solid fa-robot text-primary fs-9 me-1"></i>{{ $descricaoDetalhada }}
                    </span>
                @endif
            </div>

            {{-- Valor + Botão (lado a lado) --}}
            <div class="d-flex align-items-center gap-2 pe-4 py-3 flex-shrink-0">
                <div class="entry-value-badge badge badge-light-{{ $isReceitaItem ? 'success' : 'danger' }} fs-5 fw-bolder px-3 py-2">
                    <span class="entry-value-sign">{{ $isReceitaItem ? '+' : '-' }}</span>R$ {{ number_format($valorItem, 2, ',', '.') }}
                </div>
                <button type="button"
                        class="btn btn-sm entry-create-btn btn-{{ $isReceitaItem ? 'success' : 'danger' }} fw-bold text-nowrap"
                        onclick="createTransaction({{ $entryIndex }}, this.closest('.extracted-entry-card').dataset.entryTipo === 'entrada')"
                        data-bs-toggle="tooltip"
                        title="Criar {{ $isReceitaItem ? 'Receita' : 'Despesa' }}">
                    <i class="fa-solid fa-plus fs-7 me-1"></i>
                    <span class="entry-create-label">Criar {{ $isReceitaItem ? 'Receita' : 'Despesa' }}</span>
                </button>
            </div>

        </div>
    </div>
</div>
