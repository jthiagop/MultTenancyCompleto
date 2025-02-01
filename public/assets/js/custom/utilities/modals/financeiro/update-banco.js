"use strict";

var KTModalUpdateCaixa = function () {
    var submitButton;
    var cancelButton;
    var validator;
    var form;
    var modal;
    var modalEl;

    // Função para inicializar os elementos do formulário
    var initForm = function () {
        $('#lancamento_padrao').select2({
            placeholder: "Escolha um Lançamento...",
            allowClear: true
        });

        $('#bancoSelect').select2({
            placeholder: "Selecione um banco",
            allowClear: true
        });

        flatpickr('[name="data_competencia"], [name="dataAquisicao"]', {
            enableTime: false,
            dateFormat: "d/m/Y", // Formato visual
            locale: "pt",
            onChange: function (selectedDates, dateStr, instance) {
                // Formata para ISO 8601 e define o valor real
                const isoDate = selectedDates[0].toISOString().split('T')[0]; // YYYY-MM-DD
                instance.input.setAttribute('data-iso', isoDate);
            }
        });
    };

    // Função para manipular a validação e envio do formulário via AJAX
    var handleForm = function () {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    descricao: {
                        validators: {
                            notEmpty: {
                                message: 'Descrição é obrigatória'
                            }
                        }
                    },
                    valor: {
                        validators: {
                            notEmpty: {
                                message: 'O valor é obrigatório'
                            }
                        }
                    },
                    data_competencia: {
                        validators: {
                            notEmpty: {
                                message: 'Data é obrigatória'
                            },
                            date: {
                                format: 'DD/MM/YYYY', // Formato esperado
                                message: 'Formato de data inválido'
                            }
                        }
                    },
                    tipo: {
                        validators: {
                            notEmpty: {
                                message: 'Tipo é obrigatório'
                            }
                        }
                    },
                    tipo_documento: {
                        validators: {
                            notEmpty: {
                                message: 'O tipo de documento é obrigatório'
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

        // Ação do botão de submissão (Atualizar via AJAX)
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (validator) {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;
                        // Preparar dados do formulário
                        let formData = new FormData(form);
                        formData.append('_method', 'PUT'); // Para o Laravel entender como PUT
                        // Enviar via AJAX (fetch)
                        fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;
                            if (data.success) {
                                Swal.fire({
                                    text: "Lançamento atualizado com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, entendi!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(() => {
                                    location.reload(); // Recarregar a página após sucesso
                                });
                            } else {
                                Swal.fire({
                                    text: data.message || "Ocorreu um erro ao atualizar.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, entendi!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                text: "Erro ao enviar o formulário, tente novamente.",
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
                            text: "Há alguns erros no formulário, por favor, corrija-os.",
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
        });

        // Ação do botão de cancelamento
        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                text: "Tem certeza de que deseja cancelar?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, cancelar!",
                cancelButtonText: "Não, voltar",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.reset();
                    modal.hide();
                }
            });
        });
    };

    return {
        init: function () {
            modalEl = document.querySelector('#kt_modal_new_card');
            if (!modalEl) {
                return;
            }

            modal = new bootstrap.Modal(modalEl);
            form = document.querySelector('#kt_modal_new_card_form');
            submitButton = document.getElementById('kt_modal_new_card_submit');
            cancelButton = document.getElementById('kt_modal_new_card_cancel');

            initForm();
            handleForm();
        }
    };
}();

// Inicializar quando o documento estiver pronto
document.addEventListener('DOMContentLoaded', function () {
    KTModalUpdateCaixa.init();
});



$(document).ready(function() {
    $('#lancamento_padrao_caixa').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === '4') {
            $('#banco-deposito').show(); // Mostra o campo do banco de depósito
        } else {
            $('#banco-deposito').hide(); // Esconde o campo do banco de depósito
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo_select');
    const lancamentoPadraoBanco = document.getElementById('lancamento_padrao_banco');
    const lancamentoPadraoCaixa = document.getElementById('lancamento_padrao_caixa');

    tipoSelect.addEventListener('change', function() {
        const selectedTipo = tipoSelect.value;

        // Função para atualizar opções do select com base no tipo
        const updateOptions = (selectElement) => {
            // Limpa todas as opções do select de Lançamento Padrão
            selectElement.innerHTML = '';

            // Adiciona a opção vazia
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.text = 'Escolha um Lançamento...';
            selectElement.appendChild(emptyOption);

            // Filtra e adiciona as opções de acordo com o tipo selecionado
            lpsData.forEach(function(lp) {
                if (lp.type === selectedTipo) {
                    const option = document.createElement('option');
                    option.value = lp.id;
                    option.text = lp.description;
                    selectElement.appendChild(option);
                }
            });
        };

        // Atualizar ambos os selects, se eles existirem na página
        if (lancamentoPadraoBanco) updateOptions(lancamentoPadraoBanco);
        if (lancamentoPadraoCaixa) updateOptions(lancamentoPadraoCaixa);
    });
});


$(document).ready(function () {
    $('#kt_modal_new_target').on('shown.bs.modal', function () {
      $('#lancamento_padrao_caixa').select2({
        placeholder: "Escolha um Lançamento...",
        width: '100%',
        closeOnSelect: true,
        dropdownParent: $('#kt_modal_new_target'),
        language: 'pt' // Define o idioma para português personalizado
      });

      $('#bancoSelect').select2({
        placeholder: "Selecione o Banco",
        width: '100%',
        closeOnSelect: true,
        dropdownParent: $('#kt_modal_new_target'),
        language: 'pt'
      });

    });
  });

