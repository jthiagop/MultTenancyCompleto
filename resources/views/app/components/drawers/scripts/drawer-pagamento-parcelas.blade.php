<script>
// Script de controle de pagamento, parcelas e recorrência para o Drawer
(function() {
    function initDrawerPagamentoParcelas() {
        if (typeof $ === 'undefined') {
            console.warn('[DrawerPagamentoParcelas] jQuery não está disponível. Aguardando...');
            setTimeout(initDrawerPagamentoParcelas, 100);
            return;
        }

    $(document).ready(function() {
    
    // Função auxiliar para converter valor brasileiro
    function parseValorBrasileiro(valorStr) {
        if (!valorStr || valorStr === '') return 0;
        return parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;
    }
    
    // Função auxiliar para formatar valor brasileiro
    function formatarValorBrasileiro(valor) {
        if (isNaN(valor) || valor === null || valor === undefined) return '0,00';
        return valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    
    // Função para gerar as linhas da tabela de parcelas
    function gerarParcelas(parcelamento) {
        // Extrai o número de parcelas (ex: "2x" -> 2)
        var numParcelas = parseInt(parcelamento.replace('x', ''));
        if (isNaN(numParcelas) || numParcelas < 2) {
            return;
        }

        var tbody = $('#parcelas_table_body');
        tbody.empty();

        // Obtém o valor total do lançamento
        var valorTotalStr = $('#valor2').val() || '0';
        var valorTotal = parseValorBrasileiro(valorTotalStr);
        var valorPorParcela = valorTotal / numParcelas;
        var percentualPorParcela = 100 / numParcelas;

        // Obtém a data de vencimento base
        var dataVencimentoBase = $('#vencimento').val();
        var dataBase = null;
        if (dataVencimentoBase) {
            // Converte dd/mm/yyyy para Date
            var partes = dataVencimentoBase.split('/');
            if (partes.length === 3) {
                dataBase = new Date(partes[2], partes[1] - 1, partes[0]);
            }
        }
        if (!dataBase || isNaN(dataBase.getTime())) {
            dataBase = new Date();
        }

        // Obtém a descrição base
        var descricaoBase = $('#descricao').val() || '';

        // Gera uma linha para cada parcela
        for (var i = 1; i <= numParcelas; i++) {
            // Calcula a data de vencimento (adiciona meses)
            var dataVencimento = new Date(dataBase);
            dataVencimento.setMonth(dataBase.getMonth() + (i - 1));
            var dataFormatada = String(dataVencimento.getDate()).padStart(2, '0') + '/' +
                String(dataVencimento.getMonth() + 1).padStart(2, '0') + '/' +
                dataVencimento.getFullYear();

            // Calcula o valor da parcela (última parcela recebe o resto)
            var valorParcela = (i === numParcelas) ?
                valorTotal - (valorPorParcela * (numParcelas - 1)) :
                valorPorParcela;

            // Calcula o percentual (última parcela recebe o resto)
            var percentualParcela = (i === numParcelas) ?
                (100 - (percentualPorParcela * (numParcelas - 1))).toFixed(2) :
                percentualPorParcela.toFixed(2);

            // Formata o valor
            var valorFormatado = valorParcela.toFixed(2).replace('.', ',');

            // Clonar template
            var template = document.getElementById('parcela-row-template');
            if (!template) {
                console.error('Template de parcela não encontrado');
                continue;
            }
            
            var row = template.content.cloneNode(true);
            var tr = row.querySelector('tr');
            
            // Configurar atributos da linha
            tr.setAttribute('data-parcela', i);
            
            // Preencher dados
            tr.querySelector('.parcela-numero').textContent = i;
            
            // Vencimento
            var inputVencimento = tr.querySelector('.parcela-vencimento');
            inputVencimento.value = dataFormatada;
            inputVencimento.name = `parcelas[${i}][vencimento]`;
            inputVencimento.setAttribute('data-parcela-num', i);
            
            // Valor
            var inputValor = tr.querySelector('.parcela-valor');
            inputValor.value = valorFormatado;
            inputValor.name = `parcelas[${i}][valor]`;
            inputValor.setAttribute('data-parcela-num', i);
            
            // Última parcela: readonly
            if (i === numParcelas) {
                inputValor.classList.add('bg-light');
                inputValor.readOnly = true;
                inputValor.style.cursor = 'not-allowed';
            }
            
            // Percentual
            var inputPercentual = tr.querySelector('.parcela-percentual');
            inputPercentual.value = percentualParcela;
            inputPercentual.name = `parcelas[${i}][percentual]`;
            inputPercentual.setAttribute('data-parcela-num', i);
            
            // Última parcela: readonly
            if (i === numParcelas) {
                inputPercentual.classList.add('bg-light');
                inputPercentual.readOnly = true;
                inputPercentual.style.cursor = 'not-allowed';
            }
            
            // Forma de Pagamento
            var selectFormaPagamento = tr.querySelector('.parcela-forma-pagamento');
            selectFormaPagamento.name = `parcelas[${i}][forma_pagamento_id]`;
            selectFormaPagamento.setAttribute('data-parcela-num', i);
            
            // Conta de Pagamento
            var selectContaPagamento = tr.querySelector('.parcela-conta-pagamento');
            selectContaPagamento.name = `parcelas[${i}][conta_pagamento_id]`;
            selectContaPagamento.setAttribute('data-parcela-num', i);
            
            // Descrição
            var inputDescricao = tr.querySelector('.parcela-descricao');
            inputDescricao.value = `${descricaoBase} ${i}/${numParcelas}`;
            inputDescricao.name = `parcelas[${i}][descricao]`;
            inputDescricao.setAttribute('data-parcela-num', i);
            inputDescricao.setAttribute('data-descricao-base', descricaoBase);
            
            // Agendado
            var inputAgendado = tr.querySelector('.parcela-agendado');
            inputAgendado.name = `parcelas[${i}][agendado]`;
            inputAgendado.setAttribute('data-parcela-num', i);
            
            // Adicionar ao tbody
            tbody.append(row);
        }


        // Inicializa os datepickers e Select2
        setTimeout(function() {
            // Inicializa os datepickers para os campos de vencimento
            tbody.find('input[data-parcela-input="vencimento"]').each(function() {
                if (!$(this).data('flatpickr-initialized')) {
                    if (typeof flatpickr !== 'undefined') {
                        flatpickr(this, {
                            enableTime: false,
                            dateFormat: "d/m/Y",
                            locale: "pt",
                            allowInput: true,
                            clickOpens: true
                        });
                        $(this).data('flatpickr-initialized', true);
                    }
                }
            });

            // Inicializa máscaras de moeda para os campos de valor
            if (typeof Inputmask !== 'undefined') {
                tbody.find('input[data-parcela-input="valor"]').each(function() {
                    if (!$(this).attr('readonly') && !$(this).data('mask-initialized')) {
                        Inputmask({
                            alias: "currency",
                            groupSeparator: ".",
                            radixPoint: ",",
                            autoGroup: true,
                            digits: 2,
                            digitsOptional: false,
                            placeholder: "0,00",
                            rightAlign: false,
                            removeMaskOnSubmit: true,
                            allowMinus: false,
                            clearMaskOnLostFocus: false
                        }).mask(this);
                        $(this).data('mask-initialized', true);
                    }
                });

                // Inicializa máscaras de porcentagem
                tbody.find('input[data-parcela-input="percentual"]').each(function() {
                    if (!$(this).attr('readonly') && !$(this).data('mask-initialized')) {
                        Inputmask({
                            alias: "decimal",
                            groupSeparator: "",
                            radixPoint: ".",
                            autoGroup: false,
                            digits: 2,
                            digitsOptional: false,
                            placeholder: "0.00",
                            rightAlign: false,
                            allowMinus: false,
                            min: 0,
                            max: 100
                        }).mask(this);
                        $(this).data('mask-initialized', true);
                    }
                });
            }

            // Inicializa os Select2 para Forma de pagamento e Conta para pagamento
            tbody.find('select[data-parcela-input="forma_pagamento"], select[data-parcela-input="conta_pagamento"]').each(function() {
                var $select = $(this);

                // Verifica se já foi inicializado
                if ($select.hasClass('select2-hidden-accessible')) {
                    return;
                }

                // Prepara opções do Select2
                var options = {
                    dropdownParent: $('#kt_drawer_lancamento'),
                    placeholder: $select.attr('data-placeholder') || 'Selecione',
                    allowClear: $select.attr('data-allow-clear') === 'true',
                    minimumResultsForSearch: 0,
                    theme: 'bootstrap5'
                };

                // Inicializa Select2
                try {
                    $select.select2(options);
                } catch (error) {
                }
            });

            // Adiciona event listeners para recalcular valores e percentuais
            adicionarEventListenersParcelas(numParcelas);
        }, 100);
    }
    
    // Função para adicionar event listeners de recálculo
    function adicionarEventListenersParcelas(numParcelas) {
        var tbody = $('#parcelas_table_body');
        
        // Remove listeners anteriores para evitar duplicação
        tbody.off('input change blur', 'input[data-parcela-input="valor"]');
        tbody.off('input change blur', 'input[data-parcela-input="percentual"]');
        
        // Event listener para quando o valor mudar
        tbody.on('input change blur', 'input[data-parcela-input="valor"]', function() {
            var parcelaNum = parseInt($(this).attr('data-parcela-num'));
            if (parcelaNum === numParcelas) return; // Ignora se for a última
            
            recalcularPorValor(numParcelas, parcelaNum);
        });
        
        // Event listener para quando o percentual mudar
        tbody.on('input change blur', 'input[data-parcela-input="percentual"]', function() {
            var parcelaNum = parseInt($(this).attr('data-parcela-num'));
            if (parcelaNum === numParcelas) return; // Ignora se for a última
            
            recalcularPorPercentual(numParcelas, parcelaNum);
        });
    }
    
    // Função para recalcular quando o valor é alterado
    function recalcularPorValor(numParcelas, parcelaAlterada) {
        var valorTotalStr = $('#valor2').val() || '0';
        var valorTotal = parseValorBrasileiro(valorTotalStr);
        
        if (valorTotal <= 0) return;
        
        var somaValores = 0;
        
        // Calcula a soma dos valores editáveis (exceto a última)
        for (var i = 1; i < numParcelas; i++) {
            var valorInput = $('input[data-parcela-input="valor"][data-parcela-num="' + i + '"]');
            var valorStr = valorInput.val() || '0';
            var valor = parseValorBrasileiro(valorStr);
            somaValores += valor;
        }
        
        // Valida se a soma das parcelas não ultrapassa o valor total
        if (somaValores > valorTotal) {
            var valorInput = $('input[data-parcela-input="valor"][data-parcela-num="' + parcelaAlterada + '"]');
            valorInput.addClass('is-invalid');
            
            // Remove mensagem de erro anterior se existir
            valorInput.closest('td').find('.invalid-feedback').remove();
            
            // Adiciona mensagem de erro
            valorInput.closest('td').append(
                '<div class="invalid-feedback d-block">A soma das parcelas não pode exceder o valor total de R$ ' + 
                formatarValorBrasileiro(valorTotal) + '</div>'
            );
            
            toastr.error('A soma das parcelas não pode exceder o valor principal', 'Erro de Validação');
            return;
        }
        
        // Remove validação de erro se tudo estiver ok
        $('input[data-parcela-input="valor"]').removeClass('is-invalid');
        $('input[data-parcela-input="valor"]').closest('td').find('.invalid-feedback').remove();
        
        // Calcula o valor da última parcela (resto)
        var valorUltima = valorTotal - somaValores;
        
        // Valida se o valor da última parcela é negativo ou zero
        if (valorUltima <= 0) {
            var valorUltimaInput = $('input[data-parcela-input="valor"][data-parcela-num="' + numParcelas + '"]');
            valorUltimaInput.addClass('is-invalid');
            valorUltimaInput.closest('td').find('.invalid-feedback').remove();
            valorUltimaInput.closest('td').append(
                '<div class="invalid-feedback d-block">A soma das parcelas excede o valor total</div>'
            );
            return;
        }
        
        // Atualiza o valor da última parcela
        var valorUltimaFormatado = formatarValorBrasileiro(valorUltima);
        $('input[data-parcela-input="valor"][data-parcela-num="' + numParcelas + '"]').val(valorUltimaFormatado);
        $('input[data-parcela-input="valor"][data-parcela-num="' + numParcelas + '"]').removeClass('is-invalid');
        
        // Recalcula os percentuais baseado nos valores
        for (var i = 1; i <= numParcelas; i++) {
            var valorInput = $('input[data-parcela-input="valor"][data-parcela-num="' + i + '"]');
            var valorStr = valorInput.val() || '0';
            var valor = parseValorBrasileiro(valorStr);
            
            var percentual = (valor / valorTotal) * 100;
            var percentualFormatado = percentual.toFixed(2);
            
            $('input[data-parcela-input="percentual"][data-parcela-num="' + i + '"]').val(percentualFormatado);
        }
    }
    
    // Função para recalcular quando o percentual é alterado
    function recalcularPorPercentual(numParcelas, parcelaAlterada) {
        var valorTotalStr = $('#valor2').val() || '0';
        var valorTotal = parseValorBrasileiro(valorTotalStr);
        
        if (valorTotal <= 0) return;
        
        var somaPercentuais = 0;
        
        // Calcula a soma dos percentuais editáveis (exceto a última)
        for (var i = 1; i < numParcelas; i++) {
            var percentualInput = $('input[data-parcela-input="percentual"][data-parcela-num="' + i + '"]');
            var percentualStr = percentualInput.val() || '0';
            var percentual = parseFloat(percentualStr) || 0;
            somaPercentuais += percentual;
        }
        
        // Garante que a soma não ultrapasse 100%
        if (somaPercentuais > 100) {
            var percentualAtual = parseFloat($('input[data-parcela-input="percentual"][data-parcela-num="' + parcelaAlterada + '"]').val()) || 0;
            var diferenca = somaPercentuais - 100;
            var novoPercentual = percentualAtual - diferenca;
            if (novoPercentual < 0) novoPercentual = 0;
            
            $('input[data-parcela-input="percentual"][data-parcela-num="' + parcelaAlterada + '"]').val(novoPercentual.toFixed(2));
            somaPercentuais = 100;
        }
        
        // Calcula o percentual da última parcela (resto)
        var percentualUltima = 100 - somaPercentuais;
        
        // Atualiza o percentual da última parcela
        $('input[data-parcela-input="percentual"][data-parcela-num="' + numParcelas + '"]').val(percentualUltima.toFixed(2));
        
        // Recalcula os valores baseado nos percentuais
        for (var i = 1; i <= numParcelas; i++) {
            var percentualInput = $('input[data-parcela-input="percentual"][data-parcela-num="' + i + '"]');
            var percentualStr = percentualInput.val() || '0';
            var percentual = parseFloat(percentualStr) || 0;
            
            var valor = (valorTotal * percentual) / 100;
            var valorFormatado = formatarValorBrasileiro(valor);
            
            $('input[data-parcela-input="valor"][data-parcela-num="' + i + '"]').val(valorFormatado);
        }
    }
    
    // Controla exibição da tab de Anexos
    $('#comprovacao_fiscal_checkbox').on('change', function() {
        var tabAnexosItem = $('#tab_anexos_item');
        if ($(this).is(':checked')) {
            tabAnexosItem.show();
        } else {
            tabAnexosItem.hide();
            var tabAnexosLink = tabAnexosItem.find('a');
            if (tabAnexosLink.hasClass('active')) {
                tabAnexosLink.removeClass('active');
                $('#kt_tab_pane_2').removeClass('show active');
                $('#kt_tab_pane_1').addClass('show active');
                $('a[href="#kt_tab_pane_1"]').addClass('active');
            }
        }
    });
    
    // Controla a exibição dos wrappers de checkboxes baseado no tipo de transação
    function toggleCheckboxesByTipo() {
        var tipo = $('#tipo').val(); // 'entrada' ou 'saida'
        
        var wrapperEntrada = $('#checkboxes-entrada-wrapper');
        var wrapperSaida = $('#checkboxes-saida-wrapper');
               
        if (tipo === 'entrada') {
            // Receita: Mostra apenas Recebido
            wrapperEntrada.show();
            wrapperSaida.hide();
            
            // Desmarca checkboxes de Saída
            $('#pago_checkbox').prop('checked', false);
            $('#agendado_checkbox').prop('checked', false);
        } else if (tipo === 'saida') {
            // Despesa: Mostra Pago e Agendado
            wrapperEntrada.hide();
            wrapperSaida.show();
            
            // Desmarca checkbox de Entrada
            if (typeof $ !== 'undefined') {
                $('#recebido_checkbox').prop('checked', false);
            } else {
                var recebidoCheckbox = document.getElementById('recebido_checkbox');
                if (recebidoCheckbox) recebidoCheckbox.checked = false;
            }
        } else {
            // Default: mostrar saída
            wrapperEntrada.hide();
            wrapperSaida.show();
        }
        
        // Atualiza visibilidade dos checkboxes internos baseado no parcelamento
        toggleCheckboxPago();
    }
    
    // Controla exibição dos checkboxes Pago e Recebido baseado no parcelamento
    function toggleCheckboxPago() {
        var parcelamento = $('#parcelamento').val();
        var checkboxPagoWrapper = $('#checkbox-pago-wrapper');
        var checkboxRecebidoWrapper = $('#checkbox-recebido-wrapper');
        
        // Se recorrência estiver ativa, garante que checkboxes fiquem ocultos
        if ($('#flexSwitchDefault').is(':checked')) {
            checkboxPagoWrapper.hide();
            checkboxRecebidoWrapper.hide();
            return;
        }
        
        if (parcelamento === 'avista' || parcelamento === '1x') {
            // Mostra os checkboxes apropriados
            checkboxPagoWrapper.show();
            checkboxRecebidoWrapper.show();
        } else {
            // Oculta ambos os checkboxes quando tem mais de 1x parcelas
            checkboxPagoWrapper.hide();
            checkboxRecebidoWrapper.hide();
            if (typeof $ !== 'undefined') {
                $('#pago_checkbox').prop('checked', false);
                $('#recebido_checkbox').prop('checked', false);
            } else {
                var pagoCheckbox = document.getElementById('pago_checkbox');
                var recebidoCheckbox = document.getElementById('recebido_checkbox');
                if (pagoCheckbox) pagoCheckbox.checked = false;
                if (recebidoCheckbox) recebidoCheckbox.checked = false;
            }
        }
    }
    
    // Controla exibição do checkbox Agendado
    function toggleCheckboxAgendado() {
        var pagoCheckbox = $('#pago_checkbox');
        var agendadoWrapper = $('#checkbox-agendado-wrapper');
        var parcelamento = $('#parcelamento').val();
        var accordionInformacoesPagamento = $('#kt_accordion_informacoes_pagamento');
        var accordionPrevisaoPagamento = $('#kt_accordion_previsao_pagamento');
        
        // Verifica se descrição e valor estão preenchidos
        var descricao = $('#descricao').val() || '';
        var valorStr = $('#valor2').val() || '0';
        var valorNumerico = parseValorBrasileiro(valorStr);
        var descricaoPreenchida = descricao.trim().length > 0;
        var valorPreenchido = valorNumerico > 0;
        
        if (pagoCheckbox.is(':checked')) {
            agendadoWrapper.hide();
            $('#agendado_checkbox').prop('checked', false);
            
            // Só mostra accordion se descrição e valor estiverem preenchidos
            if (descricaoPreenchida && valorPreenchido) {
                accordionInformacoesPagamento.show();
                
                // Oculta previsão se for 1x ou À Vista
                if (parcelamento === '1x' || parcelamento === 'avista') {
                    accordionPrevisaoPagamento.hide();
                }
            } else {
                // Exibe toast de aviso se faltar dados
                if (!descricaoPreenchida || !valorPreenchido) {
                    var mensagem = 'Preencha ';
                    var campos = [];
                    if (!descricaoPreenchida) campos.push('a descrição');
                    if (!valorPreenchido) campos.push('o valor');
                    mensagem += campos.join(' e ') + ' para visualizar as informações de pagamento.';
                    toastr.warning(mensagem, 'Atenção');
                }
            }
        } else {
            agendadoWrapper.show();
            
            // Oculta accordion de informações quando desmarca Pago
            accordionInformacoesPagamento.hide();
            
            // Mostra previsão se for 1x
            if (parcelamento === '1x') {
                accordionPrevisaoPagamento.show();
            }
        }
    }
    
    // Event listeners
    $('#parcelamento').on('change', function() {
        toggleCheckboxPago();
        
        // Controla exibição dos accordions
        var parcelamento = $(this).val();
        var accordionParcelas = $('#kt_accordion_parcelas');
        var accordionInformacoesPagamento = $('#kt_accordion_informacoes_pagamento');
        var accordionPrevisaoPagamento = $('#kt_accordion_previsao_pagamento');
        
        // Mostra accordion se for 2x ou mais, oculta se for À Vista ou 1x
        if (parcelamento && parcelamento !== 'avista' && parcelamento !== '1x') {
            // Parse o número de parcelas
            var numParcelas = parseInt(parcelamento.replace('x', ''));
            if (!isNaN(numParcelas) && numParcelas >= 2) {
                // Verifica se descrição e valor estão preenchidos antes de mostrar o accordion
                var descricao = $('#descricao').val() || '';
                var valorStr = $('#valor2').val() || '0';
                var valorNumerico = parseValorBrasileiro(valorStr);
                
                var descricaoPreenchida = descricao.trim().length > 0;
                var valorPreenchido = valorNumerico > 0;
                
                // Mostra o accordion de parcelas apenas se descrição e valor estiverem preenchidos
                if (descricaoPreenchida && valorPreenchido) {
                    accordionParcelas.show();
                    accordionInformacoesPagamento.hide();
                    accordionPrevisaoPagamento.hide();
                    // Gera as linhas da tabela de parcelas
                    gerarParcelas(parcelamento);
                } else {
                    accordionParcelas.hide();
                    accordionInformacoesPagamento.hide();
                    accordionPrevisaoPagamento.hide();
                    toastr.warning('Preencha a descrição e o valor antes de selecionar o parcelamento', 'Atenção');
                }
            } else {
                accordionParcelas.hide();
                accordionInformacoesPagamento.hide();
                accordionPrevisaoPagamento.hide();
            }
        } else if (parcelamento === '1x') {
            // Para 1x, mostra o accordion correto baseado no checkbox "Pago"
            accordionParcelas.hide();
            var pagoCheckbox = $('#pago_checkbox');
            if (pagoCheckbox.is(':checked')) {
                // Pago marcado: mostra informações de pagamento
                accordionInformacoesPagamento.show();
                accordionPrevisaoPagamento.hide();
            } else {
                // Pago desmarcado: mostra previsão de pagamento
                accordionInformacoesPagamento.hide();
                accordionPrevisaoPagamento.show();
            }
        } else {
            // À Vista
            accordionParcelas.hide();
            accordionInformacoesPagamento.hide();
            accordionPrevisaoPagamento.hide();
        }
    });
    
    $('#pago_checkbox').on('change', function() {
        toggleCheckboxAgendado();
    });
    
    // Evento para checkbox "Recebido" - controla accordion de informações
    $('#recebido_checkbox').on('change', function() {
        // A mesma lógica do Pago, mas para receitas
        var recebidoCheckbox = $(this);
        var accordionInformacoesPagamento = $('#kt_accordion_informacoes_pagamento');
        var accordionPrevisaoPagamento = $('#kt_accordion_previsao_pagamento');
        var parcelamento = $('#parcelamento').val();
        
        if (recebidoCheckbox.is(':checked')) {
            // Verifica se descrição e valor estão preenchidos
            var descricao = $('#descricao').val() || '';
            var valorStr = $('#valor2').val() || '0';
            var valorNumerico = parseValorBrasileiro(valorStr);
            var descricaoPreenchida = descricao.trim().length > 0;
            var valorPreenchido = valorNumerico > 0;
            
            if (descricaoPreenchida && valorPreenchido) {
                accordionInformacoesPagamento.show();
                if (parcelamento === '1x' || parcelamento === 'avista') {
                    accordionPrevisaoPagamento.hide();
                }
            }
        } else {
            accordionInformacoesPagamento.hide();
            if (parcelamento === '1x') {
                accordionPrevisaoPagamento.show();
            }
        }
    });
    
    // Evento para mudança de tipo (entrada/saida) - controla checkboxes visíveis
    $('#tipo').on('change', function() {
        toggleCheckboxesByTipo();
    });
    
    // MutationObserver para detectar quando o tipo é definido (é um input hidden)
    var tipoInput = document.getElementById('tipo');
    if (tipoInput) {
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    toggleCheckboxesByTipo();
                }
            });
        });
        observer.observe(tipoInput, { attributes: true });
        
        // Também escuta evento 'input' como fallback
        $(tipoInput).on('input change', function() {
            toggleCheckboxesByTipo();
        });
    }
    
    // Inicializa checkboxes quando drawer abrir
    $('#kt_drawer_lancamento').on('shown.bs.drawer', function() {
        setTimeout(function() {
            toggleCheckboxesByTipo();
        }, 100);
    });
    
    // Inicializa máscaras de moeda
    function inicializarMascarasMoeda() {
        if (typeof Inputmask === 'undefined') return;
        
        var camposMoeda = ['#valor2', '#juros', '#multa', '#desconto', '#valor_a_pagar', 
                          '#valor_pago', '#juros_pagamento', '#multa_pagamento', '#desconto_pagamento'];
        
        camposMoeda.forEach(function(seletor) {
            var campo = $(seletor);
            if (campo.length && !campo.attr('data-mask-initialized')) {
                try {
                    Inputmask({
                        alias: "currency",
                        groupSeparator: ".",
                        radixPoint: ",",
                        autoGroup: true,
                        digits: 2,
                        digitsOptional: false,
                        placeholder: "0,00",
                        rightAlign: false,
                        removeMaskOnSubmit: true,
                        allowMinus: false,
                        clearMaskOnLostFocus: false
                    }).mask(campo[0]);
                    campo.attr('data-mask-initialized', '1');
                } catch (error) {
                    console.error('Erro ao inicializar máscara:', error);
                }
            }
        });
    }
    
    // Sincroniza data de competência com vencimento (unidirecional)
    $(document).on('change', '#data_competencia', function() {
        var dataCompetencia = $(this).val();
        var vencimentoInput = $('#vencimento');
        
        // Só copia se o vencimento estiver vazio ou se for válido
        if (dataCompetencia && dataCompetencia.trim() !== '') {
            vencimentoInput.val(dataCompetencia);
        }
    });
    
    // Event listeners para gerar parcelas automaticamente quando descrição e valor são preenchidos
    $('#descricao, #valor2').on('input', function() {
        tentarGerarParcelasAutomaticamente();
        
        // Se checkbox Pago estiver marcado, tenta mostrar accordion de informações
        if ($('#pago_checkbox').is(':checked')) {
            toggleCheckboxAgendado();
        }
    });
    
    // Sincroniza valor principal com valor_pago
    $('#valor2').on('input change', function() {
        var valorPrincipal = $(this).val();
        $('#valor_pago').val(valorPrincipal);
        
        // Atualiza resumo se pago estiver marcado
        if ($('#pago_checkbox').is(':checked')) {
            calcularResumoPagamento();
        }
    });
    
    // Event listeners para campos de pagamento
    $('#valor_pago, #juros_pagamento, #multa_pagamento, #desconto_pagamento').on('input change', function() {
        if ($('#pago_checkbox').is(':checked')) {
            calcularResumoPagamento();
        }
    });
    
    // Função para calcular e exibir o resumo do pagamento
    function calcularResumoPagamento() {
        var valorPrincipalStr = $('#valor2').val() || '0';
        var valorPagoStr = $('#valor_pago').val() || '0';
        var jurosStr = $('#juros_pagamento').val() || '0';
        var multaStr = $('#multa_pagamento').val() || '0';
        var descontoStr = $('#desconto_pagamento').val() || '0';

        // Remove formatação
        var valorPrincipal = parseValorBrasileiro(valorPrincipalStr);
        var valorPago = parseValorBrasileiro(valorPagoStr);
        var juros = parseValorBrasileiro(jurosStr);
        var multa = parseValorBrasileiro(multaStr);
        var desconto = parseValorBrasileiro(descontoStr);

        // Calcula valores
        var valorParaComparacao = valorPago + juros + multa;
        var totalPagar = valorParaComparacao - desconto;
        var valorAberto = valorPrincipal - valorParaComparacao;

        if (valorAberto < 0) {
            valorAberto = 0;
        }

        var valorAbertoContainer = $('#valor_aberto_container');
        var totalPagarContainer = $('#total_pagar_container');

        function formatarMoeda(valor) {
            return 'R$ ' + Math.abs(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        if (valorPago > 0) {
            totalPagarContainer.show();
            $('#total_pagar_display').text(formatarMoeda(totalPagar));

            if (valorAberto > 0.01) {
                valorAbertoContainer.show();
                $('#valor_aberto_display').text(formatarMoeda(valorAberto));
            } else {
                valorAbertoContainer.hide();
                $('#valor_aberto_display').text(formatarMoeda(0));
            }

            // Atualiza a tabela do resumo da baixa
            atualizarResumoBaixa();
        } else {
            valorAbertoContainer.hide();
            totalPagarContainer.hide();
            $('#total_pagar_display').text(formatarMoeda(0));
            $('#valor_aberto_display').text(formatarMoeda(0));
            $('#resumo_baixa_tbody').empty();
        }
    }

    // Função para atualizar a tabela do resumo da baixa
    function atualizarResumoBaixa() {
        var tbody = $('#resumo_baixa_tbody');
        tbody.empty();

        var valorPagoStr = $('#valor_pago').val() || '0';
        var valorPago = parseValorBrasileiro(valorPagoStr);

        if (valorPago <= 0) {
            return;
        }

        var dataPagamento = $('#data_pagamento').val() || '';
        var valorPrincipalStr = $('#valor2').val() || '0';
        var jurosStr = $('#juros_pagamento').val() || '0';
        var multaStr = $('#multa_pagamento').val() || '0';
        var descontoStr = $('#desconto_pagamento').val() || '0';

        var valorPrincipal = parseValorBrasileiro(valorPrincipalStr);
        var juros = parseValorBrasileiro(jurosStr);
        var multa = parseValorBrasileiro(multaStr);
        var desconto = parseValorBrasileiro(descontoStr);

        var valorParaComparacao = valorPago + juros + multa;
        var totalPagar = valorParaComparacao - desconto;
        var valorAberto = valorPrincipal - valorParaComparacao;

        if (valorAberto < 0) {
            valorAberto = 0;
        }

        // Obtém valores selecionados
        var formaPagamentoId = $('#entidade_id').val() || '';
        var contaId = $('#conta_pagamento_id, #conta_financeira_id').val() || '';

        function formatarMoeda(valor) {
            return Math.abs(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Função para criar linha usando template
        function criarLinhaResumoBaixa(dados) {
            var template = document.getElementById('resumo-baixa-row-template');
            if (!template) {
                console.error('Template de resumo da baixa não encontrado');
                return null;
            }
            
            var row = template.content.cloneNode(true);
            var tr = row.querySelector('tr');
            
            // Data
            var inputData = tr.querySelector('.resumo-data');
            inputData.value = dados.data;
            inputData.name = `resumo_baixa[${dados.index}][data_pagamento]`;
            
            // Primeira linha (pago) não pode editar data
            if (dados.index === 0) {
                inputData.readOnly = true;
                inputData.classList.add('bg-light');
                inputData.style.cursor = 'not-allowed';
            }
            
            // Forma de Pagamento
            var selectFormaPagamento = tr.querySelector('.resumo-forma-pagamento');
            selectFormaPagamento.name = `resumo_baixa[${dados.index}][forma_pagamento_id]`;
            if (dados.formaPagamentoId) {
                selectFormaPagamento.value = dados.formaPagamentoId;
            }
            // Linha "em aberto" requer forma de pagamento
            if (dados.index > 0) {
                selectFormaPagamento.required = true;
            }
            
            // Conta
            var selectConta = tr.querySelector('.resumo-conta');
            selectConta.name = `resumo_baixa[${dados.index}][conta_id]`;
            if (dados.contaId) {
                selectConta.value = dados.contaId;
            }
            // Linha "em aberto" requer conta
            if (dados.index > 0) {
                selectConta.required = true;
            }
            
            // Valor (readonly display)
            tr.querySelector('.resumo-valor-display').textContent = formatarMoeda(dados.valor);
            tr.querySelector('.resumo-valor-hidden').value = dados.valor.toFixed(2);
            
            // Juros/Multa (readonly display)
            tr.querySelector('.resumo-juros-multa-display').textContent = formatarMoeda(dados.jurosMulta);
            tr.querySelector('.resumo-juros-multa-hidden').value = dados.jurosMulta.toFixed(2);
            
            // Desconto (readonly display)
            tr.querySelector('.resumo-desconto-display').textContent = formatarMoeda(dados.desconto);
            tr.querySelector('.resumo-desconto-hidden').value = dados.desconto.toFixed(2);
            
            // Situação (readonly display)
            var badgeClass = dados.situacao === 'pago' ? 'badge-light-success' : 'badge-light-warning';
            var badgeText = dados.situacao === 'pago' ? 'Pago' : 'Em aberto';
            tr.querySelector('.resumo-situacao-badge').innerHTML = `<span class="badge ${badgeClass}">${badgeText}</span>`;
            tr.querySelector('.resumo-situacao-hidden').value = dados.situacao;
            
            return row;
        }

        // Linha 1: Lançamento Pago
        var linhaPago = criarLinhaResumoBaixa({
            index: 0,
            data: dataPagamento,
            formaPagamentoId: formaPagamentoId,
            contaId: contaId,
            valor: valorPago,
            jurosMulta: juros + multa,
            desconto: desconto,
            situacao: 'pago'
        });
        
        if (linhaPago) {
            tbody.append(linhaPago);
        }

        // Linha 2: Lançamento Em Aberto (se houver valor em aberto)
        if (valorAberto > 0.01) {
            var linhaAberto = criarLinhaResumoBaixa({
                index: 1,
                data: dataPagamento,
                formaPagamentoId: '', // Usuário precisa selecionar
                contaId: '', // Usuário precisa selecionar
                valor: valorAberto,
                jurosMulta: 0,
                desconto: 0,
                situacao: 'em_aberto'
            });
            
            if (linhaAberto) {
                tbody.append(linhaAberto);
            }
        }
        
        // Inicializa flatpickr e Select2 nas novas linhas
        setTimeout(function() {
            // Flatpickr para datas
            tbody.find('.resumo-data').each(function() {
                if (!$(this).prop('readonly') && !$(this).data('flatpickr-initialized')) {
                    if (typeof flatpickr !== 'undefined') {
                        flatpickr(this, {
                            enableTime: false,
                            dateFormat: "d/m/Y",
                            locale: "pt",
                            allowInput: true,
                            clickOpens: true
                        });
                        $(this).data('flatpickr-initialized', true);
                    }
                }
            });
            
            // Select2 para forma de pagamento e conta
            tbody.find('.resumo-forma-pagamento, .resumo-conta').each(function() {
                var $select = $(this);
                
                if ($select.hasClass('select2-hidden-accessible')) {
                    return;
                }
                
                var options = {
                    placeholder: $select.attr('data-placeholder') || 'Selecione',
                    allowClear: $select.attr('data-allow-clear') === 'true',
                    minimumResultsForSearch: 0
                };
                
                var dropdownParent = $select.attr('data-dropdown-parent');
                if (dropdownParent) {
                    var parentElement = $(dropdownParent);
                    if (parentElement.length) {
                        options.dropdownParent = parentElement;
                    }
                }
                
                try {
                    $select.select2(options);
                } catch (error) {
                    console.error('Erro ao inicializar Select2 no resumo da baixa:', error);
                }
            });
        }, 100);
        
        // Expande o accordion do resumo da baixa se houver dados
        var resumoBaixaBody = $('#kt_accordion_resumo_baixa_body');
        if ($('#resumo_baixa_tbody tr').length > 0 && resumoBaixaBody.length) {
            if (!resumoBaixaBody.hasClass('show')) {
                var bsCollapse = new bootstrap.Collapse(resumoBaixaBody[0], {
                    show: true
                });
            }
        }
    }


    
    // Função para tentar gerar parcelas automaticamente
    function tentarGerarParcelasAutomaticamente() {
        var parcelamento = $('#parcelamento').val();
        
        // Só tenta se for 2x ou mais
        if (parcelamento && parcelamento !== 'avista' && parcelamento !== '1x') {
            var numParcelas = parseInt(parcelamento.replace('x', ''));
            if (!isNaN(numParcelas) && numParcelas >= 2) {
                var descricao = $('#descricao').val() || '';
                var valorStr = $('#valor2').val() || '0';
                var valorNumerico = parseValorBrasileiro(valorStr);
                
                var descricaoPreenchida = descricao.trim().length > 0;
                var valorPreenchido = valorNumerico > 0;
                
                // Se ambos estiverem preenchidos, gera as parcelas
                if (descricaoPreenchida && valorPreenchido) {
                    var accordionParcelas = $('#kt_accordion_parcelas');
                    accordionParcelas.show();
                    gerarParcelas(parcelamento);
                }
            }
        }
    }
    
    // Inicializa quando o drawer abrir
    $(document).on('kt.drawer.show', '#kt_drawer_lancamento', function() {
        setTimeout(function() {
            inicializarMascarasMoeda();
            toggleCheckboxPago(); // Inicializa visibilidade do checkbox Pago
        }, 200);
    });
    
    // Chama toggleCheckboxPago ao carregar a página para definir estado inicial
    toggleCheckboxPago();
    });
    }

    // Inicializa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDrawerPagamentoParcelas);
    } else {
        initDrawerPagamentoParcelas();
    }
})();
</script>
