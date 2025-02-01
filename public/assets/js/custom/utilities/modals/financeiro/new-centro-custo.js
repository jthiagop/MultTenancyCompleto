"use strict";

// Class definition
var KTModalNewTarget = function () {
	var submitButton;
	var cancelButton;
	var validator;
	var form;
	var modal;
	var modalEl;

	// Init form inputs
	var initForm = function() {
		// Tags. For more info, please visit the official plugin site: https://yaireo.github.io/tagify/
		var tags = new Tagify(form.querySelector('[name="category"]'), {
            whitelist: ["Importante", "Urgente", "Alta", "Média", "Baixa"],
			maxTags: 5,
			dropdown: {
				maxItems: 10,           // <- mixumum allowed rendered suggestions
				enabled: 0,             // <- show suggestions on focus
				closeOnSelect: false    // <- do not hide the suggestions dropdown once an item has been selected
			}
		});
		tags.on("change", function(){
			// Revalidate the field when an option is chosen
            validator.revalidateField('category');
		});

		// Due date. For more info, please visit the official plugin site: https://flatpickr.js.org/
        var dueDate = $(form).find('[name="start_date"], [name="end_date"]');
		dueDate.flatpickr({
			enableTime: false,
			dateFormat: "d/m/Y",
            defaultDate: new Date() // Adiciona a data atual como valor padrão

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
            {
                fields: {
                    code: {
                        validators: {
                            // Exemplo: se o código for obrigatório
                            notEmpty: {
                                message: 'O código do Centro de Custo é obrigatório'
                            },
                            numeric: {
                                message: 'O código deve ser um número válido'
                            }
                        }
                    },
                    target_title: {
                        validators: {
                            notEmpty: {
                                message: 'O nome do Centro de Custo é obrigatório'
                            },
                            stringLength: {
                                max: 255,
                                message: 'O nome não pode exceder 255 caracteres'
                            }
                        }
                    },
                    category: {
                        validators: {
                            notEmpty: {
                                message: 'A categoria é obrigatória'
                            }
                        }
                    },
                    start_date: {
                        validators: {
                            notEmpty: {
                                message: 'A data de criação é obrigatória'
                            },
                            date: {
                                format: 'DD/MM/YYYY',
                                // Ajuste conforme o formato que você estiver usando
                                message: 'Informe uma data válida no formato DD/MM/AAAA'
                            }
                        }
                    },
                    // Se você quiser que "observations" seja opcional, não precisa colocar aqui
                    // Caso seja obrigatório, descomente:
                    /*
                    observations: {
                        validators: {
                            notEmpty: {
                                message: 'Observações são obrigatórias'
                            }
                        }
                    },
                    */
                    status: {
                        validators: {
                            callback: {
                                message: 'Defina se o centro de custo está ativo ou inativo',
                                callback: function(input) {
                                    // Se quisermos obrigar a marcar (mas nesse caso é um switch,
                                    // ele sempre terá valor se estiver marcado)
                                    // Aqui normalmente já vem marcado como checked. Ajuste conforme a necessidade.
                                    return true;
                                }
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

							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
								text: "Form has been successfully submitted!",
								icon: "success",
								buttonsStyling: false,
								confirmButtonText: "Ok, got it!",
								customClass: {
									confirmButton: "btn btn-primary"
								}
							}).then(function (result) {
								if (result.isConfirmed) {
									modal.hide();
									form.submit(); // Submit form
								}
							});
						}, 2000);
					} else {
						// Show error message.
						Swal.fire({
							text: "Sorry, looks like there are some errors detected, please try again.",
							icon: "error",
							buttonsStyling: false,
							confirmButtonText: "Ok, got it!",
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
            // Seleciona o formulário
            form = document.querySelector('#kt_modal_new_centro_custo');

            // Se não encontrar o form, simplesmente retorne (opcional)
            if (!form) {
                return;
            }

            // Seleciona o botão de submit
            submitButton = document.getElementById('kt_modal_new_target_submit');

            // Seleciona o botão de cancel
            cancelButton = document.getElementById('kt_modal_new_target_cancel');

            // Inicializa a validação do formulário, por exemplo
            initForm();

            // Lida com eventos (como o clique de submit, etc.)
            handleForm();
        }
    };

}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewTarget.init();
});
