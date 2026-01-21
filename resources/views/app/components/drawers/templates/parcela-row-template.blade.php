{{-- Template para linha de parcela (oculto, será clonado pelo JavaScript) --}}
<template id="parcela-row-template">
    <tr data-parcela="">
        <td class="parcela-numero"></td>
        <td style="width: 150px;">
            <input type="text"
                class="form-control form-control-sm parcela-vencimento"
                name="parcelas[][vencimento]"
                placeholder="dd/mm/yyyy"
                data-parcela-input="vencimento"
                data-parcela-num="">
        </td>
        <td style="width: 150px;">
            <div class="input-group input-group-sm">
                <span class="input-group-text">R$</span>
                <input type="text"
                    class="form-control parcela-valor"
                    name="parcelas[][valor]"
                    placeholder="0,00"
                    data-parcela-input="valor"
                    data-parcela-num="">
            </div>
        </td>
        <td style="width: 150px;">
            <input type="text"
                class="form-control form-control-sm parcela-percentual"
                name="parcelas[][percentual]"
                placeholder="0,00"
                data-parcela-input="percentual"
                data-parcela-num="">
        </td>
        <td>
            <select class="form-select form-select-sm parcela-forma-pagamento"
                name="parcelas[][forma_pagamento_id]"
                data-parcela-input="forma_pagamento"
                data-parcela-num=""
                data-control="select2"
                data-placeholder="Selecione"
                data-allow-clear="true"
                data-minimum-results-for-search="0"
                data-dropdown-parent="#kt_drawer_lancamento">
                <option value="">Selecione</option>
                @if (isset($formasPagamento))
                    @foreach ($formasPagamento as $formaPagamento)
                        <option value="{{ $formaPagamento->id }}">{{ $formaPagamento->id }} - {{ $formaPagamento->nome }}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm parcela-conta-pagamento"
                name="parcelas[][conta_pagamento_id]"
                data-parcela-input="conta_pagamento"
                data-parcela-num=""
                data-control="select2"
                data-placeholder="Selecione"
                data-allow-clear="true"
                data-minimum-results-for-search="0"
                data-dropdown-parent="#kt_drawer_lancamento">
                <option value="">Selecione</option>
                @if (isset($entidadesBanco))
                    @foreach ($entidadesBanco as $entidade)
                        <option value="{{ $entidade->id }}">{{ $entidade->agencia }} - {{ $entidade->conta }}</option>
                    @endforeach
                @endif
            </select>
        </td>
        <td>
            <input type="text"
                class="form-control form-control-sm parcela-descricao"
                name="parcelas[][descricao]"
                placeholder="Descrição"
                data-parcela-input="descricao"
                data-parcela-num=""
                data-descricao-base="">
        </td>
        <td>
            <div class="form-check form-check-custom form-check-solid">
                <input class="form-check-input parcela-agendado"
                    type="checkbox"
                    name="parcelas[][agendado]"
                    value="1"
                    data-parcela-input="agendado"
                    data-parcela-num="">
                <label class="form-check-label">
                    Agendado
                </label>
            </div>
        </td>
    </tr>
</template>
