"use strict";

var KTModalNewTicket = function() {
    var form;
    var submitButton;
    var cloneButton;
    var novoButton;
    var cancelButton;
    var validator;
    var modal;

    var init = function() {
        modal = document.querySelector('#kt_modal_new_ticket');
        form = document.querySelector('#kt_modal_new_ticket_form');
        submitButton = document.querySelector('#kt_modal_new_ticket_submit');
        cloneButton = document.querySelector('#kt_modal_new_ticket_clone');
        novoButton = document.querySelector('#kt_modal_new_ticket_novo');
        cancelButton = document.querySelector('#kt_modal_new_ticket_cancel');

        if (!form) return;

        // Inicializar Flatpickr para o campo de data de nascimento
        initDatepicker();

        // Inicializar máscara e validação de CPF
        initCpfValidation();

        // Validação do formulário
        validator = FormValidation.formValidation(form, {
            fields: {
                'nome_completo': {
                    validators: {
                        notEmpty: {
                            message: 'Nome completo é obrigatório'
                        }
                    }
                },
                'sexo': {
                    validators: {
                        notEmpty: {
                            message: 'Sexo é obrigatório'
                        }
                    }
                },
                'cpf': {
                    validators: {
                        notEmpty: {
                            message: 'CPF é obrigatório'
                        },
                        callback: {
                            message: 'CPF inválido',
                            callback: function(input) {
                                var cpf = input.value.replace(/\D/g, ''); // Remove caracteres não numéricos
                                if (cpf.length === 0) {
                                    return true; // Se vazio, o notEmpty vai tratar
                                }
                                return validarCPF(cpf);
                            }
                        }
                    }
                },
                'data_nascimento': {
                    validators: {
                        notEmpty: {
                            message: 'Data de nascimento é obrigatória'
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: 'is-invalid',
                    eleValidClass: 'is-valid'
                })
            }
        });

        // Submit handler - Salvar (fecha modal e recarrega)
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            var saveActionField = form.querySelector('#save_action_field');
            if (saveActionField) {
                saveActionField.value = 'submit';
            }
            handleSubmit('submit');
        });

        // Clone handler - Salvar e Clonar (mantém dados)
        if (cloneButton) {
            cloneButton.addEventListener('click', function(e) {
                e.preventDefault();
                var saveActionField = form.querySelector('#save_action_field');
                if (saveActionField) {
                    saveActionField.value = 'clone';
                }
                handleSubmit('clone');
            });
        }

        // Novo handler - Salvar e Limpar (limpa formulário)
        if (novoButton) {
            novoButton.addEventListener('click', function(e) {
                e.preventDefault();
                var saveActionField = form.querySelector('#save_action_field');
                if (saveActionField) {
                    saveActionField.value = 'new';
                }
                handleSubmit('new');
            });
        }

        // Cancel handler
        cancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                text: "Tem certeza que deseja cancelar?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, cancelar!",
                cancelButtonText: "Não, voltar",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function(result) {
                if (result.value) {
                    form.reset();
                    validator.resetForm();
                    $(modal).modal('hide');
                }
            });
        });

            // Reset form when modal is hidden
            $(modal).on('hidden.bs.modal', function() {
                form.reset();
                validator.resetForm();
                clearFormErrors();
                
                // Reset form action to create
                form.action = form.getAttribute('data-original-action') || '{{ route("fieis.store") }}';
                form.method = 'POST';
                
                // Remove method spoofing if exists
                const methodInput = form.querySelector('input[name="_method"]');
                if (methodInput) {
                    methodInput.remove();
                }
                
                // Reset modal title
                const modalTitle = document.querySelector('#kt_modal_new_ticket h1');
                if (modalTitle) {
                    modalTitle.textContent = 'Cadastro de Fiéis';
                }
                
                // Reset avatar preview
                const avatarWrapper = form.querySelector('.image-input-wrapper');
                if (avatarWrapper) {
                    avatarWrapper.style.backgroundImage = "url('/assets/media/avatars/blank.png')";
                }
                
                // Reinicializar datepicker se necessário
                initDatepicker();
                
                // Reinicializar validação de CPF
                initCpfValidation();
            });
            
            // Inicializar datepicker e validação de CPF quando o modal for aberto
            $(modal).on('shown.bs.modal', function() {
                initDatepicker();
                initCpfValidation();
                
                // Reinicializar Select2 para profissão se necessário
                var profissaoSelect = form.querySelector('[name="profissao"]');
                if (profissaoSelect && typeof $(profissaoSelect).select2 !== 'undefined') {
                    // Verificar se já está inicializado
                    if (!$(profissaoSelect).hasClass('select2-hidden-accessible')) {
                        $(profissaoSelect).select2({
                            placeholder: 'Selecione uma profissão...',
                            allowClear: true
                        });
                    }
                }
            });
    };

    // Função para validar CPF
    var validarCPF = function(cpf) {
        // Remove caracteres não numéricos
        cpf = cpf.replace(/\D/g, '');
        
        // Verifica se tem 11 dígitos
        if (cpf.length !== 11) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais (ex: 111.111.111-11)
        if (/^(\d)\1{10}$/.test(cpf)) {
            return false;
        }
        
        // Valida primeiro dígito verificador
        var soma = 0;
        for (var i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i)) * (10 - i);
        }
        var resto = 11 - (soma % 11);
        if (resto === 10 || resto === 11) {
            resto = 0;
        }
        if (resto !== parseInt(cpf.charAt(9))) {
            return false;
        }
        
        // Valida segundo dígito verificador
        soma = 0;
        for (var i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i)) * (11 - i);
        }
        resto = 11 - (soma % 11);
        if (resto === 10 || resto === 11) {
            resto = 0;
        }
        if (resto !== parseInt(cpf.charAt(10))) {
            return false;
        }
        
        return true;
    };

    var initCpfValidation = function() {
        var cpfInput = document.getElementById('cpf');
        if (!cpfInput) {
            return;
        }

        // Aplicar máscara de CPF usando Inputmask se disponível
        if (typeof Inputmask !== 'undefined') {
            Inputmask('999.999.999-99', {
                placeholder: '000.000.000-00',
                clearIncomplete: true,
                showMaskOnHover: false,
                showMaskOnFocus: true
            }).mask(cpfInput);
        }

        // Função para exibir erro
        var showCpfError = function(message) {
            var errorElement = document.getElementById('cpf-error');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                errorElement.classList.add('d-block');
            }
            cpfInput.classList.add('is-invalid');
            cpfInput.classList.remove('is-valid');
            
            // Adicionar classe no fv-row pai
            var fvRow = cpfInput.closest('.fv-row');
            if (!fvRow) {
                var parent = cpfInput.parentElement;
                while (parent && parent !== form) {
                    fvRow = parent.querySelector('.fv-row');
                    if (fvRow) break;
                    parent = parent.parentElement;
                }
            }
            if (fvRow) {
                fvRow.classList.add('has-danger');
                fvRow.classList.remove('has-success');
            }
        };

        // Função para limpar erro
        var clearCpfError = function() {
            var errorElement = document.getElementById('cpf-error');
            if (errorElement) {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
                errorElement.classList.remove('d-block');
            }
            cpfInput.classList.remove('is-invalid');
            
            // Remover classe do fv-row pai
            var fvRow = cpfInput.closest('.fv-row');
            if (!fvRow) {
                var parent = cpfInput.parentElement;
                while (parent && parent !== form) {
                    fvRow = parent.querySelector('.fv-row');
                    if (fvRow) break;
                    parent = parent.parentElement;
                }
            }
            if (fvRow) {
                fvRow.classList.remove('has-danger');
            }
        };

        // Validação quando o usuário terminar de digitar (on blur)
        var validateCpfRealTime = function() {
            var cpfValue = cpfInput.value.replace(/\D/g, ''); // Remove caracteres não numéricos
            
            // Se o campo estiver vazio, limpa o erro (o notEmpty vai tratar)
            if (cpfValue.length === 0) {
                clearCpfError();
                return;
            }
            
            // Se ainda está digitando e não tem 11 dígitos, não valida ainda
            if (cpfValue.length < 11) {
                clearCpfError();
                return;
            }
            
            // Valida o CPF
            if (validarCPF(cpfValue)) {
                clearCpfError();
                cpfInput.classList.add('is-valid');
                cpfInput.classList.remove('is-invalid');
                
                // Adicionar classe de sucesso no fv-row
                var fvRow = cpfInput.closest('.fv-row');
                if (!fvRow) {
                    var parent = cpfInput.parentElement;
                    while (parent && parent !== form) {
                        fvRow = parent.querySelector('.fv-row');
                        if (fvRow) break;
                        parent = parent.parentElement;
                    }
                }
                if (fvRow) {
                    fvRow.classList.add('has-success');
                    fvRow.classList.remove('has-danger');
                }
            } else {
                showCpfError('CPF Inválido');
            }
        };

        // Adicionar event listener apenas no blur (quando o usuário terminar de digitar)
        cpfInput.addEventListener('blur', validateCpfRealTime);
    };

    var initDatepicker = function() {
        // Verificar se Flatpickr está disponível (tentar ambas as formas)
        var flatpickrFn = null;
        if (typeof flatpickr !== 'undefined') {
            flatpickrFn = flatpickr;
        } else if (typeof $ !== 'undefined' && typeof $.fn.flatpickr !== 'undefined') {
            // Se estiver disponível via jQuery
            flatpickrFn = function(element, options) {
                return $(element).flatpickr(options);
            };
        } else {
            console.warn('Flatpickr não está disponível. O datepicker não será inicializado.');
            return;
        }

        // Buscar o input de data de nascimento
        var dateInput = document.getElementById('data_nascimento_input');
        if (!dateInput) {
            return;
        }

        // Verificar se já foi inicializado
        if (dateInput._flatpickr) {
            return;
        }

        // Inicializar Flatpickr
        try {
            flatpickrFn(dateInput, {
                dateFormat: "d/m/Y", // Formato brasileiro de data
                locale: "pt", // Define o idioma para português
                maxDate: "today", // Não permite datas futuras
                allowInput: true, // Permite digitação manual
                clickOpens: true, // Abre o calendário ao clicar
                placeholder: "Data nascimento"
            });
        } catch (error) {
            console.error('Erro ao inicializar Flatpickr:', error);
        }
    };

    var clearFormErrors = function() {
        // Remover classe was-validated do formulário
        if (form) {
            form.classList.remove('was-validated');
        }
        
        // Limpar todos os erros do formulário
        var errorElements = form.querySelectorAll('.invalid-feedback, [role="alert"].invalid-feedback');
        errorElements.forEach(function(el) {
            el.textContent = '';
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.classList.remove('d-block');
        });

        // Limpar classes de erro dos inputs, selects e textareas
        var invalidInputs = form.querySelectorAll('.is-invalid');
        invalidInputs.forEach(function(el) {
            el.classList.remove('is-invalid');
        });

        // Limpar classes de erro dos fv-rows
        var fvRows = form.querySelectorAll('.fv-row.has-danger');
        fvRows.forEach(function(el) {
            el.classList.remove('has-danger');
        });

        // Limpar classes de erro dos selects (incluindo Select2)
        var invalidSelects = form.querySelectorAll('.form-select.is-invalid, select.is-invalid');
        invalidSelects.forEach(function(el) {
            el.classList.remove('is-invalid');
            
            // Limpar também do container do Select2 se existir
            if (el.classList.contains('select2-hidden-accessible')) {
                var select2Container = el.nextElementSibling;
                if (select2Container && select2Container.classList.contains('select2-container')) {
                    select2Container.classList.remove('is-invalid');
                }
            }
        });

        // Limpar classes de erro dos containers Select2
        var select2Containers = form.querySelectorAll('.select2-container.is-invalid');
        select2Containers.forEach(function(el) {
            el.classList.remove('is-invalid');
        });
    };

    var limparFormulario = function() {
        // Limpar todos os campos do formulário
        var avatarWrapper = form.querySelector('.image-input-wrapper');
        var avatarRemoveInput = form.querySelector('input[name="avatar_remove"]');
        
        // Resetar formulário
        form.reset();
        validator.resetForm();
        clearFormErrors();
        
        // Restaurar background padrão do avatar
        if (avatarWrapper) {
            avatarWrapper.style.backgroundImage = "url('/assets/media/avatars/blank.png')";
        }
        
        // Limpar campo de remoção de avatar
        if (avatarRemoveInput) {
            avatarRemoveInput.value = '';
        }
        
        // Parar webcam se estiver ativa
        if (typeof KTWebcamCapture !== 'undefined' && KTWebcamCapture) {
            if (KTWebcamCapture.isCapturing) {
                KTWebcamCapture.stopWebcam();
            }
        }
    };

    var handleSubmit = function(actionType) {
        // Limpar erros antes de validar
        clearFormErrors();

        if (validator) {
            validator.validate().then(function(status) {
                if (status == 'Valid') {
                    // Desabilitar todos os botões durante o envio
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    if (cloneButton) {
                        cloneButton.style.pointerEvents = 'none';
                    }
                    if (novoButton) {
                        novoButton.style.pointerEvents = 'none';
                    }

                    // Criar FormData para enviar arquivos
                    var formData = new FormData(form);
                    
                    // Garantir que a ação está no FormData
                    var saveActionField = form.querySelector('#save_action_field');
                    if (saveActionField) {
                        formData.set('save_action', actionType || 'submit');
                    }

                    // Enviar via AJAX
                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return {
                                ok: response.ok,
                                status: response.status,
                                data: data
                            };
                        });
                    })
                    .then(function(result) {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                        if (cloneButton) {
                            cloneButton.style.pointerEvents = 'auto';
                        }
                        if (novoButton) {
                            novoButton.style.pointerEvents = 'auto';
                        }

                        if (result.ok && result.data.success) {
                            // Sucesso
                            Swal.fire({
                                text: result.data.message || "Fiel cadastrado com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function() {
                                // Verificar se é update ou create
                                const isUpdate = form.querySelector('input[name="_method"]') && 
                                               form.querySelector('input[name="_method"]').value === 'PUT';
                                
                                if (actionType === 'submit') {
                                    // Modo 1: Salvar - Fecha o modal e recarrega a tabela
                                    form.reset();
                                    validator.resetForm();
                                    clearFormErrors();
                                    
                                    // Reset form action if it was an update
                                    if (isUpdate) {
                                        const originalAction = form.getAttribute('data-original-action');
                                        if (originalAction) {
                                            form.action = originalAction;
                                        }
                                        const methodInput = form.querySelector('input[name="_method"]');
                                        if (methodInput) {
                                            methodInput.remove();
                                        }
                                        // Reset modal title
                                        const modalTitle = document.querySelector('#kt_modal_new_ticket h1');
                                        if (modalTitle) {
                                            modalTitle.textContent = 'Cadastro de Fiéis';
                                        }
                                        // Reset avatar
                                        const avatarWrapper = form.querySelector('.image-input-wrapper');
                                        if (avatarWrapper) {
                                            avatarWrapper.style.backgroundImage = "url('/assets/media/avatars/blank.png')";
                                        }
                                    }
                                    
                                    $(modal).modal('hide');
                                    // Recarregar a tabela via AJAX
                                    if ($.fn.DataTable.isDataTable('#kt_customers_table')) {
                                        $('#kt_customers_table').DataTable().ajax.reload(null, false);
                                    } else {
                                        location.reload();
                                    }
                                } else if (actionType === 'clone') {
                                    // Modo 2: Salvar e Clonar - Mantém dados e modal aberto
                                    // Se for update, resetar para modo create
                                    if (isUpdate) {
                                        const originalAction = form.getAttribute('data-original-action');
                                        if (originalAction) {
                                            form.action = originalAction;
                                        }
                                        const methodInput = form.querySelector('input[name="_method"]');
                                        if (methodInput) {
                                            methodInput.remove();
                                        }
                                        // Reset modal title
                                        const modalTitle = document.querySelector('#kt_modal_new_ticket h1');
                                        if (modalTitle) {
                                            modalTitle.textContent = 'Cadastro de Fiéis';
                                        }
                                    }
                                    // Recarregar a tabela via AJAX para mostrar o novo registro
                                    if ($.fn.DataTable.isDataTable('#kt_customers_table')) {
                                        $('#kt_customers_table').DataTable().ajax.reload(null, false);
                                    }
                                } else if (actionType === 'new') {
                                    // Modo 3: Salvar e Limpar - Limpa formulário mantendo modal aberto
                                    // Se for update, resetar para modo create
                                    if (isUpdate) {
                                        const originalAction = form.getAttribute('data-original-action');
                                        if (originalAction) {
                                            form.action = originalAction;
                                        }
                                        const methodInput = form.querySelector('input[name="_method"]');
                                        if (methodInput) {
                                            methodInput.remove();
                                        }
                                        // Reset modal title
                                        const modalTitle = document.querySelector('#kt_modal_new_ticket h1');
                                        if (modalTitle) {
                                            modalTitle.textContent = 'Cadastro de Fiéis';
                                        }
                                    }
                                    limparFormulario();
                                    // Recarregar a tabela via AJAX
                                    if ($.fn.DataTable.isDataTable('#kt_customers_table')) {
                                        $('#kt_customers_table').DataTable().ajax.reload(null, false);
                                    }
                                }
                            });
                        } else {
                            // Erro de validação
                            if (result.data.errors) {
                                // Limpar erros anteriores
                                clearFormErrors();
                                
                                // Reabilitar botões em caso de erro
                                if (cloneButton) {
                                    cloneButton.style.pointerEvents = 'auto';
                                }
                                if (novoButton) {
                                    novoButton.style.pointerEvents = 'auto';
                                }

                                // Exibir erros nos campos
                                var hasErrors = false;
                                for (var field in result.data.errors) {
                                    if (!result.data.errors[field] || result.data.errors[field].length === 0) {
                                        continue;
                                    }

                                    var errorMessage = result.data.errors[field][0];
                                    
                                    // Buscar o elemento de input/select/textarea pelo nome
                                    var inputElement = form.querySelector('[name="' + field + '"]');

                                    // Se não encontrar pelo nome, tentar pelo ID
                                    if (!inputElement) {
                                        inputElement = document.getElementById(field);
                                    }

                                    // Buscar o elemento de erro - tentar múltiplas formas
                                    var errorElement = document.getElementById(field + '-error');
                                    
                                    // Se não encontrar pelo ID padrão, buscar dentro do container do campo
                                    if (!errorElement && inputElement) {
                                        // Buscar no container de mensagens mais próximo (pode estar no mesmo nível ou no parent)
                                        var messageContainer = inputElement.parentElement?.querySelector('.fv-plugins-message-container');
                                        
                                        if (!messageContainer) {
                                            // Buscar no parent mais próximo que tenha o container
                                            var parent = inputElement.parentElement;
                                            while (parent && parent !== form) {
                                                messageContainer = parent.querySelector('.fv-plugins-message-container');
                                                if (messageContainer) break;
                                                parent = parent.parentElement;
                                            }
                                        }
                                        
                                        if (messageContainer) {
                                            errorElement = messageContainer.querySelector('.invalid-feedback, [role="alert"]');
                                        }
                                        
                                        // Se ainda não encontrar, buscar em qualquer lugar próximo ao input
                                        if (!errorElement) {
                                            // Buscar no mesmo container do input
                                            var inputContainer = inputElement.parentElement;
                                            if (inputContainer) {
                                                errorElement = inputContainer.querySelector('#' + field + '-error, .invalid-feedback[id*="' + field + '"], [role="alert"][id*="' + field + '"]');
                                            }
                                            
                                            // Se ainda não encontrar, buscar em qualquer fv-row próximo
                                            if (!errorElement) {
                                                var fvRow = inputElement.closest('.fv-row');
                                                if (!fvRow) {
                                                    // Buscar em qualquer parent que tenha fv-row
                                                    parent = inputElement.parentElement;
                                                    while (parent && parent !== form) {
                                                        fvRow = parent.querySelector('.fv-row');
                                                        if (fvRow) break;
                                                        parent = parent.parentElement;
                                                    }
                                                }
                                                
                                                if (fvRow) {
                                                    errorElement = fvRow.querySelector('#' + field + '-error, .invalid-feedback, [role="alert"]');
                                                }
                                            }
                                        }
                                    }

                                    // Se encontrou o elemento de erro, exibir a mensagem
                                    if (errorElement) {
                                        errorElement.textContent = errorMessage;
                                        // Forçar exibição do elemento de erro (sobrescrever qualquer CSS)
                                        errorElement.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                                        errorElement.classList.add('d-block');
                                        errorElement.removeAttribute('hidden');
                                        
                                        // Garantir que o container pai também esteja visível
                                        var errorParent = errorElement.parentElement;
                                        while (errorParent && errorParent !== form) {
                                            errorParent.style.display = 'block';
                                            errorParent.style.visibility = 'visible';
                                            if (errorParent.classList.contains('fv-plugins-message-container') || 
                                                errorParent.classList.contains('fv-help-block')) {
                                                break;
                                            }
                                            errorParent = errorParent.parentElement;
                                        }
                                    } else if (inputElement) {
                                        // Se não encontrou elemento de erro, criar um dinamicamente
                                        // Buscar o container de mensagens mais próximo
                                        var messageContainer = inputElement.closest('.fv-plugins-message-container');
                                        if (!messageContainer) {
                                            // Buscar no parent mais próximo
                                            var parent = inputElement.parentElement;
                                            while (parent && parent !== form) {
                                                messageContainer = parent.querySelector('.fv-plugins-message-container');
                                                if (messageContainer) break;
                                                
                                                // Se não encontrar, criar um novo container após o input
                                                if (!messageContainer && parent.tagName !== 'FORM') {
                                                    messageContainer = document.createElement('div');
                                                    messageContainer.className = 'fv-plugins-message-container';
                                                    
                                                    // Inserir após o input ou após o wrapper do input
                                                    if (inputElement.nextSibling) {
                                                        parent.insertBefore(messageContainer, inputElement.nextSibling);
                                                    } else {
                                                        parent.appendChild(messageContainer);
                                                    }
                                                    break;
                                                }
                                                
                                                parent = parent.parentElement;
                                            }
                                        }
                                        
                                        if (messageContainer) {
                                            var helpBlock = messageContainer.querySelector('.fv-help-block');
                                            if (!helpBlock) {
                                                helpBlock = document.createElement('div');
                                                helpBlock.className = 'fv-help-block';
                                                messageContainer.appendChild(helpBlock);
                                            }
                                            
                                            errorElement = helpBlock.querySelector('.invalid-feedback, [role="alert"]');
                                            if (!errorElement) {
                                                errorElement = document.createElement('span');
                                                errorElement.setAttribute('role', 'alert');
                                                errorElement.className = 'invalid-feedback';
                                                errorElement.id = field + '-error';
                                                helpBlock.appendChild(errorElement);
                                            }
                                            
                                            errorElement.textContent = errorMessage;
                                            // Forçar exibição do elemento de erro (sobrescrever qualquer CSS)
                                            errorElement.style.cssText = 'display: block !important; visibility: visible !important; opacity: 1 !important;';
                                            errorElement.classList.add('d-block');
                                            errorElement.removeAttribute('hidden');
                                            
                                            // Garantir que o container pai também esteja visível
                                            var errorParent = errorElement.parentElement;
                                            while (errorParent && errorParent !== form) {
                                                errorParent.style.display = 'block';
                                                errorParent.style.visibility = 'visible';
                                                if (errorParent.classList.contains('fv-plugins-message-container') || 
                                                    errorParent.classList.contains('fv-help-block')) {
                                                    break;
                                                }
                                                errorParent = errorParent.parentElement;
                                            }
                                        }
                                    }

                                    // Adicionar classe de erro no input/select/textarea
                                        if (inputElement) {
                                            inputElement.classList.add('is-invalid');

                                        // Remover classe válida se existir
                                        inputElement.classList.remove('is-valid');
                                        
                                        // Adicionar classe was-validated ao formulário para garantir que os erros sejam exibidos
                                        if (form && !form.classList.contains('was-validated')) {
                                            form.classList.add('was-validated');
                                        }

                                        // Adicionar classe no fv-row pai (buscar o mais próximo)
                                            var fvRow = inputElement.closest('.fv-row');
                                        if (!fvRow) {
                                            // Se não encontrar, buscar em qualquer parent
                                            var parent = inputElement.parentElement;
                                            while (parent && parent !== form) {
                                                fvRow = parent.querySelector('.fv-row');
                                                if (fvRow) break;
                                                // Também verificar se o próprio parent é um fv-row
                                                if (parent.classList && parent.classList.contains('fv-row')) {
                                                    fvRow = parent;
                                                    break;
                                                }
                                                parent = parent.parentElement;
                                            }
                                        }
                                        
                                            if (fvRow) {
                                                fvRow.classList.add('has-danger');
                                            fvRow.classList.remove('has-success');
                                        } else {
                                            // Se não encontrar fv-row, adicionar no parent direto do input
                                            var inputParent = inputElement.parentElement;
                                            if (inputParent && inputParent !== form) {
                                                inputParent.classList.add('has-danger');
                                                inputParent.classList.remove('has-success');
                                            }
                                        }

                                        // Tratamento especial para selects
                                            if (inputElement.tagName === 'SELECT') {
                                            // Garantir que o select2 também receba a classe de erro
                                            if (inputElement.classList.contains('select2-hidden-accessible')) {
                                                var select2Container = inputElement.nextElementSibling;
                                                if (select2Container && select2Container.classList.contains('select2-container')) {
                                                    select2Container.classList.add('is-invalid');
                                                }
                                            }
                                        }
                                    }
                                    
                                        hasErrors = true;
                                }

                                // Se houver erros, mostrar alerta com lista de erros
                                if (hasErrors) {
                                    // Criar lista de erros com nomes dos campos
                                    var errorList = [];
                                    var fieldLabels = {
                                        'nome_completo': 'Nome Completo',
                                        'data_nascimento': 'Data de Nascimento',
                                        'sexo': 'Sexo',
                                        'cpf': 'CPF',
                                        'rg': 'RG',
                                        'profissao': 'Profissão',
                                        'estado_civil': 'Estado Civil',
                                        'telefone': 'Telefone',
                                        'telefone_secundario': 'Telefone Secundário',
                                        'email': 'Email',
                                        'cep': 'CEP',
                                        'bairro': 'Bairro',
                                        'cidade': 'Cidade',
                                        'estado': 'Estado',
                                        'endereco': 'Endereço',
                                        'avatar': 'Avatar'
                                    };
                                    
                                    for (var field in result.data.errors) {
                                        if (result.data.errors[field] && result.data.errors[field].length > 0) {
                                            var fieldLabel = fieldLabels[field] || field;
                                            var errorMsg = result.data.errors[field][0];
                                            errorList.push('<strong>' + fieldLabel + ':</strong> ' + errorMsg);
                                        }
                                    }
                                    
                                    var errorHtml = '<div style="text-align: left; max-height: 300px; overflow-y: auto;">';
                                    errorHtml += '<p style="margin-bottom: 10px;">Os seguintes campos precisam ser corrigidos:</p>';
                                    errorHtml += '<ul style="margin: 0; padding-left: 20px;">';
                                    errorList.forEach(function(error) {
                                        errorHtml += '<li style="margin-bottom: 8px;">' + error + '</li>';
                                    });
                                    errorHtml += '</ul></div>';
                                    
                                    Swal.fire({
                                        html: errorHtml,
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, entendi!",
                                        width: '500px',
                                        customClass: {
                                            confirmButton: "btn btn-primary",
                                            popup: "text-start"
                                        }
                                    });

                                    // Scroll para o primeiro erro após um pequeno delay
                                    setTimeout(function() {
                                    var firstError = form.querySelector('.is-invalid');
                                    if (firstError) {
                                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    }
                                    }, 300);
                                }
                            } else {
                                Swal.fire({
                                    text: result.data.message || "Erro ao cadastrar fiel",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, entendi!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        }
                    })
                    .catch(function(error) {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                        if (cloneButton) {
                            cloneButton.style.pointerEvents = 'auto';
                        }
                        if (novoButton) {
                            novoButton.style.pointerEvents = 'auto';
                        }

                        Swal.fire({
                            text: "Erro de conexão. Por favor, tente novamente.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    });
                } else {
                    // Scroll para o primeiro erro
                    var firstError = form.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }
    };

    return {
        init: init
    };
}();

// Inicializar quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        KTModalNewTicket.init();
    });
} else {
    KTModalNewTicket.init();
}

