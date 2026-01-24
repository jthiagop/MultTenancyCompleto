{{-- 
    Componente: Formulário de Transferência
    
    Renderizado como Blade em vez de JavaScript
    Elimina renderFormFromJSON e HTML concatenado
    
    Props:
    - $conciliacao: BankStatement model
    - $entidade: Entidade financeira (origem)
    - $centrosAtivos: Collection de centros de custo
    - $lps: Collection de lançamentos padrão
--}}

@props([
    'conciliacao',
    'entidade',
    'centrosAtivos' => [],
    'lps' => []
])

<form class="conciliacao-form row"
    data-conciliacao-id="{{ $conciliacao->id }}"
    data-form-type="transferencia"
    action="{{ route('conciliacao.transferir') }}" 
    method="POST">
    @csrf

    <!-- Hidden Fields -->
    <input type="hidden" name="bank_statement_id" value="{{ $conciliacao->id }}">
    <input type="hidden" name="entidade_origem_id" value="{{ $entidade->id }}">
    <input type="hidden" name="checknum" value="{{ $conciliacao->checknum ?? '' }}">
    <input type="hidden" name="valor" value="{{ abs($conciliacao->amount) }}">
    <input type="hidden" name="data_transferencia" value="{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}">

    <!-- Conta de Destino -->
    <div class="col-md-12 mb-3">
        <label for="entidade_destino_id_{{ $conciliacao->id }}" class="required form-label fw-semibold">Conta de Destino</label>
        <select 
            class="form-select form-select-solid entidade-destino-select"
            id="entidade_destino_id_{{ $conciliacao->id }}"
            name="entidade_destino_id"
            data-control="select2"
            data-placeholder="Selecione a conta de destino"
            data-conciliacao-id="{{ $conciliacao->id }}"
            data-entidade-origem-id="{{ $entidade->id }}"
            required>
            <option value="">Carregando contas...</option>
        </select>
        <div class="form-text">Selecione para onde transferir o valor</div>
    </div>

    <!-- Lançamento Padrão -->
    <div class="col-md-6 mb-3">
        <label for="lancamento_padrao_id_transferencia_{{ $conciliacao->id }}" class="required form-label fw-semibold">Lançamento Padrão</label>
        <select 
            class="form-select form-select-solid"
            id="lancamento_padrao_id_transferencia_{{ $conciliacao->id }}"
            name="lancamento_padrao_id"
            data-control="select2"
            data-placeholder="Selecione o lançamento padrão"
            required>
            <option value=""></option>
            @foreach ($lps as $lp)
                @if ($lp->type === 'ambos' || str_contains(strtolower($lp->description), 'transferência') || str_contains(strtolower($lp->description), 'transferencia'))
                    <option value="{{ $lp->id }}">{{ $lp->id }} - {{ $lp->description }}</option>
                @endif
            @endforeach
        </select>
        <div class="form-text">Selecione um lançamento padrão do tipo "Ambos" ou relacionado a transferências</div>
    </div>

    <!-- Centro de Custo -->
    <div class="col-md-6 mb-3">
        <label for="cost_center_id_transferencia_{{ $conciliacao->id }}" class="form-label fw-semibold">Centro de Custo</label>
        <select 
            class="form-select form-select-solid"
            id="cost_center_id_transferencia_{{ $conciliacao->id }}"
            name="cost_center_id"
            data-control="select2"
            data-placeholder="Selecione o Centro de Custo"
            data-allow-clear="true">
            <option value=""></option>
            @foreach ($centrosAtivos as $centro)
                <option value="{{ $centro->id }}">{{ $centro->name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Descrição -->
    <div class="col-md-12 mb-3">
        <label for="descricao_transferencia_{{ $conciliacao->id }}" class="form-label fw-semibold">Descrição</label>
        <textarea 
            class="form-control"
            id="descricao_transferencia_{{ $conciliacao->id }}"
            name="descricao"
            rows="3"
            placeholder="Ex: Transferência automática entre contas - {{ $conciliacao->memo }}">{{ $conciliacao->memo ? 'Transferência: ' . $conciliacao->memo : '' }}</textarea>
    </div>
</form>
