"use strict";

// Class definition
var KTModalAddModule = function () {
    var submitButton;
    var cancelButton;
    var closeButton;
    var validator;
    var form;
    var modal;

    // Init form inputs
    var initForm = function () {
        // Init form validation rules
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    'module_name': {
                        validators: {
                            notEmpty: {
                                message: 'O nome do módulo é obrigatório'
                            }
                        }
                    },
                    'module_key': {
                        validators: {
                            notEmpty: {
                                message: 'A chave é obrigatória'
                            }
                        }
                    },
                    'module_route': {
                        validators: {
                            notEmpty: {
                                message: 'O nome da rota é obrigatório'
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
    }

    // Handle form submission
    var handleSubmit = function () {
        submitButton.addEventListener('click', function (e) {
            e.preventDefault();

            if (validator) {
                validator.validate().then(function (status) {
                    if (status == 'Valid') {
                        // Show loading indication
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        // Prepare form data using FormData to support file upload
                        var formData = new FormData();
                        formData.append('name', form.querySelector('[name="module_name"]').value);
                        formData.append('key', form.querySelector('[name="module_key"]').value);
                        formData.append('route_name', form.querySelector('[name="module_route"]').value);
                        formData.append('permission', form.querySelector('[name="module_permission"]').value || '');
                        formData.append('description', form.querySelector('[name="module_description"]').value || '');
                        formData.append('is_active', form.querySelector('[name="module_active"]').checked ? 1 : 0);
                        formData.append('show_on_dashboard', form.querySelector('[name="module_dashboard"]').checked ? 1 : 0);
                        formData.append('order_index', parseInt(form.querySelector('[name="module_order"]').value) || 0);

                        // Add icon file if selected
                        var iconInput = form.querySelector('[name="icon"]');
                        if (iconInput && iconInput.files && iconInput.files[0]) {
                            formData.append('icon', iconInput.files[0]);
                        }

                        // Add icon_remove if avatar was removed
                        var iconRemoveInput = form.querySelector('[name="icon_remove"]');
                        if (iconRemoveInput && iconRemoveInput.value === '1') {
                            formData.append('icon_remove', 1);
                        }

                        // Send AJAX request
                        $.ajax({
                            url: '/modules',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;

                                // Show success message
                                Swal.fire({
                                    text: response.message || "Módulo criado com sucesso!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                }).then(function () {
                                    // Close modal
                                    modal.hide();

                                    // Reset form
                                    form.reset();

                                    // Reload datatable
                                    var table = $('#kt_modules_table').DataTable();
                                    table.ajax.reload();
                                });
                            },
                            error: function (xhr) {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;

                                // Show error message
                                var errorMessage = "Erro ao criar o módulo!";
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    var errors = xhr.responseJSON.errors;
                                    errorMessage = Object.values(errors).flat().join('<br>');
                                }

                                Swal.fire({
                                    html: errorMessage,
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok!",
                                    customClass: {
                                        confirmButton: "btn btn-primary"
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    }

    // Handle cancel button
    var handleCancel = function () {
        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                text: "Tem certeza que deseja cancelar?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Sim, cancelar!",
                cancelButtonText: "Não",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-active-light"
                }
            }).then(function (result) {
                if (result.value) {
                    form.reset();
                    modal.hide();
                }
            });
        });
    }

    return {
        // Public functions
        init: function () {
            // Elements
            modal = new bootstrap.Modal(document.querySelector('#kt_modal_add_module'));
            form = document.querySelector('#kt_modal_add_module_form');
            submitButton = form.querySelector('[type="submit"]');
            cancelButton = form.querySelector('[type="reset"]');

            initForm();
            handleSubmit();
            handleCancel();
        }
    };
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTModalAddModule.init();
});
