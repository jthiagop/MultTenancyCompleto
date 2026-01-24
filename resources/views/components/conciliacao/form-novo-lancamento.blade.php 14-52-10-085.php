@props([
    'conciliacao',
    'transacaoSugerida' => null,
    'lps' => [],
    'centrosAtivos' => [],
    'entidade' => null,
])

<form id="{{ $conciliacao->id }}" class="row"
    action="{{ $transacaoSugerida ? route('conciliacao.pivot') : route('conciliacao.conciliar') }}"
    method="POST" enctype="multipart/form-data">
    @csrf

    <!-- Alert de informação -->
    <div class="alert alert-dismissible bg-light-primary border border-primary border-dashed d-flex flex-column flex-sm-row h-5 p-5 mb-10">
        <div class="d-flex flex-column">
            <span class="fs-6 fw-bold">Lançamento não encontrado automaticamente:</span>
            <span class="fs-6">Crie um novo ao alimentar o formulário e clicando no botão conciliar.</span>
        </div>
        <button type="button"
            class="btn-close position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 ms-sm-auto"
            data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Container onde o formulário será renderizado via JSON -->
    <div id="form-container-{{ $conciliacao->id }}"></div>

    <!-- Container de anexos (aparece quando checkbox é marcado) -->
    <div class="col-md-12" id="anexoInputContainer_{{ $conciliacao->id }}" style="display: none;">
        <x-anexos-input name="anexos" :anexosExistentes="[]" :uniqueId="$conciliacao->id" />
    </div>
</form>

<script>
    // Renderiza o formulário quando o componente for carregado
    document.addEventListener('DOMContentLoaded', function() {
        const conciliacaoId = {{ $conciliacao->id }};

        // Estrutura JSON para o formulário "Novo Lançamento"
        const formConfigNovoLancamento = {
            conciliacaoId: conciliacaoId,
            hiddenFields: [
                {
                    name: 'tipo',
                    value: '{{ $conciliacao->amount > 0 ? 'entrada' : 'saida' }}',
                    class: 'tipo-lancamento'
                },
                {
                    name: 'valor',
                    value: '{{ $conciliacao->amount }}'
                },
                {
                    name: 'data_competencia',
                    value: '{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}'
                },
                {
                    name: 'numero_documento',
                    value: '{{ $conciliacao->checknum }}'
                },
                {
                    name: 'descricao',
                    value: '{{ $conciliacao->memo }}'
                },
                {
                    name: 'origem',
                    value: 'Conciliação Bancária'
                },
                {
                    name: 'entidade_id',
                    value: '{{ $entidade->id }}'
                },
                {
                    name: 'bank_statement_id',
                    value: '{{ $conciliacao->id }}'
                }
                @if ($transacaoSugerida)
                    , {
                        name: 'transacao_financeira_id',
                        value: '{{ $transacaoSugerida->id }}'
                    }, {
                        name: 'valor_conciliado',
                        value: '{{ $transacaoSugerida->valor }}'
                    }
                @endif
            ],
            fields: [
                {
                    type: 'text',
                    name: 'descricao2',
                    id: 'descricao_' + conciliacaoId,
                    label: 'Descrição',
                    required: true,
                    col: 'col-md-6',
                    value: '{{ old('descricao', $conciliacao->memo) }}',
                    placeholder: 'Ex: PAYMENT - Fulano',
                    hasError: {{ $errors->has('descricao') ? 'true' : 'false' }},
                    error: '{{ $errors->first('descricao') }}'
                },
                {
                    type: 'select',
                    name: 'cost_center_id',
                    id: 'cost_center_id_' + conciliacaoId,
                    label: 'Centro de Custo',
                    required: true,
                    col: 'col-md-6',
                    allowClear: true,
                    options: [
                        @foreach ($centrosAtivos as $centro)
                            {
                                id: {{ $centro->id }},
                                name: '{{ $centro->name }}',
                                selected: {{ old('cost_center_id') == $centro->id ? 'true' : 'false' }}
                            }
                            {{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    hasError: {{ $errors->has('cost_center_id') ? 'true' : 'false' }},
                    error: '{{ $errors->first('cost_center_id') }}'
                },
                {
                    type: 'select',
                    name: 'lancamento_padrao_id',
                    id: 'lancamento_padrao_id_' + conciliacaoId,
                    label: 'Lançamento Padrão',
                    required: true,
                    col: 'col-md-8',
                    placeholder: 'Selecione o Lançamento Padrão',
                    options: [
                        @foreach ($lps as $lp)
                            {
                                id: {{ $lp->id }},
                                description: '{{ $lp->description }}',
                                dataType: '{{ $lp->type }}'
                            }
                            {{ !$loop->last ? ',' : '' }}
                        @endforeach
                    ],
                    class: 'lancamento_padrao_banco'
                },
                {
                    type: 'select',
                    name: 'tipo_documento',
                    id: 'tipo_documento_' + conciliacaoId,
                    label: 'Tipo do Documento',
                    required: true,
                    col: 'col-md-4',
                    options: [
                        { value: 'Pix', text: 'Pix', selected: {{ old('tipo_documento') == 'Pix' ? 'true' : 'false' }} },
                        { value: 'OUTR - Dafe', text: 'OUTR - Dafe', selected: {{ old('tipo_documento') == 'OUTR - Dafe' ? 'true' : 'false' }} },
                        { value: 'NF - Nota Fiscal', text: 'NF - Nota Fiscal', selected: {{ old('tipo_documento') == 'NF - Nota Fiscal' ? 'true' : 'false' }} },
                        { value: 'CF - Cupom Fiscal', text: 'CF - Cupom Fiscal', selected: {{ old('tipo_documento') == 'CF - Cupom Fiscal' ? 'true' : 'false' }} },
                        { value: 'DANF - Danfe', text: 'DANF - Danfe', selected: {{ old('tipo_documento') == 'DANF - Danfe' ? 'true' : 'false' }} },
                        { value: 'BOL - Boleto', text: 'BOL - Boleto', selected: {{ old('tipo_documento') == 'BOL - Boleto' ? 'true' : 'false' }} },
                        { value: 'REP - Repasse', text: 'REP - Repasse', selected: {{ old('tipo_documento') == 'REP - Repasse' ? 'true' : 'false' }} },
                        { value: 'CCRD - Cartão de Credito', text: 'CCRD - Cartão de Credito', selected: {{ old('tipo_documento') == 'CCRD - Cartão de Credito' ? 'true' : 'false' }} },
                        { value: 'CDBT - Cartão de Debito', text: 'CDBT - Cartão de Debito', selected: {{ old('tipo_documento') == 'CDBT - Cartão de Debito' ? 'true' : 'false' }} },
                        { value: 'CH - Cheque', text: 'CH - Cheque', selected: {{ old('tipo_documento') == 'CH - Cheque' ? 'true' : 'false' }} },
                        { value: 'REC - Recibo', text: 'REC - Recibo', selected: {{ old('tipo_documento') == 'REC - Recibo' ? 'true' : 'false' }} },
                        { value: 'CARN - Carnê', text: 'CARN - Carnê', selected: {{ old('tipo_documento') == 'CARN - Carnê' ? 'true' : 'false' }} },
                        { value: 'FAT - Fatura', text: 'FAT - Fatura', selected: {{ old('tipo_documento') == 'FAT - Fatura' ? 'true' : 'false' }} },
                        { value: 'APOL - Apólice', text: 'APOL - Apólice', selected: {{ old('tipo_documento') == 'APOL - Apólice' ? 'true' : 'false' }} },
                        { value: 'DUPL - Duplicata', text: 'DUPL - Duplicata', selected: {{ old('tipo_documento') == 'DUPL - Duplicata' ? 'true' : 'false' }} },
                        { value: 'TRIB - Tribunal', text: 'TRIB - Tribunal', selected: {{ old('tipo_documento') == 'TRIB - Tribunal' ? 'true' : 'false' }} },
                        { value: 'Outros', text: 'Outros', selected: {{ old('tipo_documento') == 'Outros' ? 'true' : 'false' }} },
                        { value: 'T Banc - Transferência Bancaria', text: 'T Banc - Transferência Bancaria', selected: {{ old('tipo_documento') == 'T Banc - Transferência Bancaria' ? 'true' : 'false' }} }
                    ]
                },
                {
                    type: 'checkbox',
                    name: 'comprovacao_fiscal',
                    id: 'comprovacaoFiscalCheckbox_' + conciliacaoId,
                    label: 'Existe comprovação fiscal para o lançamento?',
                    checkboxLabel: 'Possui Nota?',
                    col: 'col-md-12',
                    conditional: 'anexos',
                    newRow: true
                }
            ]
        };

        // Renderiza o formulário
        renderFormFromJSON(formConfigNovoLancamento, 'form-container-{{ $conciliacao->id }}');
    }, { once: true });
</script>
