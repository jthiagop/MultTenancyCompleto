"use strict";

// Class definition
var KTUsersAddUser = function () {
    // Shared variables
    const element = document.getElementById('kt_modal_add_user');
    if (!element) {
        console.warn('Modal #kt_modal_add_user não encontrado');
        return {
            init: function() {}
        };
    }

    const form = element.querySelector('#kt_modal_add_user_form');
    const modal = new bootstrap.Modal(element);

    // Init add schedule modal
    var initAddUser = () => {
        if (!form) {
            console.warn('Formulário #kt_modal_add_user_form não encontrado');
            return;
        }

        // Submit button handler
        const submitButton = element.querySelector('#kt_modal_add_user_submit');
        if (submitButton) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Limpar erros anteriores
                const errorContainer = element.querySelector('#kt_modal_add_user_errors');
                if (errorContainer) {
                    errorContainer.classList.add('d-none');
                    errorContainer.innerHTML = '';
                }

                // Coletar dados do formulário
                const formData = new FormData(form);

                // Adicionar must_change_password se checkbox estiver marcado
                const mustChangePasswordCheckbox = element.querySelector('#must_change_password');
                if (mustChangePasswordCheckbox && mustChangePasswordCheckbox.checked) {
                    formData.append('must_change_password', '1');
                }

                // Mostrar loading
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                // Obter token CSRF
                const csrfToken = formData.get('_token') || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                // Enviar via AJAX
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    // Remover loading
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    if (data.success) {
                        // Mostrar mensagem de sucesso
                        Swal.fire({
                            text: data.message || "Usuário criado ou atualizado com sucesso!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, entendi!",
                            customClass: {
                                confirmButton: "btn btn-primary"
                            }
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                form.reset();
                                modal.hide();
                                // Recarregar a página para atualizar a lista
                                window.location.reload();
                            }
                        });
                    } else {
                        throw new Error(data.message || 'Erro ao salvar usuário');
                    }
                })
                .catch(error => {
                    // Remover loading
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    let errorMessage = 'Erro ao salvar usuário. Tente novamente.';
                    let errors = [];

                    if (error.errors) {
                        // Erros de validação
                        Object.keys(error.errors).forEach(field => {
                            errors.push(...error.errors[field]);
                        });
                        errorMessage = errors.join('<br>');
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    // Mostrar erros no container
                    if (errorContainer) {
                        errorContainer.innerHTML = errorMessage;
                        errorContainer.classList.remove('d-none');
                    }

                    // Mostrar popup de erro
                    Swal.fire({
                        text: errorMessage,
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, entendi!",
                        customClass: {
                            confirmButton: "btn btn-primary"
                        }
                    });
                });
            });
        }

        // Cancel button handler
        const cancelButton = element.querySelector('[data-kt-users-modal-action="cancel"]');
        cancelButton.addEventListener('click', e => {
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
                    modal.hide();
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

        // Close button handler
        const closeButton = element.querySelector('[data-kt-users-modal-action="close"]');
        closeButton.addEventListener('click', e => {
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
                    modal.hide();
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
            initAddUser();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTUsersAddUser.init();
});
