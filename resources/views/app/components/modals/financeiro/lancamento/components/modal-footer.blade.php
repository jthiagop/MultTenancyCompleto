@php
    $formId = 'kt_drawer_lancamento_form';
    $cancelId = 'kt_drawer_lancamento_cancel';
    $submitId = 'kt_drawer_lancamento_submit';
    $cloneId = 'kt_drawer_lancamento_clone';
    $novoId = 'kt_drawer_lancamento_novo';
    $containerId = 'kt_drawer_lancamento';

    $splitItems = [
        ['id' => $cloneId, 'text' => 'Salvar e Clonar'],
        ['id' => $novoId,  'text' => 'Salvar e Limpar'],
    ];
@endphp

<!--begin::Footer-->
<div class="modal-footer">
<div class="d-flex justify-content-between align-items-center w-100">
        <!-- Lado Esquerdo: Botão Cancelar -->
        <div class="d-flex">
            <x-tenant-button
                type="button"
                id="{{ $cancelId }}"
                variant="light"
                size="sm"
                icon="fas fa-times"
                iconPosition="left"
                data-kt-drawer-dismiss="true"
            >
                Cancelar
            </x-tenant-button>
        </div>
        
        <!-- Lado Direito: Botão Salvar -->
        <div class="d-flex">
            <x-tenant-split-button
                submitId="{{ $submitId }}"
                submitText="Salvar"
                submitIcon="fas fa-save"
                variant="primary"
                size="sm"
                direction="dropup"
                :items="$splitItems"
            />
        </div>
    </div>
</div>
<!--end::Footer-->

@push('scripts')
<script>
/**
 * Limpa o formulário de lançamento - versão simplificada e direta
 * @param {string} drawerId
 */
function limparFormularioLancamento(drawerId) {
    const drawerEl = document.getElementById(drawerId);
    if (!drawerEl) return;

    console.log('🧹 [Modal-Footer] Iniciando limpeza completa do formulário...');

    // Lista completa de selects a serem limpos
    const selectIds = [
        'entidade_id',
        'lancamento_padraos_id',
        'tipo_documento',
        'fornecedor_id',
        'cost_center_id',
        'parcelamento',
        'configuracao_recorrencia',
        'dia_cobranca'
    ];

    // Se tiver Select2, reseta com trigger('change')
    if (typeof window.$ !== 'undefined' && $.fn.select2) {
        selectIds.forEach((id) => {
            const el = drawerEl.querySelector(`#${id}`);
            if (el && $(el).data('select2')) {
                // Reset completo do Select2
                $(el).val(null).trigger('change');
            } else if (el) {
                // Fallback: limpa o valor diretamente
                el.value = '';
                el.selectedIndex = 0;
            }
        });
    } else {
        // fallback sem Select2
        selectIds.forEach((id) => {
            const el = drawerEl.querySelector(`#${id}`);
            if (el) {
                el.value = '';
                el.selectedIndex = 0;
            }
        });
    }
    
    // Limpa campos de input e textarea
    const inputs = drawerEl.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"], input[type="date"], textarea');
    inputs.forEach(input => {
        input.value = '';
    });
    
    // Desmarca checkboxes
    const checkboxes = drawerEl.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Restaura campos hidden para valores padrão
    const tipoInput = drawerEl.querySelector('#tipo');
    const tipoFinanceiroInput = drawerEl.querySelector('#tipo_financeiro');
    const statusPagamentoInput = drawerEl.querySelector('#status_pagamento');
    const origemInput = drawerEl.querySelector('#origem');
    
    if (tipoInput) tipoInput.value = '';
    if (tipoFinanceiroInput) tipoFinanceiroInput.value = '';
    if (statusPagamentoInput) statusPagamentoInput.value = 'em aberto';
    if (origemInput) origemInput.value = 'Banco';

    // Esconde accordions e wrappers (escopo no drawer)
    [
        'kt_accordion_previsao_pagamento',
        'kt_accordion_parcelas', 
        'kt_accordion_informacoes_pagamento',
        'checkboxes-entrada-wrapper',
        'checkboxes-saida-wrapper',
        'checkbox-pago-wrapper',
        'checkbox-recebido-wrapper',
        'configuracao-recorrencia-wrapper',
        'dia_cobranca_wrapper'
    ].forEach((id) => {
        const el = drawerEl.querySelector(`#${id}`);
        if (el) el.style.display = 'none';
    });

    // Limpa tabelas dinâmicas (escopo no drawer)
    const parcelasBody = drawerEl.querySelector('#parcelas_table_body, #parcelas_tbody');
    if (parcelasBody) parcelasBody.innerHTML = '';
    
    const resumoBody = drawerEl.querySelector('#resumo_baixa_tbody');
    if (resumoBody) resumoBody.innerHTML = '';
    
    // Esconde estrelas de sugestão
    const stars = drawerEl.querySelectorAll('.suggestion-star-wrapper');
    stars.forEach(star => star.style.display = 'none');

    console.log('✅ [Modal-Footer] Limpeza completa do formulário concluída');
}

// Event listener específico para o botão cancelar (SEM SWEETALERT)
$(document).ready(function() {
    $(document).on('click', '#{{ $cancelId }}', function() {
        console.log('🚫 [Modal-Footer] Botão cancelar clicado - executando limpeza direta');
        
        // Executa limpeza imediatamente, sem confirmação
        limparFormularioLancamento('{{ $containerId }}');
        
        // O drawer será fechado pelo data-kt-drawer-dismiss="true"
    });
    
    // Mantém o event listener para quando o drawer for fechado por outros meios
    $(document).on('kt.drawer.hide', '#kt_drawer_lancamento', function() {
        setTimeout(function() {
            console.log('🎯 [Modal-Footer] Drawer fechado - executando limpeza preventiva');
            limparFormularioLancamento('kt_drawer_lancamento');
        }, 50); // Reduzido de 150ms para 50ms para ser mais rápido
    });
});
</script>
@endpush
