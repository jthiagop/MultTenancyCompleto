"use strict";

// Class definition
var KTModalBidding = function () {
    // Shared variables
    var element;
    var form;
    var modal;

    // Private functions
    const initForm = () => {
        // Dynamically create validation non-empty rule
        const requiredFields = form.querySelectorAll('.required');
        var validationFields = {
            fields: {},
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: '',
                    eleValidClass: ''
                })
            }
        };

        // Detect required fields
        requiredFields.forEach(el => {
            let detectedField = null;
            const fvRow = el.closest('.fv-row');
            if (!fvRow) return;

            const input = fvRow.querySelector('input');
            if (input) {
                detectedField = input;
            }
            const textarea = fvRow.querySelector('textarea');
            if (textarea) {
                detectedField = textarea;
            }
            const select = fvRow.querySelector('select');
            if (select) {
                detectedField = select;
            }

            if (detectedField) {
                const name = detectedField.getAttribute('name');
                validationFields.fields[name] = {
                    validators: {
                        notEmpty: {
                            message: el.innerText + ' is required'
                        }
                    }
                };
            }
        });

        // Init form validation rules. For more info check the FormValidation plugin's official documentation: https://formvalidation.io/
        var validator = FormValidation.formValidation(form, validationFields);

        // Submit button handler
        const submitButton = form.querySelector('[data-kt-modal-action-type="submit"]');
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');
                    if (status === 'Valid') {
                        // Exibe a mensagem de sucesso e fecha o modal
                        Swal.fire({
                            text: "Avaliador cadastrado com sucesso!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function () {
                            form.reset();
                            modal.hide();
                        });
                    } else {
                        // Exibe mensagem de erro
                        Swal.fire({
                            text: "Oops! There are some error(s) detected.",
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
    };

    // Init Select2 template options
    const initSelect2Templates = () => {
        const elements = form.querySelectorAll('[data-kt-modal-bidding-type] select');
        if (!elements || elements.length === 0) {
            return;
        }

        // Format options
        const format = (item) => {
            if (!item.id) {
                return item.text;
            }
            var url = 'assets/media/' + item.element.getAttribute('data-kt-bidding-modal-option-icon');
            var img = $("<img>", {
                class: "rounded-circle me-2",
                width: 26,
                src: url
            });
            var span = $("<span>", {
                text: " " + item.text
            });
            span.prepend(img);
            return span;
        };

        elements.forEach(el => {
            // Init Select2 --- more info: https://select2.org/
            $(el).select2({
                minimumResultsForSearch: Infinity,
                templateResult: function (item) {
                    return format(item);
                }
            });
        });
    };

    // Handle bid options
    const handleBidOptions = () => {
        const options = form.querySelectorAll('[data-kt-modal-bidding="option"]');
        const inputEl = form.querySelector('[name="bid_amount"]');
        if (!options || !inputEl) return;
        options.forEach(option => {
            option.addEventListener('click', e => {
                e.preventDefault();
                inputEl.value = e.target.innerText;
            });
        });
    };

    // Handle currency selector
    const handleCurrencySelector = () => {
        const currencySelect = form.querySelector('.form-select[name="currency_type"]');
        if (!currencySelect) return;

        // Define swapCurrency before attaching the event listener
        const swapCurrency = (target) => {
            console.log(target);
            const currencies = form.querySelectorAll('[data-kt-modal-bidding-type]');
            currencies.forEach(currency => {
                currency.classList.add('d-none');
                if (currency.getAttribute('data-kt-modal-bidding-type') === target.id) {
                    currency.classList.remove('d-none');
                }
            });
        };

        // Select2 event listener
        $(currencySelect).on('select2:select', function (e) {
            const value = e.params.data;
            swapCurrency(value);
        });
    };

    // Handle cancel modal
    const handleCancelAction = () => {
        const cancelButton = element.querySelector('[data-kt-modal-action-type="cancel"]');
        const closeButton = element.querySelector('[data-kt-modal-action-type="close"]');

        const cancelAction = (e) => {
            e.preventDefault();
            Swal.fire({
                text: "Tem certeza de que deseja cancelar?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, cancele!",
                cancelButtonText: "Não, retorne",
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
                        text: "Your form has not been cancelled!",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn btn-primary",
                        }
                    });
                }
            });
        };

        if (cancelButton) {
            cancelButton.addEventListener('click', cancelAction);
        }
        if (closeButton) {
            closeButton.addEventListener('click', cancelAction);
        }
    };

    // Public methods
    return {
        init: function () {
            // Elements
            element = document.querySelector('#Dm_modal_Avaliador');
            form = document.getElementById('kt_modal_bidding_form');
            modal = new bootstrap.Modal(element);

            if (!form) {
                return;
            }

            initForm();
            initSelect2Templates();
            handleBidOptions();
            handleCurrencySelector();
            handleCancelAction();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalBidding.init();
});
