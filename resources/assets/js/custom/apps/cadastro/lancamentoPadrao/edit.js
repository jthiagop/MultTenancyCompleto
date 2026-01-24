var KTModalCustomersEdit = function () {
    var submitButton;
    var cancelButton;
    var closeButton;
    var validator;
    var form;
    var modal;

    var handleForm = function () {
        validator = FormValidation.formValidation(
            form,
            {
                fields: {
                    // Validações para o formulário de edição
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
                    if (status == 'Valid') {
                        submitButton.setAttribute('data-kt-indicator', 'on');
                        submitButton.disabled = true;

                        setTimeout(function() {
                            submitButton.removeAttribute('data-kt-indicator');
                            Swal.fire({
                                text: "Lançamento atualizado com sucesso!",
                                icon: "success",
                                confirmButtonText: "Ok, entendi!",
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    modal.hide();
                                    submitButton.disabled = false;
                                    form.submit();
                                }
                            });
                        }, 2000);
                    } else {
                        Swal.fire({
                            text: "Parece que há alguns erros, por favor, tente novamente.",
                            icon: "error",
                            confirmButtonText: "Ok, entendi!",
                        });
                    }
                });
            }
        });

        cancelButton.addEventListener('click', function (e) {
            e.preventDefault();
            modal.hide();
        });

        closeButton.addEventListener('click', function(e){
            e.preventDefault();
            modal.hide();
        });
    }

    return {
        init: function () {
            modal = new bootstrap.Modal(document.querySelector('#kt_modal_edit_customer'));
            form = document.querySelector('#kt_modal_edit_customer_form');
            submitButton = form.querySelector('#kt_modal_edit_customer_submit');
            cancelButton = form.querySelector('#kt_modal_edit_customer_cancel');
            closeButton = form.querySelector('#kt_modal_edit_customer_close');

            handleForm();
        }
    };
}();

KTUtil.onDOMContentLoaded(function () {
    KTModalCustomersEdit.init();
});
