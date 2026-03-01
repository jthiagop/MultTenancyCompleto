@props([
    'conciliacao',
    'transacaoSugerida' => null,
    'centrosAtivos' => [],
    'lps' => [],
    'formasPagamento' => [],
    'fornecedores' => [],
    'entidade',
])

<form class="conciliacao-form row" data-conciliacao-id="{{ $conciliacao->id }}" data-form-type="novo-lancamento"
    action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}" method="POST"
    enctype="multipart/form-data">
    @csrf

    <!-- Hidden Fields -->
    {{-- ✅ CORREÇÃO: amount negativo = pagamento (saída), positivo = recebimento (entrada) --}}
    <input type="hidden" name="tipo" value="{{ $conciliacao->amount >= 0 ? 'entrada' : 'saida' }}"
        class="tipo-lancamento">
    {{-- ✅ Valor sempre positivo para o controller (o tipo já define entrada/saída) --}}
    <input type="hidden" name="valor" value="{{ abs($conciliacao->amount) }}">
    <input type="hidden" name="data_competencia"
        value="{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}">
    <input type="hidden" name="numero_documento" value="{{ $conciliacao->checknum }}">
    <input type="hidden" name="descricao" value="{{ $conciliacao->memo }}">
    <input type="hidden" name="origem" value="Conciliação Bancária">
    <input type="hidden" name="entidade_id" value="{{ $entidade->id }}">
    <input type="hidden" name="bank_statement_id" value="{{ $conciliacao->id }}">

    @if ($transacaoSugerida)
        <input type="hidden" name="transacao_financeira_id" value="{{ $transacaoSugerida->id }}">
        <input type="hidden" name="valor_conciliado" value="{{ $transacaoSugerida->valor }}">
    @endif

    @php
        $sugestao = $conciliacao->sugestao ?? null;
    @endphp

    <!-- Descrição -->
    <x-tenant-input name="descricao2" id="descricao_{{ $conciliacao->id }}" label="Descrição"
        placeholder="Ex: PAYMENT - Fulano" 
        value="{{ old('descricao', $sugestao['descricao_sugerida'] ?? $conciliacao->memo) }}" required
        class="col-md-6 mb-3" />

    <!-- Centro de Custo -->
    <x-tenant-select name="cost_center_id" id="cost_center_id_{{ $conciliacao->id }}" label="Centro de Custo" required
        :hidePlaceholder="true" class="col-md-6 mb-3">
        @foreach ($centrosAtivos as $centro)
            <option value="{{ $centro->id }}" 
                {{ (old('cost_center_id') == $centro->id || ($sugestao && isset($sugestao['cost_center_id']) && $sugestao['cost_center_id'] == $centro->id)) ? 'selected' : '' }}>
                {{ $centro->name }}
            </option>
        @endforeach
    </x-tenant-select>

    <!-- Lançamento Padrão -->
    @php
        $sugestao = $conciliacao->sugestao ?? null;
        $badgeClass = '';
        $badgeIcon = '';
        $badgeText = '';
        $tooltipText = '';
        $temSugestao = $sugestao && $sugestao['confianca'] > 0;
        
        // DEBUG: Log para verificar sugestão
        if ($sugestao) {
            \Log::info('🔍 [BLADE] Sugestão encontrada', [
                'conciliacao_id' => $conciliacao->id,
                'confianca' => $sugestao['confianca'] ?? 'NULL',
                'origem' => $sugestao['origem_sugestao'] ?? 'NULL',
                'lancamento_padrao_id' => $sugestao['lancamento_padrao_id'] ?? 'NULL',
                'tipo_documento' => $sugestao['tipo_documento'] ?? 'NULL',
                'temSugestao' => $temSugestao ? 'TRUE' : 'FALSE',
            ]);
        } else {
            \Log::warning('⚠️ [BLADE] Nenhuma sugestão encontrada', [
                'conciliacao_id' => $conciliacao->id,
            ]);
        }
        
        if ($temSugestao) {
            if ($sugestao['origem_sugestao'] === 'regra') {
                $badgeClass = 'badge-success';
                $badgeIcon = '🤖';
                $badgeText = 'Regra Aprendida';
                $tooltipText = '🤖 Sugestão automática baseada em regra aprendida (' . number_format($sugestao['confianca'], 0) . '% de confiança)';
            } elseif ($sugestao['origem_sugestao'] === 'historico') {
                $badgeClass = 'badge-warning';
                $badgeIcon = '🕒';
                $badgeText = 'Baseado no Histórico';
                $tooltipText = '🕒 Sugestão baseada em transações similares anteriores (' . number_format($sugestao['confianca'], 0) . '% de confiança)';
            } else {
                $badgeClass = 'badge-secondary';
                $badgeIcon = '💡';
                $badgeText = 'Sugestão por Padrão';
                $tooltipText = '💡 Sugestão baseada em padrões detectados na descrição (' . number_format($sugestao['confianca'], 0) . '% de confiança)';
            }
        }
    @endphp
    
    @php
        // Determinar o tipo da transação com base no amount do OFX
        $tipoTransacao = $conciliacao->amount >= 0 ? 'entrada' : 'saida';

        // Filtrar LPs compatíveis (mesmo tipo ou "ambos") e ordenar por descrição
        $lpsFiltered = $lps
            ->filter(fn($lp) => $lp->type === $tipoTransacao || $lp->type === 'ambos')
            ->sortBy('description')
            ->values();
    @endphp

    <x-tenant-select name="lancamento_padrao_id" id="lancamento_padrao_id_{{ $conciliacao->id }}"
        label="Lançamento Padrão" placeholder="Selecione o Lançamento Padrão" required class="col-md-7 mb-3"
        :showSuggestionStar="$temSugestao"
        :suggestionTooltip="$tooltipText"
        :suggestedValue="$temSugestao && isset($sugestao['lancamento_padrao_id']) ? $sugestao['lancamento_padrao_id'] : null">
        @if ($temSugestao)
            <div class="mb-2">
                <span class="badge {{ $badgeClass }}" data-bs-toggle="tooltip" 
                      title="{{ $badgeText }} ({{ number_format($sugestao['confianca'], 0) }}% de confiança)">
                    {{ $badgeIcon }} Sugestão: {{ $sugestao['confianca'] }}%
                </span>
            </div>
        @endif
        @foreach ($lpsFiltered as $idx => $lp)
            <option value="{{ $lp->id }}" data-type="{{ $lp->type }}"
                {{ $sugestao && isset($sugestao['lancamento_padrao_id']) && $sugestao['lancamento_padrao_id'] == $lp->id ? 'selected' : '' }}>
                {{ $idx + 1 }}. {{ $lp->description }}
            </option>
        @endforeach
    </x-tenant-select>

    <!-- Tipo de Documento / Forma de Pagamento -->
    <x-tenant-select name="tipo_documento" id="tipo_documento_{{ $conciliacao->id }}" label="Forma de pagamento"
        placeholder="Selecione uma forma de pagamento" required class="col-md-5 mb-3"
        :showSuggestionStar="$temSugestao && isset($sugestao['tipo_documento'])"
        :suggestionTooltip="$tooltipText"
        :suggestedValue="$temSugestao && isset($sugestao['tipo_documento']) ? $sugestao['tipo_documento'] : null">
        @if (isset($formasPagamento))
            @foreach ($formasPagamento as $formaPagamento)
                <option value="{{ $formaPagamento->codigo }}"
                    {{ (old('tipo_documento') == $formaPagamento->codigo || ($sugestao && isset($sugestao['tipo_documento']) && $sugestao['tipo_documento'] == $formaPagamento->codigo)) ? 'selected' : '' }}>
                    {{ $formaPagamento->id }} - {{ $formaPagamento->nome }}
                </option>
            @endforeach
        @endif
    </x-tenant-select>

    <!--begin::Informações complementares (collapsible Metronic)-->
    <div class="py-1">
        <!--begin::Header-->
        <div class="py-3 d-flex flex-stack flex-wrap">
            <!--begin::Toggle-->
            <div class="d-flex align-items-center collapsible toggle collapsed" 
                 data-bs-toggle="collapse" 
                 data-bs-target="#collapse_info_extra_{{ $conciliacao->id }}">
                <!--begin::Arrow-->
                <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                    <!--begin::Svg Icon | path: icons/duotune/general/gen036.svg (toggle-on = minus)-->
                    <span class="svg-icon toggle-on svg-icon-primary svg-icon-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                            <rect x="6.0104" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <!--begin::Svg Icon | path: icons/duotune/general/gen035.svg (toggle-off = plus)-->
                    <span class="svg-icon toggle-off svg-icon-2">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.3" x="2" y="2" width="20" height="20" rx="5" fill="currentColor" />
                            <rect x="10.8891" y="17.8033" width="12" height="2" rx="1" transform="rotate(-90 10.8891 17.8033)" fill="currentColor" />
                            <rect x="6.01041" y="10.9247" width="12" height="2" rx="1" fill="currentColor" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                </div>
                <!--end::Arrow-->
                <!--begin::Summary-->
                <div class="me-3">
                    <div class="d-flex align-items-center fw-bold text-gray-800">
                        Completar informações
                        <span class="badge badge-light-primary ms-3 fs-8">Opcional</span>
                    </div>
                    <div class="text-muted fs-7">
                        {{ $tipoTransacao === 'saida' ? 'Fornecedor' : 'Cliente' }}, nº documento, comprovação fiscal e anexos
                    </div>
                </div>
                <!--end::Summary-->
            </div>
            <!--end::Toggle-->
        </div>
        <!--end::Header-->
        <!--begin::Body-->
        <div id="collapse_info_extra_{{ $conciliacao->id }}" class="collapse fs-6 ps-10" data-conciliacao-id="{{ $conciliacao->id }}">
            <!--begin::Campos complementares-->
            <div class="row g-5 py-5">
                <!--begin::Fornecedor/Cliente (contextual ao tipo)-->
                @php
                    $tipoLabel = $tipoTransacao === 'saida' ? 'Fornecedor' : 'Cliente';
                    $tipoPlaceholder = $tipoTransacao === 'saida' ? 'Selecione um fornecedor' : 'Selecione um cliente';
                    $naturezaPermitida = $tipoTransacao === 'saida' ? ['fornecedor', 'ambos'] : ['cliente', 'ambos'];
                @endphp
                <x-tenant-select 
                    name="fornecedor_id" 
                    id="fornecedor_id_{{ $conciliacao->id }}" 
                    :label="$tipoLabel"
                    :placeholder="$tipoPlaceholder"
                    :minimumResultsForSearch="0"
                    control="manual"
                    class="col-md-8 mb-3">
                    @if (isset($fornecedores) && count($fornecedores) > 0)
                        @foreach ($fornecedores as $fornecedor)
                            @if(in_array($fornecedor->natureza, $naturezaPermitida))
                                <option value="{{ $fornecedor->id }}" data-natureza="{{ $fornecedor->natureza }}">
                                    {{ $fornecedor->nome }}
                                </option>
                            @endif
                        @endforeach
                    @endif
                </x-tenant-select>
                <!--end::Fornecedor/Cliente-->

                <!--begin::Número do Documento-->
                <x-tenant-input 
                    name="numero_documento_extra" 
                    id="numero_documento_{{ $conciliacao->id }}" 
                    label="Nº Documento"
                    placeholder="Ex: 1234567890" 
                    type="text" 
                    value="{{ $conciliacao->checknum }}"
                    class="col-md-4 mb-3" />
                <!--end::Número do Documento-->

                <!--begin::Separator-->
                <div class="col-md-12">
                    <div class="separator separator-dashed my-3"></div>
                </div>
                <!--end::Separator-->

                <!--begin::Comprovação Fiscal-->
                <div class="col-md-12">
                    <label class="form-check form-switch form-check-custom form-check-solid">
                        <input type="hidden" name="comprovacao_fiscal" value="0">
                        <input type="checkbox" class="form-check-input comprovacao-fiscal-check" name="comprovacao_fiscal"
                            value="1" data-conciliacao-id="{{ $conciliacao->id }}">
                        <span class="form-check-label fw-semibold text-gray-800">Existe comprovação fiscal para este lançamento?</span>
                    </label>
                </div>
                <!--end::Comprovação Fiscal-->

                <!--begin::Container de Anexos (inicialmente oculto)-->
                <div class="col-md-12 anexo-container d-none mt-4" data-conciliacao-id="{{ $conciliacao->id }}">
                    <x-tenant-file-one 
                        name="anexo" 
                        id="anexo_{{ $conciliacao->id }}" 
                        accept=".pdf,.jpg,.jpeg,.png,.ofx" 
                    />
                </div>
                <!--end::Container de Anexos-->
            </div>
            <!--end::Campos complementares-->
        </div>
        <!--end::Body-->
    </div>
    <!--end::Informações complementares-->



    