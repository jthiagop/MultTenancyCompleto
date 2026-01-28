<script>
// Script para atualizar labels dinamicamente baseado no tipo de transação
$(document).ready(function() {
    /**
     * Atualiza labels do formulário baseado no tipo (entrada/saida)
     */
    function atualizarLabelsPorTipo() {
        var tipoSelect = $('#tipo');
        if (!tipoSelect.length) return;
        
        var tipo = tipoSelect.val();
        var isReceita = (window.normalizeTipo ? window.normalizeTipo(tipo) : tipo) === 'receita';
        
        console.log('[Labels Dinâmicos] Tipo:', tipo, '| É Receita:', isReceita);
        
        // 1. Atualiza label do checkbox "Pago/Recebido"
        var checkboxLabel = $('label[for="pago_checkbox"]');
        if (checkboxLabel.length) {
            checkboxLabel.text(isReceita ? 'Recebido' : 'Pago');
            console.log('[Labels Dinâmicos] Label checkbox atualizado para:', isReceita ? 'Recebido' : 'Pago');
        }
        
        // 2. Atualiza título do accordion de pagamento
        var accordionButton = $('#kt_accordion_informacoes_pagamento_button');
        if (accordionButton.length) {
            var novoTitulo = isReceita ? 'Informações de Recebimento' : 'Informações de Pagamento';
            accordionButton.find('.accordion-title').text(novoTitulo);
            console.log('[Labels Dinâmicos] Título accordion atualizado para:', novoTitulo);
        }
        
        // 3. Atualiza placeholders dos campos de pagamento
        var dataPagamento = $('#data_pagamento');
        if (dataPagamento.length) {
            dataPagamento.attr('placeholder', isReceita ? 'Data do recebimento' : 'Data do pagamento');
        }
        
        var valorPago = $('#valor_pago');
        if (valorPago.length) {
            valorPago.attr('placeholder', isReceita ? 'Valor recebido' : 'Valor pago');
        }
        
        // 4. Atualiza labels dos campos de juros/multa/desconto
        $('label[for="juros_pagamento"]').text(isReceita ? 'Juros recebidos' : 'Juros pagos');
        $('label[for="multa_pagamento"]').text(isReceita ? 'Multa recebida' : 'Multa paga');
        $('label[for="desconto_pagamento"]').text(isReceita ? 'Desconto concedido' : 'Desconto recebido');
    }
    
    // Event listener para mudança no select de tipo
    $(document).on('change', '#tipo', function() {
        atualizarLabelsPorTipo();
        // Sincroniza checkboxes com o tipo
        if (typeof toggleCheckboxesByTipo === 'function') {
            toggleCheckboxesByTipo();
        }
    });
    
    // Atualiza quando o drawer é aberto
    $(document).on('shown.bs.drawer', '#kt_drawer_lancamento', function() {
        setTimeout(function() {
            atualizarLabelsPorTipo();
            // Sincroniza checkboxes com o tipo
            if (typeof toggleCheckboxesByTipo === 'function') {
                toggleCheckboxesByTipo();
            }
        }, 100);
    });
    
    // Atualiza na inicialização
    setTimeout(function() {
        atualizarLabelsPorTipo();
        // Sincroniza checkboxes com o tipo
        if (typeof toggleCheckboxesByTipo === 'function') {
            toggleCheckboxesByTipo();
        }
    }, 500);
});
</script>
