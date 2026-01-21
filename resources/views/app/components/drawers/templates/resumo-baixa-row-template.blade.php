{{-- Template para linha editável do resumo da baixa --}}
<template id="resumo-baixa-row-template">
    <tr class="resumo-baixa-row">
        <td style="width: 150px;">
            <input type="text"
                class="form-control form-control-sm resumo-data"
                name="resumo_baixa[][data_pagamento]"
                placeholder="dd/mm/yyyy"
                data-resumo-input="data">
        </td>
        <td>
            <select class="form-select form-select-sm resumo-forma-pagamento"
                name="resumo_baixa[][forma_pagamento_id]"
                data-resumo-input="forma_pagamento"
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
            <select class="form-select form-select-sm resumo-conta"
                name="resumo_baixa[][conta_id]"
                data-resumo-input="conta"
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
        <td class="text-end">
            <span class="resumo-valor-display"></span>
            <input type="hidden" class="resumo-valor-hidden" name="resumo_baixa[][valor]">
        </td>
        <td class="text-end">
            <span class="resumo-juros-multa-display"></span>
            <input type="hidden" class="resumo-juros-multa-hidden" name="resumo_baixa[][juros_multa]">
        </td>
        <td class="text-end">
            <span class="resumo-desconto-display"></span>
            <input type="hidden" class="resumo-desconto-hidden" name="resumo_baixa[][desconto]">
        </td>
        <td>
            <span class="resumo-situacao-badge"></span>
            <input type="hidden" class="resumo-situacao-hidden" name="resumo_baixa[][situacao]">
        </td>
    </tr>
</template>
