"use strict";
import { route } from '@inertiajs/router';
var KTUsersAddUser = function () {
    const element = document.getElementById('kt_modal_add_user');
    const form = element.querySelector('#kt_modal_add_user_form');
    const modal = new bootstrap.Modal(element);
    var initAddUser = () => {
        var validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'user_name': {
                        validators: {
                            notEmpty: {
                                message: 'Nome e sobrenome, por favor.'
                            }
                        }
                    },
                    'user_email': {
                        validators: {
                            notEmpty: {
                                message: 'Acho que você digitou o email errado.'
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
        const submitButton = element.querySelector('[data-kt-users-modal-action="submit"]');
        submitButton.addEventListener('click', e => {
            e.preventDefault();
            if (validator) {
                validator.validate().then(function (status) {
                    console.log('validated!');
                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        // Obtenha os dados do formulário
                        const formData = new FormData(form);

                        // Use Inertia para enviar os dados para o Laravel
                        inertia.post(route('tenants.store'), formData)
                            .then(() => {
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;
                                Swal.fire({
                                    text: "Formulário enviado com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function (result) {
                                    if (result.isConfirmed) {
                                        modal.hide();
                                    }
                                });
                            })
                            .catch((errors) => {
                                console.error(errors);
                                Swal.fire({
                                    text: "Ocorreu um erro ao enviar o formulário. Por favor, tente novamente.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            });
                    } else {
                        Swal.fire({
                            text: "Desculpe, parece que foram detectados alguns erros, por favor tente novamente.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        });
                    }
                });
            }
        });
        // Restante do seu código para cancelar e fechar o modal
    }
    return {
        init: function () {
            initAddUser();
        }
    };
}();
KTUtil.onDOMContentLoaded(function () {
    KTUsersAddUser.init();
});
