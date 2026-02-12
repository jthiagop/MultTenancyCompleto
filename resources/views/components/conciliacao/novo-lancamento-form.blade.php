@props([
    'conciliacao',
    'transacaoSugerida' => null,
    'centrosAtivos' => [],
    'lps' => [],
    'formasPagamento' => [],
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

    <div class="card-footer">

        <!-- Checkbox: Comprovação Fiscal -->
        <div class="col-md-12">
            <label class="form-check form-switch form-check-custom form-check-solid">
                <input type="hidden" name="comprovacao_fiscal" value="0">
                <input type="checkbox" class="form-check-input comprovacao-fiscal-check" name="comprovacao_fiscal"
                    value="1" data-conciliacao-id="{{ $conciliacao->id }}">
                <span class="form-check-label fw-semibold">Existe comprovação fiscal para este lançamento?</span>
            </label>
        </div>
        <!-- Container de Anexos (inicialmente oculto) -->
        <div class="col-md-12 anexo-container d-none mt-4" data-conciliacao-id="{{ $conciliacao->id }}">
            <x-tenant-file-one 
                name="anexo" 
                id="anexo_{{ $conciliacao->id }}" 
                accept=".pdf,.jpg,.jpeg,.png,.ofx" 
            />
        </div>
    </div>