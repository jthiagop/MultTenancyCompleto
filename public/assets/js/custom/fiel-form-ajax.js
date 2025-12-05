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
                        }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
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
            });
    };

    var clearFormErrors = function() {
        // Limpar todos os erros do formulário
        var errorElements = form.querySelectorAll('.invalid-feedback');
        errorElements.forEach(function(el) {
            el.textContent = '';
            el.style.display = 'none';
        });

        var invalidInputs = form.querySelectorAll('.is-invalid');
        invalidInputs.forEach(function(el) {
            el.classList.remove('is-invalid');
        });

        var fvRows = form.querySelectorAll('.fv-row.has-danger');
        fvRows.forEach(function(el) {
            el.classList.remove('has-danger');
        });

        // Limpar também nos selects
        var invalidSelects = form.querySelectorAll('.form-select.is-invalid');
        invalidSelects.forEach(function(el) {
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
                                    var errorElement = document.getElementById(field + '-error');
                                    var inputElement = form.querySelector('[name="' + field + '"]');

                                    if (errorElement && result.data.errors[field].length > 0) {
                                        var errorMessage = result.data.errors[field][0];
                                        errorElement.textContent = errorMessage;
                                        errorElement.style.display = 'block';

                                        // Adicionar classe de erro no input/select
                                        if (inputElement) {
                                            inputElement.classList.add('is-invalid');

                                            // Adicionar classe no fv-row pai
                                            var fvRow = inputElement.closest('.fv-row');
                                            if (fvRow) {
                                                fvRow.classList.add('has-danger');
                                            }

                                            // Se for um select, também adicionar no wrapper
                                            if (inputElement.tagName === 'SELECT') {
                                                var selectWrapper = inputElement.closest('.fv-row');
                                                if (selectWrapper) {
                                                    selectWrapper.classList.add('has-danger');
                                                }
                                            }
                                        }
                                        hasErrors = true;
                                    }
                                }

                                // Se houver erros, mostrar alerta também
                                if (hasErrors) {
                                    var errorMessages = '';
                                    for (var field in result.data.errors) {
                                        errorMessages += result.data.errors[field].join('<br>') + '<br>';
                                    }
                                    Swal.fire({
                                        text: "Por favor, corrija os erros no formulário.",
                                        icon: "error",
                                        buttonsStyling: false,
                                        confirmButtonText: "Ok, entendi!",
                                        customClass: {
                                            confirmButton: "btn btn-primary"
                                        }
                                    });

                                    // Scroll para o primeiro erro
                                    var firstError = form.querySelector('.is-invalid');
                                    if (firstError) {
                                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    }
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
                    Swal.fire({
                        text: "Por favor, corrija os erros no formulário.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
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

