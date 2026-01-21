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
<div class="card-footer py-4">
    <div class="d-flex justify-content-end gap-3 flex-wrap">
        <x-tenant-button
            type="reset"
            id="{{ $cancelId }}"
            variant="light"
            size="sm"
            icon="fas fa-times"
            iconPosition="left"
            :confirm="true"
            confirmText="Tem certeza de que deseja cancelar?"
            confirmIcon="warning"
            confirmButtonText="Sim, cancelar!"
            cancelButtonText="Não, voltar"
            :resetForm="true"
            formId="{{ $formId }}"
            onConfirm="limparFormularioLancamento('{{ $containerId }}')"
        >
            Cancelar
        </x-tenant-button>

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
<!--end::Footer-->

@push('scripts')
<script>
/**
 * Limpa o formulário de lançamento e fecha o drawer
 * @param {string} drawerId
 */
function limparFormularioLancamento(drawerId) {
    const drawerEl = document.getElementById(drawerId);
    if (!drawerEl) return;

    // Limpa selects APENAS dentro do drawer (evita conflito de IDs em outras telas)
    const selectIds = [
        'entidade_id',
        'lancamento_padraos_id',
        'tipo_documento',
        'fornecedor_id',
        'cost_center_id',
        'parcelamento',
        'configuracao_recorrencia'
    ];

    // Se tiver Select2, reseta com trigger('change')
    if (typeof window.$ !== 'undefined' && $.fn.select2) {
        selectIds.forEach((id) => {
            const el = drawerEl.querySelector(`#${id}`);
            if (el && $(el).data('select2')) {
                // Só faz trigger se o Select2 estiver inicializado
                $(el).val(null).trigger('change');
            } else if (el) {
                // Fallback: limpa o valor diretamente
                el.value = '';
            }
        });
    } else {
        // fallback sem Select2
        selectIds.forEach((id) => {
            const el = drawerEl.querySelector(`#${id}`);
            if (el) el.value = '';
        });
    }

    // Esconde accordions (escopo no drawer)
    ['kt_accordion_previsao_pagamento', 'kt_accordion_parcelas', 'kt_accordion_informacoes_pagamento']
        .forEach((id) => {
            const el = drawerEl.querySelector(`#${id}`);
            if (el) el.style.display = 'none';
        });

    // Limpa tabela de parcelas (escopo no drawer)
    const parcelasBody = drawerEl.querySelector('#parcelas_table_body');
    if (parcelasBody) parcelasBody.innerHTML = '';

    // Fecha o drawer
    const drawer = window.KTDrawer?.getInstance(drawerEl);
    drawer?.hide();
}

// Event listener para limpar o formulário quando o drawer for fechado
$(document).on('kt.drawer.hide', '#kt_drawer_lancamento', function() {
    const formEl = document.getElementById('kt_drawer_lancamento_form');
    if (formEl) {
        // Reseta o formulário
        formEl.reset();
        
        // Limpa os selects com Select2
        limparFormularioLancamento('kt_drawer_lancamento');
    }
});
</script>
@endpush
