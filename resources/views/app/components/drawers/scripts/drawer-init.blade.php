<script>
// Script de inicialização do Drawer de Lançamento
(function() {
    /**
     * Normaliza tipos de transação
     * @param {string} raw - entrada, saida, receita, despesa
     * @returns {string} receita ou despesa
     */
    function normalizeTipo(raw) {
        if (!raw) return 'despesa';
        if (raw === 'entrada') return 'receita';
        if (raw === 'saida') return 'despesa';
        return raw;
    }
    
    // Torna acessível globalmente
    window.normalizeTipo = normalizeTipo;

    // Função para atualizar labels de fornecedor/cliente baseado no tipo
    function updateFornecedorLabels(tipo) {
        if (!tipo) {
            // Tenta obter o tipo dos campos hidden
            var tipoInput = $('#tipo');
            var tipoFinanceiroInput = $('#tipo_financeiro');
            if (tipoInput.length && tipoInput.val()) {
                tipo = normalizeTipo(tipoInput.val());
            } else if (tipoFinanceiroInput.length && tipoFinanceiroInput.val()) {
                tipo = normalizeTipo(tipoFinanceiroInput.val());
            } else {
                tipo = 'despesa'; // Default
            }
        }

        // Normaliza o tipo
        tipo = normalizeTipo(tipo);

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
                var tipoAtual = normalizeTipo($('#tipo').val() || $('#tipo_financeiro').val());
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
                            var tipoAtual = normalizeTipo($('#tipo').val() || $('#tipo_financeiro').val());
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
                                var tipoAtual = normalizeTipo($('#tipo').val() || $('#tipo_financeiro').val());
                                
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
        console.log('🎯 [Drawer-Init] Abrindo drawer para tipo:', tipo);
        
        // LIMPEZA PREVENTIVA: Sempre limpa antes de abrir
        if (typeof limparFormularioDrawerCompleto === 'function') {
            limparFormularioDrawerCompleto();
        }

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
        
        // CORREÇÃO PROFISSIONAL: Sempre inicializa o estado visual após definir o tipo
        setTimeout(function() {
            inicializarEstadoDrawer();
        }, 50);

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
                
                // CORREÇÃO PROFISSIONAL: Reinicializa estado visual após Select2 estar pronto
                inicializarEstadoDrawer();

                // Adiciona listener para mudanças no campo tipo (caso o usuário mude depois)
                $('#tipo, #tipo_financeiro')
                  .off('change.drawerLanc')
                  .on('change.drawerLanc', function () {
                    var tipoAtual = normalizeTipo($('#tipo').val() || $('#tipo_financeiro').val());
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

    /**
     * Abre o drawer para edição de uma transação existente
     * @param {number} transacaoId - ID da transação a ser editada
     */
    window.abrirDrawerEdicao = function(transacaoId) {
        console.log('✏️ [Drawer-Init] Abrindo drawer para edição. ID:', transacaoId);
        
        // LIMPEZA PREVENTIVA: Sempre limpa antes de abrir
        if (typeof limparFormularioDrawerCompleto === 'function') {
            limparFormularioDrawerCompleto();
        }
        
        var drawer = $('#kt_drawer_lancamento');
        var form = $('#kt_drawer_lancamento_form');
        var drawerTitle = drawer.find('.card-title').first();
        
        // Exibe loading
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Carregando...',
                text: 'Buscando dados da transação',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }
        
        // Busca dados da transação via AJAX
        $.ajax({
            url: '{{ route("banco.dados-edicao", ":id") }}'.replace(':id', transacaoId),
            method: 'GET',
            success: function(response) {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                
                if (response.success && response.data) {
                    var dados = response.data;
                    
                    console.log('📦 [Drawer-Init] Dados recebidos:', dados);
                    
                    // Define modo edição
                    $('#transacao_id').val(dados.id);
                    $('#_method').val('PUT');
                    
                    // Define o tipo
                    var tipo = dados.tipo_financeiro || (dados.tipo === 'entrada' ? 'receita' : 'despesa');
                    tipoLancamento = tipo;
                    $('#tipo_financeiro').val(tipo);
                    $('#tipo').val(dados.tipo);
                    
                    // Atualiza título
                    var tituloTexto = tipo === 'receita' ? 'Editar Receita' : 'Editar Despesa';
                    drawerTitle.text(tituloTexto + ' #' + dados.id);
                    
                    // Atualiza action do form para update
                    var origem = dados.origem || 'Banco';
                    $('#origem').val(origem);
                    
                    if (origem === 'Caixa') {
                        form.attr('action', '{{ route("caixa.update", ":id") }}'.replace(':id', transacaoId));
                    } else {
                        form.attr('action', '{{ route("banco.update", ":id") }}'.replace(':id', transacaoId));
                    }
                    
                    // Abre o drawer primeiro
                    var drawerInstance = KTDrawer.getInstance(document.getElementById('kt_drawer_lancamento'));
                    if (drawerInstance) {
                        drawerInstance.show();
                        
                        // Preenche os campos após o drawer abrir
                        setTimeout(function() {
                            preencherFormularioEdicao(dados);
                            initDrawerSelect2();
                            updateFornecedorLabels(tipo);
                            inicializarEstadoDrawer();
                        }, 300);
                    }
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Erro ao carregar dados da transação', 'Erro');
                    }
                }
            },
            error: function(xhr) {
                if (typeof Swal !== 'undefined') {
                    Swal.close();
                }
                
                console.error('❌ [Drawer-Init] Erro ao buscar dados:', xhr);
                
                if (typeof toastr !== 'undefined') {
                    toastr.error('Erro ao carregar dados da transação', 'Erro');
                }
            }
        });
    };
    
    /**
     * Converte data do formato Y-m-d para d/m/Y (flatpickr brasileiro)
     * @param {string} dateStr - Data no formato Y-m-d (ex: 2026-02-03)
     * @returns {string} Data no formato d/m/Y (ex: 03/02/2026)
     */
    function formatDateToBR(dateStr) {
        if (!dateStr) return '';
        // Se já está no formato brasileiro, retorna como está
        if (dateStr.includes('/')) return dateStr;
        // Converte de Y-m-d para d/m/Y
        var parts = dateStr.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return dateStr;
    }
    
    // Torna acessível globalmente
    window.formatDateToBR = formatDateToBR;
    
    /**
     * Preenche o formulário com os dados da transação para edição
     * @param {object} dados - Dados da transação
     */
    function preencherFormularioEdicao(dados) {
        console.log('📝 [Drawer-Init] Preenchendo formulário com dados:', dados);
        
        // Campos de texto simples
        $('#descricao').val(dados.descricao || '');
        $('#numero_documento').val(dados.numero_documento || '');
        $('#historico_complementar').val(dados.historico_complementar || '');
        
        // Campos de valor (com máscara)
        var valorInput = $('#valor2');
        if (valorInput.length) {
            valorInput.val(dados.valor || '');
        }
        
        // Campos de data - converte para formato brasileiro (d/m/Y)
        var dataCompetencia = formatDateToBR(dados.data_competencia);
        var dataVencimento = formatDateToBR(dados.data_vencimento);
        var dataPagamento = formatDateToBR(dados.data_pagamento);
        
        // Preenche campos de data - verifica se tem flatpickr
        var dataCompetenciaInput = $('[name="data_competencia"]');
        if (dataCompetenciaInput.length && dataCompetenciaInput[0]._flatpickr) {
            dataCompetenciaInput[0]._flatpickr.setDate(dataCompetencia, true, 'd/m/Y');
        } else {
            dataCompetenciaInput.val(dataCompetencia);
        }
        
        var dataVencimentoInput = $('[name="data_vencimento"], #vencimento');
        if (dataVencimentoInput.length && dataVencimentoInput[0]._flatpickr) {
            dataVencimentoInput[0]._flatpickr.setDate(dataVencimento, true, 'd/m/Y');
        } else {
            dataVencimentoInput.val(dataVencimento);
        }
        
        var dataPagamentoInput = $('[name="data_pagamento"]');
        if (dataPagamentoInput.length && dataPagamentoInput[0]._flatpickr) {
            dataPagamentoInput[0]._flatpickr.setDate(dataPagamento, true, 'd/m/Y');
        } else {
            dataPagamentoInput.val(dataPagamento);
        }
        
        // Selects - precisam ser tratados após Select2 inicializar
        setTimeout(function() {
            // Entidade Financeira
            if (dados.entidade_id) {
                $('#entidade_id').val(dados.entidade_id).trigger('change');
            }
            
            // Lançamento Padrão (Categoria)
            if (dados.lancamento_padrao_id) {
                $('#lancamento_padraos_id').val(dados.lancamento_padrao_id).trigger('change');
            }
            
            // Centro de Custo
            if (dados.cost_center_id) {
                $('#cost_center_id').val(dados.cost_center_id).trigger('change');
            }
            
            // Forma de Pagamento
            if (dados.tipo_documento) {
                $('#tipo_documento').val(dados.tipo_documento).trigger('change');
            }
            
            // Fornecedor/Cliente
            if (dados.fornecedor_id) {
                var fornecedorSelect = $('#fornecedor_id');
                // Verifica se a opção existe
                if (fornecedorSelect.find('option[value="' + dados.fornecedor_id + '"]').length) {
                    fornecedorSelect.val(dados.fornecedor_id).trigger('change');
                } else if (dados.parceiro_nome) {
                    // Se não existe, adiciona a opção dinamicamente
                    var newOption = new Option(dados.parceiro_nome, dados.fornecedor_id, true, true);
                    fornecedorSelect.append(newOption).trigger('change');
                }
            }
        }, 100);
        
        // Checkboxes
        $('#comprovacao_fiscal_checkbox').prop('checked', dados.comprovacao_fiscal === true);
        $('#agendado_checkbox').prop('checked', dados.agendado === true);
        
        // Verifica situação para marcar pago/recebido
        var situacao = dados.situacao;
        if (situacao === 'pago' || situacao === 'recebido') {
            if (dados.tipo === 'entrada') {
                $('#recebido_checkbox').prop('checked', true);
            } else {
                $('#pago_checkbox').prop('checked', true);
            }
        }

        // Exibir card somente-leitura de parcelas (PAI com parcelas)
        if (dados.is_parcelado && dados.parcelas && dados.parcelas.length > 0) {
            console.log('📦 [Drawer-Init] Exibindo card de parcelas readonly:', dados.parcelas.length);
            exibirCardParcelasReadonly(dados.parcelas, null);
            
            // Esconde o select de parcelamento na edição (não é editável)
            $('#parcelamento_wrapper').hide();
        }
        // Se for transação FILHA (parcela individual), exibe banner informativo
        else if (dados.parent_id && dados.parcela_info) {
            console.log('📦 [Drawer-Init] Transação é parcela filha, parent_id:', dados.parent_id);
            exibirCardParcelasReadonly(null, dados.parcela_info);
        }
        
        console.log('✅ [Drawer-Init] Formulário preenchido com sucesso');
    }
    
    /**
     * Exibe o card somente-leitura de parcelas no drawer de edição
     * @param {Array|null} parcelas - Array com dados das parcelas (quando é PAI)
     * @param {Object|null} parcelaInfo - Info da parcela (quando é FILHA)
     */
    function exibirCardParcelasReadonly(parcelas, parcelaInfo) {
        var card = $('#card_parcelas_readonly');
        var badge = $('#card_parcelas_readonly_badge');
        var tbody = $('#card_parcelas_readonly_tbody');
        var tableWrapper = $('#card_parcelas_readonly_table_wrapper');
        var filhaInfo = $('#card_parcela_filha_info');
        var filhaInfoText = $('#card_parcela_filha_info_text');
        
        card.show();
        tbody.empty();
        
        // Caso 1: Transação PAI - exibe tabela de parcelas
        if (parcelas && parcelas.length > 0) {
            badge.text(parcelas.length + ' parcela' + (parcelas.length > 1 ? 's' : ''));
            tableWrapper.show();
            filhaInfo.hide();
            
            parcelas.forEach(function(parcela) {
                var situacaoBadge = '';
                var sit = parcela.situacao || 'em_aberto';
                if (sit === 'pago' || sit === 'recebido') {
                    situacaoBadge = '<span class="badge badge-light-success py-1 px-2 fs-8">Pago</span>';
                } else if (sit === 'em_aberto') {
                    situacaoBadge = '<span class="badge badge-light-warning py-1 px-2 fs-8">Em aberto</span>';
                } else {
                    situacaoBadge = '<span class="badge badge-light-secondary py-1 px-2 fs-8">' + sit.replace('_', ' ') + '</span>';
                }
                
                var valorStr = parcela.valor || '0,00';
                // Se valor veio como number, formata
                if (typeof valorStr === 'number') {
                    valorStr = valorStr.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
                
                var editBtn = '';
                if (parcela.transacao_parcela_id) {
                    editBtn = '<button type="button" class="btn btn-sm btn-icon btn-light-primary btn-parcela-editar" ' +
                        'data-transacao-id="' + parcela.transacao_parcela_id + '" title="Editar parcela">' +
                        '<i class="bi bi-pencil-square fs-6"></i></button>';
                }
                
                var row = '<tr>' +
                    '<td class="fw-bold">' + (parcela.numero_parcela || '-') + '/' + (parcela.total_parcelas || '-') + '</td>' +
                    '<td>' + (parcela.data_vencimento || '-') + '</td>' +
                    '<td class="text-end">R$ ' + valorStr + '</td>' +
                    '<td>' + situacaoBadge + '</td>' +
                    '<td class="text-center">' + editBtn + '</td>' +
                '</tr>';
                
                tbody.append(row);
            });
        }
        
        // Caso 2: Transação FILHA - exibe banner informativo
        if (parcelaInfo) {
            badge.text('Parcela ' + parcelaInfo.numero_parcela + '/' + parcelaInfo.total_parcelas);
            tableWrapper.hide();
            filhaInfo.show();
            
            var infoHtml = 'Esta é a <strong>parcela ' + parcelaInfo.numero_parcela + '/' + parcelaInfo.total_parcelas + '</strong>';
            if (parcelaInfo.parent_descricao) {
                infoHtml += ' do lançamento <strong>"' + parcelaInfo.parent_descricao + '"</strong>';
            }
            if (parcelaInfo.parent_id) {
                infoHtml += ' <a href="javascript:void(0)" class="btn-parcela-editar" data-transacao-id="' + parcelaInfo.parent_id + '">' +
                    '<i class="bi bi-box-arrow-up-right me-1"></i>Abrir lançamento pai</a>';
            }
            filhaInfoText.html(infoHtml);
        }
        
        console.log('✅ [Drawer-Init] Card de parcelas readonly exibido');
    }
    
    // Torna a função acessível globalmente
    window.preencherFormularioEdicao = preencherFormularioEdicao;
    window.exibirCardParcelasReadonly = exibirCardParcelasReadonly;
    
    // Handler delegado: clique no botão de editar parcela individual
    $(document).on('click', '.btn-parcela-editar', function(e) {
        e.preventDefault();
        var transacaoId = $(this).data('transacao-id');
        if (transacaoId && typeof abrirDrawerEdicao === 'function') {
            console.log('📝 [Drawer-Init] Abrindo parcela para edição, ID:', transacaoId);
            abrirDrawerEdicao(transacaoId);
        }
    });

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
            
            // Limpa os valores dos campos ocultos (Parcelas, Previsão e Informações de Pagamento)
            limparDadosAccordions();
            
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
            allowInput: true,
            clickOpens: true
        };

        // Só adiciona locale se estiver registrado
        if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && (flatpickr.l10ns.pt || flatpickr.l10ns.pt_BR)) {
            flatpickrConfig.locale = "pt";
        }
        
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
    
    // ========================================
    // FUNÇÕES DE LIMPEZA DE CAMPOS OCULTOS
    // ========================================
    
    /**
     * Limpa os campos do accordion de Previsão de Pagamento
     */
    function limparPrevisaoPagamento() {
        $('#previsao_pagamento').val('');
        $('#juros').val('');
        $('#multa').val('');
        $('#desconto').val('');
        $('#valor_a_pagar').val('');
    }
    
    /**
     * Limpa os campos do accordion de Informações de Pagamento
     */
    function limparInformacoesPagamento() {
        $('#data_pagamento').val('');
        $('#valor_pago').val('');
        $('#juros_pagamento').val('');
        $('#multa_pagamento').val('');
        $('#desconto_pagamento').val('');
        
        // Limpa flatpickr da data de pagamento se existir
        var dataPagamentoInput = document.getElementById('data_pagamento');
        if (dataPagamentoInput && dataPagamentoInput._flatpickr) {
            dataPagamentoInput._flatpickr.clear();
        }
        
        // Esconde containers de resumo
        $('#total_pagar_container').hide();
        $('#valor_aberto_container').hide();
        $('#resumo_baixa_tbody').empty();
    }
    
    /**
     * Limpa os campos do accordion de Parcelas
     */
    function limparParcelas() {
        $('#parcelas_table_body').empty();
    }
    
    // Expõe funções de limpeza globalmente para uso em outros scripts
    window.limparPrevisaoPagamento = limparPrevisaoPagamento;
    window.limparInformacoesPagamento = limparInformacoesPagamento;
    window.limparParcelas = limparParcelas;
    
    // Função para limpar dados dos accordions de pagamento (usa as funções específicas)
    function limparDadosAccordions() {
        limparPrevisaoPagamento();
        limparInformacoesPagamento();
        limparParcelas();
        
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
            // SOLUÇÃO PROFISSIONAL: Uma única função que gerencia todo o estado visual
            inicializarEstadoDrawer();
        }, 100);
    });
    
    // Função para inicializar o estado visual do drawer (independente de limpeza)
    function inicializarEstadoDrawer() {
        console.log('🔄 [Drawer-Init] Inicializando estado visual do drawer...');
        
        // Garante que elementos necessários estejam visíveis/ocultos conforme o estado atual
        var tipo = $('#tipo').val() || $('#tipo_financeiro').val();
        
        console.log('📋 [Drawer-Init] Tipo detectado:', tipo);
        
        // Se há um tipo definido, configura a visibilidade dos checkboxes
        if (tipo) {
            console.log('✅ [Drawer-Init] Aplicando lógica de checkbox para tipo:', tipo);
            
            if (typeof window.toggleCheckboxesByTipo === 'function') {
                console.log('🎯 [Drawer-Init] Chamando toggleCheckboxesByTipo...');
                window.toggleCheckboxesByTipo(tipo);
            } else {
                console.warn('⚠️ [Drawer-Init] toggleCheckboxesByTipo não está disponível');
            }
            
            // Pequeno delay para garantir que o DOM foi atualizado
            setTimeout(function() {
                if (typeof window.toggleCheckboxPago === 'function') {
                    console.log('🎯 [Drawer-Init] Chamando toggleCheckboxPago...');
                    window.toggleCheckboxPago();
                } else {
                    console.warn('⚠️ [Drawer-Init] toggleCheckboxPago não está disponível');
                }
            }, 50);
        } else {
            // Se não há tipo definido, oculta todos os checkboxes (estado inicial)
            console.log('❌ [Drawer-Init] Nenhum tipo definido - ocultando checkboxes');
            $('#checkboxes-entrada-wrapper, #checkboxes-saida-wrapper').hide();
            $('#checkbox-pago-wrapper, #checkbox-recebido-wrapper').hide();
        }
        
        // Garante outros estados iniciais
        garantirEstadoInicialDiaCobranca();
        
        // 🔧 CORREÇÃO: Reinicializa tooltips após mudanças de estado
        setTimeout(function() {
            console.log('🎯 [Drawer-Init] Reinicializando tooltips do drawer...');
            if (typeof window.initializeDrawerTooltips === 'function') {
                window.initializeDrawerTooltips();
            }
        }, 100);
        
        console.log('✅ [Drawer-Init] Estado visual inicializado com sucesso');
    }
    
    // Função para limpar completamente o formulário do drawer
    function limparFormularioDrawerCompleto() {
        var form = $('#kt_drawer_lancamento_form');
        if (!form.length) return;
        
        console.log('🧹 [Drawer-Init] Limpando dados do formulário completo...');
        
        // Reset básico do formulário
        form[0].reset();
        
        // Restaura valores padrão dos campos hidden
        $('#tipo').val('');
        $('#tipo_financeiro').val('');
        $('#status_pagamento').val('em aberto');
        $('#origem').val('Banco');
        
        // 🆕 Limpa campos de modo edição
        $('#transacao_id').val('');
        $('#_method').val('POST');
        form.attr('action', '{{ route("banco.store") }}');
        
        // Restaura título padrão do drawer
        var drawer = $('#kt_drawer_lancamento');
        var drawerTitle = drawer.find('.card-title').first();
        drawerTitle.text('Novo Lançamento');
        
        console.log('📝 [Drawer-Init] Campos básicos limpos - restaurando selects e checkboxes...');
        
        // Limpa e reinicializa Select2 especificamente
        form.find('select[data-control="select2"]').each(function() {
            var $select = $(this);
            if ($select.hasClass('select2-hidden-accessible')) {
                $select.val(null).trigger('change');
            } else {
                $select.val('');
            }
        });
        
        // Força limpeza de campos de texto e textarea
        form.find('input[type="text"], input[type="email"], input[type="tel"], textarea').val('');
        
        // Limpa campos de data
        form.find('input[type="date"], input[data-kt-daterangepicker]').val('');
        
        // Desmarca todos os checkboxes e radio buttons
        form.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);
        
        // IMPORTANTE: NÃO ocultar wrappers aqui - isso será gerenciado por inicializarEstadoDrawer()
        
        // Oculta accordions que dependem de dados
        $('#kt_accordion_previsao_pagamento, #kt_accordion_informacoes_pagamento, #kt_accordion_parcelas').hide();
        
        // Oculta e limpa o card de parcelas readonly
        $('#card_parcelas_readonly').hide();
        $('#card_parcelas_readonly_tbody').empty();
        $('#card_parcela_filha_info').hide();
        
        // Restaura visibilidade do select de parcelamento
        $('#parcelamento_wrapper').show();
        
        // Limpa tabelas dinâmicas
        $('#parcelas_tbody, #resumo_baixa_tbody').empty();
        
        // Remove campos dinâmicos adicionados por JavaScript
        form.find('input[name="intervalo_repeticao"], input[name="frequencia"], input[name="apos_ocorrencias"]').remove();
        
        // Esconde estrelas de sugestão
        $('.suggestion-star-wrapper').hide();
        
        // Restaura parcelamento para valor padrão
        setTimeout(function() {
            $('#parcelamento').val('avista');
            if ($('#parcelamento').hasClass('select2-hidden-accessible')) {
                $('#parcelamento').trigger('change');
            }
        }, 100);
        
        console.log('✅ [Drawer-Init] Dados do formulário limpos com sucesso');
    }
    
    // Torna as funções acessíveis globalmente para reutilização
    window.inicializarEstadoDrawer = inicializarEstadoDrawer;
    window.limparFormularioDrawerCompleto = limparFormularioDrawerCompleto;
    
    // Event listeners para limpeza
    $(document).on('kt.drawer.hide', '#kt_drawer_lancamento', function() {
        // Pequeno delay para garantir que a ação de fechamento não interfira
        setTimeout(function() {
            limparFormularioDrawerCompleto();
        }, 100);
    });
    
    // SOLUÇÃO PROFISSIONAL: Sempre inicializa estado quando drawer for mostrado
    $(document).on('kt.drawer.show', '#kt_drawer_lancamento', function() {
        setTimeout(function() {
            inicializarEstadoDrawer();
        }, 50);
    });
    
    // Event listener para botão X (fechar) - garantir que limpe também
    $(document).on('click', '#kt_drawer_lancamento [data-kt-drawer-dismiss="true"]:not(#kt_drawer_lancamento_cancel)', function() {
        console.log('❌ [Drawer-Init] Botão X clicado - executando limpeza preventiva');
        limparFormularioDrawerCompleto();
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
