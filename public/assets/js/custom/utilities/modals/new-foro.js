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
        var dueDates = document.querySelectorAll('[name="data"], [name="dataAquisicao"]');

        dueDates.forEach(function(dueDate) {
            flatpickr(dueDate, {
                enableTime: true,
                dateFormat: "d/m/Y", // Formato pt-BR para exibição
                locale: "pt", // Define a localidade como português do Brasil
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
                    "descricao": {
                        validators: {
                            notEmpty: {
                                message: 'Descrição do patrimônio é obrigatória'
                            }
                        }
                    },
                    'patrimonio': {
                        validators: {
                            notEmpty: {
                                message: 'O território foreiro é obrigatório.'
                            }
                        }
                    },

                    data: {
                        validators: {
                            notEmpty: {
                                message: 'Data é obrigatória'
                            }
                        }
                    },
                    cep: {
                        validators: {
                            notEmpty: {
                                message: 'O CEP é obrigatória'
                            }
                        }
                    },
                    bairro: {
                        validators: {
                            notEmpty: {
                                message: 'O bairro é obrigatória'
                            }
                        }
                    },
                    logradouro: {
                        validators: {
                            notEmpty: {
                                message: 'O logradouro é obrigatória'
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

							// Show success message. For more info check the plugin's official documentation: https://sweetalert2.github.io/
							Swal.fire({
                                text: "Formulário enviado com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendi!",
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
                            text: "Parece que há alguns erros. Por favor, tente novamente.",
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
				if (result.value) {
					form.reset(); // Reset form
					modal.hide(); // Hide modal
				} else if (result.dismiss === 'cancel') {
                    Swal.fire({
                        text: "Seu formulário não foi cancelado!",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
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
			modalEl = document.querySelector('#kt_modal_new_foro');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#kt_modal_foro_form');
			submitButton = document.getElementById('kt_modal_new_foro_submit');
			cancelButton = document.getElementById('kt_modal_new_foro_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewTarget.init();
});
