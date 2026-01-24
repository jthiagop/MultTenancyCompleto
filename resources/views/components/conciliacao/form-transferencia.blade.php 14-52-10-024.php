@props([
    'conciliacao',
    'transacaoSugerida' => null,
    'lps' => [],
    'centrosAtivos' => [],
    'entidade' => null,
])

<form id="form-transferencia-{{ $conciliacao->id }}"
    action="{{ route('conciliacao.transferir') }}" method="POST">
    @csrf

    <!-- Container onde o formulário será renderizado via JSON -->
    <div id="form-transferencia-container-{{ $conciliacao->id }}"></div>
</form>

<script>
    // Renderiza o formulário quando o componente for carregado
    document.addEventListener('DOMContentLoaded', function() {
        const conciliacaoId = {{ $conciliacao->id }};
        const entidadeOrigemId = {{ $entidade->id }};

        // Estrutura JSON para o formulário "Transferência"
        const formConfigTransferencia = {
            conciliacaoId: conciliacaoId,
            hiddenFields: [
                {
                    name: 'bank_statement_id',
                    value: '{{ $conciliacao->id }}'
                },
                {
                    name: 'entidade_origem_id',
                    value: '{{ $entidade->id }}'
                },
                {
                    name: 'checknum',
                    value: '{{ $conciliacao->checknum ?? '' }}'
                },
                {
                    name: 'valor',
                    value: '{{ abs($conciliacao->amount) }}'
                },
                {
                    name: 'data_transferencia',
                    value: '{{ \Carbon\Carbon::parse($conciliacao->dtposted)->format('Y-m-d') }}'
                }
            ],
            fields: [
                {
                    type: 'select',
                    name: 'entidade_destino_id',
                    id: 'entidade_destino_id_' + conciliacaoId,
                    label: 'Conta de Destino',
                    required: true,
                    col: 'col-md-12',
                    placeholder: 'Selecione a conta de destino',
                    options: [{ value: '', text: 'Carregando contas...' }],
                    helpText: 'Selecione para onde transferir o valor',
                    loadViaAjax: true
                },
                {
                    type: 'select',
                    name: 'lancamento_padrao_id',
                    id: 'lancamento_padrao_id_transferencia_' + conciliacaoId,
                    label: 'Lançamento Padrão',
                    required: true,
                    col: 'col-md-6',
                    placeholder: 'Selecione o lançamento padrão',
                    options: [
                        @foreach ($lps as $lp)
                            @if (
                                $lp->type === 'ambos' ||
                                    str_contains(strtolower($lp->description), 'transferência') ||
                                    str_contains(strtolower($lp->description), 'transferencia'))
                                {
                                    id: {{ $lp->id }},
                                    description: '{{ $lp->id }} - {{ $lp->description }}',
                                    selected: {{ old('lancamento_padrao_id') == $lp->id ? 'true' : 'false' }}
                                }
                                {{ !$loop->last ? ',' : '' }}
                            @endif
                        @endforeach
                    ]
                },
                {
                    type: 'select',
                    name: 'cost_center_id',
                    id: 'cost_center_id_transferencia_' + conciliacaoId,
                    label: 'Centro de Custo',
                    required: false,
                    col: 'col-md-6',
                    placeholder: 'Selecione o Centro de Custo',
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
                    type: 'textarea',
                    name: 'descricao',
                    id: 'descricao_transferencia_' + conciliacaoId,
                    label: 'Descrição',
                    required: false,
                    col: 'col-md-12',
                    rows: 3,
                    value: '{{ $conciliacao->memo ? 'Transferência: ' . $conciliacao->memo : '' }}',
                    placeholder: 'Ex: Transferência automática entre contas - {{ $conciliacao->memo }}',
                    newRow: true
                }
            ]
        };

        // Renderiza o formulário
        renderFormFromJSON(formConfigTransferencia, 'form-transferencia-container-{{ $conciliacao->id }}');

        // Carrega contas quando a aba é aberta (via evento Bootstrap)
        setTimeout(() => {
            const tabTransferencia = document.querySelector(`#transferencia-{{ $conciliacao->id }}-tab`);
            if (tabTransferencia) {
                tabTransferencia.addEventListener('shown.bs.tab', function() {
                    const selectDestino = document.querySelector(`#entidade_destino_id_{{ $conciliacao->id }}`);
                    if (selectDestino) {
                        const optionCount = selectDestino.querySelectorAll('option:not([value=""])').length;
                        if (optionCount === 0) {
                            carregarContasDisponiveisAjax(selectDestino, {{ $conciliacao->id }}, {{ $entidade->id }});
                        }
                    }
                });
            }
        }, 300);
    }, { once: true });
</script>
