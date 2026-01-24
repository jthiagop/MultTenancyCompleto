{{-- 
    Componente: Formulário de Novo Lançamento
    
    Renderizado como Blade em vez de JavaScript
    Elimina renderFormFromJSON e HTML concatenado
    
    Props:
    - $conciliacao: BankStatement model
    - $transacaoSugerida: Sugestão de conciliação
    - $centrosAtivos: Collection de centros de custo
    - $lps: Collection de lançamentos padrão
    - $formasPagamento: Collection de formas de pagamento
    - $entidade: Entidade financeira
--}}

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
    <input type="hidden" name="tipo" value="{{ $conciliacao->amount > 0 ? 'entrada' : 'saida' }}"
        class="tipo-lancamento">
    <input type="hidden" name="valor" value="{{ $conciliacao->amount }}">
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

    <!-- Descrição -->
    <x-tenant-input name="descricao2" id="descricao_{{ $conciliacao->id }}" label="Descrição"
        placeholder="Ex: PAYMENT - Fulano" value="{{ old('descricao', $conciliacao->memo) }}" required
        class="col-md-6 mb-3" />

    <!-- Centro de Custo -->
    <x-tenant-select name="cost_center_id" id="cost_center_id_{{ $conciliacao->id }}" label="Centro de Custo" required
        :hidePlaceholder="true" class="col-md-6 mb-3">
        @foreach ($centrosAtivos as $centro)
            <option value="{{ $centro->id }}" {{ old('cost_center_id') == $centro->id ? 'selected' : '' }}>
                {{ $centro->name }}
            </option>
        @endforeach
    </x-tenant-select>

    <!-- Lançamento Padrão -->
    <x-tenant-select name="lancamento_padrao_id" id="lancamento_padrao_id_{{ $conciliacao->id }}"
        label="Lançamento Padrão" placeholder="Selecione o Lançamento Padrão" required class="col-md-8 mb-3">
        @foreach ($lps as $lp)
            <option value="{{ $lp->id }}" data-type="{{ $lp->type }}">
                {{ $lp->description }}
            </option>
        @endforeach
    </x-tenant-select>

    <!-- Tipo de Documento / Forma de Pagamento -->
    <x-tenant-select name="tipo_documento" id="tipo_documento_{{ $conciliacao->id }}" label="Forma de pagamento"
        placeholder="Selecione uma forma de pagamento" required class="col-md-4 mb-3">
        @if (isset($formasPagamento))
            @foreach ($formasPagamento as $formaPagamento)
                <option value="{{ $formaPagamento->codigo }}"
                    {{ old('tipo_documento') == $formaPagamento->codigo ? 'selected' : '' }}>
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
        <div class="col-md-12 anexo-container d-none" data-conciliacao-id="{{ $conciliacao->id }}">
            <x-anexos-input name="anexos" :anexosExistentes="[]" :uniqueId="$conciliacao->id" />
        </div>
    </div>
</form>
