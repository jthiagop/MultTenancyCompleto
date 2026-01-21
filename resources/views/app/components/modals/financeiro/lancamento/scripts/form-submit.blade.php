<script>
// Script para capturar cliques nos botões do split button e enviar formulário
    $(document).ready(function() {
        // Usa event delegation para garantir que funcione mesmo se os elementos forem criados dinamicamente
        var form = null;
        var submitButton = null;
        var cloneButton = null;
        var novoButton = null;

        // Função para obter referências dos elementos
        function getElements() {
            form = document.getElementById('Dm_modal_financeiro_form');
            submitButton = document.getElementById('Dm_modal_financeiro_submit');
            cloneButton = document.getElementById('Dm_modal_financeiro_clone');
            novoButton = document.getElementById('Dm_modal_financeiro_novo');
            return form !== null;
        }

        // Tenta obter os elementos imediatamente
        if (!getElements()) {
            // Se não encontrou, tenta novamente quando o modal abrir
            $('#Dm_modal_financeiro').on('shown.bs.modal', function() {
                getElements();
            });
        }

        // Limpa erros quando o modal é fechado
        $('#Dm_modal_financeiro').on('hidden.bs.modal', function() {
            if (form) {
                clearFormErrors();
            }
        });

        // Função para limpar todos os erros do formulário
        function clearFormErrors() {
            if (!form) return;

            // Remove classes de erro de todos os campos
            form.querySelectorAll('.is-invalid').forEach(function(field) {
                field.classList.remove('is-invalid');
            });

            // Remove mensagens de erro
            form.querySelectorAll('.invalid-feedback').forEach(function(errorMsg) {
                errorMsg.remove();
            });

            // Remove classes de erro dos Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(form).find('.select2-container').each(function() {
                    $(this).removeClass('is-invalid');
                    $(this).css('border-color', '');
                });
            }
        }

        // Função para exibir erros nos campos
        function displayFieldErrors(errors) {
            if (!form || !errors) return;

            // Limpa erros anteriores
            clearFormErrors();

            var firstErrorField = null;

            // Itera sobre os erros
            Object.keys(errors).forEach(function(fieldName) {
                var fieldErrors = errors[fieldName];
                var errorMessage = Array.isArray(fieldErrors) ? fieldErrors[0] : fieldErrors;

                // Converte o nome do campo do formato Laravel (parcelas.1.valor) para formato HTML (parcelas[1][valor])
                var htmlFieldName = fieldName;
                if (fieldName.includes('.')) {
                    // Converte parcelas.1.valor para parcelas[1][valor]
                    var parts = fieldName.split('.');
                    htmlFieldName = parts[0];
                    for (var i = 1; i < parts.length; i++) {
                        htmlFieldName += '[' + parts[i] + ']';
                    }
                }

                // Encontra o campo no formulário
                var field = form.querySelector('[name="' + htmlFieldName + '"]');

                if (!field) {
                    // Tenta encontrar pelo nome original também (caso já esteja no formato correto)
                    field = form.querySelector('[name="' + fieldName + '"]');
                }

                // Se ainda não encontrou, tenta encontrar usando data-attributes para campos de parcelas
                if (!field && fieldName.includes('parcelas')) {
                    var parts = fieldName.split('.');
                    if (parts.length >= 3) {
                        var parcelaIndex = parts[1];
                        var campoNome = parts[2];
                        field = form.querySelector('[data-parcela-num="' + parcelaIndex +
                            '"][data-parcela-input="' + campoNome + '"]');
                    }
                }

                if (!field) {
                    // Tenta encontrar por ID (escapando caracteres especiais para CSS)
                    var escapedFieldName = fieldName.replace(/[.#\[\]]/g, '\\$&');
                    try {
                        field = form.querySelector('#' + escapedFieldName);
                    } catch (e) {
                        // Se o seletor for inválido, ignora
                        console.warn('Seletor inválido para campo:', fieldName);
                    }
                }

                if (field) {
                    // Guarda o primeiro campo com erro para scroll
                    if (!firstErrorField) {
                        firstErrorField = field;
                    }

                    // Adiciona classe de erro no campo
                    field.classList.add('is-invalid');

                    // Encontra o container do campo (fv-row ou parent)
                    var fieldContainer = field.closest('.fv-row');
                    if (!fieldContainer) {
                        fieldContainer = field.closest('.input-group') || field.parentElement;
                    }

                    // Remove mensagem de erro existente
                    var existingError = fieldContainer.querySelector('.invalid-feedback');
                    if (existingError) {
                        existingError.remove();
                    }

                    // Cria nova mensagem de erro
                    var errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback d-block';
                    errorDiv.setAttribute('role', 'alert');
                    errorDiv.id = fieldName + '-error';
                    errorDiv.textContent = errorMessage;
                    errorDiv.style.display = 'block';
                    errorDiv.style.color = '#f1416c';

                    // Insere após o campo ou no final do container
                    if (fieldContainer) {
                        // Se o campo está dentro de um input-group, insere após o input-group
                        var inputGroup = field.closest('.input-group');
                        if (inputGroup && inputGroup.parentElement) {
                            inputGroup.parentElement.appendChild(errorDiv);
                        } else {
                            fieldContainer.appendChild(errorDiv);
                        }
                    } else {
                        // Fallback: insere após o campo
                        field.parentNode.insertBefore(errorDiv, field.nextSibling);
                    }

                    // Para Select2, também adiciona classe no container
                    if (typeof $ !== 'undefined' && $.fn.select2 && $(field).hasClass(
                            'select2-hidden-accessible')) {
                        var select2Container = $(field).next('.select2-container');
                        if (select2Container.length) {
                            select2Container.addClass('is-invalid');
                            // Adiciona estilo para borda vermelha
                            select2Container.css('border-color', '#f1416c');
                        }
                    }

                    // Adiciona evento para limpar erro quando o usuário começar a digitar/selecionar
                    var clearErrorOnChange = function() {
                        field.classList.remove('is-invalid');
                        var errorMsg = document.getElementById(fieldName + '-error');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                        if (typeof $ !== 'undefined' && $.fn.select2 && $(field).hasClass(
                                'select2-hidden-accessible')) {
                            var select2Container = $(field).next('.select2-container');
                            if (select2Container.length) {
                                select2Container.removeClass('is-invalid');
                                select2Container.css('border-color', '');
                            }
                        }
                        // Remove o listener após limpar o erro
                        field.removeEventListener('input', clearErrorOnChange);
                        field.removeEventListener('change', clearErrorOnChange);
                        if ($(field).hasClass('select2-hidden-accessible')) {
                            $(field).off('change.select2', clearErrorOnChange);
                        }
                    };

                    // Adiciona listeners para limpar erro
                    if (field.tagName === 'SELECT') {
                        if (typeof $ !== 'undefined' && $(field).hasClass(
                            'select2-hidden-accessible')) {
                            $(field).on('change.select2', clearErrorOnChange);
                        } else {
                            field.addEventListener('change', clearErrorOnChange);
                        }
                    } else {
                        field.addEventListener('input', clearErrorOnChange);
                        field.addEventListener('change', clearErrorOnChange);
                    }
                }
            });

            // Scroll para o primeiro campo com erro
            if (firstErrorField) {
                setTimeout(function() {
                    firstErrorField.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    firstErrorField.focus();
                }, 300);
            }
        }

        // Função para enviar formulário (similar ao new-banco.js)
        // Função de validação manual para campos de recorrência
        function validarCamposRecorrencia() {
            // Se o checkbox de recorrência estiver marcado
            if ($('#flexSwitchDefault').is(':checked')) {
                var diaCobranca = $('#dia_cobranca').val();
                var vencimento = document.getElementById('vencimento').value;
                var configuracaoRecorrencia = $('#configuracao_recorrencia').val();
                var isValid = true;

                var diaCobrancaSelect = $('#dia_cobranca');
                var vencimentoInput = $('#vencimento');
                var configuracaoRecorrenciaSelect = $('#configuracao_recorrencia');

                // Limpa erros anteriores
                diaCobrancaSelect.removeClass('is-invalid');
                diaCobrancaSelect.closest('.fv-row').find('.invalid-feedback').remove();
                if (diaCobrancaSelect.next('.select2-container').length) {
                    diaCobrancaSelect.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }

                vencimentoInput.removeClass('is-invalid');
                vencimentoInput.closest('.fv-row').find('.invalid-feedback').remove();

                configuracaoRecorrenciaSelect.removeClass('is-invalid');
                configuracaoRecorrenciaSelect.closest('#configuracao-recorrencia-wrapper').find('.invalid-feedback').remove();
                if (configuracaoRecorrenciaSelect.next('.select2-container').length) {
                    configuracaoRecorrenciaSelect.next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }

                // Verifica Configuração de Recorrência
                if (!configuracaoRecorrencia) {
                    configuracaoRecorrenciaSelect.addClass('is-invalid');
                    if (configuracaoRecorrenciaSelect.next('.select2-container').length) {
                        configuracaoRecorrenciaSelect.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    }
                     var errorDiv = $('<div class="invalid-feedback d-block">O campo Configuração é obrigatório.</div>');
                    configuracaoRecorrenciaSelect.closest('#configuracao-recorrencia-wrapper').append(errorDiv);
                    isValid = false;
                }

                // Verifica Dia de Cobrança

                // Verifica Dia de Cobrança
                if (!diaCobranca) {
                    diaCobrancaSelect.addClass('is-invalid');
                    // Se for Select2, precisa adicionar classe ao container também ou tratar visualmente
                    if (diaCobrancaSelect.next('.select2-container').length) {
                        diaCobrancaSelect.next('.select2-container').find('.select2-selection').addClass('is-invalid');
                    }

                    var errorDiv = $('<div class="invalid-feedback d-block">O campo Dia de Cobrança é obrigatório.</div>');
                    diaCobrancaSelect.closest('.fv-row').append(errorDiv);
                    isValid = false;
                }

                // Verifica Vencimento (que agora é 1º Vencimento)
                if (!vencimento) {
                    vencimentoInput.addClass('is-invalid');
                    var errorDiv = $('<div class="invalid-feedback d-block">O campo 1º Vencimento é obrigatório.</div>');
                    vencimentoInput.closest('.fv-row').append(errorDiv);
                    isValid = false;
                }

                return isValid;
            }
            return true;
        }

        // Adiciona listeners para limpar erros em tempo real
        $(document).ready(function() {
            // Quando uma configuração de recorrência é selecionada (existente ou nova)
            $('#configuracao_recorrencia').on('change', function() {
                var selectedValue = $(this).val();

                // Remove classes de erro
                $(this).removeClass('is-invalid');
                if ($(this).next('.select2-container').length) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }
                $(this).closest('#configuracao-recorrencia-wrapper').find('.invalid-feedback').remove();

                // Se for um ID numérico (configuração existente do banco), passa o ID
                if (selectedValue && selectedValue !== '' && !isNaN(selectedValue) && !selectedValue.toString().startsWith('temp_')) {
                    // Remove campos temporários e adiciona o ID da configuração
                    $('#Dm_modal_financeiro_form').find('input[name="intervalo_repeticao"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="frequencia"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="apos_ocorrencias"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="configuracao_recorrencia_temp"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="configuracao_recorrencia"]').remove();

                    // Converte para número para garantir que seja tratado como inteiro
                    var configId = parseInt(selectedValue, 10);
                    $('#Dm_modal_financeiro_form').append(
                        '<input type="hidden" name="configuracao_recorrencia" value="' + configId + '">');
                } else if (selectedValue && selectedValue.toString().startsWith('temp_')) {
                    // Se for temp_ (configuração nova criada no drawer), mantém os campos temporários
                    // que serão processados no criarRecorrencia
                    // Remove o campo configuracao_recorrencia se existir (para não conflitar)
                    $('#Dm_modal_financeiro_form').find('input[name="configuracao_recorrencia"]').remove();
                } else if (!selectedValue || selectedValue === '') {
                    // Se não houver seleção, remove todos os campos relacionados
                    $('#Dm_modal_financeiro_form').find('input[name="intervalo_repeticao"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="frequencia"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="apos_ocorrencias"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="configuracao_recorrencia"]').remove();
                    $('#Dm_modal_financeiro_form').find('input[name="configuracao_recorrencia_temp"]').remove();
                }
            });

            $('#dia_cobranca').on('change', function() {
                $(this).removeClass('is-invalid');
                if ($(this).next('.select2-container').length) {
                    $(this).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
                }
                $(this).closest('.fv-row').find('.invalid-feedback').remove();
            });

            $('#vencimento').on('input change', function() {
                // Para flatpickr, o change é disparado
                $(this).removeClass('is-invalid');
                $(this).closest('.fv-row').find('.invalid-feedback').remove();
            });
        });

        function enviarFormulario(mode, event) {
            // Previne o submit padrão se o evento for fornecido
            if (event) {
                event.preventDefault();
            }

            // Garante que temos as referências atualizadas
            if (!getElements() || !form) {
                console.error('Formulário não encontrado');
                Swal.fire({
                    text: "Erro: Formulário não encontrado. Por favor, recarregue a página.",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok",
                    customClass: {
                        confirmButton: "btn btn-primary"
                    }
                });
                return;
            }

            // Limpa erros anteriores antes de enviar
            clearFormErrors();

            // Função auxiliar para converter valor brasileiro (com vírgula) para número
            var parseValorBrasileiro = function(valorStr) {
                if (!valorStr || valorStr === '' || valorStr.trim() === '') return 0;

                // Se já é um número válido (contém apenas números e um ponto decimal), retorna direto
                if (/^\d+\.?\d*$/.test(valorStr.replace(/\s/g, ''))) {
                    return parseFloat(valorStr) || 0;
                }

                // Se contém vírgula, é formato brasileiro (1.500,00 ou 25,00)
                if (valorStr.indexOf(',') !== -1) {
                    // Remove pontos (milhares) e substitui vírgula por ponto
                    var valorLimpo = valorStr.replace(/\./g, '').replace(',', '.');
                    return parseFloat(valorLimpo) || 0;
                }

                // Se não tem vírgula nem ponto, ou tem múltiplos pontos, tenta parse direto
                return parseFloat(valorStr.replace(/\./g, '')) || 0;
            };

            // Valida o campo valor antes de enviar
            var valorInput = form.querySelector('[name="valor"]') || form.querySelector('#valor2');
            if (valorInput) {
                var valorStr = valorInput.value || '';
                var valorNumero = parseValorBrasileiro(valorStr);

                // Valida se o valor é maior que zero
                if (!valorStr || valorStr.trim() === '' || isNaN(valorNumero) || valorNumero <= 0) {
                    // Exibe erro no campo valor
                    displayFieldErrors({
                        'valor': 'O valor deve ser maior que zero.'
                    });

                    // Reabilita botões
                    if (submitButton) {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                    }
                    if (cloneButton) cloneButton.style.pointerEvents = 'auto';
                    if (novoButton) novoButton.style.pointerEvents = 'auto';

                    return; // Interrompe o envio
                }
            }

            // Prepara os dados do formulário
            var formData = new FormData(form);

            // Converte o valor do formato brasileiro para numérico antes de enviar
            if (valorInput && valorInput.value) {
                var valorStr = valorInput.value || '';
                var valorNumero = parseValorBrasileiro(valorStr);

                // Atualiza o FormData com o valor convertido (formato numérico: 52.00)
                formData.delete('valor');
                formData.append('valor', valorNumero.toString());
            }

            // Adiciona campos de pagamento se o checkbox "Pago" estiver marcado
            var pagoCheckbox = form.querySelector('#pago_checkbox');
            if (pagoCheckbox && pagoCheckbox.checked) {
                var valorPagoInput = form.querySelector('#valor_pago');
                if (valorPagoInput && valorPagoInput.value) {
                    var valorPagoStr = valorPagoInput.value || '';
                    var valorPagoNumero = parseValorBrasileiro(valorPagoStr);
                    formData.delete('valor_pago');
                    formData.append('valor_pago', valorPagoNumero.toString());
                }

                var dataPagamentoInput = form.querySelector('#data_pagamento');
                if (dataPagamentoInput && dataPagamentoInput.value) {
                    formData.delete('data_pagamento');
                    formData.append('data_pagamento', dataPagamentoInput.value);
                }

                var jurosPagamentoInput = form.querySelector('#juros_pagamento');
                if (jurosPagamentoInput && jurosPagamentoInput.value) {
                    var jurosPagamentoStr = jurosPagamentoInput.value || '';
                    var jurosPagamentoNumero = parseValorBrasileiro(jurosPagamentoStr);
                    formData.delete('juros_pagamento');
                    formData.append('juros_pagamento', jurosPagamentoNumero.toString());
                }

                var multaPagamentoInput = form.querySelector('#multa_pagamento');
                if (multaPagamentoInput && multaPagamentoInput.value) {
                    var multaPagamentoStr = multaPagamentoInput.value || '';
                    var multaPagamentoNumero = parseValorBrasileiro(multaPagamentoStr);
                    formData.delete('multa_pagamento');
                    formData.append('multa_pagamento', multaPagamentoNumero.toString());
                }

                var descontoPagamentoInput = form.querySelector('#desconto_pagamento');
                if (descontoPagamentoInput && descontoPagamentoInput.value) {
                    var descontoPagamentoStr = descontoPagamentoInput.value || '';
                    var descontoPagamentoNumero = parseValorBrasileiro(descontoPagamentoStr);
                    formData.delete('desconto_pagamento');
                    formData.append('desconto_pagamento', descontoPagamentoNumero.toString());
                }
            }

            // Garante que o tipo seja sempre enviado
            var tipoSelect = form.querySelector('[name="tipo"]');
            var tipoValue = tipoSelect ? tipoSelect.value : '';

            if (tipoValue) {
                formData.delete('tipo');
                formData.append('tipo', tipoValue);
            }

            // Garante que comprovacao_fiscal seja enviado como 0 ou 1
            var comprovacaoFiscal = form.querySelector('[name="comprovacao_fiscal"]');
            if (comprovacaoFiscal) {
                formData.delete('comprovacao_fiscal');
                formData.append('comprovacao_fiscal', comprovacaoFiscal.checked ? '1' : '0');
            }

            // Processa valores das parcelas - converte do formato brasileiro para numérico
            var allInputs = form.querySelectorAll('input');
            allInputs.forEach(function(input) {
                if (input.name && input.name.startsWith('parcelas[') && input.name.includes(
                    '][valor]')) {
                    var valorStr = input.value || '';
                    if (valorStr && valorStr.trim() !== '') {
                        var valorNumero = parseValorBrasileiro(valorStr);
                        // Remove o valor antigo e adiciona o convertido
                        formData.delete(input.name);
                        formData.append(input.name, valorNumero.toString());
                    }
                }
            });

            // Processa percentuais das parcelas - converte do formato brasileiro para numérico
            allInputs.forEach(function(input) {
                if (input.name && input.name.startsWith('parcelas[') && input.name.includes(
                        '][percentual]')) {
                    var percentualStr = input.value || '';
                    if (percentualStr && percentualStr.trim() !== '') {
                        var percentualNumero = parseValorBrasileiro(percentualStr);
                        // Remove o valor antigo e adiciona o convertido
                        formData.delete(input.name);
                        formData.append(input.name, percentualNumero.toString());
                    }
                }
            });

            // Garante que as datas das parcelas estão no formato correto (d/m/Y)
            allInputs.forEach(function(input) {
                if (input.name && input.name.startsWith('parcelas[') && input.name.includes(
                        '][vencimento]')) {
                    var dataStr = input.value || '';
                    if (dataStr && dataStr.trim() !== '') {
                        // Remove espaços e garante formato d/m/Y
                        var dataLimpa = dataStr.trim().replace(/\s+/g, '');
                        // Se a data está em formato diferente, tenta converter
                        // Mas mantém o formato d/m/Y para validação
                        formData.delete(input.name);
                        formData.append(input.name, dataLimpa);
                    }
                }
            });

            // Formata a data para o formato esperado pelo backend (d-m-Y)
            var dataInput = form.querySelector('[name="data_competencia"]');
            if (dataInput && dataInput.value) {
                var dataValue = dataInput.value;
                formData.delete('data_competencia');

                if (dataValue.includes('-')) {
                    var partes = dataValue.split('-');
                    if (partes.length === 3) {
                        if (partes[0].length === 4) {
                            // Formato Y-m-d, converte para d-m-Y
                            formData.append('data_competencia', partes[2] + '-' + partes[1] + '-' + partes[0]);
                        } else {
                            // Já está em d-m-Y ou d/m/Y
                            formData.append('data_competencia', dataValue.replace(/\//g, '-'));
                        }
                    } else {
                        formData.append('data_competencia', dataValue);
                    }
                } else if (dataValue.includes('/')) {
                    // Formato d/m/Y, converte para d-m-Y
                    formData.append('data_competencia', dataValue.replace(/\//g, '-'));
                } else {
                    formData.append('data_competencia', dataValue);
                }
            }

            // Desabilita todos os botões
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.setAttribute('data-kt-indicator', 'on');
            }
            if (cloneButton) cloneButton.style.pointerEvents = 'none';
            if (novoButton) novoButton.style.pointerEvents = 'none';

            // Obtém a URL do formulário
            var formAction = form.getAttribute('action');
            if (!formAction || formAction === '#') {
                formAction = form.getAttribute('data-action') || window.location.pathname + '/banco';
            }

            // Envia via AJAX
            fetch(formAction, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    redirect: 'follow'
                })
                .then(response => {
                    if (response.ok) {
                        return response.json().catch(() => ({
                            success: true
                        }));
                    }
                    if (response.status === 302 || response.redirected) {
                        return {
                            success: true
                        };
                    }
                    if (response.status === 422) {
                        return response.json().then(data => {
                            // Exibe erros nos campos do formulário
                            if (data.errors) {
                                displayFieldErrors(data.errors);
                            }

                            // Se houver mensagem geral, mostra apenas no console
                            if (data.message) {
                                console.error('Erro de validação:', data.message);
                            }

                            // Lança erro para ser capturado no catch, mas sem SweetAlert
                            throw new Error('validation_error');
                        });
                    }
                    return response.text().then(text => {
                        throw new Error(text || 'Erro ao salvar');
                    });
                })
                .then(data => {
                    // Limpa erros do formulário após sucesso
                    clearFormErrors();

                    // Reabilita botões
                    if (submitButton) {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                    }
                    if (cloneButton) cloneButton.style.pointerEvents = 'auto';
                    if (novoButton) novoButton.style.pointerEvents = 'auto';

                    Swal.fire({
                        text: "O lançamento foi salvo com sucesso!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    }).then(function(result) {
                        if (result.isConfirmed || result.value) {
                            if (mode === 'enviar') {
                                // Modo 1: Enviar - Fecha o modal e recarrega a página
                                var modalElement = document.getElementById('Dm_modal_financeiro');
                                if (modalElement) {
                                    var bsModal = bootstrap.Modal.getInstance(modalElement);
                                    if (bsModal) {
                                        bsModal.hide();
                                    }
                                }
                                setTimeout(function() {
                                    window.location.reload();
                                }, 300);
                            } else if (mode === 'clonar') {
                                // Modo 2: Salvar e Clonar - Mantém dados e modal aberto
                                // Limpa erros mas mantém os dados
                                clearFormErrors();
                            } else if (mode === 'branco') {
                                // Modo 3: Salvar e Limpar - Limpa formulário mantendo data e tipo
                                form.reset();
                                clearFormErrors();
                                // Limpa Select2
                                if (typeof $ !== 'undefined' && $.fn.select2) {
                                    $(form).find('select').each(function() {
                                        if ($(this).hasClass('select2-hidden-accessible')) {
                                            $(this).val(null).trigger('change');
                                        }
                                    });
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    // Reabilita botões
                    if (submitButton) {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                    }
                    if (cloneButton) cloneButton.style.pointerEvents = 'auto';
                    if (novoButton) novoButton.style.pointerEvents = 'auto';

                    // Se for erro de validação, os erros já foram exibidos nos campos
                    if (error.message === 'validation_error') {
                        return; // Não mostra SweetAlert, os erros já estão nos campos
                    }

                    // Para outros erros, mostra SweetAlert
                    var errorMessage = error.message ||
                        "Erro ao salvar o lançamento. Por favor, tente novamente.";

                    Swal.fire({
                        title: "Erro ao salvar",
                        html: errorMessage.replace(/\n/g, '<br>'),
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                });
        }

        // Usa event delegation para capturar cliques nos botões
        // Usa event delegation para capturar cliques nos botões
        $(document).on('click', '#Dm_modal_financeiro_submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Valida recorrência antes de prosseguir
            if (!validarCamposRecorrencia()) return;

            if (getElements() && form) {
                enviarFormulario('enviar', e);
            }
        });

        // Event listener para "Salvar e Clonar"
        $(document).on('click', '#Dm_modal_financeiro_clone', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Valida recorrência antes de prosseguir
            if (!validarCamposRecorrencia()) return;

            if (getElements() && form) {
                enviarFormulario('clonar', e);
            }
        });

        // Event listener para "Salvar e Limpar"
        $(document).on('click', '#Dm_modal_financeiro_novo', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Valida recorrência antes de prosseguir
            if (!validarCamposRecorrencia()) return;

            if (getElements() && form) {
                enviarFormulario('branco', e);
            }
        });

        // Também intercepta o submit do formulário para usar a mesma lógica
        $(document).on('submit', '#Dm_modal_financeiro_form', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Valida recorrência antes de prosseguir
            if (!validarCamposRecorrencia()) return;

            if (getElements() && form) {
                enviarFormulario('enviar', e);
            }
        });
    });
</script>
