@php
    $extratoStats = [
        ['key' => 'receitas_aberto', 'label' => 'Receitas em Aberto (R$)', 'value' => '0,00', 'variant' => 'warning'],
        ['key' => 'receitas_realizadas', 'label' => 'Receitas Realizadas (R$)', 'value' => '0,00', 'variant' => 'success'],
        ['key' => 'despesas_aberto', 'label' => 'Despesas em Aberto (R$)', 'value' => '-0,00', 'variant' => 'danger'],
        ['key' => 'despesas_realizadas', 'label' => 'Despesas Realizadas (R$)', 'value' => '-0,00', 'variant' => 'danger'],
        ['key' => 'total', 'label' => 'Total do período (R$)', 'value' => '0,00', 'variant' => 'primary', 'tooltip' => true],
    ];
@endphp

<x-tenant-datatable-pane
    key="extrato"
    parentId="kt_tab_extrato"
    :active="true"
    tipo="all"
    :stats="$extratoStats"
    :accountOptions="$accountOptions ?? []"
    :showAccountFilter="true"
    :showMoreFilters="true"
/>
