@props([
    'conciliacao',
    'lps',
    'centrosAtivos',
    'entidade',
])

<form id="form-transf-{{ $conciliacao->id }}" action="{{ route('conciliacao.transferir') }}" method="POST">
    @csrf

    {{-- Campos Hidden --}}
    <input type="hidden" name="bank_statement_id" value="{{ $conciliacao->id }}">
    <input type="hidden" name="entidade_origem_id" value="{{ $entidade->id }}">
    <input type="hidden" name="checknum" value="{{ $conciliacao->checknum ?? '' }}">
    <input type="hidden" name="valor" value="{{ abs($conciliacao->amount) }}">
    <input type="hidden" name="data_transferencia" value="{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}">

    {{-- Conta de Destino (Carregada via AJAX) --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="conta-destino-{{ $conciliacao->id }}" class="required form-label fw-semibold">Conta de
                Destino</label>
            <select id="conta-destino-{{ $conciliacao->id }}" name="entidade_destino_id" class="form-select form-select-solid"
                data-control="select2" data-placeholder="Selecione a conta de destino" data-ajax-load="contas"
                data-conciliacao-id="{{ $conciliacao->id }}" data-entidade-origem="{{ $entidade->id }}" required>
                <option value="">Carregando contas...</option>
            </select>
            <div class="form-text">Selecione para onde transferir o valor</div>
        </div>
    </div>

    {{-- Lançamento Padrão --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="lp-transferencia-{{ $conciliacao->id }}" class="required form-label fw-semibold">Lançamento
                Padrão</label>
            <select id="lp-transferencia-{{ $conciliacao->id }}" name="lancamento_padrao_id" class="form-select form-select-solid"
                data-control="select2" data-placeholder="Selecione..." required>
                <option value="">Selecione o lançamento padrão</option>
                @foreach ($lps as $lp)
                    @if (
                        $lp->type === 'ambos' ||
                            str_contains(strtolower($lp->description), 'transferência') ||
                            str_contains(strtolower($lp->description), 'transferencia'))
                        <option value="{{ $lp->id }}" {{ old('lancamento_padrao_id') == $lp->id ? 'selected' : '' }}>
                            {{ $lp->id }} - {{ $lp->description }}
                        </option>
                    @endif
                @endforeach
            </select>
            <div class="form-text">Selecione um lançamento padrão do tipo "Ambos" ou relacionado a transferências</div>
        </div>

        {{-- Centro de Custo --}}
        <div class="col-md-6">
            <label for="cc-transferencia-{{ $conciliacao->id }}" class="form-label fw-semibold">Centro de Custo</label>
            <select id="cc-transferencia-{{ $conciliacao->id }}" name="cost_center_id" class="form-select form-select-solid"
                data-control="select2" data-placeholder="Selecione..." data-allow-clear="true">
                <option value=""></option>
                @foreach ($centrosAtivos as $centro)
                    <option value="{{ $centro->id }}" {{ old('cost_center_id') == $centro->id ? 'selected' : '' }}>
                        {{ $centro->name }}
                    </option>
                @endforeach
            </select>
            @error('cost_center_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    {{-- Descrição --}}
    <div class="row">
        <div class="col-md-12">
            <label for="descricao-transf-{{ $conciliacao->id }}" class="form-label fw-semibold">Descrição</label>
            <textarea id="descricao-transf-{{ $conciliacao->id }}" name="descricao" class="form-control form-control-solid"
                rows="3"
                placeholder="Ex: Transferência automática entre contas - {{ $conciliacao->memo }}">{{ $conciliacao->memo ? 'Transferência: ' . $conciliacao->memo : '' }}</textarea>
        </div>
    </div>
</form>
