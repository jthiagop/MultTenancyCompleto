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
        var startDatePicker = $(form.querySelector('[name="data_inicio"]')).flatpickr({
            enableTime: false,
            dateFormat: "d/m/Y", // Formato brasileiro
            locale: "pt" // Localidade brasileira
        });

        // Configuração do Flatpickr para Data Fim
        var endDatePicker = $(form.querySelector('[name="data_fim"]')).flatpickr({
            enableTime: false,
            dateFormat: "d/m/Y", // Formato brasileiro
            locale: "pt" // Localidade brasileira
        });

		// Team assign. For more info, plase visit the official plugin site: https://select2.org/
        $(form.querySelector('[name="team_assign"]')).on('change', function() {
            // Revalidate the field when an option is chosen
            validator.revalidateField('team_assign');
        });
	}

	// Handle form validation and submittion
    var handleForm = function () {
        // Inicializar Flatpickr para Data Início
        var startDatePicker = $(form.querySelector('[name="data_inicio"]')).flatpickr({
            enableTime: false,
            dateFormat: "d/m/Y", // Formato brasileiro
            locale: "pt", // Localidade brasileira
            onChange: function (selectedDates, dateStr, instance) {
                // Atualiza o Flatpickr de Data Fim com a data mínima
                endDatePicker.set('minDate', selectedDates[0]);
            }
        });

        // Inicializar Flatpickr para Data Fim
        var endDatePicker = $(form.querySelector('[name="data_fim"]')).flatpickr({
            enableTime: false,
            dateFormat: "d/m/Y", // Formato brasileiro
            locale: "pt", // Localidade brasileira
            onChange: function (selectedDates, dateStr, instance) {
                // Atualiza o Flatpickr de Data Início com a data máxima
                startDatePicker.set('maxDate', selectedDates[0]);
            }
        });

        // Validação do Formulário
        validator = FormValidation.formValidation(form, {
            fields: {
                entidade_id: {
                    validators: {
                        notEmpty: {
                            message: 'Selecione uma entidade financeira.'
                        }
                    }
                },
                data_inicio: {
                    validators: {
                        notEmpty: {
                            message: 'A data inicial é obrigatória.'
                        }
                    }
                },
                data_fim: {
                    validators: {
                        notEmpty: {
                            message: 'A data final é obrigatória.'
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

        // Botão de Enviar
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (validator) {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        setTimeout(function () {
                            submitButton.removeAttribute('data-kt-indicator');
                            submitButton.disabled = false;

                            Swal.fire({
                                text: "Prestação de contas submetida com sucesso!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, entendido!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        }, 1000);
                    } else {
                        Swal.fire({
                            text: "Corrija os erros no formulário antes de prosseguir.",
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

        // Botão de Cancelar
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
                    confirmButton: "btn btn-sm btn-primary",
                    cancelButton: "btn btn-sm btn-active-light"
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.reset(); // Resetar o formulário
                    modal.hide(); // Fechar o modal
                }
            });
        });
    };


	return {
		// Public functions
		init: function () {
			// Elements
			modalEl = document.querySelector('#prestacaoConta');

			if (!modalEl) {
				return;
			}

			modal = new bootstrap.Modal(modalEl);

			form = document.querySelector('#prestacaoConta_form');
			submitButton = document.getElementById('prestacaoConta_submit');
			cancelButton = document.getElementById('prestacaoConta_cancel');

			initForm();
			handleForm();
		}
	};
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
	KTModalNewTarget.init();
});
