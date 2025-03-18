"use strict";

// Função para exibir toast usando jQuery Toast Plugin
function showToast(type, message) {
    $.toast({
        heading: type === 'success' ? 'Sucesso' : 'Erro',
        text: message,
        showHideTransition: 'slide',
        icon: type, // 'success' ou 'error'
        position: 'top-right',
        hideAfter: 5000
    });
}

var KTModalNewTarget = function () {
    var submitButton;
    var cancelButton;
    var validator;
    var form;
    var modal;
    var modalEl;
    // Botões extras para as ações de clone e novo em branco
    var cloneButton;
    var novoButton;

    // Init form inputs
    $(document).ready(function () {
        // Define o modal pai (caso os selects estejam dentro de um modal)
        var dropdownParent = $('#Dm_modal_financeiro');

        $('select[name="lancamento_padraos_id"]').select2({
            allowClear: true,
            dropdownParent: dropdownParent
        });

        $('select[name="cost_centers_id"]').select2({
            placeholder: "Selecione o Centro de Custo",
            allowClear: true,
            dropdownParent: dropdownParent
        });

        $('select[name="parcelamento"]').select2({
            placeholder: "Nº de parcelas",
            allowClear: true,
            dropdownParent: dropdownParent
        });

        $('select[name="forma_pagamento"]').select2({
            placeholder: "Selecione a forma de pagamento",
            allowClear: true,
            dropdownParent: dropdownParent
        });

        $('select[name="conta_pagamento"]').select2({
            placeholder: "Selecione o centro de custo",
            allowClear: true,
            dropdownParent: dropdownParent
        });
    });

    $(document).ready(function () {
        // Verifica o estado do checkbox de recorrência
        $('#repetir-lancamento').on('change', function () {
            var isChecked = $(this).is(':checked');
            $('#campos-recorrencia').toggle(isChecked);
            // Habilita ou desabilita os validadores dos campos de recorrência
            validator.enableValidator('repetir_a_cada', isChecked);
            validator.enableValidator('frequencia', isChecked);
            validator.enableValidator('apos_ocorrencias', isChecked);
        });

        // Configuração do jQuery Validation Plugin
        $('#kt_modal_new_target_form').validate({
            rules: {
                // Outras regras...
                // Note: As regras abaixo serão ativadas apenas quando o checkbox for marcado
                repetir_a_cada: {
                    required: function () {
                        return $('#repetir-lancamento').is(':checked');
                    },
                    min: 1
                },
                frequencia: {
                    required: function () {
                        return $('#repetir-lancamento').is(':checked');
                    }
                },
                apos_ocorrencias: {
                    required: function () {
                        return $('#repetir-lancamento').is(':checked');
                    },
                    min: 1
                }
            },
            messages: {
                repetir_a_cada: {
                    required: "O campo 'Repetir a cada' é obrigatório quando a recorrência está ativa.",
                    min: "O valor mínimo é 1."
                },
                frequencia: {
                    required: "O campo 'Frequência' é obrigatório quando a recorrência está ativa."
                },
                apos_ocorrencias: {
                    required: "O campo 'Após quantas ocorrências' é obrigatório quando a recorrência está ativa.",
                    min: "O valor mínimo é 1."
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            }
        });
    });

    // Init form inputs (ex.: Tagify, flatpickr, select2 em modal interno)
    var initForm = function() {
        // Inicializa Tagify
        var tags = new Tagify(form.querySelector('[name="tags"]'), {
            whitelist: ["Important", "Urgent", "High", "Medium", "Low"],
            maxTags: 5,
            dropdown: {
                maxItems: 10,
                enabled: 0,
                closeOnSelect: false
            }
        });
        tags.on("change", function(){
            validator.revalidateField('tags');
        });

        // Configura flatpickr e Inputmask para datas
        var dueDates = document.querySelectorAll('[name="data_competencia"], [name="vencimento"]');
        dueDates.forEach(function(dueDate) {
            flatpickr(dueDate, {
                dateFormat: "d/m/Y",
                locale: "pt",
                allowInput: true,
                defaultDate: new Date()
            });
            Inputmask("99/99/9999").mask(dueDate);
        });

        // Revalidação de campo com select2
        $(form.querySelector('[name="team_assign"]')).on('change', function() {
            validator.revalidateField('team_assign');
        });

        // Exemplo para outros selects dentro de modal
        $(document).ready(function () {
            $('#seuModalId').on('shown.bs.modal', function () {
                $('#lancamento_padrao_id, #banco_id').select2({
                    dropdownParent: $(this),
                    placeholder: 'Selecione uma opção',
                    closeOnSelect: true,
                    allowClear: true,
                    minimumResultsForSearch: 0
                });
            });
        });
    };

    // Handle form validation and submission via AJAX
    var handleForm = function() {
// Inicializa a validação com FormValidation
validator = FormValidation.formValidation(
    form,
    {
        fields: {
            data_competencia: {
                validators: {
                    notEmpty: {
                        message: 'Não esqueça da data 😉'
                    }
                }
            },
            descricao: {
                validators: {
                    notEmpty: {
                        message: '⚠️ A descrição é obrigatória'
                    }
                }
            },
            valor: {
                validators: {
                    notEmpty: {
                        message: '⚠️ O valor é obrigatório'
                    },
                    callback: {
                        message: 'O valor deve ser maior que 0',
                        callback: function (input) {
                            let val = input.value
                                .replace(/[R$\s]/g, '')
                                .replace(/\./g, '')
                                .replace(',', '.');
                            let num = parseFloat(val);
                            return (!isNaN(num) && num > 0);
                        }
                    }
                }
            },
            lancamento_padraos_id: {
                validators: {
                    notEmpty: {
                        message: 'Escolha um lançamento padrão'
                    }
                }
            },
            // Campo de Parcelamento
            parcelamento: {
                validators: {
                    notEmpty: {
                        message: 'Selecione o número de parcelas'
                    }
                }
            },
            // Campo de Vencimento
            vencimento: {
                validators: {
                    notEmpty: {
                        message: 'Informe o 1º vencimento'
                    }
                }
            },
            // Campo de Forma de pagamento
            forma_pagamento: {
                validators: {
                    notEmpty: {
                        message: 'Selecione a forma de pagamento'
                    }
                }
            },
            // Campo de Conta de pagamento (Centro de Custo)
            conta_pagamento: {
                validators: {
                    notEmpty: {
                        message: 'Selecione o centro de custo'
                    }
                }
            },
            // Caso o campo cost_center_id seja utilizado em outro lugar, mantenha se necessário
            // cost_center_id: {
            //     validators: {
            //         notEmpty: {
            //             message: 'Selecione o centro de custo'
            //         }
            //     }
            // },
            // Campos de recorrência
            repetir_a_cada: {
                enabled: false,
                validators: {
                    notEmpty: {
                        message: 'O campo "Repetir a cada" é obrigatório quando a recorrência está ativa.'
                    }
                }
            },
            frequencia: {
                enabled: false,
                validators: {
                    notEmpty: {
                        message: 'O campo "Frequência" é obrigatório quando a recorrência está ativa.'
                    }
                }
            },
            apos_ocorrencias: {
                enabled: false,
                validators: {
                    notEmpty: {
                        message: 'O campo "Após quantas ocorrências" é obrigatório quando a recorrência está ativa.'
                    }
                }
            },
            'targets_notifications[]': {
                validators: {
                    notEmpty: {
                        message: 'Please select at least one communication method'
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
    }
);


        // Eventos dos botões de ação

        // Botão "Enviar" (ação normal)
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();
            enviarFormulario('normal');
        });

        // Botão "Salvar e Clonar"
        cloneButton.addEventListener('click', function (e) {
            e.preventDefault();
            enviarFormulario('clonar');
        });

        // Botão "Salvar e em Branco"
        novoButton.addEventListener('click', function (e) {
            e.preventDefault();
            enviarFormulario('novo');
        });

        // Função que envia o formulário via AJAX
        function enviarFormulario(acao) {
            validator.validate().then(function (status) {
                if (status === 'Valid') {
                    // Indica loading e desabilita os botões
                    submitButton.setAttribute('data-kt-indicator', 'on');
                    submitButton.disabled = true;
                    cloneButton.disabled = true;
                    novoButton.disabled = true;

                    var formData = new FormData(form);

                    fetch('/contas-financeiras', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async (response) => {
                        const data = await response.json();
                        if (!response.ok) {
                            if (response.status === 422) {
                                // Erro de validação
                                throw data.errors;
                            } else {
                                throw new Error(data.message || 'Ocorreu um erro no servidor.');
                            }
                        }
                        return data;
                    })
                    .then((data) => {
                        Swal.fire({
                            text: data.message || 'Sucesso!',
                            icon: 'success'
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                if (acao === 'normal') {
                                    // Fecha o modal e reseta o formulário
                                    let modalInstance = bootstrap.Modal.getInstance(document.querySelector("#Dm_modal_financeiro"));
                                    if (modalInstance) {
                                        modalInstance.hide();
                                    }
                                    form.reset();
                                    // Recarrega a página
                                    window.location.reload();

                                } else if (acao === 'novo') {
                                    // Reseta o formulário, mantendo o modal aberto para novo cadastro
                                    form.reset();
                                }
                                // Para "clonar", mantém os dados no formulário
                            }
                        });
                    })
                    .catch((error) => {
                        let errorMsg = '';
                        if (typeof error === 'object') {
                            Object.keys(error).forEach(campo => {
                                errorMsg += error[campo].join('\n') + '\n';
                            });
                        } else {
                            errorMsg = error;
                        }
                        Swal.fire({
                            text: 'Erros de validação:\n' + errorMsg,
                            icon: 'error'
                        });
                    })
                    .finally(() => {
                        submitButton.removeAttribute('data-kt-indicator');
                        submitButton.disabled = false;
                        cloneButton.disabled = false;
                        novoButton.disabled = false;
                    });
                } else {
                    Swal.fire({
                        text: "Por favor, corrija os erros no formulário.",
                        icon: 'error'
                    });
                }
            });
        }

        // Evento para o botão de cancelamento
        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                text: "Tem certeza de que deseja cancelar?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, cancele!",
                cancelButtonText: "Não, volte",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    form.reset();
                    modal.hide();
                } else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Seu formulário não foi cancelado!",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                }
            });
        });
    };

    return {
        init: function () {
            modalEl = document.querySelector('#Dm_modal_financeiro');
            if (!modalEl) {
                return;
            }
            modal = new bootstrap.Modal(modalEl);
            form = document.querySelector('#kt_modal_new_target_form');
            submitButton = document.getElementById('kt_modal_new_target_submit');
            cancelButton = document.getElementById('kt_modal_new_target_cancel');
            cloneButton = document.getElementById('kt_modal_new_target_clone');
            novoButton = document.getElementById('kt_modal_new_target_novo');

            initForm();
            handleForm();
        }
    };
}();

document.addEventListener("DOMContentLoaded", function () {
    // Configura os botões que abrem o modal, definindo título e tipo
    document.querySelectorAll("[data-bs-toggle='modal']").forEach(function (botao) {
        botao.addEventListener("click", function () {
            let tipo = this.getAttribute("data-tipo");
            let tituloModal = document.getElementById("modal_financeiro_title");
            let tipoInput = document.getElementById("tipo_financeiro");
            if (tipo === "receita") {
                tituloModal.textContent = "💰 Nova Receita";
                tipoInput.value = "receita";
            } else if (tipo === "despesa") {
                tituloModal.textContent = "💸 Nova Despesa";
                tipoInput.value = "despesa";
            }
        });
    });
});



KTUtil.onDOMContentLoaded(function () {
    KTModalNewTarget.init();
});
