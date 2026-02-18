<script>
(function() {
    function initSuggestions() {
        if (typeof jQuery === 'undefined') {
            setTimeout(initSuggestions, 100);
            return;
        }

        const $ = jQuery;
        
        const form = $('#kt_drawer_lancamento_form');
        const parceiroSelect = $('#fornecedor_id');
        const descricaoInput = $('#descricao');
        const valorInput = $('#valor2');
        
        const categoriaSelect = $('#lancamento_padraos_id');
        const costCenterSelect = $('#cost_center_id');
        const tipoDocumentoSelect = $('#tipo_documento');

        let isFetching = false;

        function fetchSuggestion() {
            const parceiroId = parceiroSelect.val();
            const descricao = descricaoInput.val();
            const valor = valorInput.val();

            if (!parceiroId && !descricao) return;
            if (isFetching) return;

            isFetching = true;

            $.ajax({
                url: '{{ route("banco.sugestao") }}',
                method: 'GET',
                data: {
                    parceiro_id: parceiroId,
                    descricao: descricao,
                    valor: valor
                },
                success: function(sugestao) {
                    if (sugestao.confianca >= 50) {
                        applySuggestion(sugestao);
                    }
                },
                error: function(xhr) {
                },

                complete: function() {
                    isFetching = false;
                }
            });
        }

        function applySuggestion(sugestao) {
            let tooltipText = '💡 Sugestão automática (' + (sugestao.confianca || 0) + '% de confiança)';
            
            if (sugestao.origem_sugestao === 'regra') {
                tooltipText = '🤖 Sugestão baseada em regra aprendida (' + sugestao.confianca + '% de confiança)';
            } else if (sugestao.origem_sugestao && sugestao.origem_sugestao.startsWith('historico')) {
                tooltipText = '🕒 Sugestão baseada em transações anteriores (' + sugestao.confianca + '% de confiança)';
            }
            
            // Categoria
            if (sugestao.lancamento_padrao_id && !categoriaSelect.val()) {
                categoriaSelect.val(sugestao.lancamento_padrao_id).trigger('change');
                registerStar('lancamento_padraos_id', sugestao.lancamento_padrao_id, tooltipText);
            }

            // Centro de Custo
            if (sugestao.cost_center_id && !costCenterSelect.val()) {
                costCenterSelect.val(sugestao.cost_center_id).trigger('change');
                registerStar('cost_center_id', sugestao.cost_center_id, tooltipText);
            }

            // Tipo de Documento / Forma de Pagamento
            if (sugestao.tipo_documento && !tipoDocumentoSelect.val()) {
                tipoDocumentoSelect.val(sugestao.tipo_documento).trigger('change');
                registerStar('tipo_documento', sugestao.tipo_documento, tooltipText);
            }

            // Valor
            if (sugestao.valor && !valorInput.val()) {
                valorInput.val(sugestao.valor).trigger('change');
                registerStar('valor2', sugestao.valor, tooltipText);
            }

            // Descrição
            if (sugestao.descricao && (!descricaoInput.val() || sugestao.origem_sugestao === 'regra')) {
                 if (sugestao.origem_sugestao === 'regra') {
                     descricaoInput.val(sugestao.descricao).trigger('change');
                     registerStar('descricao', sugestao.descricao, tooltipText);
                 } else if (!descricaoInput.val()) {
                     descricaoInput.val(sugestao.descricao).trigger('change');
                     registerStar('descricao', sugestao.descricao, tooltipText);
                 }
            }
        }

        function registerStar(elementId, value, tooltip) {
            if (window.suggestionStarManager) {
                window.suggestionStarManager.addStar(elementId, value, tooltip);
            }
        }

        // Listeners
        parceiroSelect.on('change', function() {
            fetchSuggestion();
        });

        // Debounce para descrição
        let debounceTimer;
        descricaoInput.on('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(fetchSuggestion, 1000);
        });
    }

    // Inicializa quando o drawer for mostrado
    function attachToDrawer() {
        const drawerEl = document.getElementById('kt_drawer_lancamento');
        if (drawerEl) {
            if (typeof KTDrawer !== 'undefined') {
                const drawer = KTDrawer.getInstance(drawerEl);
                if (drawer) {
                    drawer.on('kt.drawer.shown', function() {
                        initSuggestions();
                    });
                    // Caso já esteja aberto
                    if (drawer.isShown && drawer.isShown()) {
                        initSuggestions();
                    }
                } else {
                    setTimeout(attachToDrawer, 100);
                }
            } else {
                setTimeout(attachToDrawer, 100);
            }
        } else {
            // Se o elemento ainda não existe
            setTimeout(attachToDrawer, 500);
        }
    }

    // Inicializa de forma segura
    function safeInit() {
        if (typeof jQuery !== 'undefined') {
            attachToDrawer();
        } else {
            // Se jQuery não estiver disponível, aguarda o carregamento da página
            window.addEventListener('load', function() {
                if (typeof jQuery !== 'undefined') {
                    attachToDrawer();
                }
            });
        }
    }

    safeInit();
})();
</script>
