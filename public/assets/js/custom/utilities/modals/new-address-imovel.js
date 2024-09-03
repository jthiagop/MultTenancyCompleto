"use strict";

var KTModalNewAddress = function () {
    var submitButton;
    var cancelButton;
    var validator;
    var form;
    var modal;
    var modalEl;

    var initForm = function() {
        $(form.querySelector('[name="uf"]')).select2().on('change', function() {
            validator.revalidateField('uf');
        });
    }

    var handleForm = function() {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'descricao': {
                        validators: {
                            notEmpty: {
                                message: 'O nome do foro é obrigatório'
                            }
                        }
                    },
                    'cep': {
                        validators: {
                            notEmpty: {
                                message: 'O CEP é obrigatório'
                            }
                        }
                    },
                    'logradouro': {
                        validators: {
                            notEmpty: {
                                message: 'A rua é obrigatória'
                            }
                        }
                    },
                    'bairro': {
                        validators: {
                            notEmpty: {
                                message: 'O bairro é obrigatório'
                            }
                        }
                    },
                    'localidade': {
                        validators: {
                            notEmpty: {
                                message: 'A cidade é obrigatória'
                            }
                        }
                    },
                    'uf': {
                        validators: {
                            notEmpty: {
                                message: 'O estado é obrigatório'
                            }
                        }
                    },
                    'ibge': {
                        validators: {
                            notEmpty: {
                                message: 'O número do município é obrigatório'
                            }
                        }
                    },
                    'numForo': {
                        validators: {
                            notEmpty: {
                                message: 'O número do foro é obrigatório'
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

        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (validator) {
                validator.validate().then(function (status) {
                    if (status === 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');

                        // Desativa o botão para evitar múltiplos cliques
                        submitButton.disabled = true;


                        var formData = new FormData(form);
                        fetch(form.action, {
                            method: 'POST',
                            body: formData
                        }).then(response => response.json())
                          .then(data => {
                              submitButton.removeAttribute('data-kt-indicator');
                              submitButton.disabled = false;
                              Swal.fire({
                                  text: "O formulário foi enviado com sucesso!",
                                  icon: "success",
                                  buttonsStyling: false,
                                  confirmButtonText: "Ok, entendi!",
                                  customClass: {
                                      confirmButton: "btn btn-primary"
                                  }
                              }).then(function (result) {
                                  if (result.isConfirmed) {
                                      modal.hide();
                                  }
                              });
                          }).catch(error => {
                              submitButton.removeAttribute('data-kt-indicator');
                              submitButton.disabled = false;
                              Swal.fire({
                                  text: "Desculpe, ocorreu um erro ao enviar o formulário.",
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
                            text: "Desculpe, parece que há alguns erros no formulário. Por favor, tente novamente.",
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
                confirmButtonText: "Sim, cancelar",
                cancelButtonText: "Não, continuar",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    modal.hide();
                }
            });
        });
    }

    return {
        init: function () {
            modalEl = document.querySelector('#kt_modal_new_address');
            if (!modalEl) {
                return;
            }

            modal = new bootstrap.Modal(modalEl);
            form = document.querySelector('#kt_modal_new_address_form');
            submitButton = form.querySelector('#kt_modal_new_address_submit');
            cancelButton = form.querySelector('#kt_modal_new_address_cancel');

            initForm();
            handleForm();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTModalNewAddress.init();
});
