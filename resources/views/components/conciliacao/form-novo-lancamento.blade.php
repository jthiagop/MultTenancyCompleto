@props([
    'conciliacao',
    'lps',
    'centrosAtivos',
    'entidade',
    'transacaoSugerida' => null,
])

<div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row h-5 p-5 mb-10">
    <div class="d-flex flex-column">
        <span class="fs-6 fw-bold">Lançamento não encontrado automaticamente:</span>
        <span class="fs-6">Crie um novo ao alimentar o formulário e clicando no botão conciliar.</span>
    </div>
    <button type="button" class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto"
        data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<form id="form-novo-{{ $conciliacao->id }}" class="row"
    action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
    method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Campos Hidden --}}
    <input type="hidden" name="tipo" value="{{ $conciliacao->amount > 0 ? 'entrada' : 'saida' }}" class="tipo-lancamento">
    <input type="hidden" name="valor" value="{{ $conciliacao->amount }}">
    <input type="hidden" name="data_competencia" value="{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}">
    <input type="hidden" name="numero_documento" value="{{ $conciliacao->checknum }}">
    <input type="hidden" name="descricao" value="{{ $conciliacao->memo }}">
    <input type="hidden" name="origem" value="Conciliação Bancária">
    <input type="hidden" name="entidade_id" value="{{ $entidade->id }}">
    <input type="hidden" name="bank_statement_id" value="{{ $conciliacao->id }}">
    @if ($transacaoSugerida)
        <input type="hidden" name="transacao_financeira_id" value="{{ $transacaoSugerida->id }}">
        <input type="hidden" name="valor_conciliado" value="{{ $transacaoSugerida->valor }}">
    @endif

    {{-- Descrição --}}
    <div class="col-md-6">
        <label for="descricao-{{ $conciliacao->id }}" class="required form-label fw-semibold">Descrição</label>
        <input type="text" id="descricao-{{ $conciliacao->id }}" name="descricao2" class="form-control"
            value="{{ old('descricao', $conciliacao->memo) }}" placeholder="Ex: PAYMENT - Fulano" required>
        @error('descricao')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Centro de Custo --}}
    <div class="col-md-6">
        <label for="cost-center-{{ $conciliacao->id }}" class="required form-label fw-semibold">Centro de Custo</label>
        <select id="cost-center-{{ $conciliacao->id }}" name="cost_center_id" class="form-select form-select-solid"
            data-control="select2" data-placeholder="Selecione..." data-allow-clear="true" required>
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

    {{-- Lançamento Padrão e Tipo Documento --}}
    <div class="col-md-8">
        <label for="lancamento-padrao-{{ $conciliacao->id }}" class="required form-label fw-semibold">Lançamento
            Padrão</label>
        <select id="lancamento-padrao-{{ $conciliacao->id }}" name="lancamento_padrao_id" class="form-select form-select-solid"
            data-control="select2" placeholder="Selecione..." required>
            <option value="">Selecione o Lançamento Padrão</option>
            @foreach ($lps as $lp)
                <option value="{{ $lp->id }}" data-type="{{ $lp->type }}">{{ $lp->description }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label for="tipo-documento-{{ $conciliacao->id }}" class="required form-label fw-semibold">Tipo do
            Documento</label>
        <select id="tipo-documento-{{ $conciliacao->id }}" name="tipo_documento" class="form-select form-select-solid"
            data-control="select2" required>
            <option value="">Selecione...</option>
            <option value="Pix" {{ old('tipo_documento') == 'Pix' ? 'selected' : '' }}>Pix</option>
            <option value="OUTR - Dafe" {{ old('tipo_documento') == 'OUTR - Dafe' ? 'selected' : '' }}>OUTR - Dafe
            </option>
            <option value="NF - Nota Fiscal" {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'selected' : '' }}>NF -
                Nota Fiscal</option>
            <option value="CF - Cupom Fiscal" {{ old('tipo_documento') == 'CF - Cupom Fiscal' ? 'selected' : '' }}>CF -
                Cupom Fiscal</option>
            <option value="DANF - Danfe" {{ old('tipo_documento') == 'DANF - Danfe' ? 'selected' : '' }}>DANF - Danfe
            </option>
            <option value="BOL - Boleto" {{ old('tipo_documento') == 'BOL - Boleto' ? 'selected' : '' }}>BOL - Boleto
            </option>
            <option value="REP - Repasse" {{ old('tipo_documento') == 'REP - Repasse' ? 'selected' : '' }}>REP - Repasse
            </option>
            <option value="CCRD - Cartão de Credito" {{ old('tipo_documento') == 'CCRD - Cartão de Credito' ? 'selected' : '' }}>CCRD - Cartão de Credito</option>
            <option value="CDBT - Cartão de Debito" {{ old('tipo_documento') == 'CDBT - Cartão de Debito' ? 'selected' : '' }}>CDBT - Cartão de Debito</option>
            <option value="CH - Cheque" {{ old('tipo_documento') == 'CH - Cheque' ? 'selected' : '' }}>CH - Cheque
            </option>
            <option value="REC - Recibo" {{ old('tipo_documento') == 'REC - Recibo' ? 'selected' : '' }}>REC - Recibo
            </option>
            <option value="CARN - Carnê" {{ old('tipo_documento') == 'CARN - Carnê' ? 'selected' : '' }}>CARN - Carnê
            </option>
            <option value="FAT - Fatura" {{ old('tipo_documento') == 'FAT - Fatura' ? 'selected' : '' }}>FAT - Fatura
            </option>
            <option value="APOL - Apólice" {{ old('tipo_documento') == 'APOL - Apólice' ? 'selected' : '' }}>APOL -
                Apólice</option>
            <option value="DUPL - Duplicata" {{ old('tipo_documento') == 'DUPL - Duplicata' ? 'selected' : '' }}>DUPL -
                Duplicata</option>
            <option value="TRIB - Tribunal" {{ old('tipo_documento') == 'TRIB - Tribunal' ? 'selected' : '' }}>TRIB -
                Tribunal</option>
            <option value="Outros" {{ old('tipo_documento') == 'Outros' ? 'selected' : '' }}>Outros</option>
            <option value="T Banc - Transferência Bancaria" {{ old('tipo_documento') == 'T Banc - Transferência Bancaria' ? 'selected' : '' }}>T Banc - Transferência Bancaria</option>
        </select>
    </div>

    {{-- Checkbox Comprovação Fiscal --}}
    <div class="col-md-12 p-0 m-0 mb-5">
        <div class="d-flex flex-row align-items-center">
            <div class="me-5">
                <label class="fs-6 fw-semibold form-label mb-0">Existe comprovação fiscal para o lançamento?</label>
            </div>
            <label class="form-check form-switch form-check-custom form-check-solid mb-0">
                <input type="hidden" name="comprovacao_fiscal" value="0">
                <input type="checkbox" id="checkbox-fiscal-{{ $conciliacao->id }}" name="comprovacao_fiscal"
                    class="form-check-input toggle-anexos" data-target="#anexos-container-{{ $conciliacao->id }}" value="1">
                <span class="form-check-label fw-semibold text-muted">Possui Nota?</span>
            </label>
        </div>
    </div>

    {{-- Container Anexos (Escondido por padrão) --}}
    <div id="anexos-container-{{ $conciliacao->id }}" class="col-md-12" style="display: none;">
        <x-anexos-input name="anexos" :anexosExistentes="[]" :uniqueId="$conciliacao->id" />
    </div>
</form>
