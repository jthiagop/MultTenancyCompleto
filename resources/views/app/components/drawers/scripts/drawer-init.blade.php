<script>
// Script de inicialização do Drawer de Lançamento
(function() {
    // Função para atualizar labels de fornecedor/cliente baseado no tipo
    function updateFornecedorLabels(tipo) {
        if (!tipo) {
            // Tenta obter o tipo dos campos hidden
            var tipoInput = $('#tipo');
            var tipoFinanceiroInput = $('#tipo_financeiro');
            if (tipoInput.length && tipoInput.val()) {
                tipo = tipoInput.val() === 'entrada' ? 'receita' : 'despesa';
            } else if (tipoFinanceiroInput.length && tipoFinanceiroInput.val()) {
                tipo = tipoFinanceiroInput.val();
            } else {
                tipo = 'despesa'; // Default
            }
        }

        // Normaliza o tipo
        if (tipo === 'entrada') tipo = 'receita';
        if (tipo === 'saida') tipo = 'despesa';

        // Define textos baseado no tipo
        var labelText = tipo === 'receita' ? 'Cliente' : 'Fornecedor';
        var placeholderText = tipo === 'receita' ? 'Selecione um cliente' : 'Selecione um fornecedor';
        var buttonText = tipo === 'receita' ? 'Adicionar Cliente' : 'Adicionar Fornecedor';
        var drawerTitle = tipo === 'receita' ? 'Novo Cliente' : 'Novo Fornecedor';

        // Atualiza label do select de fornecedor no card de informações
        var fornecedorSelect = $('#fornecedor_id');
        if (fornecedorSelect.length) {
            // Procura o label de várias formas
            var labelElement = $('label[for="fornecedor_id"]');
            if (labelElement.length === 0) {
                labelElement = fornecedorSelect.closest('.fv-row, .col-md-4, .col-md-6').find('label').first();
            }
            if (labelElement.length === 0) {
                labelElement = fornecedorSelect.closest('.fv-row').prev('label');
            }
            
            if (labelElement.length) {
                // Atualiza o texto do label, preservando elementos como <span class="required">
                var requiredSpan = labelElement.find('span.required');
                var hasRequired = labelElement.hasClass('required') || requiredSpan.length > 0;
                
                if (requiredSpan.length) {
                    // Se tem span.required, atualiza apenas o texto dentro dele
                    requiredSpan.text(labelText);
                } else if (hasRequired) {
                    // Se o label tem classe required mas não tem span, adiciona span
                    labelElement.html('<span class="required">' + labelText + '</span>');
                } else {
                    // Atualiza o texto diretamente
                    labelElement.text(labelText);
                }
            }

            // Atualiza placeholder do select
            fornecedorSelect.attr('data-placeholder', placeholderText);
            // Se o Select2 já foi inicializado, atualiza o placeholder visualmente
            if (fornecedorSelect.hasClass('select2-hidden-accessible')) {
                var $select2Container = fornecedorSelect.next('.select2-container');
                if ($select2Container.length) {
                    var $placeholder = $select2Container.find('.select2-selection__placeholder');
                    if ($placeholder.length && !fornecedorSelect.val()) {
                        $placeholder.text(placeholderText);
                    }
                    // Atualiza também o atributo title do placeholder
                    $placeholder.attr('title', placeholderText);
                }
            }
        }

        // Atualiza título do drawer de fornecedor
        var fornecedorDrawerTitle = $('#fornecedor_drawer_title');
        if (fornecedorDrawerTitle.length === 0) {
            fornecedorDrawerTitle = $('#kt_drawer_fornecedor .card-title h3');
        }
        if (fornecedorDrawerTitle.length) {
            fornecedorDrawerTitle.text(drawerTitle);
        }

        // Armazena o texto do botão para uso posterior
        window.fornecedorButtonText = buttonText;
    }

    // Torna a função acessível globalmente
    window.updateFornecedorLabels = updateFornecedorLabels;

    // Verifica se jQuery está disponível
    function initDrawerScript() {
        if (typeof $ === 'undefined') {
            console.warn('[DrawerInit] jQuery não está disponível. Aguardando...');
            setTimeout(initDrawerScript, 100);
            return;
        }

    $(document).ready(function() {
        var tipoLancamento = null;

    // Função para inicializar/reinicializar Select2 no drawer
    function initDrawerSelect2() {
        var drawer = $('#kt_drawer_lancamento');

        if (!drawer.length) {
            return;
        }

        // Busca todos os selects com data-control="select2"
        var selects = drawer.find('select[data-control="select2"]');

        if (selects.length === 0) {
            return;
        }

        // Verifica se Select2 está disponível
        if (typeof $.fn.select2 === 'undefined') {
            return;
        }

        // Inicializa cada select
        selects.each(function(index) {
            var $select = $(this);
            var selectId = $select.attr('id') || $select.attr('name') || 'select-' + index;


            // Se já foi inicializado, destroi primeiro
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.select2('destroy');
            }

            // Prepara opções
            // Para fornecedor_id, verifica se há um placeholder atualizado baseado no tipo
            var placeholderValue = $select.attr('data-placeholder') || 'Selecione';
            if (selectId === 'fornecedor_id') {
                // Tenta obter o tipo atual para definir o placeholder correto
                var tipoAtual = $('#tipo').val() || $('#tipo_financeiro').val() || 'despesa';
                if (tipoAtual === 'entrada') tipoAtual = 'receita';
                if (tipoAtual === 'saida') tipoAtual = 'despesa';
                placeholderValue = tipoAtual === 'receita' ? 'Selecione um cliente' : 'Selecione um fornecedor';
            }
            
            var options = {
                dropdownParent: drawer,
                placeholder: placeholderValue,
                allowClear: $select.attr('data-allow-clear') === 'true',
                minimumResultsForSearch: $select.attr('data-hide-search') === 'true' ? Infinity : 0,
                width: '100%',
                theme: 'bootstrap5'
            };

            // Adiciona template personalizado para o select de entidade_id (ícones de banco/caixa) 
            if (selectId === 'entidade_id') {
                // Função para formatar opções com ícone
                var formatOptionWithIcon = function(item) {
                    if (!item.id) {
                        return item.text;
                    }

                    var iconUrl = item.element.getAttribute('data-kt-select2-icon');
                    if (!iconUrl) {
                        return item.text;
                    }

                    var span = document.createElement('span');
                    var template = '';
                    template += '<img src="' + iconUrl + '" class="rounded h-20px me-2" alt="icon"/>';
                    template += item.text;
                    span.innerHTML = template;

                    return $(span);
                };

                options.templateSelection = formatOptionWithIcon;
                options.templateResult = formatOptionWithIcon;
            }

            // Inicializa usando jQuery Select2 diretamente
            try {
                $select.select2(options);

                // Adiciona botão "Adicionar Fornecedor" se for o select de fornecedor
                if (selectId === 'fornecedor_id') {

                    // Remove eventos anteriores para evitar duplicação
                    $select.off('select2:open');

                    $select.on('select2:open', function() {

                        setTimeout(function() {
                            var $dropdown = $('.select2-container--open');
                            var $results = $dropdown.find('.select2-results');

                            if ($results.length === 0) {
                                return;
                            }

                            // Remove botão anterior se existir
                            $results.find('.select2-add-fornecedor-footer').remove();

                            // Obtém o texto do botão baseado no tipo atual
                            var tipoAtual = $('#tipo').val() || $('#tipo_financeiro').val() || 'despesa';
                            if (tipoAtual === 'entrada') tipoAtual = 'receita';
                            if (tipoAtual === 'saida') tipoAtual = 'despesa';
                            var buttonText = (window.fornecedorButtonText) ? window.fornecedorButtonText : 
                                           (tipoAtual === 'receita' ? 'Adicionar Cliente' : 'Adicionar Fornecedor');

                            // Adiciona footer com botão
                            var $footer = $(
                                '<div class="select2-add-fornecedor-footer border-top p-2 text-center"></div>'
                            );
                            var $button = $(
                                '<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus"></i> ' + buttonText + '</button>'
                            );
                            $footer.append($button);
                            $results.append($footer);


                            // Evento de clique no botão
                            $button.on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();


                                // Fecha o Select2
                                $select.select2('close');

                                // Obtém o tipo atual do lançamento para atualizar labels
                                var tipoAtual = $('#tipo').val() || $('#tipo_financeiro').val() || 'despesa';
                                if (tipoAtual === 'entrada') tipoAtual = 'receita';
                                if (tipoAtual === 'saida') tipoAtual = 'despesa';
                                
                                // ===== NOVO: Define qual select deve ser atualizado ao salvar =====
                                // Armazena referência do select alvo para atualização após cadastro
                                window.__drawerTargetSelect = '#' + selectId; // #fornecedor_id ou similar
                                
                                // Define o tipo no hidden field do drawer
                                var parceiroTipo = tipoAtual === 'receita' ? 'cliente' : 'fornecedor';
                                $('#parceiro_tipo_hidden').val(parceiroTipo);
                                
                                console.log('[DrawerInit] Abrindo drawer para:', parceiroTipo, '| Select alvo:', window.__drawerTargetSelect);
                                // ===== FIM NOVO =====
                                
                                // Atualiza labels antes de abrir o drawer
                                updateFornecedorLabels(tipoAtual);

                                // Abre o drawer de fornecedor
                                var fornecedorDrawer = document.getElementById('kt_drawer_fornecedor');
                                if (fornecedorDrawer) {
                                    var drawerInstance = KTDrawer.getInstance(fornecedorDrawer);
                                    if (drawerInstance) {
                                        drawerInstance.show();
                                    } else {
                                        // Fallback: tenta criar instância se não existir
                                        if (typeof KTDrawer.getOrCreateInstance === 'function') {
                                            var inst = KTDrawer.getOrCreateInstance(fornecedorDrawer);
                                            if (inst) inst.show();
                                        }
                                    }
                                }
                            });
                        }, 50);
                    });
                }

                // Adiciona botão "Adicionar Configuração de Recorrência" se for o select de configuração de recorrência
                if (selectId === 'configuracao_recorrencia') {

                    // Remove eventos anteriores para evitar duplicação
                    $select.off('select2:open');

                    $select.on('select2:open', function() {

                        setTimeout(function() {
                            var $dropdown = $('.select2-container--open');
                            var $results = $dropdown.find('.select2-results');

                            if ($results.length === 0) {
                                return;
                            }

                            // Remove botão anterior se existir
                            $results.find('.select2-add-recorrencia-footer').remove();

                            // Adiciona footer com botão
                            var $footer = $(
                                '<div class="select2-add-recorrencia-footer border-top p-2 text-center"></div>'
                            );
                            var $button = $(
                                '<button type="button" class="btn btn-sm btn-light-primary w-100"><i class="fas fa-plus"></i> Adicionar Configuração de Recorrência</button>'
                            );
                            $footer.append($button);
                            $results.append($footer);


                            // Evento de clique no botão
                            $button.on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();


                                // Fecha o Select2
                                $select.select2('close');

                                // Abre o drawer de recorrência
                                var recorrenciaDrawer = document.getElementById('kt_drawer_recorrencia');
                                if (recorrenciaDrawer) {
                                    var drawerInstance = KTDrawer.getInstance(recorrenciaDrawer);
                                    if (drawerInstance) {
                                        drawerInstance.show();
                                    }
                                }
                            });
                        }, 50);
                    });
                }
            } catch (error) {
            }
        });

    }

    // Função para carregar configurações de recorrência do banco de dados
    function carregarConfiguracaoRecorrencia() {
        var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');
        
        if (!configuracaoRecorrenciaSelect.length) {
            return;
        }
        
        // Carrega configurações existentes do banco via AJAX
        $.ajax({
            url: '{{ route("recorrencias.index") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    // Remove opções existentes (exceto a vazia)
                    configuracaoRecorrenciaSelect.find('option[value!=""]').remove();
                    
                    // Adiciona cada configuração como opção
                    response.data.forEach(function(config) {
                        var option = $('<option></option>')
                            .attr('value', config.id)
                            .text(config.nome);
                        configuracaoRecorrenciaSelect.append(option);
                    });
                    
                    // Atualiza o Select2
                    if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
                        configuracaoRecorrenciaSelect.trigger('change.select2');
                    }
                }
            },
            error: function(xhr) {
                toastr.error('Erro ao carregar configurações de recorrência', 'Erro');
            }
        });
    }

    // Quando o drawer for aberto via função global
    window.abrirDrawerLancamento = function(tipo, origem) {

        var drawer = $('#kt_drawer_lancamento');
        var form = $('#kt_drawer_lancamento_form');
        var drawerTitle = drawer.find('.card-title').first();
        var tipoFinanceiroInput = $('#tipo_financeiro');
        var tipoInput = $('#tipo');
        var origemInput = $('#origem');

        // Atualiza título baseado no tipo
        if (drawerTitle.length) {
            if (tipo === 'receita') {
                drawerTitle.text('Nova Receita');
                tipoFinanceiroInput.val('receita');
                tipoInput.val('entrada');
                tipoLancamento = 'receita';
            } else if (tipo === 'despesa') {
                drawerTitle.text('Nova Despesa');
                tipoFinanceiroInput.val('despesa');
                tipoInput.val('saida');
                tipoLancamento = 'despesa';
            }
        }

        // Atualiza labels de fornecedor/cliente baseado no tipo
        updateFornecedorLabels(tipo);

        // Atualiza origem
        if (origemInput.length) {
            origemInput.val(origem || 'Banco');
        }

        // Atualiza action do form
        if (origem === 'Caixa') {
            form.attr('action', '{{ route("caixa.store") }}');
        } else {
            form.attr('action', '{{ route("banco.store") }}');
        }

        // Abre o drawer
        var drawerInstance = KTDrawer.getInstance(document.getElementById('kt_drawer_lancamento'));
        if (drawerInstance) {
            drawerInstance.show();

            // Inicializa Select2 após abrir
            setTimeout(function() {
                initDrawerSelect2();

                // Atualiza labels novamente após inicializar Select2 (caso o DOM tenha mudado)
                updateFornecedorLabels(tipo);

                // Adiciona listener para mudanças no campo tipo (caso o usuário mude depois)
                $('#tipo, #tipo_financeiro').on('change', function() {
                    var tipoAtual = $('#tipo').val() || $('#tipo_financeiro').val() || 'despesa';
                    if (tipoAtual === 'entrada') tipoAtual = 'receita';
                    if (tipoAtual === 'saida') tipoAtual = 'despesa';
                    updateFornecedorLabels(tipoAtual);
                });

                // Filtra lançamentos padrão se houver tipo
                if (tipoLancamento) {
                    var lancamentoPadraoSelect = $('#lancamento_padraos_id');
                    var tipoFiltro = tipoLancamento === 'receita' ? 'entrada' : 'saida';

                    // Armazena todas as opções se ainda não foram armazenadas
                    if (!lancamentoPadraoSelect.data('all-options')) {
                        var allOptions = lancamentoPadraoSelect.find('option').clone();
                        lancamentoPadraoSelect.data('all-options', allOptions);
                    }

                    // Remove todas as opções atuais
                    lancamentoPadraoSelect.empty();

                    // Adiciona apenas as opções que correspondem ao tipo
                    var allOptions = lancamentoPadraoSelect.data('all-options');
                    allOptions.each(function() {
                        var $option = $(this).clone();
                        var optionType = $option.data('type');

                        // Adiciona opção vazia ou opções que correspondem ao tipo
                        if ($option.val() === '' || !optionType || optionType === tipoFiltro) {
                            lancamentoPadraoSelect.append($option);
                        }
                    });

                    // Reinicializa o Select2 do lançamento padrão
                    if (lancamentoPadraoSelect.hasClass('select2-hidden-accessible')) {
                        lancamentoPadraoSelect.select2('destroy');
                    }

                    var selectClasses = lancamentoPadraoSelect.attr('class');

                    var select2Options = {
                        dropdownParent: drawer,
                        placeholder: lancamentoPadraoSelect.attr('data-placeholder') || 'Escolha um Lançamento...',
                        allowClear: true,
                        minimumResultsForSearch: 0,
                        width: '100%',
                        theme: 'bootstrap5',
                        selectionCssClass: selectClasses,
                        dropdownCssClass: ''
                    };

                    lancamentoPadraoSelect.select2(select2Options);
                }
            }, 300);
        } else {
        }
    };

    // Controla exibição do select de recorrência
    $('#flexSwitchDefault').on('change', function() {
        var wrapperRecorrencia = $('#configuracao-recorrencia-wrapper');
        var wrapperParcelamento = $('#parcelamento_wrapper');
        var wrapperDiaCobranca = $('#dia_cobranca_wrapper');
        var vencimentoLabel = $('#vencimento').closest('.fv-row').find('label');
        
        // Referências aos accordions
        var accordionPrevisaoPagamento = $('#kt_accordion_previsao_pagamento');
        var accordionInformacoesPagamento = $('#kt_accordion_informacoes_pagamento');
        var accordionParcelas = $('#kt_accordion_parcelas');

        if ($(this).is(':checked')) {
            wrapperRecorrencia.show();
            wrapperParcelamento.hide();
            wrapperDiaCobranca.show();
            vencimentoLabel.text('1º Vencimento');
            $('#checkbox-pago-wrapper').hide();
            $('#checkbox-recebido-wrapper').hide();
            $('#checkbox-agendado-wrapper').hide();
            
            // Desmarca, desabilita e limpa valores dos checkboxes de pagamento
            $('#pago_checkbox').prop('checked', false).attr('disabled', true).val('');
            $('#recebido_checkbox').prop('checked', false).attr('disabled', true).val('');
            $('#agendado_checkbox').prop('checked', false).attr('disabled', true).val('');
            
            // Oculta todos os accordions de pagamento
            accordionPrevisaoPagamento.hide();
            accordionInformacoesPagamento.hide();
            accordionParcelas.hide();
            
            // Inicializa Select2 do dia de cobrança se necessário
            var diaCobrancaSelect = $('#dia_cobranca');
            if (!diaCobrancaSelect.val()) {
                diaCobrancaSelect.val('1');
            }
            
            // Destroi Select2 existente se houver para reinicializar corretamente
            if (diaCobrancaSelect.hasClass('select2-hidden-accessible')) {
                diaCobrancaSelect.select2('destroy');
            }
            
            // Inicializa Select2 sem placeholder
            diaCobrancaSelect.select2({
                dropdownParent: $('#kt_drawer_lancamento'),
                minimumResultsForSearch: 0,
                width: '100%',
                theme: 'bootstrap5',
                placeholder: '' // Placeholder vazio para não mostrar
            });
            
            diaCobrancaSelect.prop('required', true);
            
            // Define a data de vencimento para o próximo mês
            setVencimentoToNextMonth();
            
            // Carrega configurações de recorrência existentes
            carregarConfiguracaoRecorrencia();
            
            // Configura restrição de data no vencimento
            configurarRestricaoDiaCobranca();
        } else {
            wrapperRecorrencia.hide();
            wrapperParcelamento.show();
            wrapperDiaCobranca.hide(); // Oculta dia de cobrança quando desmarcar
            vencimentoLabel.text('Vencimento');
            $('#checkbox-agendado-wrapper').show();
            
            // Reabilita checkboxes ao desativar recorrência
            $('#pago_checkbox').attr('disabled', false);
            $('#recebido_checkbox').attr('disabled', false);
            $('#agendado_checkbox').attr('disabled', false);
            
            // Limpa seleções de recorrência
            $('#configuracao_recorrencia').val(null).trigger('change');
            $('#dia_cobranca').val(null).trigger('change').prop('required', false);
            
            // Limpa dados dos accordions
            limparDadosAccordions();
            
            // Remove restrição de data do vencimento
            removerRestricaoDiaCobranca();
            
            // Mostra accordions apropriados baseado no parcelamento atual
            $('#parcelamento').trigger('change');
        }
    });
    
    // Event listener para mudanças no select de configuração de recorrência
    $(document).on('change', '#configuracao_recorrencia', function() {
        var configId = $(this).val();
        
        if (!configId || configId === '') {
            return;
        }
        
        // Busca detalhes da configuração selecionada
        $.ajax({
            url: '{{ route("recorrencias.show", ":id") }}'.replace(':id', configId),
            method: 'GET',
            success: function(response) {
                if (response.success && response.data) {
                    var frequencia = response.data.frequencia;
                    atualizarDiaCobranca(frequencia);
                }
            },
            error: function(xhr) {
                // Em caso de erro, mantém visualização padrão (mensal)
                atualizarDiaCobranca('mensal');
            }
        });
    });
    
    // Função para atualizar o select Dia de Cobrança baseado na frequência
    function atualizarDiaCobranca(frequencia) {
        var diaCobrancaWrapper = $('#dia_cobranca_wrapper');
        var diaCobrancaSelect = $('#dia_cobranca');
        var diaCobrancaLabel = diaCobrancaWrapper.find('label');
        
if (!diaCobrancaWrapper.length || !diaCobrancaSelect.length) {
            return;
        }
        
        
        switch(frequencia) {
            case 'diario':
                // Oculta o wrapper completamente
                diaCobrancaWrapper.hide();
                break;
                
            case 'mensal':
                // Mostra o wrapper e atualiza para dias da semana
                diaCobrancaWrapper.show();
                diaCobrancaLabel.text('Dia de Cobrança');
                popularOpcoesSelect('mensal', diaCobrancaSelect);
                break;
                
            case 'semanal':
                // Mostra o wrapper e atualiza para dias da semana
                diaCobrancaWrapper.show();
                diaCobrancaLabel.text('Dias para cobrar');
                popularOpcoesSelect('semanal', diaCobrancaSelect);
                break;
                
            case 'anual':
                // Mostra o wrapper para frequência anual
                diaCobrancaWrapper.show();
                diaCobrancaLabel.text('Cobrar sempre no');
                popularOpcoesSelect('anual', diaCobrancaSelect);
                break;
                
            default:
                // Usa o formato mensal como padrão
                diaCobrancaWrapper.show();
                diaCobrancaLabel.text('Dia de Cobrança');
                popularOpcoesSelect('mensal', diaCobrancaSelect);
        }
    }
    
    // Função para popular as opções do select baseado no tipo
    function popularOpcoesSelect(tipo, selectElement) {
        // Destroi Select2 se existir
        if (selectElement.hasClass('select2-hidden-accessible')) {
            selectElement.select2('destroy');
        }
        
        // Limpa opções existentes
        selectElement.empty();
        
        if (tipo === 'mensal') {
            // Adiciona dias do mês (1º ao 30º + último)
            for (var i = 1; i <= 30; i++) {
                selectElement.append('<option value="' + i + '">' + i + 'º dia do mês</option>');
            }
            selectElement.append('<option value="ultimo">Último dia do mês</option>');
            
        } else if (tipo === 'anual') {
            // Adiciona dias do mês para frequência anual (1º ao 30º + último)
            for (var i = 1; i <= 30; i++) {
                selectElement.append('<option value="' + i + '">' + i + 'º dia do Mês</option>');
            }
            selectElement.append('<option value="ultimo">Último dia do mês</option>');
            
        } else if (tipo === 'semanal') {
            // Adiciona dias da semana
            var diasSemana = [
                { valor: 'segunda', nome: 'Segunda-feira' },
                { valor: 'terca', nome: 'Terça-feira' },
                { valor: 'quarta', nome: 'Quarta-feira' },
                { valor: 'quinta', nome: 'Quinta-feira' },
                { valor: 'sexta', nome: 'Sexta-feira' },
                { valor: 'sabado', nome: 'Sábado' },
                { valor: 'domingo', nome: 'Domingo' }
            ];
            
            diasSemana.forEach(function(dia) {
                selectElement.append('<option value="' + dia.valor + '">' + dia.nome + '</option>');
            });
        }
        
        // Seleciona a primeira opção
        selectElement.val(selectElement.find('option:first').val());
        
        // Reinicializa Select2 sem placeholder
        selectElement.select2({
            dropdownParent: $('#kt_drawer_lancamento'),
            minimumResultsForSearch: 0,
            width: '100%',
            theme: 'bootstrap5',
            placeholder: ''
        });
    }
    
    // Event listener para mudanças no dia de cobrança
    $(document).on('change', '#dia_cobranca', function() {
        // Atualiza a data de vencimento para o próximo mês com o novo dia
        setVencimentoToNextMonth();
        
        // Configura restrição de data
        configurarRestricaoDiaCobranca();
    });
    
    // Função para configurar restrição de dia no vencimento baseado no dia de cobrança
    function configurarRestricaoDiaCobranca() {
        var diaCobrancaVal = $('#dia_cobranca').val();
        var vencimentoInput = document.getElementById('vencimento');
        
        if (!diaCobrancaVal || !vencimentoInput) {
            return;
        }
        
        // Destroi flatpickr existente se houver
        if (vencimentoInput._flatpickr) {
            vencimentoInput._flatpickr.destroy();
        }
        
        // Mapeamento de dias da semana
        var diasSemanaMap = {
            'segunda': 1, // Segunda-feira
            'terca': 2,   // Terça-feira
            'quarta': 3,  // Quarta-feira
            'quinta': 4,  // Quinta-feira
            'sexta': 5,   // Sexta-feira
            'sabado': 6,  // Sábado
            'domingo': 0  // Domingo
        };
        
        var flatpickrConfig = {
            enableTime: false,
            dateFormat: "d/m/Y",
            locale: "pt",
            allowInput: true,
            clickOpens: true
        };
        
        // Verifica se é um dia da semana (string) ou dia do mês (número)
        if (diasSemanaMap.hasOwnProperty(diaCobrancaVal)) {
            // É um dia da semana - restringe para esse dia específico
            var diaSemana = diasSemanaMap[diaCobrancaVal];
            flatpickrConfig.enable = [
                function(date) {
                    // Permite apenas datas que correspondam ao dia da semana selecionado
                    return date.getDay() === diaSemana;
                }
            ];
        } else {
            // É um dia do mês - restringe para esse dia específico
            var diaCobranca = parseInt(diaCobrancaVal);
            if (!isNaN(diaCobranca)) {
                flatpickrConfig.enable = [
                    function(date) {
                        // Permite apenas datas que correspondam ao dia de cobrança
                        return date.getDate() === diaCobranca;
                    }
                ];
            }
        }
        
        // Recria flatpickr com restrição
        if (typeof flatpickr !== 'undefined') {
            try {
                flatpickr(vencimentoInput, flatpickrConfig);
            } catch (error) {
                console.error('[DrawerInit] Erro ao inicializar flatpickr:', error);
            }
        }
        
        
        
        // Ajusta o vencimento atual para corresponder ao dia de cobrança
        var vencimentoAtual = typeof $ !== 'undefined' ? $('#vencimento').val() : (vencimentoInput.value || '');
        var dataAjustada = null;
        
        if (vencimentoAtual) {
            var partes = vencimentoAtual.split('/');
            if (partes.length === 3) {
                var diaAtual = parseInt(partes[0]);
                var mesAtual = parseInt(partes[1]) - 1; // JavaScript meses são 0-indexed
                var anoAtual = parseInt(partes[2]);
                
                // Se o dia não corresponde, ajusta para o próximo mês com o dia correto
                if (diaAtual !== diaCobranca) {
                    dataAjustada = new Date(anoAtual, mesAtual, diaCobranca);
                    
                    // Se o dia é inválido para o mês (ex: 31 em fevereiro), pega o último dia do mês
                    if (dataAjustada.getDate() !== diaCobranca) {
                        dataAjustada = new Date(anoAtual, mesAtual + 1, 0); // Último dia do mês
                    }
                }
            }
        } else {
            // Se não há data, define para o dia selecionado do próximo mês
            var hoje = new Date();
            var proximoMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, diaCobranca);
            
            // Se o dia é inválido para o mês, pega o último dia do mês
            if (proximoMes.getDate() !== diaCobranca) {
                proximoMes = new Date(hoje.getFullYear(), hoje.getMonth() + 2, 0);
            }
            
            dataAjustada = proximoMes;
        }
        
        // Aplica a data ajustada
        if (dataAjustada) {
            var diaFormatado = String(dataAjustada.getDate()).padStart(2, '0');
            var mesFormatado = String(dataAjustada.getMonth() + 1).padStart(2, '0');
            var anoFormatado = dataAjustada.getFullYear();
            var dataFormatada = diaFormatado + '/' + mesFormatado + '/' + anoFormatado;
            
            if (typeof $ !== 'undefined') {
                $('#vencimento').val(dataFormatada);
            } else if (vencimentoInput) {
                vencimentoInput.value = dataFormatada;
            }
        }
    }
    
    // Função para remover restrição de dia do vencimento
    function removerRestricaoDiaCobranca() {
        var vencimentoInput = document.getElementById('vencimento');
        
        if (!vencimentoInput) return;
        
        // Destroi flatpickr existente
        if (vencimentoInput._flatpickr) {
            vencimentoInput._flatpickr.destroy();
        }
        
        // Recria flatpickr sem restrições
        if (typeof flatpickr !== 'undefined') {
            try {
                var config = {
                    enableTime: false,
                    dateFormat: "d/m/Y",
                    allowInput: true,
                    clickOpens: true
                };

                // Só adiciona locale se estiver registrado
                if (flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR)) {
                    config.locale = "pt";
                }

                flatpickr(vencimentoInput, config);
            } catch (error) {
                console.error('[DrawerInit] Erro ao inicializar flatpickr:', error);
            }
        }
        
    }
    
    // Função para definir vencimento para o próximo mês
    function setVencimentoToNextMonth() {
        var vencimentoInput = $('#vencimento');
        if (!vencimentoInput.length) return;
        
        // Obter dia de cobrança selecionado (se houver)
        var diaCobranca = $('#dia_cobranca').val() || '1';
        
        // Criar data para o próximo mês
        var hoje = new Date();
        var proximoMes = new Date(hoje.getFullYear(), hoje.getMonth() + 1, 1);
        
        // Se o dia de cobrança for numérico, usar esse dia no próximo mês
        if (!isNaN(diaCobranca) && diaCobranca !== 'ultimo') {
            var diaNum = parseInt(diaCobranca);
            proximoMes.setDate(diaNum);
            
            // Se o dia for inválido para o mês (ex: 31 em fevereiro), pega o último dia do mês
            if (proximoMes.getDate() !== diaNum) {
                proximoMes = new Date(proximoMes.getFullYear(), proximoMes.getMonth() + 1, 0);
            }
        } else if (diaCobranca === 'ultimo') {
            // Último dia do próximo mês
            proximoMes = new Date(proximoMes.getFullYear(), proximoMes.getMonth() + 1, 0);
        }
        
        // Formatar data no padrão brasileiro
        var dia = String(proximoMes.getDate()).padStart(2, '0');
        var mes = String(proximoMes.getMonth() + 1).padStart(2, '0');
        var ano = proximoMes.getFullYear();
        var dataFormatada = dia + '/' + mes + '/' + ano;
        
        // Aplicar a data
        vencimentoInput.val(dataFormatada);
        
        // Se houver flatpickr, atualizar também
        if (vencimentoInput[0] && vencimentoInput[0]._flatpickr) {
            vencimentoInput[0]._flatpickr.setDate(proximoMes, true);
        }
    }
    
    // Função para limpar dados dos accordions de pagamento
    function limparDadosAccordions() {
        // Limpa accordion de previsão de pagamento
        if (typeof $ !== 'undefined') {
            $('#previsao_pagamento').val('');
            $('#juros').val('');
            $('#multa').val('');
            $('#desconto').val('');
            $('#valor_a_pagar').val('');
            
            // Limpa accordion de informações de pagamento
            $('#data_pagamento').val('');
            $('#valor_pago').val('');
            $('#juros_pagamento').val('');
            $('#multa_pagamento').val('');
            $('#desconto_pagamento').val('');
        } else {
            // Fallback sem jQuery
            var previsaoPagamento = document.getElementById('previsao_pagamento');
            var juros = document.getElementById('juros');
            var multa = document.getElementById('multa');
            var desconto = document.getElementById('desconto');
            var valorAPagar = document.getElementById('valor_a_pagar');
            var dataPagamento = document.getElementById('data_pagamento');
            var valorPago = document.getElementById('valor_pago');
            var jurosPagamento = document.getElementById('juros_pagamento');
            var multaPagamento = document.getElementById('multa_pagamento');
            var descontoPagamento = document.getElementById('desconto_pagamento');
            
            if (previsaoPagamento) previsaoPagamento.value = '';
            if (juros) juros.value = '';
            if (multa) multa.value = '';
            if (desconto) desconto.value = '';
            if (valorAPagar) valorAPagar.value = '';
            if (dataPagamento) dataPagamento.value = '';
            if (valorPago) valorPago.value = '';
            if (jurosPagamento) jurosPagamento.value = '';
            if (multaPagamento) multaPagamento.value = '';
            if (descontoPagamento) descontoPagamento.value = '';
        }
        if (typeof $ !== 'undefined') {
            $('#total_pagar_container').hide();
            $('#valor_aberto_container').hide();
            $('#resumo_baixa_tbody').empty();
            
            // Limpa tabela de parcelas
            $('#parcelas_table_body').empty();
        } else {
            // Fallback sem jQuery
            var totalPagarContainer = document.getElementById('total_pagar_container');
            var valorAbertoContainer = document.getElementById('valor_aberto_container');
            var resumoBaixaTbody = document.getElementById('resumo_baixa_tbody');
            var parcelasTableBody = document.getElementById('parcelas_table_body');
            
            if (totalPagarContainer) totalPagarContainer.style.display = 'none';
            if (valorAbertoContainer) valorAbertoContainer.style.display = 'none';
            if (resumoBaixaTbody) resumoBaixaTbody.innerHTML = '';
            if (parcelasTableBody) parcelasTableBody.innerHTML = '';
        }
        
    }

    // Handler para o botão "Adicionar Fornecedor" dentro do select
    $(document).on('click', '#kt_drawer_lancamento [data-kt-drawer-show="kt_drawer_fornecedor"]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Abre o drawer de fornecedor
        var fornecedorDrawer = document.getElementById('kt_drawer_fornecedor');
        if (fornecedorDrawer) {
            var drawerInstance = KTDrawer.getInstance(fornecedorDrawer);
            if (drawerInstance) {
                drawerInstance.show();
            }
        }
    });

    // Handler para o botão "Adicionar Configuração de Recorrência"
    $(document).on('click', '#kt_drawer_lancamento [data-kt-drawer-show="kt_drawer_recorrencia"]', function(e) {
        e.preventDefault();
        e.stopPropagation();

        // Abre o drawer de recorrência
        var recorrenciaDrawer = document.getElementById('kt_drawer_recorrencia');
        if (recorrenciaDrawer) {
            var drawerInstance = KTDrawer.getInstance(recorrenciaDrawer);
            if (drawerInstance) {
                drawerInstance.show();
            }
        }
    });

    // Função para processar o submit do formulário de recorrência
    function processarRecorrenciaDrawer() {
        
        
        var recorrenciaForm = $('#kt_drawer_recorrencia_form');
        var recorrenciaSubmitButton = $('#kt_drawer_recorrencia_submit');
        
        if (!recorrenciaForm.length || !recorrenciaSubmitButton.length) {
            return;
        }
        
        // Desabilita botão de submit
        recorrenciaSubmitButton.attr('data-kt-indicator', 'on');
        recorrenciaSubmitButton.prop('disabled', true);

        // Coleta os dados do formulário
        var intervalo = $('#intervalo_repeticao').val();
        var frequencia = $('#frequencia_recorrencia').val();
        var aposOcorrencias = $('#apos_ocorrencias').val();

        // Remove validações anteriores
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Valida se os campos obrigatórios foram preenchidos
        var hasErrors = false;
        
        if (!intervalo) {
            $('#intervalo_repeticao').addClass('is-invalid');
            $('#intervalo_repeticao').after('<div class="invalid-feedback d-block">Este campo é obrigatório</div>');
            hasErrors = true;
        }
        
        if (!frequencia) {
            $('#frequencia_recorrencia').next('.select2-container').find('.select2-selection').addClass('is-invalid');
            $('#frequencia_recorrencia').next('.select2-container').after('<div class="invalid-feedback d-block">Este campo é obrigatório</div>');
            hasErrors = true;
        }
        
        if (!aposOcorrencias) {
            $('#apos_ocorrencias').addClass('is-invalid');
            $('#apos_ocorrencias').after('<div class="invalid-feedback d-block">Este campo é obrigatório</div>');
            hasErrors = true;
        }
        
        if (hasErrors) {
            // Usa toast ao invés de SweetAlert
            toastr.error('Por favor, preencha todos os campos obrigatórios.', 'Erro de Validação');
            
            // Reabilita botão
            recorrenciaSubmitButton.removeAttr('data-kt-indicator');
            recorrenciaSubmitButton.prop('disabled', false);
            return;
        }

        // Mapeia frequência para texto legível
        var frequenciaText = {
            'diario': 'Dia(s)',
            'semanal': 'Semana(s)',
            'mensal': 'Mês(es)',
            'anual': 'Ano(s)'
        };

        // Cria o texto da configuração
        var configText = 'A cada ' + intervalo + ' ' + (frequenciaText[frequencia] || frequencia) +
            ' - Após ' + aposOcorrencias + ' ocorrências';

        // Busca o select de configuração de recorrência
        var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');

        if (configuracaoRecorrenciaSelect.length) {
            // Cria ID temporário para a configuração
            var tempId = 'temp_' + Date.now();

            // Adiciona nova opção com ID temporário
            var newOption = $('<option></option>')
                .attr('value', tempId)
                .attr('selected', true)
                .attr('data-intervalo', intervalo)
                .attr('data-frequencia', frequencia)
                .attr('data-apos-ocorrencias', aposOcorrencias)
                .text(configText);

            configuracaoRecorrenciaSelect.append(newOption);

            // Atualiza o Select2
            if (configuracaoRecorrenciaSelect.hasClass('select2-hidden-accessible')) {
                configuracaoRecorrenciaSelect.val(tempId).trigger('change.select2');
            } else {
                configuracaoRecorrenciaSelect.val(tempId).trigger('change');
            }
        }

        // Atualiza os campos hidden do formulário principal
        var mainForm = $('#kt_drawer_lancamento_form');
        
        if (mainForm.length) {
            mainForm.find('input[name="intervalo_repeticao"]').remove();
            mainForm.find('input[name="frequencia"]').remove();
            mainForm.find('input[name="apos_ocorrencias"]').remove();
            mainForm.find('input[name="configuracao_recorrencia"]').remove();

            mainForm.append('<input type="hidden" name="intervalo_repeticao" value="' + intervalo + '">');
            mainForm.append('<input type="hidden" name="frequencia" value="' + frequencia + '">');
            mainForm.append('<input type="hidden" name="apos_ocorrencias" value="' + aposOcorrencias + '">');
            mainForm.append('<input type="hidden" name="configuracao_recorrencia_temp" value="' + tempId + '">');
        }

        // Marca o checkbox de repetir e mostra o select (SEM trigger para evitar limpar campos)
        $('#flexSwitchDefault').prop('checked', true);
        
        // Manualmente mostra/oculta elementos sem disparar o evento change
        $('#configuracao-recorrencia-wrapper').show();
        $('#parcelamento_wrapper').hide();
        $('#dia_cobranca_wrapper').show();
        $('#vencimento').closest('.fv-row').find('label').text('1º Vencimento');
        $('#checkbox-pago-wrapper').hide();
        $('#checkbox-recebido-wrapper').hide();
        $('#checkbox-agendado-wrapper').hide();
        
        // Oculta accordions de pagamento
        $('#kt_accordion_previsao_pagamento').hide();
        $('#kt_accordion_informacoes_pagamento').hide();
        $('#kt_accordion_parcelas').hide();

        // Fecha o drawer de recorrência
        var drawerRecorrenciaElement = document.querySelector('#kt_drawer_recorrencia');
        if (drawerRecorrenciaElement && typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerRecorrenciaElement);
            if (drawer) {
                drawer.hide();
            }
        }

        // Limpa o formulário
        recorrenciaForm[0].reset();

        // Reabilita botão de submit
        recorrenciaSubmitButton.removeAttr('data-kt-indicator');
        recorrenciaSubmitButton.prop('disabled', false);

    }

    // Inicializa os event listeners do drawer de recorrência
    function inicializarDrawerRecorrencia() {
        
        var recorrenciaForm = $('#kt_drawer_recorrencia_form');
        var recorrenciaSubmitButton = $('#kt_drawer_recorrencia_submit');
        
        if (!recorrenciaForm.length || !recorrenciaSubmitButton.length) {
            return;
        }
        
        // Remove listeners anteriores para evitar duplicação
        recorrenciaSubmitButton.off('click.recorrencia');
        recorrenciaForm.off('submit.recorrencia');

        // Evento de clique no botão de submit
        recorrenciaSubmitButton.on('click.recorrencia', function(e) {
            e.preventDefault();
            e.stopPropagation();
            processarRecorrenciaDrawer();
            return false;
        });

        // Evento de submit do form
        recorrenciaForm.on('submit.recorrencia', function(e) {
            e.preventDefault();
            e.stopPropagation();
            processarRecorrenciaDrawer();
            return false;
        });
        
    }

    // Adiciona listeners para remover erros ao preencher os campos
    $(document).on('input change', '#intervalo_repeticao, #apos_ocorrencias', function() {
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
    });

    $(document).on('change', '#frequencia_recorrencia', function() {
        $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
        $(this).next('.select2-container').next('.invalid-feedback').remove();
    });

    // Inicializa quando documento estiver pronto
    setTimeout(function() {
        inicializarDrawerRecorrencia();
    }, 500);

    // Reinicializa quando drawer for aberto
    $(document).on('kt.drawer.shown', '#kt_drawer_recorrencia', function() {
        inicializarDrawerRecorrencia();
    });
    
    // Função para garantir estado inicial correto do dia de cobrança
    function garantirEstadoInicialDiaCobranca() {
        var checkboxRecorrencia = $('#flexSwitchDefault');
        var diaCobrancaWrapper = $('#dia_cobranca_wrapper');
        
        if (checkboxRecorrencia.length && diaCobrancaWrapper.length) {
            if (!checkboxRecorrencia.is(':checked')) {
                diaCobrancaWrapper.hide();
            }
        }
    }
    
    // Garante que dia_cobranca_wrapper inicia oculto ao carregar a página
    garantirEstadoInicialDiaCobranca();
    
    // Garante estado correto ao abrir o drawer
    $(document).on('kt.drawer.show', '#kt_drawer_lancamento', function() {
        setTimeout(function() {
            garantirEstadoInicialDiaCobranca();
        }, 100);
    });
    
    // Garante estado correto após drawer estar completamente aberto
    $(document).on('kt.drawer.shown', '#kt_drawer_lancamento', function() {
        setTimeout(function() {
            garantirEstadoInicialDiaCobranca();
        }, 100);
    });
    });
    }

    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDrawerScript);
    } else {
        initDrawerScript();
    }
})();
</script>
