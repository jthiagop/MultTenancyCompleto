"use strict";

// Class definition
var KTModalUpdatePermission = function () {
    var submitButton;
    var cancelButton;
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
                    'permission_name': {
                        validators: {
                            notEmpty: {
                                message: 'O nome da permissão é obrigatório'
                            }
                        }
                    },
                    'permission_guard': {
                        validators: {
                            notEmpty: {
                                message: 'O guard é obrigatório'
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

                        // Get permission ID
                        var permissionId = form.querySelector('[name="permission_id"]').value;

                        // Prepare form data
                        var formData = {
                            name: form.querySelector('[name="permission_name"]').value,
                            guard_name: form.querySelector('[name="permission_guard"]').value,
                            _method: 'PUT'
                        };

                        // Send AJAX request
                        $.ajax({
                            url: '/permissions/' + permissionId,
                            type: 'POST',
                            data: formData,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;

                                // Show success message
                                Swal.fire({
                                    text: response.message || "Permissão atualizada com sucesso!",
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
                                    var table = $('#kt_permissions_table').DataTable();
                                    table.ajax.reload();
                                });
                            },
                            error: function (xhr) {
                                // Hide loading indication
                                submitButton.removeAttribute('data-kt-indicator');
                                submitButton.disabled = false;

                                // Show error message
                                var errorMessage = "Erro ao atualizar a permissão!";
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
            modal = new bootstrap.Modal(document.querySelector('#kt_modal_update_permission'));
            form = document.querySelector('#kt_modal_update_permission_form');
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
    KTModalUpdatePermission.init();
});
