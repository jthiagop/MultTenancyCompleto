<script>
    // Controla a exibição da tab de Anexos baseado no checkbox
    (function() {
        function initPagamentoParcelasRecorrencia() {
            if (typeof $ === 'undefined') {
                console.warn('[PagamentoParcelasRecorrencia] jQuery não está disponível. Aguardando...');
                setTimeout(initPagamentoParcelasRecorrencia, 100);
                return;
            }

        $(document).ready(function() {
            $('#comprovacao_fiscal_checkbox').on('change', function() {
            var isChecked = $(this).is(':checked');
            var tabAnexosItem = $('#tab_anexos_item');

            if (isChecked) {
                // Mostra a tab de Anexos
                tabAnexosItem.show();
            } else {
                // Esconde a tab de Anexos
                tabAnexosItem.hide();

                // Se a tab de Anexos estiver ativa, volta para a tab de Histórico
                var tabAnexosLink = tabAnexosItem.find('a');
                if (tabAnexosLink.hasClass('active')) {
                    tabAnexosLink.removeClass('active');
                    $('#kt_tab_pane_2').removeClass('show active');
                    $('#kt_tab_pane_1').addClass('show active');
                    $('a[href="#kt_tab_pane_1"]').addClass('active');
                }
            }
        });

        // Função auxiliar para converter valor brasileiro (com vírgula) para número
        function parseValorBrasileiro(valorStr) {
            if (!valorStr || valorStr === '') return 0;
            // Remove pontos (milhares) e substitui vírgula por ponto
            return parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;
        }

        // Função auxiliar para formatar número para formato brasileiro
        function formatarValorBrasileiro(valor) {
            if (isNaN(valor) || valor === null || valor === undefined) return '0,00';
            return valor.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Função para calcular e atualizar o "Valor a Pagar"
        function calcularValorAPagar() {
            var valor = parseValorBrasileiro($('#valor2').val() || '0');
            var juros = parseValorBrasileiro($('#juros').val() || '0');
            var multa = parseValorBrasileiro($('#multa').val() || '0');
            var desconto = parseValorBrasileiro($('#desconto').val() || '0');

            // Calcula: valor_a_pagar = valor + juros + multa - desconto
            var valorAPagar = valor + juros + multa - desconto;

            // Atualiza o campo (sempre positivo ou zero)
            if (valorAPagar < 0) valorAPagar = 0;

            var valorFormatado = formatarValorBrasileiro(valorAPagar);
            var campoValorAPagar = $('#valor_a_pagar');

            // Se o Inputmask estiver aplicado, usa o método setValue para atualizar com a máscara
            if (campoValorAPagar.length && campoValorAPagar[0].inputmask) {
                campoValorAPagar[0].inputmask.setValue(valorFormatado);
            } else {
                campoValorAPagar.val(valorFormatado);
            }
        }

        // Controla a exibição dos wrappers de checkboxes baseado no tipo de transação
        function toggleCheckboxesByTipo() {
            var tipoSelect = $('#tipo');
            var tipo = tipoSelect.val(); // 'entrada' ou 'saida'

            var wrapperEntrada = $('#checkboxes-entrada-wrapper');
            var wrapperSaida = $('#checkboxes-saida-wrapper');

            console.log('[toggleCheckboxesByTipo] Tipo:', tipo);
            console.log('[toggleCheckboxesByTipo] Wrapper Entrada existe:', wrapperEntrada.length > 0);
            console.log('[toggleCheckboxesByTipo] Wrapper Saida existe:', wrapperSaida.length > 0);

            if (tipo === 'entrada') {
                // Receita: Mostra apenas Recebido
                console.log('[toggleCheckboxesByTipo] Mostrando checkboxes de ENTRADA');
                wrapperEntrada.show();
                wrapperSaida.hide();

                // Desmarca checkboxes de Saída
                $('#pago_checkbox').prop('checked', false);
                $('#agendado_checkbox').prop('checked', false);
            } else if (tipo === 'saida') {
                // Despesa: Mostra Pago e Agendado
                console.log('[toggleCheckboxesByTipo] Mostrando checkboxes de SAÍDA');
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
                console.log('[toggleCheckboxesByTipo] Tipo não definido, mostrando SAÍDA por padrão');
                // Default: mostrar saída
                wrapperEntrada.hide();
                wrapperSaida.show();
            }

            // Atualiza visibilidade do checkbox inner baseado no parcelamento
            toggleCheckboxPago();
        }

        // Controla a exibição do checkbox "Agendado" baseado no estado do checkbox "Pago"
        function toggleCheckboxAgendado() {
            var pagoCheckbox = $('#pago_checkbox');
            var agendadoWrapper = $('#checkbox-agendado-wrapper');

            if (pagoCheckbox.is(':checked')) {
                // Oculta o checkbox Agendado quando Pago está marcado
                agendadoWrapper.hide();
                // Desmarca o checkbox Agendado se estiver marcado
                $('#agendado_checkbox').prop('checked', false);
            } else {
                // Mostra o checkbox Agendado quando Pago não está marcado
                agendadoWrapper.show();
            }
        }

        // Controla a exibição do accordion "Informações do Pagamento"
        // Função para atualizar o valor pago automaticamente quando o valor principal mudar
        function atualizarValorPagoAutomaticamente() {
            // Verifica se o accordion está visível e o checkbox Pago está marcado
            var accordionInformacoes = $('#kt_accordion_informacoes_pagamento');
            var pagoCheckbox = $('#pago_checkbox');

            if (!accordionInformacoes.is(':visible') || !pagoCheckbox.is(':checked')) {
                return; // Não atualiza se o accordion não estiver visível ou o checkbox não estiver marcado
            }

            var valorPagoField = $('#valor_pago');
            var valorPrincipalStr = $('#valor2').val() || '0';

            // Remove formatação para verificar se tem valor
            var valorPrincipalNumerico = parseFloat(valorPrincipalStr.replace(/\./g, '').replace(',', '.')) ||
                0;

            if (valorPrincipalNumerico > 0) {
                // Atualiza o valor pago com o valor principal
                setTimeout(function() {
                    if (typeof Inputmask !== 'undefined') {
                        try {
                            // Converte o valor para número e aplica usando Inputmask
                            Inputmask.setValue(valorPagoField[0], valorPrincipalNumerico.toString());
                        } catch (e) {
                            // Fallback: define diretamente
                            valorPagoField.val(valorPrincipalStr);
                            valorPagoField.trigger('input');
                        }
                    } else {
                        // Caso contrário, define diretamente
                        valorPagoField.val(valorPrincipalStr);
                        valorPagoField.trigger('input');
                    }
                    // Dispara o evento change para recalcular
                    setTimeout(function() {
                        valorPagoField.trigger('change');
                    }, 50);
                }, 100);
            }
        }

        function toggleAccordionInformacoesPagamento() {
            var parcelamento = $('#parcelamento').val();
            var pagoCheckbox = $('#pago_checkbox');
            var accordionInformacoes = $('#kt_accordion_informacoes_pagamento');
            var accordionPrevisao = $('#kt_accordion_previsao_pagamento');

            // Obtém os valores dos campos descrição e valor
            var descricao = $('#descricao').val() || '';
            var valorStr = $('#valor2').val() || '0';

            // Remove formatação do valor para verificar se tem valor real
            var valorNumerico = parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;

            // Verifica se descrição tem dados (trim para remover espaços)
            var descricaoPreenchida = descricao.trim().length > 0;

            // Verifica se valor tem dados (maior que zero)
            var valorPreenchido = valorNumerico > 0;

            // Mostra o accordion apenas se:
            // 1. Parcelamento é "À vista" ou "1x"
            // 2. Checkbox Pago está marcado
            // 3. Descrição tem dados
            // 4. Valor tem dados
            if ((parcelamento === 'avista' || parcelamento === '1x') &&
                pagoCheckbox.is(':checked') &&
                descricaoPreenchida &&
                valorPreenchido) {
                accordionInformacoes.show();
                // Oculta o accordion de previsão quando o accordion de informações do pagamento está visível
                accordionPrevisao.hide();

                // Atualiza o valor pago automaticamente quando o accordion é exibido
                atualizarValorPagoAutomaticamente();

                // Calcula os valores quando o accordion é exibido
                setTimeout(function() {
                    calcularResumoPagamento();
                    // Expande o accordion do resumo da baixa se houver dados
                    var resumoBaixaBody = $('#kt_accordion_resumo_baixa_body');
                    if ($('#resumo_baixa_tbody tr').length > 0 && resumoBaixaBody.length) {
                        var bsCollapse = new bootstrap.Collapse(resumoBaixaBody[0], {
                            show: true
                        });
                    }
                }, 200);
            } else {
                accordionInformacoes.hide();
                // Restaura o accordion de previsão se necessário
                if (parcelamento === '1x' && !pagoCheckbox.is(':checked')) {
                    accordionPrevisao.show();
                }
            }
        }

        // Valida que o valor pago não seja maior que o valor principal
        function validarValorPago() {
            var valorPrincipalStr = $('#valor2').val() || '0';
            var valorPagoStr = $('#valor_pago').val() || '0';

            // Remove formatação
            var valorPrincipal = parseFloat(valorPrincipalStr.replace(/\./g, '').replace(',', '.')) || 0;
            var valorPago = parseFloat(valorPagoStr.replace(/\./g, '').replace(',', '.')) || 0;

            var valorPagoField = $('#valor_pago');
            var errorMessage = valorPagoField.closest('.fv-row').find('.invalid-feedback');

            if (valorPago > valorPrincipal) {
                valorPagoField.addClass('is-invalid');
                if (errorMessage.length === 0) {
                    valorPagoField.closest('.fv-row').append(
                        '<div class="invalid-feedback">O valor pago não pode ser maior que o valor principal (R$ ' +
                        valorPrincipal.toFixed(2).replace('.', ',') + ')</div>');
                } else {
                    errorMessage.text('O valor pago não pode ser maior que o valor principal (R$ ' +
                        valorPrincipal.toFixed(2).replace('.', ',') + ')');
                }
                return false;
            } else {
                valorPagoField.removeClass('is-invalid');
                errorMessage.remove();
                return true;
            }
        }

        // Calcula e exibe o resumo do pagamento (Total a pagar ou Valor em aberto)
        function calcularResumoPagamento() {
            var valorPrincipalStr = $('#valor2').val() || '0';
            var valorPagoStr = $('#valor_pago').val() || '0';
            var jurosStr = $('#juros_pagamento').val() || '0';
            var multaStr = $('#multa_pagamento').val() || '0';
            var descontoStr = $('#desconto_pagamento').val() || '0';

            // Remove formatação
            var valorPrincipal = parseFloat(valorPrincipalStr.replace(/\./g, '').replace(',', '.')) || 0;
            var valorPago = parseFloat(valorPagoStr.replace(/\./g, '').replace(',', '.')) || 0;
            var juros = parseFloat(jurosStr.replace(/\./g, '').replace(',', '.')) || 0;
            var multa = parseFloat(multaStr.replace(/\./g, '').replace(',', '.')) || 0;
            var desconto = parseFloat(descontoStr.replace(/\./g, '').replace(',', '.')) || 0;

            // IMPORTANTE: O desconto não gera fracionamento
            // Para calcular valor em aberto, considera apenas valor_pago + juros + multa (SEM desconto)
            var valorParaComparacao = valorPago + juros + multa;

            // Calcula o total a pagar (valor pago + juros + multa - desconto) - usado apenas para exibição
            var totalPagar = valorParaComparacao - desconto;

            // Calcula o valor em aberto (valor principal - valor_pago - juros - multa, SEM desconto)
            var valorAberto = valorPrincipal - valorParaComparacao;

            // Garante que o valor em aberto não seja negativo
            if (valorAberto < 0) {
                valorAberto = 0;
            }

            var valorAbertoContainer = $('#valor_aberto_container');
            var totalPagarContainer = $('#total_pagar_container');

            // Formata valores para exibição
            function formatarMoeda(valor) {
                return 'R$ ' + Math.abs(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g,
                    '.');
            }

            if (valorPago > 0) {
                // Sempre mostra o Total a pagar quando houver valor pago
                totalPagarContainer.show();
                $('#total_pagar_display').text(formatarMoeda(totalPagar));

                // Calcula o valor em aberto
                if (valorAberto > 0.01) {
                    // Há valor em aberto - mostra ambos
                    valorAbertoContainer.show();
                    $('#valor_aberto_display').text(formatarMoeda(valorAberto));
                } else {
                    // Não há valor em aberto (total pago >= valor principal) - mostra apenas Total a pagar
                    valorAbertoContainer.hide();
                    $('#valor_aberto_display').text(formatarMoeda(0));
                }

                // Atualiza a tabela do resumo da baixa
                atualizarResumoBaixa();
            } else {
                // Nenhum valor pago ainda
                valorAbertoContainer.hide();
                totalPagarContainer.hide();
                $('#total_pagar_display').text(formatarMoeda(0));
                $('#valor_aberto_display').text(formatarMoeda(0));
                // Limpa a tabela do resumo
                $('#resumo_baixa_tbody').empty();
            }
        }

        // Função para atualizar a tabela do resumo da baixa
        function atualizarResumoBaixa() {
            var tbody = $('#resumo_baixa_tbody');
            tbody.empty();

            var valorPagoStr = $('#valor_pago').val() || '0';
            var valorPago = parseFloat(valorPagoStr.replace(/\./g, '').replace(',', '.')) || 0;

            // Só atualiza se houver valor pago
            if (valorPago <= 0) {
                return;
            }

            var dataPagamento = $('#data_pagamento').val() || '';
            var valorPrincipalStr = $('#valor2').val() || '0';
            var jurosStr = $('#juros_pagamento').val() || '0';
            var multaStr = $('#multa_pagamento').val() || '0';
            var descontoStr = $('#desconto_pagamento').val() || '0';

            // Remove formatação
            var valorPrincipal = parseFloat(valorPrincipalStr.replace(/\./g, '').replace(',', '.')) || 0;
            var juros = parseFloat(jurosStr.replace(/\./g, '').replace(',', '.')) || 0;
            var multa = parseFloat(multaStr.replace(/\./g, '').replace(',', '.')) || 0;
            var desconto = parseFloat(descontoStr.replace(/\./g, '').replace(',', '.')) || 0;

            // IMPORTANTE: O desconto não gera fracionamento
            // Para calcular valor em aberto, considera apenas valor_pago + juros + multa (SEM desconto)
            var valorParaComparacao = valorPago + juros + multa;

            // Calcula o total a pagar (valor pago + juros + multa - desconto) - usado apenas para exibição
            var totalPagar = valorParaComparacao - desconto;

            // Calcula o valor em aberto (valor principal - valor_pago - juros - multa, SEM desconto)
            var valorAberto = valorPrincipal - valorParaComparacao;

            // Garante que o valor em aberto não seja negativo
            if (valorAberto < 0) {
                valorAberto = 0;
            }

            // Obtém forma de pagamento e conta
            var formaPagamentoSelect = $('#entidade_id');
            var formaPagamentoTexto = formaPagamentoSelect.find('option:selected').text() || '';

            // Tenta obter conta de pagamento (pode não existir no formulário principal)
            var contaPagamentoTexto = '';
            var contaPagamentoSelect = $('#conta_pagamento_id, #conta_financeira_id');
            if (contaPagamentoSelect.length && contaPagamentoSelect.val()) {
                contaPagamentoTexto = contaPagamentoSelect.find('option:selected').text() || '';
            }

            // Formata valores para exibição
            function formatarMoeda(valor) {
                return Math.abs(valor).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function formatarData(data) {
                return data || '';
            }

            // Linha 1: Lançamento Pago
            var linhaPago = '<tr>' +
                '<td>' + formatarData(dataPagamento) + '</td>' +
                '<td>' + formaPagamentoTexto + '</td>' +
                '<td>' + contaPagamentoTexto + '</td>' +
                '<td class="text-end">' + formatarMoeda(valorPago) + '</td>' +
                '<td class="text-end">' + formatarMoeda(juros + multa) + '</td>' +
                '<td class="text-end">' + formatarMoeda(desconto) + '</td>' +
                '<td><span class="badge badge-light-success">Pago</span></td>' +
                '</tr>';

            tbody.append(linhaPago);

            // Linha 2: Lançamento Em Aberto (se houver valor em aberto)
            if (valorAberto > 0.01) {
                var linhaAberto = '<tr>' +
                    '<td>' + formatarData(dataPagamento) + '</td>' +
                    '<td>' + formaPagamentoTexto + '</td>' +
                    '<td>' + contaPagamentoTexto + '</td>' +
                    '<td class="text-end">' + formatarMoeda(valorAberto) + '</td>' +
                    '<td class="text-end">' + formatarMoeda(0) + '</td>' +
                    '<td class="text-end">' + formatarMoeda(0) + '</td>' +
                    '<td><span class="badge badge-light-warning">Em aberto</span></td>' +
                    '</tr>';

                tbody.append(linhaAberto);
            }
        }

        // Controla a exibição do checkbox "Pago" e "Recebido" baseado no parcelamento
        function toggleCheckboxPago() {
            var parcelamentoSelect = $('#parcelamento');
            var parcelamento = parcelamentoSelect.val();
            var checkboxPagoWrapper = $('#checkbox-pago-wrapper');
            var checkboxRecebidoWrapper = $('#checkbox-recebido-wrapper');
            var accordionPrevisao = $('#kt_accordion_previsao_pagamento');
            var accordionParcelas = $('#kt_accordion_parcelas');

            console.log('[toggleCheckboxPago] Parcelamento:', parcelamento);
            console.log('[toggleCheckboxPago] Pago wrapper existe:', checkboxPagoWrapper.length > 0);
            console.log('[toggleCheckboxPago] Recebido wrapper existe:', checkboxRecebidoWrapper.length > 0);

            if (parcelamento === 'avista' || parcelamento === '1x') {
                // Mostra o checkbox apropriado (Pago para saída, Recebido para entrada)
                console.log('[toggleCheckboxPago] Mostrando checkboxes (avista/1x)');
                checkboxPagoWrapper.show();
                checkboxRecebidoWrapper.show();
                // Atualiza a visibilidade do Agendado quando o Pago aparece
                toggleCheckboxAgendado();
                // Atualiza o accordion de informações do pagamento
                toggleAccordionInformacoesPagamento();
            } else {
                // Oculta ambos os checkboxes quando tem mais de 1x parcelas
                console.log('[toggleCheckboxPago] Ocultando checkboxes (parcelado)');
                checkboxPagoWrapper.hide();
                checkboxRecebidoWrapper.hide();
                // Desmarca os checkboxes se estiverem marcados
                $('#pago_checkbox').prop('checked', false);
                if (typeof $ !== 'undefined') {
                    $('#recebido_checkbox').prop('checked', false);
                } else {
                    var recebidoCheckbox = document.getElementById('recebido_checkbox');
                    if (recebidoCheckbox) recebidoCheckbox.checked = false;
                }
                // Mostra o Agendado quando o Pago é ocultado
                toggleCheckboxAgendado();
                // Oculta o accordion de informações do pagamento
                $('#kt_accordion_informacoes_pagamento').hide();
            }

            // Controla a exibição dos accordions
            if (parcelamento === 'avista') {
                // Oculta o accordion de previsão quando for "À vista"
                // O accordion de informações do pagamento será controlado por toggleAccordionInformacoesPagamento()
                accordionPrevisao.hide();
                accordionParcelas.hide();
            } else if (parcelamento === '1x') {
                // Controla accordions baseado no estado do checkbox Pago
                var pagoCheckbox = $('#pago_checkbox');
                if (pagoCheckbox.is(':checked')) {
                    // Se Pago está marcado, mostra o accordion de informações do pagamento
                    accordionPrevisao.hide();
                } else {
                    // Se Pago não está marcado, mostra o accordion de previsão
                    accordionPrevisao.show();
                    setTimeout(function() {
                        calcularValorAPagar();
                    }, 100);
                }
                accordionParcelas.hide();
            } else if (parcelamento && parcelamento !== 'avista' && parcelamento !== '1x') {
                // Verifica se descrição e valor estão preenchidos antes de mostrar o accordion
                var descricao = $('#descricao').val() || '';
                var valorStr = $('#valor2').val() || '0';
                var valorNumerico = parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;

                var descricaoPreenchida = descricao.trim().length > 0;
                var valorPreenchido = valorNumerico > 0;

                // Mostra o accordion de parcelas apenas se descrição e valor estiverem preenchidos
                if (descricaoPreenchida && valorPreenchido) {
                    accordionPrevisao.hide();
                    accordionParcelas.show();
                    // Gera as linhas da tabela de parcelas
                    gerarParcelas(parcelamento);
                } else {
                    accordionPrevisao.hide();
                    accordionParcelas.hide();
                }
            } else {
                accordionPrevisao.hide();
                accordionParcelas.hide();
            }
        }

        // Função para inicializar máscaras de moeda nos campos do modal
        function inicializarMascarasMoeda() {
            if (typeof Inputmask === 'undefined') {
                console.warn('Inputmask não está disponível');
                return;
            }

            var camposMoeda = ['#valor2', '#juros', '#multa', '#desconto', '#valor_a_pagar', '#valor_pago',
                '#juros_pagamento', '#multa_pagamento', '#desconto_pagamento'
            ];

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
                        console.error('Erro ao inicializar máscara para ' + seletor + ':', error);
                    }
                }
            });
        }

        // Adiciona event listeners para recalcular "Valor a Pagar" quando os campos mudarem
        $(document).ready(function() {
            // Inicializa máscaras quando o modal abrir
            $('#Dm_modal_financeiro').on('shown.bs.modal', function() {
                setTimeout(function() {
                    inicializarMascarasMoeda();

                    // Calcula o valor inicial quando o modal abrir (se o accordion estiver visível)
                    if ($('#kt_accordion_previsao_pagamento').is(':visible')) {
                        calcularValorAPagar();
                    }

                    // Inicializa visibilidade dos checkboxes baseado no tipo
                    toggleCheckboxesByTipo();
                }, 200);
            });

            // Quando o valor principal mudar
            $(document).on('input change blur', '#valor2', function() {
                calcularValorAPagar();
            });

            // Quando juros, multa ou desconto mudarem
            $(document).on('input change blur', '#juros, #multa, #desconto', function() {
                calcularValorAPagar();
            });
        });

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
            var valorTotal = parseFloat(valorTotalStr.replace(/\./g, '').replace(',', '.')) || 0;
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

                var row = `
                                                    <tr data-parcela="${i}">
                                                        <td>${i}</td>
                                                        <td style="width: 150px;">
                                                            <input type="text"
                                                                class="form-control form-control-sm"
                                                                name="parcelas[${i}][vencimento]"
                                                                value="${dataFormatada}"
                                                                placeholder="dd/mm/yyyy"
                                                                data-parcela-input="vencimento"
                                                                data-parcela-num="${i}">
                                                        </td>
                                                        <td style="width: 150px;">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">R$</span>
                                                                <input type="text"
                                                                    class="form-control ${i === numParcelas ? 'bg-light' : ''}"
                                                                    name="parcelas[${i}][valor]"
                                                                    value="${valorFormatado}"
                                                                    placeholder="0,00"
                                                                    data-parcela-input="valor"
                                                                    data-parcela-num="${i}"
                                                                    ${i === numParcelas ? 'readonly style="cursor: not-allowed;"' : ''}>
                                                            </div>
                                                        </td>
                                                        <td style="width: 150px;">
                                                            <input type="text"
                                                                class="form-control form-control-sm ${i === numParcelas ? 'bg-light' : ''}"
                                                                name="parcelas[${i}][percentual]"
                                                                value="${percentualParcela}"
                                                                placeholder="0,00"
                                                                data-parcela-input="percentual"
                                                                data-parcela-num="${i}"
                                                                ${i === numParcelas ? 'readonly style="cursor: not-allowed;"' : ''}>
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm"
                                                                name="parcelas[${i}][forma_pagamento_id]"
                                                                data-parcela-input="forma_pagamento"
                                                                data-parcela-num="${i}"
                                                                data-control="select2"
                                                                data-placeholder="Selecione"
                                                                data-allow-clear="true"
                                                                data-minimum-results-for-search="0"
                                                                data-dropdown-parent="#Dm_modal_financeiro">
                                                                <option value="">Selecione</option>
                                                                @if (isset($formasPagamento))
                                                                    @foreach ($formasPagamento as $formaPagamento)
                                                                        <option value="{{ $formaPagamento->id }}">{{ $formaPagamento->id }} - {{ $formaPagamento->nome }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-select form-select-sm"
                                                                name="parcelas[${i}][conta_pagamento_id]"
                                                                data-parcela-input="conta_pagamento"
                                                                data-parcela-num="${i}"
                                                                data-control="select2"
                                                                data-placeholder="Selecione"
                                                                data-allow-clear="true"
                                                                data-minimum-results-for-search="0"
                                                                data-dropdown-parent="#Dm_modal_financeiro">
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
                                                                class="form-control form-control-sm"
                                                                name="parcelas[${i}][descricao]"
                                                                value="${descricaoBase} ${i}/${numParcelas}"
                                                                placeholder="Descrição"
                                                                data-parcela-input="descricao"
                                                                data-parcela-num="${i}"
                                                                data-descricao-base="${descricaoBase}">
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-check-custom form-check-solid">
                                                                <input class="form-check-input"
                                                                    type="checkbox"
                                                                    name="parcelas[${i}][agendado]"
                                                                    value="1"
                                                                    data-parcela-input="agendado"
                                                                    data-parcela-num="${i}">
                                                                <label class="form-check-label">
                                                                    Agendado
                                                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="O pagamento será agendado para a data do campo &quot;Vencimento&quot;, mas não será marcado como pago automaticamente. Ele será marcado como pago apenas quando você fizer isso manualmente."></i>
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                `;
                tbody.append(row);
            }

            // Inicializa os datepickers, Select2 e tooltips
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
                        } else if (typeof $ !== 'undefined' && typeof $.fn.flatpickr !==
                            'undefined') {
                            $(this).flatpickr({
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

                // Inicializa os Select2 para Forma de pagamento e Conta para pagamento
                tbody.find(
                    'select[data-parcela-input="forma_pagamento"], select[data-parcela-input="conta_pagamento"]'
                ).each(function() {
                    var $select = $(this);

                    // Verifica se já foi inicializado
                    if ($select.hasClass('select2-hidden-accessible') ||
                        $select.attr('data-kt-initialized') === '1') {
                        return;
                    }

                    // Prepara opções do Select2
                    var options = {
                        placeholder: $select.attr('data-placeholder') || 'Selecione',
                        allowClear: $select.attr('data-allow-clear') === 'true',
                        minimumResultsForSearch: parseInt($select.attr(
                            'data-minimum-results-for-search')) || 0
                    };

                    // Configura dropdownParent se especificado
                    var dropdownParent = $select.attr('data-dropdown-parent');
                    if (dropdownParent) {
                        var parentElement = $(dropdownParent);
                        if (parentElement.length) {
                            options.dropdownParent = parentElement;
                        }
                    }

                    // Inicializa Select2
                    try {
                        if (typeof KTSelect2 !== 'undefined') {
                            new KTSelect2(this);
                        } else if (typeof $ !== 'undefined' && typeof $.fn.select2 !==
                            'undefined') {
                            $select.select2(options);
                        }
                        $select.attr('data-kt-initialized', '1');
                    } catch (error) {
                        console.error('Erro ao inicializar Select2:', error);
                    }
                });

                // Inicializa os tooltips do Bootstrap
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    tbody.find('[data-bs-toggle="tooltip"]').each(function() {
                        new bootstrap.Tooltip(this);
                    });
                }

                // Adiciona event listeners para recalcular valores e percentuais
                adicionarEventListenersParcelas(numParcelas);
            }, 100);
        }

        // Função para adicionar event listeners de recálculo
        function adicionarEventListenersParcelas(numParcelas) {
            var tbody = $('#parcelas_table_body');

            // Remove listeners anteriores para evitar duplicação
            tbody.off('input change', 'input[data-parcela-input="valor"]');
            tbody.off('input change', 'input[data-parcela-input="percentual"]');

            // Event listener para quando o valor mudar
            tbody.on('input change blur', 'input[data-parcela-input="valor"]', function() {
                var parcelaNum = parseInt($(this).attr('data-parcela-num'));
                if (parcelaNum === numParcelas) return; // Ignora se for a última

                var valorStr = $(this).val() || '0';
                var valor = parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;

                // Validação: valor não pode ser 0
                if (valor <= 0) {
                    $(this).addClass('is-invalid');
                    var errorMsg = $('<div class="invalid-feedback">O valor não pode ser zero.</div>');
                    $(this).closest('td').find('.invalid-feedback').remove();
                    $(this).closest('td').append(errorMsg);
                    return;
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).closest('td').find('.invalid-feedback').remove();
                }

                recalcularPorValor(numParcelas, parcelaNum);
            });

            // Event listener para quando o percentual mudar
            tbody.on('input change blur', 'input[data-parcela-input="percentual"]', function() {
                var parcelaNum = parseInt($(this).attr('data-parcela-num'));
                if (parcelaNum === numParcelas) return; // Ignora se for a última

                var percentualStr = $(this).val() || '0';
                var percentual = parseFloat(percentualStr.replace(',', '.')) || 0;

                // Validação: percentual não pode ser 0
                if (percentual <= 0) {
                    $(this).addClass('is-invalid');
                    var errorMsg = $(
                        '<div class="invalid-feedback">O percentual não pode ser zero.</div>');
                    $(this).closest('td').find('.invalid-feedback').remove();
                    $(this).closest('td').append(errorMsg);
                    return;
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).closest('td').find('.invalid-feedback').remove();
                }

                recalcularPorPercentual(numParcelas, parcelaNum);
            });
        }

        // Função para recalcular quando o valor é alterado
        function recalcularPorValor(numParcelas, parcelaAlterada) {
            var valorTotalStr = $('#valor2').val() || '0';
            var valorTotal = parseFloat(valorTotalStr.replace(/\./g, '').replace(',', '.')) || 0;

            if (valorTotal <= 0) return;

            var somaValores = 0;

            // Calcula a soma dos valores editáveis (exceto a última)
            for (var i = 1; i < numParcelas; i++) {
                var valorInput = $('input[data-parcela-input="valor"][data-parcela-num="' + i + '"]');
                var valorStr = valorInput.val() || '0';
                var valor = parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;

                // Validação: valor não pode ser 0
                if (valor <= 0 && i !== parcelaAlterada) {
                    valorInput.addClass('is-invalid');
                    var errorMsg = $('<div class="invalid-feedback">O valor não pode ser zero.</div>');
                    valorInput.closest('td').find('.invalid-feedback').remove();
                    valorInput.closest('td').append(errorMsg);
                } else {
                    valorInput.removeClass('is-invalid');
                    valorInput.closest('td').find('.invalid-feedback').remove();
                }

                somaValores += valor;
            }

            // Calcula o valor da última parcela (resto)
            var valorUltima = valorTotal - somaValores;
            // Validação: valor da última parcela não pode ser 0
            if (valorUltima <= 0) {
                valorUltima = 0;
                var valorUltimaInput = $('input[data-parcela-input="valor"][data-parcela-num="' + numParcelas +
                    '"]');
                valorUltimaInput.addClass('is-invalid');
                var errorMsg = $(
                    '<div class="invalid-feedback">O valor total das parcelas não pode exceder o valor principal.</div>'
                );
                valorUltimaInput.closest('td').find('.invalid-feedback').remove();
                valorUltimaInput.closest('td').append(errorMsg);
            } else {
                var valorUltimaInput = $('input[data-parcela-input="valor"][data-parcela-num="' + numParcelas +
                    '"]');
                valorUltimaInput.removeClass('is-invalid');
                valorUltimaInput.closest('td').find('.invalid-feedback').remove();
            }

            // Atualiza o valor da última parcela
            var valorUltimaFormatado = valorUltima.toFixed(2).replace('.', ',');
            $('input[data-parcela-input="valor"][data-parcela-num="' + numParcelas + '"]').val(
                valorUltimaFormatado);

            // Recalcula os percentuais baseado nos valores
            for (var i = 1; i <= numParcelas; i++) {
                var valorInput = $('input[data-parcela-input="valor"][data-parcela-num="' + i + '"]');
                var valorStr = valorInput.val() || '0';
                var valor = parseFloat(valorStr.replace(/\./g, '').replace(',', '.')) || 0;

                var percentual = (valor / valorTotal) * 100;
                var percentualFormatado = percentual.toFixed(2);

                $('input[data-parcela-input="percentual"][data-parcela-num="' + i + '"]').val(
                    percentualFormatado);
            }
        }

        // Função para recalcular quando o percentual é alterado
        function recalcularPorPercentual(numParcelas, parcelaAlterada) {
            var valorTotalStr = $('#valor2').val() || '0';
            var valorTotal = parseFloat(valorTotalStr.replace(/\./g, '').replace(',', '.')) || 0;

            if (valorTotal <= 0) return;

            var somaPercentuais = 0;

            // Calcula a soma dos percentuais editáveis (exceto a última)
            for (var i = 1; i < numParcelas; i++) {
                var percentualInput = $('input[data-parcela-input="percentual"][data-parcela-num="' + i + '"]');
                var percentualStr = percentualInput.val() || '0';
                var percentual = parseFloat(percentualStr.replace(',', '.')) || 0;

                // Validação: percentual não pode ser 0
                if (percentual <= 0 && i !== parcelaAlterada) {
                    percentualInput.addClass('is-invalid');
                    var errorMsg = $('<div class="invalid-feedback">O percentual não pode ser zero.</div>');
                    percentualInput.closest('td').find('.invalid-feedback').remove();
                    percentualInput.closest('td').append(errorMsg);
                } else {
                    percentualInput.removeClass('is-invalid');
                    percentualInput.closest('td').find('.invalid-feedback').remove();
                }

                somaPercentuais += percentual;
            }

            // Garante que a soma não ultrapasse 100%
            if (somaPercentuais > 100) {
                // Ajusta o percentual da parcela alterada
                var percentualAtual = parseFloat($('input[data-parcela-input="percentual"][data-parcela-num="' +
                    parcelaAlterada + '"]').val().replace(',', '.')) || 0;
                var diferenca = somaPercentuais - 100;
                var novoPercentual = percentualAtual - diferenca;
                if (novoPercentual < 0) novoPercentual = 0;

                $('input[data-parcela-input="percentual"][data-parcela-num="' + parcelaAlterada + '"]').val(
                    novoPercentual.toFixed(2));
                somaPercentuais = 100;
            }

            // Calcula o percentual da última parcela (resto)
            var percentualUltima = 100 - somaPercentuais;
            // Validação: percentual da última parcela não pode ser 0
            if (percentualUltima <= 0) {
                percentualUltima = 0;
                var percentualUltimaInput = $('input[data-parcela-input="percentual"][data-parcela-num="' +
                    numParcelas + '"]');
                percentualUltimaInput.addClass('is-invalid');
                var errorMsg = $(
                    '<div class="invalid-feedback">A soma dos percentuais não pode exceder 100%.</div>');
                percentualUltimaInput.closest('td').find('.invalid-feedback').remove();
                percentualUltimaInput.closest('td').append(errorMsg);
            } else {
                var percentualUltimaInput = $('input[data-parcela-input="percentual"][data-parcela-num="' +
                    numParcelas + '"]');
                percentualUltimaInput.removeClass('is-invalid');
                percentualUltimaInput.closest('td').find('.invalid-feedback').remove();
            }

            // Atualiza o percentual da última parcela
            $('input[data-parcela-input="percentual"][data-parcela-num="' + numParcelas + '"]').val(
                percentualUltima.toFixed(2));

            // Recalcula os valores baseado nos percentuais
            for (var i = 1; i <= numParcelas; i++) {
                var percentualInput = $('input[data-parcela-input="percentual"][data-parcela-num="' + i + '"]');
                var percentualStr = percentualInput.val() || '0';
                var percentual = parseFloat(percentualStr.replace(',', '.')) || 0;

                var valor = (valorTotal * percentual) / 100;
                var valorFormatado = valor.toFixed(2).replace('.', ',');

                $('input[data-parcela-input="valor"][data-parcela-num="' + i + '"]').val(valorFormatado);
            }
        }

        // Atualiza parcelas quando o valor total mudar
        $('#valor2').on('blur change', function() {
            var parcelamento = $('#parcelamento').val();
            if (parcelamento && parcelamento !== 'avista' && parcelamento !== '1x') {
                gerarParcelas(parcelamento);
            }
        });

        // Evento para Select2
        $('#parcelamento').on('change.select2', function() {
            toggleCheckboxPago();
        });

        // Evento para select normal (fallback)
        $('#parcelamento').on('change', function() {
            toggleCheckboxPago();
        });

        // Evento para select de tipo (entrada/saida) - atualiza checkboxes visíveis
        $('#tipo').on('change', function() {
            toggleCheckboxesByTipo();
        });

        // Evento para Select2 do tipo
        $('#tipo').on('change.select2', function() {
            toggleCheckboxesByTipo();
        });

        // Evento para checkbox "Pago" - oculta/mostra o checkbox "Agendado" e controla accordion
        $(document).on('change', '#pago_checkbox', function() {
            toggleCheckboxAgendado();
            toggleAccordionInformacoesPagamento();
        });

        // Evento para checkbox "Recebido" - controla accordion de informações de recebimento
        if (typeof $ !== 'undefined') {
            $(document).on('change', '#recebido_checkbox', function() {
                toggleAccordionInformacoesPagamento();
            });
        } else {
            var recebidoCheckbox = document.getElementById('recebido_checkbox');
            if (recebidoCheckbox) {
                recebidoCheckbox.addEventListener('change', function() {
                    toggleAccordionInformacoesPagamento();
                });
            }
        }

        // Função para atualizar as descrições das parcelas quando a descrição principal mudar
        function atualizarDescricoesParcelas() {
            var descricaoBase = $('#descricao').val() || '';
            var parcelamento = $('#parcelamento').val();

            // Verifica se há parcelas geradas
            if (!parcelamento || parcelamento === 'avista' || parcelamento === '1x') {
                return;
            }

            var numParcelas = parseInt(parcelamento.replace('x', ''));
            if (isNaN(numParcelas) || numParcelas < 2) {
                return;
            }

            // Atualiza cada campo de descrição das parcelas
            for (var i = 1; i <= numParcelas; i++) {
                var descricaoField = $('input[data-parcela-input="descricao"][data-parcela-num="' + i + '"]');
                if (descricaoField.length) {
                    // Atualiza apenas com a descrição base + número da parcela (ex: "Descrição 1/3")
                    descricaoField.val(descricaoBase + ' ' + i + '/' + numParcelas);
                    descricaoField.attr('data-descricao-base', descricaoBase);
                }
            }
        }

        // Eventos para campos "Descrição" e "Valor" - atualiza accordion quando mudarem
        $(document).on('blur change input', '#descricao', function() {
            toggleAccordionInformacoesPagamento();

            // Atualiza as descrições das parcelas quando a descrição principal mudar
            atualizarDescricoesParcelas();

            // Verifica se deve mostrar/ocultar o accordion de parcelas
            toggleCheckboxPago();
        });

        // Valida valor pago também quando o valor principal mudar
        // E atualiza o accordion de informações do pagamento
        $(document).on('blur change input', '#valor2', function() {
            // Atualiza o accordion de informações do pagamento
            toggleAccordionInformacoesPagamento();

            // Atualiza o valor pago automaticamente quando o valor principal mudar
            atualizarValorPagoAutomaticamente();

            // Verifica se deve mostrar/ocultar o accordion de parcelas
            toggleCheckboxPago();

            if ($('#valor_pago').val()) {
                validarValorPago();
                calcularResumoPagamento();
            }
        });

        // Eventos para campos de pagamento - validação e cálculo
        $(document).on('blur change input', '#valor_pago', function() {
            if (validarValorPago()) {
                calcularResumoPagamento();
            }
        });

        $(document).on('blur change input', '#juros_pagamento, #multa_pagamento, #desconto_pagamento',
            function() {
                calcularResumoPagamento();
            });

        // Atualiza resumo quando a data do pagamento mudar
        $(document).on('change', '#data_pagamento', function() {
            atualizarResumoBaixa();
        });

        // Atualiza resumo quando a forma de pagamento mudar
        $(document).on('change', '#entidade_id', function() {
            atualizarResumoBaixa();
        });

        // Atualiza resumo quando a conta mudar (se existir)
        $(document).on('change', '#conta_pagamento_id, #conta_financeira_id', function() {
            atualizarResumoBaixa();
        });

        // Verifica estado inicial do parcelamento quando o modal abrir
        $('#Dm_modal_financeiro').on('shown.bs.modal', function() {
            setTimeout(function() {
                toggleCheckboxPago();
                toggleCheckboxAgendado();
                toggleAccordionInformacoesPagamento();
            }, 150);
        });

        // Sincronizar Data de Competência com Vencimento e ajustar Parcelamento
        function syncCompetenciaVencimento() {
            var competenciaInput = document.getElementById('data_competencia');
            var vencimentoInput = document.getElementById('vencimento');
            var parcelamentoSelect = $('#parcelamento');

            if (!competenciaInput || !vencimentoInput) return;

            // Função para processar a mudança
            var handleDateChange = function(selectedDates, dateStr, instance) {
                // 1. Atualiza o vencimento com a mesma data
                if (vencimentoInput._flatpickr) {
                    vencimentoInput._flatpickr.setDate(dateStr);
                } else {
                    vencimentoInput.value = dateStr;
                }

                // 2. Compara com a data atual para definir o parcelamento
                // Só executa se NÃO estiver em modo de recorrência
                if (!$('#flexSwitchDefault').is(':checked')) {
                    if (selectedDates.length > 0) {
                        var selectedDate = selectedDates[0];
                        // Zera as horas para comparar apenas a data
                        selectedDate.setHours(0, 0, 0, 0);

                        var today = new Date();
                        today.setHours(0, 0, 0, 0);

                        if (selectedDate > today) {
                            // Se for maior (futuro) -> 1x
                            if (parcelamentoSelect.val() !== '1x') {
                                parcelamentoSelect.val('1x').trigger('change');
                            }
                        } else {
                            // Se for menor ou igual (hoje ou passado) -> À Vista
                            if (parcelamentoSelect.val() !== 'avista') {
                                parcelamentoSelect.val('avista').trigger('change');
                            }
                        }
                    }
                }
            };

            // Tenta acessar a instância do Flatpickr
            if (competenciaInput._flatpickr) {
                // Adiciona hook no onChange
                competenciaInput._flatpickr.config.onChange.push(handleDateChange);
            } else {
                // Se não estiver pronto ainda, tenta novamente em breve
                setTimeout(syncCompetenciaVencimento, 500);
            }
        }

        // Inicia a sincronização quando o modal é exibido
        $('#Dm_modal_financeiro').on('shown.bs.modal', function() {
            syncCompetenciaVencimento();
            setupRecurringDateLogic();
        });

        function setupRecurringDateLogic() {
            var diaCobrancaSelect = $('#dia_cobranca');
            var vencimentoInput = document.getElementById('vencimento');

            // Função para atualizar o estado do Flatpickr (desabilitar dias)
            function updateFlatpickrState(dayValue) {
                if (!vencimentoInput._flatpickr) return;

                if (!$('#flexSwitchDefault').is(':checked') || !dayValue) {
                    // Se não for recorrente ou sem dia, habilita tudo
                    vencimentoInput._flatpickr.set('disable', []);
                    return;
                }

                vencimentoInput._flatpickr.set('disable', [
                    function(date) {
                        // Retorna true para desabilitar

                        // Se for "ultimo", permite apenas o último dia do mês
                        if (dayValue === 'ultimo') {
                            var lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0)
                                .getDate();
                            return date.getDate() !== lastDayOfMonth;
                        }

                        // Se for dia específico
                        var targetDay = parseInt(dayValue);
                        var lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0)
                            .getDate();

                        // Se o dia alvo for maior que o último dia do mês (ex: 30 de Fev),
                        // permite o último dia do mês
                        if (targetDay > lastDayOfMonth) {
                            return date.getDate() !== lastDayOfMonth;
                        }

                        return date.getDate() !== targetDay;
                    }
                ]);
            }

            // 1. Ao mudar o dia de cobrança -> Atualiza o vencimento e o estado do calendário
            diaCobrancaSelect.on('change', function() {
                if (!$('#flexSwitchDefault').is(':checked')) return;

                var day = $(this).val();

                // Atualiza a visualização do calendário
                updateFlatpickrState(day);

                if (!day || !vencimentoInput._flatpickr) return;

                var currentDate = vencimentoInput._flatpickr.selectedDates[0] || new Date();
                var year = currentDate.getFullYear();
                var month = currentDate.getMonth();
                var newDate;

                if (day === 'ultimo') {
                    // Último dia do mês
                    newDate = new Date(year, month + 1, 0);
                } else {
                    // Dia específico
                    var lastDayOfMonth = new Date(year, month + 1, 0).getDate();
                    var dayNum = parseInt(day);

                    if (dayNum > lastDayOfMonth) {
                        dayNum = lastDayOfMonth;
                    }
                    newDate = new Date(year, month, dayNum);
                }

                vencimentoInput._flatpickr.setDate(newDate,
                true); // true para disparar onChange se necessário, mas nosso onChange evita loop
            });

            // Hook para quando abrir o calendário, garantir que a visualização está correta
            if (vencimentoInput._flatpickr) {
                vencimentoInput._flatpickr.config.onOpen.push(function() {
                    if ($('#flexSwitchDefault').is(':checked')) {
                        updateFlatpickrState(diaCobrancaSelect.val());
                    } else {
                        // Garante que está limpo se não for recorrente
                        vencimentoInput._flatpickr.set('disable', []);
                    }
                });
            }
        }

        });
        }

        // Inicializa quando o DOM estiver pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPagamentoParcelasRecorrencia);
        } else {
            initPagamentoParcelasRecorrencia();
        }
    })();
</script>
