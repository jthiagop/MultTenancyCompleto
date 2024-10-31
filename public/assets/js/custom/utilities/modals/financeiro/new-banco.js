"use strict";

// Class definition
var DMModalNewBanco = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	// Init form inputs
	var initForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/
		var tags = new Tagify(form.querySelector('[name="tags"]'), {
			whitelist: ["Important", "Urgent", "High", "Medium", "Low"],
			maxTags: 5,
			dropdown: {
				maxItems: 10,           // <- mixumum allowed rendered suggestions
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		});
		tags.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('tags');
		});

        // Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
        var dueDates = document.querySelectorAll('[name="data_competencia"]');

        dueDates.forEach(function(dueDate) {
            flatpickr(dueDate, {
                enableTime: true,
                dateFormat: "d-m-Y", // Formato pt-BR para exibição
                locale: "pt", // Define a localidade como português do Brasil
                defaultDate: new Date() // Define a data atual como padrão
            });
        });


		// Team assign. For more info, plase visit the official plugin site: https://select2.org/
        $(form.querySelector('[name="team_assign"]')).on('change', function() {
            // Revalidate the field when an option is chosen
            validator.revalidateField('team_assign');
        });
	}

	// Handle form validation and submittion
	var handleForm = function() {
		// Stepper custom navigation

		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validator = FormValidation.formValidation(
			form,
			{
				fields: {
					data: {
						validators: {
							notEmpty: {
								message: 'A data é requerida!'
							}
						}
					},
                    banco_id: {
						validators: {
							notEmpty: {
								message: 'O banco é requerida!'
							}
						}
					},
					valor: {
                        validators: {
                            notEmpty: {
                                message: 'O valor é obrigatório'
                            },
                            callback: {
                                message: 'O valor não pode conter apenas uma vírgula',
                                callback: function(input) {
                                    // Verifica se o valor é uma vírgula ou uma string vazia
                                    var value = input.value.trim();
                                    return value !== ',';  // Retorna falso se o valor for apenas uma vírgula
                                }
                            }
                        }
                    },
					tipo: {
						validators: {
							notEmpty: {
								message: 'O tipo é requerido.'
							}
						}
					},
					lancamento_padrao: {
						validators: {
							notEmpty: {
								message: 'Lançamento padrão é obrigatório.'
							}
						}
					},
					'targets_notifications[]': {
                        validators: {
                            notEmpty: {
                                message: 'Please select at least one communication method'
                            }
                        }
                    },
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

		// Action buttons
		submitButton.addEventListener('click', function (e) {
			e.preventDefault();
			// Validate form before submit
			if (validator) {
				validator.validate().then(function (status) {
					console.log('validated!');
					if (status == 'Valid') {
						submitButton.setAttribute('data-kt-indicator', 'on');
						// Disable button to avoid multiple click
						submitButton.disabled = true;
						setTimeout(function() {
							submitButton.removeAttribute('data-kt-indicator');
							// Enable button
							submitButton.disabled = false;
							// Mostrar mensagem de sucesso. Para mais informações, consulte a documentação oficial do plugin: https://sweetalert2.github.io/
                            Swal.fire({
                                text: "O lançamento foi enviado com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    modal.hide();
                                    form.submit(); // Enviar formulário
                                }
                            });
                            }, 2000);
                            } else {
                            // Mostrar mensagem de erro.
                            Swal.fire({
                                text: "Desculpe, parece que alguns erros foram detectados, por favor tente novamente.",
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

		cancelButton.addEventListener('click', function (e) {
			e.preventDefault();

			Swal.fire({
				text: "Are you sure you would like to cancel?",
				icon: "warning",
				showCancelButton: true,
				buttonsStyling: false,
				confirmButtonText: "Yes, cancel it!",
				cancelButtonText: "No, return",
				customClass: {
					confirmButton: "btn btn-primary",
					cancelButton: "btn btn-active-light"
				}
			}).then(function (result) {
				if (result.value) {
					form.reset(); // Reset form
					modal.hide(); // Hide modal
				} else if (result.dismiss === 'cancel') {
					Swal.fire({
						text: "Your form has not been cancelled!.",
						icon: "error",
						buttonsStyling: false,
						confirmButtonText: "Ok, got it!",
						customClass: {
							confirmButton: "btn btn-primary",
						}
					});
				}
			});
		});
	}

	return {
		// Public functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#dm_modal_novo_lancamento_banco');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#dm_modal_novo_lancamento_banco_form');
			submitButton = document.getElementById('dm_modal_novo_lancamento_banco_submit');
			cancelButton = document.getElementById('kt_modal_new_target_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	DMModalNewBanco.init();
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
                    option.value = lp.description;
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
    $('#dm_modal_novo_lancamento_banco').on('shown.bs.modal', function () {
      $('#lancamento_padrao_banco').select2({
        placeholder: "Escolha um Lançamento...",
        width: '100%',
        closeOnSelect: true,
        dropdownParent: $('#dm_modal_novo_lancamento_banco'),
        language: 'pt' // Define o idioma para português personalizado
      });

      $('#bancoSelect').select2({
        placeholder: "Selecione o Banco",
        width: '100%',
        closeOnSelect: true,
        dropdownParent: $('#dm_modal_novo_lancamento_banco'),
        language: 'pt'
      });

    });
  });
