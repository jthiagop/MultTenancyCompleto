"use strict";

const KTCreateApp = (function () {
    let modalEl, modal, stepper, form, formSubmitButton, formContinueButton, stepperObj, validations = [];

    const initStepper = function () {
        if (!window.KTStepper) {
            console.error('KTStepper dependency is missing.');
            return;
        }
        stepperObj = new KTStepper(stepper);

        stepperObj.on('kt.stepper.changed', function (stepper) {
            const stepIndex = stepperObj.getCurrentStepIndex();
            console.log(`Stepper changed to step: ${stepIndex}`);
            formSubmitButton.classList.toggle('d-none', stepIndex !== 4);
            formSubmitButton.classList.toggle('d-inline-block', stepIndex === 4);
            formContinueButton.classList.toggle('d-none', stepIndex === 5);
        });

        stepperObj.on('kt.stepper.next', function (stepper) {
            const currentStepIndex = stepper.getCurrentStepIndex();
            console.log(`Attempting to move to next step from step ${currentStepIndex}`);

            if (currentStepIndex === 1) {
                const validator = validations[0];
                if (validator) {
                    const selectedTipoParecer = form.querySelector('[name="tipo_parecer"]:checked')?.value;
                    console.log(`Selected tipo_parecer: ${selectedTipoParecer || 'none'}`);
                    validator.validate().then(function (status) {
                        console.log(`Validation status for step 1: ${status}`);
                        if (status === 'Valid') {
                            console.log('Validation passed, moving to next step');
                            stepper.goNext();
                        } else {
                            console.error('Validation failed');
                            Swal.fire({
                                text: "Selecione um tipo de parecer antes de continuar.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok",
                                customClass: { confirmButton: "btn btn-light" }
                            });
                        }
                    }).catch(error => {
                        console.error('Validation error:', error);
                    });
                } else {
                    console.log('No validator for step 1, moving to next step');
                    stepper.goNext();
                }
            } else if (currentStepIndex === 2) {
                const solicitanteTipo = form.querySelector('[name="solicitante_tipo"]:checked')?.value;
                console.log(`Selected solicitante_tipo: ${solicitanteTipo || 'none'}`);
                if (!solicitanteTipo) {
                    Swal.fire({
                        text: "Selecione quem é o solicitante.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-light" }
                    });
                    return;
                }
                if (solicitanteTipo === 'terceiro') {
                    const terceiroNome = form.querySelector('[name="terceiro_nome"]').value;
                    const terceiroCpf = form.querySelector('[name="terceiro_cpf"]').value;
                    const terceiroContato = form.querySelector('[name="terceiro_contato"]').value;
                    console.log(`Terceiro fields: nome=${terceiroNome}, cpf=${terceiroCpf}, contato=${terceiroContato}`);
                    if (!terceiroNome || !terceiroCpf || !terceiroContato) {
                        Swal.fire({
                            text: "Preencha todos os campos do solicitante terceiro.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: { confirmButton: "btn btn-light" }
                        });
                        return;
                    }
                }
                stepper.goNext();
            } else if (currentStepIndex === 3) {
                const valorMetroQuadrado = form.querySelector('[name="valor_metro_quadrado"]').value;
                const dataAvaliacao = form.querySelector('[name="data_avaliacao"]').value;
                console.log(`Avaliação fields: valor_metro_quadrado=${valorMetroQuadrado}, data_avaliacao=${dataAvaliacao}`);
                if (!valorMetroQuadrado || !dataAvaliacao) {
                    Swal.fire({
                        text: "Preencha todos os campos da avaliação.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-light" }
                    });
                    return;
                }
                if (isNaN(valorMetroQuadrado) || valorMetroQuadrado <= 0) {
                    Swal.fire({
                        text: "Insira um valor válido para o metro quadrado (ex: 800.00).",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-light" }
                    });
                    return;
                }
                stepper.goNext();
            } else {
                stepper.goNext();
            }
        });

        stepperObj.on('kt.stepper.previous', function (stepper) {
            console.log('Moving to previous step');
            stepper.goPrevious();
        });

        formSubmitButton.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('Form submit button clicked');
            formSubmitButton.disabled = true;
            formSubmitButton.setAttribute('data-kt-indicator', 'on');

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => {
                console.log('Form submission response:', response);
                if (!response.ok) throw new Error('Erro ao gerar o documento.');
                return response;
            })
            .then(() => {
                console.log('Form submission successful');
                formSubmitButton.removeAttribute('data-kt-indicator');
                formSubmitButton.disabled = false;
                stepperObj.goNext();
                form.reset();
                stepperObj.goTo(1);
            })
            .catch(error => {
                console.error('Form submission error:', error);
                Swal.fire({
                    text: error.message,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: { confirmButton: 'btn btn-light' }
                });
                formSubmitButton.removeAttribute('data-kt-indicator');
                formSubmitButton.disabled = false;
            });
        });

        form.querySelectorAll('[name="solicitante_tipo"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                console.log(`Solicitante tipo changed to: ${this.value}`);
                const isTerceiro = this.value === 'terceiro';
                document.getElementById('dados_proprietario_div').classList.toggle('d-none', isTerceiro);
                document.getElementById('form_terceiro_div').classList.toggle('d-none', !isTerceiro);
            });
        });

        form.querySelectorAll('[name="tipo_parecer"]').forEach(radio => {
            radio.addEventListener('change', () => {
                console.log(`Tipo parecer changed to: ${radio.value}`);
            });
        });
    };

    const initValidation = function () {
        if (!window.FormValidation) {
            console.error('FormValidation dependency is missing.');
            return;
        }

        // Step 1: Use notEmpty validator to match original code
        validations.push(FormValidation.formValidation(form, {
            fields: {
                'tipo_parecer': {
                    validators: {
                        notEmpty: { message: 'A natureza do PTAM é obrigatória.' }
                    }
                }
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap: new FormValidation.plugins.Bootstrap5({
                    rowSelector: '.fv-row',
                    eleInvalidClass: 'is-invalid',
                    eleValidClass: ''
                })
            }
        }));

        // Step 2: Manual validation (no FormValidation)
        // Step 3: Manual validation (no FormValidation)
        // Step 4: No validation needed
    };

    return {
        init: function () {
            console.log('Initializing KTCreateApp');
            modalEl = document.querySelector('#kt_modal_create_app');
            if (!modalEl) {
                console.error('Modal element not found.');
                return;
            }
            modal = new bootstrap.Modal(modalEl);
            stepper = document.querySelector('#kt_modal_create_app_stepper');
            form = stepper.querySelector('#kt_modal_create_app_form');
            formSubmitButton = stepper.querySelector('[data-kt-stepper-action="submit"]');
            formContinueButton = stepper.querySelector('[data-kt-stepper-action="next"]');
            if (!form || !formSubmitButton || !formContinueButton) {
                console.error('Form or button elements not found.');
                return;
            }
            initValidation();
            initStepper();
        }
    };
})();

KTUtil.onDOMContentLoaded(function () {
    if (!window.KTUtil || !window.bootstrap || !window.Swal || !window.FormValidation) {
        console.error('Required dependencies (KTUtil, Bootstrap, Swal, or FormValidation) are missing.');
        return;
    }
    KTCreateApp.init();
});